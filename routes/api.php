<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\WalletController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
//---------------------------------------------------------------------------------
Route::name('login')->get('/', function () {
    return response()->json(['error' => 'لطفا ابتدا به حساب کاربری خود وارد شوید'], 403);
});
//---------------------------------------------------------------------------------
Route::prefix('auth')->group(function () {
    Route::post('/register', [UserController::class, 'register']);
    Route::post('/login', [UserController::class, 'login']);
    Route::post('/logout', [UserController::class, 'logout'])
        ->middleware('auth:sanctum');
});
//---------------------------------------------------------------------------------
Route::prefix('wallets')->middleware('auth:sanctum')->group(function () {
    Route::get('/{id?}', [WalletController::class, 'view'])
        ->whereNumber('id');
    Route::post('/add', [WalletController::class, 'add']);
    Route::delete('/{id}', [WalletController::class, 'delete'])
        ->whereNumber('id');
    Route::prefix('/transaction')->group(function () {
        Route::post('/{id}', [WalletController::class, 'makeTransaction'])
            ->whereNumber('id');
        Route::get('/{id?}', [WalletController::class, 'getTransactions'])
            ->whereNumber('id');
    });
});
