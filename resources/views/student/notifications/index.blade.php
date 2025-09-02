<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifikasi - IT Club</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="{{ asset('js/darkmode.js') }}"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        /* Dark mode styles */
        .dark body { background-color: #1a1a1a; color: #e1e1e1; }
        .dark .bg-white { background-color: #2a2a2a; }
        .dark .bg-gray-50 { background-color: #121212; }
        .dark .text-gray-800 { color: #e1e1e1; }
        .dark .text-gray-600 { color: #9ca3af; }
        .dark .shadow-sm { box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.5); }
        .dark .border-b { border-color: #333; }
    </style>
</head>
<body class="bg-gray-50 dark:bg-gray-900">
    <div class="min-h-screen">
        <!-- Header -->
        <div class="bg-white dark:bg-gray-800 shadow-sm border-b dark:border-gray-700">
            <div class="max-w-6xl mx-auto px-4 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <i class="fas fa-bell text-blue-600 dark:text-blue-400 text-2xl"></i>
                        <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Notifikasi</h1>
                        @if($unreadCount > 0)
                            <span class="bg-red-500 text-white text-xs px-2 py-1 rounded-full">{{ $unreadCount }}</span>
                        @endif
                    </div>
                    <div class="flex items-center space-x-4">
                        <button onclick="toggleDarkMode()" class="p-2 rounded-full hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 hidden dark:block" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd" />
                            </svg>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 block dark:hidden" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z" />
                            </svg>
                        </button>
                        @if($unreadCount > 0)
                            <button onclick="markAllAsRead()" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 font-medium">
                                <i class="fas fa-check-double mr-2"></i>Tandai Semua Dibaca
                            </button>
                        @endif
                        <a href="{{ route('student.dashboard') }}" class="text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white">
                            <i class="fas fa-arrow-left mr-2"></i>Kembali ke Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="max-w-4xl mx-auto px-4 py-8">
            @if($notifications->count() > 0)
                <div class="space-y-4">
                    @foreach($notifications as $notification)
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border dark:border-gray-700 {{ $notification->is_read ? 'opacity-75' : 'border-l-4 border-l-blue-500' }} hover:shadow-md transition-shadow">
                            <div class="p-6">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-2 mb-2">
                                            <h3 class="font-semibold text-gray-800 dark:text-gray-200 {{ $notification->is_read ? '' : 'text-blue-800 dark:text-blue-400' }}">
                                                {{ $notification->title }}
                                            </h3>
                                            @if(!$notification->is_read)
                                                <span class="bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 text-xs px-2 py-1 rounded-full">Baru</span>
                                            @endif
                                        </div>
                                        <p class="text-gray-600 dark:text-gray-400 mb-3">{{ $notification->message }}</p>
                                        <div class="flex items-center justify-between">
                                            <span class="text-sm text-gray-500 dark:text-gray-500">
                                                <i class="fas fa-clock mr-1"></i>
                                                {{ $notification->created_at->diffForHumans() }}
                                            </span>
                                            <div class="flex items-center space-x-2">
                                                @if($notification->type === 'assignment_created' && isset($notification->data['assignment_id']))
                                                    <a href="{{ route('assignments.show', $notification->data['assignment_id']) }}"
                                                       class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 font-medium text-sm"
                                                       onclick="markAsRead({{ $notification->id }})">
                                                        <i class="fas fa-external-link-alt mr-1"></i>Lihat Assignment
                                                    </a>
                                                @endif
                                                @if(!$notification->is_read)
                                                    <button onclick="markAsRead({{ $notification->id }})"
                                                            class="text-green-600 dark:text-green-400 hover:text-green-800 dark:hover:text-green-300 text-sm">
                                                        <i class="fas fa-check mr-1"></i>Tandai Dibaca
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-8">
                    {{ $notifications->links() }}
                </div>
            @else
                <!-- Empty State -->
                <div class="text-center py-12">
                    <i class="fas fa-bell-slash text-gray-300 dark:text-gray-700 text-6xl mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-600 dark:text-gray-400 mb-2">Belum Ada Notifikasi</h3>
                    <p class="text-gray-500 dark:text-gray-500">Notifikasi akan muncul ketika ada assignment baru atau pengumuman penting.</p>
                    <a href="{{ route('student.dashboard') }}" class="inline-block mt-4 bg-blue-600 dark:bg-blue-700 text-white px-6 py-3 rounded-lg hover:bg-blue-700 dark:hover:bg-blue-800 transition-colors">
                        <i class="fas fa-home mr-2"></i>Kembali ke Dashboard
                    </a>
                </div>
            @endif
        </div>
    </div>

    <script>
        function markAsRead(id) {
            fetch(`/notifications/${id}/read`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            });
        }

        function markAllAsRead() {
            fetch('/notifications/mark-all-as-read', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            });
        }
    </script>
</body>
</html>
