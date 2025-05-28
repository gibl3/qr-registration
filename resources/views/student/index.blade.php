@extends('layouts.default-nav')

@section('title', 'Student Dashboard')

@section('content')
<div class="flex flex-col gap-6 p-2 sm:p-4 md:p-8">
    <!-- Welcome Header -->
    <div class="bg-white rounded-xl p-4 sm:p-6 shadow-sm border border-neutral-200 w-full max-w-3xl mx-auto">
        <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-emerald-800">Welcome, {{ Auth::user()->name }}!</h1>
        <p class="text-neutral-600 mt-1 text-sm sm:text-base">Here's your student dashboard where you can view your attendance and QR code.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 w-full max-w-4xl mx-auto">
        <!-- Student Info Card -->
        <div class="bg-white rounded-xl p-4 sm:p-6 shadow-sm border border-neutral-200 w-full">
            <h2 class="text-lg sm:text-xl font-semibold text-emerald-800 mb-4">Student Information</h2>
            <div class="space-y-3">
                <div>
                    <p class="text-xs sm:text-sm text-neutral-500">Student ID</p>
                    <p class="font-medium break-all">{{ Auth::user()->student->student_id }}</p>
                </div>
                <div>
                    <p class="text-xs sm:text-sm text-neutral-500">Program</p>
                    <p class="font-medium">{{ Auth::user()->student->program }}</p>
                </div>
                <div>
                    <p class="text-xs sm:text-sm text-neutral-500">Year Level</p>
                    <p class="font-medium">Year {{ Auth::user()->student->year_level }}</p>
                </div>
                <div>
                    <p class="text-xs sm:text-sm text-neutral-500">Section</p>
                    <p class="font-medium">Section {{ Auth::user()->student->section }}</p>
                </div>
                <div>
                    <p class="text-xs sm:text-sm text-neutral-500">Email Address</p>
                    <p class="font-medium break-all">{{ Auth::user()->student->email_address }}</p>
                </div>
            </div>
        </div>

        <!-- QR Code Card -->
        <div class="bg-white rounded-xl p-4 sm:p-6 shadow-sm border border-neutral-200 w-full">
            <h2 class="text-lg sm:text-xl font-semibold text-emerald-800 mb-4">Your QR Code</h2>
            <div class="bg-neutral-50 shadow-lg w-full rounded-lg p-4 sm:p-6 mx-auto" id="capture-area">
                <!-- Student Details -->
                <div class="space-y-4 p-2 sm:p-6">
                    <div class="text-center">
                        <p class="text-lg sm:text-2xl font-semibold">{{ Auth::user()->student->first_name }} {{ Auth::user()->student->last_name }}</p>
                        <div class="font-medium text-xs sm:text-sm text-neutral-600">
                            <p>{{ Auth::user()->student->program }} - Year {{ Auth::user()->student->year_level }}</p>
                            <p id="student-id">{{ Auth::user()->student->student_id }}</p>
                            <p>{{ Auth::user()->student->email_address }}</p>
                        </div>
                    </div>

                    <!-- QR Code -->
                    <div class="mt-4 sm:mt-6 text-center">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=400x400&data={{ Auth::user()->student->student_id }}&bgcolor=fafafa"
                            alt="QR Code for {{ Auth::user()->student->student_id }}"
                            class="mx-auto w-40 h-40 sm:w-56 sm:h-56"
                            crossOrigin="anonymous">
                    </div>
                </div>
            </div>

            <!-- Download Button -->
            <div class="mt-4 sm:mt-6 text-center">
                <button id="download-btn" class="btn-filled-tonal w-full sm:w-auto">
                    <span class="material-symbols-rounded">
                        download
                    </span>
                    Download QR Code
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/html2canvas-pro@1.5.10/dist/html2canvas-pro.min.js"></script>
@vite(['resources/js/registration/download-qr.js'])
@endpush