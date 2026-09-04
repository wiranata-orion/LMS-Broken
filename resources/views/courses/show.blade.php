<x-layout title="Detail Mata Kuliah">
    <div class="bg-white p-6 rounded shadow">
        <h1 class="text-3xl font-bold mb-2">{{ $course['name'] }} ({{ $course['code'] }})</h1>
        <p class="text-gray-600 mb-4">Dosen Pengampu: {{ $course['lecturer'] }} | SKS: {{ $course['sks'] }}</p>
        
        <div class="border-t pt-4">
            <h2 class="text-xl font-semibold mb-2">Deskripsi Mata Kuliah</h2>
            <div class="prose">
             {{ $course['description'] }}
            </div>
        </div>

        <div class="mt-6">
            <a href="{{ route('courses.index') }}" class="text-blue-600 hover:underline">&larr; Kembali ke daftar</a>
        </div>
    </div>
</x-layout>
