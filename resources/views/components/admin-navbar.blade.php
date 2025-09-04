<!-- Admin Navbar Component -->
<div class="bg-[#ff040c] p-3 text-white">
    <div class="flex justify-between items-center">
        <div class="flex items-center space-x-2">
            <a href="{{ route('admin.dashboard') }}" class="text-white hover:text-gray-200">
                <i class="fas fa-arrow-left text-lg"></i>
            </a>
            <div class="flex items-center space-x-2">
                <div class="w-6 h-6 bg-white rounded flex items-center justify-center">
                    <div class="w-3 h-3 bg-[#ff040c] transform rotate-45"></div>
                </div>
                <span class="font-semibold text-sm">{{ $title ?? 'Admin Panel' }}</span>
            </div>
        </div>
        <div class="flex items-center space-x-3">
            @if(isset($actions))
                @foreach($actions as $action)
                    <a href="{{ $action['url'] }}" class="text-white text-sm hover:text-gray-200">
                        <i class="{{ $action['icon'] }} mr-1"></i>{{ $action['text'] }}
                    </a>
                @endforeach
            @endif
            <i class="fas fa-bell text-lg"></i>
            <i class="fas fa-user-circle text-xl"></i>
        </div>
    </div>
</div>
