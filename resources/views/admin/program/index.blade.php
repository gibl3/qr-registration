<!-- filepath: resources/views/admin/program/index.blade.php -->
@extends('layouts.admin')

@section('title', 'Programs')
@section('program-bg', 'bg-neutral-200/90')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2">
        <h1 class="text-2xl font-bold text-gray-800">Programs</h1>
        {{-- disable button if $departments is empty --}}
        <button class="btn-filled-tonal" id="add-modal-btn" {{ $departments->isEmpty() ? 'disabled' : '' }}>
            <span class="material-symbols-rounded">add</span>
            Add Program
        </button>
    </div>

    <div class="bg-white rounded-lg shadow-md">
        <div class="overflow-x-auto max-h-[calc(100vh-12rem)]">
            <table class="min-w-full divide-y divide-gray-200 text-xs md:text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">
                            <input type="checkbox" id="select-all" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        </th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Department Name</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Program Name</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($programs as $program)
                    <tr class="hover:bg-gray-50" data-id="{{ $program->id }}">
                        <td class="px-4 py-4 whitespace-nowrap">
                            <input type="checkbox" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 program-checkbox" data-id="{{ $program->id }}">
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            <div class="text-gray-900">{{ $program->department->name ?? '-' }}</div>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            <div class="font-medium text-gray-900">{{ $program->name . " ($program->abbreviation)" }}</div>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap font-medium flex gap-2">
                            <button class="text-blue-600 hover:text-blue-900" 
                                onclick="openEditModal({{ $program->id }}, 
                                    '{{ addslashes($program->name) }}', 
                                    '{{ $program->department_id }}', 
                                    '{{ $program->abbreviation }}')">Edit</button>
                            <form action="{{ route('admin.program.destroy', $program) }}" method="post" class="inline" onsubmit="return confirm('Delete this program?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-8 text-gray-400">No programs found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Program Modal -->
<div id="add-modal" class="fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
        <h2 class="text-xl font-bold mb-4">Add Program</h2>
        <form id="store-program-form" action="{{ route('admin.program.store') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Department</label>
                <select name="department_id" class="input-base w-full" required>
                    @foreach($departments as $department)
                        <option value="{{ $department->id }}">{{ $department->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Program Name</label>
                <input type="text" name="name" class="input-base w-full" required autocomplete="off" placeholder="Enter program name">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Abbreviation</label>
                <input type="text" name="abbreviation" class="input-base w-full" required autocomplete="off" placeholder="Example: BSIT">
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" class="btn-text" onclick="closeAddModal()">Cancel</button>
                <button type="submit" class="btn-filled">Add</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Program Modal -->
<div id="edit-modal" class="fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
        <h2 class="text-xl font-bold mb-4">Edit Program</h2>
        <form id="edit-form" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Department</label>
                <select name="department_id" id="edit-department" class="input-base w-full" required>
                    @foreach($departments as $department)
                        <option value="{{ $department->id }}">{{ $department->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Program Name</label>
                <input type="text" name="name" id="edit-name" class="input-base w-full" required autocomplete="off">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Abbreviation</label>
                <input type="text" name="abbreviation" id="edit-abbreviation" class="input-base w-full" required autocomplete="off" placeholder="Example: BSIT">
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" class="btn-text" onclick="closeEditModal()">Cancel</button>
                <button type="submit" class="btn-filled">Update</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Modal logic
    function openAddModal() {
        document.getElementById('add-modal').classList.remove('hidden');
    }
    function closeAddModal() {
        document.getElementById('add-modal').classList.add('hidden');
    }
    function openEditModal(id, name, departmentId, abbreviation) {
        document.getElementById('edit-modal').classList.remove('hidden');
        document.getElementById('edit-name').value = name;
        document.getElementById('edit-department').value = departmentId;
        document.getElementById('edit-abbreviation').value = abbreviation;
        document.getElementById('edit-form').action = `/admin/program/${id}/update`;
    }
    function closeEditModal() {
        document.getElementById('edit-modal').classList.add('hidden');
    }

    // Select all logic
    document.addEventListener('DOMContentLoaded', function() {
        const selectAll = document.getElementById('select-all');
        const checkboxes = document.querySelectorAll('.program-checkbox');
        selectAll.addEventListener('change', function() {
            checkboxes.forEach(cb => cb.checked = this.checked);
        });

        document.getElementById("add-modal-btn").addEventListener("click", openAddModal);
    });
</script>
@endpush
@endsection