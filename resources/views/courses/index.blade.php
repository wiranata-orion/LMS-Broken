<x-layout title="Daftar Mata Kuliah">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Daftar Mata Kuliah</h1>
        <a href="{{ route('courses.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded">Tambah Mata Kuliah</a>
    </div>

    <table class="w-full bg-white rounded shadow overflow-hidden">
        <thead class="bg-gray-200 text-left">
            <tr>
                <th class="p-3">Kode</th>
                <th class="p-3">Nama Mata Kuliah</th>
                <th class="p-3">SKS</th>
                <th class="p-3">Dosen</th>
                <th class="p-3">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($courses as $course)
            <tr class="border-b">
                <td class="p-3">{{ $course->code }}</td>
                <td class="p-3">
                    <a href="{{ route('courses.show', $course->id) }}" class="text-blue-600 font-semibold hover:underline">
                        {{ $course->name }}
                    </a>
                </td>
                <td class="p-3">{{ $course->sks }}</td>
                <td class="p-3">{{ $course->lecturer->name ?? 'N/A' }}</td>
                <td class="p-3 space-x-2 flex items-center">
                    <a href="{{ route('courses.show', $course->id) }}" class="text-gray-600 hover:underline">Detail</a>
                    <form action="{{ route('courses.destroy', $course->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:underline">Hapus</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="p-4 text-center text-gray-500">Belum ada data mata kuliah.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</x-layout>