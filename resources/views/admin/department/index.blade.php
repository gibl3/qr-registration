<!-- filepath: resources/views/admin/department/index.blade.php -->
@extends('layouts.admin')

@section('title', 'Departments')
@section('department-bg', 'bg-neutral-200/90')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2">
        <h1 class="text-2xl font-bold text-gray-800">Departments</h1>
        <button class="btn-filled-tonal" id="add-modal-btn">
            <span class="material-symbols-rounded">add</span>
            Add Department
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
                        {{-- <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Description</th> --}}
                        <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($departments as $department)
                    <tr class="hover:bg-gray-50" data-id="{{ $department->id }}">
                        <td class="px-4 py-4 whitespace-nowrap">
                            <input type="checkbox" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 department-checkbox" data-id="{{ $department->id }}">
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            <div class="font-medium text-gray-900">{{ $department->name }}</div>
                        </td>
                        {{-- <td class="px-4 py-4 whitespace-nowrap">
                            <div class="text-gray-500">{{ $department->description }}</div>
                        </td> --}}
                        <td class="px-4 py-4 whitespace-nowrap font-medium flex gap-2">
                            <button class="text-blue-600 hover:text-blue-900" onclick="openEditModal({{ $department->id }}, '{{ addslashes($department->name) }}', '{{ addslashes($department->description) }}')">Edit</button>
                            <form action="{{ route('admin.department.destroy', $department) }}" method="post" class="inline" onsubmit="return confirm('Delete this department?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-8 text-gray-400">No departments found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Department Modal -->
<div id="add-modal" class="fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
        <h2 class="text-xl font-bold mb-4">Add Department</h2>
        <form id="store-department-form" action="{{ route('admin.department.store') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Department Name</label>
                <input type="text" name="name" class="input-base w-full" required autocomplete="off">
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" class="btn-text" onclick="closeAddModal()">Cancel</button>
                <button type="submit" class="btn-filled">Add</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Department Modal -->
<div id="edit-modal" class="fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
        <h2 class="text-xl font-bold mb-4">Edit Department</h2>
        <form id="edit-form" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Department Name</label>
                <input type="text" name="name" id="edit-name" class="input-base w-full" required autocomplete="off">
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
    function openEditModal(id, name) {
        document.getElementById('edit-modal').classList.remove('hidden');
        document.getElementById('edit-name').value = name;
        document.getElementById('edit-form').action = `/admin/department/${id}/update`;
    }
    function closeEditModal() {
        document.getElementById('edit-modal').classList.add('hidden');
    }

    // Select all logic
    document.addEventListener('DOMContentLoaded', function() {
        const selectAll = document.getElementById('select-all');
        const checkboxes = document.querySelectorAll('.department-checkbox');
        selectAll.addEventListener('change', function() {
            checkboxes.forEach(cb => cb.checked = this.checked);
        });

        document.getElementById("add-modal-btn").addEventListener("click", openAddModal);
        
    });
</script>
@vite(['resources/js/department/store.js'])
@endpush
@endsection