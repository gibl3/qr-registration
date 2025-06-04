@extends('layouts.default-nav')

@section('title', 'Generated QR')

@section('content')
<section class="flex flex-col justify-center items-center my-auto mx-auto">
    <div class="bg-neutral-50 shadow-lg w-fit rounded-lg p-6" id="capture-area" data-id="">
        <!-- Student Details -->
        <div class="space-y-4 p-6">
            <div class="text-center">
                <p class="text-2xl font-semibold">{{ $student->first_name }} {{ $student->last_name }}</p>

                <div class="font-medium text-sm text-neutral-600">
                    <p class="">{{ $student->program->abbreviation }} - Year {{ $student->year_level }}</p>
                    <p id="student-id">{{ $student->student_id }}</p>
                    <p>{{ $student->email_address }}</p>
                </div>
            </div>

            <!-- QR Code -->
            <div class="mt-6 text-center">
                <img src="{{ $qrCodeUrl }}" alt="QR Code for {{ $student->student_id }}" class="mx-auto size-56" crossOrigin="anonymous">
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="mt-6 text-center flex flex-col justify-center items-center gap-y-4">
        <button id="download-btn" class="btn-filled-tonal">
            <span class="material-symbols-rounded">
                download
            </span>
            Download
        </button>
    </div>
</section>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/html2canvas-pro@1.5.10/dist/html2canvas-pro.min.js"></script>
@vite(['resources/js/registration/download-qr.js'])
@endpush