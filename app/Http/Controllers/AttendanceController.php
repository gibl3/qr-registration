<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Subject;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{

    public function index()
    {
        $attendances = Attendance::with(['subject', 'student'])
            ->whereHas('subject', function ($query) {
                $query->where('instructor_id', Auth::id());
            })
            ->orderBy('date', 'desc')
            ->orderBy('time_in', 'desc')
            ->get();

        $subjects = Subject::where('instructor_id', Auth::id())->get();
        // if ($attendances->isEmpty()) {
        //     return view('instructor.attendance.index', compact('subjects'));
        // }

        return view('instructor.attendance.index', compact('attendances', 'subjects'));
    }

    public function edit(Attendance $attendance)
    {
        return view('admin.attendance.edit', ['attendance' => $attendance]);
    }

    public function markAbsent(Request $request)
    {
        $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'required|exists:students,id',
            'subject_id' => 'required|exists:subjects,id',
            'date' => 'required|date'
        ]);

        try {
            $subject = Subject::findOrFail($request->subject_id);

            // Verify the subject belongs to the current instructor
            if ($subject->instructor_id !== Auth::id()) {
                return response()->json([
                    'message' => 'You are not authorized to mark attendance for this subject.',
                ], 403);
            }

            $markedStudents = [];
            $errors = [];

            foreach ($request->student_ids as $studentId) {
                // Check if an attendance record already exists for this student, subject, and date
                $existingAttendance = Attendance::where('student_id', $studentId)
                    ->where('subject_id', $request->subject_id)
                    ->whereDate('date', $request->date)
                    ->first();

                if ($existingAttendance) {
                    $errors[] = "Student ID {$studentId} already has an attendance record for this date.";
                    continue;
                }

                $student = Student::findOrFail($studentId);

                $attendance = Attendance::create([
                    'student_id' => $student->id,
                    'subject_id' => $request->subject_id,
                    'first_name' => $student->first_name,
                    'last_name' => $student->last_name,
                    'date' => $request->date,
                    'status' => 'absent',
                ]);

                $markedStudents[] = [
                    'first_name' => $attendance->first_name,
                    'last_name' => $attendance->last_name,
                    'date' => $attendance->date,
                    'status' => $attendance->status,
                    'subject_code' => $subject->subject_code,
                ];
            }

            $response = [
                'message' => count($markedStudents) > 0
                    ? 'Students marked as absent successfully.'
                    : 'No students were marked as absent.',
                'marked_students' => $markedStudents
            ];

            if (!empty($errors)) {
                $response['errors'] = $errors;
            }

            return response()->json($response, count($markedStudents) > 0 ? 201 : 400);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while marking students as absent.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function markAbsentPage()
    {
        $subjects = Subject::where('instructor_id', Auth::id())->get();
        $date = request()->query('date', now()->toDateString());
        $subjectId = request()->query('subject_id');

        $students = collect();
        if ($subjectId) {
            $subject = Subject::findOrFail($subjectId);
            $students = $subject->students()
                ->whereDoesntHave('attendances', function ($query) use ($subject, $date) {
                    $query->where('subject_id', $subject->id)
                        ->whereDate('date', $date);
                })
                ->select('students.id', 'students.first_name', 'students.last_name', 'students.student_id')
                ->get();
        }

        return view('instructor.attendance.mark-absent', compact('subjects', 'students', 'date', 'subjectId'));
    }
}
