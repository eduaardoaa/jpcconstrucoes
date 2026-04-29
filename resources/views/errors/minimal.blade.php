@extends('errors.layout', [
    'title' => 'Erro',
    'code' => 'Erro',
    'heading' => 'Ocorreu um problema',
    'message' => 'Não foi possível concluir sua solicitação. Tente voltar ou entrar novamente no sistema.',
    'showLogoutButton' => true,
    'showBackButton' => true,
    'showHomeButton' => true,
])