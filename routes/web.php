<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\KaryawanController;

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

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected Routes
Route::middleware('auth')->group(function () {
    
    // Admin Routes
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        
        // Karyawan Management
        Route::resource('karyawan', AdminController::class, [
            'names' => [
                'index' => 'karyawan.index',
                'create' => 'karyawan.create',
                'store' => 'karyawan.store',
                'edit' => 'karyawan.edit',
                'update' => 'karyawan.update',
                'destroy' => 'karyawan.destroy',
            ],
            'only' => ['index', 'create', 'store', 'edit', 'update', 'destroy']
        ]);
        
        // Lokasi Kantor Management
        Route::resource('lokasi', AdminController::class, [
            'names' => [
                'index' => 'lokasi.index',
                'create' => 'lokasi.create',
                'store' => 'lokasi.store',
                'edit' => 'lokasi.edit',
                'update' => 'lokasi.update',
                'destroy' => 'lokasi.destroy',
            ],
            'only' => ['index', 'create', 'store', 'edit', 'update', 'destroy']
        ]);
        
        // Reports
        Route::get('/laporan/absensi', [AdminController::class, 'laporanAbsensi'])->name('laporan.absensi');
        
        // Leave Management
        Route::get('/cuti', [AdminController::class, 'indexCuti'])->name('cuti.index');
        Route::patch('/cuti/{id}/approve', [AdminController::class, 'approveCuti'])->name('cuti.approve');
        Route::patch('/cuti/{id}/reject', [AdminController::class, 'rejectCuti'])->name('cuti.reject');
        
        // Export
        Route::get('/export/karyawan', [AdminController::class, 'exportKaryawan'])->name('export.karyawan');
    });
    
    // Karyawan Routes
    Route::middleware('role:karyawan')->prefix('karyawan')->name('karyawan.')->group(function () {
        Route::get('/dashboard', [KaryawanController::class, 'dashboard'])->name('dashboard');
        
        // Attendance
        Route::get('/absen/masuk', [KaryawanController::class, 'absenMasuk'])->name('absen.masuk');
        Route::post('/absen/masuk', [KaryawanController::class, 'absenMasuk'])->name('absen.masuk');
        Route::post('/absen/pulang', [KaryawanController::class, 'absenPulang'])->name('absen.pulang');
        
        // Leave Requests
        Route::get('/izin-cuti', [KaryawanController::class, 'izinCuti'])->name('izin.cuti');
        Route::post('/izin-cuti', [KaryawanController::class, 'izinCuti'])->name('izin.cuti');
        
        // History
        Route::get('/history', [KaryawanController::class, 'historyAbsensi'])->name('history');
        
        // Profile
        Route::get('/profile', [KaryawanController::class, 'profile'])->name('profile');
        Route::put('/profile', [KaryawanController::class, 'updateProfile'])->name('profile.update');
    });
});
