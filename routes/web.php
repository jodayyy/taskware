<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;

// Welcome page
Route::get('/', function () {
	return view('welcome');
})->name('welcome');

// Authentication routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.submit');

// Authenticated user routes (requires authentication)
Route::middleware('auth')->group(function () {
	Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
	Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
	Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
	Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});