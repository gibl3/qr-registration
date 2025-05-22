@extends('layouts.admin')

@section('title', 'Edit Instructors')

@section('content')
<header class="flex justify-start items-center gap-8">
    <a href="{{ route('admin.instructor.showTable') }}" class="btn-text">
        <span class="material-symbols-rounded">
            arrow_back
        </span>
        Back
    </a>
    <h1 class="text-xl font-semibold text-center">Edit Instructor Details</h1>
</header>

<!-- Main Content -->
<main class="flex-1 flex flex-col items-center justify-center min-h-0 px-4 mx-64 mt-12">

    <!-- Edit Form Section -->
    <section class="w-full max-w-lg bg-neutral-100/30 p-6 rounded-lg shadow-md ">
        <h2 class="text-2xl font-semibold text-center mb-4">Instructor Details</h2>

        @if (session('success'))
        <div class="mb-4 px-3 py-2 text-green-800 bg-green-100 border border-green-300 rounded text-sm">
            {{ session('success') }}
        </div>
        @endif

        <form method="post" action="{{ route('admin.instructor.update', ['instructor' => $instructor]) }}" class="space-y-4">
            @csrf
            @method('put')

            <!-- First Name -->
            <div class="space-y-2">
                <label for="first_name" class="text-sm font-medium text-neutral-700">First Name</label>
                <input type="text" id="first_name" name="first_name" placeholder="Enter first name" class="input-base" value="{{ $instructor->first_name }}">
                @error('first_name')
                <p class="text-red-500 text-sm">{{ $message }}</p>
                @enderror
            </div>

            <!-- Last Name -->
            <div class="space-y-2">
                <label for="last_name" class="text-sm font-medium text-neutral-700">Last Name</label>
                <input type="text" id="last_name" name="last_name" placeholder="Enter last name" class="input-base" value="{{ $instructor->last_name }}">
                @error('last_name')
                <p class=" text-red-500 text-sm">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email Address -->
            <div class="space-y-2">
                <label for="email" class="text-sm font-medium text-neutral-700">Email Address</label>
                <input type="email" id="email" name="email" placeholder="Enter email address" class="input-base" value="{{ $instructor->email }}">
                @error('email')
                <p class="text-red-500 text-sm">{{ $message }}</p>
                @enderror
            </div>

            <!-- Department -->
            <div class="space-y-2">
                <label for="department" class="text-sm font-medium text-neutral-700">Department</label>
                <div class="input-base p-0">
                    <select id="department" name="department" class="input-base border-r-[12px] border-transparent">
                        <option value="computer_studies" {{ $instructor->department === 'computer_studies' ? 'selected' : '' }}>Computer Studies Department</option>
                    </select>
                </div>

                @error('department')
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