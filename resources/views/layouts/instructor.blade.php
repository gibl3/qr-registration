<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Add Material Icons -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <!-- Alpine.js for dropdown -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    @vite(['resources/css/app.css'])
    @stack('scripts')
    <title>@yield('title', 'QR Attendance')</title>
</head>

<body class="bg-neutral-50 min-h-screen flex flex-col overflow-hidden">
    <!-- Top Navigation Bar -->
    <nav class="shadow-xs w-full top-0 z-50 px-8">
        <div class="flex justify-between mx-auto py-2">
            <div class="flex items-center">
                <h2 class="text-2xl font-bold text-emerald-600">QR Attendance</h2>
            </div>

            <div class="flex items-center space-x-4">
                <div class="text-sm text-gray-500">Welcome back, {{ Auth::user()->name }}!</div>
                @auth
                <!-- Dropdown for settings and logout -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" @keydown.escape="open = false" class="btn-text shadow-xs rounded-full p-2.5 bg-neutral-200/60 hover:bg-neutral-200 focus:outline-none" aria-haspopup="true" :aria-expanded="open">
                        <span class="material-symbols-rounded text-neutral-600">settings</span>
                    </button>

                    <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-neutral-50 rounded-md shadow-lg z-50 py-1 border border-neutral-200" x-cloak>
                        <a href="{{ route('instructor.settings.edit') }}" class="block px-4 py-2 text-sm text-neutral-700 hover:bg-neutral-200/60{{ Route::is('instructor.settings.edit') ? 'bg-neutral-100' : '' }}">
                            Change password
                        </a>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="flex justify-start items-center w-full text-left px-4 py-2 text-sm text-neutral-700 hover:bg-neutral-200/60 gap-x-2 cursor-pointer">
                                Logout
                                <span class="material-symbols-rounded text-neutral-600">Logout</span>
                            </button>
                        </form>
                    </div>
                </div>
                @else
                <a href="{{ route('login') }}" class="bg-emerald-600 text-white px-4 py-2 rounded-md hover:bg-emerald-700">Login</a>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="flex">
        @auth
        <!-- Left Sidebar -->
        <div class="w-64 h-[calc(100vh-4rem)] bg-white shadow-md py-4 px-8 overflow-y-auto">
            <div class="space-y-2">
                <a href="{{ route('instructor.index') }}" class="flex items-center space-x-3 p-3 hover:bg-neutral-100 rounded-lg transition-colors @yield('dashboard-bg', '')">
                    <span class="material-symbols-rounded text-emerald-600">dashboard</span>
                    <span class="font-medium">Dashboard</span>
                </a>

                <a href="{{ route('instructor.scan.index') }}" class="flex items-center space-x-3 p-3 hover:bg-neutral-100 rounded-lg transition-colors @yield('scan-bg', '')">
                    <span class="material-symbols-rounded text-emerald-600">qr_code_scanner</span>
                    <span class="font-medium">Scan QR</span>
                </a>

                <a href="{{ route('instructor.subjects.index') }}" class="flex items-center space-x-3 p-3 hover:bg-neutral-100 rounded-lg transition-colors @yield('subjects-bg', '')">
                    <span class="material-symbols-rounded text-emerald-600">subject</span>
                    <span class="font-medium">Subjects</span>
                </a>

                <a href="{{ route('instructor.attendance.index') }}" class="flex items-center space-x-3 p-3 hover:bg-neutral-100 rounded-lg transition-colors @yield('attendance-bg', '')">
                    <span class="material-symbols-rounded text-emerald-600">list_alt</span>
                    <span class="font-medium">Attendance</span>
                </a>

                <a href="{{ route('instructor.students.index') }}" class="flex items-center space-x-3 p-3 hover:bg-neutral-100 rounded-lg transition-colors @yield('students-bg', '')">
                    <span class="material-symbols-rounded text-emerald-600">person_book</span>
                    <span class="font-medium">Students</span>
                </a>
            </div>
        </div>
        @endauth

        <!-- Main Content Area -->
        <div class="@auth @endauth flex-1 p-8 h-[calc(100vh-4rem)] overflow-y-auto">
            <div class="max-w-7xl mx-auto">
                @yield('content')
            </div>
        </div>
    </div>
</body>

</html>