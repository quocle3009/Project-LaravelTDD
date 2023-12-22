<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::get('/tasks', [TaskController::class, 'index'])
->name('tasks.index');
Route::post('/tasks', [TaskController::class, 'store'])
    ->name('tasks.store')
    ->middleware('auth');


Route::get('/tasks/create', [TaskController::class, 'create'])
    ->name('tasks.create')
    ->middleware('auth');



Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])
    ->name('tasks.destroy')
    ->middleware('auth');
Auth::routes();


Route::get('/tasks/{task}/edit', [TaskController::class, 'edit'])->name('tasks.edit')->middleware('auth');

// Route để xử lý việc cập nhật task sau khi chỉnh sửa
Route::put('/tasks/{task}', [TaskController::class, 'update'])->name('tasks.update')->middleware('auth');

Auth::routes();

Route::get('/home', [HomeController::class, 'index'])->name('home');

Route::middleware(['guest'])->group(function () {
    // Route đăng nhập
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    // Route đăng ký
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
});

Auth::routes();
