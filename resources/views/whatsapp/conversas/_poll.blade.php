{{--
    View parcial usada pelo polling AJAX (?_ajax_whatsapp=1).
    Retorna APENAS os dois divs que o JavaScript precisa atualizar,
    sem carregar o layout completo da aplicação. Isso reduz
    significativamente o tamanho da resposta e o tempo de render.
--}}

{{-- ===== LISTA DE CONVERSAS ===== --}}
<div class="wa-chats-container" id="waList">
    @forelse($conversas as $conversa)
        @php
            $contato  = $conversa->contato;
            $nome     = $contato?->nome_exibicao ?? 'Contato';
            $isGrupo  = (bool) ($contato?->is_grupo ?? false);
            $inicial  = $isGrupo ? '👥' : mb_substr($nome, 0, 1);
        @endphp
        <a href="{{ route('whatsapp.conversas.index', ['instancia_id' => $instanciaSelecionada->id, 'conversa_id' => $conversa->id]) }}"
           class="wa-chat-tile {{ optional($conversaSelecionada)->id === $conversa->id ? 'active' : '' }} {{ $conversa->fixado ? 'fixado' : '' }}"
           data-nome="{{ strtolower($nome) }}">
            <div class="avatar {{ $isGrupo ? 'is-grupo' : '' }}">
                @if($isGrupo)
                    <i class="bi bi-people-fill"></i>
                @else
                    {{ $inicial }}
                @endif
                @if($contato?->foto_url)
                    <img src="{{ $contato->foto_url }}" alt=""
                         style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover"
                         onerror="this.style.display='none'">
                @endif
            </div>
            <div class="info">
                <div class="top">
                    <div class="d-flex align-items-center" style="min-width:0;flex:1">
                        <div class="name">{{ $nome }}</div>
                        @if($isGrupo)<span class="wa-group-tag">GRUPO</span>@endif
                        @if($conversa->fixado)<i class="bi bi-pin-fill wa-pin-icon" title="Fixado"></i>@endif
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
            <button class="wa-pin-btn"
                title="{{ $conversa->fixado ? 'Desafixar' : 'Fixar' }}"
                data-url="{{ route('whatsapp.conversas.fixar', $conversa) }}"
                data-token="{{ csrf_token() }}"
                onclick="event.preventDefault(); fixarConversa(this)">
                <i class="bi {{ $conversa->fixado ? 'bi-pin-fill' : 'bi-pin' }}"></i>
            </button>
        </a>
    @empty
        <div class="p-5 text-center" style="color: var(--wa-text-muted); opacity: 0.4;">
            <i class="bi bi-chat-square-dots" style="font-size:40px"></i>
            <p class="small mt-2">Nenhuma conversa encontrada</p>
        </div>
    @endforelse
</div>

