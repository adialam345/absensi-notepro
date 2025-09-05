 <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Absensi - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.3.2/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <x-admin-navbar title="Laporan Absensi" subtitle="Data Kehadiran Karyawan" />

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto mt-6 px-4">
        <!-- Filter Section -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-xl font-bold mb-4" style="color: #ff040c;">Filter Laporan</h2>
            <form method="GET" action="{{ route('admin.laporan.absensi') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Bulan</label>
                    <select name="month" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#ff040c]">
                        <option value="">Pilih Bulan</option>
                        @for($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" {{ request('month') == $i ? 'selected' : '' }}>
                                {{ date('F', mktime(0, 0, 0, $i, 1)) }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tahun</label>
                    <select name="year" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#ff040c]">
                        <option value="">Pilih Tahun</option>
                        @for($i = date('Y'); $i >= date('Y') - 5; $i--)
                            <option value="{{ $i }}" {{ request('year') == $i ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Karyawan</label>
                    <select name="karyawan" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#ff040c]">
                        <option value="">Semua Karyawan</option>
                        @foreach($karyawan as $k)
                            <option value="{{ $k->id }}" {{ request('karyawan') == $k->id ? 'selected' : '' }}>
                                {{ $k->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full bg-[#ff040c] text-white px-4 py-2 rounded-md hover:bg-[#fb0302] transition-colors">
                        <i class="fas fa-search mr-2"></i>Filter
                    </button>
                </div>
            </form>
        </div>

        <!-- Export Buttons -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h3 class="text-lg font-semibold mb-4" style="color: #ff040c;">Export Data</h3>
            <div class="flex flex-wrap gap-4">
                <a href="{{ route('admin.export.absensi.excel', request()->query()) }}" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors">
                    <i class="fas fa-file-excel mr-2"></i>Export Excel
                </a>
                <a href="{{ route('admin.export.absensi.pdf', request()->query()) }}" class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 transition-colors">
                    <i class="fas fa-file-pdf mr-2"></i>Export PDF
                </a>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="flex overflow-x-auto gap-4 mb-6 pb-4 md:grid md:grid-cols-6 md:gap-6 md:overflow-visible md:pb-0">
            <div class="bg-white rounded-lg shadow p-6 flex-shrink-0 min-w-[200px]">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100">
                        <i class="fas fa-check text-green-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Hadir</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $summary['hadir'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-6 flex-shrink-0 min-w-[200px]">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-100">
                        <i class="fas fa-clock text-yellow-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Terlambat</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $summary['terlambat'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-6 flex-shrink-0 min-w-[200px]">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100">
                        <i class="fas fa-user-times text-blue-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Izin</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $summary['izin'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-6 flex-shrink-0 min-w-[200px]">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-red-100">
                        <i class="fas fa-bed text-red-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Sakit</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $summary['sakit'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-6 flex-shrink-0 min-w-[200px]">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-orange-100">
                        <i class="fas fa-calendar-times text-orange-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Cuti</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $summary['cuti'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-6 flex-shrink-0 min-w-[200px]">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-100">
                        <i class="fas fa-map-marker-alt text-purple-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Dinas Luar</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $summary['dinas_luar'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Data Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Data Absensi</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jam Masuk</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jam Pulang</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dinas Luar</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Foto</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($absensi as $a)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $a->user->name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ \Carbon\Carbon::parse($a->tanggal)->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $a->jam_masuk ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $a->jam_pulang ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $statusColors = [
                                            'hadir' => 'bg-green-100 text-green-800',
                                            'terlambat' => 'bg-yellow-100 text-yellow-800',
                                            'izin' => 'bg-blue-100 text-blue-800',
                                            'sakit' => 'bg-red-100 text-red-800'
                                        ];
                                    @endphp
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColors[$a->status] ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ ucfirst($a->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($a->dinas_luar)
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                            <i class="fas fa-map-marker-alt mr-1"></i>
                                            Ya
                                        </span>
                                        @if($a->alasan_dinas_luar)
                                            <div class="text-xs text-gray-500 mt-1" title="{{ $a->alasan_dinas_luar }}">
                                                {{ Str::limit($a->alasan_dinas_luar, 30) }}
                                            </div>
                                        @endif
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                            <i class="fas fa-building mr-1"></i>
                                            Tidak
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    @if($a->foto_masuk || $a->foto_pulang)
                                        <a href="{{ route('admin.absensi.foto', $a->id) }}" 
                                           class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md text-white bg-[#ff040c] hover:bg-[#fb0302] transition-colors">
                                            <i class="fas fa-camera mr-1"></i>
                                            Lihat Foto
                                        </a>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $a->keterangan ?? '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500">
                                    Tidak ada data absensi untuk periode yang dipilih.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</body>
</html>
