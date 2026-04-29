<?php

namespace App\Http\Controllers;

use App\Models\Produto;
use App\Services\EstoqueSyncService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ProdutoController extends Controller
{
    public function index(Request $request)
{
    $busca = trim((string) $request->get('busca'));
    $status = $request->get('status');

    $produtos = Produto::with('variacoes')
        ->when($busca !== '', function ($query) use ($busca) {
            $query->where(function ($q) use ($busca) {
                $q->where('nome', 'like', '%' . $busca . '%')
                    ->orWhereHas('variacoes', function ($variacaoQuery) use ($busca) {
                        $variacaoQuery->where('nome_variacao', 'like', '%' . $busca . '%');
                    });
            });
        })
        ->when(in_array($status, ['ativo', 'inativo']), function ($query) use ($status) {
            $query->where('status', $status);
        })
        ->orderBy('nome')
        ->get();

    $unidades = [
        'UN' => 'Unidade (UN)',
        'PAR' => 'Par (PAR)',
        'CX' => 'Caixa (CX)',
        'PCT' => 'Pacote (PCT)',
    ];

    return view('produtos.index', compact('produtos', 'unidades', 'busca', 'status'));
}

    public function store(Request $request)
    {
        $controlaVariacao = $request->boolean('controla_variacao');

        $validator = Validator::make(
            $request->all(),
            [
                'nome' => ['required', 'string', 'max:255', 'unique:produtos,nome'],
                'descricao' => ['nullable', 'string'],
                'unidade' => ['required', 'string', 'max:20'],
                'valor_unitario' => ['required', 'numeric', 'min:0'],
                'dias_entrega_media' => ['nullable', 'integer', 'min:0'],
                'controla_variacao' => ['nullable', 'boolean'],
                'status' => ['required', Rule::in(['ativo', 'inativo'])],

                'ca' => [
                    Rule::requiredIf(!$controlaVariacao),
                    'nullable',
                    'string',
                    'max:100',
                ],

                'variacoes' => ['nullable', 'array'],
                'variacoes.*.nome_variacao' => ['required_with:variacoes', 'string', 'max:255'],
                'variacoes.*.cor' => ['nullable', 'string', 'max:100'],
                'variacoes.*.tamanho' => ['nullable', 'string', 'max:100'],
                'variacoes.*.ca' => ['nullable', 'string', 'max:100'],
                'variacoes.*.status' => ['nullable', Rule::in(['ativo', 'inativo'])],
            ],
            [
                'nome.required' => 'O nome do produto é obrigatório.',
                'nome.unique' => 'Já existe um produto com este nome.',
                'unidade.required' => 'A unidade é obrigatória.',
                'valor_unitario.required' => 'O valor unitário é obrigatório.',
                'valor_unitario.numeric' => 'Informe um valor unitário válido.',
                'dias_entrega_media.integer' => 'Informe um número inteiro para os dias de entrega.',
                'status.required' => 'Selecione um status.',
                'status.in' => 'O status selecionado é inválido.',
                'ca.required' => 'Informe o C.A do produto quando ele não possuir variações.',
                'variacoes.*.nome_variacao.required_with' => 'Informe o nome da variação.',
            ]
        );

        if ($validator->fails()) {
            return redirect()
                ->route('produtos.index')
                ->withErrors($validator)
                ->withInput()
                ->with('open_create_modal', true);
        }

        $produto = Produto::create([
            'nome' => $request->nome,
            'descricao' => $request->descricao,
            'unidade' => $request->unidade,
            'valor_unitario' => $request->valor_unitario,
            'dias_entrega_media' => $request->dias_entrega_media,
            'controla_variacao' => $controlaVariacao,
            'ca' => $controlaVariacao ? null : $request->ca,
            'status' => $request->status,
        ]);

        if ($controlaVariacao && is_array($request->variacoes)) {
            foreach ($request->variacoes as $variacao) {
                if (empty($variacao['nome_variacao'])) {
                    continue;
                }

                $produto->variacoes()->create([
                    'nome_variacao' => $variacao['nome_variacao'],
                    'cor' => $variacao['cor'] ?? null,
                    'tamanho' => $variacao['tamanho'] ?? null,
                    'ca' => $variacao['ca'] ?? null,
                    'status' => $variacao['status'] ?? 'ativo',
                ]);
            }
        }

        $produto->load('variacoes');
        app(EstoqueSyncService::class)->syncForProduto($produto);

        return redirect()
            ->route('produtos.index')
            ->with('success', 'Produto cadastrado com sucesso.');
    }

    public function update(Request $request, Produto $produto)
{
    $controlaVariacao = $request->boolean('controla_variacao');

    $validator = Validator::make(
        $request->all(),
        [
            'nome' => ['required', 'string', 'max:255', Rule::unique('produtos', 'nome')->ignore($produto->id)],
            'descricao' => ['nullable', 'string'],
            'unidade' => ['required', 'string', 'max:20'],
            'valor_unitario' => ['required', 'numeric', 'min:0'],
            'dias_entrega_media' => ['nullable', 'integer', 'min:0'],
            'controla_variacao' => ['nullable', 'boolean'],
            'status' => ['required', Rule::in(['ativo', 'inativo'])],

            'ca' => [
                Rule::requiredIf(!$controlaVariacao),
                'nullable',
                'string',
                'max:100',
            ],

            'variacoes' => ['nullable', 'array'],
            'variacoes.*.id' => ['nullable', 'integer'],
            'variacoes.*.nome_variacao' => ['required_with:variacoes', 'string', 'max:255'],
            'variacoes.*.cor' => ['nullable', 'string', 'max:100'],
            'variacoes.*.tamanho' => ['nullable', 'string', 'max:100'],
            'variacoes.*.ca' => ['nullable', 'string', 'max:100'],
            'variacoes.*.status' => ['nullable', Rule::in(['ativo', 'inativo'])],
        ],
        [
            'nome.required' => 'O nome do produto é obrigatório.',
            'nome.unique' => 'Já existe um produto com este nome.',
            'unidade.required' => 'A unidade é obrigatória.',
            'valor_unitario.required' => 'O valor unitário é obrigatório.',
            'valor_unitario.numeric' => 'Informe um valor unitário válido.',
            'dias_entrega_media.integer' => 'Informe um número inteiro para os dias de entrega.',
            'status.required' => 'Selecione um status.',
            'status.in' => 'O status selecionado é inválido.',
            'ca.required' => 'Informe o C.A do produto quando ele não possuir variações.',
            'variacoes.*.nome_variacao.required_with' => 'Informe o nome da variação.',
        ]
    );

    if ($validator->fails()) {
        return redirect()
            ->route('produtos.index')
            ->withErrors($validator)
            ->withInput()
            ->with('open_edit_modal', $produto->id);
    }

    $produto->update([
        'nome' => $request->nome,
        'descricao' => $request->descricao,
        'unidade' => $request->unidade,
        'valor_unitario' => $request->valor_unitario,
        'dias_entrega_media' => $request->dias_entrega_media,
        'controla_variacao' => $controlaVariacao,
        'ca' => $controlaVariacao ? null : $request->ca,
        'status' => $request->status,
    ]);

    if ($controlaVariacao) {
        $idsRecebidos = [];

        if (is_array($request->variacoes)) {
            foreach ($request->variacoes as $variacaoData) {
                if (empty($variacaoData['nome_variacao'])) {
                    continue;
                }

                // ATUALIZA VARIAÇÃO EXISTENTE
                if (!empty($variacaoData['id'])) {
                    $variacaoExistente = $produto->variacoes()
                        ->where('id', $variacaoData['id'])
                        ->first();

                    if ($variacaoExistente) {
                        $variacaoExistente->update([
                            'nome_variacao' => $variacaoData['nome_variacao'],
                            'cor' => $variacaoData['cor'] ?? null,
                            'tamanho' => $variacaoData['tamanho'] ?? null,
                            'ca' => $variacaoData['ca'] ?? null,
                            'status' => $variacaoData['status'] ?? 'ativo',
                        ]);

                        $idsRecebidos[] = $variacaoExistente->id;
                    }
                } else {
                    // CRIA NOVA VARIAÇÃO
                    $novaVariacao = $produto->variacoes()->create([
                        'nome_variacao' => $variacaoData['nome_variacao'],
                        'cor' => $variacaoData['cor'] ?? null,
                        'tamanho' => $variacaoData['tamanho'] ?? null,
                        'ca' => $variacaoData['ca'] ?? null,
                        'status' => $variacaoData['status'] ?? 'ativo',
                    ]);

                    $idsRecebidos[] = $novaVariacao->id;

                    app(EstoqueSyncService::class)->syncForVariacao($novaVariacao);
                }
            }
        }

        // REMOVE APENAS VARIAÇÕES QUE SAÍRAM DO FORM
        // MAS SÓ SE NÃO TIVEREM ESTOQUE/MOVIMENTAÇÃO
        $variacoesRemover = $produto->variacoes()
            ->when(!empty($idsRecebidos), function ($query) use ($idsRecebidos) {
                $query->whereNotIn('id', $idsRecebidos);
            })
            ->get();

        foreach ($variacoesRemover as $variacaoRemover) {
            $temEstoque = \App\Models\Estoque::where('produto_variacao_id', $variacaoRemover->id)
                ->where('quantidade_atual', '>', 0)
                ->exists();

            $temMovimentacao = \App\Models\MovimentacaoEstoque::where('produto_variacao_id', $variacaoRemover->id)
                ->exists();

            if (!$temEstoque && !$temMovimentacao) {
                $variacaoRemover->delete();
            }
        }
    } else {
        // se não controla variação, não apaga automaticamente as antigas
        // apenas garante o estoque do produto simples
        app(EstoqueSyncService::class)->syncForProduto($produto);
    }

    $produto->load('variacoes');

    return redirect()
        ->route('produtos.index')
        ->with('success', 'Produto atualizado com sucesso.');
}
    public function inativar(Produto $produto)
{
    $produto->update([
        'status' => 'inativo',
    ]);

    return redirect()
        ->route('produtos.index')
        ->with('success', 'Produto inativado com sucesso.');
}
public function ativar(Produto $produto)
{
    $produto->update([
        'status' => 'ativo',
    ]);

    // opcional: reativar variações também
    $produto->variacoes()->update([
        'status' => 'ativo',
    ]);

    return redirect()
        ->route('produtos.index')
        ->with('success', 'Produto ativado com sucesso.');
}

    public function destroy(Produto $produto)
    {
        $produto->delete();

        return redirect()
            ->route('produtos.index')
            ->with('success', 'Produto excluído com sucesso.');
    }
}