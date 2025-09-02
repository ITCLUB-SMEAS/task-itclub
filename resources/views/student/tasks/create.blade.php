<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengumpulan Tugas - IT Club</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100">
        <!-- Header -->
        <div class="bg-white shadow-sm border-b">
            <div class="max-w-4xl mx-auto px-4 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <i class="fas fa-upload text-blue-600 text-2xl"></i>
                        <h1 class="text-2xl font-bold text-gray-800">Formulir Pengumpulan Tugas</h1>
                    </div>
                    <a href="{{ route('student.dashboard') }}" class="text-blue-600 hover:text-blue-800 font-medium">
                        <i class="fas fa-arrow-left mr-2"></i>Kembali ke Dashboard
                    </a>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="max-w-4xl mx-auto px-4 py-8">
            <!-- Info Card -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <div class="flex items-start">
                    <i class="fas fa-info-circle text-blue-600 mt-1 mr-3"></i>
                    <div>
                        <h3 class="font-semibold text-blue-800 mb-1">Informasi Penting</h3>
                        <p class="text-blue-700 text-sm">Pastikan semua kolom terisi dengan benar. Repository GitHub harus dapat diakses secara publik.</p>
                    </div>
                </div>
            </div>

            <!-- Form Card -->
            <div class="bg-white rounded-lg shadow-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-800">Detail Pengumpulan</h2>
                </div>

                <form action="{{ route('tasks.store') }}" method="POST" enctype="multipart/form-data" class="p-6">
                    @csrf
                    
                    <!-- Student Info (Read-only) -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div class="bg-gray-50 rounded-lg p-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-user mr-2"></i>Nama Lengkap
                            </label>
                            <input type="text" value="{{ auth()->user()->name }}" class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-600" readonly>
                        </div>

                        <div class="bg-gray-50 rounded-lg p-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-envelope mr-2"></i>Email
                            </label>
                            <input type="text" value="{{ auth()->user()->email }}" class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-600" readonly>
                        </div>

                        <div class="bg-gray-50 rounded-lg p-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-graduation-cap mr-2"></i>Kelas
                            </label>
                            <input type="text" value="{{ auth()->user()->kelas ?? 'Belum diset' }}" class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-600" readonly>
                        </div>

                        <div class="bg-gray-50 rounded-lg p-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-calendar mr-2"></i>Tanggal Pengumpulan
                            </label>
                            <input type="text" value="{{ now()->format('d/m/Y') }}" class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-600" readonly>
                        </div>
                    </div>

                    <!-- Assignment Selection (if available) -->
                    @php
                        $assignments = \App\Models\TaskAssignment::where('is_active', true)
                                                                ->where('deadline', '>', now())
                                                                ->where(function($query) {
                                                                    $query->whereNull('target_class')
                                                                          ->orWhere('target_class', auth()->user()->kelas);
                                                                })
                                                                ->get();
                    @endphp
                    
                    @if($assignments->count() > 0)
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-tasks mr-2"></i>Pilih Assignment (Opsional)
                        </label>
                        <select name="assignment_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Pilih assignment atau biarkan kosong untuk tugas umum</option>
                            @foreach($assignments as $assignment)
                                <option value="{{ $assignment->id }}">
                                    {{ $assignment->title }} 
                                    @if($assignment->deadline)
                                        (Deadline: {{ $assignment->deadline->format('d/m/Y H:i') }})
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    <!-- GitHub Repository -->
                    <div class="mb-6">
                        <label for="github_repo" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fab fa-github mr-2"></i>Link Repository GitHub *
                        </label>
                        <input type="url" 
                               name="github_repo" 
                               id="github_repo"
                               placeholder="https://github.com/username/nama-repository" 
                               value="{{ old('github_repo') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('github_repo') border-red-500 @enderror"
                               required>
                        @error('github_repo')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">Pastikan repository dapat diakses secara publik</p>
                    </div>

                    <!-- Description -->
                    <div class="mb-6">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-file-text mr-2"></i>Deskripsi Tugas
                        </label>
                        <textarea name="description" 
                                  id="description"
                                  rows="4" 
                                  placeholder="Jelaskan detail tugas yang Anda kumpulkan, teknologi yang digunakan, dan fitur utama..."
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <div class="flex justify-between mt-1">
                            <p class="text-sm text-gray-500">Minimal 50 karakter, maksimal 1000 karakter</p>
                            <span id="charCount" class="text-sm text-gray-400">0/1000 karakter</span>
                        </div>
                    </div>

                    <!-- File Upload -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-paperclip mr-2"></i>File Pendukung (Opsional)
                        </label>
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-400 transition-colors">
                            <input type="file" 
                                   name="files[]" 
                                   id="files"
                                   multiple
                                   accept=".pdf,.doc,.docx,.zip,.rar,.png,.jpg,.jpeg"
                                   class="hidden">
                            <label for="files" class="cursor-pointer">
                                <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-2"></i>
                                <p class="text-gray-600 font-medium">Klik untuk upload file</p>
                                <p class="text-sm text-gray-500 mt-1">PDF, DOC, DOCX, ZIP, RAR, PNG, JPG (Maks. 10MB per file)</p>
                            </label>
                        </div>
                        <div id="fileList" class="mt-3 space-y-2"></div>
                        @error('files.*')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex flex-col sm:flex-row gap-4 pt-6 border-t border-gray-200">
                        <button type="submit" class="flex-1 bg-blue-600 text-white px-6 py-3 rounded-lg font-medium hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                            <i class="fas fa-upload mr-2"></i>Kumpulkan Tugas
                        </button>
                        <a href="{{ route('student.dashboard') }}" class="flex-1 bg-gray-100 text-gray-700 px-6 py-3 rounded-lg font-medium text-center hover:bg-gray-200 focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors">
                            <i class="fas fa-times mr-2"></i>Batal
                        </a>
                    </div>
                </form>
            </div>

            <!-- Help Section -->
            <div class="mt-8 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <h3 class="font-semibold text-yellow-800 mb-2">
                    <i class="fas fa-lightbulb mr-2"></i>Tips Pengumpulan Tugas
                </h3>
                <ul class="text-yellow-700 text-sm space-y-1">
                    <li>• Pastikan repository GitHub sudah public dan dapat diakses</li>
                    <li>• Sertakan README.md yang menjelaskan cara menjalankan project</li>
                    <li>• Upload file pendukung jika diperlukan (screenshot, dokumentasi, dll)</li>
                    <li>• Periksa kembali semua informasi sebelum submit</li>
                </ul>
            </div>
        </div>
    </div>

    <script>
        // Character count for description
        const descriptionTextarea = document.getElementById('description');
        const charCount = document.getElementById('charCount');
        
        descriptionTextarea.addEventListener('input', function() {
            const count = this.value.length;
            charCount.textContent = count + '/1000 karakter';
            
            if (count > 1000) {
                charCount.classList.add('text-red-500');
                charCount.classList.remove('text-gray-400');
            } else {
                charCount.classList.remove('text-red-500');
                charCount.classList.add('text-gray-400');
            }
        });

        // File upload handler
        const fileInput = document.getElementById('files');
        const fileList = document.getElementById('fileList');
        
        fileInput.addEventListener('change', function() {
            fileList.innerHTML = '';
            
            for (let i = 0; i < this.files.length; i++) {
                const file = this.files[i];
                const fileItem = document.createElement('div');
                fileItem.className = 'flex items-center justify-between bg-gray-50 p-2 rounded border';
                fileItem.innerHTML = `
                    <div class="flex items-center">
                        <i class="fas fa-file text-gray-500 mr-2"></i>
                        <span class="text-sm text-gray-700">${file.name}</span>
                        <span class="text-xs text-gray-500 ml-2">(${(file.size / 1024 / 1024).toFixed(2)} MB)</span>
                    </div>
                `;
                fileList.appendChild(fileItem);
            }
        });
    </script>
</body>
</html>
