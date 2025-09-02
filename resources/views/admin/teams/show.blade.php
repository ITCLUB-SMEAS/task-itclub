@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Detail Tim: {{ $team->name }}</h1>
        <a href="{{ route('admin.teams.index') }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 font-medium">
            &larr; Kembali ke Daftar Tim
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Tim Info Card -->
        <div class="md:col-span-1">
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
                <div class="p-6">
                    <div class="flex justify-center mb-4">
                        @if($team->logo)
                            <img src="{{ asset('storage/' . $team->logo) }}" alt="{{ $team->name }}" class="h-32 w-32 rounded-full object-cover border-4 border-blue-100 dark:border-blue-900">
                        @else
                            <div class="h-32 w-32 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center text-blue-600 dark:text-blue-300 text-3xl font-bold border-4 border-blue-100 dark:border-blue-900">
                                {{ strtoupper(substr($team->name, 0, 2)) }}
                            </div>
                        @endif
                    </div>
                    <h2 class="text-xl font-bold text-center text-gray-900 dark:text-white mb-2">{{ $team->name }}</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 text-center mb-4">{{ $team->class_group }}</p>

                    <div class="border-t border-gray-200 dark:border-gray-700 pt-4 mt-2">
                        <div class="text-sm mb-3">
                            <span class="font-medium text-gray-500 dark:text-gray-400">Status Tim:</span>
                            @if($team->is_active)
                                <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                    Aktif
                                </span>
                            @else
                                <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                    Tidak Aktif
                                </span>
                            @endif
                        </div>

                        <div class="text-sm mb-3">
                            <span class="font-medium text-gray-500 dark:text-gray-400">Dibuat pada:</span>
                            <span class="ml-2 text-gray-700 dark:text-gray-300">{{ $team->created_at->format('d M Y') }}</span>
                        </div>

                        <div class="text-sm mb-3">
                            <span class="font-medium text-gray-500 dark:text-gray-400">Jumlah Anggota:</span>
                            <span class="ml-2 text-gray-700 dark:text-gray-300">{{ $team->team_members_count ?? $team->team_members->count() }} / {{ $team->max_members }}</span>
                        </div>

                        <div class="text-sm mb-3">
                            <span class="font-medium text-gray-500 dark:text-gray-400">Kode Undangan:</span>
                            <span class="ml-2 font-mono text-gray-700 dark:text-gray-300">{{ $team->invite_code }}</span>
                        </div>
                    </div>

                    <div class="mt-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Deskripsi Tim:</h3>
                        <p class="text-gray-700 dark:text-gray-300 text-sm">{{ $team->description }}</p>
                    </div>
                </div>

                <div class="bg-gray-50 dark:bg-gray-700 px-6 py-4">
                    <div class="flex flex-col space-y-2">
                        <form action="{{ route('admin.teams.toggle-status', $team->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full text-sm {{ $team->is_active ? 'bg-red-600 hover:bg-red-700' : 'bg-green-600 hover:bg-green-700' }} text-white font-medium py-2 px-4 rounded">
                                {{ $team->is_active ? 'Nonaktifkan Tim' : 'Aktifkan Tim' }}
                            </button>
                        </form>

                        <a href="{{ route('admin.teams.edit', $team->id) }}" class="text-sm bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded text-center">
                            Edit Tim
                        </a>

                        <form action="{{ route('admin.teams.destroy', $team->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus tim ini? Semua data tim termasuk anggota dan tugas tim akan dihapus secara permanen.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full text-sm bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-4 rounded">
                                Hapus Tim
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Team Members -->
        <div class="md:col-span-2">
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Anggota Tim</h3>
                </div>
                <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($team->team_members as $member)
                    <li class="px-6 py-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="h-10 w-10 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center text-blue-600 dark:text-blue-300 font-medium">
                                    {{ strtoupper(substr($member->user->name, 0, 2)) }}
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $member->user->name }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $member->user->email }}</p>
                                </div>
                            </div>
                            <div class="flex items-center">
                                @if($member->is_leader)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200 mr-2">
                                    Ketua Tim
                                </span>
                                @endif

                                <form action="{{ route('admin.team-members.remove', ['team' => $team->id, 'member' => $member->id]) }}" method="POST" class="ml-2">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300" onclick="return confirm('Apakah Anda yakin ingin menghapus anggota ini dari tim?')">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </li>
                    @endforeach

                    @if($team->team_members->count() < $team->max_members)
                    <li class="px-6 py-4">
                        <form action="{{ route('admin.team-members.add', $team->id) }}" method="POST" class="space-y-4">
                            @csrf
                            <div>
                                <label for="user_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tambah Anggota Baru</label>
                                <select name="user_id" id="user_id" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                                    <option value="">-- Pilih Siswa --</option>
                                    @foreach($availableStudents as $student)
                                    <option value="{{ $student->id }}">{{ $student->name }} ({{ $student->kelas }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" name="is_leader" id="is_leader" value="1" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <label for="is_leader" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                                    Jadikan sebagai ketua tim
                                </label>
                            </div>
                            <div>
                                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium py-2 px-4 rounded">
                                    Tambahkan Anggota
                                </button>
                            </div>
                        </form>
                    </li>
                    @endif
                </ul>
            </div>

            <!-- Team Assignments -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden mt-6">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Tugas Tim</h3>
                    <a href="{{ route('admin.team-assignments.create', $team->id) }}" class="inline-flex items-center px-3 py-1 border border-transparent text-sm leading-5 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-500 focus:outline-none focus:border-blue-700 focus:shadow-outline-blue active:bg-blue-700 transition ease-in-out duration-150">
                        <svg class="-ml-1 mr-1 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Tambah Tugas
                    </a>
                </div>
                <div class="p-6">
                    @if($teamAssignments && $teamAssignments->count() > 0)
                    <div class="space-y-4">
                        @foreach($teamAssignments as $assignment)
                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h4 class="text-base font-medium text-gray-900 dark:text-white">{{ $assignment->task->judul_tugas }}</h4>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Deadline: {{ $assignment->task->deadline->format('d M Y, H:i') }}</p>
                                </div>
                                <div>
                                    @if($assignment->status === 'submitted')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                        Diserahkan
                                    </span>
                                    @elseif($assignment->status === 'in_progress')
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
                            <div class="mt-3 flex space-x-3">
                                <a href="{{ route('admin.team-assignments.show', ['team' => $team->id, 'assignment' => $assignment->id]) }}" class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 font-medium">
                                    Lihat Detail
                                </a>
                                <form action="{{ route('admin.team-assignments.destroy', ['team' => $team->id, 'assignment' => $assignment->id]) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-sm text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 font-medium" onclick="return confirm('Apakah Anda yakin ingin menghapus tugas tim ini?')">
                                        Hapus Tugas
                                    </button>
                                </form>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Tidak ada tugas tim</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Belum ada tugas yang diberikan kepada tim ini.
                        </p>
                        <div class="mt-6">
                            <a href="{{ route('admin.team-assignments.create', $team->id) }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Tambahkan Tugas Tim
                            </a>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
