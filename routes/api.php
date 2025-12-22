<?php

use App\Http\Controllers\FingerspotController;
use Illuminate\Support\Facades\Route;

Route::post('/fingerspot/webhook', [FingerspotController::class, 'handleWebhook']);
