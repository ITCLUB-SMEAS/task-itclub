<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="user-id" content="{{ Auth::id() }}">
    <title>{{ $title ?? 'Pengumpulan Tugas' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="{{ asset('js/darkmode.js') }}"></script>
    <link href="{{ asset('build/assets/app-BXl0pDBb.css') }}" rel="stylesheet">
    <script src="{{ asset('build/assets/app-DjIouP1n.js') }}" defer></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .modal-hidden { display: none; }

        /* Dark mode styles */
        .dark body { background-color: #1a1a1a; color: #e1e1e1; }
        .dark .bg-white { background-color: #2a2a2a; }
        .dark .bg-gray-100 { background-color: #1a1a1a; }
        .dark .text-gray-900 { color: #e1e1e1; }
        .dark .text-gray-600, .dark .text-gray-500 { color: #9ca3af; }
        .dark .shadow-sm { box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.5); }
        .dark .border-gray-200 { border-color: #4a4a4a; }
        .dark .bg-gray-50 { background-color: #2a2a2a; }
    </style>
    @stack('styles')
</head>
<body class="bg-gray-100 dark:bg-gray-900 transition-colors duration-200">
    <!-- Header -->
    <header class="bg-white dark:bg-gray-800 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <h1 class="text-xl font-bold text-gray-900 dark:text-white">Pengumpulan Tugas</h1>
                    </div>
                </div>
                <div class="flex items-center">
                    <!-- Dark Mode Toggle -->
                    <button id="darkModeToggle" class="p-2 rounded-md text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline-none">
                        <!-- Sun icon for dark mode -->
                        <svg id="sunIcon" class="h-6 w-6 hidden dark:block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <!-- Moon icon for light mode -->
                        <svg id="moonIcon" class="h-6 w-6 block dark:hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                        </svg>
                    </button>
                    <!-- Profile Dropdown -->
                    <div class="ml-4 relative flex items-center">
                        <div class="text-sm text-gray-700 dark:text-gray-300 mr-2">{{ Auth::user()->name }}</div>
                        <div>
                            <button id="profileDropdown" class="flex text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-800 focus:ring-white" id="user-menu-button">
                                <span class="sr-only">Open user menu</span>
                                <div class="h-8 w-8 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center text-gray-700 dark:text-gray-200 font-semibold">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </div>
                            </button>
                        </div>
                        <!-- Dropdown menu -->
                        <div id="profileMenu" class="hidden origin-top-right absolute right-0 top-full mt-2 w-48 rounded-md shadow-lg py-1 bg-white dark:bg-gray-700 ring-1 ring-black ring-opacity-5 focus:outline-none z-10" role="menu">
                            <a href="{{ Auth::user()->role === 'admin' ? '/admin/dashboard' : '/student/dashboard' }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600" role="menuitem">Dashboard</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600" role="menuitem">Logout</button>
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
    <footer class="bg-white dark:bg-gray-800 mt-12 py-6 border-t border-gray-200 dark:border-gray-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center text-gray-500 dark:text-gray-400">
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
