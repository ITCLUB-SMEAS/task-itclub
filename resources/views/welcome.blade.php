<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulir Pengumpulan Tugas</title>
    <!-- Memuat Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Memuat Font Inter dari Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* Mengatur font default ke Inter */
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen p-4">

    <!-- Wrapper utama untuk formulir -->
    <div class="w-full max-w-xl mx-auto bg-white p-8 sm:p-10 rounded-2xl shadow-lg transition-shadow hover:shadow-xl">

        <!-- Judul Formulir -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Formulir Pengumpulan Tugas</h1>
            <p class="text-gray-500 mt-2">Harap isi semua kolom dengan benar.</p>

            <!-- Info User yang sedang login -->
            @auth
                <div class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <div class="text-sm text-blue-800">
                        <p><strong>👤 Nama:</strong> {{ Auth::user()->name }}</p>
                        <p><strong>📧 Email:</strong> {{ Auth::user()->email }}</p>
                        @if(Auth::user()->kelas)
                            <p><strong>🎓 Kelas:</strong> {{ Auth::user()->kelas }}</p>
                        @endif
                    </div>
                </div>
            @endauth
        </div>

        <!-- Elemen Formulir -->
        <form id="taskForm" action="{{ route('tasks.store') }}" method="POST">
            @csrf

            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="space-y-6">

                <!-- Kolom Link GitHub -->
                <div>
                    <label for="github" class="block text-sm font-medium text-gray-700 mb-2">
                        <span class="text-blue-600">📂</span> Link Repositori GitHub
                    </label>
                    <input type="url" id="github" name="github" value="{{ old('github') }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" placeholder="https://github.com/username/nama-repositori" required>
                    <p class="text-xs text-gray-500 mt-1">Pastikan repositori dapat diakses secara publik</p>
                </div>

                <!-- Kolom Deskripsi Tugas -->
                <div>
                    <label for="deskripsi" class="block text-sm font-medium text-gray-700 mb-2">
                        <span class="text-green-600">📝</span> Deskripsi Tugas
                    </label>
                    <textarea id="deskripsi" name="deskripsi" rows="4" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition resize-none" placeholder="Jelaskan detail tugas yang Anda kumpulkan, teknologi yang digunakan, dan fitur utama..." required>{{ old('deskripsi') }}</textarea>
                    <p class="text-xs text-gray-500 mt-1">Minimal 50 karakter, maksimal 1000 karakter</p>
                </div>

                <!-- Kolom Tanggal Pengumpulan -->
                <div>
                    <label for="tanggal" class="block text-sm font-medium text-gray-700 mb-2">
                        <span class="text-purple-600">📅</span> Tanggal Pengumpulan
                    </label>
                    <input type="date" id="tanggal" name="tanggal" value="{{ old('tanggal') }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" required>
                    <p class="text-xs text-gray-500 mt-1">Tanggal hari ini akan digunakan secara otomatis</p>
                </div>

                <!-- Tombol Submit -->
                <div>
                    <button type="submit" class="w-full bg-blue-600 text-white font-bold py-3 px-4 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-transform transform hover:scale-105 duration-300">
                        Kumpulkan Tugas
                    </button>
                </div>

                <!-- Navigation Links -->
                @auth
                    <div class="flex space-x-3 text-center">
                        <a href="{{ route('student.dashboard') }}" class="flex-1 bg-gray-200 text-gray-700 font-medium py-2 px-4 rounded-lg hover:bg-gray-300 transition duration-300">
                            ← Kembali ke Dashboard
                        </a>
                        <form action="{{ route('logout') }}" method="POST" class="flex-1">
                            @csrf
                            <button type="submit" class="w-full bg-red-500 text-white font-medium py-2 px-4 rounded-lg hover:bg-red-600 transition duration-300">
                                Logout
                            </button>
                        </form>
                    </div>
                @endauth

            </div>
        </form>
    </div>

    <script>
        // Fungsi untuk mengatur tanggal default ke hari ini
        function setTodayDate() {
            const today = new Date().toISOString().split('T')[0];
            const tanggalInput = document.getElementById('tanggal');
            if (!tanggalInput.value) {
                tanggalInput.value = today;
            }
        }

        // Fungsi untuk menghitung karakter deskripsi
        function setupCharacterCounter() {
            const textarea = document.getElementById('deskripsi');
            const maxLength = 1000;
            const minLength = 50;

            // Buat elemen counter
            const counter = document.createElement('div');
            counter.className = 'text-xs text-gray-500 mt-1 text-right';
            counter.id = 'char-counter';
            textarea.parentNode.appendChild(counter);

            function updateCounter() {
                const currentLength = textarea.value.length;
                counter.textContent = `${currentLength}/${maxLength} karakter`;

                if (currentLength < minLength) {
                    counter.className = 'text-xs text-red-500 mt-1 text-right';
                    counter.textContent = `${currentLength}/${maxLength} karakter (minimal ${minLength})`;
                } else if (currentLength > maxLength * 0.9) {
                    counter.className = 'text-xs text-orange-500 mt-1 text-right';
                } else {
                    counter.className = 'text-xs text-green-500 mt-1 text-right';
                }
            }

            textarea.addEventListener('input', updateCounter);
            textarea.setAttribute('maxlength', maxLength);
            updateCounter(); // Initial count
        }

        // Validasi form sebelum submit
        function validateForm() {
            const deskripsi = document.getElementById('deskripsi').value;
            if (deskripsi.length < 50) {
                alert('Deskripsi tugas minimal 50 karakter!');
                return false;
            }
            return true;
        }

        // Jalankan fungsi saat halaman dimuat
        document.addEventListener('DOMContentLoaded', function() {
            setTodayDate();
            setupCharacterCounter();

            // Tambahkan validasi pada form submit
            document.getElementById('taskForm').addEventListener('submit', function(e) {
                if (!validateForm()) {
                    e.preventDefault();
                }
            });
        });
    </script>

</body>
</html>

