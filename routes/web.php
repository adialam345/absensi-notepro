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

// Test radius checking
Route::get('/test-radius', function() {
    $userLat = -7.6528390;
    $userLon = 111.5339200;
    $officeLat = -7.6528390;
    $officeLon = 111.5339200;
    $officeRadius = 100; // 100 meters
    
    // Simple distance calculation
    $latDelta = deg2rad($officeLat - $userLat);
    $lonDelta = deg2rad($officeLon - $userLon);
    
    $a = sin($latDelta / 2) * sin($latDelta / 2) +
         cos(deg2rad($userLat)) * cos(deg2rad($officeLat)) *
         sin($lonDelta / 2) * sin($lonDelta / 2);
    
    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
    $distance = 6371000 * $c; // Earth radius in meters
    
    return response()->json([
        'user_coords' => [$userLat, $userLon],
        'office_coords' => [$officeLat, $officeLon],
        'office_radius' => $officeRadius,
        'calculated_distance' => round($distance, 2),
        'within_radius' => $distance <= $officeRadius,
        'distance_meters' => round($distance),
        'distance_km' => round($distance / 1000, 3)
    ]);
})->name('test.radius');

// Test absen masuk
Route::post('/test-absen-masuk', function(Request $request) {
    try {
        // Simulate the absen masuk logic
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated'
            ]);
        }
        
        $lokasiKantor = App\Models\LokasiKantor::find($user->lokasi_kantor_id);
        if (!$lokasiKantor) {
            return response()->json([
                'success' => false,
                'message' => 'Lokasi kantor tidak ditemukan'
            ]);
        }
        
        // Calculate distance
        $lat1 = $request->latitude;
        $lon1 = $request->longitude;
        $lat2 = $lokasiKantor->latitude;
        $lon2 = $lokasiKantor->longitude;
        
        $latDelta = deg2rad($lat2 - $lat1);
        $lonDelta = deg2rad($lon2 - $lon1);
        
        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($lonDelta / 2) * sin($lonDelta / 2);
        
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $distance = 6371000 * $c;
        
        $withinRadius = $distance <= $lokasiKantor->radius;
        
        return response()->json([
            'success' => true,
            'message' => 'Test successful',
            'data' => [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'office_location' => $lokasiKantor->nama_lokasi,
                'user_coords' => [$lat1, $lon1],
                'office_coords' => [$lat2, $lon2],
                'office_radius' => $lokasiKantor->radius,
                'calculated_distance' => round($distance, 2),
                'within_radius' => $withinRadius,
                'request_data' => $request->all()
            ]
        ]);
        
    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
    }
})->name('test.absen.masuk');

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
