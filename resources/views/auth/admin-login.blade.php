<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
    @vite(['resources/css/app.css'])
    <title>Admin Login - QR Attendance</title>
</head>

<body class="bg-neutral-50 min-h-screen flex items-center justify-center">
    <div class="w-full max-w-md p-8 space-y-8 bg-white rounded-2xl shadow-sm">
        <!-- Header -->
        <div class="text-center">
            <h1 class="text-2xl font-bold text-gray-800">Admin Login</h1>
            <p class="mt-2 text-sm text-gray-600">Sign in to access the admin dashboard</p>
        </div>

        <!-- Login Form -->
        <form id="login-form" class="mt-8 space-y-6">
            @csrf
            <input type="hidden" name="role" value="admin">

            <div class="space-y-4">
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" name="email" id="email" required class="input-base mt-1" placeholder="Enter your email">
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <input type="password" name="password" id="password" required class="input-base mt-1" placeholder="Enter your password">
                </div>
            </div>

            <div>
                <button type="submit" class="btn-filled w-full">
                    Sign in
                </button>
            </div>
        </form>

        <!-- Back to Login -->
        <div class="text-center">
            <a href="{{ route('login') }}" class="text-sm text-emerald-600 hover:text-emerald-500">
                Back to login
            </a>
        </div>
    </div>

    <script>
        document.getElementById('login-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            const form = this;
            const formData = new FormData(form);

            try {
                const response = await fetch("{{ route('login.auth') }}", {
                    method: 'POST',
                    body: formData,
                    headers: {
                        "X-CSRF-TOKEN": document
                            .querySelector('meta[name="csrf-token"]')
                            .getAttribute("content"),
                        "Accept": "application/json",
                        "X-Requested-With": "XMLHttpRequest"
                    },
                    credentials: "same-origin"
                });

                const data = await response.json();

                if (!response.ok) {
                    throw data;
                }

                window.location.href = data.redirect;
            } catch (error) {
                if (error.message) {
                    alert(error.message);
                } else {
                    alert('An error occurred while logging in.');
                }
            }
        });
    </script>
</body>

</html>