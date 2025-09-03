<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Karyawan - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <div class="bg-[#ff040c] p-3 text-white">
        <div class="flex justify-between items-center">
            <div class="flex items-center space-x-2">
                <a href="{{ route('admin.karyawan.index') }}" class="text-white">
                    <i class="fas fa-arrow-left text-lg"></i>
                </a>
                <span class="font-semibold text-sm">Tambah Karyawan</span>
            </div>
        </div>
    </div>

    <!-- Content -->
    <div class="p-4">
        <div class="max-w-2xl mx-auto">
            <!-- Form Card -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-6">Data Karyawan Baru</h2>

                @if($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        <ul class="list-disc list-inside">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.karyawan.store') }}" method="POST">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap</label>
                            <input type="text" name="name" value="{{ old('name') }}" required 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#ff040c]">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Username</label>
                            <input type="text" name="username" value="{{ old('username') }}" required 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#ff040c]">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                            <input type="email" name="email" value="{{ old('email') }}" required 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#ff040c]">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                            <input type="password" name="password" required 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#ff040c]">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Jabatan</label>
                            <input type="text" name="jabatan" value="{{ old('jabatan') }}" required 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#ff040c]">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Lokasi Kantor</label>
                            <select name="lokasi_kantor_id" required 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#ff040c]">
                                <option value="">Pilih Lokasi</option>
                                @foreach($lokasiKantor as $lokasi)
                                    <option value="{{ $lokasi->id }}" {{ old('lokasi_kantor_id') == $lokasi->id ? 'selected' : '' }}>
                                        {{ $lokasi->nama_lokasi }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Jam Kerja Masuk</label>
                            <input type="time" name="jam_kerja_masuk" value="{{ old('jam_kerja_masuk') }}" required 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#ff040c]">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Jam Kerja Pulang</label>
                            <input type="time" name="jam_kerja_pulang" value="{{ old('jam_kerja_pulang') }}" required 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#ff040c]">
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3">
                        <a href="{{ route('admin.karyawan.index') }}" 
                            class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                            Batal
                        </a>
                        <button type="submit" 
                            class="px-4 py-2 bg-[#ff040c] text-white rounded-lg hover:bg-[#fb0302] transition-colors">
                            Simpan Karyawan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
