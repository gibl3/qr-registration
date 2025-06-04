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

@section('title', 'Subjects ')

@section('subjects-bg', 'bg-neutral-200/90')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-800">Manage Subjects</h1>
        <button onclick="openModal()" class="btn-filled">
            <span class="material-symbols-rounded">add</span>
            Add subject
        </button>
    </div>
    <!-- Subject Creation Modal -->
    <dialog id="subject-modal" class="inset-0 m-auto rounded-lg shadow-lg backdrop:bg-black/50">
        <div class="bg-neutral-50 p-6 w-md space-y-4">
            <div class="flex justify-between items-center">
                <h2 id="modal-title" class="text-xl font-semibold">Add New Subject</h2>

                <button onclick="closeModal()" class="btn-text rounded-full p-1 hover:bg-neutral-200 hover:shadow-sm focus:outline-none">
                    <span class="material-symbols-rounded text-neutral-600">close</span>
                </button>
            </div>

            <div id="success-message" class="hidden bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline"></span>
            </div>

            <form action="{{ route('instructor.subjects.advise') }}" method="POST" class="space-y-4" id="subject-form">
                @csrf

                <!-- Subject Selection with Autocomplete -->
                <div 
                    x-data='{
                        query: "",
                        subjects: @json($subjectsAdvisable->values()),
                        filtered: [],
                        show: false,
                        readonly: false,
                        select(subject) {
                            this.query = subject.name + " (" + subject.code + ")";
                            this.show = false;
                            $refs.code.value = subject.code;
                            $refs.name.value = subject.name;
                        },
                        search() {
                            if (this.query.length === 0) {
                                this.filtered = [];
                                this.show = false;
                                return;
                            }
                            this.filtered = this.subjects.filter(s =>
                                s.name.toLowerCase().includes(this.query.toLowerCase()) ||
                                s.code.toLowerCase().includes(this.query.toLowerCase())
                            );
                            this.show = this.filtered.length > 0;
                        }
                    }'
                    class="space-y-2 relative">
                    <label for="subject_search" class="block text-sm font-sm text-neutral-700">Search Subject</label>
                    <input
                        type="text"
                        id="subject_search"
                        x-model="query"
                        :readonly="readonly"
                        placeholder="Type subject name or code..."
                        class="input-base"
                        autocomplete="off"
                        @input="search"
                        @focus="search"
                        @blur="setTimeout(() => show = false, 100)">
                    <!-- Suggestions Dropdown -->
                    <ul x-show="show" class="absolute z-10 bg-white border border-neutral-200 rounded shadow w-full mt-1 max-h-40 overflow-auto">
                        <template x-for="subject in filtered" :key="subject.id">
                            <li @mousedown.prevent="select(subject)"
                                class="px-4 py-2 hover:bg-emerald-100 cursor-pointer text-sm">
                                <span x-text="subject.name"></span>
                                (<span x-text="subject.code"></span>)
                            </li>
                        </template>
                    </ul>
                    <!-- Hidden fields for form submission -->
                    <input type="hidden" name="code" x-ref="code" id="subject-code">
                    <input type="hidden" name="name" x-ref="name" id="subject-name">
                </div>
                <div class="space-y-2">
                    <label for="program_input" class="block text-sm font-sm text-neutral-700">Program</label>
                    <div class="input-base p-0">
                        <select id="program_input" name="program_id" class="input-base border-r-[12px] border-transparent text-sm sm:text-base">
                            @foreach($programs as $program)
                            <option value="{{ $program->id }}">{{ $program->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="space-y-2">
                    <label for="year_input" class="block text-sm font-sm text-neutral-700">Year Level</label>
                    <div class="input-base p-0">
                        <select id="year_input" name="year_level" class="input-base border-r-[12px] border-transparent text-sm sm:text-base">
                            <option value="1">1st year</option>
                            <option value="2">2nd year</option>
                            <option value="3">3rd year</option>
                            <option value="4">4th year</option>
                        </select>
                    </div>
                </div>
                <div class="space-y-2">
                    <label for="section_input" class="block text-sm font-sm text-neutral-700">Section</label>
                    <input type="text" name="section" id="section_input" required class="input-base" placeholder="Enter section (A-E)" autocomplete="off">
                </div>

                <p id="edit-subject-warning" class="block text-xs font-sm text-neutral-700 italic hidden">
                    Changing the section will update the students enrolled.</p>

                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeModal()"
                        class="btn-text">
                        Cancel
                    </button>

                    <button type="submit" id="modal-submit-btn" class="btn-filled">
                        Create subject
                    </button>
                </div>
            </form>
        </div>
    </dialog>

    <!-- Subjects Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($subjectsAdvised as $subject)
        <div class="bg-white rounded-lg shadow-sm p-6 hover:shadow-md transition-all duration-200 flex flex-col">
            <div class="flex justify-between items-start mb-4">
                <div class="space-y-1">
                    <h3 class="text-lg font-semibold text-gray-800">{{ $subject->name }}</h3>
                    <p class="text-sm font-medium text-emerald-600">{{ $subject->code }}</p>
                    <p class="text-sm text-gray-500">{{ $subject->program->abbreviation . " " . $subject->year_level . $subject->pivot->section }}</p>
                </div>

                <div class="flex space-x-1">
                    <button
                        type="button"
                        class="btn-text rounded-full p-1.5 hover:bg-neutral-200/70 hover:shadow-sm focus:outline-none transition-colors"
                        onclick="editSubject(
                            '{{ addslashes($subject->code) }}',
                            '{{ addslashes($subject->name) }}',
                            '{{ addslashes($subject->pivot->section) }}'
                        )">
                        <span class="material-symbols-rounded text-neutral-600">edit</span>
                    </button>

                    <form action="{{ route('instructor.subjects.advise.destroy', $subject) }}" method="POST" class="inline" id="delete-form">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="btn-text rounded-full p-1.5 hover:bg-red-200/70 hover:shadow-sm focus:outline-none transition-colors">
                            <span class="material-symbols-rounded text-red-600">delete</span>
                        </button>
                    </form>
                </div>
            </div>

            <p class="text-gray-600 text-sm line-clamp-2 mb-4">{{ $subject->description }}</p>

            <div class="flex items-center justify-between pt-4 border-t border-gray-100 mt-auto">
                <div class="flex items-center space-x-2">
                    <span class="material-symbols-rounded text-neutral-500 md-icon-20">group</span>
                    {{-- <span class="text-sm text-gray-500">{{ $subject->students->count() ?? 0 }} students</span> --}}
                </div>

                <a href="{{ route('instructor.subjects.show', ['subject' => $subject]) }}"
                    class="btn-link text-neutral-600 group text-sm font-medium">
                    View students
                    <span class="material-symbols-rounded text-neutral-600 group-hover:text-emerald-500 transition-colors md-icon-20">chevron_right</span>
                </a>
            </div>
        </div>
        @empty
        <div class="col-span-full">
            <div class="text-center py-12 bg-white rounded-lg shadow-sm">
                <span class="material-symbols-rounded text-neutral-600 md-icon-48">newsstand</span>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No subjects</h3>
                <p class="mt-1 text-sm text-gray-500">Get started by creating a new subject.</p>
                {{-- <div class="mt-6">
                    <button onclick="openModal()"
                        class="btn-filled mx-auto">
                        Add new subject
                    </button>
                </div> --}}
            </div>
        </div>
        @endforelse
    </div>
</div>
@endsection

@push('scripts')
<script>
    let alpineComponent = null;
    
    function openModal() {
        document.getElementById('subject-modal').showModal();
    }

    function closeModal() {
        document.getElementById('subject-form').reset();
        document.getElementById('subject-modal').close();
        document.getElementById('success-message').classList.add('hidden');

        setTimeout(() => {
            const alpineComponent = document.getElementById('subject_search').closest('[x-data]');
            if (alpineComponent && alpineComponent.__x) {
                alpineComponent.__x.$data.query = "";
                alpineComponent.__x.$data.readonly = false;
            }
            document.getElementById('subject_search').disabled = false;
            document.getElementById('subject_search').classList.remove('bg-gray-100', 'cursor-not-allowed');
        }, 100);

        // Reset modal to add mode
        document.getElementById('modal-title').textContent = 'Add New Subject';
        document.getElementById('modal-submit-btn').textContent = 'Create subject';
        document.getElementById('subject-form').action = "{{ route('instructor.subjects.advise') }}";
        document.querySelector('#subject-form input[name=_method]')?.remove();
        document.getElementById('edit-subject-warning').classList.add('hidden');
    }

    function editSubject(code, name, section) {
        openModal();
        document.getElementById('edit-subject-warning').classList.remove('hidden');
        document.getElementById('modal-title').textContent = `Edit Subject`;
        document.getElementById('modal-submit-btn').textContent = 'Update subject';

        setTimeout(() => {
            const alpineComponent = document.getElementById('subject_search').closest('[x-data]');
            if (alpineComponent && alpineComponent.__x) {
                alpineComponent.__x.$data.query = `${name} (${code})`;
                alpineComponent.__x.$data.readonly = true;
            }
            document.getElementById('subject_search').value = `${name} (${code})`;
            document.getElementById('subject_search').classList.add('bg-gray-100', 'cursor-not-allowed');
            document.getElementById('subject_search').disabled = true;

            document.getElementById('subject-code').value = code;
            document.getElementById('subject-name').value = name;
            document.getElementById('section_input').value = section;
        }, 100); // <-- increase timeout to 100ms

        // Set form action to update route
        const form = document.getElementById('subject-form');
        form.action = `/instructor/subjects/advise/update`;
        // Add hidden _method input for PUT
        if (!form.querySelector('input[name=_method]')) {
            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'PUT';
            form.appendChild(methodInput);
        } else {
            form.querySelector('input[name=_method]').value = 'PUT';
        }
    }

    function displayValidationErrors(errors) {
        console.log(errors)
        if (errors) {
            // Display validation errors
            for (const [field, messages] of Object.entries(errors)) {
                const input = document.querySelector(`[name="${field}"]`);
                if (input) {
                    const errorDiv = document.createElement("p");
                    errorDiv.classList.add(
                        "text-red-500",
                        "text-sm",
                        "error-message"
                    );
                    errorDiv.textContent = messages[0]; // Display the first error message
                    input.parentElement.appendChild(errorDiv);

                    errorTimeout = setTimeout(() => {
                        errorDiv.classList.add("hidden");
                    }, 3000);
                }
            }
        } else {
            // Handle other errors
            alert("An error occurred while registering the student.");
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Add form submission handler
        document.getElementById('subject-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            const form = this;
            const formData = new FormData(form);

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        "X-CSRF-TOKEN": document
                            .querySelector('meta[name="csrf-token"]')
                            .getAttribute("content"),
                        "Accept": "application/json",
                        "X-Requested-With": "XMLHttpRequest"
                    },
                    credentials: "same-origin"
                });

                if (!response.ok) {
                    if (response.status === 422) {
                        const data = await response.json();
                        throw data.errors; // Throw validation errors
                    } else {
                        throw new Error("Failed to register student.");
                    }
                } else {
                    const data = await response.json();
                    const successMessage = document.getElementById('success-message');
                    successMessage.querySelector('span').textContent = data.message;
                    successMessage.classList.remove('hidden');

                    window.location.reload();
                }
            } catch (errors) {
                displayValidationErrors(errors);
            }
        });

        alpineComponent = document.getElementById('subject_search').closest('[x-data]');

        // Delete subject
        document.querySelectorAll("#delete-form").forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                if (confirm('Are you sure you want to delete this subject?')) {
                    const formData = new FormData(this);

                    fetch(this.action, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                "X-CSRF-TOKEN": document
                                    .querySelector('meta[name="csrf-token"]')
                                    .getAttribute("content"),
                                "Accept": "application/json",
                                "X-Requested-With": "XMLHttpRequest"
                            },
                            credentials: "same-origin"
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.message) {
                                alert(data.message);
                                window.location.reload();
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('An error occurred while deleting the subject.');
                        });
                }
            });
        });
    })
</script>
@endpush