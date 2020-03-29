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

// Route::post('login', 'Auth\LoginController@doLogin')->name('login');

Route::get('logout', '\App\Http\Controllers\Auth\LoginController@logout');
Route::post('/postlogin', array('as' => 'postlogin', 'routegroup' => 'login', 'uses' => 'Auth\LoginController@postLogin'));

 // Password Reset Routes...
Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('forgotpassword');
Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm');
Route::post('password/reset', 'Auth\ResetPasswordController@reset')->name('password.reset');
Route::get('/home', 'HomeController@index')->name('dashboard');

Route::get('/usermanagementList', 'admin\UserController@index')->name('usermanagement');
//Route::post('/userdetails', 'admin\UserController@userdetails')->name('userData');
//Route::get('user/delete/{id}', array('as' => 'user.delete', 'routegroup' => 'grp_admin_user', 'uses' => 'admin\UserController@delete'));
//Route::post('/user/create', [ 'as' => 'user.create', 'uses' => 'admin\UserController@create']);
Route::post('/adduser', 'admin\UserController@adduser')->name('adduser');
Route::get('delete/{id}', array('as' => 'user.delete', 'routegroup' => 'grp_admin_user', 'uses' => 'admin\UserController@delete'));
Route::post('update', array('as' => 'user.update', 'routegroup' => 'grp_admin_user', 'uses' => 'admin\UserController@update'));
Route::post('search', array('as' => 'user.search', 'routegroup' => 'grp_admin_user', 'uses' => 'admin\UserController@search'));
Route::get('export', array('as' => 'user.csv', 'routegroup' => 'grp_admin_user', 'uses' => 'admin\UserController@csv'));
//Route::post('/user/adduser', [ 'as' => 'user.adduser', 'uses' => 'admin\UserController@adduser']);


Route::get('/usermanagementList', 'admin\UserController@index')->name('usermanagement');
//Route::post('/userdetails', 'admin\UserController@userdetails')->name('userData');
//Route::get('user/delete/{id}', array('as' => 'user.delete', 'routegroup' => 'grp_admin_user', 'uses' => 'admin\UserController@delete'));
//Route::post('/user/create', [ 'as' => 'user.create', 'uses' => 'admin\UserController@create']);
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

Route::get('/dashboard', 'admin\DashboardController@index')->name('dashboard');

Route::get('/products', 'admin\ProductController@index')->name('products');
Route::post('/addproduct', 'admin\ProductController@addProducts')->name('addproduct');
Route::post('productsearch', array('as' => 'product.productsearch', 'routegroup' => 'grp_admin_user', 'uses' => 'admin\ProductController@search'));
Route::post('updateproduct', array('as' => 'product.update', 'routegroup' => 'grp_admin_user', 'uses' => 'admin\ProductController@update'));
Route::get('product/delete/{id}', array('as' => 'product.delete', 'routegroup' => 'grp_admin_user', 'uses' => 'admin\ProductController@delete'));

Route::get('products_variant/{id}', 'admin\ProductController@product_variant')->name('products_variant');
Route::post('/addproductvariant', 'admin\ProductController@addProductsVariant')->name('addproductvariant');
Route::post('productvariantsearch', array('as' => 'productvariant.productsearch', 'routegroup' => 'grp_admin_user', 'uses' => 'admin\ProductController@variantsearch'));
Route::post('updateproductvariant', array('as' => 'productvariant.update', 'routegroup' => 'grp_admin_user', 'uses' => 'admin\ProductController@variantupdate'));
Route::get('productvariant/delete/{id}', array('as' => 'productvariant.delete', 'routegroup' => 'grp_admin_user', 'uses' => 'admin\ProductController@variantdelete'));


Route::get('/excel_export', 'ExportExcelController@index');
Route::get('/expert_excel/excel','ExportExcelController@excel')->name('export_excel.excel');

Route::get('download', function(){
    return Excel::download(new BrandsExport, 'brands.csv');
});



//category route
Route::get('/emacategory', 'admin\CategoryController@index')->name('emacategory');
Route::post('/addCategory', 'admin\CategoryController@addCategory')->name('addCategory');
Route::get('categorydelete/{id}', array('as' => 'category.delete', 'routegroup' => 'grp_admin_user', 'uses' => 'admin\CategoryController@delete'));
Route::post('categoryupdate', array('as' => 'category.update', 'routegroup' => 'grp_admin_user', 'uses' => 'admin\CategoryController@update'));
Route::get('/productcategory', 'admin\CategoryController@productcategory')->name('productcategory');

