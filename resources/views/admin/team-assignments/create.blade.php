@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Tambah Tugas untuk Tim: {{ $team->name }}</h1>
        <a href="{{ route('admin.teams.show', $team->id) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 font-medium">
            &larr; Kembali ke Detail Tim
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
        <form action="{{ route('admin.team-assignments.store', $team->id) }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label for="task_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Pilih Tugas <span class="text-red-600">*</span></label>
                    <select name="task_id" id="task_id" required
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                        <option value="">-- Pilih Tugas --</option>
                        @foreach($availableTasks as $task)
                        <option value="{{ $task->id }}">{{ $task->judul_tugas }} ({{ $task->kelas }}) - Deadline: {{ $task->deadline->format('d M Y, H:i') }}</option>
                        @endforeach
                    </select>
                    @error('task_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2 pt-4">
                    <div class="bg-yellow-50 dark:bg-yellow-900/30 rounded-lg p-4 text-sm text-yellow-700 dark:text-yellow-300">
                        <div class="flex">
                            <svg class="h-5 w-5 text-yellow-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div>
                                <p><strong>Catatan:</strong> Tugas yang dipilih akan diberikan ke semua anggota tim secara serentak. Nilai yang diberikan akan diterapkan ke semua anggota tim.</p>
                                <p class="mt-1">Jika ingin menambahkan tugas baru, silakan buat tugas baru terlebih dahulu di menu Manajemen Tugas.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="md:col-span-2 flex justify-end mt-4">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Tambahkan Tugas ke Tim
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
