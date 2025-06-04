<!-- +--------------------------------------------------------------------+ -->
<!-- |                                                                    | -->
<!-- | ██████╗ ███████╗███████╗ █████╗  ██████╗████████╗ ██████╗ ██████╗  | -->
<!-- | ██╔══██╗██╔════╝██╔════╝██╔══██╗██╔════╝╚══██╔══╝██╔═══██╗██╔══██╗ | -->
<!-- | ██████╔╝█████╗  █████╗  ███████║██║        ██║   ██║   ██║██████╔╝ | -->
<!-- | ██╔══██╗██╔══╝  ██╔══╝  ██╔══██║██║        ██║   ██║   ██║██╔══██╗ | -->
<!-- | ██║  ██║███████╗██║     ██║  ██║╚██████╗   ██║   ╚██████╔╝██║  ██║ | -->
<!-- | ╚═╝  ╚═╝╚══════╝╚═╝     ╚═╝  ╚═╝ ╚═════╝   ╚═╝    ╚═════╝ ╚═╝  ╚═╝ | -->
<!-- |                                                                    | -->
<!-- +--------------------------------------------------------------------+ -->

@extends('layouts.instructor')

@section('title', 'Attendance Records')

@section('attendance-bg', 'bg-neutral-200/90')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 sm:gap-0">
        <h1 class="text-2xl font-bold text-gray-800">Attendance Records</h1>

        <!-- Filters -->
        <div class="flex items-center gap-2">
            <select id="subject-filter" class="min-w-[200px] rounded-full border-neutral-300 text-sm bg-neutral-200/50 py-1.5 px-3">
                <option value="">All Subjects</option>
                @foreach ($subjects as $subject)
                <option value="{{ $subject->subject->id }}">{{ $subject->subject->code }}</option>
                @endforeach
            </select>

            <div class="flex items-center gap-x-2">
                <input type="date" id="date-filter" class="min-w-[180px] rounded-full border-neutral-300 text-sm bg-neutral-200/50 py-1.5 px-3" value="{{ date('Y-m-d') }}">
                <button id="clear-date" class="btn-base p-0">
                    <span class="material-symbols-rounded text-neutral-600">clear</span>
                </button>
            </div>

            <!-- Action Dropdown -->
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" @keydown.escape="open = false" class="btn-text shadow-xs rounded-full p-1.5 bg-neutral-200/60 hover:bg-neutral-200 focus:outline-none" aria-haspopup="true" :aria-expanded="open">
                    <span class="material-symbols-rounded text-neutral-600">more_vert</span>
                </button>

                <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-50 py-1 border border-neutral-200" x-cloak>
                    <a href="{{ route('instructor.attendance.markAbsentPage') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                        <span class="material-symbols-rounded">person_off</span>
                        Mark Absent
                    </a>
                    <button id="scan-qr-btn" class="flex items-center gap-2 w-full text-left px-4 py-2 text-sm text-emerald-600 hover:bg-emerald-50">
                        <span class="material-symbols-rounded">qr_code_scanner</span>
                        Scan QR
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Date Navigation -->
    <div class="flex items-center justify-between bg-white rounded-lg shadow-sm p-4">
        <div class="flex flex-1 justify-start">
            <button id="prev-date" class="btn-text flex items-center space-x-2">
                <span class="material-symbols-rounded">chevron_left</span>
                <span class="hidden sm:inline">Previous Day</span>
            </button>
        </div>

        <div class="flex flex-1 justify-center">
            <p class="text-base sm:text-lg font-medium text-gray-700" id="current-date">
                {{ date('F d, Y') }}
            </p>
        </div>

        <div class="flex flex-1 justify-end">
            <button id="next-date" class="btn-text flex items-center space-x-2">
                <span class="hidden sm:inline">Next Day</span>
                <span class="material-symbols-rounded">chevron_right</span>
            </button>
        </div>
    </div>

    <!-- Table Section -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student ID</th>
                        <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time In</th>
                        <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subject</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" id="attendance-table-body">
                    @foreach ($attendances as $attendance)
                    <?php echo "<script>console.log(" . json_encode($attendance) . ")</script>"; ?>
                    <?php echo "<script>console.log(" . json_encode($attendance->subjectAdvised[0]->subject) . ")</script>"; ?>
                    <tr class="hover:bg-gray-50" data-date="{{ $attendance->date }}" data-subject="{{ $attendance->subjectAdvised[0]->subject->id ?? '' }}">
                        <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-500">
                                {{ $attendance->student->student_id }}
                            </div>
                        </td>
                        <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $attendance->first_name }} {{ $attendance->last_name }}</div>
                        </td>
                        <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-500">{{ $attendance->date }}</div>
                        </td>
                        <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-500">{{ $attendance->time_in }}</div>
                        </td>
                        <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-500">
                                @if($attendance->status === 'present')
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">Present</span>
                                @elseif($attendance->status === 'absent')
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">Absent</span>
                                @else
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">{{ ucfirst($attendance->status) }}</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-500">{{ $attendance->subjectAdvised[0]->subject->code ?? 'N/A' }}</div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- No Records Message -->
    <div id="no-records" class="hidden text-center py-12">
        <span class="material-symbols-rounded text-gray-400 text-5xl">event_busy</span>
        <p class="mt-4 text-gray-500">No attendance records found for this date</p>
    </div>
