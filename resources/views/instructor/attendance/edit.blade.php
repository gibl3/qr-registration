<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/app.css', 'resources/js/delete-attendances.js'])
    <title>Edit Attendance</title>
</head>

<body>

    <main class="flex-1 flex flex-col items-center justify-center min-h-0 px-4 mx-64">


        <!-- Product Form Section -->
        <section class="w-full max-w-lg bg-white p-6 rounded-lg shadow-lg">
            <h2 class="text-2xl font-semibold text-center mb-4">Edit Attendance</h2>

            <form method="post" action="{{ route('admin.attendance.update', ['attendance' => $attendance]) }}" class="space-y-4">
                @csrf
                @method('put')

                <!-- First Name -->
                <div class="space-y-2">
                    <label for="first_name" class="font-medium">First Name</label>
                    <input type="text" id="first_name" name="first_name" placeholder="Enter first name" class="w-full border p-2 rounded" value="{{ $attendance->first_name }}">
                    @error('first_name')
                    <p class=" text-red-500 text-sm">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Last Name -->
                <div class="space-y-2">
                    <label for="last_name" class="font-medium">Last Name</label>
                    <input type="text" id="last_name" name="last_name" placeholder="Enter last name" class="w-full border p-2 rounded" value="{{ $attendance->last_name }}">
                    @error('last_name')
                    <p class=" text-red-500 text-sm">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div class="space-y-2">
                    <label for="status" class="font-medium">Status</label>
                    <select id="status" name="status" class="w-full border p-2 rounded">
                        <option value="present" {{ $attendance->status === 'present' ? 'selected' : '' }}>Present</option>
                        <option value="absent" {{ $attendance->status === 'absent' ? 'selected' : '' }}>Absent</option>
                        <option value="late" {{ $attendance->status === 'late' ? 'selected' : '' }}>Late</option>
                    </select>
                    @error('status')
                    <p class="text-red-500 text-sm">{{ $message }}</p>
                    @enderror
                </div>

                <input type="submit" value="Register Student" class="w-full btn-filled" />
            </form>
        </section>

    </main>
</body>

</html>