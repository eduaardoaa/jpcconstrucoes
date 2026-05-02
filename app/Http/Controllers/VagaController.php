<?php

namespace App\Http\Controllers;

use App\Models\Vaga;
use App\Models\VagaCandidatura;
use App\Models\VagaPergunta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class VagaController extends Controller
{
    // ─────────────────────────────────────────────────────────
    // ADMIN: lista de vagas
    // ─────────────────────────────────────────────────────────
    public function index()
    {
        $vagas = Vaga::withCount('candidaturas')
            ->orderByDesc('created_at')
            ->get();

        // ── Analytics ──
        $totalCandidatos = VagaCandidatura::count();
        $candidatosHoje  = VagaCandidatura::whereDate('created_at', today())->count();
        $vagasAbertas    = Vaga::where('status', 'aberta')->count();

        $totalAprovados = VagaCandidatura::where('status', 'aprovada')->count();
        $taxaAprovacao  = $totalCandidatos > 0 ? round(($totalAprovados / $totalCandidatos) * 100) : 0;

        // Candidaturas por dia (últimos 14 dias)
        $dias = collect();
        for ($i = 13; $i >= 0; $i--) {
            $data = now()->subDays($i)->format('Y-m-d');
            $dias->push([
                'label' => now()->subDays($i)->format('d/m'),
                'total' => VagaCandidatura::whereDate('created_at', $data)->count(),
            ]);
        }
        $chartDias = $dias;

        // Distribuição por status
        $statusDist = VagaCandidatura::selectRaw("status, count(*) as total")
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        // Top 5 vagas com mais candidatos
        $topVagas = Vaga::withCount('candidaturas')
            ->orderByDesc('candidaturas_count')
            ->take(5)
            ->get()
            ->map(fn($v) => ['titulo' => Str::limit($v->titulo, 25), 'total' => $v->candidaturas_count]);

        return view('vagas.index', compact(
            'vagas', 'totalCandidatos', 'candidatosHoje',
            'vagasAbertas', 'taxaAprovacao', 'chartDias',
            'statusDist', 'topVagas'
        ));
    }

    // ─────────────────────────────────────────────────────────
    // ADMIN: criar vaga
    // ─────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'titulo'        => 'required|string|max:255',
            'descricao'     => 'nullable|string',
            'local'         => 'nullable|string|max:255',
            'tipo_contrato' => 'nullable|string|max:100',
            'salario'       => 'nullable|string|max:100',
            'requisitos'    => 'nullable|string',
            'diferenciais'  => 'nullable|string',
            'beneficios'    => 'nullable|string',
            'data_limite'   => 'nullable|date',

            'perguntas'              => 'nullable|array',
            'perguntas.*.texto'      => 'required_with:perguntas|string|max:500',
            'perguntas.*.tipo'       => 'required_with:perguntas|in:texto,textarea,select',
            'perguntas.*.opcoes'     => 'nullable|string',
            'perguntas.*.obrigatoria'=> 'nullable|boolean',
        ]);

        $vaga = Vaga::create([
            'titulo'        => $request->titulo,
            'descricao'     => $request->descricao,
            'local'         => $request->local,
            'tipo_contrato' => $request->tipo_contrato,
            'salario'       => $request->salario,
            'requisitos'    => $request->requisitos,
            'diferenciais'  => $request->diferenciais,
            'beneficios'    => $request->beneficios,
            'data_limite'   => $request->data_limite,
            'user_id'       => auth()->id(),
        ]);

        // Salva perguntas
        if ($request->perguntas) {
            foreach ($request->perguntas as $i => $p) {
                if (empty($p['texto'])) continue;

                $opcoes = null;
                if ($p['tipo'] === 'select' && !empty($p['opcoes'])) {
                    $opcoes = array_map('trim', explode(',', $p['opcoes']));
                }

                VagaPergunta::create([
                    'vaga_id'     => $vaga->id,
                    'pergunta'    => $p['texto'],
                    'tipo'        => $p['tipo'],
                    'opcoes'      => $opcoes,
                    'obrigatoria' => $p['obrigatoria'] ?? true,
                    'ordem'       => $i,
                ]);
            }
        }

        \Log::info("Vaga criada: ID " . $vaga->id . " - Título: " . $vaga->titulo);

        return redirect()->route('vagas.index')->with('success', 'Vaga criada com sucesso!');
    }


    // ─────────────────────────────────────────────────────────
    // ADMIN: editar vaga
    // ─────────────────────────────────────────────────────────
    public function update(Request $request, Vaga $vaga)
    {
        $request->validate([
            'titulo'        => 'required|string|max:255',
            'status'        => 'required|in:aberta,fechada',
            'requisitos'    => 'nullable|string',
            'diferenciais'  => 'nullable|string',
        ]);

        $vaga->update([
            'titulo'        => $request->titulo,
            'descricao'     => $request->descricao,
            'local'         => $request->local,
            'tipo_contrato' => $request->tipo_contrato,
            'salario'       => $request->salario,
            'requisitos'    => $request->requisitos,
            'diferenciais'  => $request->diferenciais,
            'beneficios'    => $request->beneficios,
            'data_limite'   => $request->data_limite,
            'status'        => $request->status,
        ]);

        // Sincroniza perguntas
        $vaga->perguntas()->delete();
        if ($request->perguntas) {
            foreach ($request->perguntas as $i => $p) {
                if (empty($p['texto'])) continue;

                $opcoes = null;
                if ($p['tipo'] === 'select' && !empty($p['opcoes'])) {
                    $opcoes = array_map('trim', explode(',', $p['opcoes']));
                }

                VagaPergunta::create([
                    'vaga_id'     => $vaga->id,
                    'pergunta'    => $p['texto'],
                    'tipo'        => $p['tipo'],
                    'opcoes'      => $opcoes,
                    'obrigatoria' => $p['obrigatoria'] ?? true,
                    'ordem'       => $i,
                ]);
            }
        }

        return redirect()->route('vagas.index')->with('success', 'Vaga atualizada com sucesso!');
    }

    /**
     * Analisa uma imagem de vaga e extrai os dados via IA Vision.
     */
    public function analisarImagem(Request $request)
    {
        \Log::info("Iniciando análise de imagem IA (Gemini Flash Latest)...");

        $request->validate([
            'imagem' => 'required|image|max:5120',
        ]);

        try {
            $image = $request->file('imagem');
            $base64 = base64_encode(file_get_contents($image->getPathname()));
            $mime = $image->getMimeType();

            $apiKey = env('GEMINI_API_KEY');
            
            $prompt = "Analise esta imagem de anúncio de vaga de emprego e extraia as informações em formato JSON.
            Retorne APENAS o JSON no formato:
            {
                \"titulo\": \"Nome da vaga\",
                \"descricao\": \"Resumo da descrição\",
                \"requisitos\": \"Lista de requisitos\",
                \"diferenciais\": \"Lista de diferenciais\",
                \"beneficios\": \"Lista de benefícios\",
                \"local\": \"Cidade/Estado se houver\",
                \"salario\": \"Valor se houver\"
            }";

            // Usando v1beta e gemini-flash-latest que são os mais estáveis para visão em 2026
            $response = Http::withoutVerifying()->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent?key={$apiKey}", [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt],
                            [
                                'inline_data' => [
                                    'mime_type' => $mime,
                                    'data' => $base64
                                ]
                            ]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'response_mime_type' => 'application/json'
                ]
            ]);

            if ($response->successful()) {
                $content = $response->json()['candidates'][0]['content']['parts'][0]['text'];
                return response()->json(json_decode($content, true));
            }

            \Log::error("Gemini Flash Error: " . $response->body());
            return response()->json(['error' => 'A IA não conseguiu processar esta imagem. Tente uma imagem mais nítida.'], 500);

        } catch (\Exception $e) {
            \Log::error("Exception na análise: " . $e->getMessage());
            return response()->json(['error' => 'Erro interno ao processar imagem.'], 500);
        }
    }


    // ─────────────────────────────────────────────────────────
    // ADMIN: excluir vaga
    // ─────────────────────────────────────────────────────────
    public function destroy(Vaga $vaga)
    {
        // Remove currículos dos candidatos
        foreach ($vaga->candidaturas as $c) {
            if ($c->curriculo_path && Storage::disk('public')->exists($c->curriculo_path)) {
                Storage::disk('public')->delete($c->curriculo_path);
            }
        }

        $vaga->delete();

        return redirect()->route('vagas.index')
            ->with('success', 'Vaga excluída com sucesso!');
    }

    // ─────────────────────────────────────────────────────────
    // ADMIN: ver candidatos de uma vaga
    // ─────────────────────────────────────────────────────────
    public function candidatos(Vaga $vaga)
    {
        $vaga->load(['candidaturas' => function ($query) {
            $query->orderByRaw('ai_score DESC, created_at DESC');
        }, 'perguntas']);

        return view('vagas.candidatos', compact('vaga'));
    }

    // ─────────────────────────────────────────────────────────
    // ADMIN: alterar status do candidato
    // ─────────────────────────────────────────────────────────
    public function alterarStatusCandidato(Request $request, VagaCandidatura $candidatura)
    {
        $request->validate([
            'status'      => 'required|in:nova,analisando,aprovada,reprovada',
            'observacoes' => 'nullable|string|max:1000',
        ]);

        $candidatura->update([
            'status'      => $request->status,
            'observacoes' => $request->observacoes,
        ]);

        return redirect()->route('vagas.candidatos', $candidatura->vaga_id)
            ->with('success', 'Status do candidato atualizado!');
    }

    // ─────────────────────────────────────────────────────────
    // ADMIN: excluir candidatura
    // ─────────────────────────────────────────────────────────
    public function excluirCandidatura(VagaCandidatura $candidatura)
    {
        $vagaId = $candidatura->vaga_id;

        if ($candidatura->curriculo_path && Storage::disk('public')->exists($candidatura->curriculo_path)) {
            Storage::disk('public')->delete($candidatura->curriculo_path);
        }

        $candidatura->delete();

        return redirect()->route('vagas.candidatos', $vagaId)
            ->with('success', 'Candidatura excluída com sucesso!');
    }

    // ─────────────────────────────────────────────────────────
    // ADMIN: download do currículo
    // ─────────────────────────────────────────────────────────
    public function downloadCurriculo(VagaCandidatura $candidatura)
    {
        if (!$candidatura->curriculo_path || !Storage::disk('public')->exists($candidatura->curriculo_path)) {
            abort(404, 'Currículo não encontrado.');
        }

        $path = Storage::disk('public')->path($candidatura->curriculo_path);
        $mime = Storage::disk('public')->mimeType($candidatura->curriculo_path);

        return response()->file($path, [
            'Content-Type' => $mime,
            'Content-Disposition' => 'inline; filename="' . $candidatura->curriculo_nome_original . '"',
        ]);
    }

    // ─────────────────────────────────────────────────────────
    // ADMIN: toggle status da vaga (aberta/fechada)
    // ─────────────────────────────────────────────────────────
    public function toggleStatus(Vaga $vaga)
    {
        $vaga->update([
            'status' => $vaga->status === 'aberta' ? 'fechada' : 'aberta',
        ]);

        return redirect()->route('vagas.index')
            ->with('success', 'Status da vaga alterado!');
    }

    // ═════════════════════════════════════════════════════════
    // PÚBLICO: formulário de candidatura (SEM autenticação)
    // ═════════════════════════════════════════════════════════
    public function formAplicar(string $slug)
    {
        $vaga = Vaga::where('slug', $slug)
            ->with('perguntas')
            ->firstOrFail();

        if (!$vaga->isAberta()) {
            return view('vagas.fechada', compact('vaga'));
        }

        return view('vagas.aplicar', compact('vaga'));
    }

    // ─────────────────────────────────────────────────────────
    // PÚBLICO: processar candidatura
    // ─────────────────────────────────────────────────────────
    public function aplicar(Request $request, string $slug)
    {
        $vaga = Vaga::where('slug', $slug)
            ->with('perguntas')
            ->firstOrFail();

        if (!$vaga->isAberta()) {
            return redirect()->route('vagas.aplicar', $slug)
                ->with('error', 'Esta vaga não está mais aceitando candidaturas.');
        }

        $rules = [
            'nome'      => 'required|string|max:255',
            'email'     => 'nullable|email|max:255',
            'telefone'  => 'required|string|max:20',
            'curriculo' => 'required|file|mimes:pdf,doc,docx|max:10240', // 10MB
        ];

        // Regras das perguntas
        foreach ($vaga->perguntas as $p) {
            $key = "resposta_{$p->id}";
            $rules[$key] = $p->obrigatoria ? 'required|string|max:2000' : 'nullable|string|max:2000';
        }

        $request->validate($rules, [
            'nome.required'      => 'Informe seu nome completo.',
            'telefone.required'  => 'Informe seu telefone com DDD.',
            'curriculo.required' => 'Anexe seu currículo (PDF ou Word).',
            'curriculo.mimes'    => 'O currículo deve ser PDF, DOC ou DOCX.',
            'curriculo.max'      => 'O currículo não pode passar de 10 MB.',
        ]);

        // Upload do currículo
        $arquivo     = $request->file('curriculo');
        $nomeOriginal = $arquivo->getClientOriginalName();
        $caminho      = $arquivo->store('vagas/curriculos', 'public');

        // Monta respostas
        $respostas = [];
        foreach ($vaga->perguntas as $p) {
            $respostas[$p->id] = $request->input("resposta_{$p->id}");
        }

        $candidatura = VagaCandidatura::create([
            'vaga_id'                 => $vaga->id,
            'nome'                    => $request->nome,
            'email'                   => $request->email,
            'telefone'                => $request->telefone,
            'curriculo_path'          => $caminho,
            'curriculo_nome_original' => $nomeOriginal,
            'respostas'               => $respostas,
        ]);

        // Dispara a análise por IA em segundo plano
        \App\Jobs\ScanResumeJob::dispatch($candidatura);

        return redirect()->route('vagas.aplicar', $slug)
            ->with('success', 'Candidatura enviada com sucesso! Entraremos em contato em breve.');
    }

    /**
     * Re-dispara a análise por IA de um currículo.

     */
    public function reanalisarCandidatura(\App\Models\VagaCandidatura $candidatura)
    {
        // Força o status para pending antes de disparar
        $candidatura->update(['ai_status' => 'pending']);
        
        \App\Jobs\ScanResumeJob::dispatch($candidatura);

        return back()->with('success', 'Análise reiniciada com sucesso!');
    }
}
