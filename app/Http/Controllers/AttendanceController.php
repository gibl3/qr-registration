<?php

namespace App\Http\Controllers;

use App\Models\Attendance;

class AttendanceController extends Controller
{

    public function index()
    {
        $attendances = Attendance::with('subject')
            ->whereHas('subject', function ($query) {
                $query->where('instructor_id', auth()->id());
            })->get();

        return view('instructor.attendance.index', compact('attendances'));
    }

    public function edit(Attendance $attendance)
    {
        return view('admin.attendance.edit', ['attendance' => $attendance]);
    }
}
