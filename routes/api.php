<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SubCategoryController;
use App\Http\Controllers\TransactionController;

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

// public 
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/subcategories', [SubCategoryController::class, 'index']);
Route::get('/transaction/{id}', [TransactionController::class, 'show']);

// to be protected
Route::post('/category', [CategoryController::class, 'store']);
Route::post('/subcategory', [SubCategoryController::class, 'store']);
Route::post('/transaction', [TransactionController::class, 'store']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});