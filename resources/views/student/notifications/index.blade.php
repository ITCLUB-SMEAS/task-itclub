<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifikasi - IT Club</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50">
    <div class="min-h-screen">
        <!-- Header -->
        <div class="bg-white shadow-sm border-b">
            <div class="max-w-6xl mx-auto px-4 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <i class="fas fa-bell text-blue-600 text-2xl"></i>
                        <h1 class="text-2xl font-bold text-gray-800">Notifikasi</h1>
                        @if($unreadCount > 0)
                            <span class="bg-red-500 text-white text-xs px-2 py-1 rounded-full">{{ $unreadCount }}</span>
                        @endif
                    </div>
                    <div class="flex items-center space-x-4">
                        @if($unreadCount > 0)
                            <button onclick="markAllAsRead()" class="text-blue-600 hover:text-blue-800 font-medium">
                                <i class="fas fa-check-double mr-2"></i>Tandai Semua Dibaca
                            </button>
                        @endif
                        <a href="{{ route('student.dashboard') }}" class="text-gray-600 hover:text-gray-800">
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
                        <div class="bg-white rounded-lg shadow-sm border {{ $notification->is_read ? 'opacity-75' : 'border-l-4 border-l-blue-500' }} hover:shadow-md transition-shadow">
                            <div class="p-6">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-2 mb-2">
                                            <h3 class="font-semibold text-gray-800 {{ $notification->is_read ? '' : 'text-blue-800' }}">
                                                {{ $notification->title }}
                                            </h3>
                                            @if(!$notification->is_read)
                                                <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full">Baru</span>
                                            @endif
                                        </div>
                                        <p class="text-gray-600 mb-3">{{ $notification->message }}</p>
                                        <div class="flex items-center justify-between">
                                            <span class="text-sm text-gray-500">
                                                <i class="fas fa-clock mr-1"></i>
                                                {{ $notification->created_at->diffForHumans() }}
                                            </span>
                                            <div class="flex items-center space-x-2">
                                                @if($notification->type === 'assignment_created' && isset($notification->data['assignment_id']))
                                                    <a href="{{ route('assignments.show', $notification->data['assignment_id']) }}"
                                                       class="text-blue-600 hover:text-blue-800 font-medium text-sm"
                                                       onclick="markAsRead({{ $notification->id }})">
                                                        <i class="fas fa-external-link-alt mr-1"></i>Lihat Assignment
                                                    </a>
                                                @endif
                                                @if(!$notification->is_read)
                                                    <button onclick="markAsRead({{ $notification->id }})"
                                                            class="text-green-600 hover:text-green-800 text-sm">
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
                    <i class="fas fa-bell-slash text-gray-300 text-6xl mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-600 mb-2">Belum Ada Notifikasi</h3>
                    <p class="text-gray-500">Notifikasi akan muncul ketika ada assignment baru atau pengumuman penting.</p>
                    <a href="{{ route('student.dashboard') }}" class="inline-block mt-4 bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors">
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
            fetch('/notifications/mark-all-read', {
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
