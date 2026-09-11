<x-layout title="Tambah Pengguna">
    <div class="bg-white p-6 rounded shadow max-w-lg mx-auto">
        <h1 class="text-2xl font-bold mb-4">Tambah Pengguna</h1>
        <form action="{{ route('users.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block mb-1 font-medium">Nama</label>
                <input type="text" name="name" class="w-full border rounded p-2" required>
            </div>
            <div>
                <label class="block mb-1 font-medium">Email</label>
                <input type="email" name="email" class="w-full border rounded p-2" required>
            </div>
            <div>
                <label class="block mb-1 font-medium">Password</label>
                <input type="password" name="password" class="w-full border rounded p-2" required>
            </div>
            <div>
                <label class="block mb-1 font-medium">NIM / NIP</label>
                <input type="text" name="nim_nip" class="w-full border rounded p-2">
            </div>
            <div class="flex justify-end space-x-2">
                <a href="{{ route('users.index') }}" class="px-4 py-2 border rounded">Batal</a>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Simpan</button>
            </div>
        </form>
    </div>
</x-layout>
