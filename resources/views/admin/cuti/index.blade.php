<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Cuti & Izin - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.3.2/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @php
        use Illuminate\Support\Facades\Storage;
    @endphp
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <x-admin-navbar title="Kelola Cuti & Izin" subtitle="Persetujuan Pengajuan Cuti" />

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto mt-6 px-4">
        <!-- Filter Section -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-xl font-bold mb-4" style="color: #ff040c;">Filter Pengajuan</h2>
            <form method="GET" action="{{ route('admin.cuti.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#ff040c]">
                        <option value="">Semua Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="disetujui" {{ request('status') == 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                        <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jenis</label>
                    <select name="tipe" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#ff040c]">
                        <option value="">Semua Jenis</option>
                        <option value="izin" {{ request('tipe') == 'izin' ? 'selected' : '' }}>Izin</option>
                        <option value="cuti" {{ request('tipe') == 'cuti' ? 'selected' : '' }}>Cuti</option>
                        <option value="sakit" {{ request('tipe') == 'sakit' ? 'selected' : '' }}>Sakit</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Karyawan</label>
                    <select name="karyawan" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#ff040c]">
                        <option value="">Semua Karyawan</option>
                        @foreach($karyawan as $k)
                            <option value="{{ $k->id }}" {{ request('karyawan') == $k->id ? 'selected' : '' }}>
                                {{ $k->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full bg-[#ff040c] text-white px-4 py-2 rounded-md hover:bg-[#fb0302] transition-colors">
                        <i class="fas fa-search mr-2"></i>Filter
                    </button>
                </div>
            </form>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-100">
                        <i class="fas fa-clock text-yellow-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Pending</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $summary['pending'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100">
                        <i class="fas fa-check text-green-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Disetujui</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $summary['disetujui'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-red-100">
                        <i class="fas fa-times text-red-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Ditolak</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $summary['ditolak'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Data Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Data Pengajuan Cuti & Izin</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Karyawan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Alasan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($cuti as $c)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $c->user->name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                        {{ $c->tipe == 'cuti' ? 'bg-blue-100 text-blue-800' : 
                                           ($c->tipe == 'izin' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                        {{ ucfirst($c->tipe) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ \Carbon\Carbon::parse($c->tanggal_mulai)->format('d/m/Y') }} - 
                                    {{ \Carbon\Carbon::parse($c->tanggal_selesai)->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 max-w-xs">
                                    <div class="truncate">{{ $c->keterangan }}</div>
                                    @if($c->dokumen && $c->dokumen !== '')
                                        <a href="/storage/{{ $c->dokumen }}" target="_blank" 
                                           class="text-[#ff040c] hover:text-[#fb0302] text-xs mt-1 inline-flex items-center">
                                            <i class="fas fa-file-pdf mr-1"></i>Lihat Dokumen
                                        </a>
                                    @else
                                        <span class="text-gray-400 text-xs">Tidak ada dokumen</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $statusColors = [
                                            'pending' => 'bg-yellow-100 text-yellow-800',
                                            'disetujui' => 'bg-green-100 text-green-800',
                                            'ditolak' => 'bg-red-100 text-red-800'
                                        ];
                                    @endphp
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColors[$c->status] ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ ucfirst($c->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <button onclick="viewDetail({{ $c->id }}, '{{ $c->user->name }}', '{{ $c->tipe }}', '{{ $c->tanggal_mulai }}', '{{ $c->tanggal_selesai }}', '{{ $c->keterangan }}', '{{ $c->dokumen ?? '' }}', '{{ $c->status }}')" 
                                                class="text-blue-600 hover:text-blue-900 bg-blue-100 hover:bg-blue-200 px-3 py-1 rounded text-xs">
                                            <i class="fas fa-eye mr-1"></i>Detail
                                        </button>
                                        @if($c->status == 'pending')
                                            <button onclick="approveCuti({{ $c->id }})" 
                                                    class="text-green-600 hover:text-green-900 bg-green-100 hover:bg-green-200 px-3 py-1 rounded text-xs">
                                                <i class="fas fa-check mr-1"></i>Setujui
                                            </button>
                                            <button onclick="rejectCuti({{ $c->id }})" 
                                                    class="text-red-600 hover:text-red-900 bg-red-100 hover:bg-red-200 px-3 py-1 rounded text-xs">
                                                <i class="fas fa-times mr-1"></i>Tolak
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                                    Tidak ada pengajuan cuti/izin.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            @if($cuti->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $cuti->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Modal Detail -->
    <div id="detailModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-2/3 lg:w-1/2 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Detail Pengajuan Cuti/Izin</h3>
                    <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nama Karyawan</label>
                            <p id="detailNama" class="mt-1 text-sm text-gray-900"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Jenis</label>
                            <p id="detailJenis" class="mt-1 text-sm text-gray-900"></p>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tanggal Mulai</label>
                            <p id="detailTanggalMulai" class="mt-1 text-sm text-gray-900"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tanggal Selesai</label>
                            <p id="detailTanggalSelesai" class="mt-1 text-sm text-gray-900"></p>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Alasan</label>
                        <p id="detailAlasan" class="mt-1 text-sm text-gray-900"></p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Dokumen Pendukung</label>
                        <div id="detailDokumen" class="mt-1">
                            <!-- Dokumen akan dimuat di sini -->
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Status</label>
                        <p id="detailStatus" class="mt-1 text-sm"></p>
                    </div>
                </div>
                
                <div class="mt-6 flex justify-end space-x-3">
                    <button onclick="closeModal()" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400 transition-colors">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function viewDetail(id, nama, jenis, tanggalMulai, tanggalSelesai, alasan, dokumen, status) {
            // Set data ke modal
            document.getElementById('detailNama').textContent = nama;
            document.getElementById('detailJenis').textContent = jenis.charAt(0).toUpperCase() + jenis.slice(1);
            document.getElementById('detailTanggalMulai').textContent = new Date(tanggalMulai).toLocaleDateString('id-ID');
            document.getElementById('detailTanggalSelesai').textContent = new Date(tanggalSelesai).toLocaleDateString('id-ID');
            document.getElementById('detailAlasan').textContent = alasan;
            
            // Set status dengan warna
            const statusElement = document.getElementById('detailStatus');
            const statusColors = {
                'pending': 'text-yellow-600 bg-yellow-100 px-2 py-1 rounded-full text-xs font-semibold',
                'disetujui': 'text-green-600 bg-green-100 px-2 py-1 rounded-full text-xs font-semibold',
                'ditolak': 'text-red-600 bg-red-100 px-2 py-1 rounded-full text-xs font-semibold'
            };
            statusElement.innerHTML = `<span class="${statusColors[status]}">${status.charAt(0).toUpperCase() + status.slice(1)}</span>`;
            
            // Set dokumen
            const dokumenElement = document.getElementById('detailDokumen');
            if (dokumen && dokumen !== '' && dokumen !== 'null' && dokumen !== 'undefined') {
                const fileExtension = dokumen.split('.').pop().toLowerCase();
                const iconClass = fileExtension === 'pdf' ? 'fas fa-file-pdf text-red-500' : 'fas fa-file-image text-blue-500';
                
                dokumenElement.innerHTML = `
                    <div class="flex items-center space-x-2">
                        <i class="${iconClass}"></i>
                        <a href="/storage/${dokumen}" target="_blank" class="text-[#ff040c] hover:text-[#fb0302] underline">
                            Lihat Dokumen
                        </a>
                    </div>
                `;
            } else {
                dokumenElement.innerHTML = '<span class="text-gray-400 text-sm">Tidak ada dokumen</span>';
            }
            
            // Tampilkan modal
            document.getElementById('detailModal').classList.remove('hidden');
        }
        
        function closeModal() {
            document.getElementById('detailModal').classList.add('hidden');
        }
        
        function approveCuti(id) {
            Swal.fire({
                title: 'Setujui Pengajuan?',
                text: 'Apakah Anda yakin ingin menyetujui pengajuan ini?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Setujui',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#6b7280'
            }).then((result) => {
                if (result.isConfirmed) {
                    submitAction(id, 'approve');
                }
            });
        }

        function rejectCuti(id) {
            Swal.fire({
                title: 'Tolak Pengajuan?',
                text: 'Apakah Anda yakin ingin menolak pengajuan ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Tolak',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280'
            }).then((result) => {
                if (result.isConfirmed) {
                    submitAction(id, 'reject');
                }
            });
        }

        function submitAction(id, action) {
            const formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('_method', 'PATCH');

            fetch(`/admin/cuti/${id}/${action}`, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: data.message,
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#ff040c'
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: data.message,
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#ff040c'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Terjadi Kesalahan!',
                    text: 'Silakan coba lagi.',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#ff040c'
                });
            });
        }
    </script>
</body>
</html>
