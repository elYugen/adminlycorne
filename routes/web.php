<?php

use App\Http\Controllers\OrderController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Route par défaut
Route::get('/', function () {
    return view('dashboard.index');
});

// Route lié à la gestion des utilisateurs
Route::get('/users', [UserController::class, 'index'])->name('users.index');

Route::post('/users/create', [UserController::class, 'createUser'])->name('users.create');
Route::put('/users/edit/{bcuser}', [UserController::class, 'editUser'])->name('users.edit');
Route::delete('/users/delete/{bcuser}', [UserController::class, 'deleteUser'])->name('users.delete');

// Route lié à la gestion des commandes
Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
Route::post('/orders/create', [OrderController::class, 'create'])->name('orders.store');