@extends('errors.layout', [
    'title' => 'Sessão expirada',
    'code' => '419',
    'icon' => 'bi bi-clock-history',
    'heading' => 'Sua sessão expirou',
    'message' => 'Por segurança, sua sessão foi encerrada ou o formulário expirou. Faça login novamente para continuar usando o sistema.',
    'showBackButton' => true,
    'showHomeButton' => false,
    'showLogoutButton' => false,
    'showLoginButton' => true,
])