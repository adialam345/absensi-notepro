<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Izin & Cuti - Karyawan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.3.2/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <nav class="bg-[#ff040c] p-4 text-white font-bold flex justify-between items-center">
        <div class="flex items-center space-x-4">
            <a href="{{ route('karyawan.dashboard') }}" class="hover:text-gray-200">
                <i class="fas fa-arrow-left mr-2"></i>Kembali ke Dashboard
            </a>
            <span>Izin & Cuti</span>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="bg-[#fb0302] px-4 py-1 rounded">Logout</button>
        </form>
    </nav>

    <!-- Main Content -->
    <div class="max-w-4xl mx-auto mt-6 px-4">
        <!-- Form Pengajuan Izin/Cuti -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-xl font-bold mb-4" style="color: #ff040c;">Ajukan Izin/Cuti</h2>
            <form id="izinCutiForm">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Jenis</label>
                        <select id="jenis" name="jenis" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#ff040c]">
                            <option value="">Pilih Jenis</option>
                            <option value="izin">Izin</option>
                            <option value="cuti">Cuti</option>
                            <option value="sakit">Sakit</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai</label>
                        <input type="date" id="tanggal_mulai" name="tanggal_mulai" required 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#ff040c]">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Selesai</label>
                        <input type="date" id="tanggal_selesai" name="tanggal_selesai" required 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#ff040c]">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Dokumen Pendukung</label>
                        <input type="file" id="dokumen" name="dokumen" accept=".pdf,.jpg,.jpeg,.png" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#ff040c]">
                        <p class="text-xs text-gray-500 mt-1">Format: PDF, JPG, PNG (Max: 2MB)</p>
                    </div>
                </div>
                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Alasan</label>
                    <textarea id="alasan" name="alasan" rows="4" required 
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#ff040c]"
                              placeholder="Jelaskan alasan izin/cuti..."></textarea>
                </div>
                <div class="mt-6">
                    <button type="submit" class="w-full bg-[#ff040c] text-white py-3 rounded-md font-bold hover:bg-[#fb0302] transition-colors">
                        <i class="fas fa-paper-plane mr-2"></i>Ajukan Izin/Cuti
                    </button>
                </div>
            </form>
        </div>

        <!-- History Pengajuan -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold mb-4" style="color: #ff040c;">History Pengajuan</h2>
            <div id="historyContainer">
                <div class="text-center text-gray-500 py-8">
                    <i class="fas fa-inbox text-2xl mb-2"></i>
                    <p>Belum ada pengajuan izin/cuti</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Set minimum date to today
        document.addEventListener('DOMContentLoaded', function() {
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('tanggal_mulai').min = today;
            document.getElementById('tanggal_selesai').min = today;
            
            // Load history
            loadHistory();
        });

        // Update end date minimum when start date changes
        document.getElementById('tanggal_mulai').addEventListener('change', function() {
            const startDate = this.value;
            document.getElementById('tanggal_selesai').min = startDate;
        });

        // Handle form submission
        document.getElementById('izinCutiForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            // Show loading
            Swal.fire({
                title: 'Mengirim Pengajuan...',
                html: 'Mohon tunggu sebentar',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            try {
                const response = await fetch('{{ route("karyawan.izin.cuti.post") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                                       document.querySelector('input[name="_token"]').value
                    }
                });
                
                const result = await response.json();
                
                Swal.close();
                
                if (result.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Pengajuan Berhasil!',
                        text: result.message,
                        confirmButtonText: 'Baik',
                        confirmButtonColor: '#ff040c'
                    }).then(() => {
                        // Reset form
                        document.getElementById('izinCutiForm').reset();
                        // Reload history
                        loadHistory();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Pengajuan Gagal!',
                        text: result.message,
                        confirmButtonText: 'Coba Lagi',
                        confirmButtonColor: '#ff040c'
                    });
                }
            } catch (error) {
                console.error('Error:', error);
                Swal.close();
                Swal.fire({
                    icon: 'error',
                    title: 'Terjadi Kesalahan!',
                    text: 'Silakan coba lagi atau hubungi administrator.',
                    confirmButtonText: 'Coba Lagi',
                    confirmButtonColor: '#ff040c'
                });
            }
        });

        // Load history
        async function loadHistory() {
            try {
                const response = await fetch('{{ route("karyawan.izin.cuti.history") }}');
                const result = await response.json();
                
                if (result.success) {
                    displayHistory(result.data);
                } else {
                    // Keep default message if no data
                    console.log('No history data available');
                }
            } catch (error) {
                console.error('Error loading history:', error);
                // Keep default message on error
            }
        }

        // Display history
        function displayHistory(data) {
            if (data.length === 0) {
                document.getElementById('historyContainer').innerHTML = `
                    <div class="text-center text-gray-500 py-8">
                        <i class="fas fa-inbox text-2xl mb-2"></i>
                        <p>Belum ada pengajuan izin/cuti</p>
                    </div>
                `;
                return;
            }

            const historyHTML = data.map(item => {
                const statusColors = {
                    'pending': 'bg-yellow-100 text-yellow-800',
                    'disetujui': 'bg-green-100 text-green-800',
                    'ditolak': 'bg-red-100 text-red-800'
                };
                
                const statusIcons = {
                    'pending': 'fas fa-clock',
                    'disetujui': 'fas fa-check',
                    'ditolak': 'fas fa-times'
                };

                return `
                    <div class="border border-gray-200 rounded-lg p-4 mb-4">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3 mb-2">
                                    <h3 class="font-semibold text-gray-900">${item.jenis.charAt(0).toUpperCase() + item.jenis.slice(1)}</h3>
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full ${statusColors[item.status]}">
                                        <i class="${statusIcons[item.status]} mr-1"></i>
                                        ${item.status.charAt(0).toUpperCase() + item.status.slice(1)}
                                    </span>
                                </div>
                                <p class="text-sm text-gray-600 mb-1">
                                    <i class="fas fa-calendar mr-2"></i>
                                    ${item.tanggal_mulai} - ${item.tanggal_selesai}
                                </p>
                                <p class="text-sm text-gray-600">
                                    <i class="fas fa-comment mr-2"></i>
                                    ${item.alasan}
                                </p>
                            </div>
                            <div class="text-right text-sm text-gray-500">
                                <p>${new Date(item.created_at).toLocaleDateString('id-ID')}</p>
                            </div>
                        </div>
                    </div>
                `;
            }).join('');

            document.getElementById('historyContainer').innerHTML = historyHTML;
        }
    </script>
</body>
</html>