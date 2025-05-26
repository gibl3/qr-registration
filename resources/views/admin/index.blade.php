@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('dashboard-bg', 'bg-neutral-200/90')

@section('content')
<div class="space-y-6 px-2 md:px-6">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2">
        <h1 class="text-2xl font-bold text-gray-800">Dashboard</h1>
    </div>

    <!-- Metrics Section -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
        <!-- Total Instructors Metric -->
        <div class="bg-neutral-50 rounded-lg shadow-sm p-4 md:p-6 space-y-4 ring-[0.25px] ring-neutral-400">
            <div class="flex items-center space-x-3 mb-4">
                <div class="bg-blue-100 p-2 rounded-lg">
                    <span class="material-symbols-rounded text-blue-600">groups</span>
                </div>
                <h3 class="font-semibold text-lg text-neutral-950/80">Total Instructors</h3>
            </div>
            <div class="flex items-baseline">
                <p class="text-3xl font-bold text-blue-600">{{ $totalInstructors ?? 0 }}</p>
            </div>
        </div>
        <!-- Add more metric cards here as needed -->
    </div>
</div>
@endsection