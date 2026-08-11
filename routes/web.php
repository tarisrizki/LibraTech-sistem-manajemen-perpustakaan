<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\LoanAdminController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ProfileController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\LoanController;
use App\Livewire\Admin\BookManager;
use App\Livewire\Admin\CategoryManager;
use App\Livewire\Admin\LoanManager;
use App\Livewire\Catalog\Index as CatalogIndex;
use App\Livewire\Catalog\Show as CatalogShow;
use Illuminate\Support\Facades\Route;

Route::get('/', CatalogIndex::class)->name('catalog.index');
Route::get('/books/{book}', CatalogShow::class)->name('catalog.show');

Route::middleware('guest')->group(function (): void {
    Route::get('/register', [RegisterController::class, 'show'])->name('register');
    Route::post('/register', [RegisterController::class, 'store'])->name('register.store');
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');
    Route::get('/auth/google', [GoogleController::class, 'redirect'])->name('auth.google');
    Route::get('/auth/google/callback', [GoogleController::class, 'callback'])->name('auth.google.callback');
});

Route::middleware('auth')->group(function (): void {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    Route::get('/loans', [LoanController::class, 'index'])->name('loans.index');
    Route::post('/loans', [LoanController::class, 'store'])->name('loans.store');
});

// Admin domain — Livewire + Flux (parallel safe: only admin namespace)
// GET pages via Livewire (BookManager / CategoryManager / LoanManager)
// POST actions retained via controller for backward compat with existing tests/api
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function (): void {
    Route::get('/books', BookManager::class)->name('books.index');
    Route::get('/categories', CategoryManager::class)->name('categories.index');
    Route::get('/loans', LoanManager::class)->name('loans.index');
    Route::post('/loans/{loan}/approve', [LoanAdminController::class, 'approve'])->name('loans.approve');
    Route::post('/loans/{loan}/reject', [LoanAdminController::class, 'reject'])->name('loans.reject');
    Route::post('/loans/{loan}/return', [LoanAdminController::class, 'return'])->name('loans.return');
});
