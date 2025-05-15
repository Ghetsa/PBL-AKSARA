<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PeriodeController;
use App\Http\Controllers\PrestasiController;
use App\Http\Controllers\ProdiController;
use App\Http\Controllers\ProfilController;

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
// Route::get('logout', [AuthController::class, 'logout'])->middleware('auth');

// Route::get('login', [AuthController::class, 'login'])->name('login');
// Route::post('login', [AuthController::class, 'postlogin']);
// Route::get('logout', [AuthController::class, 'logout'])->middleware('auth');

Route::get('register', [AuthController::class, 'register'])->name('register');
Route::post('/register', [AuthController::class, 'postregister'])->name('register');
Route::get('/', function () {
    $breadcrumb = (object) [
        'title' => 'Landing Page',
        'list' => ['User']
    ];

    return view('landing-page', compact('breadcrumb'));
});

Route::middleware(['auth'])->group(function () { // artinya semua route di dalam group ini harus login dulu
    Route::get('/dashboard/admin', function () {
        $breadcrumb = (object) [
            'title' => 'Dashboard',
            'list' => ['Admin', 'Dashboard']
        ];
        $activeMenu = 'dashboard';

        return view('dashboard.admin', compact('breadcrumb', 'activeMenu'));
    })->name('dashboard');

    Route::get('/dashboard/mahasiswa', function () {
        $breadcrumb = (object) [
            'title' => 'Dashboard',
            'list' => ['Mahasiswa', 'Dashboard']
        ];
        $activeMenu = 'dashboard';

        return view('dashboard.mahasiswa', compact('breadcrumb', 'activeMenu'));
    })->name('dashboardMHS');

    Route::get('/dashboard/dosen', function () {
        $breadcrumb = (object) [
            'title' => 'Dashboard',
            'list' => ['Dosen', 'Dashboard']
        ];
        $activeMenu = 'dashboard';

        return view('dashboard.mahasiswa', compact('breadcrumb', 'activeMenu'));
    })->name('dashboardDSN');

    Route::get('/profile', [ProfilController::class, 'index'])->name('profile.index');
    Route::get('/user/profile_ajax', [ProfilController::class, 'edit_ajax'])->name('profile.edit_ajax');
    Route::put('/user/profile_ajax', [ProfilController::class, 'update_ajax'])->name('profile.update_ajax');


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

    Route::middleware(['role:mahasiswa'])->prefix('mahasiswa/prestasi')->name('prestasi.mahasiswa.')->group(function () {
        Route::get('/', [PrestasiController::class, 'indexMahasiswa'])->name('index'); // Halaman utama histori prestasi (full page)
        Route::get('/list', [PrestasiController::class, 'listMahasiswa'])->name('list'); // Data untuk DataTable mahasiswa

        Route::get('/create-ajax', [PrestasiController::class, 'createFormAjaxMahasiswa'])->name('create_ajax'); // Menampilkan form tambah via AJAX
        Route::post('/store-ajax', [PrestasiController::class, 'storeAjaxMahasiswa'])->name('store_ajax'); // Menyimpan prestasi via AJAX
        Route::get('/edit-ajax/{id}', [PrestasiController::class, 'editAjaxMahasiswa'])->name('edit_ajax'); // Menampilkan form tambah via AJAX
        Route::post('/update-ajax/{id}', [PrestasiController::class, 'updateAjaxMahasiswa'])->name('update_ajax'); // Menampilkan form tambah via AJAX
        Route::get('/show-ajax/{id}', [PrestasiController::class, 'showAjaxMahasiswa'])->name('show_ajax'); // Menampilkan form tambah via AJAX

        // Jika ada fitur edit/hapus oleh mahasiswa via AJAX nantinya:
        // Route::get('/{prestasi}/edit-ajax', [PrestasiController::class, 'editFormAjaxMahasiswa'])->name('edit_ajax');
        // Route::put('/{prestasi}/update-ajax', [PrestasiController::class, 'updateAjaxMahasiswa'])->name('update_ajax');
        // Route::get('/{prestasi}/confirm-delete-ajax', [PrestasiController::class, 'confirmDeleteAjaxMahasiswa'])->name('confirm_delete_ajax');
        // Route::delete('/{prestasi}/destroy-ajax', [PrestasiController::class, 'destroyAjaxMahasiswa'])->name('destroy_ajax');
    });

    // === RUTE UNTUK ADMIN ===
    Route::middleware(['role:admin'])->prefix('admin/prestasi-verifikasi')->name('prestasi.admin.')->group(function () {
        Route::get('/', [PrestasiController::class, 'indexAdmin'])->name('index'); // Halaman utama daftar prestasi untuk admin (full page)
        Route::get('/list', [PrestasiController::class, 'listAdmin'])->name('list'); // Data untuk DataTable admin

        Route::get('/{prestasi}/verify-form-ajax', [PrestasiController::class, 'showVerifyFormAjaxAdmin'])->name('verify_form_ajax'); // Menampilkan detail & form verifikasi via AJAX
        Route::put('/{prestasi}/process-verification-ajax', [PrestasiController::class, 'processVerificationAjaxAdmin'])->name('process_verification_ajax'); // Memproses verifikasi via AJAX
    });


    // === RUTE UNTUK DOSEN ===
    Route::middleware(['role:dosen'])->prefix('dosen/prestasi')->name('prestasi.dosen.')->group(function () {
        Route::get('/', [PrestasiController::class, 'indexDosen'])->name('index'); // Halaman utama histori prestasi (full page)
        Route::get('/list', [PrestasiController::class, 'listDosen'])->name('list'); // Data untuk DataTable Dosen

        Route::get('/create-ajax', [PrestasiController::class, 'createFormAjaxDosen'])->name('create_ajax'); // Menampilkan form tambah via AJAX
        Route::post('/store-ajax', [PrestasiController::class, 'storeAjaxDosen'])->name('store_ajax'); // Menyimpan prestasi via AJAX
        
        Route::get('/{prestasi}/verify-form-ajax', [PrestasiController::class, 'showVerifyFormAjaxAdmin'])->name('verify_form_ajax'); // Menampilkan detail & form verifikasi via AJAX
        Route::put('/{prestasi}/process-verification-ajax', [PrestasiController::class, 'processVerificationAjaxAdmin'])->name('process_verification_ajax'); // Memproses verifikasi via AJAX

        // Jika ada fitur edit/hapus oleh mahasiswa via AJAX nantinya:
        // Route::get('/{prestasi}/edit-ajax', [PrestasiController::class, 'editFormAjaxMahasiswa'])->name('edit_ajax');
        // Route::put('/{prestasi}/update-ajax', [PrestasiController::class, 'updateAjaxMahasiswa'])->name('update_ajax');
        // Route::get('/{prestasi}/confirm-delete-ajax', [PrestasiController::class, 'confirmDeleteAjaxMahasiswa'])->name('confirm_delete_ajax');
        // Route::delete('/{prestasi}/destroy-ajax', [PrestasiController::class, 'destroyAjaxMahasiswa'])->name('destroy_ajax');
    });
});
