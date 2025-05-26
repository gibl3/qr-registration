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
    <nav class="shadow-xs w-full top-0 z-50 px-4 md:px-12 bg-white">
        <div class="flex flex-col md:flex-row justify-start items-start md:items-center py-2 gap-2 md:gap-8">
            <!-- Back/Home Button -->
            @if (url()->previous() !== url()->current())
            <a href="{{ route('index') }}" class="btn-text flex items-center gap-1 text-sm md:text-base">
                <span class="material-symbols-rounded text-lg md:text-2xl">
                    arrow_back
                </span>
                Back
            </a>
            @else
            <a href="{{ route('index') }}" class="btn-text flex items-center gap-1 text-sm md:text-base">
                <span class="material-symbols-rounded text-lg md:text-2xl">
                    home
                </span>
                Home
            </a>
            @endif

            <!-- Page Title -->
            <h1 class="text-lg md:text-2xl font-semibold mt-1 md:mt-0">@yield('title', 'QR Attendance')</h1>
        </div>
    </nav>

    <main class="flex-1 flex flex-col">
        @yield('content')
    </main>
</body>

</html>