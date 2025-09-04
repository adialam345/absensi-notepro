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
                    
                    @if($lastWeek->count() < 7)
                        @for($i = $lastWeek->count(); $i < 7; $i++)
                            <div class="grid grid-cols-3 px-3 py-2 text-xs">
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
                        <div class="grid grid-cols-3 px-3 py-2 text-xs">
                            <span class="text-gray-400">-</span>
                            <span class="text-gray-400">-</span>
                            <span class="text-gray-400">-</span>
                        </div>
                    @endfor
                </div>
            @endif
        </div>
    </div>

    <!-- Logout Button -->
    <div class="px-4 py-3 pb-8">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full bg-[#fb0302] text-white py-3 rounded-xl font-semibold hover:bg-[#ff040c] transition-colors">
                Logout
            </button>
        </form>
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

        // Start auto-refresh
        document.addEventListener('DOMContentLoaded', function() {
            setInterval(refreshAttendanceData, 5000);
        });
    </script>
</body>
</html>
