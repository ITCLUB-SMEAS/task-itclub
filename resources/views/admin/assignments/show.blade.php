<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Assignment - {{ $assignment->title }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
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
                <h1 class="text-2xl font-bold text-gray-900">Detail Assignment</h1>
                <p class="text-sm text-gray-500">{{ $assignment->title }}</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('admin.assignments.index') }}" class="text-sm font-medium text-gray-600 hover:text-gray-800">
                    ← Kembali ke Daftar
                </a>
                <a href="{{ route('admin.assignments.edit', $assignment) }}" class="text-sm font-medium text-blue-600 hover:text-blue-500">
                    Edit Assignment
                </a>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">

        @if(session('success'))
            <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Assignment Details -->
            <div class="lg:col-span-2 space-y-6">

                <!-- Basic Info Card -->
                <div class="bg-white shadow-sm rounded-lg p-6">
                    <div class="flex justify-between items-start mb-4">
                        <h2 class="text-xl font-semibold text-gray-900">{{ $assignment->title }}</h2>
                        <div class="flex space-x-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $assignment->getCategoryColor() }}">
                                {{ ucfirst($assignment->category) }}
                            </span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $assignment->getDifficultyColor() }}">
                                {{ ucfirst($assignment->difficulty) }}
                            </span>
                        </div>
                    </div>

                    <div class="prose max-w-none">
                        <p class="text-gray-700 leading-relaxed">{{ $assignment->description }}</p>
                    </div>
                </div>

                <!-- Requirements Card -->
                @php
                    $requirementsArray = !empty($assignment->requirements) && is_array($assignment->requirements) ? $assignment->requirements : [];
                @endphp
                @if(count($requirementsArray) > 0)
                <div class="bg-white shadow-sm rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Requirements</h3>
                    <ul class="space-y-2">
                        @foreach($requirementsArray as $requirement)
                            <li class="flex items-start">
                                <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="text-gray-700">{{ $requirement }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <!-- Submissions List -->
                <div class="bg-white shadow-sm rounded-lg p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Submissions ({{ $assignment->submissions->count() }})</h3>
                        <div class="text-sm text-gray-500">
                            {{ $assignment->submissions->where('is_late', false)->count() }} tepat waktu,
                            {{ $assignment->submissions->where('is_late', true)->count() }} terlambat
                        </div>
                    </div>

                    @if($assignment->submissions->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Siswa</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Kelas</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Submitted</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($assignment->submissions as $submission)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-3">
                                                <div class="text-sm font-medium text-gray-900">{{ $submission->user->name }}</div>
                                                <div class="text-xs text-gray-500">{{ $submission->user->email }}</div>
                                            </td>
                                            <td class="px-4 py-3 text-sm text-gray-700">
                                                {{ $submission->user->kelas ?? '-' }}
                                            </td>
                                            <td class="px-4 py-3 text-sm text-gray-700">
                                                {{ $submission->created_at->format('d M Y H:i') }}
                                            </td>
                                            <td class="px-4 py-3">
                                                @if($submission->is_late)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                                        Terlambat
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                                        Tepat Waktu
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 text-sm">
                                                @if($submission->github_link)
                                                    <a href="{{ $submission->github_link }}" target="_blank"
                                                       class="text-blue-600 hover:text-blue-900">
                                                        Lihat Repo
                                                    </a>
                                                @endif
                                                @if($submission->file_uploads && is_array($submission->file_uploads) && count($submission->file_uploads) > 0)
                                                    <div class="mt-1">
                                                        @foreach($submission->file_uploads as $file)
                                                            <a href="{{ Storage::url($file) }}" target="_blank"
                                                               class="text-green-600 hover:text-green-900 text-xs block">
                                                                📎 {{ basename($file) }}
                                                            </a>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-8 text-gray-500">
                            <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <p>Belum ada submission untuk assignment ini</p>
                        </div>
                    @endif
                </div>

            </div>

            <!-- Sidebar -->
            <div class="space-y-6">

                <!-- Assignment Status -->
                <div class="bg-white shadow-sm rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Status Assignment</h3>

                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Status</span>
                            @if($assignment->is_active)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    Nonaktif
                                </span>
                            @endif
                        </div>

                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Deadline</span>
                            <div class="text-right">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $assignment->deadline->format('d M Y') }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ $assignment->deadline->format('H:i') }}
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Deadline Status</span>
                            @if($assignment->isOverdue())
                                <span class="text-sm font-medium text-red-600">Overdue</span>
                            @elseif($assignment->isDeadlineApproaching())
                                <span class="text-sm font-medium text-orange-600">Approaching</span>
                            @else
                                <span class="text-sm font-medium text-green-600">Active</span>
                            @endif
                        </div>

                        @if($assignment->target_class)
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Target Kelas</span>
                            <span class="text-sm font-medium text-gray-900">{{ $assignment->target_class }}</span>
                        </div>
                        @endif

                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Dibuat</span>
                            <div class="text-right">
                                <div class="text-sm text-gray-900">
                                    {{ $assignment->created_at->format('d M Y') }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ $assignment->created_at->format('H:i') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="bg-white shadow-sm rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Statistik</h3>

                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Total Submissions</span>
                            <span class="text-sm font-semibold text-gray-900">{{ $assignment->submissions->count() }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Tepat Waktu</span>
                            <span class="text-sm font-semibold text-green-600">{{ $assignment->submissions->where('is_late', false)->count() }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Terlambat</span>
                            <span class="text-sm font-semibold text-red-600">{{ $assignment->submissions->where('is_late', true)->count() }}</span>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="bg-white shadow-sm rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Aksi</h3>

                    <div class="space-y-3">
                        <a href="{{ route('admin.assignments.edit', $assignment) }}"
                           class="w-full bg-blue-600 text-white text-center px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300 block">
                            Edit Assignment
                        </a>

                        @if($assignment->is_active)
                            <form action="{{ route('admin.assignments.update', $assignment) }}" method="POST" class="w-full">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="is_active" value="0">
                                <button type="submit"
                                        class="w-full bg-orange-600 text-white px-4 py-2 rounded-lg hover:bg-orange-700 transition duration-300">
                                    Nonaktifkan Assignment
                                </button>
                            </form>
                        @else
                            <form action="{{ route('admin.assignments.update', $assignment) }}" method="POST" class="w-full">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="is_active" value="1">
                                <button type="submit"
                                        class="w-full bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition duration-300">
                                    Aktifkan Assignment
                                </button>
                            </form>
                        @endif

                        <form action="{{ route('admin.assignments.destroy', $assignment) }}" method="POST" class="w-full">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="w-full bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition duration-300"
                                    onclick="return confirm('Yakin ingin menghapus assignment ini? Semua submission akan ikut terhapus.')">
                                Hapus Assignment
                            </button>
                        </form>
                    </div>
                </div>

            </div>

        </div>

    </main>

</body>
</html>
