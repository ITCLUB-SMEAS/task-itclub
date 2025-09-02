@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Edit Tim: {{ $team->name }}</h1>
        <a href="{{ route('student.teams.show', $team->id) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 font-medium">
            &larr; Kembali ke Detail Tim
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
        <form action="{{ route('student.teams.update', $team->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nama Tim <span class="text-red-600">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name', $team->name) }}" required
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                    @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="class_group" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Kelas</label>
                    <input type="text" id="class_group" value="{{ $team->class_group }}" readonly
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-gray-100 dark:bg-gray-600">
                    <p class="text-xs text-gray-500 mt-1">Kelas tim tidak dapat diubah</p>
                </div>

                <div>
                    <label for="max_members" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Jumlah Maksimum Anggota <span class="text-red-600">*</span></label>
                    <input type="number" name="max_members" id="max_members" value="{{ old('max_members', $team->max_members) }}" min="{{ $team->team_members_count }}" max="10" required
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                    <p class="text-xs text-gray-500 mt-1">Minimal harus sama dengan jumlah anggota saat ini ({{ $team->team_members_count }})</p>
                    @error('max_members')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="is_active" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status Tim</label>
                    <select name="is_active" id="is_active" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                        <option value="1" {{ $team->is_active ? 'selected' : '' }}>Aktif</option>
                        <option value="0" {{ !$team->is_active ? 'selected' : '' }}>Tidak Aktif</option>
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Tim yang tidak aktif tidak dapat menerima tugas baru</p>
                    @error('is_active')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Deskripsi Tim <span class="text-red-600">*</span></label>
                    <textarea name="description" id="description" rows="4" required
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">{{ old('description', $team->description) }}</textarea>
                    @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Logo Tim Saat Ini</label>
                    <div class="flex items-center">
                        @if($team->logo)
                            <div class="w-24 h-24 bg-gray-100 dark:bg-gray-700 rounded-md overflow-hidden mr-4">
                                <img src="{{ asset('storage/' . $team->logo) }}" alt="Logo Tim" class="w-full h-full object-cover">
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" name="remove_logo" id="remove_logo" value="1" class="mr-2">
                                <label for="remove_logo" class="text-sm text-gray-700 dark:text-gray-300">Hapus logo saat ini</label>
                            </div>
                        @else
                            <div class="w-24 h-24 bg-gray-100 dark:bg-gray-700 flex items-center justify-center rounded-md mr-4">
                                <span class="text-gray-400">Tidak ada logo</span>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="md:col-span-2">
                    <label for="logo" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Unggah Logo Baru</label>
                    <input type="file" name="logo" id="logo"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                    <p class="text-xs text-gray-500 mt-1">Opsional. Format gambar yang diterima: JPEG, JPG, PNG, GIF. Maksimum 2MB.</p>
                    @error('logo')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2 flex justify-end mt-4 space-x-3">
                    <button type="button" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded" id="deleteTeamBtn">
                        Hapus Tim
                    </button>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Simpan Perubahan
                    </button>
                </div>
            </div>
        </form>

        <!-- Delete Team Form -->
        <form id="deleteTeamForm" action="{{ route('student.teams.destroy', $team->id) }}" method="POST" class="hidden">
            @csrf
            @method('DELETE')
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const deleteTeamBtn = document.getElementById('deleteTeamBtn');
        const deleteTeamForm = document.getElementById('deleteTeamForm');

        if (deleteTeamBtn && deleteTeamForm) {
            deleteTeamBtn.addEventListener('click', function() {
                if (confirm('Apakah Anda yakin ingin menghapus tim ini? Semua data tim termasuk anggota dan tugas tim akan dihapus secara permanen. Tindakan ini tidak dapat dibatalkan.')) {
                    deleteTeamForm.submit();
                }
            });
        }
    });
</script>
@endpush
@endsection
