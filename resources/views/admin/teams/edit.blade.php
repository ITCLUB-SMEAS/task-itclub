@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Edit Tim: {{ $team->name }}</h1>
        <a href="{{ route('admin.teams.show', $team->id) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 font-medium">
            &larr; Kembali ke Detail Tim
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
        <form action="{{ route('admin.teams.update', $team->id) }}" method="POST" enctype="multipart/form-data">
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
                    <label for="class_group" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Kelas <span class="text-red-600">*</span></label>
                    <input type="text" name="class_group" id="class_group" value="{{ old('class_group', $team->class_group) }}" required
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                    @error('class_group')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="max_members" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Jumlah Maksimum Anggota <span class="text-red-600">*</span></label>
                    <input type="number" name="max_members" id="max_members" value="{{ old('max_members', $team->max_members) }}" min="{{ $team->team_members_count }}" max="15" required
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
                    @error('is_active')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="invite_code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Kode Undangan</label>
                    <div class="flex">
                        <input type="text" name="invite_code" id="invite_code" value="{{ old('invite_code', $team->invite_code) }}"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-l-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                        <button type="button" id="regenerateCodeBtn" class="px-3 py-2 bg-gray-200 dark:bg-gray-600 rounded-r-md hover:bg-gray-300 dark:hover:bg-gray-500 focus:outline-none">
                            <svg class="h-5 w-5 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                        </button>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Kode undangan untuk bergabung ke tim ini</p>
                    @error('invite_code')
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

                <div class="md:col-span-2 flex justify-end mt-4">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Simpan Perubahan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const regenerateCodeBtn = document.getElementById('regenerateCodeBtn');
        const inviteCodeInput = document.getElementById('invite_code');

        if (regenerateCodeBtn && inviteCodeInput) {
            regenerateCodeBtn.addEventListener('click', function() {
                // Generate a random 8-character alphanumeric code
                const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
                let result = '';
                for (let i = 0; i < 8; i++) {
                    result += characters.charAt(Math.floor(Math.random() * characters.length));
                }
                inviteCodeInput.value = result;
            });
        }
    });
</script>
@endpush
@endsection
