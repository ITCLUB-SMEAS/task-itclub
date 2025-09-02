<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Tugas - Pengumpulan Tugas</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-100">

    <!-- Header -->
    <header class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8 flex justify-between items-center">
            <div class="flex items-center space-x-4">
                <a href="{{ route('student.dashboard') }}" class="text-blue-600 hover:text-blue-800">
                    ← Kembali ke Dashboard
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Riwayat Tugas</h1>
                    <p class="text-sm text-gray-500">{{ Auth::user()->name }}</p>
                </div>
            </div>
            <form action="{{ route('logout') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="text-sm font-medium text-blue-600 hover:text-blue-500">Logout</button>
            </form>
        </div>
    </header>

    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">

        <!-- Statistik Ringkas -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-sm font-medium text-gray-700">Total Tugas</h3>
                <p class="mt-2 text-2xl font-bold text-gray-900">{{ $tasks->count() }}</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-sm font-medium text-gray-700">Menunggu Review</h3>
                <p class="mt-2 text-2xl font-bold text-yellow-600">{{ $tasks->where('status', 'pending')->count() }}</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-sm font-medium text-gray-700">Disetujui</h3>
                <p class="mt-2 text-2xl font-bold text-green-600">{{ $tasks->where('status', 'approved')->count() }}</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-sm font-medium text-gray-700">Perlu Revisi</h3>
                <p class="mt-2 text-2xl font-bold text-red-600">{{ $tasks->where('status', 'rejected')->count() }}</p>
            </div>
        </div>

        <!-- Tombol Kumpul Tugas Baru -->
        <div class="mb-6">
            <a href="{{ route('tasks.create') }}" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition duration-300 inline-flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Kumpulkan Tugas Baru
            </a>
        </div>

        <!-- Daftar Semua Tugas -->
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-bold text-gray-800">Semua Tugas Anda</h2>
            </div>

            @forelse($tasks as $task)
                <div class="border-b border-gray-200 p-6 {{ $task->status == 'rejected' ? 'bg-red-50' : '' }}">
                    <div class="flex flex-col lg:flex-row justify-between lg:items-start space-y-4 lg:space-y-0">
                        <div class="flex-1">
                            <div class="flex items-start justify-between">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">
                                        <a href="{{ $task->github_link }}" target="_blank" class="text-blue-600 hover:underline">
                                            Repositori GitHub
                                        </a>
                                    </h3>
                                    <p class="text-sm text-gray-600 mt-1">{{ $task->github_link }}</p>
                                </div>
                                <span class="px-3 py-1 text-xs leading-5 font-semibold rounded-full
                                    @if($task->status == 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($task->status == 'approved') bg-green-100 text-green-800
                                    @else bg-red-100 text-red-800
                                    @endif">
                                    @if($task->status == 'pending')
                                        Menunggu Review
                                    @elseif($task->status == 'approved')
                                        Disetujui
                                    @else
                                        Perlu Revisi
                                    @endif
                                </span>
                            </div>

                            <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-600">
                                <div>
                                    <span class="font-medium">Nama:</span> {{ $task->nama_lengkap }}
                                </div>
                                <div>
                                    <span class="font-medium">Kelas:</span> {{ $task->kelas }}
                                </div>
                                <div>
                                    <span class="font-medium">Email:</span> {{ $task->email }}
                                </div>
                                <div>
                                    <span class="font-medium">Tanggal:</span> {{ $task->tanggal_mengumpulkan->format('d F Y') }}
                                </div>
                                @if($task->status == 'approved' && $task->nilai !== null)
                                <div class="md:col-span-2">
                                    <span class="font-medium">Nilai:</span>
                                    <span class="font-bold {{ $task->nilai >= 70 ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $task->nilai }}/100
                                    </span>
                                </div>
                                @endif
                            </div>

                            @if($task->status == 'rejected' && $task->catatan_admin)
                                <div class="mt-4 p-4 bg-red-100 border border-red-200 rounded-lg">
                                    <h4 class="font-medium text-red-800 mb-2">Catatan dari Admin:</h4>
                                    <p class="text-red-700 text-sm">{{ $task->catatan_admin }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-12">
                    <div class="text-gray-400 text-6xl mb-4">📝</div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada tugas</h3>
                    <p class="text-gray-500 mb-6">Anda belum mengumpulkan tugas apapun.</p>
                    <a href="{{ route('tasks.create') }}" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition duration-300 inline-block">
                        📝 Kumpulkan Tugas Pertama
                    </a>
                </div>
            @endforelse
        </div>
    </main>

</body>
</html>
