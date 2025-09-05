<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Absen Masuk - Karyawan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <x-karyawan-navbar title="Absen Masuk" subtitle="ABSEN PROJECT" />

    <!-- Main Content Card -->
    <div class="mx-4 mt-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm p-4">
            
            <!-- Tabs -->
            <div class="flex border-b border-gray-200 mb-4">
                <button id="tabKantor" class="flex-1 py-2 px-4 text-center border-b-2 border-[#ff040c] text-[#ff040c] font-medium text-sm">
                    Kantor
                </button>
                <button id="tabDinasLuar" class="flex-1 py-2 px-4 text-center text-gray-500 font-medium text-sm">
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
                    Mendapatkan lokasi...
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
                        <button id="captureBtn" class="bg-[#ff040c] text-white px-4 py-2 rounded-lg text-sm hover:bg-[#fb0302] transition-colors">
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
                class="w-full bg-[#ff040c] text-white py-4 rounded-xl font-bold text-lg hover:bg-[#fb0302] transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                <i class="fas fa-camera mr-2"></i><span id="absenButtonText">Absen Masuk</span>
            </button>
            
            <!-- Dinas Luar Form (Hidden by default) -->
            <div id="dinasLuarForm" class="hidden">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Alasan Dinas Luar</label>
                    <textarea id="alasanDinasLuar" rows="3" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#ff040c] text-sm"
                        placeholder="Jelaskan alasan dinas luar..."></textarea>
                </div>
                
                <button id="absenDinasLuarBtn" disabled
                    class="w-full bg-[#ff040c] text-white py-4 rounded-xl font-bold text-lg hover:bg-[#fb0302] transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                    <i class="fas fa-camera mr-2"></i><span id="absenDinasLuarButtonText">Absen Dinas Luar</span>
                </button>
            </div>
            
            <!-- Status Absensi Hari Ini -->
            <div id="todayStatus" class="mt-4 p-3 bg-gray-50 rounded-lg text-center hidden">
                <p class="text-sm text-gray-600 mb-1">Status Absensi Hari Ini:</p>
                <div id="statusInfo" class="text-sm font-medium"></div>
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
        let todayAbsensi = null;
        let absenType = 'masuk'; // 'masuk' atau 'pulang'
        let currentTab = 'kantor'; // 'kantor' atau 'dinas_luar'

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

        // Tab switching functionality
        function switchTab(tab) {
            const tabKantor = document.getElementById('tabKantor');
            const tabDinasLuar = document.getElementById('tabDinasLuar');
            const absenBtn = document.getElementById('absenBtn');
            const dinasLuarForm = document.getElementById('dinasLuarForm');
            const absenDinasLuarBtn = document.getElementById('absenDinasLuarBtn');
            
            if (tab === 'kantor') {
                currentTab = 'kantor';
                tabKantor.classList.add('border-b-2', 'border-[#ff040c]', 'text-[#ff040c]');
                tabKantor.classList.remove('text-gray-500');
                tabDinasLuar.classList.remove('border-b-2', 'border-[#ff040c]', 'text-[#ff040c]');
                tabDinasLuar.classList.add('text-gray-500');
                absenBtn.classList.remove('hidden');
                dinasLuarForm.classList.add('hidden');
            } else {
                currentTab = 'dinas_luar';
                tabDinasLuar.classList.add('border-b-2', 'border-[#ff040c]', 'text-[#ff040c]');
                tabDinasLuar.classList.remove('text-gray-500');
                tabKantor.classList.remove('border-b-2', 'border-[#ff040c]', 'text-[#ff040c]');
                tabKantor.classList.add('text-gray-500');
                absenBtn.classList.add('hidden');
                dinasLuarForm.classList.remove('hidden');
            }
            
            // Update button states
            updateButtonStates();
        }

        // Update button states based on current tab and form readiness
        function updateButtonStates() {
            const isReady = photoTaken && locationObtained;
            const absenBtn = document.getElementById('absenBtn');
            const absenDinasLuarBtn = document.getElementById('absenDinasLuarBtn');
            const alasanDinasLuar = document.getElementById('alasanDinasLuar');
            
            if (currentTab === 'kantor') {
                if (isReady) {
                    absenBtn.disabled = false;
                } else {
                    absenBtn.disabled = true;
                }
            } else {
                // For dinas luar, need photo, location, and reason
                const dinasLuarReady = isReady && alasanDinasLuar.value.trim() !== '';
                if (dinasLuarReady) {
                    absenDinasLuarBtn.disabled = false;
                } else {
                    absenDinasLuarBtn.disabled = true;
                }
            }
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
                        
                        // Store location data
                        window.currentLocation = { lat: lat, lng: lng };
                        
                        // Update location display
                        document.getElementById('locationText').textContent = `Lokasi berhasil didapatkan`;
                        
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
            
            photoTaken = true;
            checkFormReady();
        });

        // Reset form after successful absen
        function resetForm() {
            // Reset camera
            const video = document.getElementById('camera');
            const photoPreview = document.getElementById('photoPreview');
            
            photoPreview.classList.add('hidden');
            video.classList.remove('hidden');
            
            document.getElementById('captureBtn').classList.remove('hidden');
            document.getElementById('retakeBtn').classList.add('hidden');
            
            // Clear photo data
            currentPhotoData = null;
            photoTaken = false;
            
            // Re-enable button if needed
            checkFormReady();
        }
        
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
            } else {
                absenBtn.disabled = true;
            }
        }

                    // Handle absen button click
            document.getElementById('absenBtn').addEventListener('click', async function() {
                if (!photoTaken || !locationObtained || !currentPhotoData) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Data Tidak Lengkap!',
                        text: 'Pastikan foto dan lokasi sudah diambil sebelum absen.',
                        confirmButtonText: 'Baik',
                        confirmButtonColor: '#ff040c'
                    });
                    return;
                }
                
                // Check if already completed today
                if (absenType === 'complete') {
                    Swal.fire({
                        icon: 'info',
                        title: 'Absensi Selesai',
                        text: 'Anda sudah absen masuk dan pulang hari ini.',
                        confirmButtonText: 'Baik',
                        confirmButtonColor: '#ff040c'
                    });
                    return;
                }
                
                // Show confirmation dialog
                const confirmTitle = absenType === 'masuk' ? 'Konfirmasi Absen Masuk' : 'Konfirmasi Absen Pulang';
                const confirmText = absenType === 'masuk' ? 'Apakah Anda yakin ingin absen masuk sekarang?' : 'Apakah Anda yakin ingin absen pulang sekarang?';
                const confirmButtonText = absenType === 'masuk' ? 'Ya, Absen Masuk' : 'Ya, Absen Pulang';
                
                const confirmResult = await Swal.fire({
                    icon: 'question',
                    title: confirmTitle,
                    text: confirmText,
                    showCancelButton: true,
                    confirmButtonText: confirmButtonText,
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#ff040c',
                    cancelButtonColor: '#6b7280'
                });
                
                if (!confirmResult.isConfirmed) {
                    return;
                }

            console.log('Submitting absen...');
            
            const absenBtn = document.getElementById('absenBtn');
            absenBtn.disabled = true;
            absenBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Memproses...';
            
            // Show loading state
            Swal.fire({
                title: 'Memproses Absensi...',
                html: 'Mohon tunggu sebentar',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Get coordinates from stored location data
            if (!window.currentLocation) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Lokasi Tidak Valid!',
                    text: 'Koordinat lokasi tidak dapat dibaca. Pastikan GPS aktif dan coba lagi.',
                    confirmButtonText: 'Coba Lagi',
                    confirmButtonColor: '#ff040c'
                });
                absenBtn.disabled = false;
                absenBtn.innerHTML = '<i class="fas fa-camera mr-2"></i>Absen Masuk';
                return;
            }
            
            const latitude = window.currentLocation.lat;
            const longitude = window.currentLocation.lng;
            
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
                // Use the appropriate route based on absen type
                let route;
                if (absenType === 'masuk') {
                    route = '{{ route("karyawan.absen.masuk.post") }}';
                } else if (absenType === 'pulang') {
                    route = '{{ route("karyawan.absen.pulang") }}';
                }
                
                const response = await fetch(route, {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                console.log('Response:', result);
                
                // Close loading state
                Swal.close();
                
                if (result.success) {
                    // Get current time for attendance (use server time from response if available)
                    let attendanceTime;
                    if (result.data?.absensi?.jam_masuk) {
                        // Use server time from database
                        attendanceTime = result.data.absensi.jam_masuk;
                    } else if (result.data?.absensi?.jam_pulang) {
                        // Use server time from database
                        attendanceTime = result.data.absensi.jam_pulang;
                    } else {
                        // Fallback to local time
                        const now = new Date();
                        attendanceTime = now.toLocaleTimeString('id-ID', {
                            hour: '2-digit',
                            minute: '2-digit',
                            second: '2-digit'
                        });
                    }
                    
                    // Show success alert with attendance time
                    const successTitle = absenType === 'masuk' ? 'Absen Masuk Berhasil!' : 'Absen Pulang Berhasil!';
                    const successMessage = absenType === 'masuk' ? 'Selamat datang!' : 'Selamat pulang!';
                    
                    Swal.fire({
                        icon: 'success',
                        title: successTitle,
                        html: `
                            <div class="text-center">
                                <div class="text-2xl font-bold text-green-600 mb-2">✓</div>
                                <p class="text-lg font-semibold text-gray-800 mb-2">${successMessage}</p>
                                <p class="text-sm text-gray-600 mb-1">Anda berhasil absen ${absenType} pada:</p>
                                <p class="text-xl font-bold text-blue-600">${attendanceTime}</p>
                                ${absenType === 'masuk' ? `
                                <div class="mt-3 p-2 bg-gray-100 rounded-lg text-xs">
                                    <p class="text-gray-600">Jarak: ${result.data?.calculated_distance || 'N/A'}m</p>
                                    <p class="text-gray-600">Radius: ${result.data?.office_radius || 'N/A'}m</p>
                                    <p class="text-gray-600">Status: ${result.data?.within_radius ? 'Dalam radius' : 'Luar radius'}</p>
                                </div>
                                ` : ''}
                            </div>
                        `,
                        confirmButtonText: 'Lanjut ke Dashboard',
                        confirmButtonColor: '#ff040c',
                        allowOutsideClick: false,
                        showCloseButton: false
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Redirect to dashboard
                            window.location.href = '{{ route("karyawan.dashboard") }}';
                        }
                    });
                } else {
                    // Show error alert
                    Swal.fire({
                        icon: 'error',
                        title: 'Absensi Gagal!',
                        text: result.message,
                        confirmButtonText: 'Coba Lagi',
                        confirmButtonColor: '#ff040c'
                    });
                    absenBtn.disabled = false;
                    absenBtn.innerHTML = '<i class="fas fa-camera mr-2"></i>Absen Masuk';
                }
            } catch (error) {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Terjadi Kesalahan!',
                    text: 'Silakan coba lagi atau hubungi administrator.',
                    confirmButtonText: 'Coba Lagi',
                    confirmButtonColor: '#ff040c'
                });
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

        // Check today's attendance status
        async function checkTodayAttendance() {
            try {
                const response = await fetch('{{ route("karyawan.attendance.current") }}');
                const result = await response.json();
                
                if (result.success) {
                    todayAbsensi = result.data;
                    updateAbsenButton();
                    updateStatusDisplay();
                }
            } catch (error) {
                console.error('Error checking attendance:', error);
            }
        }
        
        // Update absen button based on attendance status
        function updateAbsenButton() {
            const absenBtn = document.getElementById('absenBtn');
            const buttonText = document.getElementById('absenButtonText');
            
            if (!todayAbsensi || !todayAbsensi.has_attendance) {
                // Belum absen sama sekali
                absenType = 'masuk';
                buttonText.textContent = 'Absen Masuk';
                absenBtn.className = 'w-full bg-[#2196F3] text-white py-4 rounded-xl font-bold text-lg hover:bg-blue-600 transition-colors disabled:opacity-50 disabled:cursor-not-allowed';
            } else if (todayAbsensi.jam_masuk && !todayAbsensi.jam_pulang) {
                // Sudah absen masuk, belum pulang
                absenType = 'pulang';
                buttonText.textContent = 'Absen Pulang';
                absenBtn.className = 'w-full bg-[#fb0302] text-white py-4 rounded-xl font-bold text-lg hover:bg-red-600 transition-colors disabled:opacity-50 disabled:cursor-not-allowed';
            } else if (todayAbsensi.jam_masuk && todayAbsensi.jam_pulang) {
                // Sudah absen masuk dan pulang
                absenType = 'complete';
                buttonText.textContent = 'Absensi Selesai';
                absenBtn.disabled = true;
                absenBtn.className = 'w-full bg-gray-400 text-white py-4 rounded-xl font-bold text-lg cursor-not-allowed';
            }
        }
        
        // Update status display
        function updateStatusDisplay() {
            const statusDiv = document.getElementById('todayStatus');
            const statusInfo = document.getElementById('statusInfo');
            
            if (!todayAbsensi || !todayAbsensi.jam_masuk) {
                statusDiv.classList.add('hidden');
                return;
            }
            
            let statusText = '';
            if (todayAbsensi.jam_masuk && !todayAbsensi.jam_pulang) {
                statusText = `✓ Masuk: ${todayAbsensi.jam_masuk} | Belum pulang`;
            } else if (todayAbsensi.jam_masuk && todayAbsensi.jam_pulang) {
                statusText = `✓ Masuk: ${todayAbsensi.jam_masuk} | ✓ Pulang: ${todayAbsensi.jam_pulang}`;
            }
            
            statusInfo.textContent = statusText;
            statusDiv.classList.remove('hidden');
        }

        // Handle dinas luar absen
        async function handleDinasLuarAbsen() {
            if (!photoTaken || !locationObtained || !currentPhotoData) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Data Belum Lengkap!',
                    text: 'Pastikan foto sudah diambil dan lokasi sudah didapatkan.',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#ff040c'
                });
                return;
            }

            const alasanDinasLuar = document.getElementById('alasanDinasLuar').value.trim();
            if (!alasanDinasLuar) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Alasan Diperlukan!',
                    text: 'Silakan isi alasan dinas luar.',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#ff040c'
                });
                return;
            }

            console.log('Submitting dinas luar absen...');
            
            const absenDinasLuarBtn = document.getElementById('absenDinasLuarBtn');
            absenDinasLuarBtn.disabled = true;
            absenDinasLuarBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Memproses...';
            
            // Show loading state
            Swal.fire({
                title: 'Memproses Absensi Dinas Luar...',
                text: 'Mohon tunggu...',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });

            try {
                // Validate location
                if (!window.currentLocation || !window.currentLocation.lat || !window.currentLocation.lng) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Lokasi Tidak Valid!',
                        text: 'Koordinat lokasi tidak dapat dibaca. Pastikan GPS aktif dan coba lagi.',
                        confirmButtonText: 'Coba Lagi',
                        confirmButtonColor: '#ff040c'
                    });
                    absenDinasLuarBtn.disabled = false;
                    absenDinasLuarBtn.innerHTML = '<i class="fas fa-camera mr-2"></i>Absen Dinas Luar';
                    return;
                }
                
                const latitude = window.currentLocation.lat;
                const longitude = window.currentLocation.lng;
                
                console.log('Submitting with data:', {
                    latitude,
                    longitude,
                    foto: currentPhotoData.substring(0, 50) + '...',
                    alasan_dinas_luar: alasanDinasLuar,
                    dinas_luar: true
                });

                const response = await fetch('{{ route("karyawan.absen.masuk.post") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        latitude: latitude,
                        longitude: longitude,
                        foto: currentPhotoData,
                        alasan_dinas_luar: alasanDinasLuar,
                        dinas_luar: true
                    })
                });

                const result = await response.json();
                console.log('Dinas luar absen result:', result);

                Swal.close();

                if (result.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Absensi Dinas Luar Berhasil!',
                        text: result.message,
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#ff040c'
                    }).then(() => {
                        // Refresh attendance data
                        checkTodayAttendance();
                        // Reset form
                        document.getElementById('alasanDinasLuar').value = '';
                        photoTaken = false;
                        locationObtained = false;
                        currentPhotoData = null;
                        updateButtonStates();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Absensi Dinas Luar Gagal!',
                        text: result.message,
                        confirmButtonText: 'Coba Lagi',
                        confirmButtonColor: '#ff040c'
                    });
                    absenDinasLuarBtn.disabled = false;
                    absenDinasLuarBtn.innerHTML = '<i class="fas fa-camera mr-2"></i>Absen Dinas Luar';
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
                absenDinasLuarBtn.disabled = false;
                absenDinasLuarBtn.innerHTML = '<i class="fas fa-camera mr-2"></i>Absen Dinas Luar';
            }
        }
        
        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Page loaded, initializing...');
            
            // Update greeting and time
            updateGreeting();
            updateTime();
            
            // Update time every second
            setInterval(updateTime, 1000);
            
            // Check today's attendance first
            checkTodayAttendance();
            
            // Initialize camera and location
            initCamera();
            getLocation();
            
            // Tab switching event listeners
            document.getElementById('tabKantor').addEventListener('click', function() {
                switchTab('kantor');
            });
            
            document.getElementById('tabDinasLuar').addEventListener('click', function() {
                switchTab('dinas_luar');
            });
            
            // Dinas luar form event listeners
            document.getElementById('alasanDinasLuar').addEventListener('input', function() {
                updateButtonStates();
            });
            
            // Dinas luar button event listener
            document.getElementById('absenDinasLuarBtn').addEventListener('click', async function() {
                await handleDinasLuarAbsen();
            });
            
        });
    </script>
</body>
</html>
