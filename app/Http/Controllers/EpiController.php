<?php

namespace App\Http\Controllers;

use App\Models\Cargo;
use App\Models\Funcionario;
use App\Models\Obra;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class EpiController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->get('search', ''));
        $cargoId = $request->get('cargo_id');
        $obraId = $request->get('obra_id');
        $statusComprovante = $request->get('status_comprovante');

        $funcionarios = Funcionario::with([
            'obra',
            'cargo',
            'entregasEpi' => function ($query) {
                $query->with([
                    'itens.produto',
                    'itens.variacao',
                    'comprovantes',
                ])->orderByDesc('data_entrega')->orderByDesc('id');
            },
        ])
            ->when($search, function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('nome', 'like', '%' . $search . '%')
                        ->orWhere('matricula', 'like', '%' . $search . '%')
                        ->orWhere('cpf', 'like', '%' . $search . '%');
                });
            })
            ->when($cargoId, function ($query) use ($cargoId) {
                $query->where('cargo_id', $cargoId);
            })
            ->when($obraId, function ($query) use ($obraId) {
                $query->where('obra_id', $obraId);
            })
            ->orderBy('nome')
            ->get()
            ->map(function ($funcionario) {
                $ultimaEntrega = $funcionario->entregasEpi->first();

                $funcionario->ultima_entrega = $ultimaEntrega;
                $funcionario->ultimo_comprovante = $ultimaEntrega?->comprovantes?->last();

                return $funcionario;
            })
            ->filter(function ($funcionario) use ($statusComprovante) {
                if (!$statusComprovante) {
                    return true;
                }

                $statusAtual = $funcionario->ultima_entrega?->status_comprovante;

                return $statusAtual === $statusComprovante;
            })
            ->values();

        $cargos = Cargo::whereIn('tipo', ['funcionario', 'ambos'])
            ->orderBy('nome')
            ->get();

        $obras = Obra::orderBy('nome')->get();

        return view('epi.index', compact(
            'funcionarios',
            'cargos',
            'obras',
            'search',
            'cargoId',
            'obraId',
            'statusComprovante'
        ));
    }

    public function historico(Funcionario $funcionario)
    {
        $funcionario->load([
            'obra',
            'cargo',
            'entregasEpi' => function ($query) {
                $query->with([
                    'usuario',
                    'itens.produto',
                    'itens.variacao',
                    'comprovantes',
                ])->orderByDesc('data_entrega')->orderByDesc('id');
            },
        ]);

        $ultimaEntrega = $funcionario->entregasEpi->first();

        return view('epi.historico', compact(
            'funcionario',
            'ultimaEntrega'
        ));
    }

    public function pdfUltima(Funcionario $funcionario)
{
    $funcionario->load([
        'obra',
        'cargo',
        'entregasEpi' => function ($query) {
            $query->with([
                'itens.produto',
                'itens.variacao',
            ])->orderByDesc('data_entrega')->orderByDesc('id');
        },
    ]);

    $ultimaEntrega = $funcionario->entregasEpi->first();

    if (!$ultimaEntrega) {
        return redirect()
            ->route('epi.historico', $funcionario->id)
            ->with('error', 'Este funcionário ainda não possui entregas registradas.');
    }

    $pdf = Pdf::loadView('epi.pdf.ficha', [
        'funcionario' => $funcionario,
        'entregas' => collect([$ultimaEntrega]),
        'titulo' => 'Ficha de EPI - Última Entrega',
    ])->setPaper('a4', 'portrait');

    $nomeArquivo = 'ficha-epi-ultima-' . $this->normalizarNomeArquivo($funcionario->nome) . '.pdf';

    return $pdf->download($nomeArquivo);
}

public function pdfCompleto(Funcionario $funcionario)
{
    $funcionario->load([
        'obra',
        'cargo',
        'entregasEpi' => function ($query) {
            $query->with([
                'itens.produto',
                'itens.variacao',
            ])->orderByDesc('data_entrega')->orderByDesc('id');
        },
    ]);

    if ($funcionario->entregasEpi->isEmpty()) {
        return redirect()
            ->route('epi.historico', $funcionario->id)
            ->with('error', 'Este funcionário ainda não possui entregas registradas.');
    }

    $pdf = Pdf::loadView('epi.pdf.ficha', [
        'funcionario' => $funcionario,
        'entregas' => $funcionario->entregasEpi,
        'titulo' => 'Ficha de EPI - Histórico Completo',
    ])->setPaper('a4', 'portrait');

    $nomeArquivo = 'ficha-epi-completa-' . $this->normalizarNomeArquivo($funcionario->nome) . '.pdf';

    return $pdf->download($nomeArquivo);
}

private function normalizarNomeArquivo(string $nome): string
{
    $nome = mb_strtolower($nome, 'UTF-8');
    $nome = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $nome);
    $nome = preg_replace('/[^a-z0-9]+/', '-', $nome);
    $nome = trim($nome, '-');

    return $nome ?: 'funcionario';
}
}