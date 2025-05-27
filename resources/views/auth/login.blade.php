@extends('layouts.default-nav')

@section('title', 'Login')

@section('content')
<div class="flex items-center justify-center mx-auto flex-1">
    <div class="w-full max-w-md bg-white rounded-lg shadow-md p-8">
        <h2 class="text-2xl font-semibold text-center text-neutral-700  mb-6">Login as</h2>

        <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-lg mb-4 hidden" id="errors-div">
        </div>

        <form method="post" id="login-form" action="" class="space-y-6">
            @csrf
            @method('post')

            <div class="space-y-4">
                <div class="flex justify-center">
                    <div class="grid grid-cols-2 gap-4 max-w-md">
                        <!-- Admin Role -->
                        <label class="relative flex cursor-pointer">
                            <input type="radio" name="role" value="instructor" class="peer sr-only">
                            <div class="btn-filled-tonal w-full bg-neutral-200/60  hover:bg-emerald-50 peer-checked:border-red-500 peer-checked:bg-emerald-50 transition-colors">
                                <span class="material-symbols-rounded text-emerald-600">
                                    school
                                </span>
                                <p class="text-sm font-medium text-neutral-700">Instructor</p>
                            </div>
                        </label>

                        <!-- Instructor Role -->
                        <label class="flex flex-col cursor-pointer">
                            <input type="radio" name="role" value="admin" class="peer sr-only" checked>
                            <div class="btn-filled-tonal w-full bg-neutral-200/60  hover:bg-emerald-50 peer-checked:border-red-500 peer-checked:bg-emerald-50 transition-colors">
                                <span class="material-symbols-rounded text-emerald-600">
                                    manage_accounts
                                </span>
                                <p class="text-sm font-medium text-neutral-700">Admin</p>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <div class="space-y-2">
                <label for="email" class="block text-sm font-medium text-neutral-700">Email Address</label>
                <input type="text" id="email" name="email" placeholder="Enter email address"
                    class="w-full px-4 py-2 border border-neutral-300 rounded-md focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
            </div>

            <div class="space-y-2">
                <label for="password" class="block text-sm font-medium text-neutral-700">Password</label>

                <div class="relative flex items-center justify-end">
                    <input type="password" id="password" name="password" placeholder="Enter password"
                        class="input-base pr-10">

                    <button type="button" id="toggle-password" class="absolute flex justify-center items-center p-0 mr-3">
                        <span class="material-symbols-rounded text-neutral-500">visibility</span>
                    </button>
                </div>
            </div>

            <button type="submit" class="w-full btn-filled">
                Login
            </button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
@vite(['resources/js/auth/login.js'])
@endpush