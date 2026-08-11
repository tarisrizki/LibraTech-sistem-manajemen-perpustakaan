<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\Admin\LoanAdminController as AdminLoanController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\BookController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\LoanController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::post('/auth/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
    Route::post('/auth/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
    Route::get('/auth/me', [AuthController::class, 'me'])->middleware('auth:sanctum');

    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/books', [BookController::class, 'index']);
    Route::get('/books/{book}', [BookController::class, 'show']);

    Route::middleware('auth:sanctum')->group(function (): void {
        Route::get('/loans', [LoanController::class, 'index']);
        Route::post('/loans', [LoanController::class, 'store']);
    });

    Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function (): void {
        Route::get('/loans', [AdminLoanController::class, 'index']);
        Route::patch('/loans/{loan}/approve', [AdminLoanController::class, 'approve']);
        Route::patch('/loans/{loan}/reject', [AdminLoanController::class, 'reject']);
        Route::patch('/loans/{loan}/return', [AdminLoanController::class, 'return']);
    });
});
