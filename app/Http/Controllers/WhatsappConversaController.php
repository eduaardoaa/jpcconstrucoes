<?php

namespace App\Http\Controllers;

use App\Models\WhatsappAnexo;
use App\Models\WhatsappContato;
use App\Models\WhatsappConversa;
use App\Models\WhatsappInstancia;
use App\Models\WhatsappMensagem;
use App\Services\WhatsappEvolutionSyncService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


class WhatsappConversaController extends Controller
{
    public function index(Request $request)
    {
        $usuario = auth()->user();

        $instanciasUsuario = $usuario->whatsappInstanciasAtivas()->get();

        $instanciaId = $request->get('instancia_id') ?? session('whatsapp_instancia_id');

        if (!$instanciaId && $instanciasUsuario->count() > 0) {
            $instanciaId = $instanciasUsuario->first()->id;
        }

        $instanciaSelecionada = $instanciasUsuario->firstWhere('id', (int) $instanciaId);

        if (!$instanciaSelecionada && $instanciasUsuario->count() > 0) {
            $instanciaSelecionada = $instanciasUsuario->first();
            $instanciaId = $instanciaSelecionada->id;
        }

        if ($instanciaSelecionada) {
            session(['whatsapp_instancia_id' => $instanciaSelecionada->id]);
        }

        $conversas = collect();
        $conversaSelecionada = null;
        $mensagens = collect();

        if ($instanciaSelecionada) {
            $conversas = WhatsappConversa::with(['contato', 'atendente'])
                ->where('whatsapp_instancia_id', $instanciaSelecionada->id)
                ->orderByDesc('ultima_mensagem_em')
                ->orderByDesc('updated_at')
                ->get();

            $conversaId = $request->get('conversa_id');

            if ($conversaId) {
                $conversaSelecionada = $conversas->firstWhere('id', (int) $conversaId);
            }

            if (!$conversaSelecionada && $conversas->count() > 0) {
                $conversaSelecionada = $conversas->first();
            }

            if ($conversaSelecionada) {
                $mensagens = $conversaSelecionada->mensagens()
                    ->with(['usuario', 'anexos', 'replyTo.usuario'])
                    ->orderBy('created_at')
                    ->get();
            }

            // Ao abrir a conversa (não no polling AJAX): zera badge e marca como lida no WhatsApp
            if ($conversaSelecionada && !$request->has('_ajax_whatsapp')) {
                if ($conversaSelecionada->nao_lidas > 0) {
                    $conversaSelecionada->update(['nao_lidas' => 0]);
                }
                $contatoConv = $conversaSelecionada->contato;
                if ($contatoConv && $instanciaSelecionada) {
                    try {
                        Http::withHeaders(['apikey' => $instanciaSelecionada->api_key])
                            ->timeout(5)
                            ->put(
                                rtrim($instanciaSelecionada->api_url, '/') . '/chat/markChatAsRead/' . $instanciaSelecionada->instance_name,
                                ['chat' => $contatoConv->remote_jid]
                            );
                    } catch (\Throwable) {}
                }
            }
        }

        if ($request->has('_ajax_whatsapp')) {
            return view('whatsapp.conversas.index', compact(
                'instanciasUsuario',
                'instanciaSelecionada',
                'conversas',
                'conversaSelecionada',
                'mensagens'
            ));
        }

        return view('whatsapp.conversas.index', compact(
            'instanciasUsuario',
            'instanciaSelecionada',
            'conversas',
            'conversaSelecionada',
            'mensagens'
        ));
    }