// Area route
Route::get('/area', 'admin\AreaController@index')->name('area');
Route::post('/addArea', 'admin\AreaController@addArea')->name('addArea');
Route::get('areadelete/{id}', array('as' => 'area.delete', 'routegroup' => 'grp_admin_user', 'uses' => 'admin\AreaController@delete'));
Route::post('areaupdate', array('as' => 'area.update', 'routegroup' => 'grp_admin_user', 'uses' => 'admin\AreaController@update'));

// Shops and malls route
Route::get('/shopsandmalls', 'admin\ShopsandMallsController@index')->name('shopsmalls');
Route::post('/addShopsandMalls', 'admin\ShopsandMallsController@addShopsandMalls')->name('addShopsandMalls');
Route::get('shopsmallsdelete/{id}', array('as' => 'shopsmalls.delete', 'routegroup' => 'grp_admin_user', 'uses' => 'admin\ShopsandMallsController@delete'));
Route::post('shopsmallsupdate', array('as' => 'shopsmalls.update', 'routegroup' => 'grp_admin_user', 'uses' => 'admin\ShopsandMallsController@update'));
Route::post('shopsmallssearch', array('as' => 'shopsmalls.search', 'routegroup' => 'grp_admin_user', 'uses' => 'admin\ShopsandMallsController@search'));

//
Route::get('/banner', 'admin\BannerController@index')->name('banner');
Route::post('/addBanner', 'admin\BannerController@addBanner')->name('addBanner');
Route::post('bannerupdate', array('as' => 'banner.update', 'routegroup' => 'grp_admin_user', 'uses' => 'admin\BannerController@update'));
Route::get('bannerdelete/{id}', array('as' => 'banner.delete', 'routegroup' => 'grp_admin_user', 'uses' => 'admin\BannerController@delete'));


Route::get('/event', 'admin\EventsController@index')->name('event');
Route::post('/addEvents', 'admin\EventsController@addEvents')->name('addEvents');
Route::post('eventsupdate', array('as' => 'events.update', 'routegroup' => 'grp_admin_user', 'uses' => 'admin\EventsController@update'));
Route::get('eventsdelete/{id}', array('as' => 'events.delete', 'routegroup' => 'grp_admin_user', 'uses' => 'admin\EventsController@delete'));
Route::post('eventssearch', array('as' => 'events.search', 'routegroup' => 'grp_admin_user', 'uses' => 'admin\EventsController@search'));

Route::get('/rewards', 'admin\RewardsController@index')->name('rewards');
Route::get('rewardsdelete/{id}', array('as' => 'rewards.delete', 'routegroup' => 'grp_admin_user', 'uses' => 'admin\RewardsController@delete'));
Route::post('rewardssearch', array('as' => 'rewards.search', 'routegroup' => 'grp_admin_user', 'uses' => 'admin\RewardsController@search'));

Route::get('/privacy', 'admin\PrivacyController@index')->name('privacy');
Route::post('/addPrivacy', 'admin\PrivacyController@addPrivacy')->name('addPrivacy');
Route::post('privacyupdate', array('as' => 'privacy.update', 'routegroup' => 'grp_admin_user', 'uses' => 'admin\PrivacyController@update'));
Route::get('privacydelete/{id}', array('as' => 'privacy.delete', 'routegroup' => 'grp_admin_user', 'uses' => 'admin\PrivacyController@delete'));

Route::get('/attractions', 'admin\AttractionController@index')->name('attractions');
Route::post('/addAttractions', 'admin\AttractionController@addAttractions')->name('addAttractions');
Route::post('attractionsupdate', array('as' => 'attraction.update', 'routegroup' => 'grp_admin_user', 'uses' => 'admin\AttractionController@update'));
Route::get('attractionsdelete/{id}', array('as' => 'attraction.delete', 'routegroup' => 'grp_admin_user', 'uses' => 'admin\AttractionController@delete'));
Route::post('attractionssearch', array('as' => 'attraction.search', 'routegroup' => 'grp_admin_user', 'uses' => 'admin\AttractionController@search'));

