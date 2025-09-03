<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use Illuminate\Http\Request;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Test route for debugging
Route::post('/test-absen', function(Request $request) {
    return response()->json([
        'success' => true,
        'message' => 'Test successful',
        'data' => $request->all()
    ]);
})->name('test.absen');

// Test dashboard route
Route::get('/test-dashboard', function() {
    return view('karyawan.dashboard', [
        'todayAbsensi' => null,
        'hadir' => 0,
        'izin' => 0,
        'sakit' => 0,
        'terlambat' => 0,
        'lastWeek' => collect([]),
        'jamMasuk' => '08:00',
        'jamPulang' => '17:00'
    ]);
})->name('test.dashboard');

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
    Route::get('/karyawan/dashboard', [App\Http\Controllers\KaryawanController::class, 'dashboard'])->name('karyawan.dashboard');
    
    // Attendance Routes
    Route::get('/karyawan/absen/masuk', function () {
        return view('karyawan.absen-masuk');
    })->name('karyawan.absen.masuk');
    Route::post('/karyawan/absen/masuk', [App\Http\Controllers\KaryawanController::class, 'absenMasuk'])->name('karyawan.absen.masuk.post');
    Route::post('/karyawan/absen/pulang', [App\Http\Controllers\KaryawanController::class, 'absenPulang'])->name('karyawan.absen.pulang');
    
    // Leave Requests
    Route::get('/karyawan/izin-cuti', function () {
        return view('karyawan.izin-cuti');
    })->name('karyawan.izin.cuti');
    Route::post('/karyawan/izin-cuti', [App\Http\Controllers\KaryawanController::class, 'izinCuti'])->name('karyawan.izin.cuti.post');
    
    // History
    Route::get('/karyawan/history', function () {
        return view('karyawan.history');
    })->name('karyawan.history');
    
    // Profile
    Route::get('/karyawan/profile', function () {
        return view('karyawan.profile');
    })->name('karyawan.profile');
    Route::put('/karyawan/profile', [App\Http\Controllers\KaryawanController::class, 'updateProfile'])->name('karyawan.profile.update');
});