    public function enviarTexto(Request $request, WhatsappConversa $conversa)
    {
        // Garante tempo suficiente para os calls HTTP (evita timeout PHP padrão de 30s)
        set_time_limit(120);

        try {
            $request->validate([
                'mensagem'         => ['required', 'string', 'max:5000'],
                'reply_message_id' => ['nullable', 'integer'],
            ]);

            $this->validarAcessoConversa($conversa);

            $usuario = auth()->user();
            $instancia = WhatsappInstancia::findOrFail($conversa->whatsapp_instancia_id);
            $contato = $conversa->contato;

            if (!$contato) {
                throw new \Exception('Contato não encontrado.');
            }

            $textoOriginal = trim($request->mensagem);

            $textoWhatsapp = $conversa->enviar_identificacao
                ? "*{$usuario->name}:*\n{$textoOriginal}"
                : $textoOriginal;

            // Quoted reply: extrai key+message do payload da mensagem original
            $replyMessageId = $request->integer('reply_message_id') ?: null;
            $quoted = null;
            if ($replyMessageId) {
                $msgOriginal = WhatsappMensagem::find($replyMessageId);
                if ($msgOriginal && $msgOriginal->whatsapp_conversa_id === $conversa->id) {
                    $pl   = is_array($msgOriginal->payload) ? $msgOriginal->payload : [];
                    $data = $pl['data'] ?? $pl;
                    $key  = $data['key'] ?? null;
                    $msg  = $data['message'] ?? null;
                    if ($key && $msg) {
                        // Corrige remoteJid: payload pode ter @lid salvo antes da resolução.
                        // WhatsApp ignora a citação se o remoteJid for @lid — precisa ser o JID real.
                        if (isset($key['remoteJid']) && str_contains((string) $key['remoteJid'], '@lid')) {
                            $jidReal = $conversa->contato?->remote_jid ?? '';
                            if ($jidReal && !str_contains($jidReal, '@lid')) {
                                $key['remoteJid'] = $jidReal;
                            }
                        }
                        // Remove campos extras que o endpoint sendText não espera
                        unset($key['participant']);
                        $quoted = ['key' => $key, 'message' => $msg];
                        Log::info('enviarTexto: quoted montado', ['quoted' => $quoted]);
                    }
                }
            }

            $numero = $this->resolverNumeroParaEnvio($contato, $instancia);

            $isGrupoEnvio = $contato->is_grupo || str_contains($numero ?? '', '@g.us');

            // Grupos: pre-warm rápido para carregar sender keys na sessão
            if ($isGrupoEnvio) {
                try {
                    Http::withHeaders(['apikey' => $instancia->api_key])
                        ->timeout(4)
                        ->post(rtrim($instancia->api_url, '/') . '/group/findGroupInfos/' . $instancia->instance_name, [
                            'groupJid' => $numero,
                        ]);
                } catch (\Throwable) { }
            }

            $body = [
                'number' => $numero,
                'text'   => $textoWhatsapp,
                'delay'  => $isGrupoEnvio ? 1200 : 0,
            ];
            if ($quoted) {
                $body['quoted'] = $quoted;
            }

            $response = Http::withHeaders([
                    'apikey' => $instancia->api_key,
                    'Content-Type' => 'application/json',
                ])
                ->timeout(20)
                ->post(rtrim($instancia->api_url, '/') . '/message/sendText/' . $instancia->instance_name, $body);

            // Se der "No sessions" em grupo, tenta mais uma vez
            if (!$response->successful()
                && $isGrupoEnvio
                && (str_contains($response->body(), 'No sessions') || str_contains($response->body(), 'SessionError'))
            ) {
                $response = Http::withHeaders([
                        'apikey' => $instancia->api_key,
                        'Content-Type' => 'application/json',
                    ])
                    ->timeout(20)
                    ->post(rtrim($instancia->api_url, '/') . '/message/sendText/' . $instancia->instance_name, $body);
            }

            if (!$response->successful()) {
                $erroBody = $response->body();
                if (str_contains($erroBody, 'No sessions') || str_contains($erroBody, 'SessionError')) {
                    return response()->json([
                        'error' => 'Sessão do grupo não estabelecida. Envie uma mensagem neste grupo pelo celular e tente novamente.',
                    ], 422);
                }
                return response()->json(['error' => 'Erro Evolution: ' . $erroBody], 422);
            }

            $payload = $response->json();

            if ($quoted) {
                Log::info('enviarTexto: resposta sendText (quoted)', ['status' => $response->status(), 'body' => $response->body()]);
            }

            // NO BANCO: salva o texto LIMPO + user_id (o nome aparece pela relação no chat)
            WhatsappMensagem::create([
                'whatsapp_instancia_id' => $instancia->id,
                'whatsapp_conversa_id'  => $conversa->id,
                'whatsapp_contato_id'   => $contato->id,
                'user_id'               => $usuario->id,
                'message_id'            => data_get($payload, 'key.id') ?? 'LOC-' . Str::uuid(),
                'reply_to_message_id'   => $replyMessageId,
                'direcao'               => 'saida',
                'tipo'                  => 'texto',
                'conteudo'              => $textoOriginal,
                'payload'               => $payload,
                'status_envio'          => 'enviada',
            ]);

            $conversa->update([
                'atendente_id' => $conversa->atendente_id ?: $usuario->id,
                'ultima_mensagem_preview' => $textoOriginal,
                'ultima_mensagem_em' => now(),
            ]);

            return response()->json(['success' => true]);
        } catch (\Throwable $e) {
            Log::error('Erro em enviarTexto', [
                'conversa_id' => $conversa->id,
                'erro'        => $e->getMessage(),
                'classe'      => get_class($e),
                'linha'       => $e->getFile() . ':' . $e->getLine(),
            ]);
            return response()->json(['error' => $e->getMessage() ?: 'Erro interno no servidor.'], 500);
        }
    }

