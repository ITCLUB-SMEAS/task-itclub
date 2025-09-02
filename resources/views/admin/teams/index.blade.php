@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Manajemen Tim</h1>
        <a href="{{ route('admin.teams.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
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

    <div class="bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-lg mb-6">
        <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
            <div>
                <h2 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100">Daftar Tim</h2>
                <p class="mt-1 max-w-2xl text-sm text-gray-500 dark:text-gray-400">Semua tim yang terdaftar dalam sistem</p>
            </div>
            <div class="flex space-x-2">
                <input type="text" id="search" placeholder="Cari tim..." class="border rounded-md px-3 py-2 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <select id="filter-class" class="border rounded-md px-3 py-2 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Kelas</option>
                    <option value="X RPL 1">X RPL 1</option>
                    <option value="X RPL 2">X RPL 2</option>
                    <option value="XI RPL 1">XI RPL 1</option>
                    <option value="XI RPL 2">XI RPL 2</option>
                    <option value="XI TKJ 1">XI TKJ 1</option>
                    <option value="XII RPL 1">XII RPL 1</option>
                    <option value="XII RPL 2">XII RPL 2</option>
                </select>
            </div>
        </div>
        <div class="border-t border-gray-200 dark:border-gray-700">
            @if($teams->count() > 0)
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Nama Tim</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Kelas</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Ketua Tim</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Anggota</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($teams as $team)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                @if($team->logo)
                                <img class="h-10 w-10 rounded-full object-cover" src="{{ Storage::url($team->logo) }}" alt="{{ $team->name }}">
                                @else
                                <div class="h-10 w-10 rounded-full bg-blue-500 flex items-center justify-center text-white">
                                    {{ strtoupper(substr($team->name, 0, 1)) }}
                                </div>
                                @endif
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $team->name }}</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">Kode: {{ $team->team_code }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900 dark:text-gray-100">{{ $team->class_group }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900 dark:text-gray-100">{{ $team->leader->name ?? 'Belum ada' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900 dark:text-gray-100">{{ $team->members->count() }}/{{ $team->max_members }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $team->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $team->is_active ? 'Aktif' : 'Tidak Aktif' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <a href="{{ route('admin.teams.show', $team->id) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">Detail</a>
                                <a href="{{ route('admin.teams.edit', $team->id) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">Edit</a>
                                <form action="{{ route('admin.teams.destroy', $team->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300" onclick="return confirm('Anda yakin ingin menghapus tim ini?')">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="px-6 py-4">
                {{ $teams->links() }}
            </div>
            @else
            <div class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                <p>Belum ada tim yang dibuat</p>
                <a href="{{ route('admin.teams.create') }}" class="mt-2 inline-block text-blue-600 hover:text-blue-900 dark:text-blue-400">Buat Tim Baru</a>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('search');
        const filterClass = document.getElementById('filter-class');
        const tableRows = document.querySelectorAll('tbody tr');

        function filterRows() {
            const searchTerm = searchInput.value.toLowerCase();
            const classFilter = filterClass.value;

            tableRows.forEach(row => {
                const teamName = row.querySelector('td:first-child').textContent.toLowerCase();
                const teamClass = row.querySelector('td:nth-child(2)').textContent;

                const matchesSearch = teamName.includes(searchTerm);
                const matchesClass = !classFilter || teamClass.includes(classFilter);

                row.style.display = (matchesSearch && matchesClass) ? '' : 'none';
            });
        }

        searchInput.addEventListener('input', filterRows);
        filterClass.addEventListener('change', filterRows);
    });
</script>
@endsection
