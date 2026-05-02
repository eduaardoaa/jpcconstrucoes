<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Vaga Encerrada — JPC Construções & Incorporações</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
    *{margin:0;padding:0;box-sizing:border-box}
    body{font-family:'Inter',sans-serif;min-height:100vh;background:#050a14;color:#e2e8f0;display:flex;flex-direction:column}
    .ap-bg{position:fixed;inset:0;z-index:0;pointer-events:none;overflow:hidden}
    .ap-bg::before{content:"";position:absolute;width:800px;height:800px;top:-300px;left:50%;transform:translateX(-50%);background:radial-gradient(circle,rgba(239,68,68,.06),transparent 65%);border-radius:50%}
    .ap-grid-bg{position:fixed;inset:0;z-index:0;pointer-events:none;background-image:linear-gradient(rgba(148,163,184,.03) 1px,transparent 1px),linear-gradient(90deg,rgba(148,163,184,.03) 1px,transparent 1px);background-size:60px 60px}
    .ap-topbar{position:relative;z-index:2;padding:16px 24px;display:flex;align-items:center;justify-content:center;border-bottom:1px solid rgba(148,163,184,.06);background:rgba(5,10,20,.6);backdrop-filter:blur(12px)}
    .ap-logo{height:32px;opacity:.85}
    .ap-center{flex:1;display:flex;align-items:center;justify-content:center;position:relative;z-index:1;padding:40px 20px}
    .ap-closed-card{text-align:center;max-width:440px;padding:48px 32px;background:rgba(15,23,42,.5);border:1px solid rgba(148,163,184,.1);border-radius:22px;backdrop-filter:blur(12px)}
    .ap-closed-icon{width:64px;height:64px;border-radius:50%;background:rgba(239,68,68,.08);border:2px solid rgba(239,68,68,.15);display:flex;align-items:center;justify-content:center;margin:0 auto 16px;font-size:28px;color:#ef4444}
    .ap-closed-title{font-size:20px;font-weight:700;color:#f8fafc;margin-bottom:6px}
    .ap-closed-text{font-size:14px;color:#94a3b8;line-height:1.6}
    .ap-closed-vaga{display:inline-block;margin-top:14px;padding:8px 16px;border-radius:10px;background:rgba(255,255,255,.03);border:1px solid rgba(148,163,184,.1);font-size:13px;font-weight:600;color:#e2e8f0}
    .ap-footer{position:relative;z-index:1;text-align:center;padding:24px 20px;border-top:1px solid rgba(148,163,184,.05)}
    .ap-footer p{font-size:11.5px;color:#475569}
    </style>
</head>
<body>
    <div class="ap-bg"></div>
    <div class="ap-grid-bg"></div>
    <div class="ap-topbar">
        <img src="/assets/imgs/logo.png" alt="JPC Construções & Incorporações" class="ap-logo">
    </div>
    <div class="ap-center">
        <div class="ap-closed-card">
            <div class="ap-closed-icon"><i class="bi bi-lock-fill"></i></div>
            <div class="ap-closed-title">Vaga encerrada</div>
            <p class="ap-closed-text">Esta vaga não está mais aceitando candidaturas no momento. Fique de olho para novas oportunidades!</p>
            <div class="ap-closed-vaga"><i class="bi bi-briefcase-fill"></i> {{ $vaga->titulo }}</div>
        </div>
    </div>
    <div class="ap-footer">
        <p>&copy; {{ date('Y') }} JPC Construções & Incorporações. Todos os direitos reservados.</p>
    </div>
</body>
</html>
