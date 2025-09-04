<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Detail Pesan - Karyawan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <div class="bg-[#ff040c] p-3 text-white">
        <div class="flex justify-between items-center">
            <div class="flex items-center space-x-2">
                <a href="{{ route('karyawan.pesan.index') }}" class="text-white">
                    <i class="fas fa-arrow-left text-lg"></i>
                </a>
                <span class="font-semibold text-sm">Detail Pesan</span>
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
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <!-- Header Pesan -->
            <div class="p-4 border-b border-gray-200">
                <div class="flex items-center space-x-2 mb-3">
                    <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full
                        {{ $pesan->tipe === 'peringatan' ? 'bg-red-100 text-red-800' : 
                           ($pesan->tipe === 'pemberitahuan' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800') }}">
                        {{ ucfirst($pesan->tipe) }}
                    </span>
                    @if($pesan->dokumen)
                        <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-gray-100 text-gray-800">
                            <i class="fas fa-paperclip mr-1"></i>Dokumen
                        </span>
                    @endif
                </div>
                
                <h1 class="text-lg font-semibold text-gray-800 mb-2">{{ $pesan->judul }}</h1>
                
                <div class="flex items-center space-x-4 text-sm text-gray-600">
                    <span>
                        <i class="fas fa-user mr-1"></i>
                        Dari: {{ $pesan->pengirim->name }}
                    </span>
                    <span>
                        <i class="fas fa-clock mr-1"></i>
                        {{ $pesan->created_at->format('d F Y, H:i') }}
                    </span>
                    @if($pesan->dibaca_at)
                        <span>
                            <i class="fas fa-eye mr-1"></i>
                            Dibaca: {{ $pesan->dibaca_at->format('d F Y, H:i') }}
                        </span>
                    @endif
                </div>
            </div>

            <!-- Isi Pesan -->
            <div class="p-4">
                <div class="prose prose-sm max-w-none">
                    <p class="text-gray-700 leading-relaxed whitespace-pre-line">{{ $pesan->pesan }}</p>
                </div>
            </div>

            <!-- Dokumen -->
            @if($pesan->dokumen)
                <div class="p-4 border-t border-gray-200">
                    <h3 class="text-sm font-semibold text-gray-800 mb-3">Dokumen Pendukung</h3>
                    <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
                        <div class="flex-shrink-0">
                            <i class="fas fa-file text-2xl text-gray-400"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-800">Dokumen Terlampir</p>
                            <p class="text-xs text-gray-500">Klik untuk mengunduh</p>
                        </div>
                        <div class="flex-shrink-0">
                            <a href="{{ Storage::url($pesan->dokumen) }}" target="_blank" class="inline-flex items-center px-3 py-1 bg-[#ff040c] text-white text-sm rounded-lg hover:bg-[#fb0302] transition-colors">
                                <i class="fas fa-download mr-1"></i>
                                Unduh
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Tombol Kembali -->
        <div class="mt-4">
            <a href="{{ route('karyawan.pesan.index') }}" class="w-full bg-[#ff040c] text-white py-3 rounded-xl font-semibold hover:bg-[#fb0302] transition-colors text-center block">
                <i class="fas fa-arrow-left mr-2"></i>Kembali ke Daftar Pesan
            </a>
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
                    const bellBadge = document.getElementById('bellBadge');
                    
                    if (data.unread_count > 0) {
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
                        // Hide badge
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
