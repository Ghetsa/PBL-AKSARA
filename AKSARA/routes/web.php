<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\PrestasiController;
use App\Http\Controllers\ProdiController;
use App\Http\Controllers\PeriodeController;
use App\Http\Controllers\KeahlianUserController;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\LombaController; // Tambahkan ini

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Landing Page
Route::get('/', [LandingPageController::class, 'index']);

// ===================== AUTH =====================
Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::post('/postlogin', [AuthController::class, 'postlogin']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/register', [AuthController::class, 'register'])->name('register');
Route::post('/register', [AuthController::class, 'postregister'])->name('register');


// ===================== AUTHENTICATED ROUTES =====================
Route::middleware(['auth'])->group(function () {

    // Route untuk menampilkan form ubah password via AJAX
    Route::get('/profil/change-password-form', [ProfilController::class, 'showChangePasswordFormAjax'])->name('profil.change_password');

    // Route untuk memproses update password via AJAX
    Route::put('/profil/update-password', [ProfilController::class, 'updatePasswordAjax'])->name('profil.update_password');

    // ===================== DASHBOARD =====================
    Route::get('/dashboard/admin', function () {
        $breadcrumb = (object) ['title' => 'Dashboard', 'list' => ['Admin', 'Dashboard']];
        $activeMenu = 'dashboard';
        return view('dashboard.admin', compact('breadcrumb', 'activeMenu'));
    })->name('dashboard');

    Route::get('/dashboard/mahasiswa', function () {
        $breadcrumb = (object) ['title' => 'Dashboard', 'list' => ['Mahasiswa', 'Dashboard']];
        $activeMenu = 'dashboard';
        return view('dashboard.mahasiswa', compact('breadcrumb', 'activeMenu'));
    })->name('dashboardMHS');

    Route::get('/dashboard/dosen', function () {
        $breadcrumb = (object) ['title' => 'Dashboard', 'list' => ['Dosen', 'Dashboard']];
        $activeMenu = 'dashboard';
        return view('dashboard.mahasiswa', compact('breadcrumb', 'activeMenu'));
    })->name('dashboardDSN');

    // ===================== PROFILE =====================
    Route::get('/profile', [ProfilController::class, 'index'])->name('profile.index');
    Route::get('/user/profile_ajax', [ProfilController::class, 'edit_ajax'])->name('profile.edit_ajax');
    Route::Post('/user/profile_ajax', [ProfilController::class, 'update_ajax'])->name('profile.update_ajax');

    // ===================== LOMBA =====================
    Route::prefix('lomba')->name('lomba.')->group(function () {
        Route::get('/', [LombaController::class, 'index'])->name('index');
        Route::get('/list', [LombaController::class, 'getList'])->name('list');
        Route::get('/create', [LombaController::class, 'create'])->name('create');
        Route::post('/', [LombaController::class, 'store'])->name('store');
        Route::get('/{id}/lomba', [LombaController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [LombaController::class, 'edit'])->name('edit');
        Route::put('/{id}', [LombaController::class, 'update'])->name('update');
        Route::delete('/{id}', [LombaController::class, 'destroy'])->name('destroy');

        Route::get('/verifikasi/{id}', [LombaController::class, 'verifikasi'])->name('verifikasi');
        Route::post('/verifikasi/{id}', [LombaController::class, 'prosesVerifikasi'])->name('prosesVerifikasi');
    });

    // ===================== USER CRUD =====================
    Route::prefix('user')->name('user.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::post('/list', [UserController::class, 'list'])->name('list');
        Route::get('/create', [UserController::class, 'create'])->name('create');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::get('/create_ajax', [UserController::class, 'create_ajax'])->name('create_ajax');
        Route::post('/store_ajax', [UserController::class, 'store_ajax'])->name('store_ajax');
        Route::get('/{id}', [UserController::class, 'show'])->name('show');
        Route::get('/{id}/show_ajax', [UserController::class, 'show_ajax'])->name('show_ajax');
        Route::get('/{id}/edit', [UserController::class, 'edit'])->name('edit');
        Route::get('/{id}/edit_ajax', [UserController::class, 'edit_ajax'])->name('edit_ajax');
        Route::put('/{id}/update_ajax', [UserController::class, 'update_ajax'])->name('update_ajax');
        Route::delete('/{id}', [UserController::class, 'destroy'])->name('destroy');
        Route::get('/{id}/confirm_ajax', [UserController::class, 'confirm_ajax'])->name('confirm_ajax');
        Route::delete('/{id}/delete-ajax', [UserController::class, 'delete_ajax'])->name('delete_ajax');
    });

    // ===================== PRODI =====================
    Route::prefix('prodi')->name('prodi.')->group(function () {
        Route::get('/', [ProdiController::class, 'index'])->name('index');
        Route::post('/list', [ProdiController::class, 'list'])->name('list');
        Route::get('/create', [ProdiController::class, 'create'])->name('create');
        Route::post('/', [ProdiController::class, 'store'])->name('store');
        Route::post('/store_ajax', [ProdiController::class, 'store_ajax'])->name('store_ajax');
        Route::get('/{id}', [ProdiController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [ProdiController::class, 'edit'])->name('edit');
        Route::put('/{id}', [ProdiController::class, 'update'])->name('update');
        Route::get('/{id}/confirm_ajax', [ProdiController::class, 'confirm_ajax'])->name('confirm_ajax');
        Route::delete('/{id}/delete-ajax', [ProdiController::class, 'delete_ajax'])->name('delete_ajax');
    });

    // ===================== PERIODE =====================
    Route::prefix('periode')->name('periode.')->group(function () {
        Route::get('/', [PeriodeController::class, 'index'])->name('index');
        Route::post('/list', [PeriodeController::class, 'list'])->name('list');
        Route::get('/create', [PeriodeController::class, 'create'])->name('create');
        Route::post('/', [PeriodeController::class, 'store'])->name('store');
        Route::post('/store_ajax', [PeriodeController::class, 'store_ajax'])->name('store_ajax');
        Route::get('/{id}', [PeriodeController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [PeriodeController::class, 'edit'])->name('edit');
        Route::put('/{id}', [PeriodeController::class, 'update'])->name('update');
        Route::get('/{id}/confirm_ajax', [PeriodeController::class, 'confirm_ajax'])->name('confirm_ajax');
        Route::delete('/{id}/delete-ajax', [PeriodeController::class, 'delete_ajax'])->name('delete_ajax');
    });

    // ===================== MAHASISWA =====================
    Route::middleware(['role:mahasiswa'])->group(function () {

        // ---------- PRESTASI ----------
        Route::prefix('mahasiswa/prestasi')->name('prestasi.mahasiswa.')->group(function () {
            Route::get('/', [PrestasiController::class, 'indexMahasiswa'])->name('index');
            Route::get('/list', [PrestasiController::class, 'listMahasiswa'])->name('list');
            Route::get('/create-ajax', [PrestasiController::class, 'createFormAjaxMahasiswa'])->name('create_ajax');
            Route::post('/store-ajax', [PrestasiController::class, 'storeAjaxMahasiswa'])->name('store_ajax');
            Route::get('/edit-ajax/{id}', [PrestasiController::class, 'editAjaxMahasiswa'])->name('edit_ajax');
            Route::put('/update-ajax/{id}', [PrestasiController::class, 'updateAjaxMahasiswa'])->name('update_ajax');
            Route::get('/show-ajax/{id}', [PrestasiController::class, 'showAjaxMahasiswa'])->name('show_ajax');
            Route::get('/{id}/confirm-delete-ajax', [PrestasiController::class, 'confirmDeleteAjaxMahasiswa'])->name('confirm_delete_ajax');
            Route::delete('/{id}/destroy-ajax', [PrestasiController::class, 'destroyAjaxMahasiswa'])->name('destroy_ajax');
        });

        // ---------- KEAHLIAN USER ----------
        Route::prefix('mahasiswa/keahlian_user')->name('keahlian_user.')->group(function () {
            Route::get('/', [KeahlianUserController::class, 'index'])->name('index');
            Route::get('/list', [KeahlianUserController::class, 'list'])->name('list');
            Route::get('/create', [KeahlianUserController::class, 'create'])->name('create');
            Route::post('/store', [KeahlianUserController::class, 'store'])->name('store');
            Route::get('/edit/{id}', [KeahlianUserController::class, 'edit'])->name('edit');
            Route::put('/update/{id}', [KeahlianUserController::class, 'update'])->name('update');
            Route::delete('/delete/{id}', [KeahlianUserController::class, 'destroy'])->name('destroy');
            Route::get('/{id}/show_ajax', [KeahlianUserController::class, 'show_ajax'])->name('show_ajax');
            Route::get('/verifikasi/{id}', [KeahlianUserController::class, 'verifikasi'])->name('verifikasi');
            Route::post('/verifikasi/{id}', [KeahlianUserController::class, 'prosesVerifikasi'])->name('proses_verifikasi');
        });

        // ---------- LOMBA ----------
        Route::prefix('mahasiswa/lomba')->name('lomba.')->group(function () {
            Route::get('/', [LombaController::class, 'index'])->name('index');
            Route::get('/list', [LombaController::class, 'getList'])->name('list');
            Route::get('/create', [LombaController::class, 'create'])->name('create');
            Route::post('/', [LombaController::class, 'store'])->name('store');
            Route::get('/{id}/lomba', [LombaController::class, 'show'])->name('show');
            Route::get('/{id}/edit', [LombaController::class, 'edit'])->name('edit');
            Route::put('/{id}', [LombaController::class, 'update'])->name('update');
            Route::delete('/{id}', [LombaController::class, 'destroy'])->name('destroy');
            Route::get('/verifikasi/{id}', [LombaController::class, 'verifikasi'])->name('verifikasi');
            Route::post('/verifikasi/{id}', [LombaController::class, 'prosesVerifikasi'])->name('proses_verifikasi');
        });

        Route::prefix('lomba-saya')->name('lomba.user.')->group(function () {
            Route::get('/histori-pengajuan', [LombaController::class, 'historiPengajuanLomba'])->name('histori.index');
            Route::get('/histori-pengajuan/list', [LombaController::class, 'listHistoriPengajuanLomba'])->name('histori.list');

            Route::get('/ajukan-lomba', [LombaController::class, 'createPengajuanLomba'])->name('create_form');
            Route::post('/simpan-lomba', [LombaController::class, 'storeLomba'])->name('store'); // Route ini digunakan untuk store dari semua role

            // Jika Anda ingin user bisa mengedit pengajuan mereka yang belum disetujui:
            // Route::get('/edit-pengajuan/{id}', [LombaController::class, 'editPengajuanLombaForm'])->name('edit_form');
            // Route::put('/update-pengajuan/{id}', [LombaController::class, 'updatePengajuanLomba'])->name('update');
        });
    });


    // ===================== ADMIN =====================
    Route::middleware(['role:admin'])->group(function () {
        Route::prefix('admin/prestasi-verifikasi')->name('prestasi.admin.')->group(function () {
            Route::get('/', [PrestasiController::class, 'indexAdmin'])->name('index');
            Route::get('/list', [PrestasiController::class, 'listAdmin'])->name('list');
            Route::get('/{prestasi}/verify-form-ajax', [PrestasiController::class, 'showVerifyFormAjaxAdmin'])->name('verify_form_ajax');
            Route::put('/{prestasi}/process-verification-ajax', [PrestasiController::class, 'processVerificationAjaxAdmin'])->name('process_verification_ajax');
        });

        Route::prefix('admin/keahlian-verifikasi')->name('keahlian_user.admin.')->group(function () {
            Route::get('/', [KeahlianUserController::class, 'adminIndex'])->name('index');
            Route::get('/list', [KeahlianUserController::class, 'list_admin'])->name('list');
            Route::get('/{id}/verify-form-ajax', [KeahlianUserController::class, 'showVerificationFormAjax'])->name('verify_form_ajax');
            Route::put('/{id}/process-verification-ajax', [KeahlianUserController::class, 'prosesVerifikasiAjax'])->name('process_verification_ajax');
        });

        // ADMIN - MANAJEMEN & VERIFIKASI LOMBA
        // --- ADMIN ---
        Route::prefix('admin')->name('admin.')->group(function () {
            // --- ADMIN: VERIFIKASI LOMBA ---
            Route::prefix('verifikasi-lomba')->name('lomba.verifikasi.')->group(function () {
                Route::get('/', [LombaController::class, 'adminIndexVerifikasiLomba'])->name('index'); // Halaman daftar lomba untuk diverifikasi
                Route::get('/list', [LombaController::class, 'adminListVerifikasiLomba'])->name('list'); // DataTables untuk verifikasi
                Route::get('/{id}/form-ajax', [LombaController::class, 'adminShowVerifyFormAjax'])->name('form_ajax'); // Form AJAX modal verifikasi
                Route::put('/{id}/proses', [LombaController::class, 'adminProcessVerificationAjax'])->name('proses'); // Proses AJAX verifikasi
            });

            // --- ADMIN: MANAJEMEN LOMBA (CRUD Lomba oleh Admin) ---
            Route::prefix('manajemen-lomba')->name('lomba.crud.')->group(function () {
                Route::get('/', [LombaController::class, 'adminIndexCrudLomba'])->name('index'); // Halaman daftar semua lomba (CRUD)
                Route::get('/list', [LombaController::class, 'adminListCrudLomba'])->name('list'); // DataTables untuk CRUD
                Route::get('/create-form-ajax', [LombaController::class, 'adminCreateLombaFormAjax'])->name('create_form_ajax'); // Form AJAX tambah
                Route::post('/store-ajax', [LombaController::class, 'adminStoreLombaAjax'])->name('store_ajax'); // Simpan AJAX dari admin
                Route::get('/{id}/edit-form-ajax', [LombaController::class, 'adminEditLombaFormAjax'])->name('edit_form_ajax'); // Form AJAX edit
                Route::put('/{id}/update-ajax', [LombaController::class, 'adminUpdateLombaAjax'])->name('update_ajax'); // Update AJAX dari admin
                Route::get('/{id}/confirm-ajax', [LombaController::class, 'adminConfirmDeleteLombaAjax'])->name('confirm_delete_ajax'); // Hapus AJAX
                Route::delete('/{id}/destroy-ajax', [LombaController::class, 'adminDestroyLombaAjax'])->name('destroy_ajax'); // Hapus AJAX
            });
        });

        // Route::prefix('admin/keahlian_user')->group(function () {
        //     Route::get('/', [KeahlianUserController::class, 'index'])->name('keahlian_user.index');
        //     Route::get('/create', [KeahlianUserController::class, 'create'])->name('keahlian_user.create');
        //     Route::post('/store', [KeahlianUserController::class, 'store'])->name('keahlian_user.store');
        //     Route::get('/edit/{id}', [KeahlianUserController::class, 'edit'])->name('keahlian_user.edit');
        //     Route::put('/update/{id}', [KeahlianUserController::class, 'update'])->name('keahlian_user.update');
        //     Route::get('/delete/{id}', [KeahlianUserController::class, 'destroy'])->name('keahlian_user.destroy');
        //     Route::get('/list', [KeahlianUserController::class, 'list'])->name('keahlian_user.list');
        //     Route::get('/{id}/show_ajax', [KeahlianUserController::class, 'show_ajax'])->name('keahlian_user.show_ajax');

        //     Route::get('/verifikasi/{id}', [KeahlianUserController::class, 'verifikasi'])->name('keahlian_user.verifikasi');
        //     Route::post('/verifikasi/{id}', [KeahlianUserController::class, 'prosesVerifikasi'])->name('keahlian_user.prosesVerifikasi');
        // });
    });

    // ===================== DOSEN =====================
    Route::middleware(['role:dosen'])->prefix('dosen/prestasi')->name('prestasi.dosen.')->group(function () {
        Route::get('/', [PrestasiController::class, 'indexDosen'])->name('index');
        Route::get('/list', [PrestasiController::class, 'listDosen'])->name('list');
        Route::get('/create-ajax', [PrestasiController::class, 'createFormAjaxDosen'])->name('create_ajax');
        Route::post('/store-ajax', [PrestasiController::class, 'storeAjaxDosen'])->name('store_ajax');
        Route::get('/{prestasi}/verify-form-ajax', [PrestasiController::class, 'showVerifyFormAjaxDosen'])->name('verify_form_ajax');
        Route::put('/{prestasi}/process-verification-ajax', [PrestasiController::class, 'processVerificationAjaxDosen'])->name('process_verification_ajax');

        Route::prefix('lomba-saya')->name('lomba.user.')->group(function () {
            Route::get('/histori-pengajuan', [LombaController::class, 'historiPengajuanLomba'])->name('histori.index');
            Route::get('/histori-pengajuan/list', [LombaController::class, 'listHistoriPengajuanLomba'])->name('histori.list');

            Route::get('/ajukan-lomba', [LombaController::class, 'createPengajuanLomba'])->name('create_form');
            Route::post('/simpan-lomba', [LombaController::class, 'storeLomba'])->name('store'); // Route ini digunakan untuk store dari semua role

            // Jika Anda ingin user bisa mengedit pengajuan mereka yang belum disetujui:
            // Route::get('/edit-pengajuan/{id}', [LombaController::class, 'editPengajuanLombaForm'])->name('edit_form');
            // Route::put('/update-pengajuan/{id}', [LombaController::class, 'updatePengajuanLomba'])->name('update');
        });
    });

    // Route::middleware(['role:mahasiswa, dosen'])->group(function () {
    //     Route::prefix('lomba-saya')->name('lomba.user.')->group(function () {
    //         Route::get('/histori-pengajuan', [LombaController::class, 'historiPengajuanLomba'])->name('histori.index');
    //         Route::get('/histori-pengajuan/list', [LombaController::class, 'listHistoriPengajuanLomba'])->name('histori.list');

    //         Route::get('/ajukan-lomba', [LombaController::class, 'createPengajuanLomba'])->name('create_form');
    //         Route::post('/simpan-lomba', [LombaController::class, 'storeLomba'])->name('store'); // Route ini digunakan untuk store dari semua role

    //         // Jika Anda ingin user bisa mengedit pengajuan mereka yang belum disetujui:
    //         // Route::get('/edit-pengajuan/{id}', [LombaController::class, 'editPengajuanLombaForm'])->name('edit_form');
    //         // Route::put('/update-pengajuan/{id}', [LombaController::class, 'updatePengajuanLomba'])->name('update');
    //     });
    // });

    // --- LOMBA UNTUK SEMUA USER LOGIN (Mahasiswa, Dosen, Admin bisa lihat yang disetujui) ---
    Route::prefix('informasi-lomba')->name('lomba.publik.')->group(function () {
        Route::get('/', [LombaController::class, 'indexLombaPublik'])->name('index'); // Halaman daftar lomba publik
        Route::get('/list', [LombaController::class, 'listLombaPublik'])->name('list'); // DataTables lomba publik
        Route::get('/{id}/detail-ajax', [LombaController::class, 'showAjaxLombaPublik'])->name('show_ajax'); // Detail modal
    });
});
