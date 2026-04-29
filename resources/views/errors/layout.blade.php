<!doctype html>
<html lang="pt-BR" data-bs-theme="dark">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, viewport-fit=cover" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>{{ $title ?? 'Erro' }} - Sistema EPI JPC</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="{{ asset('assets/imgs/logo.png') }}" type="image/x-icon">

    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    <link rel="dns-prefetch" href="https://cdn.jsdelivr.net">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        :root {
            --bg-main: #0f172a;
            --bg-card: rgba(15, 23, 42, 0.88);
            --border-color: rgba(148, 163, 184, 0.18);
            --text-main: #f8fafc;
            --text-soft: #94a3b8;
            --danger: #ef4444;
            --primary: #2563eb;
            --secondary: #334155;
            --secondary-hover: #3f4d63;
            --success: #22c55e;
            --shadow: 0 25px 60px rgba(0, 0, 0, 0.45);
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: Arial, Helvetica, sans-serif;
            color: var(--text-main);
            background:
                radial-gradient(circle at top, rgba(37, 99, 235, 0.14), transparent 35%),
                radial-gradient(circle at bottom right, rgba(239, 68, 68, 0.10), transparent 25%),
                linear-gradient(135deg, #020617 0%, #0f172a 50%, #111827 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .error-wrapper {
            width: 100%;
            max-width: 560px;
        }

        .brand-top {
            text-align: center;
            margin-bottom: 22px;
        }

        .brand-top img {
            max-width: 92px;
            width: 100%;
            height: auto;
            object-fit: contain;
            filter: drop-shadow(0 8px 18px rgba(0, 0, 0, 0.35));
        }

        .error-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 22px;
            box-shadow: var(--shadow);
            padding: 38px 32px;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            text-align: center;
        }

        .error-icon {
            width: 72px;
            height: 72px;
            margin: 0 auto 18px auto;
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(239, 68, 68, 0.12);
            border: 1px solid rgba(239, 68, 68, 0.18);
            color: #f87171;
            font-size: 30px;
        }

        .error-code {
            font-size: 64px;
            line-height: 1;
            font-weight: 800;
            color: #ef4444;
            margin-bottom: 10px;
            letter-spacing: -1px;
        }

        .error-title {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 12px;
            color: var(--text-main);
        }

        .error-message {
            font-size: 1rem;
            line-height: 1.8;
            color: var(--text-soft);
            margin: 0 auto 26px auto;
            max-width: 440px;
        }

        .error-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            justify-content: center;
            margin-bottom: 18px;
        }

        .btn-error {
            min-width: 160px;
            border-radius: 14px;
            padding: 12px 20px;
            font-weight: 700;
            font-size: 15px;
            border: none;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all .2s ease;
        }

        .btn-error:hover {
            transform: translateY(-1px);
        }

        .btn-back {
            background: var(--secondary);
            color: #fff;
            border: 1px solid rgba(148, 163, 184, 0.18);
        }

        .btn-back:hover {
            background: var(--secondary-hover);
            color: #fff;
        }

        .btn-home {
            background: var(--primary);
            color: #fff;
        }

        .btn-home:hover {
            background: #1d4ed8;
            color: #fff;
        }

        .btn-logout {
            background: #dc2626;
            color: #fff;
        }

        .btn-logout:hover {
            background: #b91c1c;
            color: #fff;
        }

        .btn-login {
            background: var(--success);
            color: #fff;
        }

        .btn-login:hover {
            background: #16a34a;
            color: #fff;
        }

        .helper-text {
            margin-top: 6px;
            font-size: 14px;
            color: #64748b;
        }

        form {
            margin: 0;
        }

        @media (max-width: 576px) {
            .error-card {
                padding: 30px 20px;
                border-radius: 18px;
            }

            .error-code {
                font-size: 52px;
            }

            .error-title {
                font-size: 1.55rem;
            }

            .error-actions {
                flex-direction: column;
            }

            .btn-error,
            form {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="error-wrapper">
        <div class="brand-top">
            <a href="{{ route('login') }}">
                <img
                    src="{{ asset('assets/imgs/logo.png') }}"
                    alt="Logo Sistema EPI JPC"
                    loading="eager"
                    fetchpriority="high"
                    decoding="async"
                >
            </a>
        </div>

        <div class="error-card">
            <div class="error-icon">
                <i class="{{ $icon ?? 'bi bi-exclamation-triangle-fill' }}"></i>
            </div>

            <div class="error-code">{{ $code ?? 'Erro' }}</div>

            <h1 class="error-title">
                {{ $heading ?? 'Ocorreu um problema no sistema' }}
            </h1>

            <p class="error-message">
                {{ $message ?? 'Não foi possível concluir sua solicitação. Tente novamente ou retorne ao sistema.' }}
            </p>

            <div class="error-actions">
                @if(!empty($showBackButton))
                    <a href="javascript:history.back()" class="btn-error btn-back">
                        <i class="bi bi-arrow-left"></i>
                        Voltar
                    </a>
                @endif

                @if(!empty($showHomeButton))
                    <a href="{{ url('/dashboard') }}" class="btn-error btn-home">
                        <i class="bi bi-grid-1x2-fill"></i>
                        Ir para o painel
                    </a>
                @endif

                @if(!empty($showLoginButton))
                    <a href="{{ route('login') }}" class="btn-error btn-login">
                        <i class="bi bi-box-arrow-in-right"></i>
                        Entrar novamente
                    </a>
                @endif

                @if(!empty($showLogoutButton))
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn-error btn-logout">
                            <i class="bi bi-box-arrow-right"></i>
                            Fazer logout
                        </button>
                    </form>
                @endif
            </div>

            <div class="helper-text">
                Se o problema continuar, tente entrar novamente no sistema ou fale com o administrador.
            </div>
        </div>
    </div>
</body>
</html>