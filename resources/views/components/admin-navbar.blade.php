<!-- Admin Navbar Component -->
<div class="bg-[#ff040c] p-3 text-white">
    <div class="flex justify-between items-center">
        <div class="flex items-center space-x-3">
            @if(request()->route()->getName() !== 'admin.dashboard')
                <a href="{{ route('admin.dashboard') }}" class="text-white hover:text-gray-200 transition-colors" title="Kembali ke Dashboard">
                    <i class="fas fa-arrow-left text-lg"></i>
                </a>
            @endif
            <div class="flex items-center space-x-2">
                <div class="w-8 h-8 bg-white rounded-lg flex items-center justify-center">
                    <i class="fas fa-building text-[#ff040c] text-sm"></i>
                </div>
                <div>
                    <span class="font-semibold text-sm">{{ $title ?? 'Admin Panel' }}</span>
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
            
            <!-- Quick Actions Menu -->
            <div class="relative group">
                <button class="text-white hover:text-gray-200 transition-colors" title="Menu Cepat">
                    <i class="fas fa-ellipsis-v text-lg"></i>
                </button>
                <div class="absolute right-0 top-full mt-2 w-48 bg-white rounded-lg shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                    <div class="py-2">
                        <a href="{{ route('admin.karyawan.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-users mr-3 text-[#ff040c]"></i>
                            Kelola Karyawan
                        </a>
                        <a href="{{ route('admin.laporan.absensi') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-chart-bar mr-3 text-[#ff040c]"></i>
                            Laporan Absensi
                        </a>
                        <a href="{{ route('admin.cuti.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-calendar-times mr-3 text-[#ff040c]"></i>
                            Kelola Cuti
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
