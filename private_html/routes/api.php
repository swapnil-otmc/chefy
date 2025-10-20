<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ContentController;

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