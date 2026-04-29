<?php

namespace App\Http\Controllers;

use App\Models\Obra;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Services\EstoqueSyncService;

class ObraController extends Controller
{
    public function index(Request $request)
    {
        $busca = trim((string) $request->get('busca'));
        $status = $request->get('status');

        $obras = Obra::with('ultimoTreinamentoDds')
            ->when($busca !== '', function ($query) use ($busca) {
                $query->where(function ($q) use ($busca) {
                    $q->where('nome', 'like', '%' . $busca . '%')
                      ->orWhere('endereco', 'like', '%' . $busca . '%');
                });
            })
            ->when(in_array($status, ['ativa', 'inativa']), function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->orderBy('nome')
            ->get();

        $tecnicos = User::whereHas('cargo', function ($query) {
                $query->where('nome', 'Técnico de Segurança');
            })
            ->where('status', 'ativo')
            ->orderBy('name')
            ->get();

        return view('obras.index', compact('obras', 'tecnicos', 'busca', 'status'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'nome' => ['required', 'string', 'max:255', 'unique:obras,nome'],
                'endereco' => ['nullable', 'string', 'max:255'],
                'responsavel' => ['nullable', 'exists:users,id'],
                'data_inicio' => ['nullable', 'date'],
                'status' => ['required', Rule::in(['ativa', 'inativa'])],
                'observacoes' => ['nullable', 'string'],
            ],
            [
                'nome.required' => 'O nome da obra é obrigatório.',
                'nome.unique' => 'Já existe uma obra com este nome.',
                'nome.max' => 'O nome da obra deve ter no máximo 255 caracteres.',
                'endereco.max' => 'O endereço deve ter no máximo 255 caracteres.',
                'responsavel.exists' => 'O técnico de segurança selecionado é inválido.',
                'data_inicio.date' => 'Informe uma data de início válida.',
                'status.required' => 'Selecione um status.',
                'status.in' => 'O status selecionado é inválido.',
            ]
        );

        if ($validator->fails()) {
            return redirect()
                ->route('obras.index')
                ->withErrors($validator)
                ->withInput()
                ->with('open_create_modal', true);
        }

        $obra = Obra::create([
            'nome' => $request->nome,
            'endereco' => $request->endereco,
            'responsavel' => $request->responsavel,
            'data_inicio' => $request->data_inicio,
            'status' => $request->status,
            'observacoes' => $request->observacoes,
        ]);

        app(EstoqueSyncService::class)->syncForObra($obra);

        return redirect()
            ->route('obras.index')
            ->with('success', 'Obra cadastrada com sucesso.');
    }

    public function update(Request $request, Obra $obra)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'nome' => ['required', 'string', 'max:255', Rule::unique('obras', 'nome')->ignore($obra->id)],
                'endereco' => ['nullable', 'string', 'max:255'],
                'responsavel' => ['nullable', 'exists:users,id'],
                'data_inicio' => ['nullable', 'date'],
                'status' => ['required', Rule::in(['ativa', 'inativa'])],
                'observacoes' => ['nullable', 'string'],
            ],
            [
                'nome.required' => 'O nome da obra é obrigatório.',
                'nome.unique' => 'Já existe uma obra com este nome.',
                'nome.max' => 'O nome da obra deve ter no máximo 255 caracteres.',
                'endereco.max' => 'O endereço deve ter no máximo 255 caracteres.',
                'responsavel.exists' => 'O técnico de segurança selecionado é inválido.',
                'data_inicio.date' => 'Informe uma data de início válida.',
                'status.required' => 'Selecione um status.',
                'status.in' => 'O status selecionado é inválido.',
            ]
        );

        if ($validator->fails()) {
            return redirect()
                ->route('obras.index')
                ->withErrors($validator)
                ->withInput()
                ->with('open_edit_modal', $obra->id);
        }

        $obra->update([
            'nome' => $request->nome,
            'endereco' => $request->endereco,
            'responsavel' => $request->responsavel,
            'data_inicio' => $request->data_inicio,
            'status' => $request->status,
            'observacoes' => $request->observacoes,
        ]);

        return redirect()
            ->route('obras.index')
            ->with('success', 'Obra atualizada com sucesso.');
    }

    public function toggleStatus(Obra $obra)
    {
        $novoStatus = $obra->status === 'ativa' ? 'inativa' : 'ativa';

        $obra->update([
            'status' => $novoStatus,
        ]);

        return redirect()
            ->route('obras.index')
            ->with('success', 'Status da obra atualizado com sucesso.');
    }

    public function destroy(Obra $obra)
    {
        $obra->delete();

        return redirect()
            ->route('obras.index')
            ->with('success', 'Obra excluída com sucesso.');
    }
}