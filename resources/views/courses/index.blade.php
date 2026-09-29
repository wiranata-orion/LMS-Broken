<x-layout title="Daftar Mata Kuliah">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Daftar Mata Kuliah</h1>
            <p class="text-sm text-gray-500">Kelola dan lihat seluruh mata kuliah yang tersedia di KampusLMS</p>
        </div>

        @can('create', App\Models\Course::class)
        <a href="{{ route('courses.create') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium text-sm rounded-lg shadow transition flex items-center space-x-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>Tambah Mata Kuliah</span>
        </a>
        @endcan
    </div>

    <!-- Filter Form -->
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 mb-6">
        <form action="{{ route(auth()->user()->role === 'mahasiswa' ? 'student.courses.index' : 'lecturer.courses.index') }}" method="GET" class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau kode mata kuliah..."
                    class="w-full px-4 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <select name="status" class="w-full px-4 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500">
                    <option value="active" {{ request('status', 'active') === 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="archived" {{ request('status') === 'archived' ? 'selected' : '' }}>Arsip</option>
                </select>
            </div>
            <button type="submit" class="px-5 py-2 bg-gray-800 hover:bg-gray-900 text-white text-sm font-medium rounded-lg transition">
                Cari
            </button>
        </form>
    </div>

    <!-- Course Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-50 text-xs uppercase font-semibold text-gray-500 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4">Kode</th>
                        <th class="px-6 py-4">Mata Kuliah</th>
                        <th class="px-6 py-4">SKS</th>
                        <th class="px-6 py-4">Dosen Pengampu</th>
                        <th class="px-6 py-4">Mahasiswa</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($courses as $course)
                    <tr class="hover:bg-gray-50/50 transition">
                        <td class="px-6 py-4 font-mono font-medium text-blue-600">{{ $course->code }}</td>
                        <td class="px-6 py-4">
                            <a href="{{ route('courses.show', $course) }}" class="font-semibold text-gray-900 hover:text-blue-600 transition">
                                {{ $course->name }}
                            </a>
                        </td>
                        <td class="px-6 py-4">{{ $course->sks }} SKS</td>
                        <td class="px-6 py-4">{{ $course->lecturer->name ?? '-' }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2.5 py-1 text-xs font-semibold bg-blue-50 text-blue-700 rounded-full">
                                {{ $course->students_count }} Mahasiswa
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right space-x-3">
                            <a href="{{ route('courses.show', $course) }}" class="text-blue-600 hover:text-blue-800 font-medium">Detail</a>

                            @can('update', $course)
                            <a href="{{ route('courses.edit', $course) }}" class="text-amber-600 hover:text-amber-800 font-medium">Edit</a>
                            @endcan

                            @can('delete', $course)
                            <form action="{{ route('courses.destroy', $course) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus mata kuliah ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 font-medium">Hapus</button>
                            </form>
                            @endcan
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-400">Tidak ada data mata kuliah yang ditemukan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($courses->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $courses->links() }}
        </div>
        @endif
    </div>
</x-layout>
