@extends('layouts.instructor')

@section('title', 'QR Scanner')

@section('scan-bg', 'bg-neutral-200/90')

@section('content')
    <div class="min-h-screen bg-neutral-50">
        <div class="max-w-7xl mx-auto">
            <!-- Header Section -->
            <div class="mb-8 flex justify-between items-center">
                <h1 class="text-2xl font-bold text-gray-800">QR Code Scanner</h1>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Scanner Section -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-2xl shadow-sm p-6 space-y-6">

                        <!-- Scanner Controls -->
                        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                            <!-- Subject Selection -->
                            <div class="input-base p-0 w-full sm:w-fit">
                                <select id="subject-select"
                                    class="input-base border-r-[12px] border-transparent w-full sm:w-fit">
                                    @foreach ($subjects as $subject)
                                        <option value="{{ $subject->subject->id }}">
                                            {{ $subject->subject->code }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <button id="confirm-scan-btn" class="btn-filled flex items-center space-x-2 w-full">
                                <span id="scan-text">Confirm</span>
                            </button>
                        </div>

                        <section id="qr-input-results" class="">
                            <div class="rounded-xl p-4 space-y-4 bg-green-50" id="input-result-wrapper">
                                <div class="flex items-start justify-start space-x-3">
                                    <div class="text-base font-medium">
                                        Student Details
                                    </div>
                                </div>

                                <div id="input-attendee-details" class="text-sm">
                                    <ul class="list-disc pl-5">
                                        <?php
                                        echo '<input type="hidden" id="student-id" value="' . $student->student_id . '">';
                                        echo '<li><strong>Student ID: </strong>' . $student->student_id . '</li>';
                                        echo '<li><strong>First Name: </strong>' . $student->first_name . '</li>';
                                        echo '<li><strong>Last Name: </strong>' . $student->last_name . '</li>';
                                        echo '<li><strong>Program: </strong>' . $student->program->name . '</li>';
                                        echo '<li><strong>Section: </strong>' . $student->year_level . ' - ' . $student->section . '</li>';
                                        ?>
                                    </ul>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>

                <!-- Results Section -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-2xl shadow-sm p-6 space-y-6">
                        <h2 class="text-lg font-semibold text-gray-800">Scan Results</h2>

                        <!-- Success Result -->
                        <section id="qr-reader-results" class="hidden">
                            <div class="rounded-xl p-4 space-y-4" id="result-wrapper">
                                <div class="flex items-start justify-start space-x-3">
                                    <span class="material-symbols-rounded mt-1" id="result-icon">check_circle</span>

                                    <div id="message" class="text-base font-medium">
                                        <!-- Result content will be dynamically added here -->

                                    </div>
                                </div>
                            </div>
                        </section>

                        <!-- Quick Actions -->
                        <section class="space-y-4">
                            <a href="{{ route('instructor.attendance.index') }}"
                                class="btn-filled-tonal w-full flex items-center justify-center space-x-2">
                                <span class="material-symbols-rounded">list_alt</span>
                                <span>View Attendance Records</span>
                            </a>
                        </section>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    @vite(['resources/css/app.css', 'resources/js/scan/thirdpartyscan.js'])
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.8/html5-qrcode.min.js"
        integrity="sha512-r6rDA7W6ZeQhvl8S7yRVQUKVHdexq+GAlNkNNqVC7YyIV+NwqCTJe2hDWCiffTyRNOeGEzRRJ9ifvRm/HCzGYg=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
@endpush
