<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// NOWPayments callback route (no auth required)
Route::post('/nowpayments/callback', [App\Http\Controllers\TipController::class, 'nowPaymentsCallback']);

// Payment status check route
Route::get('/nowpayments/payment-status/{paymentId}', function($paymentId) {
    $nowPayments = new App\Services\NowPaymentsService();
    $status = $nowPayments->getPaymentStatus($paymentId);
    
    if ($status && isset($status['payment_status'])) {
        return response()->json(['status' => $status['payment_status']]);
    }
    
    return response()->json(['status' => 'pending']);
});
