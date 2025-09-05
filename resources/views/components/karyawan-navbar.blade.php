<!-- Karyawan Navbar Component -->
<div class="bg-[#ff040c] p-3 text-white">
    <div class="flex justify-between items-center">
        <div class="flex items-center space-x-3">
            @if(request()->route()->getName() !== 'karyawan.dashboard')
                <a href="{{ route('karyawan.dashboard') }}" class="text-white hover:text-gray-200 transition-colors" title="Kembali ke Dashboard">
                    <i class="fas fa-arrow-left text-lg"></i>
                </a>
            @endif
            <div class="flex items-center space-x-2">
                <div class="w-8 h-8 bg-white rounded-lg flex items-center justify-center">
                    <i class="fas fa-user text-[#ff040c] text-sm"></i>
                </div>
                <div>
                    <span class="font-semibold text-sm">{{ $title ?? 'Dashboard Karyawan' }}</span>
                    @if(isset($subtitle))
                        <div class="text-xs text-gray-200">{{ $subtitle }}</div>
                    @endif
                </div>
            </div>
        </div>
        <div class="flex items-center space-x-3">
            @if(isset($actions) && count($actions) > 0)
                @foreach($actions as $action)
                    <a href="{{ $action['url'] }}" 
                       class="inline-flex items-center px-3 py-1 bg-white bg-opacity-20 text-white text-sm rounded-md hover:bg-opacity-30 transition-colors"
                       title="{{ $action['text'] }}">
                        <i class="{{ $action['icon'] }} mr-1"></i>
                        <span class="hidden sm:inline">{{ $action['text'] }}</span>
                    </a>
                @endforeach
            @endif
            
            <!-- Notification Bell -->
            <div class="relative">
                <a href="{{ route('karyawan.pesan.index') }}" class="text-white hover:text-gray-200 transition-colors" title="Pesan">
                    <i id="bellIcon" class="fas fa-bell text-lg"></i>
                    <span id="bellBadge" class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center font-bold z-10" style="display: none;">0</span>
                </a>
            </div>
            
            <!-- Quick Actions Menu -->
            <div class="relative group">
                <button class="text-white hover:text-gray-200 transition-colors" title="Menu Cepat">
                    <i class="fas fa-ellipsis-v text-lg"></i>
                </button>
                <div class="absolute right-0 top-full mt-2 w-48 bg-white rounded-lg shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                    <div class="py-2">
                        <a href="{{ route('karyawan.absen.masuk') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-camera mr-3 text-[#ff040c]"></i>
                            Absen Masuk
                        </a>
                        <a href="{{ route('karyawan.izin.cuti') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-file-alt mr-3 text-[#ff040c]"></i>
                            Ajukan Izin
                        </a>
                        <a href="{{ route('karyawan.history') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-calendar mr-3 text-[#ff040c]"></i>
                            History Absensi
                        </a>
                        <a href="{{ route('karyawan.profile') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-user mr-3 text-[#ff040c]"></i>
                            Profil Saya
                        </a>
                        <div class="border-t border-gray-100 my-1"></div>
                        <form method="POST" action="{{ route('logout') }}" class="block">
                            @csrf
                            <button type="submit" class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-sign-out-alt mr-3 text-red-500"></i>
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Load unread message count for navbar
    function loadUnreadCount() {
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        const headers = {};
        if (csrfToken) {
            headers['X-CSRF-TOKEN'] = csrfToken.getAttribute('content');
        }
        
        fetch('{{ route("karyawan.pesan.unread-count") }}', {
            headers: headers
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                const bellBadge = document.getElementById('bellBadge');
                const bellIcon = document.getElementById('bellIcon');
                
                if (data.unread_count > 0) {
                    if (bellBadge) {
                        bellBadge.textContent = data.unread_count;
                        bellBadge.style.display = 'flex';
                    }
                    
                    if (bellIcon) {
                        bellIcon.classList.add('animate-bounce');
                        setTimeout(() => {
                            bellIcon.classList.remove('animate-bounce');
                        }, 1000);
                    }
                } else {
                    if (bellBadge) {
                        bellBadge.style.display = 'none';
                    }
                }
            })
            .catch(error => {
                console.error('Error loading unread count:', error);
            });
    }

    // Load unread count when navbar is loaded
    document.addEventListener('DOMContentLoaded', function() {
        loadUnreadCount();
        setInterval(loadUnreadCount, 10000); // Refresh every 10 seconds
    });
</script>
