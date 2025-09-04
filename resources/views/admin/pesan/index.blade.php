<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Kirim Pesan - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <x-admin-navbar 
        title="Kirim Pesan" 
        :actions="[
            ['url' => route('admin.pesan.history'), 'icon' => 'fas fa-history', 'text' => 'History']
        ]" 
    />

    <!-- Content -->
    <div class="p-4">
        <div class="bg-white rounded-xl shadow-sm p-4">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Kirim Pesan ke Karyawan</h2>
            
            <form id="pesanForm" enctype="multipart/form-data">
                @csrf
                
                <!-- Pilih Karyawan -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Karyawan</label>
                    <select name="penerima_id" id="penerima_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#ff040c] text-sm" required>
                        <option value="">-- Pilih Karyawan --</option>
                        @foreach($karyawan as $k)
                            <option value="{{ $k->id }}">{{ $k->name }} ({{ $k->email }})</option>
                        @endforeach
                    </select>
                </div>

                <!-- Tipe Pesan -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tipe Pesan</label>
                    <select name="tipe" id="tipe" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#ff040c] text-sm" required>
                        <option value="info">Informasi</option>
                        <option value="peringatan">Peringatan</option>
                        <option value="pemberitahuan">Pemberitahuan</option>
                    </select>
                </div>

                <!-- Judul -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Judul Pesan</label>
                    <input type="text" name="judul" id="judul" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#ff040c] text-sm" placeholder="Masukkan judul pesan" required>
                </div>

                <!-- Isi Pesan -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Isi Pesan</label>
                    <textarea name="pesan" id="pesan" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#ff040c] text-sm" placeholder="Masukkan isi pesan" required></textarea>
                </div>

                <!-- Upload Dokumen -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Dokumen Pendukung (Opsional)</label>
                    <div class="flex items-center justify-center w-full">
                        <label for="dokumen" class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition-colors">
                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                <i class="fas fa-cloud-upload-alt text-2xl text-gray-400 mb-2"></i>
                                <p class="mb-2 text-sm text-gray-500">
                                    <span class="font-semibold">Klik untuk upload</span> atau drag and drop
                                </p>
                                <p class="text-xs text-gray-500">PDF, DOC, DOCX, JPG, PNG (MAX. 1MB)</p>
                            </div>
                            <input id="dokumen" name="dokumen" type="file" class="hidden" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                        </label>
                    </div>
                    <div id="fileInfo" class="mt-2 text-sm text-gray-600 hidden"></div>
                    <div id="uploadProgress" class="mt-2 hidden">
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-[#ff040c] h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Mengupload dokumen...</p>
                    </div>
                </div>

                <!-- Tombol Kirim -->
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="resetForm()" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors text-sm">
                        Reset
                    </button>
                    <button type="submit" id="submitBtn" class="px-6 py-2 bg-[#ff040c] text-white rounded-lg hover:bg-[#fb0302] transition-colors text-sm">
                        <i class="fas fa-paper-plane mr-1"></i>Kirim Pesan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // File upload handler (optimized)
        document.getElementById('dokumen').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const fileInfo = document.getElementById('fileInfo');
            const uploadProgress = document.getElementById('uploadProgress');
            
            if (file) {
                // Check file size (1MB limit)
                if (file.size > 1024 * 1024) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'File Terlalu Besar!',
                        text: 'Ukuran file maksimal 1MB. Silakan pilih file yang lebih kecil.'
                    });
                    this.value = ''; // Clear the input
                    return;
                }
                
                fileInfo.classList.remove('hidden');
                fileInfo.innerHTML = `
                    <i class="fas fa-file mr-1"></i>
                    <span class="font-medium">${file.name}</span>
                    <span class="text-gray-500">(${(file.size / 1024 / 1024).toFixed(2)} MB)</span>
                `;
            } else {
                fileInfo.classList.add('hidden');
                uploadProgress.classList.add('hidden');
            }
        });

        // Form submission (optimized)
        document.getElementById('pesanForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const submitBtn = document.getElementById('submitBtn');
            const originalText = submitBtn.innerHTML;
            
            // Quick validation before sending
            const penerimaId = document.getElementById('penerima_id').value;
            const judul = document.getElementById('judul').value.trim();
            const pesan = document.getElementById('pesan').value.trim();
            
            if (!penerimaId || !judul || !pesan) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Perhatian!',
                    text: 'Mohon lengkapi semua field yang wajib diisi'
                });
                return;
            }
            
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Mengirim...';
            submitBtn.disabled = true;
            
            const formData = new FormData(this);
            
            // Show upload progress if file is selected
            const fileInput = document.getElementById('dokumen');
            const uploadProgress = document.getElementById('uploadProgress');
            const progressBar = uploadProgress.querySelector('.bg-\\[\\#ff040c\\]');
            let progressInterval;
            
            if (fileInput.files.length > 0) {
                uploadProgress.classList.remove('hidden');
                // Animate progress bar
                let progress = 0;
                progressInterval = setInterval(() => {
                    progress += Math.random() * 15;
                    if (progress > 90) progress = 90;
                    progressBar.style.width = progress + '%';
                }, 200);
                
                // Clear interval when request completes
                setTimeout(() => clearInterval(progressInterval), 25000);
            }
            
            // Add timeout for better UX
            const controller = new AbortController();
            const timeoutId = setTimeout(() => controller.abort(), 30000); // 30 second timeout
            
            fetch('{{ route("admin.pesan.store") }}', {
                method: 'POST',
                body: formData,
                signal: controller.signal,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => {
                clearTimeout(timeoutId);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: data.message,
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        resetForm();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: data.message || 'Terjadi kesalahan saat mengirim pesan'
                    });
                }
            })
            .catch(error => {
                clearTimeout(timeoutId);
                console.error('Error:', error);
                
                if (error.name === 'AbortError') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Timeout!',
                        text: 'Pengiriman pesan terlalu lama. Silakan coba lagi.'
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: 'Terjadi kesalahan saat mengirim pesan'
                    });
                }
            })
            .finally(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
                uploadProgress.classList.add('hidden');
                if (progressInterval) {
                    clearInterval(progressInterval);
                }
            });
        });

        function resetForm() {
            document.getElementById('pesanForm').reset();
            document.getElementById('fileInfo').classList.add('hidden');
            document.getElementById('uploadProgress').classList.add('hidden');
        }
    </script>
</body>
</html>
