<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Absen Pulang - Karyawan</title>
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
                <span class="font-semibold text-sm">Absen Pulang</span>
            </div>
        </div>
    </div>

    <!-- Content -->
    <div class="p-4">
        <div class="max-w-md mx-auto">
            <!-- Camera Preview -->
            <div class="bg-white rounded-xl shadow-sm p-4 mb-4">
                <h3 class="text-sm font-semibold text-gray-800 mb-3">Foto Absen Pulang</h3>
                <div class="relative">
                    <video id="camera" class="w-full h-64 bg-gray-100 rounded-lg" autoplay></video>
                    <canvas id="canvas" class="hidden"></canvas>
                    <div id="photoPreview" class="hidden w-full h-64 bg-gray-100 rounded-lg bg-cover bg-center"></div>
                    
                    <div class="absolute bottom-2 left-1/2 transform -translate-x-1/2 flex space-x-2">
                        <button id="captureBtn" class="bg-[#ff040c] text-white px-4 py-2 rounded-lg text-sm hover:bg-[#fb0302] transition-colors">
                            <i class="fas fa-camera mr-1"></i>Ambil Foto
                        </button>
                        <button id="retakeBtn" class="hidden bg-gray-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-gray-700 transition-colors">
                            <i class="fas fa-redo mr-1"></i>Ulangi
                        </button>
                    </div>
                </div>
            </div>

            <!-- Form -->
            <form id="absenForm" class="bg-white rounded-xl shadow-sm p-4">
                @csrf
                <input type="hidden" id="latitude" name="latitude">
                <input type="hidden" id="longitude" name="longitude">
                <input type="hidden" id="photoData" name="foto">
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Keterangan (Opsional)</label>
                    <textarea name="keterangan" rows="3" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#ff040c]"
                        placeholder="Tambahkan keterangan jika diperlukan..."></textarea>
                </div>

                <!-- Location Info -->
                <div class="mb-4 p-3 bg-blue-50 rounded-lg">
                    <div class="flex items-center space-x-2 text-blue-700">
                        <i class="fas fa-map-marker-alt"></i>
                        <span class="text-sm" id="locationText">Mendapatkan lokasi...</span>
                    </div>
                </div>

                <button type="submit" id="submitBtn" disabled
                    class="w-full bg-[#ff040c] text-white py-3 rounded-xl font-semibold hover:bg-[#fb0302] transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                    <i class="fas fa-check mr-2"></i>Absen Pulang
                </button>
            </form>

            <!-- Status Messages -->
            <div id="statusMessage" class="hidden mt-4 p-3 rounded-lg"></div>
        </div>
    </div>

    <script>
        let stream = null;
        let photoTaken = false;
        let locationObtained = false;

        // Initialize camera
        async function initCamera() {
            try {
                stream = await navigator.mediaDevices.getUserMedia({ 
                    video: { 
                        facingMode: 'user',
                        width: { ideal: 640 },
                        height: { ideal: 480 }
                    } 
                });
                document.getElementById('camera').srcObject = stream;
            } catch (err) {
                console.error('Error accessing camera:', err);
                showStatus('Tidak dapat mengakses kamera', 'error');
            }
        }

        // Get location
        function getLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        const lat = position.coords.latitude;
                        const lng = position.coords.longitude;
                        
                        document.getElementById('latitude').value = lat;
                        document.getElementById('longitude').value = lng;
                        document.getElementById('locationText').textContent = `Lat: ${lat.toFixed(6)}, Lng: ${lng.toFixed(6)}`;
                        
                        locationObtained = true;
                        checkFormReady();
                    },
                    function(error) {
                        console.error('Error getting location:', error);
                        document.getElementById('locationText').textContent = 'Tidak dapat mendapatkan lokasi';
                        showStatus('Tidak dapat mendapatkan lokasi. Pastikan izin lokasi diaktifkan.', 'error');
                    }
                );
            } else {
                document.getElementById('locationText').textContent = 'Geolokasi tidak didukung';
                showStatus('Browser tidak mendukung geolokasi', 'error');
            }
        }

        // Capture photo
        document.getElementById('captureBtn').addEventListener('click', function() {
            const video = document.getElementById('camera');
            const canvas = document.getElementById('canvas');
            const photoPreview = document.getElementById('photoPreview');
            
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            canvas.getContext('2d').drawImage(video, 0, 0);
            
            const photoData = canvas.toDataURL('image/jpeg');
            document.getElementById('photoData').value = photoData;
            
            // Show photo preview
            photoPreview.style.backgroundImage = `url(${photoData})`;
            photoPreview.classList.remove('hidden');
            video.classList.add('hidden');
            
            // Show retake button
            document.getElementById('captureBtn').classList.add('hidden');
            document.getElementById('retakeBtn').classList.remove('hidden');
            
            photoTaken = true;
            checkFormReady();
        });

        // Retake photo
        document.getElementById('retakeBtn').addEventListener('click', function() {
            const video = document.getElementById('camera');
            const photoPreview = document.getElementById('photoPreview');
            
            photoPreview.classList.add('hidden');
            video.classList.remove('hidden');
            
            document.getElementById('captureBtn').classList.remove('hidden');
            document.getElementById('retakeBtn').classList.add('hidden');
            
            photoTaken = false;
            checkFormReady();
        });

        // Check if form is ready
        function checkFormReady() {
            const submitBtn = document.getElementById('submitBtn');
            if (photoTaken && locationObtained) {
                submitBtn.disabled = false;
            } else {
                submitBtn.disabled = true;
            }
        }

        // Show status message
        function showStatus(message, type) {
            const statusDiv = document.getElementById('statusMessage');
            statusDiv.className = `mt-4 p-3 rounded-lg ${
                type === 'error' ? 'bg-red-100 text-red-700 border border-red-400' : 
                type === 'success' ? 'bg-green-100 text-green-700 border border-green-400' : 
                'bg-blue-100 text-blue-700 border border-blue-400'
            }`;
            statusDiv.textContent = message;
            statusDiv.classList.remove('hidden');
        }

        // Handle form submission
        document.getElementById('absenForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const submitBtn = document.getElementById('submitBtn');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Memproses...';
            
            try {
                const formData = new FormData(this);
                
                const response = await fetch('{{ route("karyawan.absen.pulang") }}', {
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
                    submitBtn.innerHTML = '<i class="fas fa-check mr-2"></i>Absen Pulang';
                }
            } catch (error) {
                console.error('Error:', error);
                showStatus('Terjadi kesalahan. Silakan coba lagi.', 'error');
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-check mr-2"></i>Absen Pulang';
            }
        });

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            initCamera();
            getLocation();
        });
    </script>
</body>
</html>
