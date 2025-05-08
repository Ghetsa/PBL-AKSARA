<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PeriodeController;
use App\Http\Controllers\ProdiController;

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

Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::post('/postlogin', [AuthController::class, 'postlogin']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Route::get('login', [AuthController::class, 'login'])->name('login');
// Route::post('login', [AuthController::class, 'postlogin']);
// Route::get('logout', [AuthController::class, 'logout'])->middleware('auth');

Route::get('register', [AuthController::class, 'register'])->name('register');
Route::post('register', [AuthController::class, 'postregister']);
Route::get('/', function () {
    $breadcrumb = (object) [
        'title' => 'Lading Page',
        'list' => ['User']
    ];

    return view('landing-page', compact('breadcrumb'));
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware('auth')->name('dashboard');

Route::group(['prefix' => 'user', 'as' => 'user.'], function () {
    Route::get('/', [UserController::class, 'index'])->name('index');
    Route::post('/list', [UserController::class, 'list'])->name('list');
    Route::get('/create', [UserController::class, 'create'])->name('create');
    Route::post('/', [UserController::class, 'store'])->name('store'); // Non-AJAX store
    Route::get('/create_ajax', [UserController::class, 'create_ajax'])->name('create_ajax');
    Route::post('/store_ajax', [UserController::class, 'store_ajax'])->name('store_ajax'); // Jika Anda punya route store_ajax terpisah
    Route::get('/{id}', [UserController::class, 'show'])->name('show');
    Route::get('/{id}/show_ajax', [UserController::class, 'show_ajax'])->name('show_ajax');
    Route::get('/{id}/edit', [UserController::class, 'edit'])->name('edit');
    Route::get('/{id}/edit_ajax', [UserController::class, 'edit_ajax'])->name('edit_ajax');
    Route::put('/{id}/update_ajax', [UserController::class, 'update_ajax'])->name('update_ajax');
    Route::delete('/{id}', [UserController::class, 'destroy'])->name('destroy');
    Route::get('/{id}/confirm_ajax', [UserController::class, 'confirm_ajax'])->name('confirm_ajax');
    Route::delete('/{id}/delete-ajax', [UserController::class, 'delete_ajax'])->name('delete_ajax');
});

Route::group(['prefix' => 'prodi', 'as' => 'prodi.'], function () {
    Route::get('/', [ProdiController::class, 'index'])->name('index');
    Route::post('/list', [ProdiController::class, 'list'])->name('list');
    Route::get('/create', [ProdiController::class, 'create'])->name('create');
    Route::post('/store_ajax', [ProdiController::class, 'store_ajax'])->name('store_ajax'); // Jika Anda punya route store_ajax terpisah
    Route::post('/', [ProdiController::class, 'store'])->name('store');
    Route::get('/{id}', [ProdiController::class, 'show'])->name('show');
    Route::get('/{id}/edit', [ProdiController::class, 'edit'])->name('edit');
    Route::put('/{id}', [ProdiController::class, 'update'])->name('update');
    // Route::delete('/{id}', [ProdiController::class, 'destroy'])->name('destroy');
    Route::get('/{id}/confirm_ajax', [ProdiController::class, 'confirm_ajax'])->name('confirm_ajax');
    Route::delete('/{id}/delete-ajax', [ProdiController::class, 'delete_ajax'])->name('delete_ajax');
});

Route::group(['prefix' => 'periode', 'as' => 'periode.'], function () {
    Route::get('/', [PeriodeController::class, 'index'])->name('index');
    Route::post('/list', [PeriodeController::class, 'list'])->name('list');
    Route::get('/create', [PeriodeController::class, 'create'])->name('create');
    Route::post('/store_ajax', [PeriodeController::class, 'store_ajax'])->name('store_ajax'); // Jika Anda punya route store_ajax terpisah
    Route::post('/', [PeriodeController::class, 'store'])->name('store');
    Route::get('/{id}', [PeriodeController::class, 'show'])->name('show');
    Route::get('/{id}/edit', [PeriodeController::class, 'edit'])->name('edit');
    Route::put('/{id}', [PeriodeController::class, 'update'])->name('update');
    // Route::delete('/{id}', [ProdiController::class, 'destroy'])->name('destroy');
    Route::get('/{id}/confirm_ajax', [PeriodeController::class, 'confirm_ajax'])->name('confirm_ajax');
    Route::delete('/{id}/delete-ajax', [PeriodeController::class, 'delete_ajax'])->name('delete_ajax');
});
