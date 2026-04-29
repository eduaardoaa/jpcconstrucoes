<?php

namespace App\Http\Controllers;

use App\Models\Cargo;
use App\Models\Funcionario;
use App\Models\Obra;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class FuncionarioController extends Controller
{
    public function index(Request $request)
    {
        $query = Funcionario::with(['obra', 'cargo']);

        if ($request->filled('obra_id')) {
            $query->where('obra_id', $request->obra_id);
        }

        if ($request->filled('busca')) {
            $busca = trim($request->busca);

            $query->where(function ($q) use ($busca) {
                $q->where('nome', 'like', "%{$busca}%")
                    ->orWhere('matricula', 'like', "%{$busca}%")
                    ->orWhere('cpf', 'like', "%{$busca}%");
            });
        }

        $funcionarios = $query
            ->orderBy('nome')
            ->get();

        $obras = Obra::where('status', 'ativa')
            ->orderBy('nome')
            ->get();

        $cargos = Cargo::whereIn('tipo', ['funcionario', 'ambos'])
            ->orderBy('nome')
            ->get();

        return view('funcionarios.index', compact('funcionarios', 'obras', 'cargos'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'obra_id' => ['required', 'exists:obras,id'],
                'cargo_id' => [
                    'required',
                    Rule::exists('cargos', 'id')->where(function ($query) {
                        $query->whereIn('tipo', ['funcionario', 'ambos']);
                    }),
                ],
                'nome' => ['required', 'string', 'max:255'],
                'cpf' => ['required', 'string', 'size:14', 'unique:funcionarios,cpf'],
                'matricula' => ['required', 'string', 'max:50', 'unique:funcionarios,matricula'],
                'telefone' => ['nullable', 'string', 'max:15', 'unique:funcionarios,telefone'],
                'status' => ['required', Rule::in(['ativo', 'inativo'])],
                'data_admissao' => ['nullable', 'date'],
                'observacoes' => ['nullable', 'string'],
            ],
            [
                'obra_id.required' => 'Selecione a obra.',
                'obra_id.exists' => 'A obra selecionada é inválida.',

                'cargo_id.required' => 'Selecione um cargo.',
                'cargo_id.exists' => 'O cargo selecionado é inválido.',

                'nome.required' => 'O nome é obrigatório.',
                'nome.max' => 'O nome deve ter no máximo 255 caracteres.',

                'cpf.required' => 'O CPF é obrigatório.',
                'cpf.size' => 'O CPF deve estar no formato 000.000.000-00.',
                'cpf.unique' => 'Este CPF já está cadastrado.',

                'matricula.required' => 'A matrícula é obrigatória.',
                'matricula.max' => 'A matrícula deve ter no máximo 50 caracteres.',
                'matricula.unique' => 'Esta matrícula já está cadastrada.',

                'telefone.max' => 'O telefone deve ter no máximo 15 caracteres.',
                'telefone.unique' => 'Este telefone já está cadastrado.',

                'status.required' => 'Selecione um status.',
                'status.in' => 'O status selecionado é inválido.',

                'data_admissao.date' => 'Informe uma data de admissão válida.',
            ]
        );

        if ($validator->fails()) {
            return redirect()
                ->route('funcionarios.index')
                ->withErrors($validator)
                ->withInput()
                ->with('open_create_modal', true);
        }

        Funcionario::create([
            'obra_id' => $request->obra_id,
            'cargo_id' => $request->cargo_id,
            'nome' => $request->nome,
            'cpf' => $this->formatarCpf($request->cpf),
            'matricula' => $request->matricula,
            'telefone' => $this->formatarTelefone($request->telefone),
            'status' => $request->status,
            'data_admissao' => $request->data_admissao,
            'observacoes' => $request->observacoes,
        ]);

        return redirect()
            ->route('funcionarios.index')
            ->with('success', 'Funcionário cadastrado com sucesso.');
    }

    public function update(Request $request, Funcionario $funcionario)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'obra_id' => ['required', 'exists:obras,id'],
                'cargo_id' => [
                    'required',
                    Rule::exists('cargos', 'id')->where(function ($query) {
                        $query->whereIn('tipo', ['funcionario', 'ambos']);
                    }),
                ],
                'nome' => ['required', 'string', 'max:255'],
                'cpf' => ['required', 'string', 'size:14', Rule::unique('funcionarios', 'cpf')->ignore($funcionario->id)],
                'matricula' => ['required', 'string', 'max:50', Rule::unique('funcionarios', 'matricula')->ignore($funcionario->id)],
                'telefone' => ['nullable', 'string', 'max:15', Rule::unique('funcionarios', 'telefone')->ignore($funcionario->id)],
                'status' => ['required', Rule::in(['ativo', 'inativo'])],
                'data_admissao' => ['nullable', 'date'],
                'observacoes' => ['nullable', 'string'],
            ],
            [
                'obra_id.required' => 'Selecione a obra.',
                'obra_id.exists' => 'A obra selecionada é inválida.',

                'cargo_id.required' => 'Selecione um cargo.',
                'cargo_id.exists' => 'O cargo selecionado é inválido.',

                'nome.required' => 'O nome é obrigatório.',
                'nome.max' => 'O nome deve ter no máximo 255 caracteres.',

                'cpf.required' => 'O CPF é obrigatório.',
                'cpf.size' => 'O CPF deve estar no formato 000.000.000-00.',
                'cpf.unique' => 'Este CPF já está cadastrado.',

                'matricula.required' => 'A matrícula é obrigatória.',
                'matricula.max' => 'A matrícula deve ter no máximo 50 caracteres.',
                'matricula.unique' => 'Esta matrícula já está cadastrada.',

                'telefone.max' => 'O telefone deve ter no máximo 15 caracteres.',
                'telefone.unique' => 'Este telefone já está cadastrado.',

                'status.required' => 'Selecione um status.',
                'status.in' => 'O status selecionado é inválido.',

                'data_admissao.date' => 'Informe uma data de admissão válida.',
            ]
        );

        if ($validator->fails()) {
            return redirect()
                ->route('funcionarios.index')
                ->withErrors($validator)
                ->withInput()
                ->with('open_edit_modal', $funcionario->id);
        }

        $funcionario->update([
            'obra_id' => $request->obra_id,
            'cargo_id' => $request->cargo_id,
            'nome' => $request->nome,
            'cpf' => $this->formatarCpf($request->cpf),
            'matricula' => $request->matricula,
            'telefone' => $this->formatarTelefone($request->telefone),
            'status' => $request->status,
            'data_admissao' => $request->data_admissao,
            'observacoes' => $request->observacoes,
        ]);

        return redirect()
            ->route('funcionarios.index')
            ->with('success', 'Funcionário atualizado com sucesso.');
    }

    public function toggleStatus(Funcionario $funcionario)
    {
        $novoStatus = $funcionario->status === 'ativo' ? 'inativo' : 'ativo';

        $funcionario->update([
            'status' => $novoStatus,
        ]);

        return redirect()
            ->route('funcionarios.index')
            ->with('success', 'Status do funcionário atualizado com sucesso.');
    }

    public function destroy(Funcionario $funcionario)
    {
        $funcionario->delete();

        return redirect()
            ->route('funcionarios.index')
            ->with('success', 'Funcionário excluído com sucesso.');
    }

    private function formatarCpf(?string $cpf): ?string
    {
        if (!$cpf) {
            return null;
        }

        $cpf = preg_replace('/\D/', '', $cpf);

        if (strlen($cpf) !== 11) {
            return $cpf;
        }

        return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $cpf);
    }

    private function formatarTelefone(?string $telefone): ?string
    {
        if (!$telefone) {
            return null;
        }

        $telefone = preg_replace('/\D/', '', $telefone);

        if (strlen($telefone) === 11) {
            return preg_replace('/(\d{2})(\d{5})(\d{4})/', '($1) $2-$3', $telefone);
        }

        if (strlen($telefone) === 10) {
            return preg_replace('/(\d{2})(\d{4})(\d{4})/', '($1) $2-$3', $telefone);
        }

        return $telefone;
    }
}