<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'KampusLMS' }} — Learning Management System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 text-gray-900 min-h-screen flex flex-col justify-between">
    <div>
        <!-- Navigation Header -->
        <nav class="bg-slate-900 text-white shadow-lg border-b border-slate-800">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    <div class="flex items-center space-x-8">
                        <a href="{{ route('dashboard') }}" class="flex items-center space-x-2 font-bold text-xl tracking-tight text-blue-400">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                            </svg>
                            <span>KampusLMS</span>
                        </a>

                        @auth
                        <div class="hidden md:flex space-x-4">
                            <a href="{{ route('dashboard') }}" class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('dashboard') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                                Dashboard
                            </a>
                            <a href="{{ route(auth()->user()->role === 'mahasiswa' ? 'student.courses.index' : 'lecturer.courses.index') }}" class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('*.courses.index') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                                Mata Kuliah
                            </a>
                            @if(auth()->user()->role === 'admin')
                            <a href="{{ route('users.index') }}" class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('users.*') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                                Pengguna
                            </a>
                            @endif
                            <a href="{{ route('notifications.index') }}" class="px-3 py-2 rounded-md text-sm font-medium relative {{ request()->routeIs('notifications.*') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                                Notifikasi
                                @if(auth()->user()->unreadNotifications->count() > 0)
                                <span class="ml-1 px-2 py-0.5 text-xs font-bold bg-red-500 text-white rounded-full">
                                    {{ auth()->user()->unreadNotifications->count() }}
                                </span>
                                @endif
                            </a>
                        </div>
                        @endauth
                    </div>

                    <div class="flex items-center space-x-4">
                        @auth
                        <div class="flex items-center space-x-3">
                            <div class="text-right hidden sm:block">
                                <div class="text-sm font-semibold text-white">{{ auth()->user()->name }}</div>
                                <div class="text-xs text-slate-400 capitalize">{{ auth()->user()->role }}</div>
                            </div>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="px-3 py-1.5 text-sm font-medium bg-slate-800 hover:bg-red-600 text-slate-300 hover:text-white rounded-lg transition border border-slate-700">
                                    Keluar
                                </button>
                            </form>
                        </div>
                        @else
                        <a href="{{ route('login') }}" class="px-4 py-2 text-sm font-medium bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition shadow-sm">
                            Masuk
                        </a>
                        @endauth
                    </div>
                </div>
            </div>
        </nav>

        <!-- Flash Messages -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            @if(session('success'))
            <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 border border-green-200 flex justify-between items-center" role="alert">
                <div class="flex items-center space-x-2">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
            @endif

            @if(session('error'))
            <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 border border-red-200 flex justify-between items-center" role="alert">
                <div class="flex items-center space-x-2">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    <span>{{ session('error') }}</span>
                </div>
            </div>
            @endif
        </div>

        <!-- Main Content -->
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            {{ $slot }}
        </main>
    </div>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 py-6 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-sm text-gray-500">
            &copy; {{ date('Y') }} KampusLMS — Sistem Informasi Akademik & Pembelajaran
        </div>
    </footer>
</body>
</html>
