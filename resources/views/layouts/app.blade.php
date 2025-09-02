<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="user-id" content="{{ Auth::id() }}">
    <title>{{ $title ?? 'Pengumpulan Tugas' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="{{ asset('build/assets/app-BXl0pDBb.css') }}" rel="stylesheet">
    <script src="{{ asset('build/assets/app-DjIouP1n.js') }}" defer></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .modal-hidden { display: none; }
    </style>
    @stack('styles')
</head>
<body class="bg-gray-100 transition-colors duration-200">
    <!-- Header -->
    <header class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <h1 class="text-xl font-bold text-gray-900">Pengumpulan Tugas</h1>
                    </div>
                </div>
                <div class="flex items-center">
                    <!-- Profile Dropdown -->
                    <div class="ml-4 relative flex items-center">
                        <div class="text-sm text-gray-700 mr-2">{{ Auth::user()->name }}</div>
                        <div>
                            <button id="profileDropdown" class="flex text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-800 focus:ring-white" id="user-menu-button">
                                <span class="sr-only">Open user menu</span>
                                <div class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center text-gray-700 font-semibold">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </div>
                            </button>
                        </div>
                        <!-- Dropdown menu -->
                        <div id="profileMenu" class="hidden origin-top-right absolute right-0 top-full mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-10" role="menu">
                            <a href="{{ Auth::user()->role === 'admin' ? '/admin/dashboard' : '/student/dashboard' }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">Dashboard</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">Logout</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white mt-12 py-6 border-t border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center text-gray-500">
                <p>&copy; {{ date('Y') }} IT Club SMEAS. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        // Profile dropdown
        const profileDropdown = document.getElementById('profileDropdown');
        const profileMenu = document.getElementById('profileMenu');

        if (profileDropdown && profileMenu) {
            profileDropdown.addEventListener('click', () => {
                profileMenu.classList.toggle('hidden');
            });

            // Close the dropdown when clicking outside
            document.addEventListener('click', (event) => {
                if (!profileDropdown.contains(event.target) && !profileMenu.contains(event.target)) {
                    profileMenu.classList.add('hidden');
                }
            });
        }
    </script>
    @stack('scripts')
</body>
</html>
