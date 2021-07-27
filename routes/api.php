<?php

use App\Http\Controllers\API\UtilityController;
use App\Http\Controllers\CreditCardController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use \App\Http\Controllers\API\Auth\AuthController;
use \App\Http\Controllers\API\WalletController;
use \App\Http\Controllers\API\DigitalAccountController;
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

    /**
     * API Version
     *
     * Gets the API basic info.
     */
    Route::get('/', function() {
        return [
            'environment' => getenv('APP_ENV'),
            'name'        => getenv('APP_NAME'),
            'framework'   => 'Laravel',
            'version'     => app()->version()
        ];
    });

    Route::post('/notifications/setup', [WalletController::class, 'setupWebhooks']);

    Route::middleware(['cors', 'json.response'])->group(function() {
        //Route::post('/login',  [AuthController::class, 'login'])->name('auth.login');
        //Route::post('/register', [AuthController::class, 'register'])->name('auth.register');

        Route::get('/nickname', [AuthController::class, 'isNicknameAvailable'])->name('auth.nickname');
        Route::get('/email', [AuthController::class, 'isEmailAvailable'])->name('auth.email');


        Route::group(['middleware' => 'throttle:100,1'], function() {
            Route::get('/users/available', [WalletController::class, 'users']);
            Route::get('/users/nickname', [WalletController::class, 'userByNickname']);
            Route::get('users/', [WalletController::class, 'paginatedUserSearch']);
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

                    Route::post('/tax', [WalletController::class, 'setDefaultTax']);
                    Route::post('/cashback', [WalletController::class, 'setDefaultCashback']);


                    Route::post('/charge', [WalletController::class, 'charge']);
                    Route::post('/charge/{reference}/pay', [WalletController::class, 'payCharge']);

                    Route::post('/payment/credit-card', [WalletController::class, 'creditCardPayment']);
                    Route::post('/deposit/pix', [WalletController::class, 'deposit']);

                    Route::group(['prefix' => '/cards'], function() {
                        Route::get('/', [CreditCardController::class, 'cards']);
                        Route::post('/add', [CreditCardController::class, 'addCard']);
                        Route::post('/delete', [CreditCardController::class, 'removeCard']);
                        Route::post('/activate', [CreditCardController::class, 'enableCard']);
                        Route::post('/disable', [CreditCardController::class, 'disableCard']);
                        Route::post('/main', [CreditCardController::class, 'main']);
                    });
                });
            });

            Route::get('charge', [WalletController::class, 'loadCharge'])->name('charge.info');
            Route::group(['prefix' => '/utility'], function() {
                Route::post('/qrcode', [UtilityController::class, 'qrcode']);
            });

            Route::group(['prefix' => '/cards'], function() {
                Route::post('/tokenize', [CreditCardController::class, 'tokenize']);
            });

            Route::group(['prefix' => '/digital-accounts'], function() {
                Route::post('/', [DigitalAccountController::class, 'open']);
                Route::get('/business-areas', [DigitalAccountController::class, 'businessAreas']);
                Route::get('/banks', [DigitalAccountController::class, 'banks']);
                Route::get('/company-types', [DigitalAccountController::class, 'companyTypes']);
                Route::get('/documents-link', [DigitalAccountController::class, 'documentsLink']);
                Route::get('/documents', [DigitalAccountController::class, 'listDocuments']);
                Route::get('/inspect', [DigitalAccountController::class, 'inspect']);
                Route::get('/', [DigitalAccountController::class, 'index']);
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

Route::post('/notifications/juno/digital-accounts/{nickname}/changed/', [DigitalAccountController::class, 'digitalAccountStatusChanged'])->name('notifications.juno.digital-accounts.changed');
Route::post('/notifications/juno/payment/', [WalletController::class, 'paymentNotification'])->name('notifications.juno.payment.notification');
Route::post('/notifications/juno/charge/', [WalletController::class, 'chargeStatusChanged'])->name('notifications.juno.payment.chargeStatusChanged');

Route::get('/notifications/juno', function(Request $request) {
    \Illuminate\Support\Facades\Log::info('notifications.juno.get', ['request' => $request->all()]);
})->name('integrations.juno.notifications.get');
Route::post('/notifications/juno', function(Request $request) {
    \Illuminate\Support\Facades\Log::info('notifications.juno.post', ['request' => $request->all()]);
})->name('integrations.juno.notifications.post');
