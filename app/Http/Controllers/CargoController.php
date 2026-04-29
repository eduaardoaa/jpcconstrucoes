<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Cargo;
use App\Models\Permissao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CargoController extends Controller
{
    public function index(Request $request)
    {
        $query = Cargo::with('permissoes');

        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        if ($request->filled('busca')) {
            $busca = trim($request->busca);

            $query->where('nome', 'like', "%{$busca}%");
        }

        $cargos = $query
            ->orderBy('nome')
            ->get();

        $permissoes = Permissao::orderBy('nome')->get();

        return view('cargos.index', compact('cargos', 'permissoes'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'nome' => ['required', 'string', 'max:255', 'unique:cargos,nome'],
                'descricao' => ['nullable', 'string', 'max:255'],
                'tipo' => ['required', Rule::in(['usuario', 'funcionario', 'ambos'])],
                'permissoes' => ['nullable', 'array'],
                'permissoes.*' => ['exists:permissoes,id'],
            ],
            [
                'nome.required' => 'O nome do cargo é obrigatório.',
                'nome.unique' => 'Já existe um cargo com este nome.',
                'nome.max' => 'O nome do cargo deve ter no máximo 255 caracteres.',
                'descricao.max' => 'A descrição deve ter no máximo 255 caracteres.',
                'tipo.required' => 'Selecione o tipo do cargo.',
                'tipo.in' => 'O tipo selecionado é inválido.',
                'permissoes.array' => 'As permissões informadas são inválidas.',
                'permissoes.*.exists' => 'Uma das permissões selecionadas é inválida.',
            ]
        );

        if ($validator->fails()) {
            return redirect()
                ->route('cargos.index')
                ->withErrors($validator)
                ->withInput()
                ->with('open_create_modal', true);
        }

        $cargo = Cargo::create([
            'nome' => $request->nome,
            'descricao' => $request->descricao,
            'tipo' => $request->tipo,
        ]);

        if (in_array($request->tipo, ['usuario', 'ambos'])) {
            $cargo->permissoes()->sync($request->permissoes ?? []);
        } else {
            $cargo->permissoes()->detach();
        }

        return redirect()
            ->route('cargos.index')
            ->with('success', 'Cargo cadastrado com sucesso.');
    }

    public function update(Request $request, Cargo $cargo)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'nome' => ['required', 'string', 'max:255', Rule::unique('cargos', 'nome')->ignore($cargo->id)],
                'descricao' => ['nullable', 'string', 'max:255'],
                'tipo' => ['required', Rule::in(['usuario', 'funcionario', 'ambos'])],
                'permissoes' => ['nullable', 'array'],
                'permissoes.*' => ['exists:permissoes,id'],
            ],
            [
                'nome.required' => 'O nome do cargo é obrigatório.',
                'nome.unique' => 'Já existe um cargo com este nome.',
                'nome.max' => 'O nome do cargo deve ter no máximo 255 caracteres.',
                'descricao.max' => 'A descrição deve ter no máximo 255 caracteres.',
                'tipo.required' => 'Selecione o tipo do cargo.',
                'tipo.in' => 'O tipo selecionado é inválido.',
                'permissoes.array' => 'As permissões informadas são inválidas.',
                'permissoes.*.exists' => 'Uma das permissões selecionadas é inválida.',
            ]
        );

        if ($validator->fails()) {
            return redirect()
                ->route('cargos.index')
                ->withErrors($validator)
                ->withInput()
                ->with('open_edit_modal', $cargo->id);
        }

        $cargo->update([
            'nome' => $request->nome,
            'descricao' => $request->descricao,
            'tipo' => $request->tipo,
        ]);

        if (in_array($request->tipo, ['usuario', 'ambos'])) {
            $cargo->permissoes()->sync($request->permissoes ?? []);
        } else {
            $cargo->permissoes()->detach();
        }

        return redirect()
            ->route('cargos.index')
            ->with('success', 'Cargo atualizado com sucesso.');
    }

    public function destroy(Cargo $cargo)
    {
        if ($cargo->users()->exists()) {
            return redirect()
                ->route('cargos.index')
                ->with('error', 'Não é possível excluir este cargo porque ele está vinculado a usuários.');
        }

        $cargo->permissoes()->detach();
        $cargo->delete();

        return redirect()
            ->route('cargos.index')
            ->with('success', 'Cargo excluído com sucesso.');
    }
}