<?php

namespace App\Http\Controllers;

use App\Models\Cargo;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $busca = trim((string) $request->get('busca'));
        $cargoId = $request->get('cargo_id');
        $status = $request->get('status');

        $usuarios = User::with('cargo')
            ->when($busca !== '', function ($query) use ($busca) {
                $query->where(function ($q) use ($busca) {
                    $q->where('name', 'like', '%' . $busca . '%')
                        ->orWhere('cpf', 'like', '%' . $busca . '%')
                        ->orWhere('email', 'like', '%' . $busca . '%')
                        ->orWhere('telefone', 'like', '%' . $busca . '%');
                });
            })
            ->when(!empty($cargoId), function ($query) use ($cargoId) {
                $query->where('cargo_id', $cargoId);
            })
            ->when(in_array($status, ['ativo', 'inativo']), function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->orderBy('name')
            ->get();

        $cargos = Cargo::orderBy('nome')->get();

        return view('usuarios.index', compact('usuarios', 'cargos', 'busca', 'cargoId', 'status'));
    }

    public function formPrimeiroAcesso()
    {
        return view('auth.primeiro-acesso');
    }

    public function salvarPrimeiroAcesso(Request $request)
    {
        $request->validate([
            'password' => ['required', 'min:5', 'confirmed'],
        ], [
            'password.required' => 'Informe a nova senha.',
            'password.min' => 'A nova senha deve ter pelo menos 5 caracteres.',
            'password.confirmed' => 'A confirmação da nova senha não confere.',
        ]);

        $user = auth()->user();

        $user->update([
            'password' => Hash::make($request->password),
            'primeiro_acesso' => false,
        ]);

        return redirect()
            ->route('dashboard')
            ->with('success', 'Senha alterada com sucesso.');
    }

    public function editProfile()
    {
        return view('shared.perfil.edit');
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'telefone' => [
                'required',
                'string',
                'max:20',
                Rule::unique('users', 'telefone')->ignore($user->id),
            ],
        ], [
            'name.required' => 'O nome é obrigatório.',
            'name.max' => 'O nome deve ter no máximo 255 caracteres.',
            'email.required' => 'O e-mail é obrigatório.',
            'email.email' => 'Informe um e-mail válido.',
            'email.max' => 'O e-mail deve ter no máximo 255 caracteres.',
            'email.unique' => 'Este e-mail já está cadastrado.',
            'telefone.required' => 'O telefone é obrigatório.',
            'telefone.max' => 'O telefone deve ter no máximo 20 caracteres.',
            'telefone.unique' => 'Este telefone já está cadastrado.',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'telefone' => $this->formatarTelefone($request->telefone),
        ]);

        return redirect()
            ->route('perfil.edit')
            ->with('success', 'Perfil atualizado com sucesso.');
    }

    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'senha_atual' => ['required'],
            'password' => ['required', 'min:5', 'confirmed'],
        ], [
            'senha_atual.required' => 'Informe a senha atual.',
            'password.required' => 'Informe a nova senha.',
            'password.min' => 'A nova senha deve ter pelo menos 5 caracteres.',
            'password.confirmed' => 'A confirmação da nova senha não confere.',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('abrir_senha_card', true);
        }

        $user = auth()->user();

        if (!Hash::check($request->senha_atual, $user->password)) {
            return back()
                ->withErrors([
                    'senha_atual' => 'A senha atual está incorreta.',
                ])
                ->withInput()
                ->with('abrir_senha_card', true);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()
            ->route('perfil.edit')
            ->with('success_password', 'Senha alterada com sucesso.');
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