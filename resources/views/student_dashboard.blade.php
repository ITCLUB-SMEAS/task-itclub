<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dasbor Siswa - Pengumpulan Tugas</title>
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
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Dasbor Siswa</h1>
                <p class="text-sm text-gray-500">Selamat datang, {{ Auth::user()->name }}!</p>
                @if(Auth::user()->kelas)
                    <p class="text-xs text-gray-400">Kelas: {{ Auth::user()->kelas }}</p>
                @endif
            </div>
            <form action="{{ route('logout') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="text-sm font-medium text-blue-600 hover:text-blue-500">Logout</button>
            </form>
        </div>
    </header>

    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <!-- Ringkasan Statistik -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-medium text-gray-700">Tugas Belum Terkumpul</h3>
                <p class="mt-2 text-3xl font-bold text-gray-900">{{ $pendingTasks }}</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-medium text-gray-700">Tugas Disetujui</h3>
                <p class="mt-2 text-3xl font-bold text-gray-900">{{ $approvedTasks }}</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-medium text-gray-700">Tugas Perlu Revisi</h3>
                <p class="mt-2 text-3xl font-bold text-gray-900">{{ $rejectedTasks }}</p>
            </div>
        </div>

        <!-- Tombol Aksi Cepat -->
        <div class="mb-8 flex space-x-4">
            <a href="{{ route('tasks.create') }}" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition duration-300">
                📝 Kumpulkan Tugas Baru
            </a>
            <a href="{{ route('tasks.my') }}" class="bg-gray-600 text-white px-6 py-3 rounded-lg hover:bg-gray-700 transition duration-300">
                📋 Lihat Riwayat Tugas
            </a>
        </div>

        <!-- Daftar Tugas -->
        <div class="bg-white shadow rounded-lg p-6">
             <h2 class="text-xl font-bold text-gray-800 mb-6">Riwayat Pengumpulan Tugas</h2>
             <div class="space-y-6">

                @forelse($recentTasks as $task)
                    <div class="border {{ $task->status == 'rejected' ? 'border-red-200 bg-red-50' : 'border-gray-200 bg-white' }} p-4 rounded-lg">
                        <div class="flex flex-col sm:flex-row justify-between sm:items-start">
                            <div class="flex-1">
                                <p class="font-semibold text-gray-800">
                                    <a href="{{ $task->github_link }}" target="_blank" class="text-blue-600 hover:underline">
                                        📂 Lihat Repositori GitHub
                                    </a>
                                </p>
                                @if($task->deskripsi_tugas)
                                    <div class="mt-2 text-sm text-gray-600">
                                        <strong>Deskripsi:</strong>
                                        <p class="mt-1">{{ $task->deskripsi_tugas }}</p>
                                    </div>
                                @endif
                                <div class="mt-2 text-sm text-gray-500">
                                    <p>📅 Dikumpulkan pada: {{ $task->tanggal_mengumpulkan->format('d F Y') }}</p>
                                    <p>🎓 Kelas: {{ $task->kelas }}</p>
                                </div>
                            </div>
                            <span class="mt-2 sm:mt-0 px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
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

                        @if($task->status == 'rejected' && $task->catatan_admin)
                            <div class="mt-4 pt-4 border-t border-red-200">
                                <p class="text-sm font-medium text-gray-800">Catatan dari Admin:</p>
                                <p class="text-sm text-gray-600 mt-1">"{{ $task->catatan_admin }}"</p>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="text-center py-8">
                        <div class="text-gray-400 text-6xl mb-4">📝</div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada tugas</h3>
                        <p class="text-gray-500 mb-4">Anda belum mengumpulkan tugas apapun.</p>
                        <a href="{{ route('tasks.create') }}" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition duration-300 inline-block">
                            📝 Kumpulkan Tugas Pertama
                        </a>
                    </div>
                @endforelse

             </div>
        </div>
    </main>

</body>
</html>
