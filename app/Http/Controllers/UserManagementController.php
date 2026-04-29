<?php

namespace App\Http\Controllers;

use App\Models\Cargo;
use App\Models\User;
use App\Models\Veiculo;
use App\Models\WhatsappInstancia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserManagementController extends Controller
{
    public function index(Request $request)
    {
        $busca = trim((string) $request->get('busca'));
        $cargoId = $request->get('cargo_id');
        $status = $request->get('status');

        $query = User::with(['cargo', 'veiculo', 'whatsappInstancias'])
            ->orderBy('name');

        if ($busca !== '') {
            $query->where(function ($q) use ($busca) {
                $q->where('name', 'like', "%{$busca}%")
                    ->orWhere('cpf', 'like', "%{$busca}%")
                    ->orWhere('email', 'like', "%{$busca}%")
                    ->orWhere('telefone', 'like', "%{$busca}%");
            });
        }

        if ($cargoId) {
            $query->where('cargo_id', $cargoId);
        }

        if (in_array($status, ['ativo', 'inativo'])) {
            $query->where('status', $status);
        }

        $usuarios = $query->get();

        $cargos = Cargo::where('tipo', 'usuario')
            ->orderBy('nome')
            ->get();

        $veiculos = Veiculo::where('status', 'ativo')
            ->whereDoesntHave('usuario')
            ->orderBy('placa')
            ->get();

        $whatsappInstancias = WhatsappInstancia::where('status', 'ativa')
            ->orderBy('nome')
            ->get();

        return view('usuarios.index', compact(
            'usuarios',
            'cargos',
            'veiculos',
            'whatsappInstancias',
            'busca',
            'cargoId',
            'status'
        ));
    }

    public function store(Request $request)
    {
        $request->merge([
            'pode_ter_veiculo' => $request->input('pode_ter_veiculo', 0),
            'pode_acessar_whatsapp' => $request->input('pode_acessar_whatsapp', 0),
        ]);

        $validator = Validator::make(
            $request->all(),
            [
                'name' => ['required', 'string', 'max:255'],
                'cpf' => ['required', 'string', 'size:14', 'unique:users,cpf'],
                'telefone' => ['required', 'string', 'max:20', 'unique:users,telefone'],
                'email' => ['required', 'email', 'max:255', 'unique:users,email'],
                'cargo_id' => [
                    'required',
                    Rule::exists('cargos', 'id')->where(fn ($q) => $q->where('tipo', 'usuario')),
                ],
                'status' => ['required', Rule::in(['ativo', 'inativo'])],

                'pode_ter_veiculo' => ['required', Rule::in(['0', '1', 0, 1, true, false])],
                'veiculo_id' => ['nullable', 'exists:veiculos,id'],

                'pode_acessar_whatsapp' => ['required', Rule::in(['0', '1', 0, 1, true, false])],
                'whatsapp_instancias' => [
                    Rule::requiredIf(fn () => $request->boolean('pode_acessar_whatsapp')),
                    'array',
                ],
                'whatsapp_instancias.*' => [
                    Rule::requiredIf(fn () => $request->boolean('pode_acessar_whatsapp')),
                    'nullable',
                    'integer',
                    'exists:whatsapp_instancias,id',
                ],
            ],
            $this->mensagensValidacao()
        );

        if ($validator->fails()) {
            return redirect()
                ->route('usuarios.index')
                ->withErrors($validator)
                ->withInput()
                ->with('open_create_modal', true);
        }

        $veiculoId = $request->boolean('pode_ter_veiculo') ? $request->veiculo_id : null;

        if ($veiculoId) {
            User::where('veiculo_id', $veiculoId)->update(['veiculo_id' => null]);
        }

        $user = User::create([
            'name' => $request->name,
            'cpf' => $this->formatarCpf($request->cpf),
            'telefone' => $this->formatarTelefone($request->telefone),
            'email' => $request->email,
            'cargo_id' => $request->cargo_id,
            'status' => $request->status,
            'primeiro_acesso' => true,
            'password' => Hash::make('12345'),
            'pode_ter_veiculo' => $request->boolean('pode_ter_veiculo'),
            'veiculo_id' => $veiculoId,
        ]);

        $whatsappIds = $request->boolean('pode_acessar_whatsapp')
            ? array_values(array_filter($request->input('whatsapp_instancias', [])))
            : [];

        $user->whatsappInstancias()->sync($whatsappIds);

        return redirect()
            ->route('usuarios.index')
            ->with('success', 'Usuário cadastrado com sucesso.');
    }

    public function update(Request $request, User $user)
    {
        $request->merge([
            'pode_ter_veiculo' => $request->input('pode_ter_veiculo', 0),
            'pode_acessar_whatsapp' => $request->input('pode_acessar_whatsapp', 0),
        ]);

        $validator = Validator::make(
            $request->all(),
            [
                'name' => ['required', 'string', 'max:255'],
                'cpf' => ['required', 'string', 'size:14', Rule::unique('users', 'cpf')->ignore($user->id)],
                'telefone' => ['required', 'string', 'max:20', Rule::unique('users', 'telefone')->ignore($user->id)],
                'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
                'cargo_id' => [
                    'required',
                    Rule::exists('cargos', 'id')->where(fn ($q) => $q->where('tipo', 'usuario')),
                ],
                'status' => ['required', Rule::in(['ativo', 'inativo'])],

                'pode_ter_veiculo' => ['required', Rule::in(['0', '1', 0, 1, true, false])],
                'veiculo_id' => ['nullable', 'exists:veiculos,id'],

                'pode_acessar_whatsapp' => ['required', Rule::in(['0', '1', 0, 1, true, false])],
                'whatsapp_instancias' => [
                    Rule::requiredIf(fn () => $request->boolean('pode_acessar_whatsapp')),
                    'array',
                ],
                'whatsapp_instancias.*' => [
                    Rule::requiredIf(fn () => $request->boolean('pode_acessar_whatsapp')),
                    'nullable',
                    'integer',
                    'exists:whatsapp_instancias,id',
                ],
            ],
            $this->mensagensValidacao()
        );

        if ($validator->fails()) {
            return redirect()
                ->route('usuarios.index')
                ->withErrors($validator)
                ->withInput()
                ->with('open_edit_modal', $user->id);
        }

        $veiculoId = $request->boolean('pode_ter_veiculo') ? $request->veiculo_id : null;

        if ($veiculoId) {
            User::where('veiculo_id', $veiculoId)
                ->where('id', '!=', $user->id)
                ->update(['veiculo_id' => null]);
        }

        $user->update([
            'name' => $request->name,
            'cpf' => $this->formatarCpf($request->cpf),
            'telefone' => $this->formatarTelefone($request->telefone),
            'email' => $request->email,
            'cargo_id' => $request->cargo_id,
            'status' => $request->status,
            'pode_ter_veiculo' => $request->boolean('pode_ter_veiculo'),
            'veiculo_id' => $veiculoId,
        ]);

        $whatsappIds = $request->boolean('pode_acessar_whatsapp')
            ? array_values(array_filter($request->input('whatsapp_instancias', [])))
            : [];

        $user->whatsappInstancias()->sync($whatsappIds);

        return redirect()
            ->route('usuarios.index')
            ->with('success', 'Usuário atualizado com sucesso.');
    }

    public function toggleStatus(User $user)
    {
        $user->update([
            'status' => $user->status === 'ativo' ? 'inativo' : 'ativo',
        ]);

        return back()->with('success', 'Status atualizado.');
    }

    public function resetPassword(User $user)
    {
        $user->update([
            'password' => Hash::make('12345'),
            'primeiro_acesso' => true,
        ]);

        return back()->with('success', 'Senha redefinida.');
    }

    public function destroy(User $user)
{
    if (auth()->id() === $user->id) {
        return back()->with('error', 'Você não pode desativar seu próprio usuário.');
    }

    if ($user->status === 'inativo') {
        return back()->with('error', 'Este usuário já está inativo.');
    }

    $user->update([
        'status' => 'inativo',
    ]);

    return back()->with('success', 'Usuário desativado com sucesso.');
}

    private function mensagensValidacao(): array
    {
        return [
            'name.required' => 'Informe o nome do usuário.',
            'cpf.required' => 'Informe o CPF.',
            'cpf.size' => 'O CPF deve estar no formato 000.000.000-00.',
            'cpf.unique' => 'Já existe um usuário com esse CPF.',
            'telefone.required' => 'Informe o telefone.',
            'telefone.unique' => 'Já existe um usuário com esse telefone.',
            'email.required' => 'Informe o e-mail.',
            'email.email' => 'Informe um e-mail válido.',
            'email.unique' => 'Já existe um usuário com esse e-mail.',
            'cargo_id.required' => 'Selecione um cargo.',
            'status.required' => 'Selecione o status.',

            'pode_acessar_whatsapp.required' => 'Informe se o usuário pode acessar o WhatsApp.',
            'whatsapp_instancias.required' => 'Selecione uma instância de WhatsApp.',
            'whatsapp_instancias.array' => 'Selecione uma instância de WhatsApp válida.',
            'whatsapp_instancias.*.required' => 'Selecione uma instância de WhatsApp.',
            'whatsapp_instancias.*.integer' => 'Selecione uma instância de WhatsApp válida.',
            'whatsapp_instancias.*.exists' => 'A instância de WhatsApp selecionada não existe.',
        ];
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