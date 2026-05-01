@extends('layouts.app')

@section('title', 'Dashboard Engenheiro')
@section('pageTitle', 'Dashboard do Engenheiro')
@section('pageDescription', 'Painel inicial com acesso conforme as permissões do cargo.')

@section('content')
    <div class="grid grid-4">
        <div class="card kpi-card">
            <div class="card-body">
                <div class="kpi-label">Obras com acesso</div>
                <div class="kpi-value">
                    {{ auth()->user()->hasPermissao('obras') ? 'Liberado' : 'Bloqueado' }}
                </div>
                <div class="kpi-trend">
                    <i class="bi bi-building"></i>
                    Módulo de obras
                </div>
            </div>
        </div>

        <div class="card kpi-card">
            <div class="card-body">
                <div class="kpi-label">Funcionários</div>
                <div class="kpi-value">
                    {{ auth()->user()->hasPermissao('funcionarios') ? 'Liberado' : 'Bloqueado' }}
                </div>
                <div class="kpi-trend">
                    <i class="bi bi-person-workspace"></i>
                    Módulo de funcionários
                </div>
            </div>
        </div>

        <div class="card kpi-card">
            <div class="card-body">
                <div class="kpi-label">Estoque</div>
                <div class="kpi-value">
                    {{ auth()->user()->hasPermissao('estoque') ? 'Liberado' : 'Bloqueado' }}
                </div>
                <div class="kpi-trend">
                    <i class="bi bi-box-seam"></i>
                    Módulo de estoque
                </div>
            </div>
        </div>

        <div class="card kpi-card">
            <div class="card-body">
                <div class="kpi-label">Entregas de EPI</div>
                <div class="kpi-value">
                    {{ auth()->user()->hasPermissao('entregas_epi') ? 'Liberado' : 'Bloqueado' }}
                </div>
                <div class="kpi-trend">
                    <i class="bi bi-shield-check"></i>
                    Módulo de entregas
                </div>
            </div>
        </div>
    </div>

    <div class="section-spacer"></div>

    <div class="card">
        <div class="card-header">
            <div class="card-title">Resumo do acesso do usuário</div>
            <div class="card-subtitle">Teste visual das permissões vinculadas ao cargo atual.</div>
        </div>

        <div class="card-body">
            <ul class="simple-list list-clean">
                <li>
                    <div>
                        <strong>Usuário logado</strong>
                        <small>{{ auth()->user()->name }}</small>
                    </div>
                    <span class="badge-status badge-info">{{ auth()->user()->cargo?->nome ?? 'Sem cargo' }}</span>
                </li>

                <li>
                    <div>
                        <strong>Permissão de usuários</strong>
                        <small>Controle de acesso ao módulo de usuários</small>
                    </div>
                    @if(auth()->user()->hasPermissao('usuarios'))
                        <span class="badge-status badge-success">Liberado</span>
                    @else
                        <span class="badge-status badge-warning">Bloqueado</span>
                    @endif
                </li>

                <li>
                    <div>
                        <strong>Permissão de cargos</strong>
                        <small>Controle de acesso ao módulo de cargos</small>
                    </div>
                    @if(auth()->user()->hasPermissao('cargos'))
                        <span class="badge-status badge-success">Liberado</span>
                    @else
                        <span class="badge-status badge-warning">Bloqueado</span>
                    @endif
                </li>

                <li>
                    <div>
                        <strong>Permissão de obras</strong>
                        <small>Controle de acesso ao módulo de obras</small>
                    </div>
                    @if(auth()->user()->hasPermissao('obras'))
                        <span class="badge-status badge-success">Liberado</span>
                    @else
                        <span class="badge-status badge-warning">Bloqueado</span>
                    @endif
                </li>
            </ul>
        </div>
    </div>
@endsection