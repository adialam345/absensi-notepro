<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use Illuminate\Http\Request;

Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/', [AuthController::class, 'login'])->name('login');
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

// Test user data
Route::get('/test-user', function() {
    $user = Auth::user();
    if (!$user) {
        return response()->json(['error' => 'User not authenticated']);
    }
    
    $lokasiKantor = App\Models\LokasiKantor::find($user->lokasi_kantor_id);
    
    return response()->json([
        'user' => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'lokasi_kantor_id' => $user->lokasi_kantor_id,
            'role' => $user->role
        ],
        'lokasi_kantor' => $lokasiKantor ? [
            'id' => $lokasiKantor->id,
            'nama_lokasi' => $lokasiKantor->nama_lokasi,
            'latitude' => $lokasiKantor->latitude,
            'longitude' => $lokasiKantor->longitude,
            'radius' => $lokasiKantor->radius
        ] : null,
        'today_absensi' => App\Models\Absensi::where('user_id', $user->id)
            ->whereDate('tanggal', \Carbon\Carbon::today())
            ->first()
    ]);
})->name('test.user');

// Test absen masuk - Actually saves to database
Route::post('/test-absen-masuk', function(Request $request) {
    try {
        // Use the actual controller method to save attendance
        $controller = new App\Http\Controllers\KaryawanController();
        return $controller->absenMasuk($request);
        
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

// Test distance calculation with specific coordinates
Route::get('/test-distance', function() {
    $user = Auth::user();
    if (!$user) {
        return response()->json(['error' => 'User not authenticated']);
    }
    
    $lokasiKantor = App\Models\LokasiKantor::find($user->lokasi_kantor_id);
    if (!$lokasiKantor) {
        return response()->json(['error' => 'Office location not found']);
    }
    
    // Test coordinates (you can change these)
    $testLat = -7.6528390; // Test latitude
    $testLon = 111.5339200; // Test longitude
    
    // Calculate distance using LocationHelper
    $distance = App\Helpers\LocationHelper::calculateDistance(
        $testLat,
        $testLon,
        $lokasiKantor->latitude,
        $lokasiKantor->longitude
    );
    
    $withinRadius = $distance <= $lokasiKantor->radius;
    
    return response()->json([
        'test_coordinates' => [
            'latitude' => $testLat,
            'longitude' => $testLon
        ],
        'office_coordinates' => [
            'latitude' => $lokasiKantor->latitude,
            'longitude' => $lokasiKantor->longitude
        ],
        'office_radius' => $lokasiKantor->radius,
        'calculated_distance' => round($distance, 2),
        'within_radius' => $withinRadius,
        'distance_meters' => round($distance),
        'distance_km' => round($distance / 1000, 3),
        'status' => $withinRadius ? 'DALAM RADIUS' : 'LUAR RADIUS'
    ]);
})->name('test.distance');

// Test distance with coordinates from request (for debugging)
Route::post('/test-distance-request', function(Request $request) {
    $user = Auth::user();
    if (!$user) {
        return response()->json(['error' => 'User not authenticated']);
    }
    
    $lokasiKantor = App\Models\LokasiKantor::find($user->lokasi_kantor_id);
    if (!$lokasiKantor) {
        return response()->json(['error' => 'Office location not found']);
    }
    
    // Get coordinates from request
    $userLat = $request->latitude;
    $userLon = $request->longitude;
    
    if (!$userLat || !$userLon) {
        return response()->json(['error' => 'Latitude and longitude required']);
    }
    
    // Calculate distance using LocationHelper
    $distance = App\Helpers\LocationHelper::calculateDistance(
        $userLat,
        $userLon,
        $lokasiKantor->latitude,
        $lokasiKantor->longitude
    );
    
    $withinRadius = $distance <= $lokasiKantor->radius;
    
    return response()->json([
        'request_coordinates' => [
            'latitude' => $userLat,
            'longitude' => $userLon
        ],
        'office_coordinates' => [
            'latitude' => $lokasiKantor->latitude,
            'longitude' => $lokasiKantor->longitude
        ],
        'office_radius' => $lokasiKantor->radius,
        'calculated_distance' => round($distance, 2),
        'within_radius' => $withinRadius,
        'distance_meters' => round($distance),
        'distance_km' => round($distance / 1000, 3),
        'status' => $withinRadius ? 'DALAM RADIUS' : 'LUAR RADIUS',
        'analysis' => [
            'coordinates_match' => ($userLat == -7.6528390 && $userLon == 111.5339200),
            'test_coordinates' => [-7.6528390, 111.5339200],
            'request_coordinates' => [$userLat, $userLon]
        ]
    ]);
})->name('test.distance.request');

// Test distance with actual GPS coordinates from absen attempt
Route::get('/test-distance-actual', function() {
    $user = Auth::user();
    if (!$user) {
        return response()->json(['error' => 'User not authenticated']);
    }
    
    $lokasiKantor = App\Models\LokasiKantor::find($user->lokasi_kantor_id);
    if (!$lokasiKantor) {
        return response()->json(['error' => 'Office location not found']);
    }
    
    // Use the actual GPS coordinates from the absen attempt
    // Koordinat akan diambil dari input user saat absen, bukan hardcode
    
    // Calculate distance using LocationHelper
    // Koordinat akan diambil dari input user saat absen
    // Untuk testing, gunakan koordinat default atau dari database
    
    return response()->json([
        'message' => 'Test route untuk validasi lokasi',
        'office_coordinates' => [
            'latitude' => $lokasiKantor->latitude,
            'longitude' => $lokasiKantor->longitude
        ],
        'office_radius' => $lokasiKantor->radius,
        'note' => 'Koordinat user akan diambil dari GPS device saat absen'
    ]);
})->name('test.distance.actual');

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
    Route::get('/karyawan/profile', function () {
        return view('karyawan.profile');
    })->name('karyawan.profile');
    Route::put('/karyawan/profile', [App\Http\Controllers\KaryawanController::class, 'updateProfile'])->name('karyawan.profile.update');
});
