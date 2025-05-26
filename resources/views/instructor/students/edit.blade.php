@extends('layouts.instructor')

@section('title', 'Edit Students')

@section('students-bg', 'bg-neutral-200/90')

@section('content')
<header class="flex justify-start items-center gap-8">
    <a href="{{ route('instructor.students.index') }}" class="btn-text">
        <span class="material-symbols-rounded">
            arrow_back
        </span>
        Back
    </a>
    <h1 class="text-2xl text-center">Edit Student Details</h1>
</header>

<!-- Main Content -->
<main class="flex-1 flex flex-col items-center justify-center min-h-0 px-4 mx-64 mt-12">

    <!-- Edit Form Section -->
    <section class="w-full max-w-lg bg-white p-6 rounded-lg shadow-lg">
        <h2 class="text-2xl font-semibold text-center mb-4">Student Details</h2>

        @if (session('success'))
        <div class="mb-4 p-4 text-green-800 bg-green-100 border border-green-300 rounded">
            {{ session('success') }}
        </div>
        @endif

        {{-- Past action: {{ route('instructor.students.update', ['student' => $student]) }} --}}
        <form method="post" action="{{ route('instructor.students.update', ['student' => $student]) }}" class="space-y-4">
            @csrf
            @method('put')

            <!-- Student ID -->
            <div class="space-y-2">
                <label for="student_id" class="font-medium">Student ID</label>
                <input type="text" id="student_id" name="student_id" placeholder="Enter student ID" class="input-base" value="{{ $student->student_id }}">
                @error('student_id')
                <p class="text-red-500 text-sm">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email Address -->
            <div class="space-y-2">
                <label for="email_address" class="font-medium">Email Address</label>
                <input type="email" id="email_address" name="email_address" placeholder="Enter email address" class="input-base" value="{{ $student->email_address }}">
                @error('email_address')
                <p class="text-red-500 text-sm">{{ $message }}</p>
                @enderror
            </div>

            <!-- First Name -->
            <div class="space-y-2">
                <label for="first_name" class="font-medium">First Name</label>
                <input type="text" id="first_name" name="first_name" placeholder="Enter first name" class="input-base" value="{{ $student->first_name }}">
                @error('first_name')
                <p class="text-red-500 text-sm">{{ $message }}</p>
                @enderror
            </div>

            <!-- Last Name -->
            <div class="space-y-2">
                <label for="last_name" class="font-medium">Last Name</label>
                <input type="text" id="last_name" name="last_name" placeholder="Enter last name" class="input-base" value="{{ $student->last_name }}">
                @error('last_name')
                <p class=" text-red-500 text-sm">{{ $message }}</p>
                @enderror
            </div>

            <!-- Program -->
            <div class="space-y-2">
                <label for="program" class="font-medium">Program</label>
                <div class="input-base p-0">
                    <select id="program" name="program" class="input-base border-r-[12px] border-transparent">
                        <option value="BSIT" {{ $student->program === 'BSIT' ? 'selected' : '' }}>Bachelor of Science in Information Technology</option>
                    </select>
                </div>

                @error('program')
                <p class="text-red-500 text-sm">{{ $message }}</p>
                @enderror
            </div>

            <div class="space-y-2">
                <label for="year_level" class="font-medium">Year Level</label>
                <div class="input-base p-0">
                    <select id="year_level" name="year_level" class="input-base border-r-[12px] border-transparent">
                        <option value="1" {{ $student->year_level === 1 ? 'selected' : '' }}>1st year</option>
                        <option value="2" {{ $student->year_level === 2 ? 'selected' : '' }}>2nd year</option>
                        <option value="3" {{ $student->year_level === 3 ? 'selected' : '' }}>3rd year</option>
                        <option value="4" {{ $student->year_level === 4 ? 'selected' : '' }}>4th year</option>
                    </select>
                </div>

                @error('year_level')
                <p class="text-red-500 text-sm">{{ $message }}</p>
                @enderror
            </div>

            <div class="space-y-2">
                <label for="section_input" class="font-medium">Section</label>
                <input type="text" id="section_input" name="section" placeholder="Enter section (A_E)" class="input-base" value="{{ $student->section }}">
                @error('section')
                <p class="text-red-500 text-sm">{{ $message }}</p>
                @enderror
            </div>

            <!-- Gender -->
            <div class="space-y-2 hidden">
                <label for="gender" class="font-medium">Gender</label>
                <div class="input-base p-0">
                    <select id="gender" name="gender" class="input-base border-r-[12px] border-transparent">
                        <option value="male" {{ $student->gender === 'male' ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ $student->gender === 'female' ? 'selected' : '' }}>Female</option>
                    </select>
                </div>
                @error('gender')
                <p class="text-red-500 text-sm">{{ $message }}</p>
                @enderror
            </div>

            <input type="submit" value="Update" class="w-full btn-filled" />
        </form>
    </section>
</main>
@endsection

@push('scripts')
@vite(['resources/css/app.css'])
@endpush