    public function apagarMensagem(Request $request, WhatsappMensagem $mensagem)
    {
        $conversa = $mensagem->conversa;
        if (!$conversa) abort(404);

        $this->validarAcessoConversa($conversa);

        $instancia = WhatsappInstancia::find($mensagem->whatsapp_instancia_id);

        // Tenta apagar no WhatsApp apenas para mensagens de saída
        $erroWhatsapp = null;
        if ($mensagem->direcao === 'saida' && $instancia && !$mensagem->apagada_em) {
            try {
                $pl   = is_array($mensagem->payload) ? $mensagem->payload : [];
                $data = $pl['data'] ?? $pl;
                $key  = $data['key'] ?? null;

                Log::info('apagarMensagem: key extraído', [
                    'mensagem_id' => $mensagem->id,
                    'key'         => $key,
                    'pl_keys'     => array_keys($pl),
                ]);

                if ($key && isset($key['id'])) {
                    $apiUrl = rtrim($instancia->api_url, '/');
                    $resp = Http::withHeaders(['apikey' => $instancia->api_key])
                        ->timeout(10)
                        ->delete(
                            "{$apiUrl}/message/deleteMessage/{$instancia->instance_name}",
                            [
                                'id'        => $key['id'],
                                'remoteJid' => $key['remoteJid'] ?? '',
                                'fromMe'    => true,
                            ]
                        );

                    Log::info('apagarMensagem: resposta Evolution API', [
                        'status' => $resp->status(),
                        'body'   => $resp->body(),
                    ]);

                    if (!$resp->successful()) {
                        $erroWhatsapp = $resp->body();
                    }
                }
            } catch (\Throwable $e) {
                Log::warning('apagarMensagem: exceção ao chamar Evolution API', ['erro' => $e->getMessage()]);
                $erroWhatsapp = $e->getMessage();
            }
        }

        $mensagem->update([
            'apagada_em' => now(),
            'conteudo'   => null,
        ]);

        return response()->json([
            'success'      => true,
            'erroWhatsapp' => $erroWhatsapp,
        ]);
    }

