<?php

namespace App\Services;

use App\Models\VagaCandidatura;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Smalot\PdfParser\Parser;

class AiResumeScannerService
{
    protected string $apiKey;
    protected string $model = 'llama-3.3-70b-versatile'; 

    public function __construct()
    {
        $this->apiKey = env('GROQ_API_KEY');
    }

    /**
     * Analisa um currículo em PDF e compara com a descrição da vaga.
     */
    public function scan(VagaCandidatura $candidatura): bool
    {
        try {
            $candidatura->update(['ai_status' => 'processing']);

            // 1. Extrair texto do currículo
            $text = $this->extractText($candidatura->curriculo_path);
            if (empty($text)) {
                $candidatura->update([
                    'ai_status' => 'failed',
                    'ai_summary' => 'Não foi possível extrair texto do currículo.'
                ]);
                return false;
            }

            // 2. Obter descrição da vaga
            $vagaDesc = "Vaga: " . $candidatura->vaga->titulo . "\n" .
                        "Descrição: " . $candidatura->vaga->descricao . "\n" .
                        "Requisitos: " . $candidatura->vaga->requisitos . "\n" .
                        "Diferenciais: " . ($candidatura->vaga->diferenciais ?? 'Não informados');

            // 3. Chamar API do Groq
            $response = $this->callGroq($text, $vagaDesc);

            if ($response && isset($response['score'])) {
                $candidatura->update([
                    'ai_score' => $response['score'],
                    'ai_summary' => $response['summary'],
                    'ai_pontos_fortes' => $response['strengths'] ?? null,
                    'ai_pontos_fracos' => $response['weaknesses'] ?? null,
                    'ai_status' => 'completed'
                ]);
                return true;
            }

            $candidatura->update(['ai_status' => 'failed']);
            return false;

        } catch (\Exception $e) {
            \Log::error("Erro no Scanner de IA: " . $e->getMessage());
            $candidatura->update(['ai_status' => 'failed']);
            return false;
        }
    }

    /**
     * Extrai o texto de um PDF usando Smalot PDF Parser.
     */
    protected function extractText(string $path): string
    {
        try {
            $fullPath = Storage::disk('public')->path($path);
            if (!file_exists($fullPath)) return '';

            $parser = new Parser();
            $pdf = $parser->parseFile($fullPath);
            return $pdf->getText();
        } catch (\Exception $e) {
            \Log::error("Erro ao extrair PDF: " . $e->getMessage());
            return '';
        }
    }

    /**
     * Faz a requisição para a API do Groq.
     */
    protected function callGroq(string $resumeText, string $jobDescription): ?array
    {
        $prompt = "Você é um especialista em recrutamento e seleção (RH) de alto nível. 
        Sua tarefa é analisar o currículo de um candidato em relação a uma vaga específica.
        
        DESCRIÇÃO DA VAGA:
        {$jobDescription}
        
        CURRÍCULO DO CANDIDATO (TEXTO EXTRAÍDO):
        {$resumeText}
        
        REGRAS DE RETORNO:
        - Retorne APENAS um objeto JSON.
        - 'score': Um número de 0 a 100 indicando a aderência total.
        - 'summary': Um parágrafo profissional resumindo o perfil.
        - 'strengths': Pontos positivos e habilidades que combinam com a vaga.
        - 'weaknesses': Lacunas ou requisitos não atendidos.

        JSON FORMAT:
        {
            \"score\": 85,
            \"summary\": \"...\",
            \"strengths\": \"...\",
            \"weaknesses\": \"...\"
        }";

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
        ])->post('https://api.groq.com/openai/v1/chat/completions', [
            'model' => $this->model,
            'messages' => [
                ['role' => 'user', 'content' => $prompt]
            ],
            'temperature' => 0.0, 
            'response_format' => ['type' => 'json_object']
        ]);

        if ($response->successful()) {
            $data = $response->json();
            $content = $data['choices'][0]['message']['content'] ?? null;
            return json_decode($content, true);
        }

        \Log::error("Erro API Groq: " . $response->body());
        return null;
    }
}


