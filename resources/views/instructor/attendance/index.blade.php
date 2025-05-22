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
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-800">Attendance Records</h1>
        <div class="flex items-center space-x-4">
            <!-- Date Filter -->
            <div class="flex items-center justify-center space-x-4">
                <input type="date" id="date-filter" class="rounded-full border-gray-300 text-sm bg-emerald-200/50 py-1.5 px-3" value="{{ date('Y-m-d') }}">

                <button id="clear-date" class="btn-base p-0">
                    <span class="material-symbols-rounded">clear</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Date Navigation -->
    <div class="flex items-center justify-between bg-white rounded-lg shadow-sm p-4">
        <button id="prev-date" class="btn-text flex items-center space-x-2">
            <span class="material-symbols-rounded">chevron_left</span>
            <span>Previous Day</span>
        </button>
        <div class="text-lg font-medium text-gray-700" id="current-date">
            {{ date('F d, Y') }}
        </div>
        <button id="next-date" class="btn-text flex items-center space-x-2">
            <span>Next Day</span>
            <span class="material-symbols-rounded">chevron_right</span>
        </button>
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time In</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subject</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" id="attendance-table-body">
                    @foreach ($attendances as $attendance)
                    <tr class="hover:bg-gray-50" data-date="{{ $attendance->date }}">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-500">
                                {{ $attendance->student->student_id }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $attendance->first_name }} {{ $attendance->last_name }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-500">{{ $attendance->date }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-500">{{ $attendance->time_in }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-500">
                                {{ ucfirst($attendance->status) }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-500">{{ $attendance->subject->subject_name ?? 'N/A' }}
                        </td>
        </div>
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

        function filterByDate(date) {
            const formattedDate = toLocalDateString(date);
            let hasRecords = false;

            Array.from(rows).forEach(row => {
                const rowDate = row.getAttribute('data-date');
                if (rowDate === formattedDate) {
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

        // Initial filter for today
        filterByDate(new Date());

        // Date filter change
        dateFilter.addEventListener('change', function() {
            filterByDate(new Date(this.value));
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
            const currentDate = new Date(dateFilter.value || new Date());
            currentDate.setDate(currentDate.getDate() - 1);
            filterByDate(currentDate);
        });

        // Next day
        nextDate.addEventListener('click', function() {
            const currentDate = new Date(dateFilter.value || new Date());
            currentDate.setDate(currentDate.getDate() + 1);
            filterByDate(currentDate);
        });
    });
</script>
@endpush