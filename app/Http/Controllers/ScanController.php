<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Attendance;
use App\Models\Subject;

class ScanController extends Controller
{
    public function index()
    {
        $instructor = auth()->user();
        $subjects = Subject::where('instructor_id', $instructor->id)->get();
        return view('instructor.scans.index', compact('subjects'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|string',
            'subject_id' => 'required|exists:subjects,id'
        ]);

        try {
            $student = Student::where('student_id', $request->student_id)->firstOrFail();

            // Check if student is enrolled in the subject
            $isEnrolled = $student->subjects()->where('subjects.id', $request->subject_id)->exists();

            if (!$isEnrolled) {
                return response()->json([
                    'message' => 'Student is not enrolled in this subject.',
                ], 403);
            }

            // Check if an attendance record already exists for today in this subject
            $attendance = Attendance::where('student_id', $student->id)
                ->where('subject_id', $request->subject_id)
                ->whereDate('date', now()->toDateString())
                ->first();

            if ($attendance) {
                return response()->json([
                    'message' => 'Attendance already recorded for this subject today.',
                    'student' => $student->only(['first_name', 'last_name', 'program', 'year_level']),
                ], 409);
            }

            $attendance = Attendance::create([
                'student_id' => $student->id,
                'subject_id' => $request->subject_id,
                'first_name' => $student->first_name,
                'last_name' => $student->last_name,
                'date' => now()->toDateString(),
                'time_in' => now()->toTimeString(),
                'status' => 'present',
            ]);

            return response()->json([
                'message' => 'Attendance recorded successfully.',
                'attendance' => $attendance->only(['first_name', 'last_name', 'date', 'time_in', 'status']),
            ], 201);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'No student found.',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while processing the request.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
