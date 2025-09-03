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
            <div>
                <h1 class="font-semibold text-sm">
                    @php
                        $hour = now()->hour;
                        if ($hour < 12) {
                            echo 'Selamat Pagi';
                        } elseif ($hour < 15) {
                            echo 'Selamat Siang';
                        } elseif ($hour < 18) {
                            echo 'Selamat Sore';
                        } else {
                            echo 'Selamat Malam';
                        }
                    @endphp
                </h1>
                <p class="text-xs opacity-90">{{ Auth::user()->name ?? 'Karyawan' }}</p>
            </div>
            <div class="text-right">
                <p class="text-xs opacity-90">{{ now()->format('d M Y') }}</p>
                <p class="text-xs opacity-90">{{ now()->format('H:i') }}</p>
            </div>
        </div>
    </div>

    <!-- Work Hours Info -->
    <div class="px-4 py-3">
        <div class="bg-white rounded-xl p-3 shadow-sm">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="font-semibold text-sm text-gray-800">Jam Kerja</h3>
                    <p class="text-xs text-gray-600">{{ $jamMasuk ?? '08:00' }} - {{ $jamPulang ?? '17:00' }}</p>
                </div>
                <div class="text-right">
                    <p class="text-xs text-gray-600">Status</p>
                    <p class="text-xs font-medium text-green-600">Aktif</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="px-4 py-3">
        <div class="grid grid-cols-2 gap-3">
            <a href="{{ route('karyawan.absen.masuk') }}" class="bg-[#ff040c] rounded-xl p-4 text-white text-center hover:bg-[#fb0302] transition-colors">
                <i class="fas fa-sign-in-alt text-xl mb-2"></i>
                <p class="font-semibold text-sm">Absen Masuk</p>
            </a>
            <a href="#" class="bg-[#ff040c] rounded-xl p-4 text-white text-center hover:bg-[#fb0302] transition-colors">
                <i class="fas fa-sign-out-alt text-xl mb-2"></i>
                <p class="font-semibold text-sm">Absen Pulang</p>
            </a>
        </div>
    </div>

    <!-- Attendance Status Cards -->
    <div class="px-4 py-3">
        <div class="grid grid-cols-2 gap-3">
            <div class="bg-[#ff040c] rounded-xl p-3 text-white">
                <h3 class="font-semibold mb-1 text-sm">Absen Masuk</h3>
                @if(isset($todayAbsensi) && $todayAbsensi && $todayAbsensi->jam_masuk)
                    <p class="text-xs opacity-90">{{ $todayAbsensi->jam_masuk }}</p>
                @else
                    <p class="text-xs opacity-90">Belum absen</p>
                @endif
            </div>
            <div class="bg-[#ff040c] rounded-xl p-3 text-white">
                <h3 class="font-semibold mb-1 text-sm">Absen Pulang</h3>
                @if(isset($todayAbsensi) && $todayAbsensi && $todayAbsensi->jam_pulang)
                    <p class="text-xs opacity-90">{{ $todayAbsensi->jam_pulang }}</p>
                @else
                    <p class="text-xs opacity-90">Belum Absen</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Monthly Summary -->
    <div class="px-4 py-3">
        <div class="bg-white rounded-xl p-3 shadow-sm">
            <h3 class="font-semibold text-sm text-gray-800 mb-3">Ringkasan Bulan Ini</h3>
            <div class="grid grid-cols-4 gap-2">
                <div class="text-center">
                    <p class="text-lg font-bold text-green-600">{{ $hadir ?? 0 }}</p>
                    <p class="text-xs text-gray-600">Hadir</p>
                </div>
                <div class="text-center">
                    <p class="text-lg font-bold text-blue-600">{{ $izin ?? 0 }}</p>
                    <p class="text-xs text-gray-600">Izin</p>
                </div>
                <div class="text-center">
                    <p class="text-lg font-bold text-yellow-600">{{ $sakit ?? 0 }}</p>
                    <p class="text-xs text-gray-600">Sakit</p>
            </div>
                <div class="text-center">
                    <p class="text-lg font-bold text-red-600">{{ $terlambat ?? 0 }}</p>
                    <p class="text-xs text-gray-600">Terlambat</p>
            </div>
                    </div>
                    </div>
                    </div>

    <!-- Navigation Menu -->
    <div class="px-4 py-3">
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <a href="{{ route('karyawan.absen.masuk') }}" class="flex items-center p-3 border-b border-gray-100 hover:bg-gray-50 transition-colors">
                <i class="fas fa-camera text-[#ff040c] w-6"></i>
                <span class="ml-3 text-sm text-gray-800">Absen Masuk</span>
                <i class="fas fa-chevron-right text-gray-400 ml-auto"></i>
            </a>
            <a href="{{ route('karyawan.izin.cuti') }}" class="flex items-center p-3 border-b border-gray-100 hover:bg-gray-50 transition-colors">
                <i class="fas fa-calendar-alt text-[#ff040c] w-6"></i>
                <span class="ml-3 text-sm text-gray-800">Izin & Cuti</span>
                <i class="fas fa-chevron-right text-gray-400 ml-auto"></i>
            </a>
            <a href="{{ route('karyawan.history') }}" class="flex items-center p-3 border-b border-gray-100 hover:bg-gray-50 transition-colors">
                <i class="fas fa-history text-[#ff040c] w-6"></i>
                <span class="ml-3 text-sm text-gray-800">Riwayat Absen</span>
                <i class="fas fa-chevron-right text-gray-400 ml-auto"></i>
            </a>
            <a href="{{ route('karyawan.profile') }}" class="flex items-center p-3 hover:bg-gray-50 transition-colors">
                <i class="fas fa-user text-[#ff040c] w-6"></i>
                <span class="ml-3 text-sm text-gray-800">Profil</span>
                <i class="fas fa-chevron-right text-gray-400 ml-auto"></i>
            </a>
                    </div>
                </div>

    <!-- Last Week Attendance -->
    @if(isset($lastWeek) && $lastWeek->count() > 0)
    <div class="px-4 py-3">
        <div class="bg-white rounded-xl p-3 shadow-sm">
            <h3 class="font-semibold text-sm text-gray-800 mb-3">Absen Minggu Ini</h3>
            <div class="space-y-2">
                @foreach($lastWeek->take(5) as $absensi)
                <div class="flex justify-between items-center text-xs">
                    <span class="text-gray-600">{{ $absensi->tanggal->format('d M') }}</span>
                    <span class="font-medium {{ $absensi->status === 'hadir' ? 'text-green-600' : ($absensi->status === 'terlambat' ? 'text-yellow-600' : 'text-red-600') }}">
                        {{ ucfirst($absensi->status) }}
                    </span>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- Logout Button -->
    <div class="px-4 py-3">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full bg-gray-600 text-white py-3 rounded-xl font-semibold hover:bg-gray-700 transition-colors text-sm">
                <i class="fas fa-sign-out-alt mr-2"></i>Logout
            </button>
        </form>
    </div>
</body>
</html>


</body>
</html>