    public function enviarMidia(Request $request, WhatsappConversa $conversa)
    {
        $request->validate([
            'arquivo' => ['required', 'file', 'max:20480'],
            'legenda' => ['nullable', 'string', 'max:2000'],
        ], [
            'arquivo.required' => 'Selecione um arquivo para enviar.',
            'arquivo.file' => 'O arquivo enviado é inválido.',
            'arquivo.max' => 'O arquivo não pode passar de 20MB.',
        ]);

        $this->validarAcessoConversa($conversa);

        $usuario = auth()->user();
        $instancia = WhatsappInstancia::findOrFail($conversa->whatsapp_instancia_id);
        $contato = $conversa->contato;

        $arquivo = $request->file('arquivo');
        $mime = $arquivo->getMimeType();
        $nomeArquivo = $arquivo->getClientOriginalName();
        $base64 = base64_encode(file_get_contents($arquivo->getRealPath()));

        $tipo = $this->tipoMidiaPorMime($mime);
        $legendaOriginal = trim((string) $request->get('legenda'));

        $legendaEnvio = $legendaOriginal;

        if ($conversa->enviar_identificacao) {
            $legendaEnvio = $legendaOriginal
                ? "*{$usuario->name}:*\n{$legendaOriginal}"
                : "*{$usuario->name}:*";
        }

        $numero = $this->resolverNumeroParaEnvio($contato, $instancia);

        $response = Http::withHeaders([
                'apikey' => $instancia->api_key,
                'Content-Type' => 'application/json',
            ])
            ->timeout(60)
            ->post(rtrim($instancia->api_url, '/') . '/message/sendMedia/' . $instancia->instance_name, [
                'number' => $numero,
                'mediatype' => $tipo,
                'mimetype' => $mime,
                'caption' => $legendaEnvio,
                'media' => $base64,
                'fileName' => $nomeArquivo,
            ]);

        if (!$response->successful()) {
            Log::error('Erro ao enviar mídia WhatsApp', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            if ($request->ajax()) {
                return response()->json(['error' => 'Erro ao enviar mídia: ' . $response->body()], 422);
            }

            return back()->with('error', 'Erro ao enviar mídia: ' . $response->body());
        }

        $payload = $response->json();

        $mensagem = WhatsappMensagem::create([
            'whatsapp_instancia_id' => $instancia->id,
            'whatsapp_conversa_id'  => $conversa->id,
            'whatsapp_contato_id'   => $contato?->id,
            'user_id'               => $usuario->id,
            'message_id'            => data_get($payload, 'key.id') ?? data_get($payload, 'message.key.id') ?? 'LOCAL-' . Str::uuid(),
            'direcao'               => 'saida',
            'tipo'                  => $tipo,
            'conteudo'              => $legendaOriginal ?: strtoupper($tipo),
            'payload'               => $payload,
            'status_envio'          => 'enviada',
        ]);

        // Salva cópia local do arquivo enviado para exibir no chat
        try {
            $caminhoLocal = $arquivo->store('whatsapp/midia', 'public');
            WhatsappAnexo::create([
                'whatsapp_mensagem_id' => $mensagem->id,
                'tipo'                 => $tipo,
                'nome_arquivo'         => $nomeArquivo,
                'mime_type'            => $mime,
                'tamanho'              => $arquivo->getSize(),
                'caminho_local'        => $caminhoLocal,
            ]);
        } catch (\Throwable $e) {
            Log::warning('Falha ao salvar cópia local da mídia enviada', ['erro' => $e->getMessage()]);
        }

        $conversa->update([
            'atendente_id'            => $conversa->atendente_id ?: $usuario->id,
            'ultima_mensagem_preview' => $legendaOriginal ?: strtoupper($tipo),
            'ultima_mensagem_em'      => now(),
        ]);

        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }

        return back();
    }

