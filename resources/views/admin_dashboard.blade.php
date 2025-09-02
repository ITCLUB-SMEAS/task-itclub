<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="user-id" content="{{ Auth::id() }}">
    <title>Dasbor Admin - Pengumpulan Tugas</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="{{ asset('build/assets/app-BXl0pDBb.css') }}" rel="stylesheet">
    <script src="{{ asset('build/assets/app-DjIouP1n.js') }}" defer></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .modal-hidden { display: none; }
    </style>
</head>
<body class="bg-gray-100 transition-colors duration-200">

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
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-medium text-gray-700">Perlu Direview</h3>
                <p class="mt-2 text-3xl font-bold text-gray-900">{{ $pendingTasks }}</p>
                <div class="mt-2 text-sm text-gray-500">{{ $totalTasks > 0 ? round(($pendingTasks / $totalTasks) * 100) : 0 }}% dari total tugas</div>
            </div>
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-medium text-gray-700">Disetujui</h3>
                <p class="mt-2 text-3xl font-bold text-gray-900">{{ $approvedTasks }}</p>
                <div class="mt-2 text-sm text-gray-500">{{ $totalTasks > 0 ? round(($approvedTasks / $totalTasks) * 100) : 0 }}% dari total tugas</div>
            </div>
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-medium text-gray-700">Perlu Revisi</h3>
                <p class="mt-2 text-3xl font-bold text-gray-900">{{ $rejectedTasks }}</p>
                <div class="mt-2 text-sm text-gray-500">{{ $totalTasks > 0 ? round(($rejectedTasks / $totalTasks) * 100) : 0 }}% dari total tugas</div>
            </div>
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-medium text-gray-700">Deadline Mendekat</h3>
                <p class="mt-2 text-3xl font-bold text-gray-900">{{ $upcomingDeadlines }}</p>
                <div class="mt-2 text-sm text-gray-500">Dalam 3 hari ke depan</div>
            </div>
        </div>

        <!-- Grafik Statistik -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <!-- Grafik Status Pengumpulan -->
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-medium text-gray-700 mb-4">Status Pengumpulan</h3>
                <div class="h-64">
                    <canvas id="submissionStatusChart"></canvas>
                </div>
            </div>

            <!-- Grafik Trend Pengumpulan -->
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-medium text-gray-700 mb-4">Trend Pengumpulan (7 Hari Terakhir)</h3>
                <div class="h-64">
                    <canvas id="submissionTrendChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Grafik Statistik Baris 2 -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <!-- Grafik Per Kelas -->
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-medium text-gray-700 mb-4">Performa Per Kelas</h3>
                <div class="h-64">
                    <canvas id="classPerformanceChart"></canvas>
                </div>
            </div>

            <!-- Grafik On-time vs Late -->
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-medium text-gray-700 mb-4">Ketepatan Waktu Pengumpulan</h3>
                <div class="h-64">
                    <canvas id="timelinessChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Team Management -->
        <div class="mb-8">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Manajemen Tim</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <a href="{{ route('admin.teams.index') }}"
                   class="bg-indigo-600 hover:bg-indigo-700 text-white p-4 rounded-lg shadow transition duration-300 flex items-center">
                    <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <div>
                        <div class="font-medium">Kelola Tim</div>
                        <div class="text-sm opacity-90">Lihat semua tim</div>
                    </div>
                </a>

                <a href="{{ route('admin.teams.create') }}"
                   class="bg-teal-600 hover:bg-teal-700 text-white p-4 rounded-lg shadow transition duration-300 flex items-center">
                    <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                    </svg>
                    <div>
                        <div class="font-medium">Buat Tim Baru</div>
                        <div class="text-sm opacity-90">Tambah tim baru</div>
                    </div>
                </a>

                <a href="{{ route('admin.assignments.index') }}?type=team"
                   class="bg-pink-600 hover:bg-pink-700 text-white p-4 rounded-lg shadow transition duration-300 flex items-center">
                    <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    <div>
                        <div class="font-medium">Tugas Tim</div>
                        <div class="text-sm opacity-90">Kelola tugas untuk tim</div>
                    </div>
                </a>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="mb-8">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Manajemen Assignment</h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <a href="{{ route('admin.assignments.index') }}"
                   class="bg-blue-600 hover:bg-blue-700 text-white p-4 rounded-lg shadow transition duration-300 flex items-center">
                    <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <div>
                        <div class="font-medium">Kelola Assignment</div>
                        <div class="text-sm opacity-90">Lihat semua assignment</div>
                    </div>
                </a>

                <a href="{{ route('admin.assignments.create') }}"
                   class="bg-green-600 hover:bg-green-700 text-white p-4 rounded-lg shadow transition duration-300 flex items-center">
                    <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <div>
                        <div class="font-medium">Buat Assignment</div>
                        <div class="text-sm opacity-90">Tambah tugas baru</div>
                    </div>
                </a>

                <a href="{{ route('admin.assignments.index') }}"
                   class="bg-purple-600 hover:bg-purple-700 text-white p-4 rounded-lg shadow transition duration-300 flex items-center">
                    <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                    <div>
                        <div class="font-medium">Assignment Aktif</div>
                        <div class="text-sm opacity-90">Monitor assignment</div>
                    </div>
                </a>                <div class="bg-orange-600 text-white p-4 rounded-lg shadow flex items-center">
                    <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <div class="font-medium">Assignment Deadline</div>
                        <div class="text-sm opacity-90">{{ $urgentAssignments ?? 0 }} mendesak</div>
                    </div>
                </div>
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

        // Chart.js Initialization
        document.addEventListener('DOMContentLoaded', function() {
            // Status Pengumpulan Chart
            const statusCtx = document.getElementById('submissionStatusChart').getContext('2d');
            new Chart(statusCtx, {
                type: 'pie',
                data: {
                    labels: ['Perlu Review', 'Disetujui', 'Perlu Revisi'],
                    datasets: [{
                        data: [{{ $pendingTasks }}, {{ $approvedTasks }}, {{ $rejectedTasks }}],
                        backgroundColor: ['#FBBF24', '#10B981', '#EF4444'],
                        hoverBackgroundColor: ['#F59E0B', '#059669', '#DC2626']
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });

            // Trend Pengumpulan Chart
            const trendCtx = document.getElementById('submissionTrendChart').getContext('2d');
            new Chart(trendCtx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($dateLabels) !!},
                    datasets: [{
                        label: 'Jumlah Tugas',
                        data: {!! json_encode($submissionCounts) !!},
                        borderColor: '#6366F1',
                        backgroundColor: 'rgba(99, 102, 241, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        }
                    }
                }
            });

            // Class Performance Chart
            const classData = {!! json_encode($tasksByClass) !!};
            const classLabels = classData.map(item => item.kelas || 'Tidak ada kelas');
            const approvedByClass = classData.map(item => item.approved_count);
            const pendingByClass = classData.map(item => item.pending_count);
            const rejectedByClass = classData.map(item => item.rejected_count);

            const classCtx = document.getElementById('classPerformanceChart').getContext('2d');
            new Chart(classCtx, {
                type: 'bar',
                data: {
                    labels: classLabels,
                    datasets: [
                        {
                            label: 'Disetujui',
                            data: approvedByClass,
                            backgroundColor: '#10B981'
                        },
                        {
                            label: 'Perlu Review',
                            data: pendingByClass,
                            backgroundColor: '#FBBF24'
                        },
                        {
                            label: 'Perlu Revisi',
                            data: rejectedByClass,
                            backgroundColor: '#EF4444'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    },
                    scales: {
                        x: {
                            stacked: true
                        },
                        y: {
                            stacked: true,
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        }
                    }
                }
            });

            // Timeliness Chart
            const timelinessCtx = document.getElementById('timelinessChart').getContext('2d');
            new Chart(timelinessCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Tepat Waktu', 'Terlambat'],
                    datasets: [{
                        data: [{{ $onTimeSubmissions }}, {{ $lateSubmissions }}],
                        backgroundColor: ['#10B981', '#EF4444'],
                        hoverBackgroundColor: ['#059669', '#DC2626']
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        });
    </script>

</body>
</html>

