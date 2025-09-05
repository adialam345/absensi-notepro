<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Foto Absensi - {{ $absensi->user->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.3.2/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <x-admin-navbar title="Foto Absensi" subtitle="Dokumentasi Kehadiran" />

    <!-- Main Content -->
    <div class="max-w-6xl mx-auto mt-6 px-4">

        <!-- Absensi Info -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-xl font-bold mb-4" style="color: #ff040c;">Informasi Absensi</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nama Karyawan</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $absensi->user->name }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Tanggal</label>
                    <p class="mt-1 text-sm text-gray-900">{{ \Carbon\Carbon::parse($absensi->tanggal)->format('d/m/Y') }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Status</label>
                    @php
                        $statusColors = [
                            'hadir' => 'bg-green-100 text-green-800',
                            'terlambat' => 'bg-yellow-100 text-yellow-800',
                            'izin' => 'bg-blue-100 text-blue-800',
                            'sakit' => 'bg-red-100 text-red-800'
                        ];
                    @endphp
                    <span class="mt-1 inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $statusColors[$absensi->status] ?? 'bg-gray-100 text-gray-800' }}">
                        {{ ucfirst($absensi->status) }}
                    </span>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Jam Masuk</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $absensi->jam_masuk ?? '-' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Jam Pulang</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $absensi->jam_pulang ?? '-' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Keterangan</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $absensi->keterangan ?? '-' }}</p>
                </div>
            </div>
        </div>

        <!-- Photos Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Foto Masuk -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">
                        <i class="fas fa-sign-in-alt mr-2 text-green-600"></i>
                        Foto Masuk
                    </h3>
                </div>
                <div class="p-6">
                    @if($absensi->foto_masuk)
                        <div class="text-center">
                            <img src="{{ asset('storage/' . $absensi->foto_masuk) }}" 
                                 alt="Foto Masuk" 
                                 class="max-w-full h-auto rounded-lg shadow-md mx-auto cursor-pointer"
                                 onclick="openModal('{{ asset('storage/' . $absensi->foto_masuk) }}', 'Foto Masuk')">
                            <p class="mt-2 text-sm text-gray-500">Klik untuk memperbesar</p>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <i class="fas fa-camera text-gray-300 text-4xl mb-2"></i>
                            <p class="text-gray-500">Tidak ada foto masuk</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Foto Pulang -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">
                        <i class="fas fa-sign-out-alt mr-2 text-red-600"></i>
                        Foto Pulang
                    </h3>
                </div>
                <div class="p-6">
                    @if($absensi->foto_pulang)
                        <div class="text-center">
                            <img src="{{ asset('storage/' . $absensi->foto_pulang) }}" 
                                 alt="Foto Pulang" 
                                 class="max-w-full h-auto rounded-lg shadow-md mx-auto cursor-pointer"
                                 onclick="openModal('{{ asset('storage/' . $absensi->foto_pulang) }}', 'Foto Pulang')">
                            <p class="mt-2 text-sm text-gray-500">Klik untuk memperbesar</p>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <i class="fas fa-camera text-gray-300 text-4xl mb-2"></i>
                            <p class="text-gray-500">Tidak ada foto pulang</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Location Info (if available) -->
        @if($absensi->lokasi_masuk || $absensi->lokasi_pulang)
        <div class="bg-white rounded-lg shadow p-6 mt-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">
                <i class="fas fa-map-marker-alt mr-2 text-blue-600"></i>
                Informasi Lokasi
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @if($absensi->lokasi_masuk)
                <div>
                    <label class="block text-sm font-medium text-gray-700">Lokasi Masuk</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $absensi->lokasi_masuk }}</p>
                </div>
                @endif
                @if($absensi->lokasi_pulang)
                <div>
                    <label class="block text-sm font-medium text-gray-700">Lokasi Pulang</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $absensi->lokasi_pulang }}</p>
                </div>
                @endif
            </div>
        </div>
        @endif
    </div>

    <!-- Modal for enlarged image -->
    <div id="imageModal" class="fixed inset-0 bg-black bg-opacity-75 hidden z-50 flex items-center justify-center p-4">
        <div class="relative max-w-4xl max-h-full">
            <button onclick="closeModal()" 
                    class="absolute top-4 right-4 text-white hover:text-gray-300 text-2xl z-10">
                <i class="fas fa-times"></i>
            </button>
            <img id="modalImage" src="" alt="" class="max-w-full max-h-full rounded-lg">
            <p id="modalTitle" class="text-white text-center mt-4 text-lg"></p>
        </div>
    </div>

    <script>
        function openModal(imageSrc, title) {
            document.getElementById('modalImage').src = imageSrc;
            document.getElementById('modalTitle').textContent = title;
            document.getElementById('imageModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeModal() {
            document.getElementById('imageModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        // Close modal when clicking outside the image
        document.getElementById('imageModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });

        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModal();
            }
        });
    </script>
</body>
</html>
