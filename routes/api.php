<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\SubCategoryController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

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

// Public routes
Route::post('/register-admin', [AdminController::class, 'register']);

Route::post('/login-admin', [AdminController::class, 'login'])->name('login-admin');

Route::post('/register-customer', [CustomerController::class, 'register']);

Route::post('/login-customer', [CustomerController::class, 'login'])->name('login-customer');

Route::get('/categories', [CategoryController::class, 'index']);

Route::get('/subcategories', [SubCategoryController::class, 'index']);

Route::get('/transaction/{id}', [TransactionController::class, 'show']);

// Protected routes
Route::group(['middleware' => ['auth:admin']], function () {
    Route::post('/category', [CategoryController::class, 'store']);

    Route::post('/subcategory', [SubCategoryController::class, 'store']);

    Route::post('/transaction', [TransactionController::class, 'store']);

    Route::post('/payment', [PaymentController::class, 'store']);

    Route::get('/transaction-payments/{id}',
        [TransactionController::class, 'transaction_payments']);

    Route::post('/logout-admin', [AdminController::class, 'logout']);
});

Route::group(['middleware' => ['auth:customer']], function () {
    Route::post('/transactions', [CustomerController::class, 'transactions']);

    Route::post('/logout-customer', [CustomerController::class, 'logout']);
});

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
