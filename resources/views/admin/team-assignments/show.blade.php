@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Detail Tugas Tim: {{ $teamAssignment->task->judul_tugas }}</h1>
        <a href="{{ route('admin.teams.show', $team->id) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 font-medium">
            &larr; Kembali ke Tim
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Assignment Details -->
        <div class="md:col-span-2">
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
                <div class="p-6">
                    <div class="flex items-start justify-between">
                        <div>
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">{{ $teamAssignment->task->judul_tugas }}</h2>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Diberikan pada: {{ $teamAssignment->created_at->format('d M Y, H:i') }}</p>
                        </div>
                        <div>
                            @if($teamAssignment->status === 'submitted')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                Diserahkan
                            </span>
                            @elseif($teamAssignment->status === 'in_progress')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                Sedang Dikerjakan
                            </span>
                            @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                Belum Dikerjakan
                            </span>
                            @endif
                        </div>
                    </div>

                    <div class="mt-4">
                        <div class="prose prose-blue max-w-none dark:prose-invert">
                            <h3 class="text-base font-medium text-gray-900 dark:text-white">Deskripsi Tugas:</h3>
                            <div class="text-gray-700 dark:text-gray-300 mt-2">
                                {!! nl2br(e($teamAssignment->task->deskripsi_tugas)) !!}
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <h3 class="text-base font-medium text-gray-900 dark:text-white">Detail:</h3>
                            <span class="text-sm text-gray-500 dark:text-gray-400">
                                Deadline: <span class="font-medium text-red-600 dark:text-red-400">{{ $teamAssignment->task->deadline->format('d M Y, H:i') }}</span>
                            </span>
                        </div>

                        <div class="mt-3 grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Nilai Maksimum:</p>
                                <p class="text-gray-900 dark:text-white font-medium">{{ $teamAssignment->task->nilai_maksimum }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Jenis Tugas:</p>
                                <p class="text-gray-900 dark:text-white font-medium">{{ $teamAssignment->task->jenis_tugas }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Kelas:</p>
                                <p class="text-gray-900 dark:text-white font-medium">{{ $teamAssignment->task->kelas }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Nilai Tim:</p>
                                <p class="text-gray-900 dark:text-white font-medium">
                                    @if($teamAssignment->grade)
                                        {{ $teamAssignment->grade }} / {{ $teamAssignment->task->nilai_maksimum }}
                                    @else
                                        Belum dinilai
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    @if($teamAssignment->task->file_pendukung)
                    <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <h3 class="text-base font-medium text-gray-900 dark:text-white mb-3">File Pendukung:</h3>
                        <div class="bg-gray-50 dark:bg-gray-700 p-3 rounded-md flex items-center justify-between">
                            <div class="flex items-center">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ basename($teamAssignment->task->file_pendukung) }}</span>
                            </div>
                            <a href="{{ asset('storage/' . $teamAssignment->task->file_pendukung) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-sm" target="_blank" download>
                                Download
                            </a>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Team Submission -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden mt-6">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Pengumpulan Tim</h3>
                </div>
                <div class="p-6">
                    @if($teamAssignment->submission_file)
                        <div class="bg-green-50 dark:bg-green-900/30 p-4 rounded-md mb-4">
                            <div class="flex">
                                <svg class="h-5 w-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <div class="ml-3">
                                    <p class="text-sm text-green-700 dark:text-green-300">Tim ini telah mengumpulkan tugas pada {{ $teamAssignment->updated_at->format('d M Y, H:i') }}</p>
                                    <p class="text-sm font-medium text-green-700 dark:text-green-300 mt-1">File yang diunggah: <a href="{{ asset('storage/' . $teamAssignment->submission_file) }}" class="underline" target="_blank">{{ basename($teamAssignment->submission_file) }}</a></p>

                                    @if($teamAssignment->comment)
                                        <p class="text-sm text-green-700 dark:text-green-300 mt-2">Komentar Tim: {{ $teamAssignment->comment }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <form action="{{ route('admin.team-assignments.grade', ['team' => $team->id, 'assignment' => $teamAssignment->id]) }}" method="POST" class="space-y-4">
                            @csrf
                            <div>
                                <label for="grade" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nilai (0-{{ $teamAssignment->task->nilai_maksimum }})</label>
                                <input type="number" name="grade" id="grade" min="0" max="{{ $teamAssignment->task->nilai_maksimum }}" value="{{ old('grade', $teamAssignment->grade) }}" required
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                                @error('grade')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="feedback" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Feedback</label>
                                <textarea name="feedback" id="feedback" rows="4"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">{{ old('feedback', $teamAssignment->feedback) }}</textarea>
                                @error('feedback')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="pt-2 flex justify-end">
                                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                    Simpan Nilai dan Feedback
                                </button>
                            </div>
                        </form>
                    @else
                        @if($teamAssignment->task->deadline->isPast())
                            <div class="bg-red-50 dark:bg-red-900/30 p-4 rounded-md">
                                <div class="flex">
                                    <svg class="h-5 w-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                    </svg>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-red-700 dark:text-red-300">Batas waktu pengumpulan telah berakhir</p>
                                        <p class="text-sm text-red-700 dark:text-red-300 mt-1">Tim ini tidak mengumpulkan tugas sebelum deadline.</p>
                                    </div>
                                </div>
                            </div>

                            <form action="{{ route('admin.team-assignments.grade', ['team' => $team->id, 'assignment' => $teamAssignment->id]) }}" method="POST" class="space-y-4 mt-4">
                                @csrf
                                <div>
                                    <label for="grade" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nilai (0-{{ $teamAssignment->task->nilai_maksimum }})</label>
                                    <input type="number" name="grade" id="grade" min="0" max="{{ $teamAssignment->task->nilai_maksimum }}" value="{{ old('grade', $teamAssignment->grade ?? 0) }}" required
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                                    @error('grade')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="feedback" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Feedback</label>
                                    <textarea name="feedback" id="feedback" rows="4"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">{{ old('feedback', $teamAssignment->feedback ?? 'Tugas tidak dikumpulkan sebelum deadline.') }}</textarea>
                                    @error('feedback')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="pt-2 flex justify-end">
                                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                        Simpan Nilai dan Feedback
                                    </button>
                                </div>
                            </form>
                        @else
                            <div class="bg-yellow-50 dark:bg-yellow-900/30 p-4 rounded-md">
                                <div class="flex">
                                    <svg class="h-5 w-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-yellow-700 dark:text-yellow-300">Menunggu Pengumpulan</p>
                                        <p class="text-sm text-yellow-700 dark:text-yellow-300 mt-1">Tim ini belum mengumpulkan tugas. Deadline: {{ $teamAssignment->task->deadline->format('d M Y, H:i') }}.</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>

        <!-- Team Info Sidebar -->
        <div class="md:col-span-1">
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Informasi Tim</h3>
                </div>
                <div class="p-6">
                    <div class="flex items-center mb-4">
                        @if($team->logo)
                            <img src="{{ asset('storage/' . $team->logo) }}" alt="{{ $team->name }}" class="h-10 w-10 rounded-full object-cover mr-3">
                        @else
                            <div class="h-10 w-10 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center text-blue-600 dark:text-blue-300 font-bold mr-3">
                                {{ strtoupper(substr($team->name, 0, 2)) }}
                            </div>
                        @endif
                        <div>
                            <h4 class="text-base font-medium text-gray-900 dark:text-white">{{ $team->name }}</h4>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $team->class_group }}</p>
                        </div>
                    </div>

                    <div class="mt-4">
                        <h5 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Anggota Tim:</h5>
                        <ul class="space-y-2">
                            @foreach($team->team_members as $member)
                            <li class="flex items-center">
                                <div class="h-6 w-6 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center text-gray-600 dark:text-gray-300 text-xs font-medium mr-2">
                                    {{ strtoupper(substr($member->user->name, 0, 2)) }}
                                </div>
                                <span class="text-sm text-gray-700 dark:text-gray-300">
                                    {{ $member->user->name }}
                                    @if($member->is_leader)
                                    <span class="ml-1 text-xs text-gray-500 dark:text-gray-400">(Ketua)</span>
                                    @endif
                                </span>
                            </li>
                            @endforeach
                        </ul>
                    </div>

                    <div class="mt-5 pt-5 border-t border-gray-200 dark:border-gray-700">
                        <a href="{{ route('admin.teams.show', $team->id) }}" class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                            Lihat Detail Tim
                        </a>
                    </div>
                </div>
            </div>

            <!-- Deadline Countdown -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden mt-6">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Status Deadline</h3>
                </div>
                <div class="p-6">
                    @if($teamAssignment->task->deadline->isPast())
                        <div class="bg-red-50 dark:bg-red-900/30 rounded-md p-4">
                            <div class="flex">
                                <svg class="h-5 w-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-red-700 dark:text-red-300">Deadline Terlewat</p>
                                    <p class="text-xs text-red-700 dark:text-red-300 mt-1">Tugas ini telah melewati batas waktu pengumpulan.</p>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center">
                            <div class="text-2xl font-bold text-red-600 dark:text-red-400" id="countdown">
                                Menghitung...
                            </div>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Waktu tersisa hingga deadline</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Actions -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden mt-6">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Tindakan</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-3">
                        <form action="{{ route('admin.team-assignments.destroy', ['team' => $team->id, 'assignment' => $teamAssignment->id]) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-3 rounded text-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus tugas tim ini?')">
                                Hapus Tugas Tim
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if(!$teamAssignment->task->deadline->isPast())
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const countdownElement = document.getElementById('countdown');
        const deadline = new Date("{{ $teamAssignment->task->deadline }}").getTime();

        function updateCountdown() {
            const now = new Date().getTime();
            const distance = deadline - now;

            if (distance < 0) {
                countdownElement.innerHTML = "Deadline telah berakhir";
                return;
            }

            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

            let countdownText = "";
            if (days > 0) countdownText += days + "h ";
            countdownText += hours + "j " + minutes + "m " + seconds + "d";

            countdownElement.innerHTML = countdownText;
        }

        updateCountdown();
        setInterval(updateCountdown, 1000);
    });
</script>
@endpush
@endif
@endsection
