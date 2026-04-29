<!doctype html>
<html lang="pt-BR" data-bs-theme="dark">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, viewport-fit=cover" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>Primeiro Acesso - Sistema EPI JPC</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="{{ asset('assets/imgs/logo.png') }}" type="image/x-icon">

    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    <link rel="dns-prefetch" href="https://cdn.jsdelivr.net">

    <link rel="preload" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" as="style">
    <link rel="preload" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" as="style">
    <link rel="preload" href="{{ asset('css/login.css') }}" as="style">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>
<body>
    <div class="container-tight">
        <div class="text-center mb-4">
            <a href="{{ url('/trocar-senha') }}" class="navbar-brand navbar-brand-autodark">
                <img
                    src="{{ asset('assets/imgs/logo.png') }}"
                    alt="Logo Sistema EPI JPC"
                    loading="eager"
                    fetchpriority="high"
                    decoding="async"
                >
            </a>
        </div>

        <h2 class="login-title">
            <i class="bi bi-key"></i> Primeiro Acesso
        </h2>

        <div class="login-subtitle">
            Defina uma nova senha para continuar no sistema
        </div>

        <form method="POST" action="{{ route('primeiro.acesso.salvar') }}" autocomplete="off">
            @csrf

            <div class="mb-3">
                <div class="input-group input-group-flat">
                    <input
                        type="password"
                        class="form-control"
                        name="password"
                        id="password"
                        placeholder="Nova senha"
                        required
                    >

                    <button
                        type="button"
                        class="input-group-text"
                        onclick="togglePassword('password', 'togglePasswordIcon')"
                        aria-label="Mostrar ou ocultar senha"
                    >
                        <i class="bi bi-eye" id="togglePasswordIcon"></i>
                    </button>
                </div>
            </div>

            <div class="mb-2">
                <div class="input-group input-group-flat">
                    <input
                        type="password"
                        class="form-control"
                        name="password_confirmation"
                        id="password_confirmation"
                        placeholder="Confirmar nova senha"
                        required
                    >

                    <button
                        type="button"
                        class="input-group-text"
                        onclick="togglePassword('password_confirmation', 'togglePasswordConfirmIcon')"
                        aria-label="Mostrar ou ocultar senha"
                    >
                        <i class="bi bi-eye" id="togglePasswordConfirmIcon"></i>
                    </button>
                </div>

                @if ($errors->any())
                    <div class="alert-custom alert-danger-custom">
                        {{ $errors->first() }}
                    </div>
                @endif

                @if (session('success'))
                    <div class="alert-custom alert-success-custom">
                        {{ session('success') }}
                    </div>
                @endif
            </div>

            <div class="form-footer mt-3">
                <button type="submit" class="btn btn-login">
                    Salvar nova senha
                </button>
            </div>
        </form>
    </div>

    @include('partials.footer')

    <script>
        function togglePassword(inputId, iconId) {
            const passwordField = document.getElementById(inputId);
            const toggleIcon = document.getElementById(iconId);

            const isPassword = passwordField.type === 'password';
            passwordField.type = isPassword ? 'text' : 'password';

            toggleIcon.classList.toggle('bi-eye', !isPassword);
            toggleIcon.classList.toggle('bi-eye-slash', isPassword);
        }
    </script>

    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        defer
    ></script>
</body>
</html>