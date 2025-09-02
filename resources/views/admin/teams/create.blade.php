@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Buat Tim Baru</h1>
        <a href="{{ route('admin.teams.index') }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 font-medium">
            &larr; Kembali ke Daftar Tim
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
        <form action="{{ route('admin.teams.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nama Tim <span class="text-red-600">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                    @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="class_group" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Kelas <span class="text-red-600">*</span></label>
                    <select name="class_group" id="class_group" required
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                        <option value="">Pilih Kelas</option>
                        <option value="X RPL 1" {{ old('class_group') == 'X RPL 1' ? 'selected' : '' }}>X RPL 1</option>
                        <option value="X RPL 2" {{ old('class_group') == 'X RPL 2' ? 'selected' : '' }}>X RPL 2</option>
                        <option value="XI RPL 1" {{ old('class_group') == 'XI RPL 1' ? 'selected' : '' }}>XI RPL 1</option>
                        <option value="XI RPL 2" {{ old('class_group') == 'XI RPL 2' ? 'selected' : '' }}>XI RPL 2</option>
                        <option value="XI TKJ 1" {{ old('class_group') == 'XI TKJ 1' ? 'selected' : '' }}>XI TKJ 1</option>
                        <option value="XII RPL 1" {{ old('class_group') == 'XII RPL 1' ? 'selected' : '' }}>XII RPL 1</option>
                        <option value="XII RPL 2" {{ old('class_group') == 'XII RPL 2' ? 'selected' : '' }}>XII RPL 2</option>
                    </select>
                    @error('class_group')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="leader_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Ketua Tim</label>
                    <select name="leader_id" id="leader_id"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                        <option value="">Pilih Ketua Tim</option>
                        @foreach($students as $student)
                        <option value="{{ $student->id }}" {{ old('leader_id') == $student->id ? 'selected' : '' }}>
                            {{ $student->name }} ({{ $student->kelas ?? 'Tidak ada kelas' }})
                        </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Jika tidak dipilih, Anda akan menjadi ketua tim</p>
                    @error('leader_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="max_members" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Jumlah Maksimum Anggota <span class="text-red-600">*</span></label>
                    <input type="number" name="max_members" id="max_members" value="{{ old('max_members', 5) }}" min="2" max="10" required
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                    @error('max_members')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Deskripsi Tim <span class="text-red-600">*</span></label>
                    <textarea name="description" id="description" rows="4" required
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">{{ old('description') }}</textarea>
                    @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="logo" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Logo Tim</label>
                    <input type="file" name="logo" id="logo"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                    <p class="text-xs text-gray-500 mt-1">Opsional. Format gambar yang diterima: JPEG, JPG, PNG, GIF. Maksimum 2MB.</p>
                    @error('logo')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2 flex justify-end mt-4">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Buat Tim
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
