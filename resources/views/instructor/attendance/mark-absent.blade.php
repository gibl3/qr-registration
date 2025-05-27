@extends('layouts.instructor')

@section('title', 'Mark Absent Students')

@section('attendance-bg', 'bg-neutral-200/90')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 sm:gap-0">
        <h1 class="text-2xl font-bold text-gray-800">Mark Absent Students</h1>
        <a href="{{ route('instructor.attendance.index') }}" class="btn-text flex items-center space-x-2">
            <span class="material-symbols-rounded">arrow_back</span>
            <span>Back to Attendance</span>
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm p-4">
        <form action="{{ route('instructor.attendance.markAbsentPage') }}" method="GET" class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
            <!-- Subject Filter -->
            <select name="subject_id" class="min-w-[200px] rounded-full border-neutral-300 text-sm bg-neutral-200/50 py-1.5 px-3">
                <option value="">Select a subject...</option>
                @foreach ($subjects as $subject)
                <option value="{{ $subject->id }}" {{ $subjectId == $subject->id ? 'selected' : '' }}>
                    {{ $subject->subject_code }} - {{ $subject->subject_name }}
                </option>
                @endforeach
            </select>

            <!-- Date Filter -->
            <div class="flex items-center gap-x-2">
                <input type="date" name="date" value="{{ $date }}" class="min-w-[180px] rounded-full border-neutral-300 text-sm bg-neutral-200/50 py-1.5 px-3">
                <button type="button" id="clear-date" class="btn-base p-0">
                    <span class="material-symbols-rounded text-neutral-600">clear</span>
                </button>
            </div>

            <button type="submit" class="btn-filled py-1.5 px-3">
                <span class="material-symbols-rounded">filter_alt</span>
                Filter
            </button>
        </form>
    </div>

    <!-- Students List -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="p-4 border-b border-neutral-200">
            <h2 class="text-lg font-semibold text-gray-800">Students to Mark as Absent</h2>
            <p class="text-sm text-gray-600">Select students who are absent for {{ \Carbon\Carbon::parse($date)->format('F d, Y') }}</p>
        </div>

        <form id="mark-absent-form" action="{{ route('instructor.attendance.markAbsent') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="date" value="{{ $date }}">
            <input type="hidden" name="subject_id" value="{{ $subjectId }}">

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <input type="checkbox" id="select-all" class="rounded border-neutral-300 text-red-600 focus:ring-red-500">
                            </th>
                            <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student ID</th>
                            <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($students as $student)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                                <input type="checkbox" name="student_ids[]" value="{{ $student->id }}" class="student-checkbox rounded border-neutral-300 text-red-600 focus:ring-red-500">
                            </td>
                            <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-500">{{ $student->student_id }}</div>
                            </td>
                            <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $student->first_name }} {{ $student->last_name }}</div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-4 sm:px-6 py-4 text-center text-sm text-gray-500">
                                @if($subjectId)
                                No students available to mark as absent.
                                @else
                                Please select a subject to view students.
                                @endif
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($students->isNotEmpty())
            <div class="p-4 border-t border-gray-200 flex justify-end">
                <button type="submit" class="btn-filled bg-red-600 hover:bg-red-700">
                    <span class="material-symbols-rounded">person_off</span>
                    Mark Selected as Absent
                </button>
            </div>
            @endif
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectAllCheckbox = document.getElementById('select-all');
        const studentCheckboxes = document.querySelectorAll('.student-checkbox');
        const clearDate = document.getElementById('clear-date');
        const dateInput = document.querySelector('input[name="date"]');
        const form = document.getElementById('mark-absent-form');

        // Select all functionality
        selectAllCheckbox.addEventListener('change', function() {
            studentCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });

        studentCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const allChecked = Array.from(studentCheckboxes).every(cb => cb.checked);
                selectAllCheckbox.checked = allChecked;
            });
        });

        // Clear date functionality
        clearDate.addEventListener('click', function() {
            dateInput.value = '';
        });

        // Form submission
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            const selectedStudents = document.querySelectorAll('input[name="student_ids[]"]:checked');
            if (selectedStudents.length === 0) {
                alert('Please select at least one student to mark as absent.');
                return;
            }

            if (confirm('Are you sure you want to mark the selected students as absent?')) {
                const formData = new FormData(this);

                fetch(this.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.errors) {
                            // Show errors
                            const errorMessage = data.errors.join('\n');
                            alert('Some students could not be marked as absent:\n' + errorMessage);
                        }

                        if (data.marked_students && data.marked_students.length > 0) {
                            // Show success message
                            alert(data.message);
                            // Redirect back to attendance index
                            window.location.href = '{{ route("instructor.attendance.index") }}';
                        } else {
                            alert('No students were marked as absent.');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred while marking students as absent. Please try again.');
                    });
            }
        });
    });
</script>
@endpush