<?php

namespace App\Services;

use App\Models\WhatsappContato;
use App\Models\WhatsappConversa;
use App\Models\WhatsappInstancia;
use App\Models\WhatsappMensagem;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsappEvolutionSyncService
{
    public function sincronizar(WhatsappInstancia $instancia): int
    {
        $baseUrl  = rtrim($instancia->api_url, '/');
        $instance = $instancia->instance_name;

        $chatsResponse = Http::withHeaders([
            'apikey'       => $instancia->api_key,
            'Content-Type' => 'application/json',
        ])->post("{$baseUrl}/chat/findChats/{$instance}", []);

        if (!$chatsResponse->successful()) {
            Log::error("Sync WhatsApp: falha ao buscar chats", [
                'instancia' => $instance,
                'status'    => $chatsResponse->status(),
                'body'      => $chatsResponse->body(),
            ]);
            return 0;
        }

        $chats = $this->normalizarLista($chatsResponse->json());
        $total = 0;

        foreach ($chats as $chat) {
            $remoteJid = $chat['id']
                ?? $chat['remoteJid']
                ?? $chat['jid']
                ?? $chat['key']['remoteJid']
                ?? null;

            if (!$remoteJid) {
                continue;
            }

            // ── @lid: contatos individuais com Privacy ID do WhatsApp ──────────
            // Ignoramos @lid no sync pois não temos pushName aqui para tentar
            // resolver. Esses contatos serão criados/atualizados quando chegarem
            // mensagens via webhook (que traz pushName no payload).
            if (str_contains($remoteJid, '@lid')) {
                Log::info("Sync: pulando @lid sem resolução → {$remoteJid}");
                continue;
            }

            $isGrupo = str_contains($remoteJid, '@g.us');
            $numero  = preg_replace('/@.+$/', '', $remoteJid);

            if ($isGrupo) {
                // Para grupos: nome vem do subject do chat
                $nomeGrupo = $chat['subject']
                    ?? $chat['name']
                    ?? $chat['pushName']
                    ?? null;

                $contato = WhatsappContato::updateOrCreate(
                    [
                        'whatsapp_instancia_id' => $instancia->id,
                        'remote_jid'            => $remoteJid,
                    ],
                    [
                        'numero'    => $numero,
                        'nome'      => $nomeGrupo ?? $numero,
                        'push_name' => $nomeGrupo,
                        'foto_url'  => $chat['profilePicUrl'] ?? $chat['picture'] ?? null,
                        'is_grupo'  => true,
                    ]
                );

                // Se ainda não tem nome, tenta buscar via API de grupos
                if (!$contato->nome || $contato->nome === $numero) {
                    $this->buscarNomeGrupo($contato, $instancia);
                }
            } else {
                // Contato individual
                $contato = WhatsappContato::updateOrCreate(
                    [
                        'whatsapp_instancia_id' => $instancia->id,
                        'remote_jid'            => $remoteJid,
                    ],
                    [
                        'numero'    => $numero,
                        'nome'      => $chat['name'] ?? $chat['pushName'] ?? $numero,
                        'push_name' => $chat['pushName'] ?? null,
                        'foto_url'  => $chat['profilePicUrl'] ?? $chat['picture'] ?? null,
                        'is_grupo'  => false,
                    ]
                );
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

            $total += $this->sincronizarMensagens($instancia, $conversa, $contato, $remoteJid);
        }

        return $total;
    }

    private function sincronizarMensagens(
        WhatsappInstancia $instancia,
        WhatsappConversa $conversa,
        WhatsappContato $contato,
        string $remoteJid
    ): int {
        $baseUrl  = rtrim($instancia->api_url, '/');
        $instance = $instancia->instance_name;

        $response = Http::withHeaders([
            'apikey'       => $instancia->api_key,
            'Content-Type' => 'application/json',
        ])->post("{$baseUrl}/chat/findMessages/{$instance}", [
            'where' => [
                'key' => [
                    'remoteJid' => $remoteJid,
                ],
            ],
            'limit' => 500,
        ]);

        if (!$response->successful()) {
            return 0;
        }

        $mensagens = $this->normalizarMensagens($response->json());

        if (!is_array($mensagens)) {
            return 0;
        }

        $total = 0;

        foreach ($mensagens as $item) {
            $key       = $item['key'] ?? [];
            $messageId = $key['id'] ?? $item['id'] ?? null;

            if (!$messageId) {
                continue;
            }

            $jaExiste = WhatsappMensagem::where('whatsapp_instancia_id', $instancia->id)
                ->where('message_id', $messageId)
                ->exists();

            if ($jaExiste) {
                continue;
            }

            $message = $item['message'] ?? [];
            [$tipo, $conteudo] = $this->extrairConteudo($message);

            $fromMe    = (bool) ($key['fromMe'] ?? false);
            $createdAt = $this->extrairData($item);

            // Participant: quem enviou dentro do grupo
            $participant = $item['participant'] ?? $key['participant'] ?? null;

            WhatsappMensagem::create([
                'whatsapp_conversa_id'  => $conversa->id,
                'whatsapp_instancia_id' => $instancia->id,
                'whatsapp_contato_id'   => $contato->id,
                'message_id'            => $messageId,
                'participant'           => $participant,
                'direcao'               => $fromMe ? 'saida' : 'entrada',
                'tipo'                  => $tipo,
                'conteudo'              => $conteudo,
                'payload'               => $item,
                'status_envio'          => $fromMe ? 'enviada' : null,
                'enviada_em'            => $createdAt,
                'created_at'            => $createdAt,
                'updated_at'            => $createdAt,
            ]);

            $conversa->update([
                'ultima_mensagem_preview' => $conteudo ?: strtoupper($tipo),
                'ultima_mensagem_em'      => $createdAt,
            ]);

            $total++;
        }

        return $total;
    }

    // ──────────────────────────────────────────────────────────────────────────
    // BUSCA NOME DO GRUPO NA EVOLUTION API
    // ──────────────────────────────────────────────────────────────────────────
    private function buscarNomeGrupo(WhatsappContato $contato, WhatsappInstancia $instancia): void
    {
        try {
            $baseUrl  = rtrim($instancia->api_url, '/');
            $instance = $instancia->instance_name;

            $response = Http::withHeaders(['apikey' => $instancia->api_key])
                ->timeout(8)
                ->post("{$baseUrl}/group/findGroupInfos/{$instance}", [
                    'groupJid' => $contato->remote_jid,
                ]);

            if (!$response->successful()) {
                $response = Http::withHeaders(['apikey' => $instancia->api_key])
                    ->timeout(8)
                    ->get("{$baseUrl}/group/findGroupInfos/{$instance}", [
                        'groupJid' => $contato->remote_jid,
                    ]);
            }

            if ($response->successful()) {
                $info = $response->json();
                if (isset($info[0])) {
                    $info = $info[0];
                }
                $subject = $info['subject'] ?? $info['name'] ?? null;

                if ($subject) {
                    $contato->update([
                        'nome'      => $subject,
                        'push_name' => $subject,
                    ]);
                }
            }
        } catch (\Throwable $e) {
            Log::warning("Sync: falha ao buscar nome do grupo {$contato->remote_jid}: {$e->getMessage()}");
        }
    }

    private function normalizarLista($json): array
    {
        if (!is_array($json)) {
            return [];
        }

        return $json['data']
            ?? $json['chats']
            ?? $json['records']
            ?? $json;
    }

    private function normalizarMensagens($json): array
    {
        if (!is_array($json)) {
            return [];
        }

        return $json['messages']['records']
            ?? $json['messages']
            ?? $json['records']
            ?? $json['data']
            ?? $json;
    }

    private function extrairConteudo(array $message): array
    {
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
            return ['documento', $message['documentMessage']['fileName'] ?? $message['documentMessage']['title'] ?? null];
        }

        if (isset($message['stickerMessage'])) {
            return ['figurinha', null];
        }

        if (isset($message['locationMessage'])) {
            return ['localizacao', 'Localização recebida'];
        }

        if (isset($message['contactMessage'])) {
            return ['contato', $message['contactMessage']['displayName'] ?? 'Contato recebido'];
        }

        return ['outro', null];
    }

    private function extrairData(array $item): string
    {
        $timestamp = $item['messageTimestamp'] ?? $item['timestamp'] ?? null;

        if (is_numeric($timestamp)) {
            return date('Y-m-d H:i:s', (int) $timestamp);
        }

        return now()->format('Y-m-d H:i:s');
    }
}