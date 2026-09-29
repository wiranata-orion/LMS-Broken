<x-layout title="404 — Halaman Tidak Ditemukan">
    <div class="text-center py-12">
        <h1 class="text-6xl font-bold text-red-500 mb-4">404</h1>
        <p class="text-xl font-semibold mb-2">Halaman Tidak Ditemukan</p>
        <p class="text-gray-600 mb-6">Maaf, halaman atau data yang Anda cari tidak ada.</p>
        <a href="{{ auth()->check() ? route(auth()->user()->role === 'mahasiswa' ? 'student.courses.index' : 'lecturer.courses.index') : route('login') }}" class="bg-blue-600 text-white px-6 py-2 rounded shadow hover:bg-blue-700">
            Kembali ke Daftar Mata Kuliah
        </a>
    </div>
</x-layout>
