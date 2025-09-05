
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\KaryawanPesanController;
use App\Http\Controllers\AdminPesanController;
use Illuminate\Http\Request;

Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/', [AuthController::class, 'login'])->name('login');
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
    Route::get('/admin/absensi/{id}/foto', [AdminController::class, 'viewFotoAbsensi'])->name('admin.absensi.foto');
    Route::get('/admin/cuti', [AdminController::class, 'indexCuti'])->name('admin.cuti.index');
    Route::patch('/admin/cuti/{id}/approve', [AdminController::class, 'approveCuti'])->name('admin.cuti.approve');
    Route::patch('/admin/cuti/{id}/reject', [AdminController::class, 'rejectCuti'])->name('admin.cuti.reject');
    
    // Export Data
    Route::get('/admin/export/karyawan', [AdminController::class, 'exportKaryawan'])->name('admin.export.karyawan');
    Route::get('/admin/export/absensi', [AdminController::class, 'exportAbsensi'])->name('admin.export.absensi');
    
    // Pesan Routes
    Route::get('/admin/pesan', [App\Http\Controllers\AdminPesanController::class, 'index'])->name('admin.pesan.index');
    Route::post('/admin/pesan', [App\Http\Controllers\AdminPesanController::class, 'store'])->name('admin.pesan.store');
    Route::get('/admin/pesan/history', [App\Http\Controllers\AdminPesanController::class, 'history'])->name('admin.pesan.history');
});

Route::middleware(['auth', 'role:karyawan'])->group(function () {
    Route::get('/karyawan/dashboard', [App\Http\Controllers\KaryawanController::class, 'dashboard'])->name('karyawan.dashboard');
    
    // Get current attendance data (for real-time updates)
    Route::get('/karyawan/attendance/current', [App\Http\Controllers\KaryawanController::class, 'getCurrentAttendance'])->name('karyawan.attendance.current');
    
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
    Route::get('/karyawan/izin-cuti/history', [App\Http\Controllers\KaryawanController::class, 'historyIzinCuti'])->name('karyawan.izin.cuti.history');
    
    // History
    Route::get('/karyawan/history', [App\Http\Controllers\KaryawanController::class, 'historyAbsensi'])->name('karyawan.history');
    
    // Profile
    Route::get('/karyawan/profile', [App\Http\Controllers\KaryawanController::class, 'profile'])->name('karyawan.profile');
    Route::put('/karyawan/profile', [App\Http\Controllers\KaryawanController::class, 'updateProfile'])->name('karyawan.profile.update');
    
    // Pesan Routes
    Route::get('/karyawan/pesan/unread-count', [KaryawanPesanController::class, 'getUnreadCount'])->name('karyawan.pesan.unread-count');
    Route::get('/karyawan/pesan', [KaryawanPesanController::class, 'index'])->name('karyawan.pesan.index');
    Route::get('/karyawan/pesan/{id}', [KaryawanPesanController::class, 'show'])->name('karyawan.pesan.show');
    Route::post('/karyawan/pesan/{id}/mark-read', [KaryawanPesanController::class, 'markAsRead'])->name('karyawan.pesan.mark-read');
});

