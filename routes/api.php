<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KontainerController;
use App\Http\Controllers\ApiAuthController;
use App\Http\Controllers\ApiProfileController;
use App\Http\Controllers\ApiWalletController;
use App\Http\Controllers\Documents;
use Illuminate\Support\Facades\Auth;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::get('/parse-number-kontainer/{number}', [KontainerController::class, 'parse']);
Route::get('/unauthorized', function () {
    return response()->json(['success' => false, 'status' => 'Unauthorized', 'message' => 'Login for access this page'], 403);
})->name('unauthorized');

/** 
 * Max 2x requests perMinute
 */
Route::post('/register', [ApiAuthController::class, 'register'])->middleware(['throttle:register']);
Route::post('/login', [ApiAuthController::class, 'login'])->middleware(['throttle:register']);

Route::get('/document-pdf/{id}', [Documents::class, 'print_invoice']);

Route::middleware('auth:api')->group(function () {

    Route::prefix('wallet')->group(function () {

        /** 
         * Max 50x Requests perminute
         */
        Route::middleware(['throttle:requestdata'])->group(function () {
            Route::post('/balance', [ApiWalletController::class, 'getBalance']);
            Route::get('/topup-status/{id}', [ApiWalletController::class, 'topup_status']);
            Route::get('/unpaid-billing', [ApiWalletController::class, 'unpaid_bill']);
            Route::get('/withdraw-status/{id}', [ApiWalletController::class, 'withdraw_status']);
            Route::get('/mutation', [ApiWalletController::class, 'mutation']);
        });
        /**
         * Max 1x request perMinute.
         */
        Route::middleware(['throttle:input'])->group(function () {
            Route::put('/topup', [ApiWalletController::class, 'topup']);
            Route::get('/billing-payment/{id}', [ApiWalletController::class, 'bill_payment']);
            Route::post('/transfer', [ApiWalletController::class, 'transfer']);
            Route::post('/withdraw', [ApiWalletController::class, 'withdraw']);
        });
    });


    Route::post('/reset-password', [ApiProfileController::class, 'reset_password']);
    Route::get('/logs-activity', [ApiProfileController::class, 'logs']);
    Route::get('/logout', [ApiProfileController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});
