<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Absen Masuk - Karyawan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <div class="bg-[#2196F3] p-3 text-white">
        <div class="flex justify-between items-center">
            <div class="flex items-center space-x-2">
                <div class="w-6 h-6 bg-white rounded flex items-center justify-center">
                    <div class="w-3 h-3 bg-[#2196F3] transform rotate-45"></div>
                </div>
                <span class="font-semibold text-sm">ABSEN PROJECT</span>
            </div>
            <div class="flex items-center space-x-3">
                <i class="fas fa-bell text-lg"></i>
                <i class="fas fa-user-circle text-xl"></i>
            </div>
        </div>
    </div>

    <!-- Main Content Card -->
    <div class="mx-4 mt-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm p-4">
            
            <!-- Tabs -->
            <div class="flex border-b border-gray-200 mb-4">
                <button class="flex-1 py-2 px-4 text-center border-b-2 border-[#2196F3] text-[#2196F3] font-medium text-sm">
                    Kantor
                </button>
                <button class="flex-1 py-2 px-4 text-center text-gray-500 font-medium text-sm">
                    Dinas Luar
                </button>
            </div>

            <!-- User Info & Time -->
            <div class="flex justify-between items-start mb-4">
                <div>
                    <h1 class="font-bold text-black text-lg" id="greeting">Selamat Siang</h1>
                    <p class="font-bold text-black text-base" id="userName">{{ Auth::user()->name ?? 'Karyawan' }}</p>
                </div>
                <div class="text-right">
                    <p class="text-black text-base" id="currentDate">{{ now()->format('d F Y') }}</p>
                    <p class="text-black text-base" id="currentTime">{{ now()->format('H:i:s') }}</p>
                </div>
            </div>

            <!-- Geolocation Data -->
            <div class="text-center mb-4">
                <p class="text-gray-500 text-sm" id="locationText">
                    Lat-Long : Mendapatkan lokasi...
                </p>
                <p class="text-gray-500 text-xs mt-1" id="statusText">
                    Model selesai dimuat.
                </p>
            </div>

            <!-- Separator -->
            <div class="border-t border-gray-200 mb-4"></div>

            <!-- Camera Section -->
            <div class="mb-4">
                <h3 class="font-semibold text-gray-800 text-sm mb-3">Foto Absen</h3>
                <div class="relative">
                    <video id="camera" class="w-full h-64 bg-gray-100 rounded-lg" autoplay muted></video>
                    <canvas id="canvas" class="hidden"></canvas>
                    <div id="photoPreview" class="hidden w-full h-64 bg-gray-100 rounded-lg bg-cover bg-center"></div>
                    
                    <!-- Camera Controls -->
                    <div class="absolute bottom-2 left-1/2 transform -translate-x-1/2 flex space-x-2">
                        <button id="captureBtn" class="bg-[#2196F3] text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-600 transition-colors">
                            <i class="fas fa-camera mr-1"></i>Ambil Foto
                        </button>
                        <button id="retakeBtn" class="hidden bg-gray-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-gray-700 transition-colors">
                            <i class="fas fa-redo mr-1"></i>Ulangi
                        </button>
                    </div>
                </div>
            </div>

            <!-- Absen Button -->
            <button id="absenBtn" disabled
                class="w-full bg-[#2196F3] text-white py-4 rounded-xl font-bold text-lg hover:bg-blue-600 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                <i class="fas fa-camera mr-2"></i>Absen Masuk
            </button>

            <!-- Debug Info (Hidden in production) -->
            <div class="mt-4 p-3 bg-gray-100 rounded-lg text-xs hidden" id="debugInfo">
                <div><strong>Latitude:</strong> <span id="debugLat">-</span></div>
                <div><strong>Longitude:</strong> <span id="debugLng">-</span></div>
                <div><strong>Photo Taken:</strong> <span id="debugPhoto">No</span></div>
                <div><strong>Form Ready:</strong> <span id="debugFormReady">No</span></div>
            </div>
        </div>
    </div>

    <!-- Status Messages -->
    <div id="statusMessage" class="hidden mx-4 mb-4 p-3 rounded-lg text-sm"></div>

    <script>
        let stream = null;
        let photoTaken = false;
        let locationObtained = false;
        let currentPhotoData = null;

        // Update greeting based on time
        function updateGreeting() {
            const hour = new Date().getHours();
            let greeting = 'Selamat Pagi';
            
            if (hour >= 12 && hour < 15) {
                greeting = 'Selamat Siang';
            } else if (hour >= 15 && hour < 18) {
                greeting = 'Selamat Sore';
            } else if (hour >= 18) {
                greeting = 'Selamat Malam';
            }
            
            document.getElementById('greeting').textContent = greeting;
        }

        // Update time every second
        function updateTime() {
            const now = new Date();
            document.getElementById('currentDate').textContent = now.toLocaleDateString('id-ID', {
                day: 'numeric',
                month: 'long',
                year: 'numeric'
            });
            document.getElementById('currentTime').textContent = now.toLocaleTimeString('id-ID');
        }

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
                console.log('Camera initialized successfully');
            } catch (err) {
                console.error('Error accessing camera:', err);
                showStatus('Tidak dapat mengakses kamera', 'error');
            }
        }

        // Get location
        function getLocation() {
            console.log('Getting location...');
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        const lat = position.coords.latitude;
                        const lng = position.coords.longitude;
                        
                        console.log('Location obtained:', lat, lng);
                        
                        // Update location display
                        document.getElementById('locationText').textContent = `Lat-Long : ${lat.toFixed(7)}, ${lng.toFixed(7)}`;
                        
                        // Update debug info
                        document.getElementById('debugLat').textContent = lat.toFixed(7);
                        document.getElementById('debugLng').textContent = lng.toFixed(7);
                        
                        locationObtained = true;
                        checkFormReady();
                    },
                    function(error) {
                        console.error('Error getting location:', error);
                        document.getElementById('locationText').textContent = 'Lat-Long : Tidak dapat mendapatkan lokasi';
                        showStatus('Tidak dapat mendapatkan lokasi. Pastikan izin lokasi diaktifkan.', 'error');
                    }
                );
            } else {
                document.getElementById('locationText').textContent = 'Lat-Long : Geolokasi tidak didukung';
                showStatus('Browser tidak mendukung geolokasi', 'error');
            }
        }

        // Capture photo
        document.getElementById('captureBtn').addEventListener('click', function() {
            console.log('Capturing photo...');
            const video = document.getElementById('camera');
            const canvas = document.getElementById('canvas');
            const photoPreview = document.getElementById('photoPreview');
            
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            canvas.getContext('2d').drawImage(video, 0, 0);
            
            const photoData = canvas.toDataURL('image/jpeg');
            console.log('Photo captured, length:', photoData.length);
            
            // Store photo data
            currentPhotoData = photoData;
            
            // Show photo preview
            photoPreview.style.backgroundImage = `url(${photoData})`;
            photoPreview.classList.remove('hidden');
            video.classList.add('hidden');
            
            // Show retake button
            document.getElementById('captureBtn').classList.add('hidden');
            document.getElementById('retakeBtn').classList.remove('hidden');
            
            // Update debug info
            document.getElementById('debugPhoto').textContent = 'Yes';
            
            photoTaken = true;
            checkFormReady();
        });

        // Retake photo
        document.getElementById('retakeBtn').addEventListener('click', function() {
            console.log('Retaking photo...');
            const video = document.getElementById('camera');
            const photoPreview = document.getElementById('photoPreview');
            
            photoPreview.classList.add('hidden');
            video.classList.remove('hidden');
            
            document.getElementById('captureBtn').classList.remove('hidden');
            document.getElementById('retakeBtn').classList.add('hidden');
            
            // Clear photo data
            currentPhotoData = null;
            
            // Update debug info
            document.getElementById('debugPhoto').textContent = 'No';
            
            photoTaken = false;
            checkFormReady();
        });

        // Check if form is ready
        function checkFormReady() {
            const absenBtn = document.getElementById('absenBtn');
            const isReady = photoTaken && locationObtained;
            
            console.log('Form ready check:', { photoTaken, locationObtained, isReady });
            
            if (isReady) {
                absenBtn.disabled = false;
                document.getElementById('debugFormReady').textContent = 'Yes';
            } else {
                absenBtn.disabled = true;
                document.getElementById('debugFormReady').textContent = 'No';
            }
        }

        // Handle absen button click
        document.getElementById('absenBtn').addEventListener('click', async function() {
            if (!photoTaken || !locationObtained || !currentPhotoData) {
                showStatus('Data tidak lengkap. Pastikan foto dan lokasi sudah diambil.', 'error');
                return;
            }

            console.log('Submitting absen...');
            
            const absenBtn = document.getElementById('absenBtn');
            absenBtn.disabled = true;
            absenBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Memproses...';
            
            // Get coordinates from location text
            const locationText = document.getElementById('locationText').textContent;
            const coordsMatch = locationText.match(/Lat-Long : ([\d.-]+), ([\d.-]+)/);
            
            if (!coordsMatch) {
                showStatus('Koordinat lokasi tidak valid', 'error');
                absenBtn.disabled = false;
                absenBtn.innerHTML = '<i class="fas fa-camera mr-2"></i>Absen Masuk';
                return;
            }
            
            const latitude = coordsMatch[1];
            const longitude = coordsMatch[2];
            
            // Create form data
            const formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('latitude', latitude);
            formData.append('longitude', longitude);
            formData.append('foto', currentPhotoData);
            formData.append('keterangan', 'Absen masuk via mobile app');
            
            // Debug: Log form data
            console.log('Form Data:');
            console.log('Latitude:', latitude);
            console.log('Longitude:', longitude);
            console.log('Foto length:', currentPhotoData.length);
            
            try {
                const response = await fetch('{{ route("test.absen.masuk") }}', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                console.log('Response:', result);
                
                if (result.success) {
                    showStatus('Test berhasil! ' + result.message, 'success');
                    // Show radius info
                    if (result.data) {
                        const info = result.data;
                        showStatus(`Test berhasil! Jarak: ${info.calculated_distance}m, Radius: ${info.office_radius}m, Dalam radius: ${info.within_radius ? 'Ya' : 'Tidak'}`, 'success');
                    }
                    setTimeout(() => {
                        window.location.href = '{{ route("karyawan.dashboard") }}';
                    }, 3000);
                } else {
                    showStatus(result.message, 'error');
                    absenBtn.disabled = false;
                    absenBtn.innerHTML = '<i class="fas fa-camera mr-2"></i>Absen Masuk';
                }
            } catch (error) {
                console.error('Error:', error);
                showStatus('Terjadi kesalahan. Silakan coba lagi.', 'error');
                absenBtn.disabled = false;
                absenBtn.innerHTML = '<i class="fas fa-camera mr-2"></i>Absen Masuk';
            }
        });

        // Show status message
        function showStatus(message, type) {
            const statusDiv = document.getElementById('statusMessage');
            statusDiv.className = `mx-4 mb-4 p-3 rounded-lg text-sm ${
                type === 'error' ? 'bg-red-100 text-red-700 border border-red-400' : 
                type === 'success' ? 'bg-green-100 text-green-700 border border-green-400' : 
                'bg-blue-100 text-blue-700 border border-blue-400'
            }`;
            statusDiv.textContent = message;
            statusDiv.classList.remove('hidden');
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Page loaded, initializing...');
            
            // Update greeting and time
            updateGreeting();
            updateTime();
            
            // Update time every second
            setInterval(updateTime, 1000);
            
            // Initialize camera and location
            initCamera();
            getLocation();
            
            // Show debug info in development
            if (window.location.hostname === '127.0.0.1' || window.location.hostname === 'localhost') {
                document.getElementById('debugInfo').classList.remove('hidden');
            }
        });
    </script>
</body>
</html>
