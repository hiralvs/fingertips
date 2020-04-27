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
   
   
    Route::post('addToFavorites', 'API\FavoriteController@addToFavorites');   
    Route::post('search', 'API\HomePageController@search');
    Route::post('changepassword', 'API\RegisterController@changePassword');  
    Route::get('rewards','API\RewardsController@rewards');
    Route::get('purchases','API\OrderController@purchases');
    Route::get('editProfile', 'API\RegisterController@editProfile');
    Route::post('updateProfile','API\RegisterController@updateProfile');
    Route::post('checkinUser','API\CheckinController@checkinUser');
    Route::get('checkoutUser','API\CheckinController@checkoutUser');
    Route::post('addToCart','API\ProductController@addToCart');
    Route::post('updateCart','API\ProductController@updateCart');
    Route::post('deleteCart','API\ProductController@deleteCart');
    Route::post('createcard','API\StripePaymentController@stripeCard');
    Route::get('retrieveCustomer','API\StripePaymentController@retrieveStripeCustomer');
    Route::post('deleteCard','API\StripePaymentController@deleteCard');
    Route::post('stripeCharge','API\StripePaymentController@stripeCharge');
    Route::post('deleteNotification','API\NotificationController@deleteNotification');
    Route::get('getCart','API\ProductController@getCart');
});


Route::post('login', 'API\RegisterController@login');
Route::get('verify/{token}', 'API\RegisterController@verifyEmail')->name('verify');
Route::get('resendverifyEmail/{id}', 'API\RegisterController@resendverifyEmail')->name('resendverifyEmail');
Route::post('register', 'API\RegisterController@register');
Route::post('otp', 'API\RegisterController@otpverify');
Route::post('forgot/password', 'API\ForgotPasswordController@forgotPassword');    
Route::post('logout', 'API\RegisterController@logout');    
Route::post('resetPassword', 'API\RegisterController@resetPassword');  

Route::get('dashboard', 'API\DashboardController@dashboard');
Route::get('notification','API\NotificationController@notification');
Route::post('events', 'API\HomePageController@eventListing');
Route::post('malls', 'API\HomePageController@mallListing');
Route::post('attractions', 'API\HomePageController@attractionListing');
Route::get('featuredEvents', 'API\HomePageController@featuredEvents');
Route::get('featuredShops', 'API\HomePageController@featuredShops');
Route::get('featuredMalls', 'API\HomePageController@featuredMalls');
Route::get('featuredAttraction', 'API\HomePageController@featuredAttraction');
Route::get('highlightsnflashsale/{id}/{type}', 'API\HomePageController@highlightsnflashsale');
Route::get('flashsale', 'API\HomePageController@flashsale');
Route::get('banners/{type}', 'API\HomePageController@banners');
Route::get('eventsdetails/{id}', 'API\HomePageController@eventsDetails');
Route::get('mallDetails/{id}', 'API\HomePageController@mallDetails');
Route::get('attractionDetails/{id}', 'API\HomePageController@attractionDetails');
Route::post('search', 'API\HomePageController@search');
Route::get('privacypolicy', 'API\HomePageController@privacypolicy');    
Route::get('termsandcondition', 'API\HomePageController@termsandcondition');    
Route::post('homepagedetails', 'API\HomePageController@homepagedetails');  
Route::get('eventFavorites', 'API\FavoriteController@eventFavorites');
Route::get('mallsFavorites', 'API\FavoriteController@mallsFavorites');
Route::get('attractionFavorites', 'API\FavoriteController@attractionFavorites');
Route::get('products', 'API\ProductController@products');
Route::get('products_variant/{id}', 'API\ProductController@products_variant');
Route::get('nearbyESMA', 'API\HomePageController@nearbyESMA');
Route::post('eventFilter','API\HomePageController@eventFilter');
Route::post('eventFilter','API\HomePageController@eventFilter');
Route::post('shopmallFilter','API\HomePageController@shopmallFilter');
Route::post('attractionFilter','API\HomePageController@attractionFilter');
Route::get('filters', 'API\HomePageController@filters');
Route::get('retrievefaq','API\NotificationController@faq');



//Route::get('userDetails','RegisterController@details');
