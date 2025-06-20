<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/registration/student-store.js'])
    <title>QR Code Registration</title>
</head>

<body class="bg-neutral-50 min-h-screen flex flex-col text-neutral-950">
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
                <a href="{{ route('login') }}" class="btn-filled text-sm sm:text-base px-3 sm:px-4 py-1.5 sm:py-2">Log in</a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="flex flex-1 flex-col md:flex-row items-stretch justify-center bg-gradient-to-l from-emerald-50 to-white px-4 sm:px-6 md:px-12">

        <section class="flex-1 flex flex-col items-start justify-start py-6 sm:py-8 md:py-12 gap-y-4 sm:gap-y-6 w-full">
            <!-- Welcome/Info Section -->
            <div class="space-y-2 w-full max-w-md md:max-w-none">
                <h1 class="text-2xl sm:text-3xl md:text-5xl lg:text-6xl font-extrabold text-emerald-800 leading-tight">Welcome to QR Code Student Attendance</h1>
                <p class="text-neutral-600 text-sm sm:text-base md:text-lg leading-normal md:w-[48ch]">Track and manage student attendance efficiently and securely using QR codes.</p>
            </div>

            <div class="text-neutral-700 text-xs sm:text-sm leading-relaxed w-full max-w-md md:max-w-none md:w-[48ch] pl-6 sm:pl-12">
                <ul class="list-disc space-y-1">
                    <li>This portal is officially provided by <span class="font-semibold">Computer Studies Department</span>.</li>
                    <li>Your information is <span class="font-semibold">kept private</span> and used only for attendance purposes.</li>
                    <li>Need help? Contact <a href="#" class="text-emerald-700 underline" target="_blank">main.guidance@evsu.edu.ph</a></li>
                </ul>
            </div>
        </section>

        <section class="flex-1 flex items-center justify-center py-6 sm:py-8 md:py-12 w-full">
            <!-- Registration Form Section -->
            <section class="w-full max-w-md md:max-w-lg bg-white rounded-xl sm:rounded-2xl shadow-lg p-4 sm:p-6 md:p-8 ring-1 ring-emerald-100">
                <h2 class="text-lg sm:text-xl md:text-2xl font-bold text-center mb-4 md:mb-6 text-emerald-800">Student Registration</h2>
                <form method="post" action="" class="space-y-3 sm:space-y-4 md:space-y-5" id="student-form">
                    @csrf
                    @method('post')

                    <div class="space-y-1.5 sm:space-y-2">
                        <label for="student_id" class="text-sm font-medium text-neutral-700">Student ID</label>
                        <input type="text" id="student_id" name="student_id" placeholder="Enter student ID" class="input-base text-sm sm:text-base">
                    </div>
                    <div class="space-y-1.5 sm:space-y-2">
                        <label for="email_address" class="text-sm font-medium text-neutral-700">Email Address</label>
                        <input type="email" id="email_address" name="email_address" placeholder="Enter email address" class="input-base text-sm sm:text-base">
                    </div>
                    <div class="space-y-1.5 sm:space-y-2">
                        <label for="first_name" class="text-sm font-medium text-neutral-700">First Name</label>
                        <input type="text" id="first_name" name="first_name" placeholder="Enter first name" class="input-base text-sm sm:text-base">
                    </div>
                    <div class="space-y-1.5 sm:space-y-2">
                        <label for="last_name" class="text-sm font-medium text-neutral-700">Last Name</label>
                        <input type="text" id="last_name" name="last_name" placeholder="Enter last name" class="input-base text-sm sm:text-base">
                    </div>
                    <div class="space-y-1.5 sm:space-y-2">
                        <label for="program" class="text-sm font-medium text-neutral-700">Program</label>
                        <div class="input-base p-0">
                            <select id="program" name="program_id" class="input-base border-r-[12px] border-transparent text-sm sm:text-base">
                                @foreach ($programs as $program)
                                    <option value="{{ $program->id }}">{{ $program->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="space-y-1.5 sm:space-y-2">
                        <label for="year_level" class="text-sm font-medium text-neutral-700">Year Level</label>
                        <div class="input-base p-0">
                            <select id="year_level" name="year_level" class="input-base border-r-[12px] border-transparent text-sm sm:text-base">
                                <option value="1">1st year</option>
                                <option value="2">2nd year</option>
                                <option value="3">3rd year</option>
                                <option value="4">4th year</option>
                            </select>
                        </div>
                    </div>
                    <div class="space-y-1.5 sm:space-y-2">
                        <label for="section_input" class="text-sm font-medium text-neutral-700">Section</label>
                        <input type="text" id="section_input" name="section" placeholder="Enter section (A-E)" class="input-base text-sm sm:text-base">
                    </div>
                    <div class="space-y-1.5 sm:space-y-2">
                        <label for="gender" class="text-sm font-medium text-neutral-700">Gender</label>
                        <div class="input-base p-0">
                            <select id="gender" name="gender" class="input-base border-r-[12px] border-transparent text-sm sm:text-base">
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                            </select>
                        </div>
                    </div>
                    <input type="submit" value="Register" class="w-full btn-filled mt-4 text-sm sm:text-base py-2 sm:py-2.5" />
                </form>
            </section>
        </section>
    </main>

    <footer class="flex">
        <div class="flex items-center gap-2 py-3 sm:py-4 mx-auto">
            <img src="/build/assets/school-seal.png" alt="School Seal" class="h-6 w-6 sm:h-8 sm:w-8 md:h-10 md:w-10 rounded-full border border-neutral-200 bg-white shadow" onerror="this.style.display='none'">
            <span class="text-[10px] sm:text-xs text-neutral-500">© {{ date('Y') }} Eastern Visayas State University. All rights reserved.</span>
        </div>
    </footer>
</body>

</html>