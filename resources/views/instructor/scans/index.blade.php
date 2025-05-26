@extends('layouts.instructor')

@section('title', 'QR Scanner')

@section('scan-bg', 'bg-neutral-200/90')

@section('content')
<div class="min-h-screen bg-neutral-50">
    <div class="max-w-7xl mx-auto">
        <!-- Header Section -->
        <div class="mb-8 flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-800">QR Code Scanner</h1>
            <button id="toggle-scan-btn" class="btn-filled flex items-center space-x-2">
                <span class="material-symbols-rounded" id="scan-icon">qr_code_scanner</span>
                <span id="scan-text">Start Scanning</span>
            </button>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Scanner Section -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-sm p-6 space-y-6">

                    <!-- Scanner Controls -->
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 sm:gap-0">
                        <!-- Subject Selection -->
                        <div class="input-base p-0 w-full sm:w-fit">
                            <select id="subject-select" class="input-base border-r-[12px] border-transparent w-full sm:w-fit">
                                <option value="">Select a subject...</option>
                                @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}">{{ $subject->subject_name }} ({{ $subject->subject_code }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex items-center space-x-2">
                            <span class="material-symbols-rounded text-gray-400">info</span>
                            <span class="text-sm text-gray-500">Position QR code within frame</span>
                        </div>
                    </div>

                    <!-- Scanner View -->
                    <div class="relative">
                        <div class="aspect-square w-full max-w-xl mx-auto bg-neutral-100 rounded-xl overflow-hidden border-2 border-dashed border-neutral-300">
                            <div id="qr-reader" class="size-full flex items-center justify-center">
                                <div class="text-center space-y-4 flex flex-col">
                                    <span class="material-symbols-rounded text-neutral-400 qr-scan-icon">
                                        qr_code_2
                                    </span>
                                    <p class="text-neutral-500 text-wrap">Camera will activate when you start scanning</p>
                                </div>
                            </div>
                        </div>
                    </div>
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

                            <div id="attendee-details" class="text-sm">
                                <!-- Attendee details will be dynamically added here -->
                            </div>
                        </div>
                    </section>

                    <!-- Quick Actions -->
                    <section class="space-y-4">
                        <a href="{{ route('instructor.attendance.index') }}" class="btn-filled-tonal w-full flex items-center justify-center space-x-2">
                            <span class="material-symbols-rounded">list_alt</span>
                            <span>View Attendance Records</span>
                        </a>
                        <button class="btn-outlined w-full flex items-center justify-center space-x-2" onclick="window.location.reload()">
                            <span class="material-symbols-rounded">refresh</span>
                            <span>Reset Scanner</span>
                        </button>
                    </section>

                    <!-- Help Section -->
                    <div class="bg-blue-50 rounded-xl p-4 space-y-2">
                        <h3 class="font-medium text-blue-800">Tips for Scanning</h3>
                        <ul class="text-sm text-blue-700 space-y-2">
                            <li class="flex items-start space-x-2">
                                <span class="material-symbols-rounded text-sm">lightbulb</span>
                                <span>Ensure good lighting for better scanning</span>
                            </li>
                            <li class="flex items-start space-x-2">
                                <span class="material-symbols-rounded text-sm">lightbulb</span>
                                <span>Hold the QR code steady within the frame</span>
                            </li>
                            <li class="flex items-start space-x-2">
                                <span class="material-symbols-rounded text-sm">lightbulb</span>
                                <span>Keep the QR code clean and undamaged</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@vite(['resources/css/app.css', 'resources/js/scan/scan2.js'])
<script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.8/html5-qrcode.min.js" integrity="sha512-r6rDA7W6ZeQhvl8S7yRVQUKVHdexq+GAlNkNNqVC7YyIV+NwqCTJe2hDWCiffTyRNOeGEzRRJ9ifvRm/HCzGYg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
@endpush