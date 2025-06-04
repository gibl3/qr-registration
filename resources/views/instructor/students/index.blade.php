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

@section('students-bg', 'bg-neutral-200/90')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2">
        <h1 class="text-2xl font-bold text-gray-800">Registered Students</h1>
        <div class="flex gap-x-2">
            <button class="btn-filled-tonal-warning" id="delete-btn">
                <span class="material-symbols-rounded">delete</span>
            </button>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md">
        <div class="overflow-x-auto max-h-[calc(100vh-12rem)]">
            <table class="min-w-[950px] w-full divide-y divide-gray-200 text-xs md:text-sm">
                <thead class="bg-gray-50 sticky top-0 z-10">
                    <tr>
                        <th class="px-3 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">
                            <input type="checkbox" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        </th>
                        <th class="px-3 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Student ID</th>
                        <th class="px-3 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-3 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Program</th>
                        <th class="px-3 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Year</th>
                        <th class="px-3 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Section</th>
                        <th class="px-3 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($students as $student)
                    <tr class="hover:bg-gray-50" data-id="{{ $student->id }}">
                        <td class="px-3 py-4 whitespace-nowrap">
                            <input type="checkbox" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" data-id="{{ $student->id }}">
                        </td>
                        <td class="px-3 py-4 whitespace-nowrap">
                            <div class="text-gray-500">{{ $student->student_id ?? 'N/A' }}</div>
                        </td>
                        <td class="px-3 py-4 whitespace-nowrap">
                            <div class="font-medium text-gray-900">{{ $student->first_name }} {{ $student->last_name }}</div>
                        </td>
                        <td class="px-3 py-4 whitespace-nowrap">
                            <div class="text-gray-500">{{ $student->program->abbreviation }}</div>
                        </td>
                        <td class="px-3 py-4 whitespace-nowrap">
                            <div class="text-gray-500">{{ $student->year_level }}</div>
                        </td>
                        <td class="px-3 py-4 whitespace-nowrap">
                            <div class="text-gray-500">{{ $student->section }}</div>
                        </td>
                        <td class="px-3 py-4 whitespace-nowrap font-medium">
                            <a href="{{ route('instructor.students.edit', ['student' => $student]) }}"
                                class="text-blue-600 hover:text-blue-900">Edit</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@vite(['resources/js/student/delete-student.js'])
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectAllCheckbox = document.querySelector('thead input[type="checkbox"]');
        const studentCheckboxes = document.querySelectorAll('tbody input[type="checkbox"]');

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
    });
</script>
@endpush