<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dasbor Admin - Pengumpulan Tugas</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .modal-hidden { display: none; }
    </style>
</head>
<body class="bg-gray-100">

    <!-- Header -->
    <header class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8 flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-900">Dasbor Admin</h1>
            <div class="flex items-center space-x-4">
                <span class="text-sm text-gray-600">Selamat datang, {{ Auth::user()->name }}!</span>
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="text-sm font-medium text-blue-600 hover:text-blue-500">Logout</button>
                </form>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <!-- Bagian Siswa Paling Rajin -->
        <div class="mb-8">
             <div class="bg-white p-6 rounded-lg shadow flex items-center space-x-6 bg-gradient-to-r from-blue-500 to-indigo-600 text-white">
                <!-- Ikon Piala -->
                <div class="flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold">Siswa Paling Rajin</h3>
                    <p class="mt-1 text-2xl font-bold">{{ $topStudent->name }}</p>
                    <p class="text-sm opacity-80">
                        @if($topStudent->approved_tasks_count > 0)
                            Dengan {{ $topStudent->approved_tasks_count }} tugas telah disetujui!
                        @else
                            Belum ada tugas yang disetujui
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <!-- Ringkasan Statistik -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-medium text-gray-700">Perlu Direview</h3>
                <p class="mt-2 text-3xl font-bold text-gray-900">{{ $pendingTasks }}</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-medium text-gray-700">Disetujui</h3>
                <p class="mt-2 text-3xl font-bold text-gray-900">{{ $approvedTasks }}</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-medium text-gray-700">Perlu Revisi</h3>
                <p class="mt-2 text-3xl font-bold text-gray-900">{{ $rejectedTasks }}</p>
            </div>
        </div>

        <!-- Tabel Pengumpulan Tugas -->
        <div class="bg-white shadow rounded-lg overflow-x-auto">
            <div class="flex justify-between items-center p-6">
                <h2 class="text-xl font-bold text-gray-800">Tugas Masuk</h2>
                <a href="{{ route('admin.tasks') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                    Lihat Semua →
                </a>
            </div>
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Siswa</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kelas</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deskripsi</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">GitHub</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($recentTasks as $task)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $task->nama_lengkap }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $task->kelas }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 max-w-xs">
                                <div class="truncate" title="{{ $task->deskripsi_tugas ?? 'Tidak ada deskripsi' }}">
                                    {{ Str::limit($task->deskripsi_tugas ?? 'Tidak ada deskripsi', 50) }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <a href="{{ $task->github_link }}" target="_blank" class="text-blue-600 hover:underline">
                                    Lihat Repositori
                                </a>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $task->tanggal_mengumpulkan->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    @if($task->status == 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($task->status == 'approved') bg-green-100 text-green-800
                                    @else bg-red-100 text-red-800
                                    @endif">
                                    @if($task->status == 'pending')
                                        Perlu Direview
                                    @elseif($task->status == 'approved')
                                        Disetujui
                                    @else
                                        Perlu Revisi
                                    @endif
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                @if($task->status == 'pending')
                                    <form action="{{ route('admin.tasks.status', $task) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="approved">
                                        <button type="submit" class="text-green-600 hover:text-green-900">Approve</button>
                                    </form>
                                    <button onclick="openRevisionModal('{{ $task->nama_lengkap }}', {{ $task->id }})" class="text-red-600 hover:text-red-900">Revisi</button>
                                @elseif($task->status == 'rejected')
                                    <form action="{{ route('admin.tasks.status', $task) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="approved">
                                        <button type="submit" class="text-green-600 hover:text-green-900">Approve</button>
                                    </form>
                                    <button onclick="openRevisionModal('{{ $task->nama_lengkap }}', {{ $task->id }})" class="text-blue-600 hover:text-blue-900">Edit Revisi</button>
                                @else
                                    <span class="text-gray-400">Selesai</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                <div class="text-4xl mb-4">📝</div>
                                <p>Belum ada tugas yang dikumpulkan</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </main>

    <!-- Modal untuk Revisi -->
    <div id="revisionModal" class="fixed z-10 inset-0 overflow-y-auto modal-hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form id="revisionForm" method="POST">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="rejected">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                            Alasan Revisi untuk <span id="studentName" class="font-bold"></span>
                        </h3>
                        <div class="mt-4">
                            <textarea name="catatan_admin" id="revisionReason" rows="4" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border border-gray-300 rounded-md p-2" placeholder="Contoh: Ada error saat menjalankan project, tolong perbaiki bagian controllernya." required></textarea>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                            Kirim Revisi
                        </button>
                        <button type="button" onclick="closeRevisionModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const modal = document.getElementById('revisionModal');
        const studentNameEl = document.getElementById('studentName');
        const revisionForm = document.getElementById('revisionForm');
        const revisionReason = document.getElementById('revisionReason');

        function openRevisionModal(studentName, taskId) {
            studentNameEl.textContent = studentName;
            revisionForm.action = `/admin/tasks/${taskId}/status`;
            revisionReason.value = '';
            modal.classList.remove('modal-hidden');
        }

        function closeRevisionModal() {
            modal.classList.add('modal-hidden');
        }

        // Close modal when clicking outside
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeRevisionModal();
            }
        });
    </script>

</body>
</html>