    public function alterarIdentificacao(Request $request, WhatsappConversa $conversa)
    {
        $this->validarAcessoConversa($conversa);

        $conversa->update([
            'enviar_identificacao' => $request->boolean('enviar_identificacao'),
        ]);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'enviar_identificacao' => $conversa->enviar_identificacao]);
        }

        return back();
    }

    public function downloadMidia(WhatsappMensagem $mensagem)
    {
        $conversa = $mensagem->conversa;
        abort_if(!$conversa, 404);
        $this->validarAcessoConversa($conversa);

        // 1. Arquivo já está salvo localmente
        $anexo = $mensagem->anexos()->first();
        if ($anexo && $anexo->caminho_local && Storage::disk('public')->exists($anexo->caminho_local)) {
            return response()->file(
                Storage::disk('public')->path($anexo->caminho_local),
                array_filter([
                    'Content-Type'  => $anexo->mime_type,
                    'Cache-Control' => 'public, max-age=86400',
                ])
            );
        }

        // 2. Busca da Evolution API sob demanda
        $instancia = WhatsappInstancia::find($mensagem->whatsapp_instancia_id);
        abort_if(!$instancia, 404);

        $payload = is_array($mensagem->payload) ? $mensagem->payload : [];
        $data    = $payload['data'] ?? $payload;
        $key     = $data['key']     ?? null;
        $message = $data['message'] ?? null;

        abort_if(!$key || !$message, 404, 'Dados de mídia não disponíveis.');

        try {
            $resp = Http::withHeaders(['apikey' => $instancia->api_key])
                ->timeout(30)
                ->post(
                    rtrim($instancia->api_url, '/') . '/chat/getBase64FromMediaMessage/' . $instancia->instance_name,
                    ['message' => ['key' => $key, 'message' => $message], 'convertToMp4' => false]
                );

            abort_unless($resp->successful(), 404, 'Mídia indisponível na Evolution API.');

            $resData  = $resp->json();
            $base64   = $resData['base64'] ?? null;
            abort_if(!$base64, 404, 'Base64 não retornado pela Evolution API.');

            $binary   = base64_decode($base64);
            $mimeType = $resData['mimetype'] ?? ($anexo?->mime_type ?? 'application/octet-stream');

            // Salva localmente para não precisar baixar de novo
            $ext          = $this->extPorMime($mimeType, $mensagem->tipo);
            $caminhoLocal = 'whatsapp/midia/wa_' . $mensagem->id . '.' . $ext;
            Storage::disk('public')->put($caminhoLocal, $binary);

            if ($anexo) {
                $anexo->update(['caminho_local' => $caminhoLocal, 'mime_type' => $mimeType]);
            } else {
                WhatsappAnexo::create([
                    'whatsapp_mensagem_id' => $mensagem->id,
                    'tipo'                 => $mensagem->tipo,
                    'nome_arquivo'         => 'wa_' . $mensagem->tipo . '_' . $mensagem->id . '.' . $ext,
                    'mime_type'            => $mimeType,
                    'tamanho'              => strlen($binary),
                    'caminho_local'        => $caminhoLocal,
                ]);
            }

            return response($binary, 200, [
                'Content-Type'  => $mimeType,
                'Content-Length'=> strlen($binary),
                'Cache-Control' => 'public, max-age=86400',
            ]);
        } catch (\Throwable $e) {
            Log::warning('Falha ao baixar mídia WhatsApp via proxy', [
                'mensagem_id' => $mensagem->id,
                'erro'        => $e->getMessage(),
            ]);
            abort(404, 'Não foi possível carregar a mídia.');
        }
    }

    private function extPorMime(string $mime, string $tipo): string
    {
        $map = [
            'audio/ogg' => 'ogg', 'audio/mpeg' => 'mp3', 'audio/mp4' => 'm4a', 'audio/webm' => 'webm',
            'image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif',
            'video/mp4' => 'mp4', 'video/3gpp' => '3gp', 'video/webm' => 'webm',
            'application/pdf' => 'pdf',
        ];
        foreach ($map as $pattern => $ext) {
            if (str_contains($mime, $pattern)) return $ext;
        }
        return match ($tipo) {
            'audio' => 'ogg', 'imagem' => 'jpg', 'video' => 'mp4', 'figurinha' => 'webp', default => 'bin',
        };
    }

    public function novaConversa(Request $request)
    {
        $request->validate([
            'instancia_id' => ['required', 'integer'],
            'numero'       => ['required', 'string', 'regex:/^[0-9]+$/'],
        ]);

        $usuario   = auth()->user();
        $instancia = WhatsappInstancia::findOrFail($request->instancia_id);

        if (!$usuario->isAdmin()) {
            $temAcesso = $usuario->whatsappInstancias()
                ->where('whatsapp_instancias.id', $instancia->id)
                ->exists();
            abort_unless($temAcesso, 403);
        }

        $numero = preg_replace('/\D/', '', $request->numero);
        // Normaliza Brasil 12→13 dígitos: insere 9 após o 4º dígito
        if (strlen($numero) === 12 && str_starts_with($numero, '55')) {
            $numero = substr($numero, 0, 4) . '9' . substr($numero, 4);
        }

        $contato = WhatsappContato::where('whatsapp_instancia_id', $instancia->id)
            ->where('is_grupo', false)
            ->where(function ($q) use ($numero) {
                $q->where('numero', $numero)
                  ->orWhere('remote_jid', 'like', $numero . '@%');
            })
            ->first();

        if (!$contato) {
            $contato = WhatsappContato::create([
                'whatsapp_instancia_id' => $instancia->id,
                'remote_jid'            => $numero . '@s.whatsapp.net',
                'numero'                => $numero,
                'is_grupo'              => false,
            ]);
        }

        $conversa = WhatsappConversa::firstOrCreate(
            [
                'whatsapp_instancia_id' => $instancia->id,
                'whatsapp_contato_id'   => $contato->id,
            ],
            [
                'status'    => 'aberta',
                'nao_lidas' => 0,
            ]
        );

        return response()->json([
            'success'      => true,
            'conversa_id'  => $conversa->id,
            'instancia_id' => $instancia->id,
        ]);
    }

    public function sincronizar(WhatsappInstancia $instancia, WhatsappEvolutionSyncService $sync)
    {
        try {
            $usuario = auth()->user();

            if (!$usuario->isAdmin()) {
                $temAcesso = $usuario->whatsappInstancias()
                    ->where('whatsapp_instancias.id', $instancia->id)
                    ->exists();

                abort_unless($temAcesso, 403);
            }

            $sync->sincronizar($instancia);

            return redirect()
                ->route('whatsapp.conversas.index', ['instancia_id' => $instancia->id]);
        } catch (\Throwable $e) {
            Log::error('Erro ao sincronizar WhatsApp', [
                'instancia_id' => $instancia->id,
                'erro' => $e->getMessage(),
            ]);

            return redirect()
                ->route('whatsapp.conversas.index', ['instancia_id' => $instancia->id])
                ->with('error', 'Erro ao sincronizar: ' . $e->getMessage());
        }
    }

    public function sincronizarNomes(WhatsappInstancia $instancia)
    {
        $usuario = auth()->user();
        if (!$usuario->isAdmin()) {
            $temAcesso = $usuario->whatsappInstancias()
                ->where('whatsapp_instancias.id', $instancia->id)->exists();
            abort_unless($temAcesso, 403);
        }

        try {
            $resp = Http::withHeaders(['apikey' => $instancia->api_key])
                ->timeout(30)
                ->get(rtrim($instancia->api_url, '/') . '/contacts/fetch/' . $instancia->instance_name);

            if (!$resp->successful()) {
                return response()->json(['error' => 'Falha na Evolution API: ' . $resp->body()], 422);
            }

            $contatos = $resp->json();
            if (!is_array($contatos)) {
                return response()->json(['error' => 'Resposta inesperada da API.'], 422);
            }

            $atualizados = 0;
            foreach ($contatos as $c) {
                $jid      = $c['remoteJid'] ?? $c['jid'] ?? $c['id'] ?? null;
                $pushName = $c['pushName'] ?? null;
                $nome     = $c['name'] ?? $c['verifiedName'] ?? null;

                if (!$jid || str_contains($jid, '@g.us') || str_contains($jid, '@lid')) continue;

                // Normaliza JID brasileiro
                if (str_contains($jid, '@s.whatsapp.net')) {
                    $numero = preg_replace('/@.+$/', '', $jid);
                    if (strlen($numero) === 12 && str_starts_with($numero, '55')) {
                        $jid = substr($numero, 0, 4) . '9' . substr($numero, 4) . '@s.whatsapp.net';
                    }
                }

                $contato = WhatsappContato::where('whatsapp_instancia_id', $instancia->id)
                    ->where('remote_jid', $jid)->first();
                if (!$contato) continue;

                $update = [];
                if ($pushName) $update['push_name'] = $pushName;
                // Só preenche nome da agenda se o usuário ainda não definiu um nome manual
                if ($nome && !$contato->nome) $update['nome'] = $nome;

                if ($update) {
                    $contato->update($update);
                    $atualizados++;
                }
            }

            return response()->json(['success' => true, 'atualizados' => $atualizados]);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function validarAcessoConversa(WhatsappConversa $conversa): void
    {
        $usuario = auth()->user();

        if ($usuario->isAdmin()) {
            return;
        }

        $temAcesso = $usuario->whatsappInstancias()
            ->where('whatsapp_instancias.id', $conversa->whatsapp_instancia_id)
            ->exists();

        abort_unless($temAcesso, 403);
    }

    /**
     * Resolve o número/JID para envio via Evolution API.
     *
     * Ordem de preferência:
     *  1. Grupo → retorna JID completo (@g.us)
     *  2. JID normal @s.whatsapp.net → retorna só o número
     *  3. Número salvo no campo `numero` (sem @) → retorna o número
     *  4. @lid → Evolution API v2 aceita o JID @lid diretamente; usa ele sem lançar exceção
     *  5. Qualquer outro → extrai só dígitos do JID
     */
    private function resolverNumeroParaEnvio(WhatsappContato $contato, WhatsappInstancia $instancia): string
    {
        $jid = $contato->remote_jid;

        // 1. Grupos: usa o JID completo (@g.us)
        if ($contato->is_grupo || str_contains($jid, '@g.us')) {
            return $jid;
        }

        // 2. JID padrão @s.whatsapp.net → apenas o número
        if (str_contains($jid, '@s.whatsapp.net')) {
            return preg_replace('/\D/', '', str_replace('@s.whatsapp.net', '', $jid));
        }

        // 3. Número salvo localmente (sem domínio @)
        if ($contato->numero && !str_contains($contato->numero, '@')) {
            $numero = preg_replace('/\D/', '', $contato->numero);
            if (strlen($numero) >= 10) {
                return $numero;
            }
        }

        // 4. @lid: tenta resolver o número real em várias etapas.
        if (str_contains($jid, '@lid')) {
            $pushName = $contato->push_name ?: $contato->nome;

            // 4a. Banco local: outro contato com mesmo nome mas JID real
            if ($pushName) {
                $contatoReal = \App\Models\WhatsappContato::where('whatsapp_instancia_id', $contato->whatsapp_instancia_id)
                    ->where(function ($q) use ($pushName) {
                        $q->where('push_name', $pushName)
                          ->orWhere('nome', $pushName);
                    })
                    ->where('is_grupo', false)
                    ->where('remote_jid', 'not like', '%@lid%')
                    ->first();

                if ($contatoReal) {
                    Log::info("@lid resolvido para envio via banco: {$jid} → {$contatoReal->remote_jid}");
                    return preg_replace('/\D/', '', str_replace('@s.whatsapp.net', '', $contatoReal->remote_jid));
                }
            }

            // 4b. Evolution API: busca o contato pelo @lid diretamente
            $jidResolvido = $this->resolverLidParaEnvio($jid, $pushName, $instancia);
            if ($jidResolvido) {
                // Salva o JID real no contato para evitar resolver toda vez
                $contato->update([
                    'remote_jid' => $jidResolvido,
                    'numero'     => preg_replace('/\D/', '', str_replace('@s.whatsapp.net', '', $jidResolvido)),
                    'lid_jid'    => $jid,
                ]);
                Log::info("@lid resolvido via API para envio e salvo: {$jid} → {$jidResolvido}");
                return preg_replace('/\D/', '', str_replace('@s.whatsapp.net', '', $jidResolvido));
            }

            // Não foi possível resolver o @lid. O usuário precisa definir o número manualmente.
            $nome = $pushName ?? 'este contato';
            throw new \Exception(
                "Número privado (@lid): não foi possível descobrir o número de \"{$nome}\". " .
                "Clique em \"Definir número\" no cabeçalho da conversa para corrigir."
            );
        }

        // 5. Fallback: extrai dígitos
        return preg_replace('/\D/', '', $jid);
    }

    public function renomearContato(Request $request, WhatsappConversa $conversa)
    {
        $request->validate(['nome' => ['required', 'string', 'max:100']]);
        $this->validarAcessoConversa($conversa);

        $contato = $conversa->contato;
        if (!$contato) {
            return response()->json(['error' => 'Contato não encontrado.'], 422);
        }

        $contato->update(['nome' => trim($request->nome)]);

        return response()->json(['success' => true, 'nome' => $contato->nome_exibicao]);
    }

    public function definirNumeroContato(Request $request, WhatsappConversa $conversa)
    {
        $request->validate(['numero' => ['required', 'string', 'regex:/^[0-9]{10,15}$/']]);
        $this->validarAcessoConversa($conversa);

        $contato = $conversa->contato;
        if (!$contato) {
            return response()->json(['error' => 'Contato não encontrado.'], 422);
        }

        $numero  = preg_replace('/\D/', '', $request->numero);
        // Normaliza Brasil 12→13 dígitos: insere 9 após o 4º dígito
        if (strlen($numero) === 12 && str_starts_with($numero, '55')) {
            $numero = substr($numero, 0, 4) . '9' . substr($numero, 4);
        }
        $novoJid = $numero . '@s.whatsapp.net';
        $lidAtual = str_contains($contato->remote_jid ?? '', '@lid')
            ? $contato->remote_jid
            : $contato->lid_jid;

        // Funde um contato duplicado na conversa atual (move mensagens e deleta o dup)
        $fundir = function (WhatsappContato $dup) use ($conversa, $contato) {
            foreach ($dup->conversas as $convOrigem) {
                if ($convOrigem->id !== $conversa->id) {
                    WhatsappMensagem::where('whatsapp_conversa_id', $convOrigem->id)
                        ->update(['whatsapp_conversa_id' => $conversa->id, 'whatsapp_contato_id' => $contato->id]);
                    $convOrigem->delete();
                }
            }
            WhatsappMensagem::where('whatsapp_contato_id', $dup->id)
                ->update(['whatsapp_contato_id' => $contato->id]);
            Log::info("Contato duplicado fundido em definirNumero: id={$dup->id} → conversa id={$conversa->id}");
            $dup->delete();
        };

        // Funde contato que já tem o número real (evita JID duplicado na tabela)
        $dupNumero = WhatsappContato::where('whatsapp_instancia_id', $contato->whatsapp_instancia_id)
            ->where('remote_jid', $novoJid)->where('id', '!=', $contato->id)->first();
        if ($dupNumero) $fundir($dupNumero);

        // Funde contato @lid órfão criado antes do mapeamento ser estabelecido
        if ($lidAtual) {
            $dupLid = WhatsappContato::where('whatsapp_instancia_id', $contato->whatsapp_instancia_id)
                ->where('remote_jid', $lidAtual)->where('id', '!=', $contato->id)->first();
            if ($dupLid) $fundir($dupLid);
        }

        $contato->update([
            'lid_jid'    => $lidAtual,
            'remote_jid' => $novoJid,
            'numero'     => $numero,
        ]);

        Log::info("Número @lid definido manualmente: {$lidAtual} → {$novoJid}");

        return response()->json(['success' => true, 'numero' => $numero]);
    }

    private function resolverLidParaEnvio(string $lid, ?string $pushName, WhatsappInstancia $instancia): ?string
    {
        $base = rtrim($instancia->api_url, '/');
        $inst = $instancia->instance_name;
        $headers = ['apikey' => $instancia->api_key];

        try {
            // Tentativa 1: fetchProfile com o próprio @lid
            $r = Http::withHeaders($headers)->timeout(8)
                ->post("{$base}/chat/fetchProfile/{$inst}", ['number' => $lid]);
            if ($r->successful()) {
                $data = $r->json();
                foreach (['jid', 'id', 'remoteJid', 'wid'] as $field) {
                    $j = $data[$field] ?? null;
                    if ($j && str_contains($j, '@s.whatsapp.net')) {
                        return $j;
                    }
                }
            }

            if ($pushName) {
                // Tentativa 2: POST /contacts/fetch filtrado por pushName
                $r2 = Http::withHeaders($headers)->timeout(8)
                    ->post("{$base}/contacts/fetch/{$inst}", ['where' => ['pushName' => $pushName]]);
                if ($r2->successful()) {
                    foreach ((array) $r2->json() as $c) {
                        $cJid = $c['remoteJid'] ?? $c['jid'] ?? $c['id'] ?? '';
                        if (str_contains($cJid, '@s.whatsapp.net')) {
                            return $cJid;
                        }
                    }
                }

                // Tentativa 3: GET /contacts/fetch — todos os contatos da Evolution API.
                // A Evolution API armazena o mapeamento @lid → @s.whatsapp.net localmente
                // quando o contato está na agenda do celular.
                $r3 = Http::withHeaders($headers)->timeout(15)->get("{$base}/contacts/fetch/{$inst}");
                if ($r3->successful()) {
                    $allContacts = $r3->json();
                    if (is_array($allContacts)) {
                        foreach ($allContacts as $c) {
                            $cJid  = $c['remoteJid'] ?? $c['jid'] ?? $c['id'] ?? '';
                            $cName = $c['pushName'] ?? $c['name'] ?? $c['verifiedName'] ?? '';
                            if (str_contains($cJid, '@s.whatsapp.net') && $cName === $pushName) {
                                return $cJid;
                            }
                        }
                    }
                }

                // Tentativa 4: findContacts (endpoint alternativo de alguns builds)
                $r4 = Http::withHeaders($headers)->timeout(8)
                    ->post("{$base}/chat/findContacts/{$inst}", ['where' => ['pushName' => $pushName]]);
                if ($r4->successful()) {
                    foreach ((array) $r4->json() as $c) {
                        $cJid = $c['remoteJid'] ?? $c['jid'] ?? $c['id'] ?? '';
                        if ($cJid && str_contains($cJid, '@s.whatsapp.net')) {
                            return $cJid;
                        }
                    }
                }
            }
        } catch (\Throwable $e) {
            Log::warning("resolverLidParaEnvio falhou para {$lid}: " . $e->getMessage());
        }

        return null;
    }

    private function tipoMidiaPorMime(?string $mime): string
    {
        $mime = strtolower((string) $mime);

        if (str_starts_with($mime, 'image/')) {
            return 'image';
        }

        if (str_starts_with($mime, 'video/')) {
            return 'video';
        }

        if (str_starts_with($mime, 'audio/')) {
            return 'audio';
        }

        return 'document';
    }
}