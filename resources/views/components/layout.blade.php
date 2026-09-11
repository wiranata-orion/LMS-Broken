<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'KampusLMS' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 text-gray-800">
    <nav class="bg-blue-600 text-white p-4 shadow">
        <div class="container mx-auto flex justify-between items-center">
            <a href="/" class="font-bold text-xl">KampusLMS</a>
            <div class="space-x-4">
                <a href="{{ route('courses.index') }}" class="hover:underline">Mata Kuliah</a>
                <a href="{{ route('users.index') }}" class="hover:underline">Pengguna</a>
            </div>
        </div>
    </nav>
    <main class="container mx-auto p-6">
        {{ $slot }}
    </main>
</body>
</html>
