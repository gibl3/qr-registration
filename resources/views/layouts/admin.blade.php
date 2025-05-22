<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Add Material Icons -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
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
                <div class="text-sm text-gray-500">Welcome back, {{ Auth::user()->email }}!</div>
                @auth
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="btn-text shadow-xs rounded-full p-2.5 bg-neutral-200/60 hover:bg-neutral-200">
                        <span class="material-symbols-rounded text-neutral-600">logout</span>
                    </button>
                </form>
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
                <a href="{{ route('admin.index') }}" class="flex items-center space-x-3 p-3 hover:bg-neutral-100 rounded-lg transition-colors @yield('dashboard-bg', '')">
                    <span class="material-symbols-rounded text-emerald-600">dashboard</span>
                    <span class="font-medium">Dashboard</span>
                </a>

                <a href="{{ route('admin.instructor.create') }}" class="flex items-center space-x-3 p-3 hover:bg-neutral-100 rounded-lg transition-colors @yield('instructor-bg', '')">
                    <span class="material-symbols-rounded text-emerald-600">person_add</span>
                    <span class="font-medium">Add Instructors</span>
                </a>

                <a href="{{ route('admin.instructor.showTable') }}" class="flex items-center space-x-3 p-3 hover:bg-neutral-100 rounded-lg transition-colors @yield('view-instructor-bg', '')">
                    <span class="material-symbols-rounded text-emerald-600">badge</span>
                    <span class="font-medium">View Instructors</span>
                </a>
            </div>
        </div>
        @endauth

        <!-- Main Content Area -->
        <div class="@auth @endauth flex-1 p-8 h-[calc(100vh-4rem)]">
            <div class="max-w-7xl mx-auto">
                @yield('content')
            </div>
        </div>
    </div>
</body>

</html>