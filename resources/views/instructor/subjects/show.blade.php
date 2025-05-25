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

@section('title', 'View Students')

@section('subjects-bg', 'bg-neutral-200/90')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div class="">
            <p class="text-gray-600">{{ $subject->subject_name }}</p>
            <h1 class="text-2xl font-bold text-gray-800">Registered Students</h1>
        </div>

        <div class="flex gap-x-2">
            <button type="button" onclick="enrollSelected()" class="btn-filled">
                <span class="material-symbols-rounded">person_add</span>
                Enroll Selected
            </button>
        </div>
    </div>

    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
        <span class="block sm:inline">{{ session('success') }}</span>
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
        <span class="block sm:inline">{{ session('error') }}</span>
    </div>
    @endif

    <div class="bg-white rounded-lg shadow-md">
        <div class="overflow-x-auto max-h-[400px]">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <input type="checkbox" id="select-all" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Program</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Year Level</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student ID</th>
                    </tr>
                </thead>

                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($students as $student)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <input type="checkbox" name="student_ids[]" value="{{ $student->id }}" class="student-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $student->first_name }} {{ $student->last_name }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-500">{{ $student->program }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-500">{{ $student->year_level }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-500">{{ $student->student_id ?? 'N/A' }}</div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                            No students available for enrollment.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Enrolled Students Section -->
    <div class="mt-8">
        <div class="flex items-center justify-between">
            <div class="">
                {{-- <p class="text-gray-600">{{ $subject->subject_name }}</p> --}}

                <h2 class="text-xl font-semibold text-gray-800 mb-4">Currently Enrolled Students</h2>
            </div>

            <button type="button" onclick="unenrollSelected()" class="btn-filled-warning">
                <span class="material-symbols-rounded">person_remove</span>
                Unenroll Selected
            </button>
        </div>
        <div class="bg-white rounded-lg shadow-md">
            <div class="overflow-x-auto max-h-[400px]">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <input type="checkbox" id="select-all-enrolled" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Program</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Year Level</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student ID</th>
                        </tr>
                    </thead>

                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($subject->students as $enrolledStudent)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <input type="checkbox" name="enrolled_student_ids[]" value="{{ $enrolledStudent->id }}" class="enrolled-student-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $enrolledStudent->first_name }} {{ $enrolledStudent->last_name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-500">{{ $enrolledStudent->program }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-500">{{ $enrolledStudent->year_level }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-500">{{ $enrolledStudent->student_id ?? 'N/A' }}</div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                                No students enrolled in this subject yet.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<form id="enroll-form" action="{{ route('instructor.subjects.enroll', $subject) }}" method="POST" class="hidden">
    @csrf
    <input type="hidden" name="student_ids" id="selected-students">
</form>

<form id="unenroll-form" action="{{ route('instructor.subjects.unenroll', $subject) }}" method="POST" class="hidden">
    @csrf
    <input type="hidden" name="student_ids" id="selected-enrolled-students">
</form>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Enrollment checkboxes
        const selectAllCheckbox = document.getElementById('select-all');
        const studentCheckboxes = document.querySelectorAll('.student-checkbox');

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

        // Unenrollment checkboxes
        const selectAllEnrolledCheckbox = document.getElementById('select-all-enrolled');
        const enrolledStudentCheckboxes = document.querySelectorAll('.enrolled-student-checkbox');

        selectAllEnrolledCheckbox.addEventListener('change', function() {
            enrolledStudentCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });

        enrolledStudentCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const allChecked = Array.from(enrolledStudentCheckboxes).every(cb => cb.checked);
                selectAllEnrolledCheckbox.checked = allChecked;
            });
        });
    });

    function enrollSelected() {
        const selectedCheckboxes = document.querySelectorAll('.student-checkbox:checked');
        const selectedIds = Array.from(selectedCheckboxes).map(cb => cb.value);

        if (selectedIds.length === 0) {
            alert('Please select at least one student to enroll.');
            return;
        }

        document.getElementById('selected-students').value = JSON.stringify(selectedIds);
        document.getElementById('enroll-form').submit();
    }

    function unenrollSelected() {
        const selectedCheckboxes = document.querySelectorAll('.enrolled-student-checkbox:checked');
        const selectedIds = Array.from(selectedCheckboxes).map(cb => cb.value);

        if (selectedIds.length === 0) {
            alert('Please select at least one student to unenroll.');
            return;
        }

        if (!confirm('Are you sure you want to unenroll the selected students?')) {
            return;
        }

        document.getElementById('selected-enrolled-students').value = JSON.stringify(selectedIds);
        document.getElementById('unenroll-form').submit();
    }
</script>
@endpush