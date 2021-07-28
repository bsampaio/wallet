<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//Route::get('/', function () {
//    return view('welcome');
//});
//
//Auth::routes();
//
//Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


Route::get('docs', function() {
    return view('scribe.index');
})->middleware('auth')->excludedMiddleware(['json.response', 'cors']);
Route::get('login', function() {
    return view('auth.login');
})->name('login');
Route::post('login', function() {

});
Route::get('docs/postman', function() {
    return ['error' => 'Not enabled'];
})->name('scribe.postman');
Route::get('docs/openapi', function() {
    return ['error' => 'Not enabled'];
})->name('scribe.openapi');