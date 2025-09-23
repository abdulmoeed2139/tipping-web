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
    route::get('/dashboard', [HomeController::class, 'index'])->name('home');
    route::get('/tip', [FrontentController::class, 'onetimePayment']);
});


Route::post('/create-order', [TipController::class, 'createOrder']);
Route::get('/pay/{order}', [TipController::class, 'pay']);
Route::get('/checkout/{order}', [TipController::class, 'checkout']);
Route::get('/order/{id}/success', [TipController::class, 'paymentSuccessful']);
Route::get('/order/{id}/failed', [TipController::class, 'unsuccessfulPayment']);
Route::get('/paylink/{token}', [TipController::class, 'validateLink']);
Route::post('/generate-link', [TipController::class, 'generateLink']);



// route::get('/select-merchant', [FrontentController::class, 'selectMethod']);
// route::get('/invoice', [FrontentController::class, 'invoice']);
// route::get('/failure', [FrontentController::class, 'unsuccessfulPayment']);
// route::get('/success', [FrontentController::class, 'paymentSuccessful']);
