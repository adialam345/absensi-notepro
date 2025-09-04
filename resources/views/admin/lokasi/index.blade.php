<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lokasi Kantor - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <x-admin-navbar title="Lokasi Kantor" />

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
            <a href="{{ route('admin.lokasi.create') }}" class="bg-[#ff040c] text-white px-4 py-2 rounded-lg hover:bg-[#fb0302] transition-colors">
                <i class="fas fa-plus mr-2"></i>Tambah Lokasi
            </a>
        </div>

        <!-- Location Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($lokasi as $l)
                <div class="bg-white rounded-lg shadow-sm p-4 hover:shadow-md transition-shadow">
                    <div class="flex justify-between items-start mb-3">
                        <div class="flex items-center space-x-2">
                            <div class="w-8 h-8 bg-[#ff040c] rounded-lg flex items-center justify-center">
                                <i class="fas fa-map-marker-alt text-white text-sm"></i>
                            </div>
                            <h3 class="font-semibold text-gray-800">{{ $l->nama_lokasi }}</h3>
                        </div>
                        <div class="flex items-center space-x-2">
                            <a href="{{ route('admin.lokasi.edit', $l->id) }}" class="text-[#ff040c] hover:text-[#fb0302]">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.lokasi.destroy', $l->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus lokasi ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                    
                    <div class="space-y-2 text-sm">
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-map text-gray-400 w-4"></i>
                            <span class="text-gray-600">{{ $l->alamat }}</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-location-arrow text-gray-400 w-4"></i>
                            <span class="text-gray-600">{{ $l->latitude }}, {{ $l->longitude }}</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-circle text-gray-400 w-4"></i>
                            <span class="text-gray-600">Radius: {{ $l->radius }}m</span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full">
                    <div class="bg-white rounded-lg shadow-sm p-8 text-center">
                        <i class="fas fa-map-marker-alt text-4xl text-gray-300 mb-4"></i>
                        <p class="text-gray-500">Belum ada lokasi kantor</p>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($lokasi->hasPages())
            <div class="mt-6">
                {{ $lokasi->links() }}
            </div>
        @endif
    </div>
</body>
</html>
