<?php

use App\Http\Controllers\Auth\Admin\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Product\ProductController;
use App\Http\Controllers\Category\CategoryController;

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

Route::prefix('auth')->name('admin.auth.')->group(function () {
    Route::post('login', [AuthController::class, 'login'])->name('login');
    Route::middleware('auth:admin_api')->group(function () {
        Route::post('refresh', [AuthController::class, 'refresh'])->name('refresh');
        Route::post('logout', [AuthController::class, 'logout'])->name('logout');
        Route::post('check', [AuthController::class, 'checkAuth'])->name('checkAuth');
    });
});

Route::middleware('auth:admin_api')->group(function(){
    Route::prefix('product')->name('admin.product.')->group(function () {
        Route::post('add', [ProductController::class, 'add'])->name('add');
        Route::put('update/{id}', [ProductController::class, 'updateProduct'])->name('update');
        Route::delete('delete/{id}', [ProductController::class, 'deleteProduct'])->name('delete');
    });

    Route::prefix('category')->name('admin.category.')->group(function () {
        Route::post('add', [CategoryController::class, 'addCategory'])->name('add');
        Route::put('update/{id}', [CategoryController::class, 'updateCategory'])->name('update');
        Route::delete('delete/{id}', [CategoryController::class, 'deleteCategory'])->name('delete');
        Route::post('view',[CategoryController::class,'viewCategory'])->name('view');
    });
});

