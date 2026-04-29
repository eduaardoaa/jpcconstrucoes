<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WhatsappWebhookController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::post('/whatsapp/webhook/{token}', [WhatsappWebhookController::class, 'receber'])
    ->name('api.whatsapp.webhook');