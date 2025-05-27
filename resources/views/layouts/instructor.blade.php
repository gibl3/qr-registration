<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Add Material Icons -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />

    <style>
        .icon-filled {
            font-variation-settings:
                'FILL' 1,
                'wght' 400,
                'GRAD' 0,
                'opsz' 24
        }
    </style>

    <!-- Alpine.js for dropdown -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    @vite(['resources/css/app.css'])
    @stack('scripts')
    <title>@yield('title', 'QR Attendance')</title>
</head>

<body class="bg-neutral-50 min-h-screen flex flex-col overflow-hidden">
    <!-- Top Navigation Bar -->
    <nav class="shadow-xs w-full top-0 z-50 px-4 md:px-8 bg-white">
        <div class="flex justify-between items-center mx-auto py-2">
            <div class="flex items-center">
                <h2 class="text-lg md:text-2xl font-bold text-emerald-600">QR Attendance</h2>
            </div>

            <div class="flex items-center space-x-2 md:space-x-4">
                <div class="hidden sm:block text-xs md:text-sm text-gray-500">Welcome back, {{ Auth::user()->name }}!</div>
                @auth
                <!-- Dropdown for settings and logout -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" @keydown.escape="open = false" class="btn-text p-0 bg-neutral-200/90 hover:bg-neutral-200 focus:outline-none" aria-haspopup="true" :aria-expanded="open">
                        <span class="material-symbols-rounded text-neutral-600/90 md-icon-36 icon-filled">account_circle</span>
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
                <!-- Sidebar toggle button for mobile -->
                <button @click="sidebarOpen = !sidebarOpen" class="sm:hidden ml-2 p-2 rounded-md bg-neutral-100 hover:bg-neutral-200 focus:outline-none" x-data="{ sidebarOpen: false }" x-on:click="$dispatch('sidebar-toggle')">
                    <span class="material-symbols-rounded">menu</span>
                </button>
                @else
                <a href="{{ route('login') }}" class="bg-emerald-600 text-white px-4 py-2 rounded-md hover:bg-emerald-700 text-xs md:text-base">Login</a>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="flex flex-1 min-h-0" x-data="{ sidebarOpen: false }" @sidebar-toggle.window="sidebarOpen = !sidebarOpen">
        @auth
        <!-- Left Sidebar for desktop -->
        <div class="hidden sm:block w-56 md:w-64 h-[calc(100vh-4rem)] bg-white shadow-md py-4 px-4 md:px-8 overflow-y-auto">
            <div class="space-y-2 text-neutral-700">
                <a href="{{ route('instructor.index') }}" class="flex items-center space-x-3 p-3 hover:bg-neutral-100 rounded-lg transition-colors @yield('dashboard-bg', '')">
                    <span class="material-symbols-rounded">dashboard</span>
                    <span class="font-medium">Dashboard</span>
                </a>

                <a href="{{ route('instructor.scan.index') }}" class="flex items-center space-x-3 p-3 hover:bg-neutral-100 rounded-lg transition-colors @yield('scan-bg', '')">
                    <span class="material-symbols-rounded">qr_code_scanner</span>
                    <span class="font-medium">Scan QR</span>
                </a>

                <a href="{{ route('instructor.subjects.index') }}" class="flex items-center space-x-3 p-3 hover:bg-neutral-100 rounded-lg transition-colors @yield('subjects-bg', '')">
                    <span class="material-symbols-rounded">subject</span>
                    <span class="font-medium">Subjects</span>
                </a>

                <a href="{{ route('instructor.attendance.index') }}" class="flex items-center space-x-3 p-3 hover:bg-neutral-100 rounded-lg transition-colors @yield('attendance-bg', '')">
                    <span class="material-symbols-rounded">list_alt</span>
                    <span class="font-medium">Attendance</span>
                </a>

                <a href="{{ route('instructor.students.index') }}" class="flex items-center space-x-3 p-3 hover:bg-neutral-100 rounded-lg transition-colors @yield('students-bg', '')">
                    <span class="material-symbols-rounded">person_book</span>
                    <span class="font-medium">Students</span>
                </a>
            </div>
        </div>

        <!-- Sidebar for mobile -->
        <div class="sm:hidden fixed inset-0 z-40" x-show="sidebarOpen" style="display: none;">
            <div class="absolute inset-0 bg-black opacity-30" @click="sidebarOpen = false"></div>
            <div class="relative w-56 bg-white h-full shadow-lg py-4 px-4 flex flex-col">
                <button class="absolute top-2 right-2 p-2 rounded-full hover:bg-neutral-100" @click="sidebarOpen = false">
                    <span class="material-symbols-rounded">close</span>
                </button>
                <!-- Title at the top -->
                <div class="flex items-center mb-8 mt-4">
                    <h2 class="text-lg font-bold text-emerald-600">QR Attendance</h2>
                </div>
                <!-- Nav links below the title -->
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
        </div>
        @endauth

        <!-- Main Content Area -->
        <div class="@auth @endauth flex-1 p-4 md:p-8 h-[calc(100vh-4rem)] overflow-y-auto">
            <div class="max-w-7xl mx-auto">
                @yield('content')
            </div>
        </div>
    </div>
</body>

</html>