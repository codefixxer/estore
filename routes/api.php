// routes/api.php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StripePaymentController;

Route::prefix('stripe')->group(function () {
    // Payment Intent API
    Route::post('/create-payment-intent', [StripePaymentController::class, 'createPaymentIntent']);
    
    // Test endpoint
    Route::get('/test', [StripePaymentController::class, 'hello']);
    
    // Webhook handler
    Route::post('/webhook', [StripePaymentController::class, 'handleWebhook']);
});