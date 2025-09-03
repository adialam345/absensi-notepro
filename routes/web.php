<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    
    // CRUD Karyawan
    Route::get('/admin/karyawan', [AdminController::class, 'indexKaryawan'])->name('admin.karyawan.index');
    Route::get('/admin/karyawan/create', [AdminController::class, 'createKaryawan'])->name('admin.karyawan.create');
    Route::post('/admin/karyawan', [AdminController::class, 'storeKaryawan'])->name('admin.karyawan.store');
    Route::get('/admin/karyawan/{id}/edit', [AdminController::class, 'editKaryawan'])->name('admin.karyawan.edit');
    Route::put('/admin/karyawan/{id}', [AdminController::class, 'updateKaryawan'])->name('admin.karyawan.update');
    Route::delete('/admin/karyawan/{id}', [AdminController::class, 'destroyKaryawan'])->name('admin.karyawan.destroy');
    
    // CRUD Lokasi Kantor
    Route::get('/admin/lokasi', [AdminController::class, 'indexLokasi'])->name('admin.lokasi.index');
    Route::get('/admin/lokasi/create', [AdminController::class, 'createLokasi'])->name('admin.lokasi.create');
    Route::post('/admin/lokasi', [AdminController::class, 'storeLokasi'])->name('admin.lokasi.store');
    Route::get('/admin/lokasi/{id}/edit', [AdminController::class, 'editLokasi'])->name('admin.lokasi.edit');
    Route::put('/admin/lokasi/{id}', [AdminController::class, 'updateLokasi'])->name('admin.lokasi.update');
    Route::delete('/admin/lokasi/{id}', [AdminController::class, 'destroyLokasi'])->name('admin.lokasi.destroy');
    
    // Laporan & Monitoring
    Route::get('/admin/laporan/absensi', [AdminController::class, 'laporanAbsensi'])->name('admin.laporan.absensi');
    Route::get('/admin/cuti', [AdminController::class, 'indexCuti'])->name('admin.cuti.index');
    Route::patch('/admin/cuti/{id}/approve', [AdminController::class, 'approveCuti'])->name('admin.cuti.approve');
    Route::patch('/admin/cuti/{id}/reject', [AdminController::class, 'rejectCuti'])->name('admin.cuti.reject');
    
    // Export Data
    Route::get('/admin/export/karyawan', [AdminController::class, 'exportKaryawan'])->name('admin.export.karyawan');
});

Route::middleware(['auth', 'role:karyawan'])->group(function () {
    Route::get('/karyawan/dashboard', function () {
        return view('karyawan.dashboard');
    })->name('karyawan.dashboard');
    // Route karyawan lainnya
});
