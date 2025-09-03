<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
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

    <!-- User Greeting -->
    <div class="bg-white rounded-t-2xl -mt-1 p-4 shadow-sm">
        <div class="flex justify-between items-center">
            <div>
                <p class="text-gray-500 text-xs">Selamat Datang</p>
                <h1 class="text-lg font-semibold text-[#ff040c]">{{ Auth::user()->name ?? 'Admin' }}</h1>
            </div>
            <div class="text-right">
                <p class="text-gray-500 text-xs">Role</p>
                <div class="text-sm font-medium text-[#ff040c]">Administrator</div>
            </div>
        </div>
    </div>

    <!-- System Overview -->
    <div class="px-4 py-3">
        <div class="grid grid-cols-3 gap-3">
            <div class="bg-white rounded-xl p-3 text-center shadow-sm">
                <div class="text-lg font-bold text-[#ff040c]">{{ $totalKaryawan ?? 0 }}</div>
                <div class="text-xs text-gray-600">Total Karyawan</div>
            </div>
            <div class="bg-white rounded-xl p-3 text-center shadow-sm">
                <div class="text-lg font-bold text-[#ff040c]">{{ $karyawanAktif ?? 0 }}</div>
                <div class="text-xs text-gray-600">Aktif</div>
            </div>
            <div class="bg-white rounded-xl p-3 text-center shadow-sm">
                <div class="text-lg font-bold text-[#ff040c]">{{ $totalLokasi ?? 0 }}</div>
                <div class="text-xs text-gray-600">Lokasi Kantor</div>
            </div>
        </div>
    </div>

    <!-- Main Management -->
    <div class="px-4 py-3">
        <h2 class="text-sm font-semibold text-gray-700 mb-3">Manajemen Utama</h2>
        <div class="space-y-3">
            <a href="{{ route('admin.karyawan.index') }}" class="bg-white rounded-xl p-4 shadow-sm hover:shadow-md transition-shadow flex items-center space-x-3">
                <div class="w-8 h-8 bg-[#ff040c] rounded-lg flex items-center justify-center">
                    <i class="fas fa-users text-white text-sm"></i>
                </div>
                <div class="flex-1">
                    <h3 class="font-medium text-gray-800 text-sm">Kelola Karyawan</h3>
                    <p class="text-xs text-gray-500">Tambah, edit, hapus data karyawan</p>
                </div>
                <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
            </a>
            
            <a href="{{ route('admin.lokasi.index') }}" class="bg-white rounded-xl p-4 shadow-sm hover:shadow-md transition-shadow flex items-center space-x-3">
                <div class="w-8 h-8 bg-[#ff040c] rounded-lg flex items-center justify-center">
                    <i class="fas fa-map-marker-alt text-white text-sm"></i>
                </div>
                <div class="flex-1">
                    <h3 class="font-medium text-gray-800 text-sm">Lokasi Kantor</h3>
                    <p class="text-xs text-gray-500">Kelola lokasi dan area kerja</p>
                </div>
                <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
            </a>
            
            <a href="{{ route('admin.laporan.absensi') }}" class="bg-white rounded-xl p-4 shadow-sm hover:shadow-md transition-shadow flex items-center space-x-3">
                <div class="w-8 h-8 bg-[#ff040c] rounded-lg flex items-center justify-center">
                    <i class="fas fa-chart-bar text-white text-sm"></i>
                </div>
                <div class="flex-1">
                    <h3 class="font-medium text-gray-800 text-sm">Laporan Absensi</h3>
                    <p class="text-xs text-gray-500">Lihat laporan kehadiran karyawan</p>
                </div>
                <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
            </a>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="px-4 py-3">
        <h2 class="text-sm font-semibold text-gray-700 mb-3">Aksi Cepat</h2>
        <div class="grid grid-cols-4 gap-2">
            <a href="{{ route('admin.karyawan.create') }}" class="bg-white rounded-lg p-3 shadow-sm hover:shadow-md transition-shadow text-center">
                <div class="w-6 h-6 bg-[#fb0302] rounded-full flex items-center justify-center mx-auto mb-1">
                    <i class="fas fa-user-plus text-white text-xs"></i>
                </div>
                <span class="text-xs font-medium text-gray-700">Tambah</span>
            </a>
            
            <a href="{{ route('admin.export.karyawan') }}" class="bg-white rounded-lg p-3 shadow-sm hover:shadow-md transition-shadow text-center">
                <div class="w-6 h-6 bg-[#fb0302] rounded-full flex items-center justify-center mx-auto mb-1">
                    <i class="fas fa-download text-white text-xs"></i>
                </div>
                <span class="text-xs font-medium text-gray-700">Export</span>
            </a>
            
            <a href="{{ route('admin.cuti.index') }}" class="bg-white rounded-lg p-3 shadow-sm hover:shadow-md transition-shadow text-center">
                <div class="w-6 h-6 bg-[#fb0302] rounded-full flex items-center justify-center mx-auto mb-1">
                    <i class="fas fa-calendar-check text-white text-xs"></i>
                </div>
                <span class="text-xs font-medium text-gray-700">Cuti</span>
            </a>
            
            <a href="{{ route('admin.lokasi.create') }}" class="bg-white rounded-lg p-3 shadow-sm hover:shadow-md transition-shadow text-center">
                <div class="w-6 h-6 bg-[#fb0302] rounded-full flex items-center justify-center mx-auto mb-1">
                    <i class="fas fa-plus text-white text-xs"></i>
                </div>
                <span class="text-xs font-medium text-gray-700">Lokasi</span>
            </a>
        </div>
    </div>

    <!-- Recent Employee Activities -->
    <div class="px-4 py-3">
        <h2 class="text-sm font-semibold text-gray-700 mb-3">Aktivitas Karyawan Terbaru</h2>
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="bg-[#ff040c] text-white px-3 py-2">
                <div class="grid grid-cols-3 text-xs font-medium">
                    <span>Waktu</span>
                    <span>Karyawan</span>
                    <span>Aktivitas</span>
                </div>
            </div>
            <div class="p-3">
                @if(isset($recentActivities) && $recentActivities->count() > 0)
                    @foreach($recentActivities as $activity)
                        <div class="flex items-center justify-between py-2 {{ !$loop->last ? 'border-b border-gray-100' : '' }}">
                            <span class="text-xs text-gray-600">{{ $activity->jam_masuk ?? 'N/A' }}</span>
                            <span class="text-xs text-gray-800">{{ $activity->user->name ?? 'Unknown' }}</span>
                            <span class="text-xs text-gray-600">{{ $activity->status ?? 'Absen masuk' }}</span>
                        </div>
                    @endforeach
                @else
                    <div class="p-3 text-center text-gray-500">
                        <p class="text-xs">Belum ada aktivitas</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Logout Button -->
    <div class="px-4 py-3 pb-6">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full bg-[#fb0302] text-white py-2.5 rounded-xl font-medium hover:bg-[#ff040c] transition-colors text-sm">
                Logout
            </button>
        </form>
    </div>
</body>
</html>
