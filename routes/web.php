<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProspectController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Route non protégé
Route::get('/', [AuthController::class, 'index'])->name('auth.index');
Route::post('/auth/login', [AuthController::class, 'authenticate'])->name('auth.authenticate');

Route::get('/orders/{commande}/confirm', [OrderController::class, 'showCgv'])->name('orders.showCgv');
Route::post('/orders/{commande}/confirm', [OrderController::class, 'validateCgv'])->name('orders.confirm');

// Route protégées
Route::middleware('auth')->group(function () {
    
    // Route lié à l'authentification
    Route::post('/auth/logout', [AuthController::class, 'logout'])->name('auth.logout');
    
    // Route lié aux Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
    
    // Route lié aux Prospects
    Route::prefix('prospect')->group(function () {
        Route::get('/', [ProspectController::class, 'index'])->name('prospect.index');
        Route::post('/create', [ProspectController::class, 'create'])->name('prospect.create');
        Route::put('/edit/{bcuser}', [ProspectController::class, 'edit'])->name('prospect.edit');
        Route::delete('/delete/{bcuser}', [ProspectController::class, 'delete'])->name('prospect.delete');
    });

    // Route lié aux commandes
    Route::prefix('orders')->group(function () {
        Route::get('/', [OrderController::class, 'index'])->name('orders.index');
        Route::post('/create', [OrderController::class, 'create'])->name('orders.create');
        Route::put('/processed/{commande}', [OrderController::class, 'processedOrder'])->name('orders.processed');
    });

    // Route lié aux utilisateurs
    Route::prefix('user')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('user.index');
        Route::post('/create', [UserController::class, 'create'])->name('user.create');
        Route::put('/edit/{bcuser}', [UserController::class, 'edit'])->name('user.edit');
        Route::delete('/delete/{bcuser}', [UserController::class, 'delete'])->name('user.delete');
    });
});