<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/registration/student-store.js'])
    <title>QR Code Registration</title>
</head>

<body class="py-2">
    <nav class="shadow-xs w-full top-0 z-50 px-12">
        <div class="flex justify-start items-center py-2 gap-8">
            <a href="{{ url()->previous() }}" class="btn-text">
                <span class="material-symbols-rounded">
                    arrow_back
                </span>
                Back
            </a>
            <h1 class="text-2xl text-center">Register Student</h1>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="flex-1 flex flex-col items-center justify-center min-h-0 px-4 mx-32 mt-10">

        <!-- Form Section -->
        <section class="w-full max-w-lg bg-neutral-100/50 p-6 rounded-lg shadow-md ">
            <h2 class="text-2xl font-semibold text-center mb-4">Student Details</h2>

            <form method="post" action="" class="space-y-4" id="student-form">
                @csrf
                @method('post')

                <!-- Student ID -->
                <div class="space-y-2">
                    <label for="student_id" class="font-medium text-neutral-700">Student ID</label>
                    <input type="text" id="student_id" name="student_id" placeholder="Enter student ID" class="input-base">
                </div>

                <!-- Email Address -->
                <div class="space-y-2">
                    <label for="email_address" class="font-medium text-neutral-700">Email Address</label>
                    <input type="email" id="email_address" name="email_address" placeholder="Enter email address" class="input-base">
                </div>

                <!-- First Name -->
                <div class="space-y-2">
                    <label for="first_name" class="font-medium text-neutral-700">First Name</label>
                    <input type="text" id="first_name" name="first_name" placeholder="Enter first name" class="input-base">
                </div>

                <!-- Last Name -->
                <div class="space-y-2">
                    <label for="last_name" class="font-medium text-neutral-700">Last Name</label>
                    <input type="text" id="last_name" name="last_name" placeholder="Enter last name" class="input-base">
                </div>

                <!-- Program -->
                <div class="space-y-2">
                    <label for="program" class="font-medium text-neutral-700">Program</label>
                    <div class="input-base p-0">
                        <select id="program" name="program" class="input-base border-r-[12px] border-transparent">
                            <option value="BSIT">Bachelor of Science in Information Technology</option>
                        </select>
                    </div>

                </div>

                <div class="space-y-2">
                    <label for="year_level" class="font-medium text-neutral-700">Year Level</label>
                    <div class="input-base p-0">
                        <select id="year_level" name="year_level" class="input-base border-r-[12px] border-transparent">
                            <option value="1">1st year</option>
                            <option value="2">2nd year</option>
                            <option value="3">3rd year</option>
                            <option value="4">4th year</option>
                        </select>
                    </div>
                </div>

                <div class="space-y-2">
                    <label for="section_input" class="font-medium text-neutral-700">Section</label>
                    <input type="text" id="section_input" name="section" placeholder="Enter Section (A-E)" class="input-base">
                </div>

                <!-- Gender -->
                <div class="space-y-2">
                    <label for="gender" class="font-medium text-neutral-700">Gender</label>
                    <div class="input-base p-0">
                        <select id="gender" name="gender" class="input-base border-r-[12px] border-transparent">
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                        </select>
                    </div>
                </div>

                <input type="submit" value="Register Student" class="w-full btn-filled" />
            </form>
        </section>
    </main>
</body>

</html>