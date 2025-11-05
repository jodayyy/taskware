<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;

// Welcome page
Route::get('/', function () {
	return view('welcome');
})->name('welcome');

// Authentication routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:login')->name('login.submit');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.submit');

// Guest user routes
Route::prefix('guest')->name('guest.')->group(function () {
	Route::get('/start', [GuestController::class, 'start'])->name('start');
	Route::get('/dashboard', [GuestController::class, 'dashboard'])->name('dashboard');
	Route::get('/profile', [GuestController::class, 'showProfile'])->name('profile');
	Route::put('/profile', [GuestController::class, 'updateProfile'])->name('profile.update');
	Route::post('/logout', [GuestController::class, 'logout'])->name('logout');
	
	// Guest task routes
	Route::get('/tasks', [GuestController::class, 'indexTasks'])->name('tasks.index');
	Route::get('/tasks/create', [GuestController::class, 'createTask'])->name('tasks.create');
	Route::post('/tasks', [GuestController::class, 'storeTask'])->name('tasks.store');
	Route::get('/tasks/{id}', [GuestController::class, 'showTask'])->name('tasks.task-details');
	Route::put('/tasks/{id}', [GuestController::class, 'updateTask'])->name('tasks.update');
	Route::delete('/tasks/{id}', [GuestController::class, 'destroyTask'])->name('tasks.destroy');
	
	// Guest project routes
	Route::get('/projects', [GuestController::class, 'indexProjects'])->name('projects.index');
	Route::get('/projects/create', [GuestController::class, 'createProject'])->name('projects.create');
	Route::post('/projects', [GuestController::class, 'storeProject'])->name('projects.store');
	Route::get('/projects/{id}', [GuestController::class, 'showProject'])->name('projects.show');
	Route::put('/projects/{id}', [GuestController::class, 'updateProject'])->name('projects.update');
	Route::delete('/projects/{id}', [GuestController::class, 'destroyProject'])->name('projects.destroy');
});

// Authenticated user routes (requires authentication)
Route::middleware('auth')->group(function () {
	Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
	Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
	Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
	Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
	
	// Task routes
	Route::resource('tasks', TaskController::class);
	
	// Project routes
	Route::resource('projects', ProjectController::class);
});