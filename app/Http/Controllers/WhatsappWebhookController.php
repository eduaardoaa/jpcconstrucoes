<?php

namespace App\Http\Controllers;

use App\Models\WhatsappAnexo;
use App\Models\WhatsappInstancia;
use App\Models\WhatsappContato;
use App\Models\WhatsappConversa;
use App\Models\WhatsappMensagem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class WhatsappWebhookController extends Controller
{
    public function receber(Request $request, string $token)
    {
        $instancia = WhatsappInstancia::where('webhook_token', $token)->first();

        if (!$instancia) {
            return response()->json([
                'ok'   => false,
                'erro' => 'Instância não encontrada.',
            ], 404);
        }

        $payload = $request->all();

        Log::info('Webhook WhatsApp recebido', [
            'instancia_id' => $instancia->id,
            'payload'      => $payload,
        ]);

        // ──────────────────────────────────────────────────────────────────────
        // AUTO-DESCOBERTA DO JID PRÓPRIO DA INSTÂNCIA
        // A Evolution API envia um campo "sender" na raiz do payload com o JID
        // do número da instância. Guardamos na primeira vez que aparecer.
        // ──────────────────────────────────────────────────────────────────────
        $senderJid = $payload['sender'] ?? null;
        if ($senderJid && !$instancia->jid_proprio) {
            $instancia->update(['jid_proprio' => $senderJid]);
            Log::info("JID próprio da instância {$instancia->id} descoberto: {$senderJid}");
        }

        $data  = $payload['data'] ?? null;
        $event = $payload['event'] ?? '';

        if (!$data) {
            return response()->json(['ok' => true]);
        }

        // ──────────────────────────────────────────────────────────────────────
        // messages.delete e messages.update NÃO têm data.key — têm data.keyId.
        // Precisam ser tratados ANTES da guarda que exige data.key.
        // ──────────────────────────────────────────────────────────────────────
        if ($event === 'messages.delete') {
            $delKey = $data['key'] ?? null;
            $delId  = is_array($delKey) ? ($delKey['id'] ?? null) : $delKey;
            if (!$delId) $delId = $data['keyId'] ?? null;
            if ($delId) {
                WhatsappMensagem::where('whatsapp_instancia_id', $instancia->id)
                    ->where('message_id', $delId)
                    ->whereNull('apagada_em')
                    ->update(['apagada_em' => now(), 'conteudo' => null]);
            }
            return response()->json(['ok' => true]);
        }

        if ($event === 'messages.update') {
            $updates = isset($data[0]) ? $data : [$data];
            foreach ($updates as $upd) {
                $upId = $upd['keyId'] ?? ($upd['key']['id'] ?? null);
                if (!$upId) continue;

                $upStatus = $upd['status'] ?? ($upd['update']['status'] ?? null);
                if ($upStatus === 'DELETED') {
                    WhatsappMensagem::where('whatsapp_instancia_id', $instancia->id)
                        ->where('message_id', $upId)
                        ->whereNull('apagada_em')
                        ->update(['apagada_em' => now(), 'conteudo' => null]);
                    continue;
                }

                // Edição: data.message.editedMessage.message.conversation
                $editedMsgContent = $upd['message']['editedMessage']['message'] ?? null;
                if ($editedMsgContent) {
                    $novoTexto = $editedMsgContent['conversation']
                              ?? ($editedMsgContent['extendedTextMessage']['text'] ?? null);
                    if ($novoTexto !== null) {
                        WhatsappMensagem::where('whatsapp_instancia_id', $instancia->id)
                            ->where('message_id', $upId)
                            ->whereNull('apagada_em')
                            ->update(['conteudo' => $novoTexto, 'editada_em' => now()]);
                    }
                }

                // Status de entrega/leitura
                $statusMap = ['SERVER_ACK' => 'enviada', 'DELIVERY_ACK' => 'entregue', 'READ' => 'lida'];
                if ($upStatus && isset($statusMap[$upStatus])) {
                    WhatsappMensagem::where('whatsapp_instancia_id', $instancia->id)
                        ->where('message_id', $upId)
                        ->update(['status_envio' => $statusMap[$upStatus]]);
                }
            }
            return response()->json(['ok' => true]);
        }

        // Para todos os outros eventos (messages.upsert etc.) exige data.key
        if (!isset($data['key'])) {
            return response()->json(['ok' => true]);
        }

        $remoteJid = $data['key']['remoteJid'] ?? null;
        $fromMe    = (bool) ($data['key']['fromMe'] ?? false);
        $messageId = $data['key']['id'] ?? null;
        $pushName  = $data['pushName'] ?? null;

        // Participant: quem enviou dentro do grupo (null em conversas individuais)
        // Evolution API coloca em data.participant ou data.key.participant
        $participant = $data['participant'] ?? $data['key']['participant'] ?? null;

        if (!$remoteJid) {
            return response()->json(['ok' => true]);
        }

        // ──────────────────────────────────────────────────────────────────────
        // IGNORA MENSAGENS PARA O PRÓPRIO NÚMERO DA INSTÂNCIA
        // Evita criar conversa "para si mesmo" quando a Evolution API ecoa
        // mensagens de saída com remoteJid = JID do próprio número.
        //
        // Usa comparação pelos últimos 8 dígitos para tolerar variações no
        // formato do número (ex: com/sem o 9 extra do Brasil).
        // ──────────────────────────────────────────────────────────────────────
        $jidProprio = $instancia->jid_proprio ?? ($senderJid ?? null);
        if ($jidProprio && !str_contains($remoteJid, '@lid')) {
            $propDigits   = preg_replace('/\D/', '', preg_replace('/@.+$/', '', $jidProprio));
            $remoteDigits = preg_replace('/\D/', '', preg_replace('/@.+$/', '', $remoteJid));
            $propLast8    = substr($propDigits, -8);
            $remoteLast8  = substr($remoteDigits, -8);
            if (strlen($propLast8) === 8 && $propLast8 === $remoteLast8) {
                Log::info("Webhook ignorado: remoteJid {$remoteJid} é o próprio número da instância.");
                return response()->json(['ok' => true, 'skipped' => 'self_jid']);
            }
        }

        // Ignora também status@broadcast (atualizações de status do WhatsApp)
        if (str_contains($remoteJid, 'status@broadcast') || str_contains($remoteJid, 'broadcast')) {
            return response()->json(['ok' => true, 'skipped' => 'broadcast']);
        }

        // ──────────────────────────────────────────────────────────────────────
        // EVENTOS DE EDIÇÃO E EXCLUSÃO
        // ──────────────────────────────────────────────────────────────────────
        $event = $payload['event'] ?? '';

        // messages.delete direto
        if ($event === 'messages.delete') {
            $delKey = $data['key'] ?? null;
            $delId  = is_array($delKey) ? ($delKey['id'] ?? null) : $delKey;
            if ($delId) {
                WhatsappMensagem::where('whatsapp_instancia_id', $instancia->id)
                    ->where('message_id', $delId)
                    ->whereNull('apagada_em')
                    ->update(['apagada_em' => now(), 'conteudo' => null]);
            }
            return response()->json(['ok' => true]);
        }

        // messages.update — Evolution API v2 envia keyId/status/message direto em $data
        if ($event === 'messages.update') {
            // Pode chegar como array de updates ou como objeto único
            $updates = isset($data[0]) ? $data : [$data];
            foreach ($updates as $upd) {
                // ID da mensagem original: vem como keyId (não dentro de key.id)
                $upId = $upd['keyId'] ?? ($upd['key']['id'] ?? null);
                if (!$upId) continue;

                // Status atualizado diretamente em $upd (não em $upd['update'])
                $upStatus = $upd['status'] ?? ($upd['update']['status'] ?? null);
                if ($upStatus === 'DELETED') {
                    WhatsappMensagem::where('whatsapp_instancia_id', $instancia->id)
                        ->where('message_id', $upId)
                        ->whereNull('apagada_em')
                        ->update(['apagada_em' => now(), 'conteudo' => null]);
                    continue;
                }

                // Edição: vem em message.editedMessage.message (não em update.editedMessage)
                $editedMsgContent = $upd['message']['editedMessage']['message']
                                 ?? $upd['update']['editedMessage']['message']['protocolMessage']['editedMessage']
                                 ?? $upd['update']['message']['protocolMessage']['editedMessage']
                                 ?? null;
                if ($editedMsgContent) {
                    $novoTexto = $editedMsgContent['conversation']
                              ?? ($editedMsgContent['extendedTextMessage']['text'] ?? null);
                    if ($novoTexto !== null) {
                        WhatsappMensagem::where('whatsapp_instancia_id', $instancia->id)
                            ->where('message_id', $upId)
                            ->whereNull('apagada_em')
                            ->update(['conteudo' => $novoTexto, 'editada_em' => now()]);
                    }
                }

                // Atualiza status de entrega/leitura
                if ($upStatus) {
                    $statusMap = [
                        'SERVER_ACK'   => 'enviada',
                        'DELIVERY_ACK' => 'entregue',
                        'READ'         => 'lida',
                    ];
                    if (isset($statusMap[$upStatus])) {
                        WhatsappMensagem::where('whatsapp_instancia_id', $instancia->id)
                            ->where('message_id', $upId)
                            ->update(['status_envio' => $statusMap[$upStatus]]);
                    }
                }
            }
            return response()->json(['ok' => true]);
        }

        // messages.upsert com protocolMessage — edição (type=14) ou exclusão (type=REVOKE/0)
        $msgObj = $data['message'] ?? [];
        $proto  = $msgObj['protocolMessage'] ?? null;
        if ($proto) {
            $protoType  = $proto['type'] ?? null;
            $protoKeyId = $proto['key']['id'] ?? null;

            // Exclusão (REVOKE = 0 ou string "REVOKE")
            if (($protoType === 0 || $protoType === 'REVOKE') && $protoKeyId) {
                WhatsappMensagem::where('whatsapp_instancia_id', $instancia->id)
                    ->where('message_id', $protoKeyId)
                    ->whereNull('apagada_em')
                    ->update(['apagada_em' => now(), 'conteudo' => null]);
                return response()->json(['ok' => true]);
            }

            // Edição (type=14)
            if ($protoType === 14 && $protoKeyId) {
                $editadoMsg = $proto['editedMessage'] ?? null;
                $novoTexto  = $editadoMsg['conversation']
                           ?? ($editadoMsg['extendedTextMessage']['text'] ?? null);
                if ($novoTexto !== null) {
                    WhatsappMensagem::where('whatsapp_instancia_id', $instancia->id)
                        ->where('message_id', $protoKeyId)
                        ->whereNull('apagada_em')
                        ->update(['conteudo' => $novoTexto, 'editada_em' => now()]);
                }
                return response()->json(['ok' => true]);
            }
        }

        // messages.upsert com placeholderMessage (exclusão do outro lado)
        if (isset($msgObj['placeholderMessage']) && ($msgObj['placeholderMessage']['type'] ?? -1) === 0) {
            $delId = ($data['key']['id'] ?? null);
            if ($delId) {
                WhatsappMensagem::where('whatsapp_instancia_id', $instancia->id)
                    ->where('message_id', $delId)
                    ->whereNull('apagada_em')
                    ->update(['apagada_em' => now(), 'conteudo' => null]);
            }
            return response()->json(['ok' => true]);
        }

        // ──────────────────────────────────────────────────────────────────────
        // RESOLUÇÃO DE @lid EM CONTATOS INDIVIDUAIS
        //
        // O WhatsApp usa @lid como Privacy ID para alguns contatos.
        // Estratégia em 3 etapas para evitar contatos duplicados:
        //
        //  1. Tenta resolver via Evolution API (busca por pushName)
        //  2. Se falhar, busca no banco um contato existente com o mesmo pushName
        //     → evita criar duplicata para alguém que já tem conversa ativa
        //  3. Se não achar nada, mantém @lid (vai precisar de envio manual)
        // ──────────────────────────────────────────────────────────────────────
        $lidOriginal = null; // guarda o @lid antes de qualquer resolução

        if (str_contains($remoteJid, '@lid')) {
            $lidOriginal = $remoteJid;

            // Etapa 0: verifica se já existe um contato com este @lid mapeado para número real.
            // Isso garante que mensagens futuras do mesmo @lid vão para a conversa certa,
            // mesmo após o remote_jid ter sido atualizado manualmente.
            $contatoMapeado = WhatsappContato::where('whatsapp_instancia_id', $instancia->id)
                ->where('lid_jid', $remoteJid)
                ->where('remote_jid', 'not like', '%@lid%')
                ->first();

            if ($contatoMapeado) {
                $remoteJid = $contatoMapeado->remote_jid;
                Log::info("@lid resolvido via mapeamento salvo: {$lidOriginal} → {$remoteJid}");
            } else {
            // Etapa 1: tenta Evolution API
            $jidResolvido = $this->resolverLid($remoteJid, $pushName, $instancia);

            if ($jidResolvido) {
                $remoteJid = $jidResolvido;
            } elseif (!$fromMe && $pushName) {
                // Etapa 2: busca por pushName NO BANCO — só para mensagens RECEBIDAS.
                // Para fromMe=true o pushName é o nome da PRÓPRIA CONTA (remetente),
                // não do destinatário. Usar pushName para fromMe causaria roteamento
                // incorreto (e.g., mensagens para "Diogo" parar na conversa de outro
                // contato que também tenha pushName = nome do dono da instância).
                $contatoExistente = WhatsappContato::where('whatsapp_instancia_id', $instancia->id)
                    ->where('push_name', $pushName)
                    ->where('is_grupo', false)
                    ->where('remote_jid', 'not like', '%@lid%') // só conta se já tem número real
                    ->first();

                if ($contatoExistente) {
                    $remoteJid = $contatoExistente->remote_jid;
                    // Salva @lid imediatamente para futuras mensagens usarem Etapa 0
                    if (!$contatoExistente->lid_jid) {
                        $contatoExistente->update(['lid_jid' => $lidOriginal]);
                    }
                    // Funde qualquer contato @lid órfão criado antes deste mapeamento
                    $this->fundirDuplicadoLid($contatoExistente, $lidOriginal);
                    Log::info("@lid unificado com contato existente via pushName: {$remoteJid} ({$pushName})");
                } else {
                    // Etapa 3: tenta também pelo campo nome salvo
                    $contatoExistente = WhatsappContato::where('whatsapp_instancia_id', $instancia->id)
                        ->where('nome', $pushName)
                        ->where('is_grupo', false)
                        ->where('remote_jid', 'not like', '%@lid%')
                        ->first();

                    if ($contatoExistente) {
                        $remoteJid = $contatoExistente->remote_jid;
                        if (!$contatoExistente->lid_jid) {
                            $contatoExistente->update(['lid_jid' => $lidOriginal]);
                        }
                        $this->fundirDuplicadoLid($contatoExistente, $lidOriginal);
                        Log::info("@lid unificado com contato existente via nome: {$remoteJid} ({$pushName})");
                    }
                    // Se não achou nada → mantém @lid (contato novo, sem histórico)
                }
            }
            } // fecha else do contatoMapeado
        }

        // Normaliza número brasileiro: 12 dígitos (8 após DDD) → 13 dígitos (insere 9 no 4º dígito)
        if (str_contains($remoteJid, '@s.whatsapp.net')) {
            $remoteJidNorm = $this->normalizarJidBrasileiro($remoteJid);
            if ($remoteJidNorm !== $remoteJid) {
                $contatoNorm = WhatsappContato::where('whatsapp_instancia_id', $instancia->id)
                    ->where('remote_jid', $remoteJidNorm)->first();
                if ($contatoNorm) {
                    // Já existe contato com o formato correto: funde o antigo nele
                    $this->fundirDuplicadoLid($contatoNorm, $remoteJid);
                } else {
                    // Só existe o formato antigo: atualiza para o correto
                    WhatsappContato::where('whatsapp_instancia_id', $instancia->id)
                        ->where('remote_jid', $remoteJid)
                        ->update(['remote_jid' => $remoteJidNorm,
                                  'numero'     => preg_replace('/@.+$/', '', $remoteJidNorm)]);
                }
                $remoteJid = $remoteJidNorm;
            }
        }

        $isGrupo = str_contains($remoteJid, '@g.us');

        // ──────────────────────────────────────────────────────────────────────
        // RESOLUÇÃO DE @lid EM PARTICIPANTS DE GRUPO
        // Em mensagens de grupo o remoteJid é @g.us (correto), mas o participant
        // pode vir como @lid. Tentamos resolver para @s.whatsapp.net.
        // ──────────────────────────────────────────────────────────────────────
        if ($isGrupo && $participant && str_contains($participant, '@lid') && $pushName) {
            $participantResolvido = $this->resolverLid($participant, $pushName, $instancia);
            if ($participantResolvido) {
                $participant = $participantResolvido;
            }
        }

        // ──────────────────────────────────────────────────────────────────────
        // CORREÇÃO fromMe EM GRUPOS
        // A Evolution API às vezes dispara o webhook com fromMe=false para
        // mensagens enviadas pelo próprio número em grupos. Detectamos isso
        // comparando o participant com o JID da instância pelos últimos 8 dígitos.
        // ──────────────────────────────────────────────────────────────────────
        if (!$fromMe && $isGrupo && $participant) {
            $ownJid    = $jidProprio ?? $senderJid;
            $ownLast8  = strlen($ownJid ?? '') > 0
                ? substr(preg_replace('/\D/', '', preg_replace('/@.+$/', '', $ownJid)), -8)
                : '';
            $partLast8 = substr(preg_replace('/\D/', '', preg_replace('/@.+$/', '', $participant)), -8);

            if (strlen($ownLast8) === 8 && $ownLast8 === $partLast8) {
                $fromMe = true;
                Log::info("fromMe corrigido para true: participant {$participant} é o próprio número da instância.");
            }
        }

        // Número limpo (sem sufixo @...) — campo de exibição/busca
        $numero = preg_replace('/@.+$/', '', $remoteJid);

        // ──────────────────────────────────────────────────────────────────────
        // CONTATO
        //
        // Regras importantes:
        //
        // GRUPO → nunca usa pushName da mensagem como nome do grupo.
        //   O pushName em grupos é o nome de quem enviou (participante),
        //   não o nome do grupo. Buscamos o nome real na API.
        //
        // MENSAGEM ENVIADA (fromMe=true) → apenas confirma que o contato
        //   existe, sem sobrescrever nada. O pushName em mensagens fromMe
        //   é o nome da SUA conta WhatsApp, não do destinatário.
        //
        // MENSAGEM RECEBIDA (fromMe=false) → atualiza push_name com o nome
        //   real de quem enviou.
        // ──────────────────────────────────────────────────────────────────────
        if ($isGrupo) {
            // Grupo: firstOrCreate sem tocar em nome com dados do remetente
            $contato = WhatsappContato::firstOrCreate(
                [
                    'whatsapp_instancia_id' => $instancia->id,
                    'remote_jid'            => $remoteJid,
                ],
                [
                    'numero'   => $numero,
                    'is_grupo' => true,
                ]
            );

            if (!$contato->nome && !$contato->push_name) {
                $this->buscarNomeGrupo($contato, $instancia);
            }
            if (!$contato->foto_url) {
                $this->buscarFotoContato($contato, $instancia);
            }

        } elseif ($fromMe) {
            // Mensagem enviada por mim: só garante que o contato existe.
            // NÃO atualiza push_name — em fromMe o pushName é o nome da
            // nossa própria conta, não do destinatário.
            $contato = WhatsappContato::firstOrCreate(
                [
                    'whatsapp_instancia_id' => $instancia->id,
                    'remote_jid'            => $remoteJid,
                ],
                [
                    'numero'   => $numero,
                    'is_grupo' => false,
                    'lid_jid'  => $lidOriginal,
                ]
            );
            // Preenche lid_jid em contatos existentes que ainda não têm o mapeamento
            if ($lidOriginal && !$contato->lid_jid) {
                $contato->update(['lid_jid' => $lidOriginal]);
            }
            if (!$contato->foto_url && $contato->wasRecentlyCreated) {
                $this->buscarFotoContato($contato, $instancia);
            }

        } else {
            // Mensagem recebida: atualiza push_name com o nome real do contato
            $updateData = [
                'numero'    => $numero,
                'push_name' => $pushName,
                'is_grupo'  => false,
            ];
            if ($lidOriginal) {
                $updateData['lid_jid'] = $lidOriginal;
            }
            $contato = WhatsappContato::updateOrCreate(
                [
                    'whatsapp_instancia_id' => $instancia->id,
                    'remote_jid'            => $remoteJid,
                ],
                $updateData
            );

            // Auto-preenche nome da agenda na primeira vez que o contato não tem nome manual
            if (!$contato->nome) {
                $this->tentarPreencherNomeAgenda($contato, $instancia);
            }
            if (!$contato->foto_url) {
                $this->buscarFotoContato($contato, $instancia);
            }
        }

        // ──────────────────────────────────────────────────────────────────────
        // CONVERSA
        // ──────────────────────────────────────────────────────────────────────
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

        // ──────────────────────────────────────────────────────────────────────
        // MENSAGEM
        // ──────────────────────────────────────────────────────────────────────
        [$tipo, $conteudo] = $this->extrairConteudoMensagem($data);

        if (!$messageId) {
            $messageId = 'sem_id_' . md5(json_encode($payload) . microtime(true));
        }

        // Para mensagens enviadas (fromMe=true) verifica se já existe no banco
        // (criada por enviarTexto). Se existir, apenas atualiza o status — NUNCA
        // sobrescreve whatsapp_conversa_id/whatsapp_contato_id porque o enviarTexto
        // já as roteou corretamente, e o webhook pode ter resolvido @lid para o
        // contato errado (ex: mesmo nome, número diferente).
        if ($fromMe) {
            $mensagemExistente = WhatsappMensagem::where('whatsapp_instancia_id', $instancia->id)
                ->where('message_id', $messageId)
                ->first();

            if ($mensagemExistente) {
                $mensagemExistente->update([
                    'payload'      => $payload,
                    'status_envio' => 'enviada',
                    'enviada_em'   => now(),
                    'direcao'      => 'saida', // corrige caso tenha sido armazenada como entrada
                ]);
                $mensagem = $mensagemExistente;
            } else {
                // Enviada pelo celular (não pelo app): cria normalmente
                $mensagem = WhatsappMensagem::create([
                    'whatsapp_instancia_id' => $instancia->id,
                    'whatsapp_conversa_id'  => $conversa->id,
                    'whatsapp_contato_id'   => $contato->id,
                    'message_id'            => $messageId,
                    'participant'           => $participant,
                    'direcao'               => 'saida',
                    'tipo'                  => $tipo,
                    'conteudo'              => $conteudo,
                    'payload'               => $payload,
                    'status_envio'          => 'enviada',
                    'enviada_em'            => now(),
                ]);
            }
        } else {
            // Mensagem recebida: updateOrCreate normal
            $mensagem = WhatsappMensagem::updateOrCreate(
                [
                    'whatsapp_instancia_id' => $instancia->id,
                    'message_id'            => $messageId,
                ],
                [
                    'whatsapp_conversa_id' => $conversa->id,
                    'whatsapp_contato_id'  => $contato->id,
                    'participant'          => $participant,
                    'direcao'              => 'entrada',
                    'tipo'                 => $tipo,
                    'conteudo'             => $conteudo,
                    'payload'              => $payload,
                    'status_envio'         => null,
                    'enviada_em'           => now(),
                ]
            );
        }

        // Cria registro de anexo para mídias (imagem, áudio, vídeo, documento)
        // Usa doesntExist para evitar duplicata independente de como a mensagem foi criada
        if (in_array($tipo, ['imagem', 'audio', 'video', 'documento', 'figurinha'])
            && $mensagem->anexos()->doesntExist()
        ) {
            $this->criarAnexoMensagem($mensagem, $data);
        }

        // Vincula reply_to_message_id quando a mensagem é uma resposta a outra
        if (!$mensagem->reply_to_message_id) {
            $msg      = $data['message'] ?? [];
            // Evolution API coloca contextInfo direto em $data, não dentro do tipo de mensagem
            $ctxInfo  = $data['contextInfo']
                     ?? $msg['extendedTextMessage']['contextInfo']
                     ?? $msg['imageMessage']['contextInfo']
                     ?? $msg['videoMessage']['contextInfo']
                     ?? $msg['audioMessage']['contextInfo']
                     ?? $msg['documentMessage']['contextInfo']
                     ?? $msg['stickerMessage']['contextInfo']
                     ?? null;
            $stanzaId = $ctxInfo['stanzaId'] ?? null;
            if ($stanzaId) {
                $replyMsg = WhatsappMensagem::where('whatsapp_instancia_id', $instancia->id)
                    ->where('message_id', $stanzaId)
                    ->first();
                if ($replyMsg) {
                    $mensagem->update(['reply_to_message_id' => $replyMsg->id]);
                }
            }
        }

        // Preview para a lista de conversas
        $preview = $conteudo ?: match ($tipo) {
            'imagem'      => '📷 Imagem',
            'audio'       => '🎤 Áudio',
            'video'       => '🎥 Vídeo',
            'documento'   => '📄 Documento',
            'figurinha'   => '😄 Figurinha',
            'localizacao' => '📍 Localização',
            'contato'     => '👤 Contato',
            default       => 'Mensagem',
        };

        // Em grupos: prefixo com o nome de quem enviou (pushName ou número)
        if ($isGrupo && !$fromMe && $participant) {
            $prefixo = $pushName
                ?: preg_replace('/[^0-9]/', '', preg_replace('/@.+$/', '', $participant));
            $preview = $prefixo . ': ' . $preview;
        }

        $conversa->update([
            'ultima_mensagem_preview' => $preview,
            'ultima_mensagem_em'      => now(),
            'nao_lidas'               => $fromMe ? $conversa->nao_lidas : $conversa->nao_lidas + 1,
        ]);

        return response()->json([
            'ok'          => true,
            'mensagem_id' => $mensagem->id,
        ]);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // CRIAÇÃO DE ANEXO PARA MENSAGENS DE MÍDIA
    // Cria o registro no banco. Se o payload tiver base64 já salva o arquivo;
    // caso contrário deixa caminho_local=null e o proxy baixa sob demanda.
    // ──────────────────────────────────────────────────────────────────────────
    private function criarAnexoMensagem(WhatsappMensagem $mensagem, array $data): void
    {
        try {
            $message = $data['message'] ?? [];

            $msgData = $message['audioMessage']
                ?? $message['imageMessage']
                ?? $message['videoMessage']
                ?? $message['documentMessage']
                ?? $message['stickerMessage']
                ?? [];

            $mimeType   = $msgData['mimetype'] ?? null;
            $fileLength = isset($msgData['fileLength']) ? (int) $msgData['fileLength'] : null;
            $fileName   = $msgData['fileName'] ?? $msgData['title'] ?? null;
            $ext        = $this->extPorMime($mimeType ?? '', $mensagem->tipo);
            $nomeArquivo = $fileName ?: ('wa_' . $mensagem->tipo . '_' . $mensagem->id . '.' . $ext);

            $caminhoLocal = null;

            // Se a Evolution API enviou o base64 direto no webhook (depende de configuração)
            $base64 = $data['base64'] ?? null;
            if ($base64) {
                $caminho = 'whatsapp/midia/' . $nomeArquivo;
                Storage::disk('public')->put($caminho, base64_decode($base64));
                $caminhoLocal = $caminho;
            }

            WhatsappAnexo::create([
                'whatsapp_mensagem_id' => $mensagem->id,
                'tipo'                 => $mensagem->tipo,
                'nome_arquivo'         => $nomeArquivo,
                'mime_type'            => $mimeType,
                'tamanho'              => $fileLength,
                'caminho_local'        => $caminhoLocal,
            ]);
        } catch (\Throwable $e) {
            Log::warning('Falha ao criar anexo de mídia WhatsApp', [
                'mensagem_id' => $mensagem->id,
                'erro'        => $e->getMessage(),
            ]);
        }
    }

    private function extPorMime(string $mime, string $tipo): string
    {
        $map = [
            'audio/ogg'  => 'ogg', 'audio/mpeg' => 'mp3',
            'audio/mp4'  => 'm4a', 'audio/webm' => 'webm',
            'image/jpeg' => 'jpg', 'image/png'  => 'png',
            'image/webp' => 'webp','image/gif'  => 'gif',
            'video/mp4'  => 'mp4', 'video/3gpp' => '3gp',
            'video/webm' => 'webm',
            'application/pdf' => 'pdf',
        ];
        foreach ($map as $pattern => $ext) {
            if (str_contains($mime, $pattern)) {
                return $ext;
            }
        }
        return match ($tipo) {
            'audio'    => 'ogg',
            'imagem'   => 'jpg',
            'video'    => 'mp4',
            'figurinha'=> 'webp',
            default    => 'bin',
        };
    }

    // ──────────────────────────────────────────────────────────────────────────
    // EXTRAÇÃO DE CONTEÚDO DA MENSAGEM
    // ──────────────────────────────────────────────────────────────────────────
    private function extrairConteudoMensagem(array $data): array
    {
        $message = $data['message'] ?? [];

        if (isset($message['conversation'])) {
            return ['texto', $message['conversation']];
        }

        if (isset($message['extendedTextMessage']['text'])) {
            return ['texto', $message['extendedTextMessage']['text']];
        }

        if (isset($message['imageMessage'])) {
            return ['imagem', $message['imageMessage']['caption'] ?? null];
        }

        if (isset($message['audioMessage'])) {
            return ['audio', null];
        }

        if (isset($message['videoMessage'])) {
            return ['video', $message['videoMessage']['caption'] ?? null];
        }

        if (isset($message['documentMessage'])) {
            return ['documento', $message['documentMessage']['title'] ?? $message['documentMessage']['fileName'] ?? null];
        }

        if (isset($message['stickerMessage'])) {
            return ['figurinha', null];
        }

        if (isset($message['locationMessage'])) {
            return ['localizacao', $message['locationMessage']['name'] ?? null];
        }

        if (isset($message['contactMessage']) || isset($message['contactsArrayMessage'])) {
            $lista = isset($message['contactMessage'])
                ? [$message['contactMessage']]
                : ($message['contactsArrayMessage']['contacts'] ?? []);

            $partes = [];
            foreach ($lista as $c) {
                $nome = $c['displayName'] ?? null;
                $vcard = $c['vcard'] ?? '';
                // Extrai waid (número WhatsApp) do vCard: TEL;...;waid=XXXXX:...
                $tel = null;
                if (preg_match('/waid=(\d+)/', $vcard, $m)) {
                    $tel = $m[1];
                } elseif (preg_match('/TEL[^:\n]*:([^\n]+)/', $vcard, $m)) {
                    $tel = preg_replace('/\D/', '', trim($m[1]));
                }
                if ($nome || $tel) {
                    $partes[] = ($nome ?: $tel) . ($nome && $tel ? '|' . $tel : '');
                }
            }
            return ['contato', implode(';;', $partes) ?: null];
        }

        return ['outro', null];
    }

    // ──────────────────────────────────────────────────────────────────────────
    // RESOLUÇÃO DE @lid → @s.whatsapp.net
    //
    // Tenta descobrir o JID real de um contato @lid consultando a Evolution API.
    // Retorna null se não conseguir — nesse caso o @lid é mantido e enviado
    // diretamente (Evolution API v2 suporta).
    // ──────────────────────────────────────────────────────────────────────────
    private function resolverLid(string $lid, ?string $pushName, WhatsappInstancia $instancia): ?string
    {
        if (!$pushName) {
            return null;
        }

        try {
            // Tentativa 1: busca por pushName na lista de contatos
            $response = Http::withHeaders(['apikey' => $instancia->api_key])
                ->timeout(10)
                ->post(
                    rtrim($instancia->api_url, '/') . '/chat/findContacts/' . $instancia->instance_name,
                    ['where' => ['pushName' => $pushName]]
                );

            if ($response->successful()) {
                $contatos = $response->json();

                if (is_array($contatos)) {
                    foreach ($contatos as $c) {
                        $cJid  = $c['remoteJid'] ?? $c['id'] ?? '';
                        $cName = $c['pushName'] ?? '';

                        if (str_contains($cJid, '@s.whatsapp.net') && $cName === $pushName) {
                            Log::info("@lid resolvido: {$lid} → {$cJid} ({$pushName})");
                            return $cJid;
                        }
                    }
                }
            }

            // Tentativa 2: fetchProfile com o próprio @lid — Evolution API v2 às
            // vezes consegue resolver o JID real pelo lid diretamente
            $resp2 = Http::withHeaders(['apikey' => $instancia->api_key])
                ->timeout(10)
                ->post(
                    rtrim($instancia->api_url, '/') . '/chat/fetchProfile/' . $instancia->instance_name,
                    ['number' => $lid]
                );

            if ($resp2->successful()) {
                $profile = $resp2->json();
                $profileJid = $profile['jid'] ?? $profile['id'] ?? $profile['remoteJid'] ?? null;
                if ($profileJid && str_contains($profileJid, '@s.whatsapp.net')) {
                    Log::info("@lid resolvido via fetchProfile: {$lid} → {$profileJid}");
                    return $profileJid;
                }
            }

            // Tentativa 3: busca pelo número embutido no @lid (ex: 5511999@lid)
            $lidBase    = explode('@', $lid)[0] ?? '';
            $lidNumeros = preg_replace('/[^0-9]/', '', $lidBase);

            if (strlen($lidNumeros) >= 8) {
                $response3 = Http::withHeaders(['apikey' => $instancia->api_key])
                    ->timeout(10)
                    ->post(
                        rtrim($instancia->api_url, '/') . '/chat/findContacts/' . $instancia->instance_name,
                        ['where' => ['remoteJid' => ['$like' => $lidNumeros . '@s.whatsapp.net']]]
                    );

                if ($response3->successful()) {
                    $contatos3 = $response3->json();
                    if (is_array($contatos3)) {
                        foreach ($contatos3 as $c) {
                            $cJid = $c['remoteJid'] ?? $c['id'] ?? '';
                            if (str_contains($cJid, '@s.whatsapp.net')) {
                                Log::info("@lid resolvido por número: {$lid} → {$cJid}");
                                return $cJid;
                            }
                        }
                    }
                }
            }
        } catch (\Throwable $e) {
            Log::warning("Falha ao resolver @lid {$lid}: {$e->getMessage()}");
        }

        return null;
    }

    // ──────────────────────────────────────────────────────────────────────────
    // FUSÃO DE CONTATO @lid DUPLICADO
    //
    // Quando um @lid é vinculado a um contato real (via Etapas 2/3 ou Etapa 0),
    // pode existir um contato "órfão" com remote_jid = @lid criado por mensagens
    // anteriores ao mapeamento. Esta função move todo o histórico para o contato
    // principal e deleta o duplicado.
    // ──────────────────────────────────────────────────────────────────────────
    private function fundirDuplicadoLid(WhatsappContato $principal, string $lidOriginal): void
    {
        try {
            $duplicado = WhatsappContato::where('whatsapp_instancia_id', $principal->whatsapp_instancia_id)
                ->where('remote_jid', $lidOriginal)
                ->where('id', '!=', $principal->id)
                ->first();

            if (!$duplicado) return;

            $convDestino = WhatsappConversa::where('whatsapp_instancia_id', $principal->whatsapp_instancia_id)
                ->where('whatsapp_contato_id', $principal->id)
                ->first();

            foreach ($duplicado->conversas as $convOrigem) {
                if ($convDestino) {
                    WhatsappMensagem::where('whatsapp_conversa_id', $convOrigem->id)
                        ->update(['whatsapp_conversa_id' => $convDestino->id, 'whatsapp_contato_id' => $principal->id]);
                    $convOrigem->delete();
                } else {
                    $convOrigem->update(['whatsapp_contato_id' => $principal->id]);
                    $convDestino = $convOrigem;
                }
            }

            WhatsappMensagem::where('whatsapp_contato_id', $duplicado->id)
                ->update(['whatsapp_contato_id' => $principal->id]);
            $duplicado->delete();

            Log::info("Contato @lid duplicado fundido automaticamente: {$lidOriginal} (id={$duplicado->id}) → principal id={$principal->id}");
        } catch (\Throwable $e) {
            Log::warning("Falha ao fundir contato @lid duplicado {$lidOriginal}: " . $e->getMessage());
        }
    }

    // ──────────────────────────────────────────────────────────────────────────
    // AUTO-PREENCHIMENTO DO NOME DA AGENDA
    //
    // Chamado na primeira mensagem recebida de um contato sem nome manual.
    // Consulta o endpoint de contatos da Evolution API: se o número estiver
    // salvo na agenda do celular, o campo `name` vem preenchido com o nome
    // que o usuário salvou — diferente de `pushName` (nome que o próprio
    // contato escolheu no WhatsApp).
    // ──────────────────────────────────────────────────────────────────────────
    private function tentarPreencherNomeAgenda(WhatsappContato $contato, WhatsappInstancia $instancia): void
    {
        try {
            $resp = Http::withHeaders(['apikey' => $instancia->api_key])
                ->timeout(4)
                ->post(
                    rtrim($instancia->api_url, '/') . '/contacts/fetch/' . $instancia->instance_name,
                    ['where' => ['remoteJid' => $contato->remote_jid]]
                );

            if (!$resp->successful()) return;

            $data = $resp->json();
            // Resposta pode ser array de registros ou objeto único
            $c    = (is_array($data) && array_is_list($data)) ? ($data[0] ?? null) : $data;
            if (!$c) return;

            $nome = $c['name'] ?? $c['verifiedName'] ?? null;
            // Ignora se for vazio, número ou JID
            if ($nome && !str_contains($nome, '@') && !preg_match('/^\+?\d+$/', $nome)) {
                $contato->update(['nome' => $nome]);
                Log::info("Nome da agenda preenchido automaticamente: {$contato->remote_jid} → {$nome}");
            }
        } catch (\Throwable) { }
    }

    // ──────────────────────────────────────────────────────────────────────────
    // NORMALIZAÇÃO DE NÚMERO BRASILEIRO (8 → 9 DÍGITOS)
    // ──────────────────────────────────────────────────────────────────────────
    private function normalizarJidBrasileiro(string $jid): string
    {
        if (!str_contains($jid, '@s.whatsapp.net')) return $jid;
        $numero = preg_replace('/@.+$/', '', $jid);
        // 55 + 2 DDD + 8 dígitos = 12 → insere 9 após o 4º dígito: 5579|99683306 → 5579|9|99683306
        if (strlen($numero) === 12 && str_starts_with($numero, '55')) {
            $numero = substr($numero, 0, 4) . '9' . substr($numero, 4);
        }
        return $numero . '@s.whatsapp.net';
    }

    // ──────────────────────────────────────────────────────────────────────────
    // BUSCA NOME DO GRUPO NA EVOLUTION API
    //
    // Chamada best-effort: se falhar, o grupo fica sem nome mas funciona normal.
    // Na próxima sincronização manual o nome é preenchido.
    // ──────────────────────────────────────────────────────────────────────────
    private function buscarNomeGrupo(WhatsappContato $contato, WhatsappInstancia $instancia): void
    {
        try {
            $baseUrl  = rtrim($instancia->api_url, '/');
            $instance = $instancia->instance_name;

            // Evolution API v2: POST /group/findGroupInfos/{instance}
            $response = Http::withHeaders(['apikey' => $instancia->api_key])
                ->timeout(8)
                ->post("{$baseUrl}/group/findGroupInfos/{$instance}", [
                    'groupJid' => $contato->remote_jid,
                ]);

            // Alguns builds usam GET com query string
            if (!$response->successful()) {
                $response = Http::withHeaders(['apikey' => $instancia->api_key])
                    ->timeout(8)
                    ->get("{$baseUrl}/group/findGroupInfos/{$instance}", [
                        'groupJid' => $contato->remote_jid,
                    ]);
            }

            if ($response->successful()) {
                $info = $response->json();

                // Normaliza: a API pode retornar objeto único ou array de objetos
                if (isset($info[0])) {
                    $info = $info[0];
                }

                $subject = $info['subject'] ?? $info['name'] ?? null;

                if ($subject) {
                    $contato->update([
                        'nome'      => $subject,
                        'push_name' => $subject,
                    ]);
                    Log::info("Nome do grupo preenchido: {$contato->remote_jid} → {$subject}");
                }
            }
        } catch (\Throwable $e) {
            Log::warning("Falha ao buscar nome do grupo {$contato->remote_jid}: {$e->getMessage()}");
        }
    }

    // ──────────────────────────────────────────────────────────────────────────
    // BUSCA FOTO DE PERFIL AUTOMATICAMENTE (contato ou grupo)
    // ──────────────────────────────────────────────────────────────────────────
    private function buscarFotoContato(WhatsappContato $contato, WhatsappInstancia $instancia): void
    {
        try {
            $base    = rtrim($instancia->api_url, '/');
            $inst    = $instancia->instance_name;
            $headers = ['apikey' => $instancia->api_key];

            if ($contato->is_grupo) {
                // Foto de grupo
                $r = Http::withHeaders($headers)->timeout(6)
                    ->post("{$base}/group/fetchGroupInfo/{$inst}", ['groupJid' => $contato->remote_jid]);
                if (!$r->successful()) {
                    $r = Http::withHeaders($headers)->timeout(6)
                        ->post("{$base}/group/findGroupInfos/{$inst}", ['groupJid' => $contato->remote_jid]);
                }
                if ($r->successful()) {
                    $info = $r->json();
                    if (isset($info[0])) $info = $info[0];
                    $fotoUrl = $info['pictureUrl'] ?? $info['profilePictureUrl'] ?? null;
                    if ($fotoUrl) {
                        $contato->update(['foto_url' => $fotoUrl]);
                    }
                }
            } else {
                // Foto de contato individual
                $r = Http::withHeaders($headers)->timeout(6)
                    ->post("{$base}/chat/fetchProfile/{$inst}", ['number' => $contato->remote_jid]);
                if ($r->successful()) {
                    $data    = $r->json();
                    $fotoUrl = $data['profilePictureUrl'] ?? $data['profilePicUrl'] ?? $data['picture'] ?? null;
                    if ($fotoUrl) {
                        $contato->update(['foto_url' => $fotoUrl]);
                    }
                }
            }
        } catch (\Throwable) { }
    }
}