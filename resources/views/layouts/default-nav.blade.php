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

<body class="bg-neutral-50 min-h-screen flex flex-col">
    <!-- Top Navigation Bar -->
    <nav class="shadow-xs w-full top-0 z-50 px-4 sm:px-8 md:px-12 bg-neutral-50 border-b border-neutral-200">
        <div class="flex justify-between items-center py-2 mx-auto">
            <div class="flex items-center gap-2 sm:gap-3">
                <img src="/build/assets/logo.png" alt="School Logo" class="p-1.5 sm:p-2 bg-neutral-100/70 border rounded-full border-emerald-100 w-10 h-10 sm:w-12 sm:h-12">
                <div>
                    <h2 class="text-lg sm:text-xl md:text-2xl font-bold text-emerald-700 tracking-tight">QR Attendance</h2>
                    <p class="text-[10px] sm:text-xs text-neutral-500 font-medium">Official Student Attendance Registration</p>
                </div>
            </div>
            <div class="flex items-center">
                @if(Route::is('student.*'))
                <form action="{{ route('logout') }}" method="POST" class="m-0">
                    @csrf
                    <button type="submit" class="btn-text text-sm sm:text-base px-3 sm:px-4 py-1.5 sm:py-2 flex items-center gap-1">
                        <span class="material-symbols-rounded text-lg">logout</span>
                        Logout
                    </button>
                </form>
                @else
                <a href="{{ route('index') }}" class="btn-text text-sm sm:text-base px-3 sm:px-4 py-1.5 sm:py-2 flex items-center gap-1">
                    <span class="material-symbols-rounded text-lg">home</span>
                    Home
                </a>
                @endif
            </div>
        </div>
    </nav>

    <main class="flex-1 flex flex-col">
        @yield('content')
    </main>
</body>

</html>