</div>
@endsection

@push('scripts')
@vite(['resources/css/app.css'])
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const dateFilter = document.getElementById('date-filter');
        const clearDate = document.getElementById('clear-date');
        const prevDate = document.getElementById('prev-date');
        const nextDate = document.getElementById('next-date');
        const currentDate = document.getElementById('current-date');
        const tableBody = document.getElementById('attendance-table-body');
        const noRecords = document.getElementById('no-records');
        const subjectFilter = document.getElementById('subject-filter');
        const scanQrBtn = document.getElementById('scan-qr-btn');
        const rows = tableBody.getElementsByTagName('tr');

        function toLocalDateString(date) {
            const offset = date.getTimezoneOffset();
            const localDate = new Date(date.getTime() - (offset * 60 * 1000));
            return localDate.toISOString().split('T')[0];
        }

        function formatDate(date) {
            return date.toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
        }

        function filterBySubjectAndDate(subjectId, date) {
            const formattedDate = toLocalDateString(date);
            let hasRecords = false;

            Array.from(rows).forEach(row => {
                const rowDate = row.getAttribute('data-date');
                const rowSubject = row.getAttribute('data-subject');
                const dateMatch = rowDate === formattedDate;
                const subjectMatch = !subjectId || rowSubject === subjectId;

                if (dateMatch && subjectMatch) {
                    row.style.display = '';
                    hasRecords = true;
                } else {
                    row.style.display = 'none';
                }
            });

            // Show/hide no records message
            noRecords.classList.toggle('hidden', hasRecords);
            tableBody.classList.toggle('hidden', !hasRecords);

            // Update current date display
            currentDate.textContent = formatDate(date);
            dateFilter.value = formattedDate;
        }

        // Initial filter for today and all subjects
        filterBySubjectAndDate(subjectFilter.value, new Date());

        // Date filter change
        dateFilter.addEventListener('change', function() {
            filterBySubjectAndDate(subjectFilter.value, new Date(this.value));
        });

        // Subject filter change
        subjectFilter.addEventListener('change', function() {
            filterBySubjectAndDate(this.value, new Date(dateFilter.value));
        });

        // Clear date filter
        clearDate.addEventListener('click', function() {
            dateFilter.value = '';
            Array.from(rows).forEach(row => row.style.display = '');
            noRecords.classList.add('hidden');
            tableBody.classList.remove('hidden');
            currentDate.textContent = 'All Dates';
        });

        // Previous day
        prevDate.addEventListener('click', function() {
            const current = new Date(dateFilter.value || new Date());
            current.setDate(current.getDate() - 1);
            filterBySubjectAndDate(subjectFilter.value, current);
        });

        // Next day
        nextDate.addEventListener('click', function() {
            const current = new Date(dateFilter.value || new Date());
            current.setDate(current.getDate() + 1);
            filterBySubjectAndDate(subjectFilter.value, current);
        });

        // Scan QR Code functionality
        scanQrBtn.addEventListener('click', function() {
            // Implement scan QR code functionality
            alert('Scan QR Code functionality not implemented yet.');
        });
    });
</script>
@endpush