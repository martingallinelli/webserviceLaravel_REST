<?php

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

// USUARIO
Route::post('user', 'App\Http\Controllers\UserController@store');
Route::post('login', 'App\Http\Controllers\UserController@login')->name('login');

// rutas protegidas de la API
Route::group(['middleware' => 'auth:api'], function () {
    Route::apiResource('directorio', 'App\Http\Controllers\DirectorioController');
    Route::post('logout', 'App\Http\Controllers\UserController@logout');
});
