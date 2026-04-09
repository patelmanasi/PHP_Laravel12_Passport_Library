<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\UserController;

// Public routes
Route::get('/', function () {
    return view('welcome');
});

Route::get('/register', [AuthController::class, 'showRegister']);
Route::post('/register', [AuthController::class, 'register']);

Route::get('/login', [AuthController::class, 'showLogin']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes (auth required)
Route::middleware('auth')->group(function () {
    // Dashboard & logout
    Route::get('/dashboard', [AuthController::class, 'dashboard']);
    Route::get('/logout', [AuthController::class, 'logout']);

    // Users management
    Route::get('/users', [UserController::class, 'index']);
    Route::get('/users/delete/{id}', [UserController::class, 'destroy']);
    Route::get('/users/restore/{id}', [UserController::class, 'restore']);
    Route::get('/users/toggle-status/{id}', [UserController::class, 'toggleStatus']);
    Route::get('/users/export', [UserController::class, 'export']);
});