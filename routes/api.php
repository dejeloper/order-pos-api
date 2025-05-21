<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Middleware\IsUserAuth;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::get('users', [AuthController::class, 'getAllUsers']);


// Protected routes
Route::middleware([IsUserAuth::class])->group(function () {
    Route::controller(AuthController::class)->group(function () {
        Route::post('logout', 'logout');
        Route::get('me', 'getUser');
    });

    Route::controller(ProductController::class)->prefix('products')->group(function () {
        // Ver productos
        Route::get('/', 'index')->middleware('check_permission:view_products');
        Route::get('/disabled', 'indexDisabled')->middleware('check_permission:view_disabled_products');
        Route::get('/{id}', 'show')->middleware('check_permission:view_products');
        Route::get('/trashed/{id}', 'showTrashed')->middleware('check_permission:view_trashed_product');
        // Crear
        Route::post('/', 'store')->middleware('check_permission:create_products');
        // Actualizar - Restaurar
        Route::patch('/{id}', 'update')->middleware('check_permission:edit_products');
        Route::patch('/restore/{id}', 'restore')->middleware('check_permission:restore_products');
        // Delete
        Route::delete('/{id}', 'destroy')->middleware('check_permission:delete_products');
        Route::delete('/force/{id}', 'forceDelete')->middleware('check_permission:force_delete_products');
    });
});
