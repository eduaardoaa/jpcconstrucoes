@extends('layouts.app')

@section('title', 'Meu Perfil')
@section('pageTitle', 'Meu Perfil')
@section('pageDescription', 'Atualize seus dados de acesso e altere sua senha.')

@section('content')
    <div class="page-head">
        <div>
            <h2>Editar perfil</h2>
            <p>Gerencie suas informações pessoais e de acesso.</p>
        </div>
    </div>

    @if (session('success'))
        <div class="alert-success-box">
            {{ session('success') }}
        </div>
    @endif

    @if (session('success_password'))
        <div class="alert-success-box">
            {{ session('success_password') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert-error-box">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <div class="card-title">Dados do perfil</div>
            <div class="card-subtitle">Atualize nome, e-mail e telefone.</div>
        </div>

        <div class="card-body">
            <form action="{{ route('perfil.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Nome</label>
                        <input
                            type="text"
                            name="name"
                            class="form-control-custom"
                            value="{{ old('name', auth()->user()->name) }}"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label class="form-label">E-mail</label>
                        <input
                            type="email"
                            name="email"
                            class="form-control-custom"
                            value="{{ old('email', auth()->user()->email) }}"
                        >
                    </div>

                    <div class="form-group">
                        <label class="form-label">Telefone</label>
                        <input
                            type="text"
                            name="telefone"
                            id="telefone"
                            class="form-control-custom"
                            value="{{ old('telefone', auth()->user()->telefone) }}"
                            maxlength="15"
                            inputmode="numeric"
                            placeholder="(00) 00000-0000"
                        >
                    </div>

                    <div class="form-group">
                        <label class="form-label">CPF</label>
                        <input
                            type="text"
                            class="form-control-custom"
                            value="{{ auth()->user()->cpf }}"
                            disabled
                        >
                    </div>

                    <div class="form-group">
                        <label class="form-label">Matrícula</label>
                        <input
                            type="text"
                            class="form-control-custom"
                            value="{{ auth()->user()->matricula }}"
                            disabled
                        >
                    </div>

                    <div class="form-group">
                        <label class="form-label">Cargo</label>
                        <input
                            type="text"
                            class="form-control-custom"
                            value="{{ auth()->user()->cargo?->nome ?? 'Sem cargo' }}"
                            disabled
                        >
                    </div>

                    <div class="form-group-full actions-inline">
                        <button type="submit" class="btn btn-green">
                            <i class="bi bi-check2-circle"></i>
                            Salvar alterações
                        </button>

                        <button type="button" class="btn btn-dark" onclick="toggleSenhaCard()">
                            <i class="bi bi-shield-lock"></i>
                            Alterar senha
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="section-spacer"></div>

    <div class="card" id="senhaCard" style="display: none;">
        <div class="card-header">
            <div class="card-title">Alterar senha</div>
            <div class="card-subtitle">Informe sua senha atual e defina uma nova senha.</div>
        </div>

        <div class="card-body">
            <form action="{{ route('perfil.password.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-grid">
                    <div class="form-group form-group-full">
                        <label class="form-label">Senha atual</label>
                        <input
                            type="password"
                            name="senha_atual"
                            class="form-control-custom"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label class="form-label">Nova senha</label>
                        <input
                            type="password"
                            name="password"
                            class="form-control-custom"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label class="form-label">Confirmar nova senha</label>
                        <input
                            type="password"
                            name="password_confirmation"
                            class="form-control-custom"
                            required
                        >
                    </div>

                    <div class="form-group-full actions-inline">
                        <button type="submit" class="btn btn-dark">
                            <i class="bi bi-shield-lock"></i>
                            Salvar nova senha
                        </button>

                        <button type="button" class="btn btn-danger-soft" onclick="toggleSenhaCard(false)">
                            <i class="bi bi-x-circle"></i>
                            Cancelar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        function toggleSenhaCard(force = null) {
            const card = document.getElementById('senhaCard');
            if (!card) return;

            const isHidden = card.style.display === 'none' || card.style.display === '';
            const shouldShow = force === null ? isHidden : force;

            card.style.display = shouldShow ? 'block' : 'none';

            if (shouldShow) {
                card.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            const telefoneInput = document.getElementById('telefone');

            if (telefoneInput) {
                telefoneInput.addEventListener('input', function (e) {
                    let value = e.target.value.replace(/\D/g, '').slice(0, 11);

                    if (value.length > 0) {
                        value = value.replace(/^(\d{0,2})/, '($1');
                    }

                    if (value.length > 2) {
                        value = value.replace(/^\((\d{2})(\d)/, '($1) $2');
                    }

                    if (value.length > 6) {
                        if (value.length === 11) {
                            value = value.replace(/(\d{5})(\d{1,4})$/, '$1-$2');
                        } else {
                            value = value.replace(/(\d{4})(\d{1,4})$/, '$1-$2');
                        }
                    }

                    e.target.value = value;
                });

                telefoneInput.addEventListener('keypress', function (e) {
                    if (!/[0-9]/.test(e.key)) {
                        e.preventDefault();
                    }
                });
            }

            @if (session('abrir_senha_card'))
                toggleSenhaCard(true);
            @endif
        });
    </script>
@endsection