<?php

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

// Route::get('/', function () {
//     //return view('welcome');
//     return view('home');
    
// });

Auth::routes();


// route to process the form
//Route::post('login', array('uses' => 'HomeController@doLogin'));
 Route::get('/', array('as' => 'loginpage', 'routegroup' => 'login', 'uses' => 'Auth\LoginController@index'));
 Route::post('/postlogin', array('as' => 'postlogin', 'routegroup' => 'login', 'uses' => 'Auth\LoginController@postLogin'));

// Route::post('login', 'Auth\LoginController@doLogin')->name('login');

Route::get('logout', '\App\Http\Controllers\Auth\LoginController@logout');

 // Password Reset Routes...
Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('forgotpassword');
Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm');
Route::post('password/reset', 'Auth\ResetPasswordController@reset')->name('password.reset');
Route::get('/home', 'HomeController@index')->name('dashboard');

Route::get('/usermanagementList', 'admin\UserController@index')->name('usermanagement');
//Route::post('/userdetails', 'admin\UserController@userdetails')->name('userData');
Route::get('user/delete/{id}', array('as' => 'user.delete', 'routegroup' => 'grp_admin_user', 'uses' => 'admin\UserController@delete'));
Route::post('/user/create', [ 'as' => 'user.create', 'uses' => 'admin\UserController@create']);
Route::post('/adduser', 'admin\UserController@adduser')->name('adduser');
Route::get('delete/{id}', array('as' => 'user.delete', 'routegroup' => 'grp_admin_user', 'uses' => 'admin\UserController@delete'));
Route::post('update', array('as' => 'user.update', 'routegroup' => 'grp_admin_user', 'uses' => 'admin\UserController@update'));
Route::post('search', array('as' => 'user.search', 'routegroup' => 'grp_admin_user', 'uses' => 'admin\UserController@search'));
Route::get('export', array('as' => 'user.csv', 'routegroup' => 'grp_admin_user', 'uses' => 'admin\UserController@csv'));
//Route::post('/user/adduser', [ 'as' => 'user.adduser', 'uses' => 'admin\UserController@adduser']);

Route::get('/brandList', 'admin\BrandController@index')->name('brand');
Route::get('brand/delete/{id}', array('as' => 'brand.delete', 'routegroup' => 'grp_admin_user', 'uses' => 'admin\BrandController@delete'));
Route::post('/brand/create', [ 'as' => 'brand.create', 'brand' => 'admin\BrandController@create']);
Route::post('/addbrand', 'admin\BrandController@addbrand')->name('addbrand');
// Route::get('delete/{id}', array('as' => 'brand.delete', 'routegroup' => 'grp_admin_user', 'uses' => 'admin\BrandController@delete'));
Route::post('brandsearch', array('as' => 'brand.brandsearch', 'routegroup' => 'grp_admin_user', 'uses' => 'admin\BrandController@search'));
Route::post('updatebrand', array('as' => 'brand.update', 'routegroup' => 'grp_admin_user', 'uses' => 'admin\BrandController@update'));
Route::get('/excel_export', 'ExportExcelController@index');
Route::get('/expert_excel/excel','ExportExcelController@excel')->name('export_excel.excel');
