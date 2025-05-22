<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    @vite(['resources/css/app.css'])
    <title>Change Password</title>
</head>

<body class="bg-gray-100 min-h-screen">
    <nav class="shadow-xs w-full top-0 z-50 px-8">
        <div class="flex justify-between mx-auto py-2">
            <div class="flex items-center">
                <h2 class="text-2xl font-bold text-emerald-600">QR Attendance</h2>
            </div>

            <div class="flex items-center space-x-4">
                @auth
                <a href="{{ route('instructor.index') }}" class="btn-text text-neutral-700">
                    <span class="material-symbols-rounded">
                        arrow_back
                    </span>
                    Back
                </a>

                <!-- Dropdown for settings and logout -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" @keydown.escape="open = false" class="btn-text shadow-xs rounded-full p-2.5 bg-neutral-200/60 hover:bg-neutral-200 focus:outline-none" aria-haspopup="true" :aria-expanded="open">
                        <span class="material-symbols-rounded text-neutral-600">settings</span>
                    </button>

                    <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-neutral-50 rounded-md shadow-lg z-50 py-1 border border-neutral-200" x-cloak>
                        <a
                            @if(Route::is('instructor.settings.edit'))
                            class="block px-4 py-2 text-sm text-neutral-700 bg-neutral-200/60 pointer-events-none"
                            @else
                            href="{{ route('instructor.settings.edit') }}"
                            class="block px-4 py-2 text-sm text-neutral-700 hover:bg-neutral-200/60"
                            @endif>
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
                @endauth
            </div>
        </div>
    </nav>

    <div class="flex flex-col items-center justify-center min-h-screen">
        <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4 w-full max-w-md">
            <h2 class="text-2xl font-bold mb-6 text-center">Change Password</h2>

            @if (session('status'))
            <div class="mb-4 p-2 text-green-800 bg-green-100 border border-green-300 rounded text-sm">
                {{ session('status') }}
            </div>
            @endif

            <form method="POST" action="{{ route('instructor.settings.updatePassword') }}" class="space-y-4">
                @csrf
                @method('post')

                <!-- Current Password -->
                <div class="space-y-2">
                    <label for="current_password" class="block text-sm font-medium text-neutral-700">Current Password</label>
                    <input type="password" id="current_password" name="current_password" placeholder="Enter current password" class="input-base">
                    @error('current_password')
                    <p class="text-red-500 text-sm">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label for="new_password" class="block text-sm font-medium text-neutral-700">New Password</label>
                    <input type="password" id="new_password" name="new_password" placeholder="Enter new password" class="input-base">
                    @error('new_password')
                    <p class="text-red-500 text-sm">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label for="new_password_confirmation" class="block text-sm font-medium text-neutral-700">Confirm Password</label>
                    <input type="password" id="new_password_confirmation" name="new_password_confirmation" placeholder="Confirm new password" class="input-base">
                    @error('new_password_confirmation')
                    <p class="text-red-500 text-sm">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-between w-full">
                    <button class="btn-filled w-full" type="submit">
                        Update Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>

</html>