<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Karyawan - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <x-admin-navbar 
        title="Kelola Karyawan" 
        :actions="[
            ['url' => route('admin.export.karyawan'), 'icon' => 'fas fa-download', 'text' => 'Export']
        ]" 
    />

    <!-- Content -->
    <div class="p-4">
        <!-- Success Message -->
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <!-- Add Button -->
        <div class="mb-4">
            <a href="{{ route('admin.karyawan.create') }}" class="bg-[#ff040c] text-white px-4 py-2 rounded-lg hover:bg-[#fb0302] transition-colors">
                <i class="fas fa-plus mr-2"></i>Tambah Karyawan
            </a>
        </div>

        <!-- Search & Filter -->
        <div class="bg-white rounded-lg p-4 mb-4 shadow-sm">
            <div class="flex flex-wrap gap-4">
                <div class="flex-1 min-w-64">
                    <input type="text" placeholder="Cari nama atau username..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#ff040c]">
                </div>
                <select class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#ff040c]">
                    <option value="">Semua Status</option>
                    <option value="aktif">Aktif</option>
                    <option value="nonaktif">Non Aktif</option>
                </select>
            </div>
        </div>

        <!-- Employee Table -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-[#ff040c] text-white">
                        <tr>
                            <th class="px-4 py-3 text-left text-sm font-medium">Nama</th>
                            <th class="px-4 py-3 text-left text-sm font-medium">Username</th>
                            <th class="px-4 py-3 text-left text-sm font-medium">Email</th>
                            <th class="px-4 py-3 text-left text-sm font-medium">Jabatan</th>
                            <th class="px-4 py-3 text-left text-sm font-medium">Jam Kerja</th>
                            <th class="px-4 py-3 text-left text-sm font-medium">Status</th>
                            <th class="px-4 py-3 text-left text-sm font-medium">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($karyawan as $k)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-8 h-8 bg-[#ff040c] rounded-full flex items-center justify-center">
                                            <span class="text-white text-sm font-medium">{{ substr($k->name, 0, 1) }}</span>
                                        </div>
                                        <div>
                                            <div class="font-medium text-gray-900">{{ $k->name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $k->username }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $k->email }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $k->jabatan }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $k->jam_kerja }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $k->status === 'aktif' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ ucfirst($k->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center space-x-2">
                                        <a href="{{ route('admin.karyawan.edit', $k->id) }}" class="text-[#ff040c] hover:text-[#fb0302]">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.karyawan.destroy', $k->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus karyawan ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                    <div class="flex flex-col items-center">
                                        <i class="fas fa-users text-4xl text-gray-300 mb-2"></i>
                                        <p>Belum ada data karyawan</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        @if($karyawan->hasPages())
            <div class="mt-4">
                {{ $karyawan->links() }}
            </div>
        @endif
    </div>
</body>
</html>
