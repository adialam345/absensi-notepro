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
    <x-admin-navbar title="Dashboard" subtitle="ABSENSI PROJECT" />

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
        <div class="grid grid-cols-3 gap-3">
            <a href="{{ route('admin.karyawan.index') }}" class="bg-white rounded-xl p-3 shadow-sm hover:shadow-md transition-shadow text-center">
                <div class="w-10 h-10 bg-[#ff040c] rounded-lg flex items-center justify-center mx-auto mb-2">
                    <i class="fas fa-users text-white text-sm"></i>
                </div>
                <h3 class="font-medium text-gray-800 text-xs mb-1">Kelola Karyawan</h3>
                <p class="text-xs text-gray-500">Tambah, edit, hapus data</p>
            </a>
            
            <a href="{{ route('admin.lokasi.index') }}" class="bg-white rounded-xl p-3 shadow-sm hover:shadow-md transition-shadow text-center">
                <div class="w-10 h-10 bg-[#ff040c] rounded-lg flex items-center justify-center mx-auto mb-2">
                    <i class="fas fa-map-marker-alt text-white text-sm"></i>
                </div>
                <h3 class="font-medium text-gray-800 text-xs mb-1">Lokasi Kantor</h3>
                <p class="text-xs text-gray-500">Kelola lokasi dan area</p>
            </a>
            
            <a href="{{ route('admin.laporan.absensi') }}" class="bg-white rounded-xl p-3 shadow-sm hover:shadow-md transition-shadow text-center">
                <div class="w-10 h-10 bg-[#ff040c] rounded-lg flex items-center justify-center mx-auto mb-2">
                    <i class="fas fa-chart-bar text-white text-sm"></i>
                </div>
                <h3 class="font-medium text-gray-800 text-xs mb-1">Laporan Absensi</h3>
                <p class="text-xs text-gray-500">Lihat laporan kehadiran</p>
            </a>
            
            <a href="{{ route('admin.cuti.index') }}" class="bg-white rounded-xl p-3 shadow-sm hover:shadow-md transition-shadow text-center">
                <div class="w-10 h-10 bg-[#ff040c] rounded-lg flex items-center justify-center mx-auto mb-2">
                    <i class="fas fa-calendar-times text-white text-sm"></i>
                </div>
                <h3 class="font-medium text-gray-800 text-xs mb-1">Kelola Cuti & Izin</h3>
                <p class="text-xs text-gray-500">Setujui/tolak pengajuan</p>
            </a>
            
            <a href="{{ route('admin.pesan.index') }}" class="bg-white rounded-xl p-3 shadow-sm hover:shadow-md transition-shadow text-center">
                <div class="w-10 h-10 bg-[#ff040c] rounded-lg flex items-center justify-center mx-auto mb-2">
                    <i class="fas fa-envelope text-white text-sm"></i>
                </div>
                <h3 class="font-medium text-gray-800 text-xs mb-1">Kirim Pesan</h3>
                <p class="text-xs text-gray-500">Kirim pesan ke karyawan</p>
            </a>
            
            <a href="{{ route('admin.statistik') }}" class="bg-white rounded-xl p-3 shadow-sm hover:shadow-md transition-shadow text-center">
                <div class="w-10 h-10 bg-[#ff040c] rounded-lg flex items-center justify-center mx-auto mb-2">
                    <i class="fas fa-chart-line text-white text-sm"></i>
                </div>
                <h3 class="font-medium text-gray-800 text-xs mb-1">Statistik</h3>
                <p class="text-xs text-gray-500">Lihat statistik kehadiran</p>
            </a>
        </div>
    </div>


    <!-- Recent Employee Activities -->
    <div class="px-4 py-3">
        <div class="flex items-center justify-between mb-3">
            <h2 class="text-sm font-semibold text-gray-700">Aktivitas Terbaru</h2>
            <a href="{{ route('admin.laporan.absensi') }}" class="text-xs text-[#ff040c] hover:text-[#fb0302] transition-colors">
                Lihat Semua <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="bg-[#ff040c] text-white px-4 py-3">
                <div class="grid grid-cols-12 gap-2 text-xs font-medium">
                    <div class="col-span-3">Waktu</div>
                    <div class="col-span-4">Karyawan</div>
                    <div class="col-span-3">Status</div>
                    <div class="col-span-2 text-center">Foto</div>
                </div>
            </div>
            <div class="max-h-40 overflow-y-auto">
                @if(isset($recentActivities) && $recentActivities->count() > 0)
                    @foreach($recentActivities->take(5) as $activity)
                        <div class="grid grid-cols-12 gap-2 items-center px-4 py-3 {{ !$loop->last ? 'border-b border-gray-100' : '' }} hover:bg-gray-50 transition-colors">
                            <div class="col-span-3">
                                <div class="text-xs font-medium text-gray-900">
                                    {{ $activity->jam_masuk ? \Carbon\Carbon::parse($activity->jam_masuk)->format('H:i') : 'N/A' }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ \Carbon\Carbon::parse($activity->tanggal)->format('d/m') }}
                                </div>
                            </div>
                            <div class="col-span-4">
                                <div class="text-xs font-medium text-gray-900 truncate">
                                    {{ $activity->user->name ?? 'Unknown' }}
                                </div>
                                <div class="text-xs text-gray-500 truncate">
                                    {{ $activity->user->jabatan ?? '' }}
                                </div>
                            </div>
                            <div class="col-span-3">
                                @php
                                    $statusColors = [
                                        'hadir' => 'bg-green-100 text-green-800',
                                        'terlambat' => 'bg-yellow-100 text-yellow-800',
                                        'izin' => 'bg-blue-100 text-blue-800',
                                        'sakit' => 'bg-red-100 text-red-800'
                                    ];
                                @endphp
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $statusColors[$activity->status] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ ucfirst($activity->status ?? 'Absen') }}
                                </span>
                            </div>
                            <div class="col-span-2 text-center">
                                @if($activity->foto_masuk || $activity->foto_pulang)
                                    <a href="{{ route('admin.absensi.foto', $activity->id) }}" 
                                       class="inline-flex items-center justify-center w-7 h-7 bg-[#ff040c] text-white rounded-full hover:bg-[#fb0302] transition-colors shadow-sm"
                                       title="Lihat Foto">
                                        <i class="fas fa-camera text-xs"></i>
                                    </a>
                                @else
                                    <div class="inline-flex items-center justify-center w-7 h-7 bg-gray-100 text-gray-400 rounded-full">
                                        <i class="fas fa-camera text-xs"></i>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="p-6 text-center text-gray-500">
                        <i class="fas fa-clock text-2xl mb-2 text-gray-300"></i>
                        <p class="text-xs">Belum ada aktivitas absensi</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

</body>
</html>
