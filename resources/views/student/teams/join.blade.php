@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Gabung Tim</h1>
        <a href="{{ route('student.teams.index') }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 font-medium">
            &larr; Kembali ke Tim Saya
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
        <div class="max-w-md mx-auto">
            <div class="text-center mb-6">
                <svg class="mx-auto h-12 w-12 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                <h2 class="mt-2 text-lg font-medium text-gray-900 dark:text-white">Gabung ke Tim dengan Kode Undangan</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Masukkan kode undangan yang telah dibagikan oleh ketua tim untuk bergabung ke tim mereka.
                </p>
            </div>

            @if(session('error'))
            <div class="bg-red-50 dark:bg-red-900/30 border-l-4 border-red-500 p-4 mb-6">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-red-700 dark:text-red-300">
                            {{ session('error') }}
                        </p>
                    </div>
                </div>
            </div>
            @endif

            <form action="{{ route('student.teams.join-by-code') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="invite_code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Kode Undangan <span class="text-red-600">*</span></label>
                    <input type="text" name="invite_code" id="invite_code" value="{{ old('invite_code') }}" required autofocus
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white text-center tracking-widest font-mono uppercase">
                    @error('invite_code')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mt-6">
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Gabung Tim
                    </button>
                </div>
            </form>

            <div class="mt-6 text-center text-sm text-gray-500 dark:text-gray-400">
                <p>Ingin membuat tim baru?</p>
                <a href="{{ route('student.teams.create') }}" class="font-medium text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300">
                    Buat Tim Baru
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
