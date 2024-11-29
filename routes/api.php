<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\BannerController;
use App\Http\Controllers\Api\UserController;
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

Route::middleware('auth:sanctum')->get('/user', [UserController::class, 'userauth'] );
Route::get('getcategorieslist', [CategoryController::class, 'getcategorieslist']);
Route::get('getcategorybyid/{id}', [CategoryController::class, 'getcategorybyid']);
Route::get('getsubcategorieslist/{id}', [CategoryController::class, 'getsubcategorieslist']);
Route::get('getproductlist', [ProductController::class, 'getproductlist']);
Route::get('getproductbyid/{id}', [ProductController::class, 'getproductbyid']);
Route::get('getproductbycategoryid/{id}', [ProductController::class, 'getproductbycategoryid']);
Route::get('getis_featuredproduct', [ProductController::class, 'getis_featuredproduct']);
Route::get('getbannerlist', [BannerController::class, 'getbannerlist']);
Route::get('getbrandlist', [\App\Http\Controllers\Api\BrandController::class, 'getbrandlist']);
Route::post('userlogin', [UserController::class, 'userlogin']);
Route::get('getcatwithsubandsub',[CategoryController::class,'catsubsub']);
Route::get('getproductSearch/', [ProductController::class, 'getproductSearch']);
Route::get('getCateidProduct/', [ProductController::class, 'getCateidProduct']);
Route::get('getSubCateidProduct/', [ProductController::class, 'getSubCateidProduct']);
Route::get('getSubSubCateidProduct/', [ProductController::class, 'getSubSubCateidProduct']);




