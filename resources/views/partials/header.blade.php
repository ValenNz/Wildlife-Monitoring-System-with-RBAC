<header class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-40">
    <div class="flex items-center justify-between px-6 py-4">

        <!-- Page Title & Breadcrumb -->
        <div class="flex-1">
            <div class="flex items-center space-x-3 text-sm text-gray-600 mb-1">
                <a href="{{ route('dashboard.index') }}" class="hover:text-blue-600 transition-colors">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                    </svg>
                </a>
                <span>/</span>
                <span class="font-medium text-gray-900">@yield('page-title', 'Dashboard')</span>
            </div>
            <h2 class="text-2xl font-bold text-gray-900">@yield('page-title', 'Dashboard')</h2>
            <p class="text-sm text-gray-500 mt-1">@yield('page-subtitle', 'Welcome to Wildlife Monitoring System')</p>
        </div>

        <!-- Header Actions -->
        <div class="flex items-center space-x-4">

            <!-- Search Bar -->
            <div class="relative hidden md:block">
                <input type="text"
                       placeholder="Search animals, devices..."
                       class="w-64 pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                <svg class="w-5 h-5 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>

            <!-- Quick Actions -->
            <div class="flex items-center space-x-2">

                <!-- Refresh Button -->
                <button onclick="location.reload()"
                        class="p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors"
                        title="Refresh Data">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                </button>

                <!-- Notifications Bell -->
                <a href="{{ route('notifications.index') }}"
                   class="relative p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                    </svg>
                    <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full animate-pulse"></span>
                </a>

                <!-- Full Screen Toggle -->
                <button onclick="toggleFullScreen()"
                        class="p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors hidden lg:block"
                        title="Toggle Fullscreen">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path>
                    </svg>
                </button>
            </div>

            <!-- User Menu Dropdown -->
            <!-- User Menu Dropdown -->
@guest
<div class="flex items-center space-x-3">
    <a href="{{ route('login') }}"
       class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition-colors">
        Login
    </a>
</div>
@else
<div class="relative">
    <button onclick="toggleUserMenu()"
            class="flex items-center space-x-3 px-3 py-2 hover:bg-gray-50 rounded-lg transition-colors">
        <div class="relative">
            <div class="w-9 h-9 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                <span class="text-sm font-bold text-white">{{ substr(Auth::user()->name, 0, 2) }}</span>
            </div>
            <span class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-green-500 border-2 border-white rounded-full"></span>
        </div>
        <div class="text-left hidden lg:block">
            <p class="text-sm font-semibold text-gray-900">{{ Auth::user()->name }}</p>
            <p class="text-xs text-gray-500">
                @php
                    $roleMap = [
                        1 => 'Administrator',
                        2 => 'Peneliti Ekologi',
                        3 => 'Konservasionis Lapangan',
                        4 => 'Pengambil Keputusan'
                    ];
                    echo $roleMap[Auth::user()->role_id] ?? 'User';
                @endphp
            </p>
        </div>
        <svg class="w-4 h-4 text-gray-600 hidden lg:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
        </svg>
    </button>

    <!-- Dropdown Menu -->
    <div id="userMenu" class="hidden absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-lg border border-gray-200 py-2 z-50">
        <div class="px-4 py-3 border-b border-gray-100">
            <p class="text-sm font-semibold text-gray-900">{{ Auth::user()->name }}</p>
            <p class="text-xs text-gray-500">{{ Auth::user()->email }}</p>
        </div>
        <a href="{{ route('dashboard.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
            </svg>
            Dashboard
        </a>
        <form method="POST" action="{{ route('logout') }}" class="w-full">
            @csrf
            <button type="submit" class="flex items-center px-4 py-2 text-sm text-red-600 hover:bg-red-50 w-full text-left transition-colors">
                <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                </svg>
                Logout
            </button>
        </form>
    </div>
    </div>
    @endguest
        </div>
    </div>
</header>

<script>
    // Toggle user menu dropdown
    function toggleUserMenu() {
        const menu = document.getElementById('userMenu');
        menu.classList.toggle('hidden');
    }

    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
        const userMenu = document.getElementById('userMenu');
        const userButton = event.target.closest('button');

        if (!userButton || !userButton.onclick.toString().includes('toggleUserMenu')) {
            userMenu.classList.add('hidden');
        }
    });

    // Toggle fullscreen
    function toggleFullScreen() {
        if (!document.fullscreenElement) {
            document.documentElement.requestFullscreen();
        } else {
            if (document.exitFullscreen) {
                document.exitFullscreen();
            }
        }
    }
</script>
