@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Tim Saya</h1>
        <a href="{{ route('student.teams.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            Buat Tim Baru
        </a>
    </div>

    @if (session('success'))
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
        <p>{{ session('success') }}</p>
    </div>
    @endif

    @if (session('error'))
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
        <p>{{ session('error') }}</p>
    </div>
    @endif

    <!-- Form Gabung Tim -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 mb-6">
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Gabung Tim</h2>
        <form action="{{ route('student.teams.join') }}" method="POST" class="flex items-center">
            @csrf
            <div class="flex-1 mr-4">
                <label for="team_code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Kode Tim</label>
                <input type="text" name="team_code" id="team_code" placeholder="Masukkan kode tim" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                @error('team_code')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div class="pt-6">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Gabung
                </button>
            </div>
        </form>
    </div>

    <!-- Tim Saya -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 mb-6">
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Tim Saya</h2>

        @if($userTeams->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($userTeams as $team)
            <div class="bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg overflow-hidden shadow-md">
                <div class="p-4 bg-gradient-to-r from-blue-500 to-indigo-600 text-white">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-bold">{{ $team->name }}</h3>
                        <span class="px-2 py-1 text-xs rounded-full {{ $team->is_active ? 'bg-green-500' : 'bg-red-500' }}">
                            {{ $team->is_active ? 'Aktif' : 'Tidak Aktif' }}
                        </span>
                    </div>
                    <p class="text-sm opacity-80">{{ $team->class_group }}</p>
                </div>
                <div class="p-4">
                    <p class="text-sm text-gray-600 dark:text-gray-300 mb-3">{{ Str::limit($team->description, 100) }}</p>
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Anggota: {{ $team->members->count() }}/{{ $team->max_members }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Ketua: {{ $team->leader->name ?? 'Belum ada' }}</p>
                        </div>
                        <a href="{{ route('student.teams.show', $team->id) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 text-sm font-medium">
                            Detail →
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-8 text-gray-500 dark:text-gray-400">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
            <p class="mt-2 text-lg font-medium">Anda belum tergabung dalam tim manapun</p>
            <p class="mt-1">Gabung tim dengan kode tim atau buat tim baru</p>
        </div>
        @endif
    </div>

    <!-- Tim yang Tersedia -->
    @if($availableTeams->count() > 0)
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Tim yang Tersedia</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($availableTeams as $team)
            <div class="bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg overflow-hidden shadow-sm">
                <div class="p-4 bg-gradient-to-r from-gray-500 to-gray-600 text-white">
                    <h3 class="text-lg font-bold">{{ $team->name }}</h3>
                    <p class="text-sm opacity-80">{{ $team->class_group }}</p>
                </div>
                <div class="p-4">
                    <p class="text-sm text-gray-600 dark:text-gray-300 mb-3">{{ Str::limit($team->description, 80) }}</p>
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Anggota: {{ $team->members->count() }}/{{ $team->max_members }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Ketua: {{ $team->leader->name ?? 'Belum ada' }}</p>
                        </div>
                        <form action="{{ route('student.teams.join') }}" method="POST" class="inline">
                            @csrf
                            <input type="hidden" name="team_code" value="{{ $team->team_code }}">
                            <button type="submit" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 text-sm font-medium">
                                Gabung →
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection
