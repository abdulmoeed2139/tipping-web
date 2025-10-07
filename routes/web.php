<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TipController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\FrontentController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [FrontentController::class, 'homePage']);

Auth::routes();

Route::get('/home', function(){
    return redirect('/dashboard');
})->name('home');

Route::middleware('auth')->group(function () {
    route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');
    route::get('/tip', [FrontentController::class, 'onetimePayment']);
});


Route::post('/create-order', [TipController::class, 'createOrder']);
Route::get('/pay/{order}', [TipController::class, 'pay']);
Route::get('/checkout/{order}', [TipController::class, 'checkout']);
Route::get('/order/{order}/success', [TipController::class, 'paymentSuccessful']);
Route::get('/order/{order}/invoice', [TipController::class, 'generateInvoice']);
Route::get('/order/{id}/failed', [TipController::class, 'unsuccessfulPayment']);
Route::get('/paylink/{token}', [TipController::class, 'validateLink']);
Route::post('/generate-link', [TipController::class, 'generateLink']);
Route::post('/regenerate-link', [TipController::class, 'regenerateLink']);

// Crypto payment routes
Route::post('/create-crypto-payment', [TipController::class, 'createCryptoPayment']);
Route::get('/crypto-pay/{order}', [TipController::class, 'cryptoPay']);
Route::get('/crypto-payment-status/{order}', [TipController::class, 'checkCryptoPaymentStatus']);
Route::get('/supported-crypto', [TipController::class, 'getSupportedCrypto']);

// Fiat payment routes (Visa/Mastercard/Apple Pay)
Route::post('/create-fiat-payment', [TipController::class, 'createFiatPayment']);
Route::get('/fiat-pay/{order}', [TipController::class, 'fiatPay']);
Route::get('/fiat-payment-success/{order}', [TipController::class, 'fiatPaymentSuccess']);
Route::get('/fiat-payment-cancel/{order}', [TipController::class, 'fiatPaymentCancel']);
Route::get('/supported-fiat-methods', [TipController::class, 'getSupportedFiatMethods']);



// route::get('/select-merchant', [FrontentController::class, 'selectMethod']);
// route::get('/invoice', [FrontentController::class, 'invoice']);
// route::get('/failure', [FrontentController::class, 'unsuccessfulPayment']);
// route::get('/success', [FrontentController::class, 'paymentSuccessful']);
