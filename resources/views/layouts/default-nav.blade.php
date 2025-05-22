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
    <nav class="shadow-xs w-full top-0 z-50 px-12 bg-white">
        <div class="flex justify-start items-center py-2 gap-8">
            <!-- Back Button -->
            @if (url()->previous() !== url()->current())
            <a href="{{ route('index') }}" class="btn-text">
                <span class="material-symbols-rounded">
                    arrow_back
                </span>
                Back
            </a>
            @else
            <a href="{{ route('index') }}" class="btn-text">
                <span class="material-symbols-rounded">
                    home
                </span>
                Home
            </a>
            @endif

            <!-- Page Title -->
            <h1 class="text-2xl font-semibold">@yield('title', 'QR Attendance')</h1>
        </div>
    </nav>

    <main class="flex-1 flex">
        @yield('content')
    </main>
</body>

</html>