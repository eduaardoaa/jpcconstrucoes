@extends('layouts.app')

@section('title', 'WhatsApp Web JPC')

@section('content')
<style>
    /* Ajustado para não cobrir o layout/sidebar do sistema */
    #main-content, .container, .container-fluid, .content-wrapper, .main-panel {
        padding: 0 !important;
        max-width: 100% !important;
    }

    .page-header, .content-header {
        display: none !important;
    }

    body {
        overflow-x: hidden;
        overflow-y: auto;
    }

    :root {
        --wa-bg: #0b141a;
        --wa-sidebar: #111b21;
        --wa-header: #202c33;
        --wa-border: #222d34;
        --wa-text: #e9edef;
        --wa-text-muted: #8696a0;
        --wa-green: #00a884;
        --wa-blue: #53bdeb;
        --wa-bubble-in: #202c33;
        --wa-bubble-out: #005c4b;
        --wa-active: #2a3942;
        --wa-icon: #aebac1;
        --wa-group-color: #f0a500;
    }

    .wa-wrapper {
    position: relative;
    width: 100%;
height: calc(100vh - 125px);    display: flex;
    background: var(--wa-bg);
    z-index: auto;
    overflow: hidden;
}

    /* ========== SIDEBAR ========== */
    .wa-sidebar {
        width: 420px;
        min-width: 380px;
        display: flex;
        flex-direction: column;
        background: var(--wa-sidebar);
        border-right: 1px solid var(--wa-border);
        flex-shrink: 0;
    }

    .wa-sidebar-header {
        background: var(--wa-header);
        padding: 10px 16px;
        display: flex;
        flex-direction: column;
        gap: 12px;
        flex-shrink: 0;
    }

    .wa-sidebar-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .wa-sidebar-title {
        font-size: 20px;
        font-weight: 700;
        color: var(--wa-text);
        margin: 0;
    }

    .wa-instancia-select {
        background: #2a3942;
        border: 1px solid #3b4a54;
        color: var(--wa-text);
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 14px;
        outline: none;
        width: 100%;
        cursor: pointer;
    }

    .wa-search-bar {
        padding: 7px 12px;
        background: var(--wa-sidebar);
        border-bottom: 1px solid var(--wa-border);
        flex-shrink: 0;
    }

    .wa-search-input-group {
        background: var(--wa-header);
        border-radius: 8px;
        display: flex;
        align-items: center;
        padding: 0 12px;
        height: 35px;
    }

    .wa-search-input-group i {
        color: var(--wa-text-muted);
        font-size: 14px;
        margin-right: 12px;
    }

    .wa-search-input-group input {
        background: transparent;
        border: none;
        color: var(--wa-text);
        font-size: 14px;
        width: 100%;
        outline: none;
    }

    .wa-chats-container {
        flex: 1;
        overflow-y: auto;
    }

    /* ========== CHAT TILE ========== */
    .wa-chat-tile {
        display: flex;
        height: 72px;
        padding: 0 15px;
        align-items: center;
        cursor: pointer;
        text-decoration: none;
        color: inherit;
        border-bottom: 1px solid var(--wa-border);
        transition: background 0.15s;
    }

    .wa-chat-tile:hover { background: var(--wa-active); }
    .wa-chat-tile.active { background: var(--wa-active); }

    .wa-chat-tile .avatar {
        width: 49px;
        height: 49px;
        border-radius: 50%;
        background: #51585c;
        margin-right: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-weight: 500;
        flex-shrink: 0;
        font-size: 18px;
        text-transform: uppercase;
    }

    .wa-chat-tile .avatar.is-grupo {
        background: #1e3a2a;
        color: var(--wa-green);
        font-size: 20px;
    }

    .wa-chat-tile .info { flex: 1; min-width: 0; }
    .wa-chat-tile .top { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2px; }
    .wa-chat-tile .name { font-size: 16px; color: var(--wa-text); font-weight: 500; overflow: hidden; white-space: nowrap; text-overflow: ellipsis; max-width: 220px; }
    .wa-chat-tile .time { font-size: 12px; color: var(--wa-text-muted); flex-shrink: 0; margin-left: 6px; }
    .wa-chat-tile .bottom { display: flex; justify-content: space-between; align-items: center; }
    .wa-chat-tile .preview { font-size: 13px; color: var(--wa-text-muted); overflow: hidden; white-space: nowrap; text-overflow: ellipsis; flex: 1; }

    .wa-chat-tile .unread-badge {
        background: var(--wa-green);
        color: #0b141a;
        font-weight: bold;
        border-radius: 10px;
        min-width: 20px;
        height: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0 6px;
        font-size: 12px;
        flex-shrink: 0;
        margin-left: 6px;
    }

    .wa-group-tag {
        font-size: 10px;
        color: var(--wa-green);
        background: #1e3a2a;
        border-radius: 4px;
        padding: 1px 5px;
        margin-left: 6px;
        flex-shrink: 0;
        font-weight: 600;
        letter-spacing: 0.3px;
    }

    /* ========== MAIN CHAT ========== */
    .wa-main {
        flex: 1;
        min-width: 0;
        display: flex;
        flex-direction: column;
        background: var(--wa-bg);
        position: relative;
    }

    .wa-main-header {
        height: 60px;
        background: var(--wa-header);
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 16px;
        border-left: 1px solid var(--wa-border);
        flex-shrink: 0;
    }

    .wa-contact-wrapper { display: flex; align-items: center; gap: 12px; }

    .wa-contact-wrapper .avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #51585c;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
        font-size: 16px;
        flex-shrink: 0;
    }

    .wa-contact-wrapper .avatar.is-grupo {
        background: #1e3a2a;
        color: var(--wa-green);
        font-size: 18px;
    }

    .wa-contact-wrapper .name { font-size: 16px; font-weight: 500; color: var(--wa-text); }
    .wa-contact-wrapper .status { font-size: 12px; color: var(--wa-text-muted); }

    .wa-header-actions { display: flex; align-items: center; gap: 16px; }
    .wa-icon-btn { background: transparent; border: none; color: var(--wa-icon); font-size: 20px; cursor: pointer; display: flex; align-items: center; padding: 4px; }
    .wa-icon-btn:hover { color: var(--wa-text); }

    /* ========== ASSINATURA TOGGLE ========== */
    .wa-sig-btn {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 6px 14px;
        background: #2a3942;
        border: 1px solid #3b4a54;
        border-radius: 20px;
        color: var(--wa-text-muted);
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        user-select: none;
    }

    .wa-sig-btn.active {
        background: #005c4b;
        border-color: #00a884;
        color: #fff;
    }

    .wa-sig-btn i { font-size: 14px; }

    /* ========== MENSAGENS ========== */
    .wa-messages-scroll {
        flex: 1;
        overflow-y: auto;
        padding: 20px 8% 10px;
        background-image: url('https://user-images.githubusercontent.com/15075759/28719144-86dc0f70-73b1-11e7-911d-60d70fcded21.png');
        background-blend-mode: overlay;
        display: flex;
        flex-direction: column;
    }

    .wa-msg-row { display: flex; width: 100%; margin-bottom: 2px; }
    .wa-msg-row.out { justify-content: flex-end; }
    .wa-msg-row.in { justify-content: flex-start; }

    .wa-msg-bubble {
        max-width: 65%;
        padding: 6px 7px 8px 9px;
        border-radius: 8px;
        font-size: 14.2px;
        line-height: 19px;
        position: relative;
        box-shadow: 0 1px 0.5px rgba(0,0,0,0.13);
    }

    .wa-msg-row.in .wa-msg-bubble { background: var(--wa-bubble-in); border-top-left-radius: 0; }
    .wa-msg-row.out .wa-msg-bubble { background: var(--wa-bubble-out); border-top-right-radius: 0; }

    .wa-msg-sender-internal {
        font-size: 12.5px;
        font-weight: 700;
        color: var(--wa-blue);
        margin-bottom: 3px;
    }

    .wa-msg-sender-group {
        font-size: 12.5px;
        font-weight: 600;
        color: var(--wa-group-color);
        margin-bottom: 3px;
    }

    .wa-msg-content { white-space: pre-wrap; word-break: break-word; color: var(--wa-text); }
    .wa-msg-footer { display: flex; justify-content: flex-end; align-items: center; gap: 4px; margin-top: 4px; }
    .wa-msg-time { font-size: 11px; color: var(--wa-text-muted); }
    .wa-msg-status { font-size: 14px; color: var(--wa-text-muted); line-height: 1; }
    .wa-msg-status.read { color: var(--wa-blue); }

    /* Mídia */
    .wa-msg-media { margin-bottom: 4px; border-radius: 6px; overflow: hidden; }
    .wa-msg-media img, .wa-msg-media video { max-width: 100%; max-height: 350px; display: block; cursor: pointer; border-radius: 6px; }
    .wa-msg-media audio { width: 100%; min-width: 250px; }

    .wa-msg-media .wa-doc-link {
        display: flex;
        align-items: center;
        gap: 10px;
        background: rgba(255,255,255,0.06);
        padding: 10px 14px;
        border-radius: 8px;
        color: var(--wa-text);
        text-decoration: none;
        font-size: 13px;
    }

    .wa-msg-media .wa-doc-link:hover { background: rgba(255,255,255,0.1); }
    .wa-msg-media .wa-doc-link i { font-size: 24px; color: var(--wa-blue); }

    /* Separador de data */
    .wa-date-divider {
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 12px 0;
    }

    .wa-date-divider span {
        background: #182229;
        border-radius: 8px;
        padding: 4px 12px;
        font-size: 12px;
        color: var(--wa-text-muted);
        box-shadow: 0 1px 2px rgba(0,0,0,0.3);
    }

    /* ========== COMPOSER ========== */
    .wa-footer-composer {
        min-height: 62px;
        background: var(--wa-header);
        display: flex;
        align-items: flex-end;
        padding: 8px 16px;
        gap: 12px;
        flex-shrink: 0;
        position: relative;
    }

    .wa-input-wrap {
        flex: 1;
        background: var(--wa-active);
        border-radius: 8px;
        padding: 9px 12px;
        display: flex;
        flex-direction: column;
        align-items: stretch;
        min-height: 42px;
    }
    .wa-input-wrap textarea {
        align-self: flex-start;
        width: 100%;
    }

    #inputContainer > textarea,
    .wa-input-wrap textarea {
        background: transparent;
        border: none;
        color: var(--wa-text);
        width: 100%;
        font-size: 15px;
        outline: none;
        resize: none;
        line-height: 22px;
        max-height: 140px;
        overflow-y: auto;
        padding: 0;
        font-family: inherit;
    }

    .wa-input-wrap textarea::placeholder { color: var(--wa-text-muted); }

    /* Gravação de áudio */
    .wa-rec-ui {
        flex: 1;
        display: none;
        align-items: center;
        justify-content: space-between;
        padding: 0 14px;
        background: var(--wa-active);
        border-radius: 8px;
        height: 42px;
    }

    .wa-rec-ui.active { display: flex; }

    .wa-rec-dot {
        width: 10px;
        height: 10px;
        background: #f44336;
        border-radius: 50%;
        animation: pulse 1s infinite;
        margin-right: 10px;
    }

    @keyframes pulse {
        0%,100% { opacity: 1; }
        50% { opacity: 0.3; }
    }

    .wa-rec-cancel { color: #f44336; font-weight: bold; cursor: pointer; font-size: 13px; }

    /* Popup de anexo */
    .wa-attach-popup {
        position: absolute;
        bottom: 75px;
        left: 20px;
        background: #233138;
        border-radius: 16px;
        padding: 12px;
        display: none;
        flex-direction: column;
        gap: 8px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.5);
        z-index: 1000;
    }

    .wa-attach-popup.active { display: flex; }

    .wa-attach-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 8px 12px;
        border-radius: 10px;
        cursor: pointer;
        color: var(--wa-text);
        font-size: 14px;
        transition: background 0.15s;
    }

    .wa-attach-item:hover { background: rgba(255,255,255,0.08); }

    .wa-attach-circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 18px;
        flex-shrink: 0;
    }

    /* Welcome screen */
    .wa-welcome-screen {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        color: var(--wa-text-muted);
        border-left: 1px solid var(--wa-border);
    }

    .wa-welcome-screen i { font-size: 80px; opacity: 0.08; margin-bottom: 20px; }
    .wa-welcome-screen h3 { color: var(--wa-text); margin-bottom: 10px; font-size: 22px; font-weight: 300; }
    .wa-welcome-screen p { font-size: 14px; opacity: 0.6; }

    /* ========== MODAL NOVA CONVERSA ========== */
    .wa-modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.55);
        z-index: 2000;
        display: none;
        align-items: center;
        justify-content: center;
    }

    .wa-modal-overlay.active { display: flex; }

    .wa-modal-box {
        background: var(--wa-header);
        border-radius: 14px;
        width: 380px;
        max-width: 90vw;
        box-shadow: 0 20px 60px rgba(0,0,0,0.6);
        overflow: hidden;
    }

    .wa-modal-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px 20px;
        border-bottom: 1px solid var(--wa-border);
    }

    .wa-modal-header span { font-size: 16px; font-weight: 600; color: var(--wa-text); }

    .wa-modal-body { padding: 20px; }
    .wa-modal-body p { font-size: 13px; color: var(--wa-text-muted); margin-bottom: 12px; }

    .wa-modal-input {
        width: 100%;
        background: var(--wa-active);
        border: 1px solid var(--wa-border);
        border-radius: 8px;
        color: var(--wa-text);
        font-size: 15px;
        padding: 10px 14px;
        outline: none;
        transition: border-color 0.2s;
    }

    .wa-modal-input:focus { border-color: var(--wa-green); }
    .wa-modal-input::placeholder { color: var(--wa-text-muted); }

    .wa-modal-footer {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        padding: 14px 20px;
        border-top: 1px solid var(--wa-border);
    }

    .wa-btn-cancel {
        background: transparent;
        border: 1px solid var(--wa-border);
        color: var(--wa-text-muted);
        padding: 8px 20px;
        border-radius: 8px;
        font-size: 14px;
        cursor: pointer;
        transition: background 0.15s;
    }

    .wa-btn-cancel:hover { background: rgba(255,255,255,0.06); }

    .wa-btn-primary {
        background: var(--wa-green);
        border: none;
        color: #fff;
        padding: 8px 20px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.15s;
    }

    .wa-btn-primary:hover { background: #00c49a; }
    .wa-btn-primary:disabled { opacity: 0.5; cursor: not-allowed; }

    /* Banner @lid */
    .wa-lid-banner {
        background: #2d1f00;
        border-bottom: 1px solid #a06000;
        padding: 8px 16px;
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 13px;
        color: #ffb347;
        flex-shrink: 0;
    }

    .wa-lid-banner i { font-size: 16px; flex-shrink: 0; }

    .wa-lid-set-btn {
        margin-left: auto;
        background: #a06000;
        border: none;
        color: #fff;
        padding: 4px 14px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        flex-shrink: 0;
        transition: background 0.15s;
    }

    .wa-lid-set-btn:hover { background: #c07800; }

    /* Scrollbars */
    .wa-chats-container::-webkit-scrollbar,
    .wa-messages-scroll::-webkit-scrollbar { width: 6px; }

    .wa-chats-container::-webkit-scrollbar-thumb,
    .wa-messages-scroll::-webkit-scrollbar-thumb {
        background: #374045;
        border-radius: 3px;
    }

    /* Animação de entrada nova mensagem */
    @keyframes spin { from{transform:rotate(0deg)} to{transform:rotate(360deg)} }
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(8px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .wa-msg-new { animation: fadeInUp 0.25s ease; }

    /* ========== AÇÕES NA MENSAGEM (responder / apagar) ========== */
    .wa-msg-actions {
        position: absolute;
        top: 4px;
        right: 4px;
        display: none;
        gap: 3px;
        z-index: 10;
    }
    .wa-msg-row:hover .wa-msg-actions { display: flex; }
    .wa-msg-action-btn {
        background: rgba(0,0,0,0.45);
        border: none;
        color: #fff;
        width: 26px;
        height: 26px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        cursor: pointer;
        padding: 0;
        transition: background 0.15s;
        flex-shrink: 0;
    }
    .wa-msg-action-btn.reply:hover  { background: rgba(0,168,132,0.75); }
    .wa-msg-action-btn.delete:hover { background: rgba(244,67,54,0.75); }

    /* ========== CITAÇÃO / QUOTED ========== */
    .wa-quoted-bubble {
        background: rgba(0,0,0,0.22);
        border-left: 3px solid var(--wa-green);
        border-radius: 0 6px 6px 0;
        padding: 5px 8px;
        margin-bottom: 5px;
        cursor: default;
        overflow: hidden;
    }
    .wa-quoted-autor {
        font-size: 12px;
        font-weight: 700;
        color: var(--wa-green);
        margin-bottom: 2px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .wa-quoted-text {
        font-size: 12.5px;
        color: var(--wa-text-muted);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* ========== BARRA DE RESPOSTA NO COMPOSER ========== */
    .wa-reply-bar {
        display: flex;
        align-items: stretch;
        gap: 8px;
        background: #1a262c;
        border-radius: 6px 6px 0 0;
        padding: 6px 10px;
        margin-bottom: 4px;
        border-bottom: 1px solid var(--wa-border);
    }
    .wa-reply-bar-line {
        width: 3px;
        background: var(--wa-green);
        border-radius: 2px;
        flex-shrink: 0;
    }
    .wa-reply-bar-body { flex: 1; min-width: 0; }
    .wa-reply-bar-autor {
        font-size: 12px;
        font-weight: 700;
        color: var(--wa-green);
        margin-bottom: 1px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .wa-reply-bar-texto {
        font-size: 12.5px;
        color: var(--wa-text-muted);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .wa-reply-bar-cancel {
        background: transparent;
        border: none;
        color: var(--wa-text-muted);
        font-size: 18px;
        cursor: pointer;
        line-height: 1;
        padding: 0 2px;
        flex-shrink: 0;
        align-self: center;
    }
    .wa-reply-bar-cancel:hover { color: var(--wa-text); }
</style>

<div class="wa-wrapper">
    <!-- ===== SIDEBAR ===== -->
    <aside class="wa-sidebar">
        <header class="wa-sidebar-header">
            <div class="wa-sidebar-top">
                <h2 class="wa-sidebar-title">
                    <i class="bi bi-whatsapp me-2" style="color: var(--wa-green)"></i>WhatsApp
                </h2>
                <div class="d-flex gap-2">
                    <button class="wa-icon-btn" title="Nova conversa"><i class="bi bi-chat-left-text"></i></button>
                    <button class="wa-icon-btn" title="Opções"><i class="bi bi-three-dots-vertical"></i></button>
                </div>
            </div>

            <div class="d-flex gap-2 align-items-center">
                <form method="GET" action="{{ route('whatsapp.conversas.index') }}" style="flex:1">
                    <select name="instancia_id" class="wa-instancia-select" onchange="this.form.submit()">
                        @forelse($instanciasUsuario as $inst)
                            <option value="{{ $inst->id }}" @selected(optional($instanciaSelecionada)->id === $inst->id)>
                                {{ $inst->nome }}
                            </option>
                        @empty
                            <option value="">Nenhuma instância</option>
                        @endforelse
                    </select>
                </form>
                @if($instanciaSelecionada)
                <button id="syncNomesBtn"
                    title="Sincronizar nomes dos contatos da agenda"
                    style="background:#2a3942;border:1px solid #3b4a54;color:var(--wa-text-muted);padding:6px 10px;border-radius:8px;cursor:pointer;font-size:13px;white-space:nowrap;flex-shrink:0"
                    data-url="{{ route('whatsapp.instancias.sincronizar-nomes', $instanciaSelecionada) }}"
                    data-token="{{ csrf_token() }}">
                    <i class="bi bi-person-lines-fill"></i>
                </button>
                @endif
            </div>
        </header>

        <div class="wa-search-bar">
            <div class="wa-search-input-group">
                <i class="bi bi-search"></i>
                <input type="text" id="searchInput" placeholder="Pesquisar conversa...">
            </div>
        </div>

        <div class="wa-chats-container" id="waList">
            @forelse($conversas as $conversa)
                @php
                    $contato  = $conversa->contato;
                    // Prioridade: nome salvo > push_name > numero > remote_jid
                    $nome     = $contato?->nome_exibicao ?? 'Contato';
                    $isGrupo  = (bool) ($contato?->is_grupo ?? false);
                    $inicial  = $isGrupo ? '👥' : mb_substr($nome, 0, 1);
                @endphp
                <a href="{{ route('whatsapp.conversas.index', ['instancia_id' => $instanciaSelecionada->id, 'conversa_id' => $conversa->id]) }}"
                   class="wa-chat-tile {{ optional($conversaSelecionada)->id === $conversa->id ? 'active' : '' }}"
                   data-nome="{{ strtolower($nome) }}">
                    <div class="avatar {{ $isGrupo ? 'is-grupo' : '' }}">
                        @if($isGrupo) <i class="bi bi-people-fill"></i> @else {{ $inicial }} @endif
                    </div>
                    <div class="info">
                        <div class="top">
                            <div class="d-flex align-items-center" style="min-width:0;flex:1">
                                <div class="name">{{ $nome }}</div>
                                @if($isGrupo)<span class="wa-group-tag">GRUPO</span>@endif
                            </div>
                            <div class="time">
                                {{ $conversa->ultima_mensagem_em?->format('H:i') ?? $conversa->updated_at?->format('H:i') }}
                            </div>
                        </div>
                        <div class="bottom">
                            <div class="preview">{{ $conversa->ultima_mensagem_preview ?: 'Clique para conversar' }}</div>
                            @if($conversa->nao_lidas > 0)
                                <div class="unread-badge">{{ $conversa->nao_lidas }}</div>
                            @endif
                        </div>
                    </div>
                </a>
            @empty
                <div class="p-5 text-center" style="color: var(--wa-text-muted); opacity: 0.4;">
                    <i class="bi bi-chat-square-dots" style="font-size:40px"></i>
                    <p class="small mt-2">Nenhuma conversa encontrada</p>
                </div>
            @endforelse
        </div>
    </aside>

    <!-- ===== MAIN CHAT ===== -->
    <main class="wa-main">
        @if(!$conversaSelecionada)
            <div class="wa-welcome-screen">
                <i class="bi bi-whatsapp"></i>
                <h3>WhatsApp Web JPC</h3>
                <p>Selecione uma conversa para começar.<br>Mensagens sincronizadas automaticamente.</p>
                <div class="mt-4 small" style="opacity:0.25">
                    <i class="bi bi-lock-fill"></i> Criptografado de ponta a ponta
                </div>
            </div>
        @else
            @php
                $contatoAtual = $conversaSelecionada->contato;
                $isGrupoAtual = (bool) ($contatoAtual?->is_grupo ?? false);
                // Nome priorizado: nome salvo > push_name > numero > JID
                $nomeAtual    = $contatoAtual?->nome_exibicao ?? 'Contato';

                // Pré-carrega contatos da instância para resolver nomes de participants em grupos
                $contatosMap  = [];
                if ($isGrupoAtual && $instanciaSelecionada) {
                    \App\Models\WhatsappContato::where('whatsapp_instancia_id', $instanciaSelecionada->id)
                        ->whereNotNull('remote_jid')
                        ->get(['remote_jid', 'nome', 'push_name', 'numero'])
                        ->each(function ($c) use (&$contatosMap) {
                            $contatosMap[$c->remote_jid] = $c->nome_exibicao;
                        });
                }
            @endphp

            <header class="wa-main-header">
                <div class="wa-contact-wrapper">
                    <div class="avatar {{ $isGrupoAtual ? 'is-grupo' : '' }}">
                        @if($isGrupoAtual)
                            <i class="bi bi-people-fill"></i>
                        @else
                            {{ mb_substr($nomeAtual, 0, 1) }}
                        @endif
                    </div>
                    <div>
                        <div class="name" style="display:flex;align-items:center;gap:8px;">
                            <span id="nomeContatoDisplay">{{ $nomeAtual }}</span>
                            @if($isGrupoAtual)
                                <span class="wa-group-tag">GRUPO</span>
                            @endif
                            <button class="wa-icon-btn" id="renomearContatoBtn"
                                    title="Renomear contato"
                                    style="font-size:13px;padding:2px 4px;color:var(--wa-text-muted);opacity:0.6;">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                        </div>
                        <div class="status">
                            @if($isGrupoAtual)
                                Conversa em grupo
                            @else
                                {{ $contatoAtual?->numero_exibicao ?? '' }}
                            @endif
                        </div>
                    </div>
                </div>

                <div class="wa-header-actions">
                    {{-- Toggle de Assinatura --}}
                    <div class="wa-sig-btn {{ $conversaSelecionada->enviar_identificacao ? 'active' : '' }}"
                         id="sigToggle"
                         title="Com assinatura: envia seu nome no WhatsApp. Sem assinatura: envia sem nome, mas internamente sempre aparece.">
                        <i class="bi bi-{{ $conversaSelecionada->enviar_identificacao ? 'bookmark-check-fill' : 'bookmark' }}"></i>
                        <span>Assinatura <span id="sigStatus">{{ $conversaSelecionada->enviar_identificacao ? 'ON' : 'OFF' }}</span></span>
                    </div>

                    <button class="wa-icon-btn" title="Buscar"><i class="bi bi-search"></i></button>
                    <button class="wa-icon-btn" title="Opções"><i class="bi bi-three-dots-vertical"></i></button>
                </div>
            </header>

            {{-- Banner de aviso para contatos @lid (número privado) --}}
            @if(!$isGrupoAtual && str_contains($contatoAtual?->remote_jid ?? '', '@lid'))
            <div class="wa-lid-banner" id="lidBanner">
                <i class="bi bi-shield-exclamation"></i>
                <span>Número privado — salve este contato na agenda do celular para resolver automaticamente, ou defina o número manualmente.</span>
                <button class="wa-lid-set-btn" id="lidSetNumeroBtn">Definir número</button>
            </div>
            @endif

            <div class="wa-messages-scroll" id="waMessages">
                @php $dataAnterior = null; @endphp

                @forelse($mensagens as $mensagem)
                    @php
                        $dataMsg = $mensagem->created_at->format('Y-m-d');

                        // Nome do remetente interno (atendente que enviou)
                        $nomeInterno = $mensagem->usuario?->name;

                        // Nome do participant em grupos (quem enviou dentro do grupo)
                        $nomeParticipant = null;
                        if ($isGrupoAtual && $mensagem->direcao === 'entrada' && $mensagem->participant) {
                            // Tenta pelo mapa de contatos pré-carregados
                            $nomeParticipant = $contatosMap[$mensagem->participant] ?? null;
                            if (!$nomeParticipant) {
                                // Fallback: mostra o número
                                $nomeParticipant = preg_replace('/[^0-9]/', '', str_replace('@s.whatsapp.net', '', $mensagem->participant));
                            }
                        }
                    @endphp

                    {{-- Separador de data --}}
                    @if($dataMsg !== $dataAnterior)
                        @php $dataAnterior = $dataMsg; @endphp
                        <div class="wa-date-divider">
                            <span>
                                @if($mensagem->created_at->isToday()) Hoje
                                @elseif($mensagem->created_at->isYesterday()) Ontem
                                @else {{ $mensagem->created_at->format('d/m/Y') }}
                                @endif
                            </span>
                        </div>
                    @endif

                    @php
                        // Quoted/reply context
                        $quotedConteudo = null;
                        $quotedAutor    = null;

                        // 1. Resposta enviada pela plataforma: usa relação replyTo
                        if ($mensagem->reply_to_message_id && $mensagem->replyTo) {
                            $ro = $mensagem->replyTo;
                            $quotedConteudo = $ro->apagada_em ? 'Mensagem apagada' : ($ro->conteudo ?: '📎 Mídia');
                            $quotedAutor    = $ro->direcao === 'saida'
                                ? ($ro->usuario?->name ?? 'Você')
                                : ($nomeAtual ?? 'Contato');
                        }

                        // 2. Resposta recebida: extrai contextInfo do payload
                        if (!$quotedConteudo) {
                            $pl_  = is_array($mensagem->payload) ? $mensagem->payload : [];
                            $d_   = $pl_['data'] ?? $pl_;
                            $m_   = $d_['message'] ?? [];
                            $ctx  = $m_['extendedTextMessage']['contextInfo']
                                 ?? $m_['imageMessage']['contextInfo']
                                 ?? $m_['videoMessage']['contextInfo']
                                 ?? $m_['audioMessage']['contextInfo']
                                 ?? null;
                            if ($ctx) {
                                $qm = $ctx['quotedMessage'] ?? null;
                                if ($qm) {
                                    $qText = $qm['conversation']
                                          ?? ($qm['extendedTextMessage']['text'] ?? null)
                                          ?? (isset($qm['imageMessage']) ? '📷 Imagem' : null)
                                          ?? (isset($qm['audioMessage']) ? '🎤 Áudio' : null)
                                          ?? (isset($qm['videoMessage']) ? '🎥 Vídeo' : null)
                                          ?? '...';
                                    $quotedConteudo = $qText;
                                    $qFromMe = $ctx['participant'] ?? null;
                                    $quotedAutor = $qFromMe ? ($nomeAtual ?? 'Contato') : 'Você';
                                }
                            }
                        }
                    @endphp

                    <div class="wa-msg-row {{ $mensagem->direcao === 'saida' ? 'out' : 'in' }}"
                         data-msg-id="{{ $mensagem->id }}"
                         data-direcao="{{ $mensagem->direcao }}"
                         data-conteudo="{{ $mensagem->apagada_em ? '' : e(mb_substr($mensagem->conteudo ?? '', 0, 120)) }}">
                        <div class="wa-msg-bubble">

                            {{-- Botões de ação (hover) --}}
                            @unless($mensagem->apagada_em)
                            <div class="wa-msg-actions">
                                <button class="wa-msg-action-btn reply" title="Responder">
                                    <i class="bi bi-reply-fill"></i>
                                </button>
                                <button class="wa-msg-action-btn delete" title="Apagar">
                                    <i class="bi bi-trash3"></i>
                                </button>
                            </div>
                            @endunless

                            {{-- Citação / quoted --}}
                            @if($quotedConteudo)
                            <div class="wa-quoted-bubble">
                                <div class="wa-quoted-autor">{{ $quotedAutor }}</div>
                                <div class="wa-quoted-text">{{ $quotedConteudo }}</div>
                            </div>
                            @endif

                            {{--
                                REGRA DE EXIBIÇÃO DE NOME:
                                - Mensagem SAÍDA: sempre mostra quem enviou internamente (atendente)
                                - Mensagem ENTRADA em GRUPO: mostra quem no grupo enviou (participant)
                                - Mensagem ENTRADA individual: não mostra nome
                            --}}
                            @if($mensagem->direcao === 'saida' && $nomeInterno)
                                <div class="wa-msg-sender-internal">{{ $nomeInterno }}</div>
                            @elseif($nomeParticipant)
                                <div class="wa-msg-sender-group">{{ $nomeParticipant }}</div>
                            @endif

                            @if($mensagem->apagada_em)
                                {{-- Mensagem apagada --}}
                                <div class="wa-msg-content" style="color:var(--wa-text-muted);font-style:italic;">
                                    <i class="bi bi-slash-circle"></i> Mensagem apagada
                                </div>
                            @else
                                {{-- Mídia / Anexos --}}
                                @foreach($mensagem->anexos as $anexo)
                                    @php
                                        $ext = strtolower(pathinfo($anexo->nome_arquivo ?? '', PATHINFO_EXTENSION));
                                        $midiaUrl = route('whatsapp.conversas.midia', $mensagem);
                                    @endphp
                                    <div class="wa-msg-media">
                                        @if(in_array($ext, ['jpg','jpeg','png','webp','gif']) || $mensagem->tipo === 'imagem')
                                            <img src="{{ $midiaUrl }}" loading="lazy"
                                                 onclick="window.open(this.src)" alt="{{ $anexo->nome_arquivo }}">
                                        @elseif($mensagem->tipo === 'audio' || in_array($ext, ['mp3','wav','ogg','m4a','opus']))
                                            {{-- Checamos o tipo antes da extensão: .webm de áudio gravado no browser
                                                 seria erroneamente renderizado como <video> se verificarmos a extensão primeiro --}}
                                            <audio src="{{ $midiaUrl }}" controls preload="none"
                                                   style="min-width:220px"></audio>
                                        @elseif(in_array($ext, ['mp4','webm','mov','3gp']) || $mensagem->tipo === 'video')
                                            <video src="{{ $midiaUrl }}" controls preload="metadata"></video>
                                        @else
                                            <a href="{{ $midiaUrl }}" target="_blank" class="wa-doc-link">
                                                <i class="bi bi-file-earmark-arrow-down"></i>
                                                {{ $anexo->nome_arquivo }}
                                            </a>
                                        @endif
                                    </div>
                                @endforeach

                                {{-- Conteúdo texto --}}
                                @if($mensagem->conteudo)
                                    <div class="wa-msg-content">{{ $mensagem->conteudo }}</div>
                                @elseif($mensagem->tipo === 'audio')
                                    <div class="wa-msg-content" style="color: var(--wa-text-muted); font-style: italic;">🎤 Mensagem de voz</div>
                                @elseif($mensagem->tipo === 'figurinha')
                                    <div class="wa-msg-content" style="color: var(--wa-text-muted); font-style: italic;">😄 Figurinha</div>
                                @elseif($mensagem->tipo === 'localizacao')
                                    <div class="wa-msg-content" style="color: var(--wa-text-muted); font-style: italic;">📍 Localização</div>
                                @elseif($mensagem->tipo === 'contato')
                                    <div class="wa-msg-content" style="color: var(--wa-text-muted); font-style: italic;">👤 Contato</div>
                                @endif
                            @endif

                            <div class="wa-msg-footer">
                                <span class="wa-msg-time">{{ $mensagem->created_at->format('H:i') }}</span>
                                @if($mensagem->direcao === 'saida')
                                    <span class="wa-msg-status {{ $mensagem->status_envio === 'lida' ? 'read' : '' }}">
                                        @if($mensagem->status_envio === 'pendente')
                                            <i class="bi bi-clock"></i>
                                        @elseif($mensagem->status_envio === 'enviada')
                                            <i class="bi bi-check2"></i>
                                        @elseif(in_array($mensagem->status_envio, ['entregue','lida']))
                                            <i class="bi bi-check2-all"></i>
                                        @elseif($mensagem->status_envio === 'falha')
                                            <i class="bi bi-exclamation-circle text-danger"></i>
                                        @else
                                            <i class="bi bi-check2"></i>
                                        @endif
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5" style="color: var(--wa-text-muted); opacity:0.4;">
                        <i class="bi bi-shield-lock" style="font-size: 40px;"></i>
                        <p class="small mt-2">Mensagens protegidas de ponta a ponta</p>
                    </div>
                @endforelse
            </div>

            <footer class="wa-footer-composer">
                <button class="wa-icon-btn" id="attachBtn" title="Anexar"><i class="bi bi-plus-lg"></i></button>

                <div class="wa-attach-popup" id="attachMenu">
                    <div class="wa-attach-item" onclick="document.getElementById('fileImagem').click()">
                        <div class="wa-attach-circle" style="background: #bf59cf;"><i class="bi bi-image"></i></div>
                        <span>Foto ou Vídeo</span>
                    </div>
                    <div class="wa-attach-item" onclick="document.getElementById('fileDocumento').click()">
                        <div class="wa-attach-circle" style="background: #0078d4;"><i class="bi bi-file-earmark"></i></div>
                        <span>Documento</span>
                    </div>
                </div>

                <input type="file" id="fileImagem" accept="image/*,video/*" hidden>
                <input type="file" id="fileDocumento" hidden>

                <div class="wa-input-wrap" id="inputContainer">
                    <div id="replyBar" class="wa-reply-bar" style="display:none;width:100%;margin-bottom:6px">
                        <div class="wa-reply-bar-line"></div>
                        <div class="wa-reply-bar-body">
                            <div class="wa-reply-bar-autor" id="replyBarAutor"></div>
                            <div class="wa-reply-bar-texto" id="replyBarTexto"></div>
                        </div>
                        <button class="wa-reply-bar-cancel" id="replyBarCancel" title="Cancelar resposta">×</button>
                    </div>
                    <textarea id="mainInput" placeholder="Digite uma mensagem" rows="1"></textarea>
                </div>

                <div class="wa-rec-ui" id="recordingUI">
                    <div class="d-flex align-items-center">
                        <div class="wa-rec-dot"></div>
                        <span id="recTime" style="color: var(--wa-text); font-size: 14px;">0:00</span>
                    </div>
                    <span class="wa-rec-cancel" id="cancelRec">CANCELAR</span>
                </div>

                <button class="wa-icon-btn" id="sendBtn" title="Enviar">
                    <i class="bi bi-mic-fill" id="sendIcon"></i>
                </button>
            </footer>
        @endif
    </main>
</div>

<!-- Modal: Renomear Contato -->
<div class="wa-modal-overlay" id="renomearOverlay">
    <div class="wa-modal-box">
        <div class="wa-modal-header">
            <span><i class="bi bi-pencil-square me-2" style="color:var(--wa-green)"></i>Renomear contato</span>
            <button class="wa-icon-btn" id="fecharRenomear"><i class="bi bi-x-lg"></i></button>
        </div>
        <div class="wa-modal-body">
            <p>Este nome fica salvo na plataforma e tem prioridade sobre o nome do WhatsApp.</p>
            <input type="text" id="renomearInput" class="wa-modal-input"
                   placeholder="Nome do contato" maxlength="100">
        </div>
        <div class="wa-modal-footer">
            <button class="wa-btn-cancel" id="cancelarRenomear">Cancelar</button>
            <button class="wa-btn-primary" id="confirmarRenomear">Salvar</button>
        </div>
    </div>
</div>

<!-- Modal: Definir Número (@lid) -->
<div class="wa-modal-overlay" id="lidNumeroOverlay">
    <div class="wa-modal-box">
        <div class="wa-modal-header">
            <span><i class="bi bi-telephone-plus me-2" style="color:var(--wa-green)"></i>Definir número do contato</span>
            <button class="wa-icon-btn" id="fecharLidNumero"><i class="bi bi-x-lg"></i></button>
        </div>
        <div class="wa-modal-body">
            <p>Digite o número completo com código do país e DDD (ex: 5511999998888).<br>
               <span style="color:var(--wa-text-muted);font-size:12px;">Este contato usa número privado (@lid). Após definir, você poderá enviar mensagens normalmente.</span>
            </p>
            <input type="text" id="lidNumeroInput" class="wa-modal-input"
                   placeholder="5511999998888" maxlength="20" inputmode="numeric">
        </div>
        <div class="wa-modal-footer">
            <button class="wa-btn-cancel" id="cancelarLidNumero">Cancelar</button>
            <button class="wa-btn-primary" id="confirmarLidNumero">Salvar número</button>
        </div>
    </div>
</div>

<!-- Modal: Nova Conversa -->
<div class="wa-modal-overlay" id="novaConversaOverlay">
    <div class="wa-modal-box">
        <div class="wa-modal-header">
            <span><i class="bi bi-chat-left-text me-2" style="color:var(--wa-green)"></i>Nova Conversa</span>
            <button class="wa-icon-btn" id="fecharNovaConversa"><i class="bi bi-x-lg"></i></button>
        </div>
        <div class="wa-modal-body">
            <p>Digite o número com código do país e DDD (ex: 5511999998888)</p>
            <input type="text" id="novoNumeroInput" class="wa-modal-input"
                   placeholder="5511999998888" maxlength="20" inputmode="numeric">
        </div>
        <div class="wa-modal-footer">
            <button class="wa-btn-cancel" id="cancelarNovaConversa">Cancelar</button>
            <button class="wa-btn-primary" id="confirmarNovaConversa">Abrir Conversa</button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // ===== HELPER: parse JSON seguro (evita crash quando servidor retorna HTML) =====
    async function parseJsonSafe(response) {
        try {
            return await response.json();
        } catch {
            return null;
        }
    }

    // Interpreta a resposta de erro e retorna mensagem amigável
    async function mensagemDeErro(response, fallback) {
        if (response.status === 419) {
            return 'Sessão expirada. Recarregue a página (F5) e tente novamente.';
        }
        if (response.status === 401 || response.status === 403) {
            return 'Você não tem permissão para esta ação.';
        }
        const data = await parseJsonSafe(response);
        // data.error pode ser string vazia — trata como ausente apenas se for undefined/null
        if (data && data.error != null) return data.error || 'Erro interno no servidor.';
        if (data && data.message) return data.message;
        return fallback + ` (HTTP ${response.status})`;
    }

    // ===== SINCRONIZAR NOMES =====
    const syncNomesBtn = document.getElementById('syncNomesBtn');
    if (syncNomesBtn) {
        syncNomesBtn.addEventListener('click', async function () {
            const url   = this.dataset.url;
            const token = this.dataset.token;
            this.disabled = true;
            this.innerHTML = '<i class="bi bi-arrow-repeat" style="animation:spin 1s linear infinite"></i>';

            const fd = new FormData();
            fd.append('_token', token);
            try {
                const resp = await fetch(url, { method: 'POST', body: fd, headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                const data = await parseJsonSafe(resp);
                if (resp.ok && data?.success) {
                    this.innerHTML = '<i class="bi bi-check2" style="color:var(--wa-green)"></i>';
                    this.title = `${data.atualizados} nomes atualizados`;
                    setTimeout(() => {
                        this.innerHTML = '<i class="bi bi-person-lines-fill"></i>';
                        this.title = 'Sincronizar nomes dos contatos da agenda';
                    }, 3000);
                } else {
                    this.innerHTML = '<i class="bi bi-person-lines-fill"></i>';
                    alert('⚠️ ' + (data?.error || 'Falha ao sincronizar nomes.'));
                }
            } catch (err) {
                this.innerHTML = '<i class="bi bi-person-lines-fill"></i>';
            } finally {
                this.disabled = false;
            }
        });
    }

    // ===== MODAL RENOMEAR CONTATO =====
    const renomearOverlay  = document.getElementById('renomearOverlay');
    const renomearInput    = document.getElementById('renomearInput');
    const confirmarRenomear = document.getElementById('confirmarRenomear');

    document.getElementById('renomearContatoBtn')?.addEventListener('click', () => {
        if (!renomearOverlay) return;
        renomearInput.value = document.getElementById('nomeContatoDisplay')?.textContent?.trim() ?? '';
        renomearOverlay.classList.add('active');
        renomearInput.focus();
        renomearInput.select();
    });
    document.getElementById('fecharRenomear')?.addEventListener('click', () => renomearOverlay?.classList.remove('active'));
    document.getElementById('cancelarRenomear')?.addEventListener('click', () => renomearOverlay?.classList.remove('active'));
    renomearOverlay?.addEventListener('click', e => { if (e.target === renomearOverlay) renomearOverlay.classList.remove('active'); });
    renomearInput?.addEventListener('keydown', e => { if (e.key === 'Enter') confirmarRenomear?.click(); });

    confirmarRenomear?.addEventListener('click', async function () {
        const nome = renomearInput.value.trim();
        if (!nome) {
            renomearInput.style.borderColor = '#f44336';
            setTimeout(() => renomearInput.style.borderColor = '', 1500);
            return;
        }

        this.disabled = true;
        this.textContent = 'Salvando...';

        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('_method', 'PATCH');
        formData.append('nome', nome);

        try {
            const resp = await fetch(
                "{{ $conversaSelecionada ? route('whatsapp.conversas.renomear', $conversaSelecionada) : '#' }}",
                { method: 'POST', body: formData, headers: { 'X-Requested-With': 'XMLHttpRequest' } }
            );

            if (!resp.ok) {
                const d = await parseJsonSafe(resp);
                throw new Error(d?.error || 'Erro ao renomear');
            }

            const data = await resp.json();
            // Atualiza o nome exibido no header sem recarregar
            const display = document.getElementById('nomeContatoDisplay');
            if (display) display.textContent = data.nome;
            renomearOverlay.classList.remove('active');
        } catch (err) {
            alert('⚠️ ' + err.message);
            this.disabled = false;
            this.textContent = 'Salvar';
        }
    });

    // ===== MODAL DEFINIR NÚMERO (@lid) =====
    const lidNumeroOverlay  = document.getElementById('lidNumeroOverlay');
    const lidNumeroInput    = document.getElementById('lidNumeroInput');
    const confirmarLidNumero = document.getElementById('confirmarLidNumero');

    document.getElementById('lidSetNumeroBtn')?.addEventListener('click', () => {
        if (lidNumeroOverlay) {
            lidNumeroInput.value = '';
            lidNumeroOverlay.classList.add('active');
            lidNumeroInput.focus();
        }
    });
    document.getElementById('fecharLidNumero')?.addEventListener('click', () => lidNumeroOverlay?.classList.remove('active'));
    document.getElementById('cancelarLidNumero')?.addEventListener('click', () => lidNumeroOverlay?.classList.remove('active'));
    lidNumeroOverlay?.addEventListener('click', e => { if (e.target === lidNumeroOverlay) lidNumeroOverlay.classList.remove('active'); });

    lidNumeroInput?.addEventListener('input', function () { this.value = this.value.replace(/\D/g, ''); });
    lidNumeroInput?.addEventListener('keydown', e => { if (e.key === 'Enter') confirmarLidNumero?.click(); });

    confirmarLidNumero?.addEventListener('click', async function () {
        const numero = lidNumeroInput.value.replace(/\D/g, '');
        if (!numero || numero.length < 10) {
            lidNumeroInput.style.borderColor = '#f44336';
            setTimeout(() => lidNumeroInput.style.borderColor = '', 1500);
            return;
        }

        this.disabled = true;
        this.textContent = 'Salvando...';

        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('_method', 'PATCH');
        formData.append('numero', numero);

        try {
            const resp = await fetch(
                "{{ $conversaSelecionada ? route('whatsapp.conversas.definir-numero', $conversaSelecionada) : '#' }}",
                { method: 'POST', body: formData, headers: { 'X-Requested-With': 'XMLHttpRequest' } }
            );

            if (!resp.ok) {
                const d = await parseJsonSafe(resp);
                throw new Error(d?.error || 'Erro ao salvar número');
            }

            // Recarrega para mostrar o histórico completo após a fusão de contatos
            lidNumeroOverlay.classList.remove('active');
            window.location.reload();
        } catch (err) {
            alert('⚠️ ' + err.message);
            this.disabled = false;
            this.textContent = 'Salvar número';
        }
    });

    // ===== MODAL NOVA CONVERSA =====
    const novaConversaOverlay  = document.getElementById('novaConversaOverlay');
    const novoNumeroInput      = document.getElementById('novoNumeroInput');
    const confirmarNovaConversa = document.getElementById('confirmarNovaConversa');

    function abrirModalNovaConversa() {
        novaConversaOverlay.classList.add('active');
        novoNumeroInput.value = '';
        novoNumeroInput.focus();
    }

    function fecharModalNovaConversa() {
        novaConversaOverlay.classList.remove('active');
    }

    document.querySelector('[title="Nova conversa"]')?.addEventListener('click', abrirModalNovaConversa);
    document.getElementById('fecharNovaConversa')?.addEventListener('click', fecharModalNovaConversa);
    document.getElementById('cancelarNovaConversa')?.addEventListener('click', fecharModalNovaConversa);
    novaConversaOverlay?.addEventListener('click', function (e) {
        if (e.target === novaConversaOverlay) fecharModalNovaConversa();
    });

    novoNumeroInput?.addEventListener('input', function () {
        this.value = this.value.replace(/\D/g, '');
    });

    novoNumeroInput?.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') confirmarNovaConversa?.click();
    });

    confirmarNovaConversa?.addEventListener('click', async function () {
        const numero = novoNumeroInput.value.replace(/\D/g, '');
        if (!numero || numero.length < 10) {
            novoNumeroInput.focus();
            novoNumeroInput.style.borderColor = '#f44336';
            setTimeout(() => novoNumeroInput.style.borderColor = '', 1500);
            return;
        }

        this.disabled = true;
        this.textContent = 'Aguarde...';

        const instanciaId = '{{ $instanciaSelecionada?->id ?? "" }}';
        if (!instanciaId) {
            alert('Selecione uma instância primeiro.');
            this.disabled = false;
            this.textContent = 'Abrir Conversa';
            return;
        }

        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('instancia_id', instanciaId);
        formData.append('numero', numero);

        try {
            const response = await fetch('{{ route("whatsapp.conversas.nova") }}', {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
            });

            if (!response.ok) {
                const msg = await mensagemDeErro(response, 'Erro ao abrir conversa');
                throw new Error(msg);
            }

            const data = await response.json();
            fecharModalNovaConversa();
            window.location.href = '{{ route("whatsapp.conversas.index") }}'
                + '?instancia_id=' + data.instancia_id
                + '&conversa_id=' + data.conversa_id;
        } catch (err) {
            alert('⚠️ ' + err.message);
            this.disabled = false;
            this.textContent = 'Abrir Conversa';
        }
    });

    // ===== BUSCA NA SIDEBAR =====
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            const q = this.value.toLowerCase().trim();
            document.querySelectorAll('.wa-chat-tile').forEach(tile => {
                const nome = tile.dataset.nome || '';
                tile.style.display = nome.includes(q) ? 'flex' : 'none';
            });
        });
    }

    // ===== TOGGLE DE ASSINATURA =====
    const sigToggle = document.getElementById('sigToggle');
    if (sigToggle) {
        sigToggle.addEventListener('click', async function () {
            const isActive = this.classList.contains('active');
            const newValue = !isActive;

            const formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('_method', 'PATCH');
            formData.append('enviar_identificacao', newValue ? '1' : '0');

            try {
                const response = await fetch(
                    "{{ route('whatsapp.conversas.identificacao', $conversaSelecionada ?? 0) }}",
                    { method: 'POST', body: formData, headers: { 'X-Requested-With': 'XMLHttpRequest' } }
                );
                if (response.ok) {
                    const data = await response.json();
                    if (data.success) {
                        this.classList.toggle('active', data.enviar_identificacao);
                        this.querySelector('i').className =
                            `bi bi-${data.enviar_identificacao ? 'bookmark-check-fill' : 'bookmark'}`;
                        document.getElementById('sigStatus').textContent =
                            data.enviar_identificacao ? 'ON' : 'OFF';
                    }
                }
            } catch (err) {
                console.error('Erro ao alternar assinatura:', err);
            }
        });
    }

    // ===== RESPONDER MENSAGEM =====
    let replyToId   = null;
    const replyBar  = document.getElementById('replyBar');

    function setReply(msgId, autor, texto) {
        replyToId = msgId;
        document.getElementById('replyBarAutor').textContent = autor;
        document.getElementById('replyBarTexto').textContent =
            texto.length > 100 ? texto.slice(0, 100) + '…' : texto;
        if (replyBar) replyBar.style.display = 'flex';
        document.getElementById('mainInput')?.focus();
    }

    function clearReply() {
        replyToId = null;
        if (replyBar) replyBar.style.display = 'none';
    }

    document.getElementById('replyBarCancel')?.addEventListener('click', clearReply);

    // ===== APAGAR MENSAGEM =====
    async function apagarMensagem(msgId, rowEl) {
        const direcao  = rowEl.dataset.direcao;
        const isOutgoing = direcao === 'saida';
        const confirmMsg = isOutgoing
            ? 'Apagar para todos?\nA mensagem será removida da conversa no WhatsApp e na plataforma.'
            : 'Apagar esta mensagem da plataforma?';
        if (!confirm(confirmMsg)) return;

        const fd = new FormData();
        fd.append('_token', '{{ csrf_token() }}');
        fd.append('_method', 'DELETE');

        try {
            const resp = await fetch(
                '{{ url("whatsapp/conversas/mensagens") }}/' + msgId,
                { method: 'POST', body: fd, headers: { 'X-Requested-With': 'XMLHttpRequest' } }
            );
            if (!resp.ok) {
                const msg = await mensagemDeErro(resp, 'Erro ao apagar');
                throw new Error(msg);
            }
            const data = await parseJsonSafe(resp);
            if (data?.erroWhatsapp) {
                console.warn('Apagado na plataforma, mas falhou no WhatsApp:', data.erroWhatsapp);
            }
            // Atualiza visualmente sem esperar pelo polling
            const bubble = rowEl.querySelector('.wa-msg-bubble');
            if (bubble) {
                const actionsEl = bubble.querySelector('.wa-msg-actions');
                const mediaEls  = bubble.querySelectorAll('.wa-msg-media, .wa-msg-sender-internal, .wa-msg-sender-group, .wa-quoted-bubble');
                let   contentEl = bubble.querySelector('.wa-msg-content');
                if (actionsEl) actionsEl.remove();
                mediaEls.forEach(el => el.remove());
                if (!contentEl) {
                    contentEl = document.createElement('div');
                    contentEl.className = 'wa-msg-content';
                    bubble.insertBefore(contentEl, bubble.querySelector('.wa-msg-footer'));
                }
                contentEl.innerHTML = '<i class="bi bi-slash-circle"></i> Mensagem apagada';
                contentEl.style.cssText = 'color:var(--wa-text-muted);font-style:italic';
            }
        } catch (err) {
            alert('⚠️ ' + err.message);
        }
    }

    // ===== EVENT DELEGATION — ações por mensagem =====
    document.getElementById('waMessages')?.addEventListener('click', function (e) {
        const replyBtn  = e.target.closest('.wa-msg-action-btn.reply');
        const deleteBtn = e.target.closest('.wa-msg-action-btn.delete');

        if (replyBtn) {
            const row     = replyBtn.closest('[data-msg-id]');
            if (!row) return;
            const msgId   = row.dataset.msgId;
            const direcao = row.dataset.direcao;
            const texto   = row.dataset.conteudo || '…';
            const autor   = direcao === 'saida'
                ? '{{ auth()->user()->name }}'
                : '{{ $nomeAtual ?? "Contato" }}';
            setReply(msgId, autor, texto);
        }

        if (deleteBtn) {
            const row = deleteBtn.closest('[data-msg-id]');
            if (!row) return;
            apagarMensagem(row.dataset.msgId, row);
        }
    });

    // ===== HELPERS =====
    function scrollBottom(force = false) {
        const box = document.getElementById('waMessages');
        if (!box) return;
        const atBottom = box.scrollHeight - box.scrollTop - box.clientHeight < 120;
        if (atBottom || force) box.scrollTop = box.scrollHeight;
    }

    // ===== TEXTAREA AUTO-RESIZE =====
    const mainInput = document.getElementById('mainInput');
    const sendBtn   = document.getElementById('sendBtn');
    const sendIcon  = document.getElementById('sendIcon');
    const attachBtn = document.getElementById('attachBtn');
    const attachMenu = document.getElementById('attachMenu');

    if (mainInput) {
        mainInput.addEventListener('input', function () {
            this.style.height = '22px';
            this.style.height = Math.min(this.scrollHeight, 140) + 'px';
            sendIcon.className = this.value.trim() ? 'bi bi-send-fill' : 'bi bi-mic-fill';
            sendIcon.style.color = this.value.trim() ? 'var(--wa-green)' : '';
        });

        mainInput.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                enviarTexto();
            }
        });
    }

    // ===== ANEXOS =====
    if (attachBtn) {
        attachBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            attachMenu.classList.toggle('active');
        });
    }
    document.addEventListener('click', function () {
        if (attachMenu) attachMenu.classList.remove('active');
    });

    document.getElementById('fileImagem')?.addEventListener('change', function () {
        if (this.files[0]) { enviarArquivo(this.files[0]); this.value = ''; }
    });
    document.getElementById('fileDocumento')?.addEventListener('change', function () {
        if (this.files[0]) { enviarArquivo(this.files[0]); this.value = ''; }
    });

    // ===== ENVIAR TEXTO =====
    async function enviarTexto() {
        if (!mainInput) return;
        const text = mainInput.value.trim();
        if (!text) return;

        const originalText = text;
        const sendingReplyId = replyToId;
        mainInput.value = '';
        mainInput.style.height = '22px';
        sendIcon.className = 'bi bi-mic-fill';
        sendIcon.style.color = '';
        clearReply();

        // Append otimista (aparece na hora)
        const msgsBox = document.getElementById('waMessages');
        const now  = new Date();
        const time = now.getHours() + ':' + now.getMinutes().toString().padStart(2, '0');
        const tempId   = 'temp_' + Date.now();
        const userName = '{{ auth()->user()->name }}';

        const htmlMsg = `
            <div class="wa-msg-row out wa-msg-new" id="${tempId}" style="opacity:0.7">
                <div class="wa-msg-bubble">
                    <div class="wa-msg-sender-internal">${userName}</div>
                    <div class="wa-msg-content">${escapeHtml(text)}</div>
                    <div class="wa-msg-footer">
                        <span class="wa-msg-time">${time}</span>
                        <span class="wa-msg-status"><i class="bi bi-clock"></i></span>
                    </div>
                </div>
            </div>`;
        if (msgsBox) {
            msgsBox.insertAdjacentHTML('beforeend', htmlMsg);
            scrollBottom(true);
        }

        const formData = new FormData();
        formData.append('mensagem', text);
        formData.append('_token', '{{ csrf_token() }}');
        if (sendingReplyId) formData.append('reply_message_id', sendingReplyId);

        try {
            const response = await fetch(
                "{{ route('whatsapp.conversas.enviar-texto', $conversaSelecionada ?? 0) }}",
                { method: 'POST', body: formData, headers: { 'X-Requested-With': 'XMLHttpRequest' } }
            );

            if (!response.ok) {
                const msg = await mensagemDeErro(response, 'Erro ao enviar mensagem');
                throw new Error(msg);
            }

            await atualizarMensagens(true);
            document.getElementById(tempId)?.remove();
        } catch (err) {
            document.getElementById(tempId)?.remove();
            console.error('Erro ao enviar texto:', err);
            alert('⚠️ ' + err.message);
            mainInput.value = originalText;
            sendIcon.className = 'bi bi-send-fill';
            sendIcon.style.color = 'var(--wa-green)';
        }
    }

    function escapeHtml(text) {
        return text
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/\n/g, '<br>');
    }

    // ===== ENVIAR ARQUIVO =====
    async function enviarArquivo(file, opts = {}) {
        const formData = new FormData();
        formData.append('arquivo', file);
        formData.append('_token', '{{ csrf_token() }}');
        if (opts.ptt) formData.append('ptt', '1');

        // Feedback visual
        const msgsBox = document.getElementById('waMessages');
        const tempId = 'temp_file_' + Date.now();
        const htmlMsg = `
            <div class="wa-msg-row out" id="${tempId}" style="opacity:0.5">
                <div class="wa-msg-bubble">
                    <div class="wa-msg-content" style="color:var(--wa-text-muted);font-style:italic;">
                        <i class="bi bi-cloud-upload"></i> Enviando ${escapeHtml(file.name)}...
                    </div>
                </div>
            </div>`;
        if (msgsBox) {
            msgsBox.insertAdjacentHTML('beforeend', htmlMsg);
            scrollBottom(true);
        }

        try {
            const response = await fetch(
                "{{ route('whatsapp.conversas.enviar-midia', $conversaSelecionada ?? 0) }}",
                { method: 'POST', body: formData, headers: { 'X-Requested-With': 'XMLHttpRequest' } }
            );

            if (!response.ok) {
                const msg = await mensagemDeErro(response, 'Erro ao enviar arquivo');
                throw new Error(msg);
            }

            await atualizarMensagens(true);
            document.getElementById(tempId)?.remove();
        } catch (err) {
            document.getElementById(tempId)?.remove();
            console.error('Erro ao enviar arquivo:', err);
            alert('⚠️ ' + err.message);
        }
    }

    // ===== BOTÃO ENVIAR / MIC =====
    if (sendBtn) {
        sendBtn.addEventListener('click', function () {
            if (mainInput && mainInput.value.trim()) {
                enviarTexto();
            } else if (!mediaRecorder || mediaRecorder.state === 'inactive') {
                startRecording();
            } else {
                stopRecording();
            }
        });
    }

    // ===== CONVERSÃO WebM → WAV (compatibilidade WhatsApp sem FFmpeg no servidor) =====
    // Chrome grava em WebM/Opus. Baileys/Evolution API não entrega WebM sem FFmpeg.
    // WAV (PCM) não precisa de conversão no servidor e é aceito pelo WhatsApp.
    async function audioParaWav(blob) {
        const arrayBuffer = await blob.arrayBuffer();
        const audioCtx = new AudioContext();
        let audioBuffer;
        try {
            audioBuffer = await audioCtx.decodeAudioData(arrayBuffer);
        } finally {
            await audioCtx.close();
        }

        const sampleRate = audioBuffer.sampleRate;
        const length = audioBuffer.length;

        // Mixdown para mono (voz não precisa de stereo; reduz tamanho pela metade)
        const mono = new Float32Array(length);
        for (let ch = 0; ch < audioBuffer.numberOfChannels; ch++) {
            const src = audioBuffer.getChannelData(ch);
            for (let i = 0; i < length; i++) mono[i] += src[i];
        }
        if (audioBuffer.numberOfChannels > 1) {
            for (let i = 0; i < length; i++) mono[i] /= audioBuffer.numberOfChannels;
        }

        const byteCount = length * 2; // PCM 16-bit = 2 bytes/amostra
        const buf = new ArrayBuffer(44 + byteCount);
        const v = new DataView(buf);
        const w = (off, s) => { for (let i = 0; i < s.length; i++) v.setUint8(off + i, s.charCodeAt(i)); };

        w(0, 'RIFF'); v.setUint32(4, 36 + byteCount, true);
        w(8, 'WAVE'); w(12, 'fmt ');
        v.setUint32(16, 16, true);
        v.setUint16(20, 1, true);              // PCM
        v.setUint16(22, 1, true);              // mono
        v.setUint32(24, sampleRate, true);
        v.setUint32(28, sampleRate * 2, true); // byte rate
        v.setUint16(32, 2, true);              // block align
        v.setUint16(34, 16, true);             // 16-bit
        w(36, 'data'); v.setUint32(40, byteCount, true);

        let off = 44;
        for (let i = 0; i < length; i++) {
            const s = Math.max(-1, Math.min(1, mono[i]));
            v.setInt16(off, s < 0 ? s * 0x8000 : s * 0x7FFF, true);
            off += 2;
        }

        return new Blob([buf], { type: 'audio/wav' });
    }

    // ===== GRAVAÇÃO DE ÁUDIO =====
    let mediaRecorder, audioChunks = [], recordInterval, secondsElapsed = 0;
    const recordingUI  = document.getElementById('recordingUI');
    const inputContainer = document.getElementById('inputContainer');
    const recTime      = document.getElementById('recTime');
    const cancelRec    = document.getElementById('cancelRec');

    async function startRecording() {
        try {
            const stream = await navigator.mediaDevices.getUserMedia({ audio: true });

            // Preferência: OGG/Opus (Firefox, funciona como PTT), depois WebM (Chrome)
            const mimeType = ['audio/ogg;codecs=opus', 'audio/ogg', 'audio/webm;codecs=opus', 'audio/webm']
                .find(m => MediaRecorder.isTypeSupported(m)) || '';

            mediaRecorder = new MediaRecorder(stream, mimeType ? { mimeType } : {});
            audioChunks = [];
            mediaRecorder.ondataavailable = e => audioChunks.push(e.data);
            mediaRecorder.onstop = async () => {
                const actualMime = mediaRecorder.mimeType || mimeType || 'audio/ogg';
                const rawBlob = new Blob(audioChunks, { type: actualMime });

                if (actualMime.includes('ogg')) {
                    // OGG/Opus (Firefox): envia como nota de voz PTT
                    enviarArquivo(new File([rawBlob], 'audio_' + Date.now() + '.ogg', { type: actualMime }), { ptt: true });
                } else {
                    // WebM (Chrome): converte para WAV — WhatsApp não entrega WebM sem FFmpeg no servidor
                    try {
                        const wavBlob = await audioParaWav(rawBlob);
                        enviarArquivo(new File([wavBlob], 'audio_' + Date.now() + '.wav', { type: 'audio/wav' }));
                    } catch (convErr) {
                        console.warn('Conversão WAV falhou, enviando WebM:', convErr);
                        enviarArquivo(new File([rawBlob], 'audio_' + Date.now() + '.webm', { type: actualMime }));
                    }
                }
                stream.getTracks().forEach(t => t.stop());
            };
            mediaRecorder.start();
            if (inputContainer) inputContainer.style.display = 'none';
            if (recordingUI) recordingUI.classList.add('active');
            secondsElapsed = 0;
            if (recTime) recTime.textContent = '0:00';
            recordInterval = setInterval(() => {
                secondsElapsed++;
                if (recTime) recTime.textContent =
                    Math.floor(secondsElapsed / 60) + ':' + (secondsElapsed % 60).toString().padStart(2, '0');
            }, 1000);
            if (sendIcon) { sendIcon.className = 'bi bi-stop-fill'; sendIcon.style.color = '#f44336'; }
        } catch (err) {
            alert('Permita o acesso ao microfone para gravar.');
        }
    }

    function stopRecording() {
        if (mediaRecorder && mediaRecorder.state !== 'inactive') {
            mediaRecorder.stop();
            clearInterval(recordInterval);
            if (recordingUI) recordingUI.classList.remove('active');
            if (inputContainer) inputContainer.style.display = 'flex';
            if (sendIcon) { sendIcon.className = 'bi bi-mic-fill'; sendIcon.style.color = ''; }
        }
    }

    if (cancelRec) {
        cancelRec.addEventListener('click', function () {
            if (mediaRecorder && mediaRecorder.state !== 'inactive') {
                mediaRecorder.onstop = null; // cancela sem enviar
                mediaRecorder.stop();
                clearInterval(recordInterval);
                if (recordingUI) recordingUI.classList.remove('active');
                if (inputContainer) inputContainer.style.display = 'flex';
                if (sendIcon) { sendIcon.className = 'bi bi-mic-fill'; sendIcon.style.color = ''; }
            }
        });
    }

    // ===== POLLING AUTOMÁTICO (mensagens + lista) =====
    let atualizando = false;
    let ultimoHash  = '';

    async function atualizarMensagens(force = false) {
        if (atualizando && !force) return;
        atualizando = true;

        try {
            const url = new URL(window.location.href);
            url.searchParams.set('_ajax_whatsapp', '1');

            const resp = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            if (!resp.ok) return;

            const html = await resp.text();

            // Hash simples para evitar re-render desnecessário
            const hash = html.length + '_' + html.slice(-200);
            if (!force && hash === ultimoHash) return;
            ultimoHash = hash;

            const doc = new DOMParser().parseFromString(html, 'text/html');

            // Atualiza área de mensagens
            const newMsgs  = doc.getElementById('waMessages');
            const currMsgs = document.getElementById('waMessages');
            if (newMsgs && currMsgs && newMsgs.innerHTML !== currMsgs.innerHTML) {
                const atBottom = currMsgs.scrollHeight - currMsgs.scrollTop - currMsgs.clientHeight < 120;
                currMsgs.innerHTML = newMsgs.innerHTML;
                if (atBottom || force) scrollBottom(true);
            }

            // Atualiza lista de conversas
            const newList  = doc.getElementById('waList');
            const currList = document.getElementById('waList');
            if (newList && currList && newList.innerHTML !== currList.innerHTML) {
                currList.innerHTML = newList.innerHTML;
            }
        } catch (err) {
            console.error('Erro no polling:', err);
        } finally {
            atualizando = false;
        }
    }

    // Rola para o final ao carregar
    scrollBottom(true);

    // Polling a cada 1 segundo
    setInterval(atualizarMensagens, 1000);
});
</script>
@endsection