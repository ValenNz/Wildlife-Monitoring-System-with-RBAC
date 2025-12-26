<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Wildlife Monitoring Dashboard')</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @stack('styles')

    <style>
        [x-cloak] { display: none !important; }
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: #888; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #555; }
    </style>
</head>
<body class="bg-gray-50 antialiased">
    @auth
        <div class="flex h-screen overflow-hidden">
            <!-- Sidebar -->
            @include('partials.sidebar')

            <!-- Main Content -->
            <div class="flex-1 flex flex-col overflow-hidden">
                <!-- Header -->
                @include('partials.header')

                <!-- Page Content -->
                <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-6">
                    @yield('content')
                </main>

                <!-- Footer -->
                @include('partials.footer')
            </div>
        </div>
    @endauth

    @guest
        <div class="min-h-screen flex items-center justify-center bg-gray-100">
            @yield('content')
        </div>
    @endguest

    @stack('scripts')
</body>
</html>

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