Route::get('/notifications', 'admin\NotificationController@index')->name('notifications');
Route::get('notificationsdelete/{id}', array('as' => 'notifications.delete', 'routegroup' => 'grp_admin_user', 'uses' => 'admin\NotificationController@delete'));
Route::post('/addNotifications', 'admin\NotificationController@addNotifications')->name('addNotifications');
Route::post('notificationsupdate', array('as' => 'notifications.update', 'routegroup' => 'grp_admin_user', 'uses' => 'admin\NotificationController@update'));
Route::post('notificationssearch', array('as' => 'notifications.search', 'routegroup' => 'grp_admin_user', 'uses' => 'admin\NotificationController@search'));

Route::get('/tax', 'admin\TaxController@index')->name('tax');
Route::get('taxdelete/{id}', array('as' => 'tax.delete', 'routegroup' => 'grp_admin_user', 'uses' => 'admin\TaxController@delete'));
Route::post('/addTax', 'admin\TaxController@addTax')->name('addTax');
Route::post('taxupdate', array('as' => 'tax.update', 'routegroup' => 'grp_admin_user', 'uses' => 'admin\TaxController@update'));

Route::get('/rewardsetting', 'admin\RewardSettingController@index')->name('rewardsetting');
Route::get('rewardsettingdelete/{id}', array('as' => 'rewardsetting.delete', 'routegroup' => 'grp_admin_user', 'uses' => 'admin\RewardSettingController@delete'));
Route::post('/addRewardSetting', 'admin\RewardSettingController@addRewardSetting')->name('addRewardSetting');
Route::post('rewardsettingupdate', array('as' => 'rewardsetting.update', 'routegroup' => 'grp_admin_user', 'uses' => 'admin\RewardSettingController@update'));

Route::get('/floor', 'admin\FloorController@index')->name('floor');
Route::get('floordelete/{id}', array('as' => 'floor.delete', 'routegroup' => 'grp_admin_user', 'uses' => 'admin\FloorController@delete'));
Route::post('/addFloor', 'admin\FloorController@addFloor')->name('addFloor');
Route::post('floorupdate', array('as' => 'floor.update', 'routegroup' => 'grp_admin_user', 'uses' => 'admin\FloorController@update'));

Route::get('/help', 'admin\HelpController@index')->name('help');
Route::get('helpdelete/{id}', array('as' => 'help.delete', 'routegroup' => 'grp_admin_user', 'uses' => 'admin\HelpController@delete'));
Route::post('/addHelp', 'admin\HelpController@addHelp')->name('addHelp');
Route::post('helpupdate', array('as' => 'help.update', 'routegroup' => 'grp_admin_user', 'uses' => 'admin\HelpController@update'));

Route::get('/loginprivacy', 'admin\LoginPrivacyController@index')->name('loginprivacy');
Route::get('loginprivacydelete/{id}', array('as' => 'loginprivacy.delete', 'routegroup' => 'grp_admin_user', 'uses' => 'admin\LoginPrivacy@delete'));
Route::post('/addLoginPrivacy', 'admin\LoginPrivacy@addLoginPrivacy')->name('addLoginPrivacy');
Route::post('loginprivacyupdate', array('as' => 'loginprivacy.update', 'routegroup' => 'grp_admin_user', 'uses' => 'admin\LoginPrivacy@update'));


Route::get('/mallbrands', 'admin\CommonBrandsController@index')->name('mallbrands');
Route::post('/addMallBrand', 'admin\CommonBrandsController@addMallBrand')->name('addMallBrand');
Route::post('mallbrandsupdate', array('as' => 'mallbrands.update', 'routegroup' => 'grp_admin_user', 'uses' => 'admin\CommonBrandsController@update'));
Route::get('mallbrandsdelete/{id}', array('as' => 'mallbrands.delete', 'routegroup' => 'grp_admin_user', 'uses' => 'admin\CommonBrandsController@delete'));
Route::post('mallbrandssearch', array('as' => 'mallbrands.search', 'routegroup' => 'grp_admin_user', 'uses' => 'admin\CommonBrandsController@search'));

