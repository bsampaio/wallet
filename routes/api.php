<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use \App\Http\Controllers\API\Auth\AuthController;
use \App\Http\Controllers\API\WalletController;
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

Route::middleware('heimdall')->group(function() {

    Route::get('/', function() {
        return [
            'environment' => getenv('APP_ENV'),
            'name'        => getenv('APP_NAME'),
            'framework'   => 'Laravel',
            'version'     => app()->version()
        ];
    });

    Route::middleware(['cors', 'json.response'])->group(function() {
        Route::post('/login',  [AuthController::class, 'login'])->name('auth.login');
        Route::post('/register', [AuthController::class, 'register'])->name('auth.register');
        Route::get('/nickname', [AuthController::class, 'isNicknameAvailable'])->name('auth.nickname');



        Route::group(['middleware' => 'throttle:100,1'], function() {
            Route::get('users/available', [WalletController::class, 'users']);
            /**
             * Groups all wallet methods
             */
            Route::group(['prefix' => 'wallet'], function() {
                Route::post('/', [WalletController::class, 'make']);
                Route::get('{nickname}/key', [WalletController::class, 'key']);
                Route::post('{nickname}/enable', [WalletController::class, 'enable']);

                Route::group(['middleware' => 'wallet.key'], function() {
                    Route::get('/info', [WalletController::class, 'info']);
                    Route::get('/balance', [WalletController::class, 'balance']);
                    Route::post('/transfer', [WalletController::class, 'transfer']);
                    Route::get('/statement', [WalletController::class, 'statement']);

//                    Route::post('{nickname}/card/add', [WalletController::class, 'addCard']);
//                    Route::get('{nickname}/cards', [WalletController::class, 'cards']);
                });
            });
        });
    });

    Route::middleware('auth:api')->group(function() {
        Route::post('/logout',  [AuthController::class, 'logout'])->name('auth.logout');
        Route::get('/user', function(Request $request) {
            return $request->user();
        });
    });
});
