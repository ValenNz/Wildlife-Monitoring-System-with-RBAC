<aside class="w-64 bg-gradient-to-b from-green-900 to-gray-800 text-white flex flex-col shadow-2xl">
    <!-- Logo & Brand -->
    <div class="p-6 border-b border-gray-700">
        <div class="flex items-center space-x-3">
            <div class="bg-gradient-to-br from-blue-500 to-green-600 rounded-xl p-2">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
            </div>
            <div>
                <h1 class="text-xl font-bold">Wildlife</h1>
                <p class="text-xs text-gray-400">Monitoring System</p>
            </div>
        </div>
    </div>

    <!-- Navigation Menu -->
    <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">

        <!-- Dashboard Section -->
        <div class="pb-2">
            <p class="px-4 text-xs text-gray-400 uppercase tracking-wider font-semibold">Main</p>
        </div>

        <!-- Dashboard -->
        <a href="{{ route('dashboard.index') }}"
           class="group flex items-center px-4 py-3 rounded-xl transition-all duration-200
                  {{ request()->routeIs('dashboard.*') ? 'bg-gradient-to-r from-blue-600 to-green-600 shadow-lg' : 'hover:bg-gray-700' }}">
            <svg class="w-5 h-5 mr-3 {{ request()->routeIs('dashboard.*') ? 'text-white' : 'text-gray-400 group-hover:text-white' }}"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
            </svg>
            <span class="font-medium">Dashboard</span>
        </a>

        <!-- Live Map -->
        @if(auth()->check() && auth()->user()->canAccess('view_map'))
        <a href="{{ route('map.index') }}"
           class="group flex items-center px-4 py-3 rounded-xl transition-all duration-200
                  {{ request()->routeIs('map.*') ? 'bg-gradient-to-r from-blue-600 to-green-600 shadow-lg' : 'hover:bg-gray-700' }}">
            <svg class="w-5 h-5 mr-3 {{ request()->routeIs('map.*') ? 'text-white' : 'text-gray-400 group-hover:text-white' }}"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
            </svg>
            <span class="font-medium">Live Map</span>
            <span class="ml-auto px-2 py-0.5 text-xs font-bold bg-green-500 text-white rounded-full animate-pulse">Live</span>
        </a>
        @endif

        <!-- Divider -->
        <div class="pt-4 pb-2">
            <p class="px-4 text-xs text-gray-400 uppercase tracking-wider font-semibold">Monitoring</p>
        </div>

        <!-- Devices -->
        @if(auth()->check() && auth()->user()->canAccess('manage_devices'))
        <a href="{{ route('devices.index') }}"
           class="group flex items-center px-4 py-3 rounded-xl transition-all duration-200
                  {{ request()->routeIs('devices.*') ? 'bg-gradient-to-r from-blue-600 to-green-600 shadow-lg' : 'hover:bg-gray-700' }}">
            <svg class="w-5 h-5 mr-3 {{ request()->routeIs('devices.*') ? 'text-white' : 'text-gray-400 group-hover:text-white' }}"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"></path>
            </svg>
            <span class="font-medium">Devices</span>
        </a>
        @endif

        <!-- Geozones -->
        @if(auth()->check() && auth()->user()->canAccess('manage_geozones'))
        <a href="{{ route('geozones.index') }}"
           class="group flex items-center px-4 py-3 rounded-xl transition-all duration-200
                  {{ request()->routeIs('geozones.*') ? 'bg-gradient-to-r from-blue-600 to-green-600 shadow-lg' : 'hover:bg-gray-700' }}">
            <svg class="w-5 h-5 mr-3 {{ request()->routeIs('geozones.*') ? 'text-white' : 'text-gray-400 group-hover:text-white' }}"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
            </svg>
            <span class="font-medium">Geozones</span>
        </a>
        @endif

        <!-- Historical Tracking -->
        @if(auth()->check() && auth()->user()->canAccess('view_map'))
        <a href="{{ route('historical-tracking.index') }}"
           class="group flex items-center px-4 py-3 rounded-xl transition-all duration-200
                  {{ request()->routeIs('historical-tracking.*') ? 'bg-gradient-to-r from-blue-600 to-green-600 shadow-lg' : 'hover:bg-gray-700' }}">
            <svg class="w-5 h-5 mr-3 {{ request()->routeIs('historical-tracking.*') ? 'text-white' : 'text-gray-400 group-hover:text-white' }}"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span class="font-medium">History</span>
        </a>
        @endif

        <!-- Weather -->
        @if(auth()->check() && auth()->user()->canAccess('view_weather'))
        <a href="{{ route('weather.index') }}"
           class="group flex items-center px-4 py-3 rounded-xl transition-all duration-200
                  {{ request()->routeIs('weather.*') ? 'bg-gradient-to-r from-blue-600 to-green-600 shadow-lg' : 'hover:bg-gray-700' }}">
            <svg class="w-5 h-5 mr-3 {{ request()->routeIs('weather.*') ? 'text-white' : 'text-gray-400 group-hover:text-white' }}"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"></path>
            </svg>
            <span class="font-medium">Weather</span>
        </a>
        @endif

        <!-- Divider -->
        <div class="pt-4 pb-2">
            <p class="px-4 text-xs text-gray-400 uppercase tracking-wider font-semibold">Reports & Alerts</p>
        </div>

        <!-- Notifications -->
        @if(auth()->check() && auth()->user()->canAccess('view_notifications'))
        <a href="{{ route('notifications.index') }}"
           class="group flex items-center px-4 py-3 rounded-xl transition-all duration-200 relative
                  {{ request()->routeIs('notifications.*') ? 'bg-gradient-to-r from-blue-600 to-green-600 shadow-lg' : 'hover:bg-gray-700' }}">
            <svg class="w-5 h-5 mr-3 {{ request()->routeIs('notifications.*') ? 'text-white' : 'text-gray-400 group-hover:text-white' }}"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
            </svg>
            <span class="font-medium">Notifications</span>
            <!-- Unread Badge -->
            <span id="sidebar-notification-badge" class="ml-auto hidden px-2 py-0.5 text-xs font-bold bg-red-500 text-white rounded-full">0</span>
        </a>
        @endif

        <!-- Incidents -->
        @if(auth()->check() && auth()->user()->canAccess('manage_incidents'))
        <a href="{{ route('incidents.index') }}"
           class="group flex items-center px-4 py-3 rounded-xl transition-all duration-200
                  {{ request()->routeIs('incidents.*') ? 'bg-gradient-to-r from-blue-600 to-green-600 shadow-lg' : 'hover:bg-gray-700' }}">
            <svg class="w-5 h-5 mr-3 {{ request()->routeIs('incidents.*') ? 'text-white' : 'text-gray-400 group-hover:text-white' }}"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
            <span class="font-medium">Incidents</span>
        </a>
        @endif

        <!-- Reports -->
        @if(auth()->check() && auth()->user()->canAccess('view_reports'))
        <a href="{{ route('reports.index') }}"
           class="group flex items-center px-4 py-3 rounded-xl transition-all duration-200
                  {{ request()->routeIs('reports.*') ? 'bg-gradient-to-r from-blue-600 to-green-600 shadow-lg' : 'hover:bg-gray-700' }}">
            <svg class="w-5 h-5 mr-3 {{ request()->routeIs('reports.*') ? 'text-white' : 'text-gray-400 group-hover:text-white' }}"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <span class="font-medium">Reports</span>
        </a>
        @endif

        <!-- Activity Logs -->
        @if(auth()->check() && auth()->user()->canAccess('view_activity_logs'))
        <a href="{{ route('activity-logs.index') }}"
           class="group flex items-center px-4 py-3 rounded-xl transition-all duration-200
                  {{ request()->routeIs('activity-logs.*') ? 'bg-gradient-to-r from-blue-600 to-green-600 shadow-lg' : 'hover:bg-gray-700' }}">
            <svg class="w-5 h-5 mr-3 {{ request()->routeIs('activity-logs.*') ? 'text-white' : 'text-gray-400 group-hover:text-white' }}"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <span class="font-medium">Activity Logs</span>
        </a>
        @endif

        <!-- Divider -->
        <div class="pt-4 pb-2">
            <p class="px-4 text-xs text-gray-400 uppercase tracking-wider font-semibold">System</p>
        </div>

        <!-- Users -->
        @if(auth()->check() && auth()->user()->canAccess('manage_users'))
        <a href="{{ route('users.index') }}"
           class="group flex items-center px-4 py-3 rounded-xl transition-all duration-200
                  {{ request()->routeIs('users.*') ? 'bg-gradient-to-r from-blue-600 to-green-600 shadow-lg' : 'hover:bg-gray-700' }}">
            <svg class="w-5 h-5 mr-3 {{ request()->routeIs('users.*') ? 'text-white' : 'text-gray-400 group-hover:text-white' }}"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
            </svg>
            <span class="font-medium">Users</span>
        </a>
        @endif

        <!-- Smart Integrations -->
        @if(auth()->check() && auth()->user()->canAccess('manage_smart'))
        <a href="{{ route('smart-integrations.index') }}"
           class="group flex items-center px-4 py-3 rounded-xl transition-all duration-200
                  {{ request()->routeIs('smart-integrations.*') ? 'bg-gradient-to-r from-blue-600 to-green-600 shadow-lg' : 'hover:bg-gray-700' }}">
            <svg class="w-5 h-5 mr-3 {{ request()->routeIs('smart-integrations.*') ? 'text-white' : 'text-gray-400 group-hover:text-white' }}"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            <span class="font-medium">Integrations</span>
        </a>
        @endif

        <!-- Backups -->
        @if(auth()->check() && auth()->user()->canAccess('manage_backups'))
        <a href="{{ route('backups.index') }}"
           class="group flex items-center px-4 py-3 rounded-xl transition-all duration-200
                  {{ request()->routeIs('backups.*') ? 'bg-gradient-to-r from-blue-600 to-green-600 shadow-lg' : 'hover:bg-gray-700' }}">
            <svg class="w-5 h-5 mr-3 {{ request()->routeIs('backups.*') ? 'text-white' : 'text-gray-400 group-hover:text-white' }}"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
            </svg>
            <span class="font-medium">Backups</span>
        </a>
        @endif

        <!-- Settings -->
        @if(auth()->check() && auth()->user()->canAccess('manage_backups'))
        <a href="#"
           class="group flex items-center px-4 py-3 rounded-xl transition-all duration-200 hover:bg-gray-700">
            <svg class="w-5 h-5 mr-3 text-gray-400 group-hover:text-white"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
            <span class="font-medium">Settings</span>
        </a>
        @endif
    </nav>

    <!-- User Profile -->
    @guest
    <div class="p-4 border-t border-gray-700 bg-gray-800">
        <div class="flex items-center space-x-3 px-3 py-2 rounded-xl hover:bg-gray-700 cursor-pointer transition-colors">
            <a href="{{ route('login') }}" class="text-white hover:text-blue-300">Login</a>
        </div>
    </div>
    @else
    <div class="p-4 border-t border-gray-700 bg-gray-800">
        <div class="flex items-center space-x-3 px-3 py-2 rounded-xl hover:bg-gray-700 cursor-pointer transition-colors">
            <div class="relative">
                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-green-600 rounded-full flex items-center justify-center">
                    <span class="text-sm font-bold text-white">{{ substr(Auth::user()->name, 0, 2) }}</span>
                </div>
                <span class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-gray-800 rounded-full"></span>
            </div>
            <div class="flex-1 min-w-0">
                <p class="font-semibold text-sm truncate">{{ Auth::user()->name }}</p>
                <p class="text-xs text-gray-400 truncate">{{ Auth::user()->email }}</p>
            </div>
            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </div>
    </div>
    @endguest
</aside>

<!-- JavaScript for Notification Badge -->
@push('scripts')
<script>
// Update notification badge in sidebar
function updateNotificationBadge() {
    fetch('/notifications/api/unread-count')
        .then(res => res.json())
        .then(data => {
            const badge = document.getElementById('sidebar-notification-badge');
            if (badge) {
                if (data.count > 0) {
                    badge.textContent = data.count;
                    badge.classList.remove('hidden');
                } else {
                    badge.classList.add('hidden');
                }
            }
        })
        .catch(err => console.log('Failed to update notification badge'));
}

// Update on page load
document.addEventListener('DOMContentLoaded', updateNotificationBadge);

// Update every 30 seconds
setInterval(updateNotificationBadge, 30000);
</script>
@endpush
