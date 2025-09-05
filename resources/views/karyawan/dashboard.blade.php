<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard Karyawan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <x-karyawan-navbar title="Dashboard" subtitle="ASRI PROJECT" />

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
        <div class="grid grid-cols-5 gap-3">
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
            <a href="{{ route('karyawan.profile') }}" class="text-center">
                <div class="w-10 h-10 bg-[#ff040c] rounded-xl flex items-center justify-center mx-auto mb-2">
                    <i class="fas fa-user text-white text-sm"></i>
                </div>
                <span class="text-xs text-gray-700">Profil</span>
            </a>
            <a href="{{ route('karyawan.pesan.index') }}" class="text-center">
                <div class="w-10 h-10 bg-[#ff040c] rounded-xl flex items-center justify-center mx-auto mb-2 relative">
                    <i class="fas fa-envelope text-white text-sm"></i>
                    <span id="pesanBadge" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center font-bold z-10" style="display: none;">0</span>
                </div>
                <span class="text-xs text-gray-700">Pesan</span>
            </a>
        </div>
    </div>

    <!-- Attendance Status Cards -->
    <div class="px-4 py-3">
        <div class="grid grid-cols-2 gap-3">
            <div class="{{ $todayAbsensi && $todayAbsensi->jam_masuk ? 'bg-green-600' : 'bg-[#ff040c]' }} rounded-xl p-3 text-white" data-attendance="masuk">
                <h3 class="font-semibold mb-1 text-sm">Absen Masuk</h3>
                @if($todayAbsensi && $todayAbsensi->jam_masuk)
                    <p class="text-xs opacity-90" id="jam-masuk">{{ $todayAbsensi->jam_masuk }}</p>
                @else
                    <p class="text-xs opacity-90" id="jam-masuk">Belum absen</p>
                @endif
            </div>
            <div class="{{ $todayAbsensi && $todayAbsensi->jam_pulang ? 'bg-green-600' : 'bg-[#ff040c]' }} rounded-xl p-3 text-white" data-attendance="pulang">
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
        <div class="bg-white rounded-xl p-4 shadow-sm mb-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-[#ff040c] rounded-lg flex items-center justify-center">
                        <i class="fas fa-calendar-alt text-white text-sm"></i>
                    </div>
                    <div>
                        <h2 class="text-sm font-semibold text-gray-800">Ringkasan Absensi</h2>
                        <p class="text-xs text-gray-500">
                            Bulan <span class="text-[#ff040c] font-medium">{{ \Carbon\Carbon::now()->format('F Y') }}</span>
                        </p>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-xs text-gray-500">Total Hari Kerja</div>
                    <div class="text-sm font-semibold text-[#ff040c]">
                        {{ \Carbon\Carbon::now()->daysInMonth }} Hari
                    </div>
                </div>
            </div>
        </div>
        
        <div class="grid grid-cols-2 gap-3">
            <!-- Hadir -->
            <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-4 shadow-sm border border-green-200">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center">
                            <i class="fas fa-check text-white text-sm"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800 text-sm">Hadir</p>
                            <p class="text-xs text-gray-600">{{ $hadir ?? 0 }} Hari</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-lg font-bold text-green-600">{{ $hadir ?? 0 }}</div>
                        <div class="text-xs text-green-500">
                            @php
                                $totalDays = \Carbon\Carbon::now()->daysInMonth;
                                $percentage = $totalDays > 0 ? round((($hadir ?? 0) / $totalDays) * 100, 1) : 0;
                            @endphp
                            {{ $percentage }}%
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Izin -->
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-4 shadow-sm border border-blue-200">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center">
                            <i class="fas fa-user-clock text-white text-sm"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800 text-sm">Izin</p>
                            <p class="text-xs text-gray-600">{{ $izin ?? 0 }} Hari</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-lg font-bold text-blue-600">{{ $izin ?? 0 }}</div>
                        <div class="text-xs text-blue-500">
                            @php
                                $percentage = $totalDays > 0 ? round((($izin ?? 0) / $totalDays) * 100, 1) : 0;
                            @endphp
                            {{ $percentage }}%
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Sakit -->
            <div class="bg-gradient-to-br from-red-50 to-red-100 rounded-xl p-4 shadow-sm border border-red-200">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-red-500 rounded-lg flex items-center justify-center">
                            <i class="fas fa-thermometer-half text-white text-sm"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800 text-sm">Sakit</p>
                            <p class="text-xs text-gray-600">{{ $sakit ?? 0 }} Hari</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-lg font-bold text-red-600">{{ $sakit ?? 0 }}</div>
                        <div class="text-xs text-red-500">
                            @php
                                $percentage = $totalDays > 0 ? round((($sakit ?? 0) / $totalDays) * 100, 1) : 0;
                            @endphp
                            {{ $percentage }}%
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Terlambat -->
            <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-xl p-4 shadow-sm border border-yellow-200">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-yellow-500 rounded-lg flex items-center justify-center">
                            <i class="fas fa-clock text-white text-sm"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800 text-sm">Terlambat</p>
                            <p class="text-xs text-gray-600">{{ $terlambat ?? 0 }} Hari</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-lg font-bold text-yellow-600">{{ $terlambat ?? 0 }}</div>
                        <div class="text-xs text-yellow-500">
                            @php
                                $percentage = $totalDays > 0 ? round((($terlambat ?? 0) / $totalDays) * 100, 1) : 0;
                            @endphp
                            {{ $percentage }}%
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Progress Bar -->
        <div class="mt-4 bg-white rounded-xl p-4 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-semibold text-gray-800">Tingkat Kehadiran</h3>
                <span class="text-xs text-gray-500">
                    @php
                        $totalAttendance = ($hadir ?? 0) + ($izin ?? 0) + ($sakit ?? 0);
                        $attendanceRate = $totalDays > 0 ? round(($totalAttendance / $totalDays) * 100, 1) : 0;
                    @endphp
                    {{ $attendanceRate }}% dari {{ $totalDays }} hari
                </span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-gradient-to-r from-[#ff040c] to-[#fb0302] h-2 rounded-full transition-all duration-300" 
                     style="width: {{ $attendanceRate }}%"></div>
            </div>
            <div class="flex justify-between text-xs text-gray-500 mt-1">
                <span>0%</span>
                <span>50%</span>
                <span>100%</span>
            </div>
        </div>
    </div>

    <!-- Last Week's Attendance -->
    <div class="px-4 py-3 pb-6">
        <h2 class="text-sm font-semibold text-gray-800 mb-3">1 Minggu Terakhir</h2>
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="bg-[#ff040c] text-white px-3 py-2">
                <div class="grid grid-cols-4 text-xs font-medium">
                    <span>Tanggal</span>
                    <span>Jam Masuk</span>
                    <span>Jam Pulang</span>
                    <span>Status</span>
                </div>
            </div>
            @if($lastWeek && $lastWeek->count() > 0)
                <div class="divide-y divide-gray-200">
                    @foreach($lastWeek->take(7) as $absensi)
                        <div class="grid grid-cols-4 px-3 py-2 text-xs">
                            <span class="text-gray-800">{{ \Carbon\Carbon::parse($absensi->tanggal)->format('d/m') }}</span>
                            <span class="text-gray-600">{{ $absensi->jam_masuk ?: '-' }}</span>
                            <span class="text-gray-600">{{ $absensi->jam_pulang ?: '-' }}</span>
                            <span class="text-xs font-medium {{ $absensi->status === 'terlambat' ? 'text-red-600' : ($absensi->status === 'hadir' ? 'text-green-600' : 'text-gray-400') }}">
                                @if($absensi->status === 'terlambat')
                                    Terlambat
                                @elseif($absensi->status === 'hadir')
                                    Tepat Waktu
                                @else
                                    -
                                @endif
                            </span>
                        </div>
                    @endforeach
                    
                    @if($lastWeek->count() < 7)
                        @for($i = $lastWeek->count(); $i < 7; $i++)
                            <div class="grid grid-cols-4 px-3 py-2 text-xs">
                                <span class="text-gray-400">-</span>
                                <span class="text-gray-400">-</span>
                                <span class="text-gray-400">-</span>
                                <span class="text-gray-400">-</span>
                            </div>
                        @endfor
                    @endif
                </div>
            @else
                <div class="divide-y divide-gray-200">
                    @for($i = 0; $i < 7; $i++)
                        <div class="grid grid-cols-4 px-3 py-2 text-xs">
                            <span class="text-gray-400">-</span>
                            <span class="text-gray-400">-</span>
                            <span class="text-gray-400">-</span>
                            <span class="text-gray-400">-</span>
                        </div>
                    @endfor
                </div>
            @endif
        </div>
    </div>



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
                        const masukCard = document.querySelector('[data-attendance="masuk"]');
                        const pulangCard = document.querySelector('[data-attendance="pulang"]');
                        
                        if (masukCard) {
                            if (data.data.jam_masuk) {
                                masukCard.classList.add('bg-green-600');
                                masukCard.classList.remove('bg-[#ff040c]');
                            } else {
                                masukCard.classList.add('bg-[#ff040c]');
                                masukCard.classList.remove('bg-green-600');
                            }
                        }
                        
                        if (pulangCard) {
                            if (data.data.jam_pulang) {
                                pulangCard.classList.add('bg-green-600');
                                pulangCard.classList.remove('bg-[#ff040c]');
                            } else {
                                pulangCard.classList.add('bg-[#ff040c]');
                                pulangCard.classList.remove('bg-green-600');
                            }
                        }
                    }
                })
                .catch(error => console.error('Error refreshing data:', error));
        }

        // Load unread message count for pesan badge
        function loadPesanBadge() {
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            const headers = {};
            if (csrfToken) {
                headers['X-CSRF-TOKEN'] = csrfToken.getAttribute('content');
            }
            
            fetch('{{ route("karyawan.pesan.unread-count") }}', {
                headers: headers
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    const pesanBadge = document.getElementById('pesanBadge');
                    
                    if (data.unread_count > 0) {
                        if (pesanBadge) {
                            pesanBadge.textContent = data.unread_count;
                            pesanBadge.style.display = 'flex';
                        }
                    } else {
                        if (pesanBadge) {
                            pesanBadge.style.display = 'none';
                        }
                    }
                })
                .catch(error => {
                    console.error('Error loading pesan badge:', error);
                });
        }

        // Start auto-refresh
        document.addEventListener('DOMContentLoaded', function() {
            // Load pesan badge immediately
            loadPesanBadge();
            
            // Set up intervals
            setInterval(refreshAttendanceData, 5000);
            setInterval(loadPesanBadge, 10000); // Refresh pesan badge every 10 seconds
        });
    </script>
</body>
</html>
