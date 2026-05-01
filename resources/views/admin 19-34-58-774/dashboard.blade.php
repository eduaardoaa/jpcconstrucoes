@extends('layouts.app')

@php
    $title = 'Dashboard Administrativo';
@endphp

@section('content')
    <div class="grid grid-4">
        <div class="card kpi-card">
            <div class="card-body">
                <div class="kpi-label">Obras cadastradas</div>
                <div class="kpi-value">{{ $totalObras ?? 0 }}</div>
                <div class="kpi-trend">
                    <i class="bi bi-building"></i>
                    Base de obras do sistema
                </div>
            </div>
        </div>

        <div class="card kpi-card">
            <div class="card-body">
                <div class="kpi-label">Usuários do sistema</div>
                <div class="kpi-value">{{ $totalUsuarios ?? 0 }}</div>
                <div class="kpi-trend">
                    <i class="bi bi-people"></i>
                    Administradores e engenheiros
                </div>
            </div>
        </div>

        <div class="card kpi-card">
            <div class="card-body">
                <div class="kpi-label">Funcionários</div>
                <div class="kpi-value">{{ $totalFuncionarios ?? 0 }}</div>
                <div class="kpi-trend">
                    <i class="bi bi-person-badge"></i>
                    Colaboradores vinculados
                </div>
            </div>
        </div>

        <div class="card kpi-card">
            <div class="card-body">
                <div class="kpi-label">EPIs cadastrados</div>
                <div class="kpi-value">{{ $totalEpis ?? 0 }}</div>
                <div class="kpi-trend">
                    <i class="bi bi-shield-check"></i>
                    Itens disponíveis no sistema
                </div>
            </div>
        </div>
    </div>

    <div class="section-spacer"></div>

    <div class="grid grid-2">
        <div class="card">
            <div class="card-header">
                <div class="card-title">Ações rápidas</div>
                <div class="card-subtitle">Acessos principais do administrativo</div>
            </div>

            <div class="card-body">
                <div class="action-grid">
                    <a href="#" class="action-card">
                        <div class="action-icon">
                            <i class="bi bi-building-add"></i>
                        </div>
                        <div class="action-title">Cadastrar obra</div>
                        <div class="action-desc">Criar uma nova obra no sistema.</div>
                    </a>

                    <a href="#" class="action-card">
                        <div class="action-icon">
                            <i class="bi bi-person-plus"></i>
                        </div>
                        <div class="action-title">Cadastrar usuário</div>
                        <div class="action-desc">Adicionar um novo usuário de acesso.</div>
                    </a>

                    <a href="#" class="action-card">
                        <div class="action-icon">
                            <i class="bi bi-tags"></i>
                        </div>
                        <div class="action-title">Gerenciar cargos</div>
                        <div class="action-desc">Controlar perfis e permissões.</div>
                    </a>

                    <a href="#" class="action-card">
                        <div class="action-icon">
                            <i class="bi bi-box-seam"></i>
                        </div>
                        <div class="action-title">Ver estoque</div>
                        <div class="action-desc">Acompanhar o estoque por obra.</div>
                    </a>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <div class="card-title">Resumo do sistema</div>
                <div class="card-subtitle">Visão geral inicial</div>
            </div>

            <div class="card-body">
                <ul class="simple-list list-clean">
                    <li>
                        <div>
                            <strong>Ambiente administrativo</strong>
                            <small>Controle de usuários, cargos e obras.</small>
                        </div>
                        <span class="badge-status badge-success">Ativo</span>
                    </li>

                    <li>
                        <div>
                            <strong>Controle operacional</strong>
                            <small>Funcionários, estoque e entregas de EPI.</small>
                        </div>
                        <span class="badge-status badge-warning">Em evolução</span>
                    </li>

                    <li>
                        <div>
                            <strong>Relatórios e fichas</strong>
                            <small>PDFs e comprovantes assinados.</small>
                        </div>
                        <span class="badge-status badge-info">Planejado</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
@endsection