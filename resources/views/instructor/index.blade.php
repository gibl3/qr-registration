@extends('layouts.instructor')

@section('title', 'Instructor Dashboard')

@section('dashboard-bg', 'bg-neutral-200/90')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-800">Dashboard</h1>
    </div>

    <!-- Overall Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Students -->
        <div class="bg-white rounded-lg shadow-sm p-6 space-y-4 ring-[0.25px] ring-neutral-400">
            <div class="flex items-center space-x-3">
                <div class="bg-blue-100 p-2 rounded-lg">
                    <span class="material-symbols-rounded text-blue-600">group</span>
                </div>
                <h3 class="font-semibold text-lg text-neutral-950/80">Total Students</h3>
            </div>
            <p class="text-3xl font-bold text-blue-600">{{ $totalStudents }}</p>
        </div>

        <!-- Present Today -->
        <div class="bg-white rounded-lg shadow-sm p-6 space-y-4 ring-[0.25px] ring-neutral-400">
            <div class="flex items-center space-x-3">
                <div class="bg-green-100 p-2 rounded-lg">
                    <span class="material-symbols-rounded text-green-600">check_circle</span>
                </div>
                <h3 class="font-semibold text-lg text-neutral-950/80">Present Today</h3>
            </div>
            <p class="text-3xl font-bold text-green-600">{{ $presentToday }}</p>
        </div>

        <!-- Absent Today -->
        <div class="bg-white rounded-lg shadow-sm p-6 space-y-4 ring-[0.25px] ring-neutral-400">
            <div class="flex items-center space-x-3">
                <div class="bg-red-100 p-2 rounded-lg">
                    <span class="material-symbols-rounded text-red-600">cancel</span>
                </div>
                <h3 class="font-semibold text-lg text-neutral-950/80">Absent Today</h3>
            </div>
            <p class="text-3xl font-bold text-red-600">{{ $absentToday }}</p>
        </div>

        <!-- Gender Distribution -->
        <div class="bg-white rounded-lg shadow-sm p-6 space-y-4 ring-[0.25px] ring-neutral-400">
            <div class="flex items-center space-x-3">
                <div class="bg-purple-100 p-2 rounded-lg">
                    <span class="material-symbols-rounded text-purple-600">wc</span>
                </div>
                <h3 class="font-semibold text-lg text-neutral-950/80">Gender Distribution</h3>
            </div>
            <div class="flex gap-x-6">
                <div class="flex items-baseline">
                    <p class="text-3xl font-bold text-purple-600">{{ $maleCount }}</p>
                    <p class="ml-2 text-sm text-gray-500">Male</p>
                </div>
                <div class="flex items-baseline">
                    <p class="text-3xl font-bold text-purple-600">{{ $femaleCount }}</p>
                    <p class="ml-2 text-sm text-gray-500">Female</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Subject-wise Statistics -->
    <div class="mt-8">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Subject Overview</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($subjects as $subject)
            <div class="bg-white rounded-lg shadow-sm p-6 space-y-4 ring-[0.25px] ring-neutral-400">
                <div class="flex items-center justify-between">
                    <h3 class="font-semibold text-lg text-neutral-950/80">{{ $subject->subject_name }}</h3>
                    <span class="text-sm text-gray-500">{{ $subject->subject_code }}</span>
                </div>

                <!-- Attendance Stats -->
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Present Today</span>
                        <span class="font-semibold text-green-600">{{ $subject->present_count }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Absent Today</span>
                        <span class="font-semibold text-red-600">{{ $subject->absent_count }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Total Students</span>
                        <span class="font-semibold text-blue-600">{{ $subject->total_students }}</span>
                    </div>
                </div>

                <!-- Gender Distribution -->
                <div class="pt-3 border-t border-gray-100">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Male</span>
                        <span class="font-semibold text-purple-600">{{ $subject->male_count }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Female</span>
                        <span class="font-semibold text-purple-600">{{ $subject->female_count }}</span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection