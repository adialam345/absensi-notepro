<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pesan Masuk - Karyawan</title>
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
                <span class="font-semibold text-sm">Pesan Masuk</span>
            </div>
            <div class="flex items-center space-x-2">
                <div class="relative">
                    <i id="bellIcon" class="fas fa-bell text-lg"></i>
                    <span id="bellBadge" class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center hidden font-bold">0</span>
                </div>
                <span id="unreadCount" class="bg-yellow-500 text-white text-xs px-2 py-1 rounded-full"></span>
            </div>
        </div>
    </div>

    <!-- Content -->
    <div class="p-4">
        @if($pesanMasuk->count() > 0)
            <div class="space-y-3">
                @foreach($pesanMasuk as $pesan)
                    <div class="bg-white rounded-xl shadow-sm overflow-hidden {{ !$pesan->dibaca ? 'border-l-4 border-l-[#ff040c]' : '' }}">
                        <a href="{{ route('karyawan.pesan.show', $pesan->id) }}" class="block p-4 hover:bg-gray-50">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-2 mb-2">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                            {{ $pesan->tipe === 'peringatan' ? 'bg-red-100 text-red-800' : 
                                               ($pesan->tipe === 'pemberitahuan' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800') }}">
                                            {{ ucfirst($pesan->tipe) }}
                                        </span>
                                        @if($pesan->dokumen)
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                                <i class="fas fa-paperclip mr-1"></i>Dokumen
                                            </span>
                                        @endif
                                        @if(!$pesan->dibaca)
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-[#ff040c] text-white">
                                                Baru
                                            </span>
                                        @endif
                                    </div>
                                    
                                    <h3 class="font-semibold text-gray-800 text-sm mb-1 {{ !$pesan->dibaca ? 'font-bold' : '' }}">{{ $pesan->judul }}</h3>
                                    <p class="text-gray-600 text-sm mb-2">{{ Str::limit($pesan->pesan, 80) }}</p>
                                    
                                    <div class="flex items-center justify-between text-xs text-gray-500">
                                        <span>
                                            <i class="fas fa-user mr-1"></i>
                                            Dari: {{ $pesan->pengirim->name }}
                                        </span>
                                        <span>
                                            <i class="fas fa-clock mr-1"></i>
                                            {{ $pesan->created_at->format('d/m/Y H:i') }}
                                        </span>
                                    </div>
                                </div>
                                <div class="ml-2">
                                    <i class="fas fa-chevron-right text-gray-400"></i>
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
            
            <!-- Pagination -->
            @if($pesanMasuk->hasPages())
                <div class="mt-4">
                    {{ $pesanMasuk->links() }}
                </div>
            @endif
        @else
            <div class="bg-white rounded-xl shadow-sm p-8 text-center text-gray-500">
                <i class="fas fa-inbox text-3xl text-gray-300 mb-2"></i>
                <p class="text-sm">Belum ada pesan masuk</p>
            </div>
        @endif
    </div>

    <script>
        // Load unread count
        function loadUnreadCount() {
            fetch('{{ route("karyawan.pesan.unread-count") }}')
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    const unreadCount = document.getElementById('unreadCount');
                    const bellBadge = document.getElementById('bellBadge');
                    
                    if (data.unread_count > 0) {
                        // Update unread count badge
                        if (unreadCount) {
                            unreadCount.textContent = data.unread_count;
                            unreadCount.style.display = 'flex';
                        }
                        
                        // Update bell badge
                        if (bellBadge) {
                            bellBadge.textContent = data.unread_count;
                            bellBadge.style.display = 'flex';
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
                        // Hide both badges
                        if (unreadCount) unreadCount.style.display = 'none';
                        if (bellBadge) bellBadge.style.display = 'none';
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