Route::get('/eventbrands', 'admin\CommonBrandsController@index')->name('eventbrands');
Route::post('/addEventBrand', 'admin\CommonBrandsController@addMallBrand')->name('addEventBrand');
Route::post('eventbrandsupdate', array('as' => 'eventbrands.update', 'routegroup' => 'grp_admin_user', 'uses' => 'admin\CommonBrandsController@update'));
Route::get('eventbrandsdelete/{id}', array('as' => 'eventbrands.delete', 'routegroup' => 'grp_admin_user', 'uses' => 'admin\CommonBrandsController@delete'));
Route::post('eventbrandssearch', array('as' => 'eventbrands.search', 'routegroup' => 'grp_admin_user', 'uses' => 'admin\CommonBrandsController@search'));

Route::get('/attractionbrands', 'admin\CommonBrandsController@index')->name('attractionbrands');
Route::post('/addAttractionBrand', 'admin\CommonBrandsController@addMallBrand')->name('addAttractionBrand');
Route::post('attractionbrandsupdate', array('as' => 'attractionbrands.update', 'routegroup' => 'grp_admin_user', 'uses' => 'admin\CommonBrandsController@update'));
Route::get('attractionbrandsdelete/{id}', array('as' => 'attractionbrands.delete', 'routegroup' => 'grp_admin_user', 'uses' => 'admin\CommonBrandsController@delete'));
Route::post('attractionbrandssearch', array('as' => 'attractionbrands.search', 'routegroup' => 'grp_admin_user', 'uses' => 'admin\CommonBrandsController@search'));

Route::get('/mallslider', 'admin\CommonSliderController@index')->name('mallslider');
Route::post('/addMallSlider', 'admin\CommonSliderController@addCommonSlider')->name('addMallSlider');
Route::post('mallsliderupdate', array('as' => 'mallslider.update', 'routegroup' => 'grp_admin_user', 'uses' => 'admin\CommonSliderController@update'));
Route::get('mallsliderdelete/{id}', array('as' => 'mallslider.delete', 'routegroup' => 'grp_admin_user', 'uses' => 'admin\CommonSliderController@delete'));
Route::post('mallslidersearch', array('as' => 'mallslider.search', 'routegroup' => 'grp_admin_user', 'uses' => 'admin\CommonSliderController@search'));

Route::get('/eventslider', 'admin\CommonSliderController@index')->name('eventslider');
Route::post('/addEventSlider', 'admin\CommonSliderController@addCommonSlider')->name('addEventSlider');
Route::post('eventsliderupdate', array('as' => 'eventslider.update', 'routegroup' => 'grp_admin_user', 'uses' => 'admin\CommonSliderController@update'));
Route::get('eventsliderdelete/{id}', array('as' => 'eventslider.delete', 'routegroup' => 'grp_admin_user', 'uses' => 'admin\CommonSliderController@delete'));
Route::post('eventslidersearch', array('as' => 'eventslider.search', 'routegroup' => 'grp_admin_user', 'uses' => 'admin\CommonSliderController@search'));

Route::get('/attractionslider', 'admin\CommonSliderController@index')->name('attractionslider');
Route::post('/addAttractionSlider', 'admin\CommonSliderController@addCommonSlider')->name('addAttractionSlider');
Route::post('attractionsliderupdate', array('as' => 'attractionslider.update', 'routegroup' => 'grp_admin_user', 'uses' => 'admin\CommonSliderController@update'));
Route::get('attractionsliderdelete/{id}', array('as' => 'attractionslider.delete', 'routegroup' => 'grp_admin_user', 'uses' => 'admin\CommonSliderController@delete'));
Route::post('attractionslidersearch', array('as' => 'attractionslider.search', 'routegroup' => 'grp_admin_user', 'uses' => 'admin\CommonSliderController@search'));

Route::get('/trending', 'admin\TrendingController@index')->name('trending');
Route::post('/addTrending', 'admin\TrendingController@addTrending')->name('addTrending');
Route::post('trendingupdate', array('as' => 'trending.update', 'routegroup' => 'grp_admin_user', 'uses' => 'admin\TrendingController@update'));
Route::get('trendingdelete/{id}', array('as' => 'trending.delete', 'routegroup' => 'grp_admin_user', 'uses' => 'admin\TrendingController@delete'));
Route::post('trendingsearch', array('as' => 'trending.search', 'routegroup' => 'grp_admin_user', 'uses' => 'admin\TrendingController@search'));

