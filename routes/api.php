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
    Route::get('editProfile', 'API\RegisterController@editProfile');
    Route::get('events', 'API\HomePageController@eventListing');
    Route::get('malls', 'API\HomePageController@mallListing');
    Route::get('attractions', 'API\HomePageController@attractionListing');
    Route::post('updateProfile','API\RegisterController@updateProfile');
    Route::get('rewards','API\RewardsController@rewards');
    Route::get('notification','API\NotificationController@notification');
    Route::get('purchases','API\OrderController@purchases');
    Route::get('featuredEvents', 'API\HomePageController@featuredEvents');
    Route::get('featuredShops', 'API\HomePageController@featuredShops');
    Route::get('featuredMalls', 'API\HomePageController@featuredMalls');
    Route::get('featuredAttraction', 'API\HomePageController@featuredAttraction');
    Route::get('highlights/{id}/{type}', 'API\HomePageController@highlights');
    Route::get('flashsale', 'API\HomePageController@flashsale');
    Route::get('banners/{type}', 'API\HomePageController@banners');
    Route::get('eventsdetails/{id}', 'API\HomePageController@eventsDetails');
    Route::get('mallDetails/{id}', 'API\HomePageController@mallDetails');
    Route::get('attractionDetails/{id}', 'API\HomePageController@attractionDetails');
    Route::post('addToFavorites/{id}', 'API\FavoriteController@addToFavorites');
    Route::get('eventFavorites/{id}', 'API\FavoriteController@eventFavorites');
    Route::get('mallsFavorites/{id}', 'API\FavoriteController@mallsFavorites');
    Route::get('attractionFavorites/{id}', 'API\FavoriteController@attractionFavorites');
    //Route::put('updateProfileId/{id}','API\RegisterController@updateProfile');
});


Route::post('login', 'API\RegisterController@login');
Route::post('register', 'API\RegisterController@register');
Route::post('otp', 'API\RegisterController@otpverify');
Route::post('forgot/password', 'API\ForgotPasswordController@forgotPassword');    
Route::post('changeassword', 'API\ForgotPasswordController@changePassword');    
Route::post('privacypolicy', 'API\RegisterController@privacypolicy');    
Route::post('logout', 'API\RegisterController@logout');    

//Route::get('userDetails','RegisterController@details');
