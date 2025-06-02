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
                $subjectCode = $attendance->subject->subject_code ?? '';
                return response()->json([
                    'message' => "Attendance already recorded for the subject $subjectCode today.",
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
                'attendance' => [
                    'first_name' => $attendance->first_name,
                    'last_name' => $attendance->last_name,
                    'date' => $attendance->date,
                    'time_in' => $attendance->time_in,
                    'status' => $attendance->status,
                    'subject_code' => $attendance->subject->subject_code,
                ],
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

    public function thirdPartyIndex(Request $request)
    {
        // from q GET argument
        $qrCode = $request->query('q');
        $subjectCode = $request->query('s');

        // add space before digits in $subjectCode
        if ($subjectCode) {
            $subjectCode = preg_replace('/(\d+)/', ' $1', $subjectCode);
        }

        if (!$qrCode) {
            return $this->index();
        }

        $instructor = auth()->user();
        $subjects = Subject::where('instructor_id', $instructor->id)->get();
        
        $studentID = $qrCode;
        $student = Student::where('student_id', $studentID)->first();
        if (!$student) {
            return redirect()->route('instructor.scan.index')->withErrors(['message' => 'No student found with this ID.']);
        }

        $enrolledSubjects = $student->subjects()->whereIn('subjects.id', $subjects->pluck('id'))->get();
        if ($subjectCode) {
            $enrolledSubjects = $enrolledSubjects->sortBy(function ($subject) use ($subjectCode) {
                return $subject->subject_code === $subjectCode ? 1 : 0;
            });
        }

        if ($enrolledSubjects->isEmpty()) {
            return redirect()->route('instructor.scan.index')->withErrors(['message' => 'Student is not enrolled in any of your subjects.']);
        }

        return view('instructor.scans.other', [
            'qrCode' => $qrCode,
            'subjects' => $enrolledSubjects,
            'student' => $student,
        ]);
    }
    
}
