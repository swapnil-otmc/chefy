<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ContentController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\CouponController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

////For Login ///
Route::post('/users/loginOTP',[LoginController::class,'login'])->name('login');
Route::post('/users/loginCheck',[LoginController::class,'loginCheck'])->name('loginCheck');
Route::post('/users/logout',[LoginController::class,'logout'])->name('logout');
///end login process

// Home ///////
Route::post('/home/sliders',[HomeController::class,'getSlider'])->name('getSlider');
Route::post('/home/menu',[HomeController::class,'getMenu'])->name('getMenu');
/// end home


// Home Page data 
Route::post('/home/content',[CategoryController::class,'getHomePageContentList'])->name('getHomePageContentList');
Route::post('/home/moreContent',[CategoryController::class,'getMoreContentList'])->name('getMoreContentList');
Route::post('/home/menuContent' ,[CategoryController::class,'getMenuContentList'])->name('getMenuContentList');

/// End Home Page Data Process 


//----------Start  Home Content----------//
Route::post('/home/content/contentdetails',[ContentController::class,'getContentDetailByID'])->name('getContentDetailByID');
Route::post('/home/content/searchcontent',[ContentController::class,'searchContent'])->name('searchContent');
///////// end Home Content//////////

///// History Sectiom Start///// 
Route::post('/home/history/addhistory',[HistoryController::class,'addHistory'])->name('addHistory');
Route::post('home/history/gethistory',[HistoryController::class,'getHistory'])->name('getHistory');
///// History Section End Here////////////

//// watch Later Section Start///////
Route::post('home/continuewatch/addWatchlist',[HistoryController::class,'addWatchlist'])->name('addWatchlist');
Route::post('home/watchlater/getwatchlater',[HistoryController::class, 'getWatchlist'])->name('getWatchlist');
Route::post('home/watchlater/deleteWatchList',[HistoryController::class,'removeWatchlist'])->name('removeWatchlist');
/////// watch later section end here///////////

//// Continue Watching section start //////
Route::post('home/continuewatch/addcontinuewatching',[HistoryController::class,'addContinueWatching'])->name('addContinueWatching');
Route::post('home/continuewatch/getcontinuewatchinglist',[HistoryController::class,'getContinueWatching'])->name('getContinueWatching');
Route::post('home/continuewatch/deletecontinuewatchinglist',[HistoryController::class,'deleteContinueWatching'])->name('deleteContinueWatching');
//////Continue Watching Section end Here //////////

/////// User Profile Section Start Here///////
Route::post('users/getprofile',[UserController::class,'getProfile'])->name('getProfile');
Route::post('users/updateprofile',[UserController::class,'updateProfile'])->name('updateProfile');
Route::post('users/deactivate',[UserController::class,'deactivateProfile'])->name('deactivateProfile');
Route::post('users/activate',[UserController::class,'activateProfile'])->name('activateProfile');
////////////User Section end Here //////////////////

/// Payment section start ///////////////////
Route::post('payment/createOrder',[PaymentController::class,'initiateOrder'])->name('initiateOrder');
Route::post('payment/makePayment',[PaymentController::class,'makePayment'])->name('makePayment');
Route::post('payment/gettransactionhistory',[PaymentController::class,'getTransactionHistory'])->name('getTransactionHistory');
//////// Payment Section End///////////////////

///////Subcription Section Start ////////////////////
Route::post('/subscription/toggle',[LoginController::class,'subscriptionToggle'])->name('subscriptionToggle');
///////Subcription Section end Here ////////////

////// Coupon Section Start here////////////////
Route::post('/coupon/redeem',[CouponController::class,'activateCoupon'])->name('activateCoupon');
/////// Coupon Section End Here///////////////