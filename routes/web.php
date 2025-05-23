<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProspectController;
use App\Http\Controllers\StripeController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Route non protégé
Route::get('/', [AuthController::class, 'index'])->name('auth.index');
Route::post('/auth/login', [AuthController::class, 'authenticate'])->name('auth.authenticate');

Route::get('/orders/{commande}/confirm', [OrderController::class, 'showCgv'])->name('orders.showCgv')->middleware('throttle:3,1');
Route::post('/orders/{commande}/confirm', [OrderController::class, 'validateCgv'])->name('orders.confirm')->middleware('throttle:3,1');
Route::get('/orders/{commande}/finished', [OrderController::class, 'finishedCgv'])->name('orders.finishedCgv');

// Route lié à stripe
Route::post('/stripe/checkout-session', [StripeController::class, 'createCheckoutSession'])->name('stripe.checkout.session');

// Route protégées
Route::middleware('auth')->group(function () {
    
    // Route lié à l'authentification
    Route::post('/auth/logout', [AuthController::class, 'logout'])->name('auth.logout');
    
    // Route lié aux Prospects
    Route::prefix('prospect')->group(function () {
        Route::get('/', [ProspectController::class, 'index'])->name('prospect.index');
        Route::post('/create', [ProspectController::class, 'create'])->name('prospect.create');
        Route::post('/create/from/order', [ProspectController::class, 'store'])->name('prospect.store');
        Route::put('/edit/{prospect}', [ProspectController::class, 'edit'])->name('prospect.edit');
        Route::delete('/delete/{prospect}', [ProspectController::class, 'delete'])->name('prospect.delete');
        Route::get('/search', [ProspectController::class, 'search'])->name('prospect.search');
    });

    // Route lié aux commandes
    Route::prefix('orders')->group(function () {
        Route::get('/', [OrderController::class, 'index'])->name('orders.index');
        Route::get('/create', [OrderController::class, 'create'])->name('orders.create');
        Route::delete('/orders/{commande}', [OrderController::class, 'delete'])->name('orders.delete');
        Route::post('/add', [OrderController::class, 'add'])->name('orders.add');
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