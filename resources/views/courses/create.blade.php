<x-layout title="Tambah Mata Kuliah">
    <div class="max-w-2xl mx-auto bg-white p-8 rounded-xl shadow-sm border border-gray-100">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">Tambah Mata Kuliah Baru</h1>

        <form action="{{ route('courses.store') }}" method="POST" class="space-y-5">
            @csrf

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="code" class="block text-sm font-medium text-gray-700 mb-1">Kode Mata Kuliah</label>
                    <input type="text" name="code" id="code" value="{{ old('code') }}" required placeholder="SI2514029"
                        class="w-full px-4 py-2.5 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500">
                    @error('code')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="sks" class="block text-sm font-medium text-gray-700 mb-1">Jumlah SKS</label>
                    <input type="number" name="sks" id="sks" value="{{ old('sks', 3) }}" min="1" max="6" required
                        class="w-full px-4 py-2.5 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500">
                    @error('sks')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Mata Kuliah</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required placeholder="Pemrograman Web Lanjut"
                    class="w-full px-4 py-2.5 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500">
                @error('name')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="lecturer_id" class="block text-sm font-medium text-gray-700 mb-1">Dosen Pengampu</label>
                <select name="lecturer_id" id="lecturer_id" required class="w-full px-4 py-2.5 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500">
                    <option value="">-- Pilih Dosen --</option>
                    @foreach($lecturers as $dosen)
                    <option value="{{ $dosen->id }}" {{ old('lecturer_id') == $dosen->id ? 'selected' : '' }}>
                        {{ $dosen->name }} ({{ $dosen->nim_nip }})
                    </option>
                    @endforeach
                </select>
                @error('lecturer_id')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status Publikasi</label>
                <select name="status" id="status" required class="w-full px-4 py-2.5 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500">
                    <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="draft" {{ old('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="archived" {{ old('status') === 'archived' ? 'selected' : '' }}>Arsip</option>
                </select>
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                <textarea name="description" id="description" rows="4" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500">{{ old('description') }}</textarea>
            </div>

            <div class="flex justify-end space-x-3 pt-4 border-t border-gray-100">
                <a href="{{ route('courses.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 text-sm rounded-lg hover:bg-gray-200">Batal</a>
                <button type="submit" class="px-5 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg shadow hover:bg-blue-700">Simpan</button>
            </div>
        </form>
    </div>
</x-layout>
