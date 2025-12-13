<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FingerspotController;

Route::post('/fingerspot/webhook', [FingerspotController::class, 'handleWebhook']);