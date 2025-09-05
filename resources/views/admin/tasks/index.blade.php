@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center space-x-4">
                <a href="{{ route('admin.dashboard') }}" class="text-blue-600 hover:text-blue-800">
                    ← Kembali ke Dashboard
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Kelola Tugas</h1>
                    <p class="text-sm text-gray-500">Kelola semua tugas yang dikumpulkan siswa</p>
                </div>
            </div>
            <form action="{{ route('logout') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="text-sm font-medium text-blue-600 hover:text-blue-500">Logout</button>
            </form>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

    <!-- Statistik Ringkas -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
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

    <!-- Filter & Search -->
    <div class="bg-white p-4 rounded-lg shadow mb-6">
            <form action="{{ route('admin.tasks') }}" method="GET" class="grid grid-cols-1 md:grid-cols-12 gap-4">
                <div class="md:col-span-4 min-w-0">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Cari Apa?</label>
                    <input type="text" name="search" id="search" placeholder="Nama atau Email Siswa"
                        value="{{ request('search') }}"
                        class="h-10 min-w-0 shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                </div>

                <div class="md:col-span-3 min-w-0">
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" id="status" class="h-10 min-w-0 shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                        <option value="">Semua Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Menunggu Review</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Disetujui</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Perlu Revisi</option>
                    </select>
                </div>

                <div class="md:col-span-2 min-w-0">
                    <label for="kelas" class="block text-sm font-medium text-gray-700 mb-1">Kelas</label>
                    <select name="kelas" id="kelas" class="h-10 min-w-0 shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                        <option value="">Semua Kelas</option>
                        @foreach($kelasOptions as $kelas)
                            <option value="{{ $kelas }}" {{ request('kelas') == $kelas ? 'selected' : '' }}>{{ $kelas }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-3 flex flex-wrap items-end gap-2 md:justify-end">
                    <button type="submit" class="h-10 inline-flex items-center px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Filter
                    </button>
                    <a href="{{ route('admin.tasks') }}" class="h-10 inline-flex items-center px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Reset
                    </a>
                    <a href="{{ route('admin.export.tasks') }}?{{ http_build_query(request()->all()) }}" class="h-10 inline-flex items-center px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Export
                    </a>
                </div>
            </form>
        </div>

        <!-- Tabel Semua Tugas -->
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-bold text-gray-800">Semua Tugas</h2>
            </div>

            <div class="overflow-x-auto max-h-[70vh] overflow-y-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50 sticky top-0 z-10">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Siswa</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kelas</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">GitHub</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nilai</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($tasks as $task)
                            <tr class="odd:bg-white even:bg-gray-50 {{ $task->status == 'rejected' ? 'bg-red-50' : '' }}">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $task->nama_lengkap }}</div>
                                    <div class="text-sm text-gray-500">{{ $task->email }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $task->kelas }}
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
                                            Perlu Review
                                        @elseif($task->status == 'approved')
                                            Disetujui
                                        @else
                                            Perlu Revisi
                                        @endif
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    @if($task->nilai !== null)
                                        <span class="font-medium {{ $task->nilai >= 70 ? 'text-green-600' : 'text-red-600' }}">
                                            {{ $task->nilai }}/100
                                        </span>
                                    @else
                                        <form action="{{ route('admin.tasks.grade', $task) }}" method="POST" class="flex items-center space-x-2">
                                            @csrf
                                            @method('PATCH')
                                            <input type="number" name="nilai" min="0" max="100"
                                                class="w-16 px-2 py-1 border border-gray-300 rounded text-sm"
                                                placeholder="0-100" required>
                                            <button type="submit" class="bg-blue-100 text-blue-700 text-xs px-2 py-1 rounded">
                                                Set
                                            </button>
                                        </form>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        @if($task->status != 'approved')
                                            <form action="{{ route('admin.tasks.status', $task) }}" method="POST" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="approved">
                                                <button type="submit" class="text-green-600 hover:text-green-900">
                                                    Approve
                                                </button>
                                            </form>
                                        @endif

                                        @if($task->status != 'rejected')
                                            <button onclick="openRevisionModal(event, '{{ $task->nama_lengkap }}', {{ $task->id }})" class="text-red-600 hover:text-red-900">
                                                Revisi
                                            </button>
                                        @else
                                            <button onclick="openRevisionModal(event, '{{ $task->nama_lengkap }}', {{ $task->id }})" class="text-blue-600 hover:text-blue-900">
                                                Edit Revisi
                                            </button>
                                        @endif
                                    </div>

                                    @if($task->status == 'rejected' && $task->catatan_admin)
                                        <div class="mt-2 text-xs text-red-600 revision-reason">
                                            "{{ $task->catatan_admin }}"
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                    <div class="text-4xl mb-4">📝</div>
                                    <p>Belum ada tugas yang dikumpulkan</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
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
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
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

        function openRevisionModal(evt, studentName, taskId) {
            studentNameEl.textContent = studentName;
            revisionForm.action = `/admin/tasks/${taskId}/status`;

            // If editing existing revision, get current reason
            const currentReason = evt && evt.target ? evt.target.closest('tr').querySelector('.revision-reason') : null;
            if (currentReason) {
                revisionReason.value = currentReason.textContent.replace(/"/g, '');
            } else {
                revisionReason.value = '';
            }

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
    </div>
@endsection