{{-- ===== ÁREA DE MENSAGENS ===== --}}
@if($conversaSelecionada)
    @php
        $contatoAtual = $conversaSelecionada->contato;
        $isGrupoAtual = (bool) ($contatoAtual?->is_grupo ?? false);
        $nomeAtual    = $contatoAtual?->nome_exibicao ?? 'Contato';

        $contatosMap   = [];
        $contatosIdMap = []; // jid → contato id (para renomear)
        if ($isGrupoAtual && $instanciaSelecionada) {
            \App\Models\WhatsappContato::where('whatsapp_instancia_id', $instanciaSelecionada->id)
                ->whereNotNull('remote_jid')
                ->get(['id', 'remote_jid', 'lid_jid', 'nome', 'push_name', 'numero'])
                ->each(function ($c) use (&$contatosMap, &$contatosIdMap) {
                    $contatosMap[$c->remote_jid]   = $c->nome_exibicao;
                    $contatosIdMap[$c->remote_jid] = $c->id;
                    if ($c->lid_jid) {
                        $contatosMap[$c->lid_jid]   = $c->nome_exibicao;
                        $contatosIdMap[$c->lid_jid] = $c->id;
                    }
                });
        }
    @endphp

    <div class="wa-messages-scroll" id="waMessages">
        @php $dataAnterior = null; @endphp

        @forelse($mensagens as $mensagem)
            @php
                $dataMsg = $mensagem->created_at->format('Y-m-d');
                $nomeInterno = $mensagem->usuario?->name;

                $nomeParticipant  = null;
                $partAltGlobal    = null; // @s.whatsapp.net alternativo do participant
                if ($isGrupoAtual && $mensagem->direcao === 'entrada' && $mensagem->participant) {
                    $nomeParticipant = $contatosMap[$mensagem->participant] ?? null;
                    if (!$nomeParticipant) {
                        $pl_alt        = is_array($mensagem->payload) ? $mensagem->payload : [];
                        $partAltGlobal = $pl_alt['data']['key']['participantAlt']
                                      ?? $pl_alt['data']['participantAlt']
                                      ?? null;
                        if ($partAltGlobal) {
                            $nomeParticipant = $contatosMap[$partAltGlobal] ?? null;
                        }
                    }
                    if (!$nomeParticipant) {
                        $pl_          = is_array($mensagem->payload) ? $mensagem->payload : [];
                        $pushFallback = $pl_['data']['pushName'] ?? null;
                        $nomeParticipant = $pushFallback
                            ?: preg_replace('/[^0-9]/', '', str_replace(['@s.whatsapp.net','@lid'], '', $mensagem->participant));
                    }
                }
            @endphp

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
                $quotedConteudo = null;
                $quotedAutor    = null;

                if ($mensagem->reply_to_message_id && $mensagem->replyTo) {
                    $ro = $mensagem->replyTo;
                    $quotedConteudo = $ro->apagada_em ? 'Mensagem apagada' : ($ro->conteudo ?: '📎 Mídia');
                    $quotedAutor    = $ro->direcao === 'saida'
                        ? ($ro->usuario?->name ?? 'Você')
                        : ($nomeAtual ?? 'Contato');
                }

                if (!$quotedConteudo) {
                    $pl_  = is_array($mensagem->payload) ? $mensagem->payload : [];
                    $d_   = $pl_['data'] ?? $pl_;
                    $m_   = $d_['message'] ?? [];
                    // Evolution API coloca contextInfo diretamente em $data, não dentro do tipo de mensagem
                    $ctx  = $d_['contextInfo']
                         ?? $m_['extendedTextMessage']['contextInfo']
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

                    @unless($mensagem->apagada_em)
                    <div class="wa-msg-actions">
                        <button class="wa-msg-action-btn react" title="Reagir"
                            data-react-url="{{ route('whatsapp.conversas.mensagem.reagir', [$conversaSelecionada, $mensagem]) }}"
                            data-token="{{ csrf_token() }}">
                            <i class="bi bi-emoji-smile"></i>
                        </button>
                        <button class="wa-msg-action-btn reply" title="Responder">
                            <i class="bi bi-reply-fill"></i>
                        </button>
                        @if($mensagem->direcao === 'saida' && $mensagem->tipo === 'texto')
                        <button class="wa-msg-action-btn edit" title="Editar"
                            data-url="{{ route('whatsapp.conversas.mensagem.editar', $mensagem) }}"
                            data-token="{{ csrf_token() }}">
                            <i class="bi bi-pencil"></i>
                        </button>
                        @endif
                        <button class="wa-msg-action-btn delete" title="Apagar">
                            <i class="bi bi-trash3"></i>
                        </button>
                    </div>
                    @endunless

                    @if($quotedConteudo)
                    <div class="wa-quoted-bubble">
                        <div class="wa-quoted-autor">{{ $quotedAutor }}</div>
                        <div class="wa-quoted-text">{{ $quotedConteudo }}</div>
                    </div>
                    @endif

                    @if($mensagem->direcao === 'saida' && $nomeInterno)
                        <div class="wa-msg-sender-internal">{{ $nomeInterno }}</div>
                    @elseif($nomeParticipant)
                        <div class="wa-msg-sender-group wa-sender-renomear"
                             data-url="{{ route('whatsapp.instancias.membro.renomear', $instanciaSelecionada->id) }}"
                             data-token="{{ csrf_token() }}"
                             data-participant="{{ $mensagem->participant }}"
                             data-participant-alt="{{ $partAltGlobal ?? '' }}"
                             data-nome-atual="{{ $nomeParticipant }}"
                             title="Clique para renomear">
                            {{ $nomeParticipant }}
                            <i class="bi bi-pencil-fill wa-rename-icon"></i>
                        </div>
                    @endif

                    @if($mensagem->apagada_em)
                        <div class="wa-msg-content" style="color:var(--wa-text-muted);font-style:italic;">
                            <i class="bi bi-slash-circle"></i> Mensagem apagada
                        </div>
                    @else
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

                        @if($mensagem->tipo === 'contato')
                            @php $contatosCard = $mensagem->conteudo ? explode(';;', $mensagem->conteudo) : []; @endphp
                            @forelse($contatosCard as $cardStr)
                                @php [$cardNome, $cardTel] = array_pad(explode('|', $cardStr, 2), 2, null); @endphp
                                <div class="wa-contact-card">
                                    <div class="wa-contact-card__avatar">{{ mb_substr($cardNome ?: '?', 0, 1) }}</div>
                                    <div style="flex:1;min-width:0">
                                        <div class="wa-contact-card__name">{{ $cardNome ?: $cardTel }}</div>
                                        @if($cardTel && $cardNome)<div class="wa-contact-card__tel">{{ $cardTel }}</div>@endif
                                    </div>
                                </div>
                                @if($cardTel)
                                <a href="https://wa.me/{{ $cardTel }}" target="_blank" class="wa-contact-card__link">
                                    <i class="bi bi-whatsapp"></i> Conversar no WhatsApp
                                </a>
                                @endif
                            @empty
                                <div class="wa-msg-content" style="color:var(--wa-text-muted);font-style:italic;">👤 Contato</div>
                            @endforelse
                        @elseif($mensagem->conteudo)
                            <div class="wa-msg-content">{{ $mensagem->conteudo }}</div>
                        @elseif($mensagem->tipo === 'audio')
                            <div class="wa-msg-content" style="color: var(--wa-text-muted); font-style: italic;">🎤 Mensagem de voz</div>
                        @elseif($mensagem->tipo === 'figurinha')
                            <div class="wa-msg-content" style="color: var(--wa-text-muted); font-style: italic;">😄 Figurinha</div>
                        @elseif($mensagem->tipo === 'localizacao')
                            <div class="wa-msg-content" style="color: var(--wa-text-muted); font-style: italic;">📍 Localização</div>
                        @endif
                    @endif

                    <div class="wa-msg-footer">
                        @if($mensagem->editada_em)
                            <span class="wa-edited-tag">Editada</span>
                        @endif
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

                @if(!empty($mensagem->reacoes))
                    @php
                        $totalReacoes = array_sum(array_map('count', $mensagem->reacoes));
                    @endphp
                    <div class="wa-msg-reactions">
                        @foreach($mensagem->reacoes as $emoji => $reatores)
                            @if(!empty($reatores))
                                <span class="wa-reaction-badge" title="{{ implode(', ', array_map(fn($r) => $r === 'me' ? 'Você' : preg_replace('/[^0-9]/', '', str_replace(['@s.whatsapp.net','@lid'], '', $r)), $reatores)) }}">
                                    {{ $emoji }}{{ count($reatores) > 1 ? ' ' . count($reatores) : '' }}
                                </span>
                            @endif
                        @endforeach
                    </div>
                @endif
            </div>
        @empty
            <div class="text-center py-5" style="color: var(--wa-text-muted); opacity:0.4;">
                <i class="bi bi-shield-lock" style="font-size: 40px;"></i>
                <p class="small mt-2">Mensagens protegidas de ponta a ponta</p>
            </div>
        @endforelse
    </div>
@else
    <div class="wa-messages-scroll" id="waMessages"></div>
@endif
