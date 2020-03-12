<?php

use Illuminate\Http\Request;

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

Route::middleware('auth:api')->group(function () {
    Route::get('details', 'API\RegisterController@details');
});

Route::post('login', 'API\RegisterController@login');
Route::post('register', 'API\RegisterController@register');
Route::post('editProfile/{id}', 'API\RegisterController@editProfile')->name('editProfile');
//Route::get('userDetails','RegisterController@details');
