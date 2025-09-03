<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Lokasi Kantor - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <div class="bg-[#ff040c] p-3 text-white">
        <div class="flex justify-between items-center">
            <div class="flex items-center space-x-2">
                <a href="{{ route('admin.lokasi.index') }}" class="text-white">
                    <i class="fas fa-arrow-left text-lg"></i>
                </a>
                <span class="font-semibold text-sm">Tambah Lokasi Kantor</span>
            </div>
        </div>
    </div>

    <!-- Content -->
    <div class="p-4">
        <div class="max-w-2xl mx-auto">
            <!-- Form Card -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-6">Data Lokasi Kantor Baru</h2>

                @if($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        <ul class="list-disc list-inside">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.lokasi.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lokasi</label>
                        <input type="text" name="nama_lokasi" value="{{ old('nama_lokasi') }}" required 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#ff040c]"
                            placeholder="Contoh: Kantor Pusat Jakarta">
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Alamat Lengkap</label>
                        <textarea name="alamat" rows="3" required 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#ff040c]"
                            placeholder="Masukkan alamat lengkap lokasi kantor">{{ old('alamat') }}</textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Latitude</label>
                            <input type="number" name="latitude" value="{{ old('latitude') }}" step="any" required 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#ff040c]"
                                placeholder="Contoh: -6.2088">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Longitude</label>
                            <input type="number" name="longitude" value="{{ old('longitude') }}" step="any" required 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#ff040c]"
                                placeholder="Contoh: 106.8456">
                        </div>
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Radius (meter)</label>
                        <input type="number" name="radius" value="{{ old('radius', 100) }}" min="50" required 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#ff040c]"
                            placeholder="Minimal 50 meter">
                        <p class="text-xs text-gray-500 mt-1">Radius area absensi dalam meter (minimal 50m)</p>
                    </div>

                    <div class="flex justify-end space-x-3">
                        <a href="{{ route('admin.lokasi.index') }}" 
                            class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                            Batal
                        </a>
                        <button type="submit" 
                            class="px-4 py-2 bg-[#ff040c] text-white rounded-lg hover:bg-[#fb0302] transition-colors">
                            Simpan Lokasi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
