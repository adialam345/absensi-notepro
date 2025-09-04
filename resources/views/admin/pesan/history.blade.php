<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>History Pesan - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <div class="bg-[#ff040c] p-3 text-white">
        <div class="flex justify-between items-center">
            <div class="flex items-center space-x-2">
                <a href="{{ route('admin.pesan.index') }}" class="text-white">
                    <i class="fas fa-arrow-left text-lg"></i>
                </a>
                <span class="font-semibold text-sm">History Pesan Terkirim</span>
            </div>
        </div>
    </div>

    <!-- Content -->
    <div class="p-4">
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="p-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-800">Pesan yang Sudah Dikirim</h2>
            </div>
            
            @if($pesanTerikirim->count() > 0)
                <div class="divide-y divide-gray-200">
                    @foreach($pesanTerikirim as $pesan)
                        <div class="p-4 hover:bg-gray-50">
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
                                    </div>
                                    
                                    <h3 class="font-semibold text-gray-800 text-sm mb-1">{{ $pesan->judul }}</h3>
                                    <p class="text-gray-600 text-sm mb-2">{{ Str::limit($pesan->pesan, 100) }}</p>
                                    
                                    <div class="flex items-center space-x-4 text-xs text-gray-500">
                                        <span>
                                            <i class="fas fa-user mr-1"></i>
                                            Kepada: {{ $pesan->penerima->name }}
                                        </span>
                                        <span>
                                            <i class="fas fa-clock mr-1"></i>
                                            {{ $pesan->created_at->format('d/m/Y H:i') }}
                                        </span>
                                        <span class="flex items-center">
                                            <i class="fas fa-eye mr-1"></i>
                                            {{ $pesan->dibaca ? 'Dibaca' : 'Belum dibaca' }}
                                            @if($pesan->dibaca_at)
                                                ({{ $pesan->dibaca_at->format('d/m/Y H:i') }})
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <!-- Pagination -->
                @if($pesanTerikirim->hasPages())
                    <div class="p-4 border-t border-gray-200">
                        {{ $pesanTerikirim->links() }}
                    </div>
                @endif
            @else
                <div class="p-8 text-center text-gray-500">
                    <i class="fas fa-inbox text-3xl text-gray-300 mb-2"></i>
                    <p class="text-sm">Belum ada pesan yang dikirim</p>
                </div>
            @endif
        </div>
    </div>
</body>
</html>
