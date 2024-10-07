<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductController;
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

Route::get('getcategorieslist', [CategoryController::class, 'getcategorieslist']);
Route::get('getcategorybyid/{id}', [CategoryController::class, 'getcategorybyid']);
Route::get('getsubcategorieslist/{id}', [CategoryController::class, 'getsubcategorieslist']);
Route::get('getproductlist', [ProductController::class, 'getproductlist']);
Route::get('getproductbyid/{id}', [ProductController::class, 'getproductbyid']);
Route::get('getproductbycategoryid/{id}', [ProductController::class, 'getproductbycategoryid']);
Route::get('getis_featuredproduct', [ProductController::class, 'getis_featuredproduct']);


