<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Izin/Cuti - Karyawan</title>
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
                <span class="font-semibold text-sm">Pengajuan Izin/Cuti</span>
            </div>
        </div>
    </div>

    <!-- Content -->
    <div class="p-4">
        <div class="max-w-lg mx-auto">
            <!-- Form Card -->
            <div class="bg-white rounded-xl shadow-sm p-4">
                <h2 class="text-sm font-semibold text-gray-800 mb-4">Form Pengajuan</h2>

                @if($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 px-3 py-2 rounded mb-4 text-xs">
                        <ul class="list-disc list-inside">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form id="izinForm" action="{{ route('karyawan.izin.cuti') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Pengajuan</label>
                        <select name="jenis" required 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#ff040c] text-sm">
                            <option value="">Pilih Jenis</option>
                            <option value="izin">Izin</option>
                            <option value="cuti">Cuti</option>
                            <option value="sakit">Sakit</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai</label>
                            <input type="date" name="tanggal_mulai" required 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#ff040c] text-sm">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Selesai</label>
                            <input type="date" name="tanggal_selesai" required 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#ff040c] text-sm">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Alasan</label>
                        <textarea name="alasan" rows="4" required 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#ff040c] text-sm"
                            placeholder="Jelaskan alasan pengajuan..."></textarea>
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Dokumen Pendukung (Opsional)</label>
                        <input type="file" name="dokumen" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#ff040c] text-sm"
                            accept=".pdf,.jpg,.jpeg,.png">
                        <p class="text-xs text-gray-500 mt-1">Format: PDF, JPG, PNG (Max: 2MB)</p>
                    </div>

                    <div class="flex justify-end space-x-3">
                        <a href="{{ route('karyawan.dashboard') }}" 
                            class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors text-sm">
                            Batal
                        </a>
                        <button type="submit" id="submitBtn"
                            class="px-4 py-2 bg-[#ff040c] text-white rounded-lg hover:bg-[#fb0302] transition-colors text-sm">
                            <i class="fas fa-paper-plane mr-1"></i>Kirim Pengajuan
                        </button>
                    </div>
                </form>
            </div>

            <!-- Status Messages -->
            <div id="statusMessage" class="hidden mt-4 p-3 rounded-lg text-sm"></div>
        </div>
    </div>

    <script>
        // Set minimum date to today
        const today = new Date().toISOString().split('T')[0];
        document.querySelector('input[name="tanggal_mulai"]').min = today;
        document.querySelector('input[name="tanggal_selesai"]').min = today;

        // Update end date minimum when start date changes
        document.querySelector('input[name="tanggal_mulai"]').addEventListener('change', function() {
            document.querySelector('input[name="tanggal_selesai"]').min = this.value;
        });

        // Handle form submission
        document.getElementById('izinForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const submitBtn = document.getElementById('submitBtn');
            const originalText = submitBtn.innerHTML;
            
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Mengirim...';
            
            try {
                const formData = new FormData(this);
                
                const response = await fetch('{{ route("karyawan.izin.cuti") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                    }
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showStatus(result.message, 'success');
                    setTimeout(() => {
                        window.location.href = '{{ route("karyawan.dashboard") }}';
                    }, 2000);
                } else {
                    showStatus(result.message, 'error');
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }
            } catch (error) {
                console.error('Error:', error);
                showStatus('Terjadi kesalahan. Silakan coba lagi.', 'error');
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
        });

        // Show status message
        function showStatus(message, type) {
            const statusDiv = document.getElementById('statusMessage');
            statusDiv.className = `mt-4 p-3 rounded-lg text-sm ${
                type === 'error' ? 'bg-red-100 text-red-700 border border-red-400' : 
                type === 'success' ? 'bg-green-100 text-green-700 border border-green-400' : 
                'bg-blue-100 text-blue-700 border border-blue-400'
            }`;
            statusDiv.textContent = message;
            statusDiv.classList.remove('hidden');
        }
    </script>
</body>
</html>
