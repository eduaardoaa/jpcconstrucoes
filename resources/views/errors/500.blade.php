@extends('errors.layout', [
    'title' => 'Erro interno',
    'code' => '500',
    'icon' => 'bi bi-exclamation-octagon-fill',
    'heading' => 'Ocorreu um erro interno no sistema',
    'message' => 'Algo deu errado ao processar sua solicitação. Tente novamente. Se o problema persistir, entre novamente no sistema.',
    'showBackButton' => true,
    'showHomeButton' => true,
    'showLogoutButton' => true,
    'showLoginButton' => false,
])