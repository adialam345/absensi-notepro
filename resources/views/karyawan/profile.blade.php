<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil - Karyawan</title>
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
                <span class="font-semibold text-sm">Profil Saya</span>
            </div>
            <div class="flex items-center space-x-2">
                <div class="relative">
                    <i id="bellIcon" class="fas fa-bell text-lg"></i>
                    <span id="bellBadge" class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center hidden font-bold">0</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Content -->
    <div class="p-4">
        <div class="max-w-lg mx-auto">
            <!-- Profile Info -->
            <div class="bg-white rounded-xl shadow-sm p-4 mb-4">
                <div class="flex items-center space-x-3 mb-4">
                    <div class="w-16 h-16 bg-[#ff040c] rounded-full flex items-center justify-center">
                        <span class="text-white text-xl font-bold">{{ substr($user->name, 0, 1) }}</span>
                    </div>
                    <div>
                        <h2 class="text-sm font-semibold text-gray-800">{{ $user->name }}</h2>
                        <p class="text-xs text-gray-600">{{ $user->jabatan }}</p>
                        <p class="text-xs text-gray-500">{{ $user->email }}</p>
                    </div>
                </div>
                
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Username:</span>
                        <span class="text-gray-800">{{ $user->username }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Jam Kerja:</span>
                        <span class="text-gray-800">{{ $user->jam_kerja ?? '08:00 - 17:00' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Status:</span>
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                            {{ $user->status === 'aktif' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ ucfirst($user->status) }}
                        </span>
                    </div>
                    @if($lokasiKantor)
                    <div class="flex justify-between">
                        <span class="text-gray-600">Lokasi Kantor:</span>
                        <span class="text-gray-800">{{ $lokasiKantor->nama_lokasi }}</span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Edit Profile Form -->
            <div class="bg-white rounded-xl shadow-sm p-4">
                <h3 class="text-sm font-semibold text-gray-800 mb-4">Edit Profil</h3>

                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-3 py-2 rounded mb-4 text-xs">
                        {{ session('success') }}
                    </div>
                @endif

                <form action="{{ route('karyawan.profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-4">
                        <label class="block text-xs font-medium text-gray-700 mb-1">Nama Lengkap</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#ff040c] text-sm">
                    </div>

                    <div class="mb-4">
                        <label class="block text-xs font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#ff040c] text-sm">
                    </div>

                    <div class="mb-4">
                        <label class="block text-xs font-medium text-gray-700 mb-1">Password Baru (Kosongkan jika tidak ingin mengubah)</label>
                        <input type="password" name="password" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#ff040c] text-sm">
                    </div>

                    <div class="mb-4">
                        <label class="block text-xs font-medium text-gray-700 mb-1">Konfirmasi Password</label>
                        <input type="password" name="password_confirmation" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#ff040c] text-sm">
                    </div>

                    <div class="mb-6">
                        <label class="block text-xs font-medium text-gray-700 mb-1">Foto Profil (Opsional)</label>
                        <input type="file" name="foto" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#ff040c] text-sm"
                            accept="image/*">
                        <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG (Max: 2MB)</p>
                    </div>

                    <button type="submit" 
                        class="w-full bg-[#ff040c] text-white py-3 rounded-xl font-semibold hover:bg-[#fb0302] transition-colors text-sm">
                        <i class="fas fa-save mr-2"></i>Update Profil
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Load unread count for bell notification
        function loadUnreadCount() {
            fetch('{{ route("karyawan.pesan.unread-count") }}')
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Unread count data:', data); // Debug log
                    
                    const bellBadge = document.getElementById('bellBadge');
                    
                    if (data.unread_count > 0) {
                        // Update bell badge
                        if (bellBadge) {
                            bellBadge.textContent = data.unread_count;
                            bellBadge.classList.remove('hidden');
                        }
                        
                        // Add bounce animation to bell icon
                        const bellIcon = document.getElementById('bellIcon');
                        if (bellIcon) {
                            bellIcon.classList.add('animate-bounce');
                            setTimeout(() => {
                                bellIcon.classList.remove('animate-bounce');
                            }, 1000);
                        }
                    } else {
                        // Hide badge
                        if (bellBadge) bellBadge.classList.add('hidden');
                    }
                })
                .catch(error => {
                    console.error('Error loading unread count:', error);
                });
        }

        // Load unread count on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadUnreadCount();
        });
    </script>
</body>
</html>
