<x-layout title="{{ $course->name }}">
    <!-- Course Header -->
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 mb-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <div class="flex items-center space-x-3">
                    <span class="px-3 py-1 font-mono text-sm font-semibold bg-blue-100 text-blue-800 rounded-lg">{{ $course->code }}</span>
                    <h1 class="text-3xl font-bold text-gray-900">{{ $course->name }}</h1>
                </div>
                <p class="text-sm text-gray-500 mt-2">
                    Dosen Pengampu: <strong class="text-gray-800">{{ $course->lecturer->name }}</strong> &bull; {{ $course->sks }} SKS
                </p>
            </div>

            <div class="flex items-center space-x-3">
                <a href="{{ route(auth()->user()->role === 'mahasiswa' ? 'student.courses.index' : 'lecturer.courses.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition">
                    &larr; Kembali
                </a>
                @can('update', $course)
                <a href="{{ route('courses.edit', $course) }}" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white text-sm font-medium rounded-lg transition">
                    Edit
                </a>
                @endcan
            </div>
        </div>

        <div class="mt-6 border-t border-gray-100 pt-4">
            <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-2">Deskripsi Mata Kuliah</h2>
            <!-- Safe escaping preventing XSS -->
            <div class="text-gray-700 leading-relaxed text-sm">
                {{ $course->description }}
            </div>
        </div>
    </div>

    <!-- Content Tabs: Materials & Assignments -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Materials Section -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-bold text-gray-900">Materi Pembelajaran</h2>
            </div>

            @can('create', App\Models\Material::class)
            <!-- Add Material Form -->
            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 mb-6">
                <h3 class="text-sm font-semibold text-gray-800 mb-3">Tambah Materi Baru</h3>
                <form action="{{ route('materials.store', $course) }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                    @csrf
                    <div>
                        <input type="text" name="title" placeholder="Judul Materi" required
                            class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm">
                    </div>
                    <div>
                        <textarea name="description" placeholder="Deskripsi singkat (opsional)" rows="2"
                            class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm"></textarea>
                    </div>
                    <div class="flex gap-4">
                        <label class="inline-flex items-center text-sm">
                            <input type="radio" name="type" value="file" checked class="text-blue-600">
                            <span class="ml-2">Unggah Berkas</span>
                        </label>
                        <label class="inline-flex items-center text-sm">
                            <input type="radio" name="type" value="link" class="text-blue-600">
                            <span class="ml-2">Tautan Luar</span>
                        </label>
                    </div>
                    <div>
                        <input type="file" name="file" class="w-full text-xs text-gray-500 border border-gray-300 rounded-lg p-2">
                    </div>
                    <div>
                        <input type="url" name="external_url" placeholder="https://..." class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm">
                    </div>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-xs font-semibold rounded-lg shadow hover:bg-blue-700 transition">
                        Simpan Materi
                    </button>
                </form>
            </div>
            @endcan

            <div class="space-y-3">
                @forelse ($course->materials as $material)
                <div class="p-4 rounded-lg border border-gray-100 hover:border-gray-200 bg-gray-50/50 flex justify-between items-center">
                    <div>
                        <div class="font-semibold text-gray-900 text-sm">{{ $material->title }}</div>
                        <p class="text-xs text-gray-500 mt-0.5">{{ $material->description }}</p>
                    </div>
                    <div class="flex items-center space-x-2">
                        @if($material->type === 'file')
                        <a href="{{ route('materials.download', $material) }}" class="px-3 py-1 bg-blue-100 text-blue-700 text-xs font-medium rounded-md hover:bg-blue-200">
                            Unduh
                        </a>
                        @else
                        <a href="{{ $material->external_url }}" target="_blank" class="px-3 py-1 bg-gray-100 text-gray-700 text-xs font-medium rounded-md hover:bg-gray-200">
                            Buka Link
                        </a>
                        @endif

                        @can('delete', $material)
                        <form action="{{ route('materials.destroy', $material) }}" method="POST" onsubmit="return confirm('Hapus materi ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 text-xs hover:underline">Hapus</button>
                        </form>
                        @endcan
                    </div>
                </div>
                @empty
                <p class="text-xs text-gray-400 text-center py-4">Belum ada materi pembelajaran.</p>
                @endforelse
            </div>
        </div>

        <!-- Assignments Section -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-bold text-gray-900">Tugas & Evaluasi</h2>
            </div>

            @can('create', App\Models\Course::class)
            <!-- Add Assignment Form -->
            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 mb-6">
                <h3 class="text-sm font-semibold text-gray-800 mb-3">Buat Tugas Baru</h3>
                <form action="{{ route('assignments.store', $course) }}" method="POST" class="space-y-3">
                    @csrf
                    <div>
                        <input type="text" name="title" placeholder="Judul Tugas" required
                            class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm">
                    </div>
                    <div>
                        <textarea name="description" placeholder="Petunjuk pengerjaan..." rows="2"
                            class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm"></textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="text-xs text-gray-600">Batas Waktu</label>
                            <input type="datetime-local" name="due_at" required class="w-full px-3 py-1.5 rounded-lg border border-gray-300 text-xs">
                        </div>
                        <div>
                            <label class="text-xs text-gray-600">Nilai Maksimal</label>
                            <input type="number" name="max_score" value="100" class="w-full px-3 py-1.5 rounded-lg border border-gray-300 text-xs">
                        </div>
                    </div>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-xs font-semibold rounded-lg shadow hover:bg-blue-700 transition">
                        Terbitkan Tugas
                    </button>
                </form>
            </div>
            @endcan

            <div class="space-y-3">
                @forelse ($course->assignments as $assignment)
                <div class="p-4 rounded-lg border border-gray-100 hover:border-gray-200 bg-gray-50/50 flex justify-between items-center">
                    <div>
                        <a href="{{ route('assignments.show', [$course, $assignment]) }}" class="font-semibold text-gray-900 text-sm hover:text-blue-600">
                            {{ $assignment->title }}
                        </a>
                        <p class="text-xs text-gray-500 mt-0.5">Deadline: {{ $assignment->due_at->format('d M Y H:i') }}</p>
                    </div>
                    <div class="flex items-center space-x-2">
                        <a href="{{ route('assignments.show', [$course, $assignment]) }}" class="px-3 py-1 bg-gray-200 text-gray-800 text-xs font-medium rounded-md hover:bg-gray-300">
                            Lihat Details
                        </a>
                    </div>
                </div>
                @empty
                <p class="text-xs text-gray-400 text-center py-4">Belum ada tugas yang diterbitkan.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-layout>
