<?php

namespace App\Http\Controllers;

use App\Models\SolicitacaoAbastecimento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class SolicitacaoAbastecimentoController extends Controller
{
    public function index()
    {
        $user = auth()->user()->load('veiculo');

        $solicitacoes = SolicitacaoAbastecimento::with(['veiculo', 'aprovador'])
            ->where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->get();

        return view('abastecimento.solicitacoes.index', compact('user', 'solicitacoes'));
    }

    public function store(Request $request)
    {
        $user = auth()->user()->load('veiculo');

        if (!$user->podeSolicitarAbastecimento()) {
            return redirect()
                ->route('abastecimento.solicitacoes.index')
                ->with('error', 'Você precisa ter um veículo ativo vinculado para enviar uma solicitação.');
        }

        $validator = Validator::make(
            $request->all(),
            [
                'data_solicitacao' => ['required', 'date'],
                'km_informado' => ['required', 'numeric', 'min:0'],

                'foto_painel_base64' => ['required', 'string'],
                'foto_painel_nome' => ['nullable', 'string', 'max:255'],
                'foto_painel_mime' => ['nullable', Rule::in(['image/jpeg', 'image/png', 'image/webp'])],

                'tipo_solicitacao' => ['required', Rule::in(['valor', 'litros'])],
                'quantidade_solicitada' => ['required', 'numeric', 'min:0.01'],
                'observacao_usuario' => ['nullable', 'string'],
            ],
            [
                'data_solicitacao.required' => 'A data da solicitação é obrigatória.',
                'data_solicitacao.date' => 'Informe uma data válida.',

                'km_informado.required' => 'O KM do veículo é obrigatório.',
                'km_informado.numeric' => 'O KM informado deve ser numérico.',
                'km_informado.min' => 'O KM informado não pode ser negativo.',

                'foto_painel_base64.required' => 'A foto do painel com o KM é obrigatória.',
                'foto_painel_base64.string' => 'A foto enviada é inválida.',
                'foto_painel_nome.max' => 'O nome da foto é muito grande.',
                'foto_painel_mime.in' => 'O tipo da imagem deve ser JPEG, PNG ou WEBP.',

                'tipo_solicitacao.required' => 'Selecione se a solicitação é por valor ou litros.',
                'tipo_solicitacao.in' => 'O tipo de solicitação é inválido.',

                'quantidade_solicitada.required' => 'Informe a quantidade solicitada.',
                'quantidade_solicitada.numeric' => 'A quantidade solicitada deve ser numérica.',
                'quantidade_solicitada.min' => 'A quantidade solicitada deve ser maior que zero.',
            ]
        );

        if ($validator->fails()) {
            return redirect()
                ->route('abastecimento.solicitacoes.index')
                ->withErrors($validator)
                ->withInput()
                ->with('open_create_modal', true);
        }

        $fotoPainelPath = $this->salvarFotoBase64(
            $request->foto_painel_base64,
            'abastecimento/painel',
            'painel_'
        );

        if (!$fotoPainelPath) {
            return redirect()
                ->route('abastecimento.solicitacoes.index')
                ->withErrors(['foto_painel_base64' => 'Não foi possível processar a foto do painel. Tire a foto novamente.'])
                ->withInput()
                ->with('open_create_modal', true);
        }

        SolicitacaoAbastecimento::create([
            'user_id' => $user->id,
            'veiculo_id' => $user->veiculo_id,
            'data_solicitacao' => $request->data_solicitacao,
            'km_informado' => $request->km_informado,
            'foto_painel' => $fotoPainelPath,
            'tipo_solicitacao' => $request->tipo_solicitacao,
            'quantidade_solicitada' => $request->quantidade_solicitada,
            'status' => 'pendente',
            'status_comprovante' => null,
            'observacao_usuario' => $request->observacao_usuario,
        ]);

        return redirect()
            ->route('abastecimento.solicitacoes.index')
            ->with('success', 'Solicitação de abastecimento enviada com sucesso.');
    }

    public function enviarComprovante(Request $request, SolicitacaoAbastecimento $solicitacao)
    {
        $user = auth()->user();

        if ((int) $solicitacao->user_id !== (int) $user->id) {
            abort(403, 'Você não tem permissão para enviar comprovante para esta solicitação.');
        }

        if (!in_array($solicitacao->status, ['aprovada', 'ajustada'])) {
            return redirect()
                ->route('abastecimento.solicitacoes.index')
                ->with('error', 'Só é possível enviar comprovante para solicitações aprovadas ou ajustadas.');
        }

        if ($solicitacao->foto_nota || $solicitacao->foto_selfie) {
            return redirect()
                ->route('abastecimento.solicitacoes.index')
                ->with('error', 'O comprovante desta solicitação já foi enviado.');
        }

        $validator = Validator::make(
            $request->all(),
            [
                'foto_nota_base64' => ['required', 'string'],
                'foto_nota_nome' => ['nullable', 'string', 'max:255'],
                'foto_nota_mime' => ['nullable', Rule::in(['image/jpeg', 'image/png', 'image/webp'])],

                'foto_selfie_base64' => ['required', 'string'],
                'foto_selfie_nome' => ['nullable', 'string', 'max:255'],
                'foto_selfie_mime' => ['nullable', Rule::in(['image/jpeg', 'image/png', 'image/webp'])],
            ],
            [
                'foto_nota_base64.required' => 'A foto da nota é obrigatória.',
                'foto_nota_base64.string' => 'A foto da nota é inválida.',
                'foto_nota_nome.max' => 'O nome da foto da nota é muito grande.',
                'foto_nota_mime.in' => 'A foto da nota deve ser JPEG, PNG ou WEBP.',

                'foto_selfie_base64.required' => 'A selfie do usuário é obrigatória.',
                'foto_selfie_base64.string' => 'A selfie enviada é inválida.',
                'foto_selfie_nome.max' => 'O nome da selfie é muito grande.',
                'foto_selfie_mime.in' => 'A selfie deve ser JPEG, PNG ou WEBP.',
            ]
        );

        if ($validator->fails()) {
            return redirect()
                ->route('abastecimento.solicitacoes.index')
                ->withErrors($validator)
                ->withInput()
                ->with('open_comprovante_modal', $solicitacao->id);
        }

        $fotoNotaPath = $this->salvarFotoBase64(
            $request->foto_nota_base64,
            'abastecimento/comprovantes/notas',
            'nota_'
        );

        $fotoSelfiePath = $this->salvarFotoBase64(
            $request->foto_selfie_base64,
            'abastecimento/comprovantes/selfies',
            'selfie_'
        );

        if (!$fotoNotaPath || !$fotoSelfiePath) {
            return redirect()
                ->route('abastecimento.solicitacoes.index')
                ->withErrors([
                    'foto_nota_base64' => 'Não foi possível processar as fotos do comprovante. Tire as fotos novamente.'
                ])
                ->withInput()
                ->with('open_comprovante_modal', $solicitacao->id);
        }

        $solicitacao->update([
            'foto_nota' => $fotoNotaPath,
            'foto_selfie' => $fotoSelfiePath,
            'comprovante_enviado_em' => now(),
            'status_comprovante' => 'enviado',
        ]);

        return redirect()
            ->route('abastecimento.solicitacoes.index')
            ->with('success', 'Comprovante enviado com sucesso.');
    }

    private function salvarFotoBase64(string $base64, string $pasta, string $prefixo = 'img_'): ?string
    {
        if (!preg_match('/^data:image\/(\w+);base64,/', $base64, $matches)) {
            return null;
        }

        $extensao = strtolower($matches[1]);

        if (!in_array($extensao, ['jpg', 'jpeg', 'png', 'webp'])) {
            return null;
        }

        $conteudo = substr($base64, strpos($base64, ',') + 1);
        $conteudo = base64_decode($conteudo);

        if ($conteudo === false) {
            return null;
        }

        $nomeArquivo = $pasta . '/' . uniqid($prefixo, true) . '.' . $extensao;

        Storage::disk('public')->put($nomeArquivo, $conteudo);

        return $nomeArquivo;
    }
}