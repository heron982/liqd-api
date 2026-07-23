<?php

use App\Modules\Auth\Features\Login\Controllers\LoginController;
use App\Modules\Auth\Features\Me\Controllers\MeController;
use App\Modules\Auth\Features\Register\Controllers\RegisterController;
use App\Modules\Market\Features\BtcPrice\Controllers\ShowBtcPriceController;
use App\Modules\Trade\Features\Buy\Controllers\BuyController;
use App\Modules\Trade\Features\Sell\Controllers\SellController;
use App\Modules\Transaction\Features\Index\Controllers\IndexTransactionController;
use App\Modules\Wallet\Features\Show\Controllers\ShowWalletController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [RegisterController::class, 'execute']);
Route::post('/login', [LoginController::class, 'execute']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [MeController::class, 'execute']);
    Route::get('/wallet', [ShowWalletController::class, 'execute']);
    Route::get('/market/btc', [ShowBtcPriceController::class, 'execute']);
    Route::post('/trade/buy', [BuyController::class, 'execute']);
    Route::post('/trade/sell', [SellController::class, 'execute']);
    Route::get('/transactions', [IndexTransactionController::class, 'execute']);
});
