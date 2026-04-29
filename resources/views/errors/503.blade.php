@extends('errors.layout', [
    'title' => 'Sistema indisponível',
    'code' => '503',
    'icon' => 'bi bi-tools',
    'heading' => 'Sistema temporariamente indisponível',
    'message' => 'O sistema está em manutenção ou passando por instabilidade. Tente novamente em alguns instantes.',
    'showBackButton' => true,
    'showHomeButton' => true,
    'showLogoutButton' => false,
    'showLoginButton' => false,
])