<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\BannerController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\BrandController;
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


Route::get('getcategorieslist', [CategoryController::class, 'getcategorieslist']);
Route::get('getcategorybyid/{id}', [CategoryController::class, 'getcategorybyid']);
Route::get('getsubcategorieslist/{id}', [CategoryController::class, 'getsubcategorieslist']);
Route::get('getproductlist', [ProductController::class, 'getproductlist']);
Route::get('getproductbyid/{id}', [ProductController::class, 'getproductbyid']);
Route::get('getproductbycategoryid/{id}', [ProductController::class, 'getproductbycategoryid']);
Route::get('getis_featuredproduct', [ProductController::class, 'getis_featuredproduct']);
Route::get('getbannerlist', [BannerController::class, 'getbannerlist']);
Route::get('getbrandlist', [BrandController::class, 'getbrandlist']);
Route::post('userlogin', [UserController::class, 'userlogin']);
Route::get('getcatwithsubandsub',[CategoryController::class,'catsubsub']);
Route::get('getproductSearch/', [ProductController::class, 'getproductSearch']);
Route::get('getCateidProduct', [ProductController::class, 'getCateidProduct']);
Route::get('getSubCateidProduct', [ProductController::class, 'getSubCateidProduct']);
Route::get('getSubSubCateidProduct', [ProductController::class, 'getSubSubCateidProduct']);


Route::group( [ 'middleware' => ['auth:sanctum']], function () {
    Route:: get('user', [UserController::class, 'userauth']);
    Route:: post('savecart', [OrderController::class, 'savecart']);
    Route:: post('updatecart', [OrderController::class, 'updatecart']);
    Route:: post('removecart', [OrderController::class, 'removecart']);
    Route:: post('clearcart', [OrderController::class, 'clearcart']);
    Route:: get('getcartdata', [OrderController::class,'getcartdata']);
    Route:: post('placeorder', [OrderController::class, 'placeorder']);
    Route:: post('createrazorpayorder', [PaymentController::class, 'createOrder']);
    Route:: post('paychecksave', [PaymentController::class, 'paychecksave']);
    Route:: get('orderdetails/{orid}', [OrderController::class, 'orderdetails']);
    Route:: get('order/pdf/{orderNumber}', [OrderController::class, 'generateInvoicePdf'])->name('api.order.pdf');
    Route:: post('saveshippinginfo', [UserController::class, 'saveshippinginfo']);
    Route:: get('getshippinginfo', [UserController::class, 'getshippinginfo']);
});
