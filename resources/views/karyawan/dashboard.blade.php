<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Karyawan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <div class="bg-[#ff040c] p-3 text-white">
        <div class="flex justify-between items-center">
            <div class="flex items-center space-x-2">
                <i class="fas fa-bars text-lg"></i>
                <div class="flex items-center space-x-2">
                    <div class="w-6 h-6 bg-white rounded flex items-center justify-center">
                        <div class="w-3 h-3 bg-[#ff040c] transform rotate-45"></div>
                    </div>
                    <span class="font-semibold text-sm">ASRI PROJECT</span>
                </div>
            </div>
            <div class="flex items-center space-x-2">
                <i class="fas fa-bell text-lg"></i>
                <i class="fas fa-user-circle text-xl"></i>
            </div>
        </div>
    </div>

    <!-- User Greeting and Work Schedule -->
    <div class="bg-white rounded-t-2xl -mt-1 p-4 shadow-sm">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-gray-500 text-xs">Selamat {{ \Carbon\Carbon::now()->hour < 12 ? 'Pagi' : (\Carbon\Carbon::now()->hour < 15 ? 'Siang' : (\Carbon\Carbon::now()->hour < 18 ? 'Sore' : 'Malam')) }}</p>
                <h1 class="text-lg font-semibold text-[#ff040c]">{{ Auth::user()->name ?? 'Adi Alam Sami Aji' }}</h1>
            </div>
            <div class="text-right">
                <p class="text-gray-500 text-xs">Jam Kerja</p>
                <div class="text-sm font-medium text-[#ff040c]">
                    <div>{{ $jamMasuk ?? '08:00' }} - {{ $jamPulang ?? '17:00' }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation Icons -->
    <div class="px-4 py-3">
        <div class="grid grid-cols-6 gap-3">
            <a href="{{ route('karyawan.absen.masuk') }}" class="text-center">
                <div class="w-10 h-10 bg-[#ff040c] rounded-xl flex items-center justify-center mx-auto mb-2">
                    <i class="fas fa-camera text-white text-sm"></i>
                </div>
                <span class="text-xs text-gray-700">Absen</span>
            </a>
            <a href="{{ route('karyawan.izin.cuti') }}" class="text-center">
                <div class="w-10 h-10 bg-[#ff040c] rounded-xl flex items-center justify-center mx-auto mb-2">
                    <i class="fas fa-file-alt text-white text-sm"></i>
                </div>
                <span class="text-xs text-gray-700">Izin</span>
            </a>
            <a href="{{ route('karyawan.history') }}" class="text-center">
                <div class="w-10 h-10 bg-[#ff040c] rounded-xl flex items-center justify-center mx-auto mb-2">
                    <i class="fas fa-calendar text-white text-sm"></i>
                </div>
                <span class="text-xs text-gray-700">History</span>
            </a>
            <a href="{{ route('karyawan.history') }}" class="text-center">
                <div class="w-10 h-10 bg-[#ff040c] rounded-xl flex items-center justify-center mx-auto mb-2">
                    <i class="fas fa-layer-group text-white text-sm"></i>
                </div>
                <span class="text-xs text-gray-700">History</span>
            </a>
            <a href="{{ route('karyawan.profile') }}" class="text-center">
                <div class="w-10 h-10 bg-[#ff040c] rounded-xl flex items-center justify-center mx-auto mb-2">
                    <i class="fas fa-user text-white text-sm"></i>
                </div>
                <span class="text-xs text-gray-700">Profil</span>
            </a>
            <a href="#" class="text-center">
                <div class="w-10 h-10 bg-[#ff040c] rounded-xl flex items-center justify-center mx-auto mb-2">
                    <i class="fas fa-envelope text-white text-sm"></i>
                </div>
                <span class="text-xs text-gray-700">Pesan</span>
            </a>
        </div>
    </div>

    <!-- Attendance Status Cards -->
    <div class="px-4 py-3">
        <div class="grid grid-cols-2 gap-3">
            <div class="bg-[#ff040c] rounded-xl p-3 text-white" data-attendance="masuk">
                <h3 class="font-semibold mb-1 text-sm">Absen Masuk</h3>
                @if($todayAbsensi && $todayAbsensi->jam_masuk)
                    <p class="text-xs opacity-90" id="jam-masuk">{{ $todayAbsensi->jam_masuk }}</p>
                @else
                    <p class="text-xs opacity-90" id="jam-masuk">Belum absen</p>
                @endif
            </div>
            <div class="bg-[#ff040c] rounded-xl p-3 text-white" data-attendance="pulang">
                <h3 class="font-semibold mb-1 text-sm">Absen Pulang</h3>
                @if($todayAbsensi && $todayAbsensi->jam_pulang)
                    <p class="text-xs opacity-90" id="jam-pulang">{{ $todayAbsensi->jam_pulang }}</p>
                @else
                    <p class="text-xs opacity-90" id="jam-pulang">Belum Absen</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Monthly Attendance Summary -->
    <div class="px-4 py-3">
        <div class="flex justify-between items-center mb-3">
            <h2 class="text-sm font-semibold text-gray-800">
                Absensi Bulan <span class="text-[#ff040c]">{{ \Carbon\Carbon::now()->format('F') }}</span>
            </h2>
            <div class="flex items-center space-x-2">
                <span class="text-gray-800 text-sm">{{ \Carbon\Carbon::now()->format('Y') }}</span>
                <i class="fas fa-chevron-down text-gray-600 text-xs"></i>
            </div>
        </div>
        
        <div class="grid grid-cols-2 gap-3">
            <div class="bg-white rounded-xl p-3 shadow-sm">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-[#ff040c] rounded-full flex items-center justify-center">
                        <i class="fas fa-arrow-right text-white text-sm"></i>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-800 text-sm">Hadir</p>
                        <p class="text-xs text-gray-600">{{ $hadir ?? 0 }} Hari</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl p-3 shadow-sm">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-[#ff040c] rounded-full flex items-center justify-center">
                        <i class="fas fa-user text-white text-sm"></i>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-800 text-sm">Izin</p>
                        <p class="text-xs text-gray-600">{{ $izin ?? 0 }} Hari</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl p-3 shadow-sm">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-[#ff040c] rounded-full flex items-center justify-center">
                        <i class="fas fa-sad-tear text-white text-sm"></i>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-800 text-sm">Sakit</p>
                        <p class="text-xs text-gray-600">{{ $sakit ?? 0 }} Hari</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl p-3 shadow-sm">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-[#ff040c] rounded-full flex items-center justify-center">
                        <i class="fas fa-clock text-white text-sm"></i>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-800 text-sm">Terlambat</p>
                        <p class="text-xs text-gray-600">{{ $terlambat ?? 0 }} hari</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Last Week's Attendance -->
    <div class="px-4 py-3 pb-6">
        <h2 class="text-sm font-semibold text-gray-800 mb-3">1 Minggu Terakhir</h2>
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="bg-[#ff040c] text-white px-3 py-2">
                <div class="grid grid-cols-3 text-xs font-medium">
                    <span>Tanggal</span>
                    <span>Jam Masuk</span>
                    <span>Jam Pulang</span>
                </div>
            </div>
            @if($lastWeek && $lastWeek->count() > 0)
                <div class="divide-y divide-gray-200">
                    @foreach($lastWeek->take(7) as $absensi)
                        <div class="grid grid-cols-3 px-3 py-2 text-xs">
                            <span class="text-gray-800">{{ \Carbon\Carbon::parse($absensi->tanggal)->format('d/m') }}</span>
                            <span class="text-gray-600">{{ $absensi->jam_masuk ?: '-' }}</span>
                            <span class="text-gray-600">{{ $absensi->jam_pulang ?: '-' }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="p-3 text-center text-gray-500">
                    <p class="text-xs">Belum ada data absensi</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Logout Button -->
    <div class="px-4 py-3 pb-6">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full bg-[#fb0302] text-white py-3 rounded-xl font-semibold hover:bg-[#ff040c] transition-colors">
                Logout
            </button>
        </form>
    </div>

    <!-- Debug Button (Development Only) -->
    @if(app()->environment('local'))
    <div class="px-4 py-3 pb-6">
        <div class="grid grid-cols-2 gap-3 mb-3">
            <button type="button" id="debugBtn" class="bg-yellow-500 text-white py-3 rounded-xl font-semibold hover:bg-yellow-600 transition-colors">
                Debug Data
            </button>
            <button type="button" id="refreshBtn" class="bg-blue-500 text-white py-3 rounded-xl font-semibold hover:bg-blue-600 transition-colors">
                Refresh Now
            </button>
        </div>
        <div class="grid grid-cols-2 gap-3 mb-3">
            <button type="button" id="testUserBtn" class="bg-green-500 text-white py-3 rounded-xl font-semibold hover:bg-green-600 transition-colors">
                Test User
            </button>
            <button type="button" id="testDistanceBtn" class="bg-purple-500 text-white py-3 rounded-xl font-semibold hover:bg-purple-600 transition-colors">
                Test Distance
            </button>
        </div>
        <div class="grid grid-cols-2 gap-3">
            <button type="button" id="testDistanceRequestBtn" class="bg-orange-500 text-white py-3 rounded-xl font-semibold hover:bg-orange-600 transition-colors">
                Test Request
            </button>
            <button type="button" id="testDistanceActualBtn" class="bg-red-500 text-white py-3 rounded-xl font-semibold hover:bg-red-600 transition-colors">
                Test GPS Real
            </button>
        </div>
        <div id="debugResult" class="mt-3 p-3 bg-gray-100 rounded-lg text-xs hidden"></div>
    </div>
    @endif

    <script>
        // Auto-refresh attendance data every 5 seconds
        function refreshAttendanceData() {
            fetch('{{ route("karyawan.attendance.current") }}')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update jam masuk
                        const jamMasukElement = document.getElementById('jam-masuk');
                        if (jamMasukElement) {
                            jamMasukElement.textContent = data.data.jam_masuk || 'Belum absen';
                        }
                        
                        // Update jam pulang
                        const jamPulangElement = document.getElementById('jam-pulang');
                        if (jamPulangElement) {
                            jamPulangElement.textContent = data.data.jam_pulang || 'Belum Absen';
                        }
                        
                        // Update status colors if needed
                        if (data.data.has_attendance) {
                            const masukCard = document.querySelector('[data-attendance="masuk"]');
                            if (masukCard && data.data.jam_masuk) {
                                masukCard.classList.add('bg-green-600');
                                masukCard.classList.remove('bg-[#ff040c]');
                            }
                        }
                    }
                })
                .catch(error => console.error('Error refreshing data:', error));
        }

        // Debug button functionality
        document.addEventListener('DOMContentLoaded', function() {
            const debugBtn = document.getElementById('debugBtn');
            const refreshBtn = document.getElementById('refreshBtn');
            const testUserBtn = document.getElementById('testUserBtn');
            const testDistanceBtn = document.getElementById('testDistanceBtn');
            const testDistanceRequestBtn = document.getElementById('testDistanceRequestBtn');
            const testDistanceActualBtn = document.getElementById('testDistanceActualBtn');
            
            if (debugBtn) {
                debugBtn.addEventListener('click', function() {
                    const debugResult = document.getElementById('debugResult');
                    debugResult.classList.remove('hidden');
                    
                                    // Get current attendance data
                fetch('{{ route("karyawan.attendance.current") }}')
                    .then(response => response.json())
                    .then(data => {
                        let debugInfo = '<strong>Debug Info:</strong><br>';
                        debugInfo += 'API Response: ' + JSON.stringify(data, null, 2) + '<br><br>';
                        debugInfo += 'Absen Masuk: ' + (data.data.jam_masuk || 'Belum absen') + '<br>';
                        debugInfo += 'Absen Pulang: ' + (data.data.jam_pulang || 'Belum Absen') + '<br>';
                        debugInfo += 'Status: ' + (data.data.status || 'N/A') + '<br>';
                        debugInfo += 'Has Attendance: ' + (data.data.has_attendance ? 'Yes' : 'No') + '<br>';
                        debugInfo += 'Current Time: ' + new Date().toLocaleString('id-ID') + '<br>';
                        debugInfo += 'Page URL: ' + window.location.href;
                        
                        debugResult.innerHTML = debugInfo;
                    })
                        .catch(error => {
                            document.getElementById('debugResult').innerHTML = 'Error: ' + error.message;
                        });
                });
            }
            
            if (refreshBtn) {
                refreshBtn.addEventListener('click', function() {
                    refreshAttendanceData();
                    this.textContent = 'Refreshing...';
                    this.disabled = true;
                    setTimeout(() => {
                        this.textContent = 'Refresh Now';
                        this.disabled = false;
                    }, 2000);
                });
            }
            
            if (testUserBtn) {
                testUserBtn.addEventListener('click', function() {
                    const debugResult = document.getElementById('debugResult');
                    debugResult.classList.remove('hidden');
                    debugResult.innerHTML = 'Loading user data...';
                    
                    fetch('/test-user')
                        .then(response => response.json())
                        .then(data => {
                            let debugInfo = '<strong>User Test Info:</strong><br>';
                            debugInfo += 'User ID: ' + (data.user?.id || 'N/A') + '<br>';
                            debugInfo += 'User Name: ' + (data.user?.name || 'N/A') + '<br>';
                            debugInfo += 'User Email: ' + (data.user?.email || 'N/A') + '<br>';
                            debugInfo += 'Lokasi Kantor ID: ' + (data.user?.lokasi_kantor_id || 'N/A') + '<br>';
                            debugInfo += 'User Role: ' + (data.user?.role || 'N/A') + '<br><br>';
                            
                            if (data.lokasi_kantor) {
                                debugInfo += '<strong>Office Location:</strong><br>';
                                debugInfo += 'Office ID: ' + data.lokasi_kantor.id + '<br>';
                                debugInfo += 'Office Name: ' + data.lokasi_kantor.nama_lokasi + '<br>';
                                debugInfo += 'Latitude: ' + data.lokasi_kantor.latitude + '<br>';
                                debugInfo += 'Longitude: ' + data.lokasi_kantor.longitude + '<br>';
                                debugInfo += 'Radius: ' + data.lokasi_kantor.radius + 'm<br><br>';
                            } else {
                                debugInfo += '<strong>Office Location: NOT FOUND!</strong><br><br>';
                            }
                            
                            if (data.today_absensi) {
                                debugInfo += '<strong>Today\'s Attendance:</strong><br>';
                                debugInfo += 'Attendance ID: ' + data.today_absensi.id + '<br>';
                                debugInfo += 'Jam Masuk: ' + (data.today_absensi.jam_masuk || 'N/A') + '<br>';
                                debugInfo += 'Jam Pulang: ' + (data.today_absensi.jam_pulang || 'N/A') + '<br>';
                                debugInfo += 'Status: ' + (data.today_absensi.status || 'N/A') + '<br>';
                            } else {
                                debugInfo += '<strong>Today\'s Attendance: NOT FOUND!</strong><br>';
                            }
                            
                            debugResult.innerHTML = debugInfo;
                        })
                        .catch(error => {
                            document.getElementById('debugResult').innerHTML = 'Error: ' + error.message;
                        });
                });
            }
            
            if (testDistanceBtn) {
                testDistanceBtn.addEventListener('click', function() {
                    const debugResult = document.getElementById('debugResult');
                    debugResult.classList.remove('hidden');
                    debugResult.innerHTML = 'Loading distance data...';
                    
                    fetch('/test-distance')
                        .then(response => response.json())
                        .then(data => {
                            let debugInfo = '<strong>Distance Test Info:</strong><br>';
                            debugInfo += '<strong>Test Coordinates:</strong><br>';
                            debugInfo += 'Latitude: ' + data.test_coordinates.latitude + '<br>';
                            debugInfo += 'Longitude: ' + data.test_coordinates.longitude + '<br><br>';
                            
                            debugInfo += '<strong>Office Coordinates:</strong><br>';
                            debugInfo += 'Latitude: ' + data.office_coordinates.latitude + '<br>';
                            debugInfo += 'Longitude: ' + data.office_coordinates.longitude + '<br><br>';
                            
                            debugInfo += '<strong>Distance Calculation:</strong><br>';
                            debugInfo += 'Office Radius: ' + data.office_radius + 'm<br>';
                            debugInfo += 'Calculated Distance: ' + data.calculated_distance + 'm<br>';
                            debugInfo += 'Distance (km): ' + data.distance_km + 'km<br>';
                            debugInfo += 'Within Radius: ' + (data.within_radius ? 'YES' : 'NO') + '<br>';
                            debugInfo += 'Status: ' + data.status + '<br><br>';
                            
                            debugInfo += '<strong>Analysis:</strong><br>';
                            if (data.within_radius) {
                                debugInfo += '✅ User berada dalam radius kantor<br>';
                                debugInfo += '✅ Bisa melakukan absensi<br>';
                            } else {
                                debugInfo += '❌ User berada di luar radius kantor<br>';
                                debugInfo += '❌ TIDAK BISA melakukan absensi<br>';
                                debugInfo += '⚠️ Radius kantor: ' + data.office_radius + 'm<br>';
                                debugInfo += '⚠️ Jarak user: ' + data.calculated_distance + 'm<br>';
                            }
                            
                            debugResult.innerHTML = debugInfo;
                        })
                        .catch(error => {
                            document.getElementById('debugResult').innerHTML = 'Error: ' + error.message;
                        });
                });
            }
            
            if (testDistanceRequestBtn) {
                testDistanceRequestBtn.addEventListener('click', function() {
                    const debugResult = document.getElementById('debugResult');
                    debugResult.classList.remove('hidden');
                    debugResult.innerHTML = 'Loading request distance data...';
                    
                    // Get coordinates from the last absen attempt or use test coordinates
                    const testLat = -7.6528390;
                    const testLon = 111.5339200;
                    
                    const formData = new FormData();
                    formData.append('latitude', testLat);
                    formData.append('longitude', testLon);
                    
                    fetch('/test-distance-request', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        let debugInfo = '<strong>Distance Request Test Info:</strong><br>';
                        debugInfo += '<strong>Request Coordinates:</strong><br>';
                        debugInfo += 'Latitude: ' + data.request_coordinates.latitude + '<br>';
                        debugInfo += 'Longitude: ' + data.request_coordinates.longitude + '<br><br>';
                        
                        debugInfo += '<strong>Office Coordinates:</strong><br>';
                        debugInfo += 'Latitude: ' + data.office_coordinates.latitude + '<br>';
                        debugInfo += 'Longitude: ' + data.office_coordinates.longitude + '<br><br>';
                        
                        debugInfo += '<strong>Distance Calculation:</strong><br>';
                        debugInfo += 'Office Radius: ' + data.office_radius + 'm<br>';
                        debugInfo += 'Calculated Distance: ' + data.calculated_distance + 'm<br>';
                        debugInfo += 'Distance (km): ' + data.distance_km + 'km<br>';
                        debugInfo += 'Within Radius: ' + (data.within_radius ? 'YES' : 'NO') + '<br>';
                        debugInfo += 'Status: ' + data.status + '<br><br>';
                        
                        debugInfo += '<strong>Coordinate Analysis:</strong><br>';
                        debugInfo += 'Coordinates Match: ' + (data.analysis.coordinates_match ? 'YES' : 'NO') + '<br>';
                        debugInfo += 'Test Coordinates: ' + data.analysis.test_coordinates.join(', ') + '<br>';
                        debugInfo += 'Request Coordinates: ' + data.analysis.request_coordinates.join(', ') + '<br><br>';
                        
                        debugInfo += '<strong>Analysis:</strong><br>';
                        if (data.within_radius) {
                            debugInfo += '✅ User berada dalam radius kantor<br>';
                            debugInfo += '✅ Bisa melakukan absensi<br>';
                        } else {
                            debugInfo += '❌ User berada di luar radius kantor<br>';
                            debugInfo += '❌ TIDAK BISA melakukan absensi<br>';
                            debugInfo += '⚠️ Radius kantor: ' + data.office_radius + 'm<br>';
                            debugInfo += '⚠️ Jarak user: ' + data.calculated_distance + 'm<br>';
                        }
                        
                        debugResult.innerHTML = debugInfo;
                    })
                    .catch(error => {
                        document.getElementById('debugResult').innerHTML = 'Error: ' + error.message;
                    });
                });
            }
            
            if (testDistanceActualBtn) {
                testDistanceActualBtn.addEventListener('click', function() {
                    const debugResult = document.getElementById('debugResult');
                    debugResult.classList.remove('hidden');
                    debugResult.innerHTML = 'Loading actual GPS distance data...';
                    
                    fetch('/test-distance-actual')
                        .then(response => response.json())
                        .then(data => {
                            let debugInfo = '<strong>Actual GPS Distance Test Info:</strong><br>';
                            debugInfo += '<strong>Actual GPS Coordinates:</strong><br>';
                            debugInfo += 'Latitude: ' + data.actual_gps_coordinates.latitude + '<br>';
                            debugInfo += 'Longitude: ' + data.actual_gps_coordinates.longitude + '<br><br>';
                            
                            debugInfo += '<strong>Office Coordinates:</strong><br>';
                            debugInfo += 'Latitude: ' + data.office_coordinates.latitude + '<br>';
                            debugInfo += 'Longitude: ' + data.office_coordinates.longitude + '<br><br>';
                            
                            debugInfo += '<strong>Distance Calculation:</strong><br>';
                            debugInfo += 'Office Radius: ' + data.office_radius + 'm<br>';
                            debugInfo += 'Calculated Distance: ' + data.calculated_distance + 'm<br>';
                            debugInfo += 'Distance (km): ' + data.distance_km + 'km<br>';
                            debugInfo += 'Within Radius: ' + (data.within_radius ? 'YES' : 'NO') + '<br>';
                            debugInfo += 'Status: ' + data.status + '<br><br>';
                            
                            debugInfo += '<strong>Coordinate Analysis:</strong><br>';
                            debugInfo += 'Coordinates Match: ' + (data.analysis.coordinates_match ? 'YES' : 'NO') + '<br>';
                            debugInfo += 'Test Coordinates: ' + data.analysis.test_coordinates.join(', ') + '<br>';
                            debugInfo += 'Actual GPS Coordinates: ' + data.analysis.actual_coordinates.join(', ') + '<br>';
                            debugInfo += 'Distance Difference: ' + data.analysis.distance_difference + '<br><br>';
                            
                            debugInfo += '<strong>Analysis:</strong><br>';
                            if (data.within_radius) {
                                debugInfo += '✅ User berada dalam radius kantor<br>';
                                debugInfo += '✅ Bisa melakukan absensi<br>';
                            } else {
                                debugInfo += '❌ User berada di luar radius kantor<br>';
                                debugInfo += '❌ TIDAK BISA melakukan absensi<br>';
                                debugInfo += '⚠️ Radius kantor: ' + data.office_radius + 'm<br>';
                                debugInfo += '⚠️ Jarak user: ' + data.calculated_distance + 'm<br>';
                            }
                            
                            debugResult.innerHTML = debugInfo;
                        })
                        .catch(error => {
                            document.getElementById('debugResult').innerHTML = 'Error: ' + error.message;
                        });
                });
            }
            
            // Start auto-refresh
            setInterval(refreshAttendanceData, 5000);
        });
    </script>
</body>
</html>