Route::get('/sponsors', 'admin\SponsorController@index')->name('sponsors');
Route::post('/addSponsors', 'admin\SponsorController@addSponsors')->name('addSponsors');
Route::get('sponsorsdelete/{id}', array('as' => 'sponsors.delete', 'routegroup' => 'grp_admin_user', 'uses' => 'admin\SponsorController@delete'));
Route::post('sponsorsupdate', array('as' => 'sponsors.update', 'routegroup' => 'grp_admin_user', 'uses' => 'admin\SponsorController@update'));
Route::post('sponsorssearch', array('as' => 'sponsors.search', 'routegroup' => 'grp_admin_user', 'uses' => 'admin\SponsorController@search'));

Route::get('/directory', 'admin\DirectoryController@index')->name('directory');
Route::post('/addDirectory', 'admin\DirectoryController@addDirectory')->name('addDirectory');
Route::get('directorydelete/{id}', array('as' => 'directory.delete', 'routegroup' => 'grp_admin_user', 'uses' => 'admin\DirectoryController@delete'));
Route::post('directoryupdate', array('as' => 'directory.update', 'routegroup' => 'grp_admin_user', 'uses' => 'admin\DirectoryController@update'));
Route::post('directorysearch', array('as' => 'directory.search', 'routegroup' => 'grp_admin_user', 'uses' => 'admin\DirectoryController@search'));

Route::get('/highlights', 'admin\CommonhighlightController@index')->name('highlights');
Route::post('/addHighlights', 'admin\CommonhighlightController@addHighlights')->name('addHighlights');
Route::get('highlightsdelete/{id}', array('as' => 'highlights.delete', 'routegroup' => 'grp_admin_user', 'uses' => 'admin\CommonhighlightController@delete'));
Route::post('highlightsupdate', array('as' => 'highlights.update', 'routegroup' => 'grp_admin_user', 'uses' => 'admin\CommonhighlightController@update'));
Route::post('highlightssearch', array('as' => 'highlights.search', 'routegroup' => 'grp_admin_user', 'uses' => 'admin\CommonhighlightController@search'));

Route::get('/flashsale', 'admin\FlashsaleController@index')->name('flashsale');

Route::get('/mallphotos', 'admin\CommonPhotosController@index')->name('mallphotos');
Route::post('/addMallPhotos', 'admin\CommonPhotosController@addPhotos')->name('addMallPhotos');
Route::get('mallphotosdelete/{id}', array('as' => 'mallphotos.delete', 'routegroup' => 'grp_admin_user', 'uses' => 'admin\CommonPhotosController@delete'));
Route::post('mallphotosupdate', array('as' => 'mallphotos.update', 'routegroup' => 'grp_admin_user', 'uses' => 'admin\CommonPhotosController@update'));

Route::get('/eventphotos', 'admin\CommonPhotosController@index')->name('eventphotos');
Route::post('/addEventPhotos', 'admin\CommonPhotosController@addPhotos')->name('addEventPhotos');
Route::get('eventphotosdelete/{id}', array('as' => 'eventphotos.delete', 'routegroup' => 'grp_admin_user', 'uses' => 'admin\CommonPhotosController@delete'));
Route::post('eventphotosupdate', array('as' => 'eventphotos.update', 'routegroup' => 'grp_admin_user', 'uses' => 'admin\CommonPhotosController@update'));

Route::get('/attractionphotos', 'admin\CommonPhotosController@index')->name('attractionphotos');
Route::post('/addAttractionPhotos', 'admin\CommonPhotosController@addPhotos')->name('addAttractionPhotos');
Route::get('attractionphotosdelete/{id}', array('as' => 'attractionphotos.delete', 'routegroup' => 'grp_admin_user', 'uses' => 'admin\CommonPhotosController@delete'));
Route::post('attractionphotosupdate', array('as' => 'attractionphotos.update', 'routegroup' => 'grp_admin_user', 'uses' => 'admin\CommonPhotosController@update'));


// Route::get('/checkin', 'admin\LoginPrivacyController@index')->name('checkin');
// Route::get('/orders', 'admin\LoginPrivacyController@index')->name('orders');
// Route::get('/sliderimage', 'admin\LoginPrivacyController@index')->name('sliderimage');
// Route::get('/mapimage', 'admin\LoginPrivacyController@index')->name('mapimage');