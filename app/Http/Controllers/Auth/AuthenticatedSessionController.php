<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    public function create()
    {
        return view('auth.login');
    }

    public function store(Request $request)
    {
        $request->validate([
            'cpf' => ['required', 'size:14'],
            'password' => ['required'],
        ], [
            'cpf.required' => 'Informe o CPF.',
            'cpf.size' => 'O CPF deve estar no formato 000.000.000-00.',
            'password.required' => 'Informe a senha.',
        ]);

        $cpf = $this->formatarCpf($request->cpf);

        if (!Auth::attempt([
            'cpf' => $cpf,
            'password' => $request->password,
            'status' => 'ativo',
        ])) {
            return back()->withErrors([
                'cpf' => 'CPF ou senha inválidos.',
            ])->onlyInput('cpf');
        }

        $request->session()->regenerate();

        $user = auth()->user();

        if ($user->primeiro_acesso) {
            return redirect()->route('primeiro.acesso');
        }

        if (!$user->cargo_id) {
            Auth::logout();

            return redirect()->route('login')->withErrors([
                'cpf' => 'Usuário sem cargo vinculado.',
            ]);
        }

        return redirect()->route('dashboard');
    }

    public function destroy(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    private function formatarCpf(string $cpf): string
    {
        $cpf = preg_replace('/\D/', '', $cpf);

        return preg_replace(
            '/(\d{3})(\d{3})(\d{3})(\d{2})/',
            '$1.$2.$3-$4',
            $cpf
        );
    }
}