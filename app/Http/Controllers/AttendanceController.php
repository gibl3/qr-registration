<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Subject;

class AttendanceController extends Controller
{

    public function index()
    {
        $attendances = Attendance::with('subject')
            ->whereHas('subject', function ($query) {
                $query->where('instructor_id', auth()->id());
            })->get();
        
        $subjects = Subject::where('instructor_id', auth()->id())->get();
        // if ($attendances->isEmpty()) {
        //     return view('instructor.attendance.index', compact('subjects'));
        // }

        return view('instructor.attendance.index', compact('attendances', 'subjects'));
    }

    public function edit(Attendance $attendance)
    {
        return view('admin.attendance.edit', ['attendance' => $attendance]);
    }
}
