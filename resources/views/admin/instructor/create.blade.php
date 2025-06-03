@extends('layouts.admin')

@section('title', 'Add Instructor')

@section('instructor-bg', 'bg-neutral-200/90')

@section('content')
<!-- Main Content -->
<main class="flex-1 flex flex-col items-center justify-center min-h-0 px-2 md:px-4 lg:px-8 mt-6 md:mt-10">
    <!-- Form Section -->
    <section class="w-full max-w-md md:max-w-lg bg-neutral-100/30 p-4 md:p-6 rounded-lg shadow-md ">
        <div class="">
            <h2 class="text-2xl font-semibold text-center mb-4">Instructor Details</h2>

            <div class="mb-4 p-2 text-green-800 bg-green-100 border border-green-300 rounded hidden text-sm" id="response-box">
            </div>
        </div>

        <form method="post" action="" class="space-y-4" id="instructor-form">
            @csrf
            @method('post')

            <!-- First Name -->
            <div class="space-y-2">
                <label for="first_name" class="text-sm font-medium text-neutral-700">First Name</label>
                <input type="text" id="first_name" name="first_name" placeholder="Enter first name" class="input-base">
            </div>

            <!-- Last Name -->
            <div class="space-y-2">
                <label for="last_name" class="text-sm font-medium text-neutral-700">Last Name</label>
                <input type="text" id="last_name" name="last_name" placeholder="Enter last name" class="input-base">
            </div>

            <!-- Email Address -->
            <div class="space-y-2">
                <label for="email" class="text-sm font-medium text-neutral-700">Email Address</label>
                <input type="email" id="email" name="email" placeholder="Enter email address" class="input-base">
            </div>

            <!-- Department -->
            <div class="space-y-2">
                <label for="department" class="text-sm font-medium text-neutral-700">Department</label>
                <div class="input-base p-0">
                    <select id="department" name="department_id" class="input-base border-r-[12px] border-transparent">
                        <option value="">Select Department</option>
                        @foreach ($departments as $department)
                            <option value="{{ $department->id }}">{{ $department->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <input type="submit" value="Save Instructor" class="w-full btn-filled" />
        </form>
    </section>
</main>
@endsection

@push('scripts')
@vite(['resources/css/app.css', 'resources/js/instructor/store.js'])
@endpush