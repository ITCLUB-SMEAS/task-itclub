<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $assignment->title }} - Assignment Detail</title>
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
                <h1 class="text-2xl font-bold text-gray-900">{{ $assignment->title }}</h1>
                <p class="text-sm text-gray-500">Assignment Detail</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('assignments.available') }}" class="text-sm font-medium text-gray-600 hover:text-gray-800">
                    ← Kembali ke Daftar
                </a>
                <a href="{{ route('student.dashboard') }}" class="text-sm font-medium text-gray-600 hover:text-gray-800">
                    Dashboard
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

        @if(session('error'))
            <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Assignment Content -->
            <div class="lg:col-span-2 space-y-6">

                <!-- Assignment Details -->
                <div class="bg-white shadow-sm rounded-lg p-6">
                    <div class="flex justify-between items-start mb-6">
                        <div class="flex space-x-3">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $assignment->getCategoryColor() }}">
                                {{ ucfirst($assignment->category) }}
                            </span>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $assignment->getDifficultyColor() }}">
                                {{ ucfirst($assignment->difficulty) }}
                            </span>
                            @if($assignment->target_class)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-100 text-purple-800">
                                    {{ $assignment->target_class }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="prose max-w-none">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Deskripsi Assignment</h2>
                        <p class="text-gray-700 leading-relaxed whitespace-pre-line">{{ $assignment->description }}</p>
                    </div>
                </div>

                <!-- Requirements -->
                @if($assignment->requirements && count(json_decode($assignment->requirements, true)) > 0)
                <div class="bg-white shadow-sm rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Requirements yang Harus Dipenuhi</h3>
                    <ul class="space-y-3">
                        @foreach(json_decode($assignment->requirements, true) as $index => $requirement)
                            <li class="flex items-start">
                                <span class="flex-shrink-0 w-6 h-6 bg-blue-100 text-blue-800 text-sm font-medium rounded-full flex items-center justify-center mr-3 mt-0.5">
                                    {{ $index + 1 }}
                                </span>
                                <span class="text-gray-700">{{ $requirement }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <!-- Submission Form -->
                @php
                    $userSubmission = $assignment->submissions()->where('user_id', auth()->id())->first();
                @endphp

                @if($userSubmission)
                    <!-- Already Submitted -->
                    <div class="bg-white shadow-sm rounded-lg p-6">
                        <div class="flex items-center mb-4">
                            <svg class="w-6 h-6 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <h3 class="text-lg font-medium text-gray-900">Assignment Sudah Dikumpulkan</h3>
                        </div>

                        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                <div>
                                    <span class="font-medium text-green-800">Waktu Submit:</span>
                                    <div class="text-green-700">{{ $userSubmission->created_at->format('d M Y, H:i') }}</div>
                                </div>
                                <div>
                                    <span class="font-medium text-green-800">Status:</span>
                                    @if($userSubmission->is_late)
                                        <div class="text-red-600 font-medium">Terlambat</div>
                                    @else
                                        <div class="text-green-600 font-medium">Tepat Waktu</div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Current Submission Details -->
                        <div class="space-y-4">
                            @if($userSubmission->github_repo)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">GitHub Repository</label>
                                    <a href="{{ $userSubmission->github_repo }}" target="_blank"
                                       class="text-blue-600 hover:text-blue-800 underline break-all">
                                        {{ $userSubmission->github_repo }}
                                    </a>
                                </div>
                            @endif

                            @if($userSubmission->description)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi Submission</label>
                                    <div class="bg-gray-50 rounded-lg p-3 text-gray-700 whitespace-pre-line">{{ $userSubmission->description }}</div>
                                </div>
                            @endif

                            @if($userSubmission->file_uploads && count(json_decode($userSubmission->file_uploads, true)) > 0)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">File yang Diupload</label>
                                    <div class="space-y-2">
                                        @foreach(json_decode($userSubmission->file_uploads, true) as $file)
                                            <a href="{{ Storage::url($file) }}" target="_blank"
                                               class="flex items-center text-blue-600 hover:text-blue-800 text-sm">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                                </svg>
                                                {{ basename($file) }}
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <!-- Update Submission Button -->
                            @if(!$assignment->isOverdue())
                                <div class="pt-4 border-t border-gray-200">
                                    <button onclick="showUpdateForm()"
                                            class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300">
                                        Update Submission
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>

                @else
                    <!-- Submission Form -->
                    <div class="bg-white shadow-sm rounded-lg p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Submit Assignment</h3>

                        @if($assignment->isOverdue())
                            <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-red-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                    <div>
                                        <p class="text-red-800 font-medium">Assignment ini sudah melewati deadline</p>
                                        <p class="text-red-600 text-sm">Submission akan ditandai sebagai terlambat</p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <form action="{{ route('tasks.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                            @csrf
                            <input type="hidden" name="assignment_id" value="{{ $assignment->id }}">

                            <!-- GitHub Repository -->
                            <div>
                                <label for="github_repo" class="block text-sm font-medium text-gray-700 mb-2">
                                    GitHub Repository URL <span class="text-red-500">*</span>
                                </label>
                                <input type="url"
                                       id="github_repo"
                                       name="github_repo"
                                       value="{{ old('github_repo') }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="https://github.com/username/repository"
                                       required>
                                <p class="text-xs text-gray-500 mt-1">Masukkan URL repository GitHub project Anda</p>
                            </div>

                            <!-- Description -->
                            <div>
                                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                                    Deskripsi Submission <span class="text-gray-500">(Opsional)</span>
                                </label>
                                <textarea id="description"
                                          name="description"
                                          rows="4"
                                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                          placeholder="Jelaskan apa yang telah Anda kerjakan, kendala yang dihadapi, fitur yang ditambahkan, dll...">{{ old('description') }}</textarea>
                            </div>

                            <!-- File Upload -->
                            <div>
                                <label for="files" class="block text-sm font-medium text-gray-700 mb-2">
                                    Upload File Tambahan <span class="text-gray-500">(Opsional)</span>
                                </label>
                                <input type="file"
                                       id="files"
                                       name="files[]"
                                       multiple
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                       accept=".pdf,.doc,.docx,.zip,.rar,.png,.jpg,.jpeg">
                                <p class="text-xs text-gray-500 mt-1">File yang diizinkan: PDF, DOC, ZIP, RAR, PNG, JPG (Max: 10MB per file)</p>
                            </div>

                            <!-- Submit Button -->
                            <div class="pt-4">
                                <button type="submit"
                                        class="w-full bg-green-600 text-white py-3 px-4 rounded-lg hover:bg-green-700 transition duration-300 font-medium">
                                    {{ $assignment->isOverdue() ? 'Submit Assignment (Terlambat)' : 'Submit Assignment' }}
                                </button>
                            </div>
                        </form>
                    </div>
                @endif

            </div>

            <!-- Sidebar -->
            <div class="space-y-6">

                <!-- Deadline Info -->
                <div class="bg-white shadow-sm rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Deadline</h3>

                    @php
                        $now = now();
                        $timeLeft = $assignment->deadline->diff($now);
                        $isOverdue = $assignment->deadline->isPast();
                        $totalHours = $assignment->created_at->diffInHours($assignment->deadline);
                        $hoursLeft = max(0, $now->diffInHours($assignment->deadline, false));
                        $progress = $totalHours > 0 ? (($totalHours - $hoursLeft) / $totalHours) * 100 : 100;
                        $progress = min(100, max(0, $progress));
                    @endphp

                    <div class="text-center mb-4">
                        <div class="text-2xl font-bold text-gray-900">
                            {{ $assignment->deadline->format('d M Y') }}
                        </div>
                        <div class="text-sm text-gray-600">
                            {{ $assignment->deadline->format('H:i') }} WIB
                        </div>
                    </div>

                    @if($isOverdue)
                        <div class="text-center p-3 bg-red-50 border border-red-200 rounded-lg mb-4">
                            <div class="text-red-600 font-medium">⏰ Deadline Terlewat</div>
                            <div class="text-sm text-red-500">
                                {{ $timeLeft->days }}d {{ $timeLeft->h }}h {{ $timeLeft->i }}m yang lalu
                            </div>
                        </div>
                    @else
                        <div class="text-center p-3 bg-green-50 border border-green-200 rounded-lg mb-4">
                            <div class="text-green-600 font-medium">⏳ Waktu Tersisa</div>
                            <div class="text-sm text-green-700">
                                {{ $timeLeft->days }}d {{ $timeLeft->h }}h {{ $timeLeft->i }}m
                            </div>
                        </div>
                    @endif

                    <!-- Progress Bar -->
                    <div class="mb-4">
                        <div class="flex justify-between text-xs text-gray-600 mb-1">
                            <span>Progress Waktu</span>
                            <span>{{ number_format($progress, 0) }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div class="h-3 rounded-full {{ $progress > 80 ? 'bg-red-500' : ($progress > 60 ? 'bg-orange-500' : 'bg-green-500') }}"
                                 style="width: {{ $progress }}%"></div>
                        </div>
                    </div>

                    @if($progress > 80 && !$isOverdue)
                        <div class="text-center text-orange-600 text-sm font-medium">
                            ⚠️ Waktu hampir habis!
                        </div>
                    @endif
                </div>

                <!-- Assignment Stats -->
                <div class="bg-white shadow-sm rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Statistik Assignment</h3>

                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Total Submissions</span>
                            <span class="text-sm font-semibold text-gray-900">{{ $assignment->getSubmissionsCount() }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Tepat Waktu</span>
                            <span class="text-sm font-semibold text-green-600">{{ $assignment->submissions()->where('is_late', false)->count() }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Terlambat</span>
                            <span class="text-sm font-semibold text-red-600">{{ $assignment->submissions()->where('is_late', true)->count() }}</span>
                        </div>
                    </div>
                </div>

                <!-- Your Status -->
                @if($userSubmission)
                <div class="bg-white shadow-sm rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Status Anda</h3>

                    <div class="text-center">
                        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <svg class="w-8 h-8 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="text-green-600 font-medium mb-1">Sudah Dikumpulkan</div>
                        <div class="text-sm text-gray-600">
                            {{ $userSubmission->created_at->format('d M Y, H:i') }}
                        </div>
                        @if($userSubmission->is_late)
                            <div class="text-xs text-red-600 mt-1">Dikumpulkan terlambat</div>
                        @endif
                    </div>
                </div>
                @endif

            </div>

        </div>

    </main>

    <!-- Update Form Modal (Hidden by default) -->
    @if($userSubmission && !$assignment->isOverdue())
    <div id="updateModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="bg-white rounded-lg p-6 max-w-2xl w-full max-h-screen overflow-y-auto">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Update Submission</h3>
                    <button onclick="hideUpdateForm()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <form action="{{ route('tasks.update', $userSubmission) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    @method('PATCH')

                    <!-- GitHub Repository -->
                    <div>
                        <label for="update_github_repo" class="block text-sm font-medium text-gray-700 mb-2">
                            GitHub Repository URL <span class="text-red-500">*</span>
                        </label>
                        <input type="url"
                               id="update_github_repo"
                               name="github_repo"
                               value="{{ $userSubmission->github_repo }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                               required>
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="update_description" class="block text-sm font-medium text-gray-700 mb-2">
                            Deskripsi Submission
                        </label>
                        <textarea id="update_description"
                                  name="description"
                                  rows="4"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">{{ $userSubmission->description }}</textarea>
                    </div>

                    <!-- File Upload -->
                    <div>
                        <label for="update_files" class="block text-sm font-medium text-gray-700 mb-2">
                            Upload File Baru <span class="text-gray-500">(akan mengganti file lama)</span>
                        </label>
                        <input type="file"
                               id="update_files"
                               name="files[]"
                               multiple
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                               accept=".pdf,.doc,.docx,.zip,.rar,.png,.jpg,.jpeg">
                    </div>

                    <div class="flex justify-end space-x-3 pt-4">
                        <button type="button" onclick="hideUpdateForm()"
                                class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                            Batal
                        </button>
                        <button type="submit"
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            Update Submission
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    <script>
        function showUpdateForm() {
            document.getElementById('updateModal').classList.remove('hidden');
        }

        function hideUpdateForm() {
            document.getElementById('updateModal').classList.add('hidden');
        }

        // Close modal when clicking outside
        document.getElementById('updateModal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                hideUpdateForm();
            }
        });
    </script>

</body>
</html>
