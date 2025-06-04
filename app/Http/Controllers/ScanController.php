<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Attendance;
use App\Models\Instructor;
use App\Models\Subject;
use App\Models\SubjectAdvised;

class ScanController extends Controller
{
    public function index()
    {
        $instructor = Instructor::where('email', auth()->user()->email)->first();

        // Get all SubjectAdvised records for this instructor, eager loading the related Subject
        $subjectsAdvised = SubjectAdvised::with('subject')
            ->where('instructor_id', $instructor->id)
            ->get();

        return view('instructor.scans.index', [
            'subjects' => $subjectsAdvised
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
        'student_id' => 'required|string',
        'subject_id' => 'required|exists:subjects,id'
        ]);

        try {
            $student = Student::where('student_id', $request->student_id)->firstOrFail();

            // Get the current instructor
            $instructor = Instructor::where('email', auth()->user()->email)->first();

            // Find the subject_advised record for this subject and any instructor
            $subjectAdvised = SubjectAdvised::where('subject_id', $request->subject_id)
                ->where('instructor_id', $instructor->id)
                ->orderByRaw(
                    '(program_id = ? AND year_level = ? AND section = ?) DESC',
                    [$student->program_id, $student->year_level, $student->section]
                )
                ->first();

            if (!$subjectAdvised) {
                return response()->json([
                    'message' => 'Subject not found.',
                ], 404);
            }

            // Check if the student is enrolled in this subject_advised
            $isEnrolled = $subjectAdvised->students()->where('students.id', $student->id)->exists();

            if (!$isEnrolled) {
                return response()->json([
                    'message' => 'Student is not enrolled in this subject.',
                ], 403);
            }

            // Check if an attendance record already exists for today in this subject_advised
            $attendance = Attendance::where('student_id', $student->id)
                ->where('subject_advised_id', $subjectAdvised->id)
                ->whereDate('date', now()->toDateString())
                ->first();

            if ($attendance) {
                $subjectCode = $subjectAdvised->subject->subject_code ?? '';
                return response()->json([
                    'message' => "Attendance already recorded for the subject $subjectCode today.",
                    'student' => $student->only(['first_name', 'last_name', 'program_id', 'year_level']),
                ], 409);
            }

            // Record attendance
            $attendance = Attendance::create([
                'student_id' => $student->id,
                'subject_advised_id' => $subjectAdvised->id,
                'instructor_id' => $instructor->id,
                'first_name' => $student->first_name,
                'last_name' => $student->last_name,
                'date' => now()->toDateString(),
                'time_in' => now()->toTimeString(),
                'status' => 'present',
            ]);

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

        return response()->json([
            'message' => 'Attendance recorded successfully.',
            'attendance' => [
                'first_name' => $attendance->first_name,
                'last_name' => $attendance->last_name,
                'date' => $attendance->date,
                'time_in' => $attendance->time_in,
                'status' => $attendance->status,
                'subject_code' => $subjectAdvised->subject->subject_code ?? '',
            ],
        ], 201);
    }

    public function thirdPartyIndex(Request $request)
    {
        // from q GET argument
        $qrCode = $request->query('q');
        if (!$qrCode) {
            return $this->index();
        }

        $instructor = Instructor::where('email', auth()->user()->email)->first();
        $subjectsAdvised = Subject::whereHas('instructors', function ($query) use ($instructor) {
            $query->where('instructor_id', $instructor->id);
        })->get();
        
        $studentID = $qrCode;
        $student = Student::where('student_id', $studentID)->first();
        if (!$student) {
            return redirect()->route('instructor.scan.index')->withErrors(['message' => 'No student found with this ID.']);
        }

        $enrolledSubjects = $student->subjects()->with('subject')->get();

        // get the subject_id of the last attendance taken by the instructor
        $lastAttendance = Attendance::where('instructor_id', $instructor->id)
            ->whereHas('subjectAdvisedbelongs', function ($query) use ($instructor) {
                $query->where('instructor_id', $instructor->id);
            })
            ->latest()
            ->first();

        $subjectID = null;
        if ($lastAttendance && $lastAttendance->subjectAdvisedbelongs) {
            $subjectID = $lastAttendance->subjectAdvisedbelongs->subject_id;
        }    

        if ($subjectID) {
            $enrolledSubjects = $enrolledSubjects->sortBy(function ($subject) use ($subjectID) {
                return $subject->subject_id === $subjectID ? 0 : 1;
            });
        }

        // if ($enrolledSubjects->isEmpty()) {
        //     return redirect()->route('instructor.scan.index')->withErrors(['message' => 'Student is not enrolled in any of your subjects.']);
        // }

        return view('instructor.scans.other', [
            'qrCode' => $qrCode,
            'subjects' => $enrolledSubjects,
            'student' => $student,
        ]);
    }
    
}
