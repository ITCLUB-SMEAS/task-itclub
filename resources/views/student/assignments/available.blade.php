<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assignment Tersedia - Student Dashboard</title>
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
                <h1 class="text-2xl font-bold text-gray-900">Assignment Tersedia</h1>
                <p class="text-sm text-gray-500">Pilih dan kerjakan assignment yang tersedia</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('student.dashboard') }}" class="text-sm font-medium text-gray-600 hover:text-gray-800">
                    ← Dashboard
                </a>
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="text-sm font-medium text-blue-600 hover:text-blue-500">Logout</button>
                </form>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">

        @if(session('success'))
            <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        <!-- Filter Bar -->
        <div class="mb-6 bg-white rounded-lg shadow-sm p-4">
            <form method="GET" action="{{ route('assignments.available') }}" class="flex flex-wrap gap-4 items-center">
                <div>
                    <label for="category" class="text-sm font-medium text-gray-700 mr-2">Kategori:</label>
                    <select name="category" id="category" class="px-3 py-1 border border-gray-300 rounded-lg text-sm" onchange="this.form.submit()">
                        <option value="">Semua Kategori</option>
                        <option value="web" {{ request('category') == 'web' ? 'selected' : '' }}>Web Development</option>
                        <option value="mobile" {{ request('category') == 'mobile' ? 'selected' : '' }}>Mobile Development</option>
                        <option value="programming" {{ request('category') == 'programming' ? 'selected' : '' }}>Programming</option>
                        <option value="design" {{ request('category') == 'design' ? 'selected' : '' }}>Design</option>
                        <option value="database" {{ request('category') == 'database' ? 'selected' : '' }}>Database</option>
                        <option value="other" {{ request('category') == 'other' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                </div>
                <div>
                    <label for="difficulty" class="text-sm font-medium text-gray-700 mr-2">Kesulitan:</label>
                    <select name="difficulty" id="difficulty" class="px-3 py-1 border border-gray-300 rounded-lg text-sm" onchange="this.form.submit()">
                        <option value="">Semua Level</option>
                        <option value="easy" {{ request('difficulty') == 'easy' ? 'selected' : '' }}>Mudah</option>
                        <option value="medium" {{ request('difficulty') == 'medium' ? 'selected' : '' }}>Sedang</option>
                        <option value="hard" {{ request('difficulty') == 'hard' ? 'selected' : '' }}>Sulit</option>
                    </select>
                </div>
                <div>
                    <label for="sort" class="text-sm font-medium text-gray-700 mr-2">Urutkan:</label>
                    <select name="sort" id="sort" class="px-3 py-1 border border-gray-300 rounded-lg text-sm" onchange="this.form.submit()">
                        <option value="deadline_asc" {{ request('sort') == 'deadline_asc' ? 'selected' : '' }}>Deadline Terdekat</option>
                        <option value="deadline_desc" {{ request('sort') == 'deadline_desc' ? 'selected' : '' }}>Deadline Terjauh</option>
                        <option value="created_desc" {{ request('sort') == 'created_desc' ? 'selected' : '' }}>Terbaru</option>
                        <option value="created_asc" {{ request('sort') == 'created_asc' ? 'selected' : '' }}>Terlama</option>
                    </select>
                </div>
            </form>
        </div>

        <!-- Assignments Grid -->
        @if($assignments->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($assignments as $assignment)
                    <div class="bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-md transition-shadow duration-300">

                        <!-- Card Header -->
                        <div class="p-6 pb-4">
                            <div class="flex justify-between items-start mb-3">
                                <h3 class="text-lg font-semibold text-gray-900 leading-tight">{{ $assignment->title }}</h3>
                                @if($assignment->isOverdue())
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 ml-2">
                                        Overdue
                                    </span>
                                @elseif($assignment->isDeadlineApproaching())
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800 ml-2">
                                        Urgent
                                    </span>
                                @endif
                            </div>

                            <p class="text-gray-600 text-sm mb-4 line-clamp-3">{{ Str::limit($assignment->description, 120) }}</p>

                            <!-- Badges -->
                            <div class="flex flex-wrap gap-2 mb-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $assignment->getCategoryColor() }}">
                                    {{ ucfirst($assignment->category) }}
                                </span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $assignment->getDifficultyColor() }}">
                                    {{ ucfirst($assignment->difficulty) }}
                                </span>
                                @if($assignment->target_class)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800">
                                        {{ $assignment->target_class }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Card Content -->
                        <div class="px-6 pb-4">
                            <!-- Deadline Info -->
                            <div class="flex items-center text-sm text-gray-600 mb-3">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="font-medium">Deadline: {{ $assignment->deadline->format('d M Y, H:i') }}</span>
                            </div>

                            <!-- Time Remaining -->
                            @php
                                $now = now();
                                $timeLeft = $assignment->deadline->diff($now);
                                $isOverdue = $assignment->deadline->isPast();
                            @endphp

                            <div class="mb-4">
                                @if($isOverdue)
                                    <div class="text-red-600 text-sm font-medium">
                                        ⏰ Overdue {{ $timeLeft->days }}d {{ $timeLeft->h }}h {{ $timeLeft->i }}m yang lalu
                                    </div>
                                @else
                                    <div class="text-green-600 text-sm font-medium">
                                        ⏳ {{ $timeLeft->days }}d {{ $timeLeft->h }}h {{ $timeLeft->i }}m tersisa
                                    </div>
                                @endif
                            </div>

                            <!-- Progress Bar for Deadline -->
                            @php
                                $totalDays = $assignment->created_at->diffInDays($assignment->deadline);
                                $daysLeft = max(0, $now->diffInDays($assignment->deadline, false));
                                $progress = $totalDays > 0 ? (($totalDays - $daysLeft) / $totalDays) * 100 : 100;
                                $progress = min(100, max(0, $progress));
                            @endphp

                            <div class="mb-4">
                                <div class="flex justify-between text-xs text-gray-600 mb-1">
                                    <span>Progress Waktu</span>
                                    <span>{{ number_format($progress, 0) }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="h-2 rounded-full {{ $progress > 80 ? 'bg-red-500' : ($progress > 60 ? 'bg-orange-500' : 'bg-green-500') }}"
                                         style="width: {{ $progress }}%"></div>
                                </div>
                            </div>

                            <!-- Requirements Preview -->
                            @if($assignment->requirements && count(json_decode($assignment->requirements, true)) > 0)
                                <div class="mb-4">
                                    <p class="text-xs text-gray-600 mb-1">Requirements ({{ count(json_decode($assignment->requirements, true)) }} item):</p>
                                    <ul class="text-xs text-gray-700 space-y-1">
                                        @foreach(array_slice(json_decode($assignment->requirements, true), 0, 2) as $requirement)
                                            <li class="flex items-start">
                                                <span class="text-green-500 mr-1">✓</span>
                                                <span>{{ Str::limit($requirement, 40) }}</span>
                                            </li>
                                        @endforeach
                                        @if(count(json_decode($assignment->requirements, true)) > 2)
                                            <li class="text-gray-500">+ {{ count(json_decode($assignment->requirements, true)) - 2 }} lainnya...</li>
                                        @endif
                                    </ul>
                                </div>
                            @endif
                        </div>

                        <!-- Card Footer -->
                        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                            <!-- Check if already submitted -->
                            @php
                                $userSubmission = $assignment->submissions()->where('user_id', auth()->id())->first();
                            @endphp

                            @if($userSubmission)
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center text-green-600 text-sm">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                        <span class="font-medium">Sudah Dikumpulkan</span>
                                    </div>
                                    <a href="{{ route('assignments.show', $assignment) }}"
                                       class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                        Lihat Detail
                                    </a>
                                </div>
                                @if($userSubmission->is_late)
                                    <p class="text-red-600 text-xs mt-1">Dikumpulkan terlambat</p>
                                @endif
                            @else
                                <div class="flex justify-between items-center">
                                    <a href="{{ route('assignments.show', $assignment) }}"
                                       class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                        Lihat Detail
                                    </a>
                                    <a href="{{ route('assignments.show', $assignment) }}"
                                       class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition duration-300">
                                        {{ $isOverdue ? 'Kerjakan (Terlambat)' : 'Kerjakan Sekarang' }}
                                    </a>
                                </div>
                            @endif
                        </div>

                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($assignments->hasPages())
                <div class="mt-8">
                    {{ $assignments->appends(request()->query())->links() }}
                </div>
            @endif

        @else
            <div class="text-center py-12">
                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada assignment tersedia</h3>
                <p class="text-gray-600">Assignment baru akan muncul di sini ketika admin menambahkannya.</p>
            </div>
        @endif

    </main>

    <style>
        .line-clamp-3 {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>

</body>
</html>
