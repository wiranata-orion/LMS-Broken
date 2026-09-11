<x-layout title="Daftar Pengguna">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Daftar Pengguna</h1>
        <a href="{{ route('users.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded">Tambah Pengguna</a>
    </div>

    <table class="w-full bg-white rounded shadow overflow-hidden">
        <thead class="bg-gray-200 text-left">
            <tr>
                <th class="p-3">Nama</th>
                <th class="p-3">Email</th>
                <th class="p-3">NIM/NIP</th>
                <th class="p-3">Role</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($users as $user)
            <tr class="border-b">
                <td class="p-3">{{ $user->name }}</td>
                <td class="p-3">{{ $user->email }}</td>
                <td class="p-3">{{ $user->nim_nip ?? '-' }}</td>
                <td class="p-3"><span class="px-2 py-1 bg-gray-200 rounded text-sm">{{ $user->role }}</span></td>
            </tr>
            @endforeach
        </tbody>
    </table>
</x-layout>
