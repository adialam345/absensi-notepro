<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>History Absensi - Karyawan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <div class="bg-[#ff040c] p-3 text-white">
        <div class="flex justify-between items-center">
            <div class="flex items-center space-x-2">
                <a href="{{ route('karyawan.dashboard') }}" class="text-white">
                    <i class="fas fa-arrow-left text-lg"></i>
                </a>
                <span class="font-semibold text-sm">History Absensi</span>
            </div>
        </div>
    </div>

    <!-- Content -->
    <div class="p-4">
        <!-- Filter -->
        <div class="bg-white rounded-xl shadow-sm p-4 mb-4">
            <form method="GET" class="flex flex-wrap gap-3 items-end">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Bulan</label>
                    <select name="month" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#ff040c] text-sm">
                        <option value="1" {{ $month == 1 ? 'selected' : '' }}>Januari</option>
                        <option value="2" {{ $month == 2 ? 'selected' : '' }}>Februari</option>
                        <option value="3" {{ $month == 3 ? 'selected' : '' }}>Maret</option>
                        <option value="4" {{ $month == 4 ? 'selected' : '' }}>April</option>
                        <option value="5" {{ $month == 5 ? 'selected' : '' }}>Mei</option>
                        <option value="6" {{ $month == 6 ? 'selected' : '' }}>Juni</option>
                        <option value="7" {{ $month == 7 ? 'selected' : '' }}>Juli</option>
                        <option value="8" {{ $month == 8 ? 'selected' : '' }}>Agustus</option>
                        <option value="9" {{ $month == 9 ? 'selected' : '' }}>September</option>
                        <option value="10" {{ $month == 10 ? 'selected' : '' }}>Oktober</option>
                        <option value="11" {{ $month == 11 ? 'selected' : '' }}>November</option>
                        <option value="12" {{ $month == 12 ? 'selected' : '' }}>Desember</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Tahun</label>
                    <select name="year" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#ff040c] text-sm">
                        @for($y = date('Y'); $y >= date('Y') - 2; $y--)
                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                
                <button type="submit" class="px-4 py-2 bg-[#ff040c] text-white rounded-lg hover:bg-[#fb0302] transition-colors text-sm">
                    <i class="fas fa-search mr-1"></i>Filter
                </button>
            </form>
        </div>

        <!-- Attendance List -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-[#ff040c] text-white">
                        <tr>
                            <th class="px-3 py-2 text-left text-xs font-medium">Tanggal</th>
                            <th class="px-3 py-2 text-left text-xs font-medium">Jam Masuk</th>
                            <th class="px-3 py-2 text-left text-xs font-medium">Jam Pulang</th>
                            <th class="px-3 py-2 text-left text-xs font-medium">Status</th>
                            <th class="px-3 py-2 text-left text-xs font-medium">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($absensi as $a)
                            <tr class="hover:bg-gray-50">
                                <td class="px-3 py-2">
                                    <div class="text-sm text-gray-900">
                                        {{ \Carbon\Carbon::parse($a->tanggal)->format('d/m/Y') }}
                                        <div class="text-xs text-gray-500">
                                            {{ \Carbon\Carbon::parse($a->tanggal)->format('l') }}
                                        </div>
                                    </div>
                                </td>
                                <td class="px-3 py-2">
                                    @if($a->jam_masuk)
                                        <span class="text-sm text-gray-900">{{ $a->jam_masuk }}</span>
                                    @else
                                        <span class="text-xs text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-3 py-2">
                                    @if($a->jam_pulang)
                                        <span class="text-sm text-gray-900">{{ $a->jam_pulang }}</span>
                                    @else
                                        <span class="text-xs text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-3 py-2">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                        {{ $a->status === 'hadir' ? 'bg-green-100 text-green-800' : 
                                           ($a->status === 'terlambat' ? 'bg-yellow-100 text-yellow-800' : 
                                           ($a->status === 'izin' ? 'bg-blue-100 text-blue-800' : 
                                           ($a->status === 'sakit' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800')) }}">
                                        {{ ucfirst($a->status) }}
                                    </span>
                                </td>
                                <td class="px-3 py-2">
                                    <span class="text-xs text-gray-600">
                                        {{ $a->keterangan ?: '-' }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-3 py-8 text-center text-gray-500">
                                    <div class="flex flex-col items-center">
                                        <i class="fas fa-calendar-times text-3xl text-gray-300 mb-2"></i>
                                        <p class="text-sm">Tidak ada data absensi</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        @if($absensi->hasPages())
            <div class="mt-4">
                {{ $absensi->links() }}
            </div>
        @endif
    </div>
</body>
</html>
