<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Absen Masuk - Karyawan</title>
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
                <span class="font-semibold text-sm">Test Absen Masuk</span>
            </div>
        </div>
    </div>

    <!-- Content -->
    <div class="p-4">
        <div class="max-w-md mx-auto">
            <!-- Debug Info -->
            <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-3 py-2 rounded mb-4 text-xs">
                <strong>Test Mode:</strong> Form sederhana untuk debug submission
            </div>

            <!-- Simple Test Form -->
            <div class="bg-white rounded-xl shadow-sm p-4 mb-4">
                <h3 class="text-sm font-semibold text-gray-800 mb-3">Test Form</h3>
                
                <form id="testForm" class="space-y-3">
                    @csrf
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Latitude</label>
                        <input type="text" id="testLat" name="latitude" value="-6.2088" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                    </div>
                    
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Longitude</label>
                        <input type="text" id="testLng" name="longitude" value="106.8456" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                    </div>
                    
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Foto (Base64)</label>
                        <textarea id="testFoto" name="foto" rows="3" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm"
                            placeholder="Data base64 foto akan muncul di sini..."></textarea>
                    </div>
                    
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Keterangan</label>
                        <input type="text" name="keterangan" value="Test keterangan" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                    </div>
                    
                    <button type="submit" 
                        class="w-full bg-[#ff040c] text-white py-3 rounded-xl font-semibold hover:bg-[#fb0302] transition-colors text-sm">
                        <i class="fas fa-paper-plane mr-2"></i>Test Submit
                    </button>
                </form>
            </div>

            <!-- Camera Preview (Hidden for test) -->
            <div class="bg-white rounded-xl shadow-sm p-4 mb-4 hidden">
                <h3 class="text-sm font-semibold text-gray-800 mb-3">Foto Absen</h3>
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

            <!-- Status Messages -->
            <div id="statusMessage" class="hidden mt-4 p-3 rounded-lg text-sm"></div>
        </div>
    </div>

    <script>
        // Generate fake base64 photo data
        function generateFakePhoto() {
            const canvas = document.createElement('canvas');
            canvas.width = 100;
            canvas.height = 100;
            const ctx = canvas.getContext('2d');
            
            // Draw a simple colored rectangle
            ctx.fillStyle = '#ff040c';
            ctx.fillRect(0, 0, 100, 100);
            ctx.fillStyle = 'white';
            ctx.font = '20px Arial';
            ctx.fillText('TEST', 20, 60);
            
            return canvas.toDataURL('image/jpeg');
        }

        // Set fake photo data
        document.getElementById('testFoto').value = generateFakePhoto();

        // Handle test form submission
        document.getElementById('testForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            console.log('Test form submitted!');
            
            const formData = new FormData(this);
            
            // Debug: Log form data
            console.log('Form Data:');
            for (let [key, value] of formData.entries()) {
                if (key === 'foto') {
                    console.log(key + ':', value.substring(0, 50) + '...');
                } else {
                    console.log(key + ':', value);
                }
            }
            
            try {
                const response = await fetch('{{ route("test.absen") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                    }
                });
                
                const result = await response.json();
                console.log('Response:', result);
                
                if (result.success) {
                    showStatus('Test berhasil! Data terkirim: ' + JSON.stringify(result.data), 'success');
                } else {
                    showStatus(result.message, 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                showStatus('Terjadi kesalahan. Silakan coba lagi.', 'error');
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
