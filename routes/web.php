<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\ObraController;
use App\Http\Controllers\CargoController;
use App\Http\Controllers\ProdutoController;
use App\Http\Controllers\EstoqueController;
use App\Http\Controllers\MovimentacaoEstoqueController;
use App\Http\Controllers\FuncionarioController;
use App\Http\Controllers\EntregaEpiController;
use App\Http\Controllers\EpiController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RelatorioController;
use App\Http\Controllers\ObraTreinamentoDdsController;
use App\Http\Controllers\VeiculoController;
use App\Http\Controllers\SolicitacaoAbastecimentoController;
use App\Http\Controllers\AbastecimentoAdminController;
use App\Http\Controllers\CombustivelController;
use App\Http\Controllers\DeslocamentoVeiculoController;
use App\Http\Controllers\WhatsappInstanciaController;
use App\Http\Controllers\WhatsappConversaController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::redirect('/', '/login');

/*
|--------------------------------------------------------------------------
| LOGIN / LOGOUT
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');
});

Route::middleware('auth')->post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

/*
|--------------------------------------------------------------------------
| ROTAS AUTENTICADAS GERAIS
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/primeiro-acesso', [UserController::class, 'formPrimeiroAcesso'])->name('primeiro.acesso');
    Route::post('/primeiro-acesso', [UserController::class, 'salvarPrimeiroAcesso'])->name('primeiro.acesso.salvar');

    Route::get('/perfil', [UserController::class, 'editProfile'])->name('perfil.edit');
    Route::put('/perfil', [UserController::class, 'updateProfile'])->name('perfil.update');
    Route::put('/perfil/senha', [UserController::class, 'updatePassword'])->name('perfil.password.update');
});

/*
|--------------------------------------------------------------------------
| USUÁRIOS DO SISTEMA
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'permissao:usuarios'])->group(function () {
    Route::get('/gerenciar-usuarios', [UserManagementController::class, 'index'])->name('usuarios.index');
    Route::post('/gerenciar-usuarios', [UserManagementController::class, 'store'])->name('usuarios.store');
    Route::put('/gerenciar-usuarios/{user}', [UserManagementController::class, 'update'])->name('usuarios.update');
    Route::patch('/gerenciar-usuarios/{user}/toggle-status', [UserManagementController::class, 'toggleStatus'])->name('usuarios.toggle-status');
    Route::patch('/gerenciar-usuarios/{user}/reset-password', [UserManagementController::class, 'resetPassword'])->name('usuarios.reset-password');
    Route::delete('/gerenciar-usuarios/{user}', [UserManagementController::class, 'destroy'])->name('usuarios.destroy');
});

/*
|--------------------------------------------------------------------------
| OBRAS
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'permissao:obras'])->group(function () {
    Route::get('/gerenciar-obras', [ObraController::class, 'index'])->name('obras.index');
    Route::post('/gerenciar-obras', [ObraController::class, 'store'])->name('obras.store');
    Route::put('/gerenciar-obras/{obra}', [ObraController::class, 'update'])->name('obras.update');
    Route::patch('/gerenciar-obras/{obra}/toggle-status', [ObraController::class, 'toggleStatus'])->name('obras.toggle-status');
    Route::delete('/gerenciar-obras/{obra}', [ObraController::class, 'destroy'])->name('obras.destroy');
});

/*
|--------------------------------------------------------------------------
| CARGOS
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'permissao:cargos'])->group(function () {
    Route::get('/gerenciar-cargos', [CargoController::class, 'index'])->name('cargos.index');
    Route::post('/gerenciar-cargos', [CargoController::class, 'store'])->name('cargos.store');
    Route::put('/gerenciar-cargos/{cargo}', [CargoController::class, 'update'])->name('cargos.update');
    Route::delete('/gerenciar-cargos/{cargo}', [CargoController::class, 'destroy'])->name('cargos.destroy');
});

/*
|--------------------------------------------------------------------------
| PRODUTOS
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'permissao:produtos'])->group(function () {
    Route::get('/gerenciar-produtos', [ProdutoController::class, 'index'])->name('produtos.index');
    Route::post('/gerenciar-produtos', [ProdutoController::class, 'store'])->name('produtos.store');
    Route::put('/gerenciar-produtos/{produto}', [ProdutoController::class, 'update'])->name('produtos.update');
    Route::delete('/gerenciar-produtos/{produto}', [ProdutoController::class, 'destroy'])->name('produtos.destroy');
    Route::patch('/gerenciar-produtos/{produto}/inativar', [ProdutoController::class, 'inativar'])->name('produtos.inativar');
    Route::patch('/gerenciar-produtos/{produto}/ativar', [ProdutoController::class, 'ativar'])->name('produtos.ativar');
});

/*
|--------------------------------------------------------------------------
| ESTOQUE
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'permissao:estoque'])->group(function () {
    Route::get('/estoque', [EstoqueController::class, 'index'])->name('estoque.index');
    Route::post('/estoque/reabastecer', [EstoqueController::class, 'reabastecer'])->name('estoque.reabastecer');
    Route::get('/estoque/historico', [MovimentacaoEstoqueController::class, 'index'])->name('estoque.historico');
});

/*
|--------------------------------------------------------------------------
| FUNCIONÁRIOS
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'permissao:funcionarios'])->group(function () {
    Route::get('/gerenciar-funcionarios', [FuncionarioController::class, 'index'])->name('funcionarios.index');
    Route::post('/gerenciar-funcionarios', [FuncionarioController::class, 'store'])->name('funcionarios.store');
    Route::put('/gerenciar-funcionarios/{funcionario}', [FuncionarioController::class, 'update'])->name('funcionarios.update');
    Route::patch('/gerenciar-funcionarios/{funcionario}/toggle-status', [FuncionarioController::class, 'toggleStatus'])->name('funcionarios.toggle-status');
    Route::delete('/gerenciar-funcionarios/{funcionario}', [FuncionarioController::class, 'destroy'])->name('funcionarios.destroy');
});

/*
|--------------------------------------------------------------------------
| ENTREGAS DE EPI
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'permissao:entregas_epi'])->group(function () {
    Route::get('/epi', [EpiController::class, 'index'])->name('epi.index');
    Route::get('/epi/funcionario/{funcionario}', [EpiController::class, 'historico'])->name('epi.historico');
    Route::get('/epi/funcionario/{funcionario}/pdf/ultima', [EpiController::class, 'pdfUltima'])->name('epi.pdf.ultima');
    Route::get('/epi/funcionario/{funcionario}/pdf/completo', [EpiController::class, 'pdfCompleto'])->name('epi.pdf.completo');

    Route::get('/entregas', [EntregaEpiController::class, 'index'])->name('entregas.index');
    Route::post('/entregas', [EntregaEpiController::class, 'store'])->name('entregas.store');
    Route::delete('/entregas/{entrega}', [EntregaEpiController::class, 'destroy'])->name('entregas.destroy');
    Route::post('/entregas/{entrega}/comprovantes', [EntregaEpiController::class, 'uploadComprovantes'])->name('entregas.comprovantes.upload');
});

Route::post('/entregas/{entrega}/comprovantes/upload', [EntregaEpiController::class, 'uploadComprovantes'])
    ->name('entregas.comprovantes.upload');

Route::get('/entregas/comprovantes/{comprovante}', [EntregaEpiController::class, 'abrirComprovante'])
    ->name('entregas.comprovantes.abrir');

/*
|--------------------------------------------------------------------------
| RELATÓRIOS
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'permissao:relatorios'])->group(function () {
    Route::get('/relatorios', [RelatorioController::class, 'index'])->name('relatorios.index');

    Route::get('/relatorios/estoque-obra', [RelatorioController::class, 'estoquePorObra'])->name('relatorios.estoque-obra');
    Route::get('/relatorios/estoque-obra/pdf', [RelatorioController::class, 'estoquePorObraPdf'])->name('relatorios.estoque-obra.pdf');

    Route::get('/relatorios/funcionarios', [RelatorioController::class, 'funcionarios'])->name('relatorios.funcionarios');
    Route::get('/relatorios/funcionarios/pdf', [RelatorioController::class, 'funcionariosPdf'])->name('relatorios.funcionarios.pdf');

    Route::get('/relatorios/consumo', [RelatorioController::class, 'consumo'])->name('relatorios.consumo');
    Route::get('/relatorios/consumo/pdf', [RelatorioController::class, 'consumoPdf'])->name('relatorios.consumo.pdf');

    Route::get('/relatorios/comprovantes', [RelatorioController::class, 'comprovantes'])->name('relatorios.comprovantes');
    Route::get('/relatorios/comprovantes/pdf', [RelatorioController::class, 'comprovantesPdf'])->name('relatorios.comprovantes.pdf');
});

/*
|--------------------------------------------------------------------------
| DDS DAS OBRAS
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'permissao:obras'])->group(function () {
    Route::get('/gerenciar-obras/{obra}/dds', [ObraTreinamentoDdsController::class, 'historico'])
        ->name('obras.dds.historico');

    Route::post('/gerenciar-obras/{obra}/dds', [ObraTreinamentoDdsController::class, 'store'])
        ->name('obras.dds.store');

    Route::delete('/gerenciar-obras/dds/{treinamento}', [ObraTreinamentoDdsController::class, 'destroy'])
        ->name('obras.dds.destroy');

    Route::get('/gerenciar-obras/dds/anexo/{anexo}', [ObraTreinamentoDdsController::class, 'abrirAnexo'])
        ->name('obras.dds.anexo.abrir');
});

/*
|--------------------------------------------------------------------------
| ABASTECIMENTO - VEÍCULOS E PAINEL
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'permissao:gerenciamento_combustivel'])->group(function () {
    Route::get('/abastecimento/veiculos', [VeiculoController::class, 'index'])->name('veiculos.index');
    Route::post('/abastecimento/veiculos', [VeiculoController::class, 'store'])->name('veiculos.store');
    Route::put('/abastecimento/veiculos/{veiculo}', [VeiculoController::class, 'update'])->name('veiculos.update');
    Route::patch('/abastecimento/veiculos/{veiculo}/toggle-status', [VeiculoController::class, 'toggleStatus'])->name('veiculos.toggle-status');

    Route::get('/abastecimento/painel', [CombustivelController::class, 'index'])->name('abastecimento.painel.index');
    Route::get('/abastecimento/painel/pdf', [CombustivelController::class, 'pdf'])->name('abastecimento.painel.pdf');
});

/*
|--------------------------------------------------------------------------
| ABASTECIMENTO - SOLICITAÇÃO DO USUÁRIO
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    Route::get('/abastecimento/minhas-solicitacoes', [SolicitacaoAbastecimentoController::class, 'index'])
        ->name('abastecimento.solicitacoes.index');

    Route::post('/abastecimento/minhas-solicitacoes', [SolicitacaoAbastecimentoController::class, 'store'])
        ->name('abastecimento.solicitacoes.store');

    Route::post('/abastecimento/minhas-solicitacoes/{solicitacao}/comprovante', [SolicitacaoAbastecimentoController::class, 'enviarComprovante'])
        ->name('abastecimento.solicitacoes.comprovante');
});
/*
|--------------------------------------------------------------------------
| DESLOCAMENTOS - USUÁRIO
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    Route::get('/abastecimento/meus-deslocamentos', [DeslocamentoVeiculoController::class, 'meusDeslocamentos'])
        ->name('deslocamentos.meus');

    Route::post('/abastecimento/meus-deslocamentos', [DeslocamentoVeiculoController::class, 'store'])
        ->name('deslocamentos.store');

    Route::post('/abastecimento/meus-deslocamentos/{deslocamento}/parada', [DeslocamentoVeiculoController::class, 'storeParada'])
        ->name('deslocamentos.parada.store');

    Route::post('/abastecimento/meus-deslocamentos/{deslocamento}/chegada', [DeslocamentoVeiculoController::class, 'storeChegada'])
        ->name('deslocamentos.chegada.store');
});

/*
|--------------------------------------------------------------------------
| DESLOCAMENTOS - CONTROLE
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'permissao:deslocamentos'])->group(function () {
    Route::get('/abastecimento/controle-deslocamentos', [DeslocamentoVeiculoController::class, 'index'])
        ->name('deslocamentos.index');
});

/*
|--------------------------------------------------------------------------
| ABASTECIMENTO - ADMIN
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'permissao:gerenciamento_combustivel'])->group(function () {
    Route::get('/abastecimento/gerenciar-solicitacoes', [AbastecimentoAdminController::class, 'index'])
        ->name('abastecimento.admin.index');

    Route::patch('/abastecimento/solicitacoes/{solicitacao}/aprovar', [AbastecimentoAdminController::class, 'aprovar'])
        ->name('abastecimento.admin.aprovar');

    Route::patch('/abastecimento/solicitacoes/{solicitacao}/reprovar', [AbastecimentoAdminController::class, 'reprovar'])
        ->name('abastecimento.admin.reprovar');

    Route::patch('/abastecimento/solicitacoes/{solicitacao}/ajustar', [AbastecimentoAdminController::class, 'ajustar'])
        ->name('abastecimento.admin.ajustar');
});
/*
|--------------------------------------------------------------------------
| WHATSAPP
|--------------------------------------------------------------------------
*/
Route::prefix('whatsapp')->name('whatsapp.')->middleware('auth')->group(function () {
    Route::get('/conversas', [WhatsappConversaController::class, 'index'])
        ->name('conversas.index');

    Route::get('/instancias', [WhatsappInstanciaController::class, 'index'])
        ->name('instancias.index');

    Route::post('/instancias', [WhatsappInstanciaController::class, 'store'])
        ->name('instancias.store');

    Route::put('/instancias/{instancia}', [WhatsappInstanciaController::class, 'update'])
        ->name('instancias.update');

    Route::patch('/instancias/{instancia}/status', [WhatsappInstanciaController::class, 'toggleStatus'])
        ->name('instancias.toggle-status');

    Route::patch('/instancias/{instancia}/regenerar-webhook', [WhatsappInstanciaController::class, 'regenerarWebhook'])
        ->name('instancias.regenerar-webhook');

    Route::post('/instancias/{instancia}/sincronizar', [WhatsappConversaController::class, 'sincronizar'])
        ->name('instancias.sincronizar');

    Route::post('/instancias/{instancia}/sincronizar-nomes', [WhatsappConversaController::class, 'sincronizarNomes'])
        ->name('instancias.sincronizar-nomes');

    Route::post('/instancias/{instancia}/sincronizar-fotos', [WhatsappConversaController::class, 'sincronizarFotos'])
        ->name('instancias.sincronizar-fotos');

    Route::post('/conversas/nova', [WhatsappConversaController::class, 'novaConversa'])
        ->name('conversas.nova');

    Route::get('/conversas/{mensagem}/midia', [WhatsappConversaController::class, 'downloadMidia'])
        ->name('conversas.midia');

    Route::post('/conversas/{conversa}/enviar-texto', [WhatsappConversaController::class, 'enviarTexto'])
        ->name('conversas.enviar-texto');

    Route::post('/conversas/{conversa}/enviar-midia', [WhatsappConversaController::class, 'enviarMidia'])
        ->name('conversas.enviar-midia');

    Route::patch('/conversas/{conversa}/identificacao', [WhatsappConversaController::class, 'alterarIdentificacao'])
        ->name('conversas.identificacao');

    Route::patch('/conversas/{conversa}/definir-numero', [WhatsappConversaController::class, 'definirNumeroContato'])
        ->name('conversas.definir-numero');

    Route::patch('/conversas/{conversa}/renomear', [WhatsappConversaController::class, 'renomearContato'])
        ->name('conversas.renomear');

    Route::delete('/conversas/mensagens/{mensagem}', [WhatsappConversaController::class, 'apagarMensagem'])
        ->name('conversas.mensagem.apagar');
});