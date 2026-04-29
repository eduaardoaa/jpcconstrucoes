<?php

namespace App\Services;

use App\Models\WhatsappContato;
use App\Models\WhatsappInstancia;
use App\Models\WhatsappLidMap;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsappLidResolverService
{
    /**
     * Resolve um @lid para o JID real (@s.whatsapp.net).
     *
     * Estratégia em cascata:
     * 1. Verifica tabela de cache whatsapp_lid_map
     * 2. Verifica se o payload do webhook contém senderPn ou remoteJidAlt
     * 3. Consulta Evolution API /chat/findContacts por pushName
     * 4. Consulta Evolution API /contact/profile/{lid}
     * 5. Faz fetch de todos os contatos e compara
     */
    public function resolver(
        string $lid,
        WhatsappInstancia $instancia,
        ?string $pushName = null,
        ?array $webhookPayload = null
    ): ?string {
        // 1. Cache local (tabela whatsapp_lid_map)
        $cached = WhatsappLidMap::where('whatsapp_instancia_id', $instancia->id)
            ->where('lid', $lid)
            ->first();

        if ($cached) {
            Log::debug("LID resolvido via cache: {$lid} -> {$cached->jid_real}");
            return $cached->jid_real;
        }

        // 2. Campos alternativos no payload do webhook
        if ($webhookPayload) {
            $jidAlt = $this->extrairJidAlternativo($webhookPayload);
            if ($jidAlt) {
                $this->salvarMapeamento($instancia->id, $lid, $jidAlt, $pushName);
                Log::info("LID resolvido via payload alt: {$lid} -> {$jidAlt}");
                return $jidAlt;
            }
        }

        $baseUrl = rtrim($instancia->api_url, '/');
        $instanceName = $instancia->instance_name;

        // 3. Consulta findContacts por pushName (se disponível)
        if ($pushName) {
            $jid = $this->resolverViaPushName($baseUrl, $instanceName, $instancia->api_key, $lid, $pushName);
            if ($jid) {
                $this->salvarMapeamento($instancia->id, $lid, $jid, $pushName);
                return $jid;
            }
        }

        // 4. Consulta profile do LID
        $jid = $this->resolverViaProfile($baseUrl, $instanceName, $instancia->api_key, $lid);
        if ($jid) {
            $this->salvarMapeamento($instancia->id, $lid, $jid, $pushName);
            return $jid;
        }

        // 5. Fetch completo de contatos (último recurso)
        $jid = $this->resolverViaFetchCompleto($baseUrl, $instanceName, $instancia->api_key, $lid, $pushName);
        if ($jid) {
            $this->salvarMapeamento($instancia->id, $lid, $jid, $pushName);
            return $jid;
        }

        Log::warning("Não foi possível resolver LID: {$lid} (pushName={$pushName})");
        return null;
    }

    /**
     * Extrai JID alternativo do payload do webhook.
     * Verifica: senderPn, remoteJidAlt, participantAlt, etc.
     */
    private function extrairJidAlternativo(array $payload): ?string
    {
        $data = $payload['data'] ?? $payload;

        // senderPn — campo introduzido por Baileys mais recentes
        $senderPn = $data['senderPn'] ?? $data['key']['senderPn'] ?? null;
        if ($senderPn && !str_contains($senderPn, '@lid')) {
            // senderPn pode ser só o número ou número@s.whatsapp.net
            return $this->normalizarParaJid($senderPn);
        }

        // remoteJidAlt — alternativa ao remoteJid
        $jidAlt = $data['remoteJidAlt'] ?? $data['key']['remoteJidAlt'] ?? null;
        if ($jidAlt && str_contains($jidAlt, '@s.whatsapp.net')) {
            return $jidAlt;
        }

        // participantAlt — para grupos
        $participantAlt = $data['participantAlt'] ?? $data['key']['participantAlt'] ?? null;
        if ($participantAlt && str_contains($participantAlt, '@s.whatsapp.net')) {
            return $participantAlt;
        }

        // Verifica dentro de messageContextInfo (se existir)
        $contextInfo = $data['message']['extendedTextMessage']['contextInfo'] ?? [];
        if (!empty($contextInfo['participant']) && str_contains($contextInfo['participant'], '@s.whatsapp.net')) {
            return $contextInfo['participant'];
        }

        return null;
    }

    /**
     * Resolve LID via busca de contatos por pushName.
     */
    private function resolverViaPushName(
        string $baseUrl,
        string $instanceName,
        string $apiKey,
        string $lid,
        string $pushName
    ): ?string {
        try {
            $response = Http::withHeaders(['apikey' => $apiKey])
                ->timeout(10)
                ->post("{$baseUrl}/chat/findContacts/{$instanceName}", [
                    'where' => ['pushName' => $pushName],
                ]);

            if (!$response->successful()) {
                return null;
            }

            $contatos = $response->json();
            if (!is_array($contatos)) {
                return null;
            }

            foreach ($contatos as $c) {
                $cJid  = $c['remoteJid'] ?? $c['id'] ?? '';
                $cName = $c['pushName'] ?? '';

                if (str_contains($cJid, '@s.whatsapp.net') && $cName === $pushName) {
                    Log::info("LID resolvido via findContacts (pushName): {$lid} -> {$cJid}");
                    return $cJid;
                }
            }
        } catch (\Throwable $e) {
            Log::warning("Erro ao resolver LID via pushName: {$e->getMessage()}");
        }

        return null;
    }

    /**
     * Resolve LID via endpoint de profile.
     */
    private function resolverViaProfile(
        string $baseUrl,
        string $instanceName,
        string $apiKey,
        string $lid
    ): ?string {
        try {
            // Tenta o endpoint /contact/profile
            $response = Http::withHeaders(['apikey' => $apiKey])
                ->timeout(10)
                ->get("{$baseUrl}/contact/profile/{$instanceName}", [
                    'remoteJid' => $lid,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                // Procura número no retorno
                $phoneNumber = $data['phoneNumber'] ?? $data['number'] ?? $data['remoteJid'] ?? null;
                if ($phoneNumber && !str_contains($phoneNumber, '@lid')) {
                    $jid = $this->normalizarParaJid($phoneNumber);
                    Log::info("LID resolvido via profile: {$lid} -> {$jid}");
                    return $jid;
                }
            }
        } catch (\Throwable $e) {
            Log::debug("Falha ao resolver LID via profile: {$e->getMessage()}");
        }

        return null;
    }

    /**
     * Resolve LID fazendo fetch completo de contatos (último recurso).
     */
    private function resolverViaFetchCompleto(
        string $baseUrl,
        string $instanceName,
        string $apiKey,
        string $lid,
        ?string $pushName
    ): ?string {
        try {
            $response = Http::withHeaders(['apikey' => $apiKey])
                ->timeout(15)
                ->post("{$baseUrl}/chat/findContacts/{$instanceName}", []);

            if (!$response->successful()) {
                return null;
            }

            $contatos = $response->json();
            if (!is_array($contatos)) {
                return null;
            }

            // Extrai o número do LID para comparação
            $lidNumero = preg_replace('/[^0-9]/', '', str_replace('@lid', '', $lid));

            foreach ($contatos as $c) {
                $cJid  = $c['remoteJid'] ?? $c['id'] ?? '';
                $cName = $c['pushName'] ?? '';
                $cLid  = $c['lid'] ?? $c['linkedId'] ?? '';

                // Match direto por LID
                if ($cLid === $lid && str_contains($cJid, '@s.whatsapp.net')) {
                    Log::info("LID resolvido via fetch completo (match direto): {$lid} -> {$cJid}");

                    // Aproveita para salvar vários mapeamentos em batch
                    $this->salvarMapeamentosBatch($contatos, $baseUrl, $instanceName, $apiKey);

                    return $cJid;
                }

                // Match por pushName como fallback (se não tem match direto)
                if ($pushName && $cName === $pushName && str_contains($cJid, '@s.whatsapp.net')) {
                    Log::info("LID resolvido via fetch completo (pushName): {$lid} -> {$cJid}");
                    return $cJid;
                }
            }
        } catch (\Throwable $e) {
            Log::warning("Erro ao resolver LID via fetch completo: {$e->getMessage()}");
        }

        return null;
    }

    /**
     * Salva mapeamento único no cache.
     */
    public function salvarMapeamento(int $instanciaId, string $lid, string $jidReal, ?string $pushName = null): void
    {
        $numero = preg_replace('/\D/', '', str_replace('@s.whatsapp.net', '', $jidReal));

        WhatsappLidMap::updateOrCreate(
            [
                'whatsapp_instancia_id' => $instanciaId,
                'lid' => $lid,
            ],
            [
                'numero' => $numero,
                'jid_real' => $jidReal,
                'push_name' => $pushName,
            ]
        );

        // Também atualiza o contato se existir com @lid no remote_jid
        WhatsappContato::where('whatsapp_instancia_id', $instanciaId)
            ->where('remote_jid', $lid)
            ->update([
                'remote_jid' => $jidReal,
                'numero' => $numero,
            ]);
    }

    /**
     * Salva mapeamentos em batch (quando fazemos fetch completo).
     */
    private function salvarMapeamentosBatch(array $contatos, string $baseUrl, string $instanceName, string $apiKey): void
    {
        // Não fazemos isso aqui para não atrasar o webhook — agendamos para background
        // Por ora, salvamos apenas os que já têm LID mapeado
    }

    /**
     * Normaliza qualquer formato para JID @s.whatsapp.net.
     */
    private function normalizarParaJid(string $input): string
    {
        // Já é um JID válido
        if (str_contains($input, '@s.whatsapp.net')) {
            return $input;
        }

        // Remove tudo que não é número
        $numero = preg_replace('/\D/', '', $input);

        return $numero . '@s.whatsapp.net';
    }

    /**
     * Resolve número para envio a partir de um contato.
     * Usado pelo controller na hora de enviar mensagem.
     */
    public function resolverNumeroParaEnvio(WhatsappContato $contato, WhatsappInstancia $instancia): string
    {
        $jid = $contato->remote_jid;

        // Grupos: enviar o JID completo do grupo
        if ($contato->is_grupo || str_contains($jid, '@g.us')) {
            return $jid;
        }

        // JID normal: extrai só o número
        if (str_contains($jid, '@s.whatsapp.net')) {
            return preg_replace('/\D/', '', str_replace('@s.whatsapp.net', '', $jid));
        }

        // Tem número salvo e válido (não contém @)
        if ($contato->numero && !str_contains($contato->numero, '@')) {
            $numero = preg_replace('/\D/', '', $contato->numero);
            if (strlen($numero) >= 10) {
                return $numero;
            }
        }

        // É @lid — tenta resolver via cache/API
        if (str_contains($jid, '@lid')) {
            $jidResolvido = $this->resolver($jid, $instancia, $contato->push_name);

            if ($jidResolvido) {
                // Atualiza o contato com o JID real
                $contato->update([
                    'remote_jid' => $jidResolvido,
                    'numero' => preg_replace('/\D/', '', str_replace('@s.whatsapp.net', '', $jidResolvido)),
                ]);

                return preg_replace('/\D/', '', str_replace('@s.whatsapp.net', '', $jidResolvido));
            }

            throw new \Exception(
                'Esse contato usa o formato @lid (novo sistema do WhatsApp) e ainda não foi possível descobrir o número real. ' .
                'Peça para o contato enviar uma nova mensagem ou sincronize os contatos da instância.'
            );
        }

        // Fallback: trata como número
        return preg_replace('/\D/', '', $jid);
    }
}
