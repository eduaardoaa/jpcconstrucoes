@extends('errors.layout', [
    'title' => 'Acesso negado',
    'code' => '403',
    'icon' => 'bi bi-shield-lock-fill',
    'heading' => 'Você não tem permissão para acessar esta área',
    'message' => 'Seu usuário não possui autorização para visualizar esta página. Caso ache que isso é um erro, volte para a tela anterior, acesse o painel ou faça login novamente.',
    'showBackButton' => true,
    'showHomeButton' => true,
    'showLogoutButton' => true,
    'showLoginButton' => false,
])