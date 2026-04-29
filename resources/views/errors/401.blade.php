@extends('errors.layout', [
    'title' => 'Não autenticado',
    'code' => '401',
    'icon' => 'bi bi-person-lock',
    'heading' => 'Sua sessão não é válida',
    'message' => 'Você precisa entrar novamente no sistema para continuar. Isso pode acontecer quando a sessão expira ou há falha de autenticação.',
    'showBackButton' => true,
    'showHomeButton' => false,
    'showLogoutButton' => false,
    'showLoginButton' => true,
])