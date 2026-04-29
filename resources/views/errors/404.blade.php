@extends('errors.layout', [
    'title' => 'Página não encontrada',
    'code' => '404',
    'icon' => 'bi bi-search',
    'heading' => 'Página não encontrada',
    'message' => 'A página que você tentou acessar não existe, foi removida ou o endereço está incorreto.',
    'showBackButton' => true,
    'showHomeButton' => true,
    'showLogoutButton' => false,
    'showLoginButton' => false,
])