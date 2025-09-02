<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Assignment - {{ $assignment->title }}</title>
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
                <h1 class="text-2xl font-bold text-gray-900">Edit Assignment</h1>
                <p class="text-sm text-gray-500">{{ $assignment->title }}</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('admin.assignments.show', $assignment) }}" class="text-sm font-medium text-gray-600 hover:text-gray-800">
                    ← Kembali ke Detail
                </a>
                <a href="{{ route('admin.assignments.index') }}" class="text-sm font-medium text-gray-600 hover:text-gray-800">
                    Daftar Assignment
                </a>
            </div>
        </div>
    </header>

    <main class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">

        @if($errors->any())
            <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white shadow-sm rounded-lg overflow-hidden">
            <form action="{{ route('admin.assignments.update', $assignment) }}" method="POST" class="p-6 space-y-6">
                @csrf
                @method('PATCH')

                <!-- Title -->
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Judul Assignment *</label>
                    <input type="text"
                           id="title"
                           name="title"
                           value="{{ old('title', $assignment->title) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Contoh: Project Website Portfolio"
                           required>
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Deskripsi *</label>
                    <textarea id="description"
                              name="description"
                              rows="4"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Jelaskan detail assignment, tujuan pembelajaran, dan yang diharapkan dari siswa..."
                              required>{{ old('description', $assignment->description) }}</textarea>
                </div>

                <!-- Category and Difficulty -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="category" class="block text-sm font-medium text-gray-700 mb-2">Kategori *</label>
                        <select id="category"
                                name="category"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                required>
                            <option value="">Pilih Kategori</option>
                            <option value="web" {{ old('category', $assignment->category) == 'web' ? 'selected' : '' }}>Web Development</option>
                            <option value="mobile" {{ old('category', $assignment->category) == 'mobile' ? 'selected' : '' }}>Mobile Development</option>
                            <option value="programming" {{ old('category', $assignment->category) == 'programming' ? 'selected' : '' }}>Programming</option>
                            <option value="design" {{ old('category', $assignment->category) == 'design' ? 'selected' : '' }}>Design</option>
                            <option value="database" {{ old('category', $assignment->category) == 'database' ? 'selected' : '' }}>Database</option>
                            <option value="other" {{ old('category', $assignment->category) == 'other' ? 'selected' : '' }}>Lainnya</option>
                        </select>
                    </div>

                    <div>
                        <label for="difficulty" class="block text-sm font-medium text-gray-700 mb-2">Tingkat Kesulitan *</label>
                        <select id="difficulty"
                                name="difficulty"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                required>
                            <option value="">Pilih Kesulitan</option>
                            <option value="easy" {{ old('difficulty', $assignment->difficulty) == 'easy' ? 'selected' : '' }}>Mudah</option>
                            <option value="medium" {{ old('difficulty', $assignment->difficulty) == 'medium' ? 'selected' : '' }}>Sedang</option>
                            <option value="hard" {{ old('difficulty', $assignment->difficulty) == 'hard' ? 'selected' : '' }}>Sulit</option>
                        </select>
                    </div>
                </div>

                <!-- Deadline and Target Class -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="deadline" class="block text-sm font-medium text-gray-700 mb-2">Deadline *</label>
                        <input type="datetime-local"
                               id="deadline"
                               name="deadline"
                               value="{{ old('deadline', $assignment->deadline->format('Y-m-d\TH:i')) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                               required>
                        @if($assignment->isOverdue())
                            <p class="text-xs text-red-600 mt-1">⚠️ Assignment ini sudah melewati deadline</p>
                        @endif
                    </div>

                    <div>
                        <label for="target_class" class="block text-sm font-medium text-gray-700 mb-2">Target Kelas</label>
                        <input type="text"
                               id="target_class"
                               name="target_class"
                               value="{{ old('target_class', $assignment->target_class) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Contoh: XII RPL 1, XI TKJ, atau kosongkan untuk semua kelas">
                    </div>
                </div>

                <!-- Requirements -->
                <div>
                    <label for="requirements" class="block text-sm font-medium text-gray-700 mb-2">Requirements/Persyaratan</label>
                    <div class="space-y-2">
                        <textarea id="requirements_text"
                                  rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="Masukkan requirements, satu per baris. Contoh:&#10;- Menggunakan HTML5 dan CSS3&#10;- Responsive design&#10;- Upload ke GitHub Pages"
                                  onchange="convertToJson()">{{ $assignment->requirements ? implode("\n", json_decode($assignment->requirements, true)) : '' }}</textarea>
                        <input type="hidden" id="requirements" name="requirements" value="{{ old('requirements', $assignment->requirements) }}">
                        <p class="text-xs text-gray-500">Tuliskan setiap requirement di baris baru. Akan otomatis diformat.</p>
                    </div>
                </div>

                <!-- Status -->
                <div>
                    <label class="flex items-center">
                        <input type="checkbox"
                               name="is_active"
                               value="1"
                               {{ old('is_active', $assignment->is_active) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-700">Assignment aktif (siswa dapat melihat dan mengerjakan)</span>
                    </label>
                </div>

                <!-- Submission Info -->
                @if($assignment->getSubmissionsCount() > 0)
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-blue-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                        <div>
                            <p class="text-sm font-medium text-blue-800">Assignment ini sudah memiliki {{ $assignment->getSubmissionsCount() }} submission</p>
                            <p class="text-xs text-blue-600">Hati-hati saat mengubah deadline atau requirement yang sudah ada</p>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Submit Buttons -->
                <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                    <a href="{{ route('admin.assignments.show', $assignment) }}"
                       class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition duration-300">
                        Batal
                    </a>
                    <button type="submit"
                            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-300">
                        Update Assignment
                    </button>
                </div>

            </form>
        </div>

    </main>

    <script>
        function convertToJson() {
            const textArea = document.getElementById('requirements_text');
            const hiddenInput = document.getElementById('requirements');

            if (textArea.value.trim()) {
                const lines = textArea.value.split('\n')
                    .map(line => line.trim())
                    .filter(line => line.length > 0)
                    .map(line => line.replace(/^[-*•]\s*/, '')); // Remove bullet points

                hiddenInput.value = JSON.stringify(lines);
            } else {
                hiddenInput.value = '[]';
            }
        }

        // Convert requirements on page load
        document.addEventListener('DOMContentLoaded', function() {
            convertToJson();
        });
    </script>

</body>
</html>
