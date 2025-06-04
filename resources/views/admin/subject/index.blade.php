<!-- filepath: resources/views/admin/subject/index.blade.php -->
@extends('layouts.admin')

@section('title', 'Subjects')
@section('subject-bg', 'bg-neutral-200/90')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2">
        <h1 class="text-2xl font-bold text-gray-800">Subjects</h1>
        <button class="btn-filled-tonal" id="add-modal-btn">
            <span class="material-symbols-rounded">add</span>
            Add Subject
        </button>
    </div>

    <div class="bg-white rounded-lg shadow-md">
        <div class="overflow-x-auto max-h-[calc(100vh-12rem)]">
            <table class="min-w-full divide-y divide-gray-200 text-xs md:text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Code</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Department</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($subjects as $subject)
                    <tr class="hover:bg-gray-50" data-id="{{ $subject->id }}">
                        <td class="px-4 py-4 whitespace-nowrap">
                            <div class="font-medium text-gray-900">{{ $subject->code }}</div>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            <div class="text-gray-900">{{ $subject->name }}</div>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            <div class="text-gray-900">{{ $subject->department->name ?? 'NONE' }}</div>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap font-medium flex gap-2">
                            <button class="text-blue-600 hover:text-blue-900" onclick="openEditModal({{ $subject->id }}, 
                                '{{ addslashes($subject->code) }}', 
                                '{{ addslashes($subject->name) }}', 
                                '{{ $subject->department_id }}')">Edit</button>
                            <form action="{{ route('admin.subject.destroy', $subject) }}" method="post" class="inline" onsubmit="return confirm('Delete this subject?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-8 text-gray-400">No subjects found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Subject Modal -->
<div id="add-modal" class="fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
        <h2 class="text-xl font-bold mb-4">Add Subject</h2>
        <form id="store-subject-form" action="{{ route('admin.subject.store') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Code</label>
                <input type="text" name="code" class="input-base w-full" required autocomplete="off">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Name</label>
                <input type="text" name="name" class="input-base w-full" required autocomplete="off">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Department</label>
                <select id="department-select-add" name="department_id" class="input-base w-full">
                    @foreach($departments as $department)
                        <option value="{{ $department->id }}">{{ $department->name }}</option>
                    @endforeach
                    <option value="">NONE</option>
                </select>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" class="btn-text" onclick="closeAddModal()">Cancel</button>
                <button type="submit" class="btn-filled">Add</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Subject Modal -->
<div id="edit-modal" class="fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
        <h2 class="text-xl font-bold mb-4">Edit Subject</h2>
        <form id="edit-form" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Code</label>
                <input type="text" name="code" id="edit-code" class="input-base w-full" required autocomplete="off">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Name</label>
                <input type="text" name="name" id="edit-name" class="input-base w-full" required autocomplete="off">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Department</label>
                <select name="department_id" id="edit-department" class="input-base w-full">
                    @foreach($departments as $department)
                        <option value="{{ $department->id }}">{{ $department->name }}</option>
                    @endforeach
                    <option value="">NONE</option>
                </select>
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
    function openAddModal() {
        document.getElementById('add-modal').classList.remove('hidden');
    }
    function closeAddModal() {
        document.getElementById('add-modal').classList.add('hidden');
    }
    function openEditModal(id, code, name, departmentId) {
        document.getElementById('edit-modal').classList.remove('hidden');
        document.getElementById('edit-code').value = code;
        document.getElementById('edit-name').value = name;
        document.getElementById('edit-department').value = departmentId;
        document.getElementById('edit-form').action = `/admin/subject/${id}/update`;
    }
    function closeEditModal() {
        document.getElementById('edit-modal').classList.add('hidden');
    }
    async function storeSubject(event) {
        event.preventDefault();
        const form = document.getElementById('store-subject-form');
        const formData = new FormData(form);
        
        const response = await fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });

        const data = await response.json();
        if (response.ok) {
            location.reload();
            return;
        }

        alert('Failed to add subject. Please try again.');
        console.error('Error:', data.error);
    }
    
    async function updateSubject(event) {
        event.preventDefault();
        const form = document.getElementById('edit-form');
        const formData = new FormData(form);
        
        const response = await fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });

        const data = await response.json();
        if (response.ok) {
            location.reload();
            return;
        }

        alert('Failed to update subject. Please try again.');
        console.error('Error:', data.error);
    }

    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById("add-modal-btn").addEventListener("click", openAddModal);

        document.getElementById("store-subject-form").addEventListener("submit", storeSubject);
        document.getElementById("edit-form").addEventListener("submit", updateSubject);
    });
</script>

@endpush
@endsection