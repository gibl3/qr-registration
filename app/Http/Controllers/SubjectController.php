<?php

namespace App\Http\Controllers;

use App\Models\Instructor;
use App\Models\Program;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SubjectController extends Controller
{
    public function index()
    {
        $subjects = Subject::with('program')->get();
        $programs = Program::all();
        return view('admin.subject.index', compact('subjects', 'programs'));
    }

    public function instructorIndex() {
        $subjects = Subject::where('instructor_id', auth()->id())->get();
        $instructor = Instructor::find(auth()->id());
        return view('instructor.subjects.index', ['subjects' => $subjects, 'instructor' => $instructor]);
    }

    public function store(Request $request)
    {
        $subject = null;
        try {
            $data = $request->validate([
                'code' => 'required|string|max:255',
                'name' => 'required|string|max:255',
                'program_id' => 'required|exists:programs,id',
                'year_level' => 'required|integer|min:1|max:4',
            ]);
            $data['admin_id'] = auth()->id();

            $subject = Subject::create($data);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while adding the subject.',
                'error' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => 'Subject added successfully.',
            'subject' => $subject
        ], 200);
    }

    public function update(Request $request, Subject $subject)
    {
        try {
            $data = $request->validate([
                'code' => 'required|string|max:255',
                'name' => 'required|string|max:255',
                'program_id' => 'required|exists:programs,id',
                'year_level' => 'required|integer|min:1|max:4',
            ]);

            // $didProgramYearSectionChanged = $subject->program !== $request->input('program') ||
            //     $subject->year_level !== $request->input('year_level') ||
            //     $subject->section !== $request->input('section');

            $subject->update($data);

            // if ($didProgramYearSectionChanged) {
            //     $this->reenrollStudents($subject);
            // }
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while adding the subject.',
                'error' => $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'message' => 'Subject updated successfully.',
            'subject' => ['data' => $subject->only(['subject_code', 'subject_name'])],
        ], 200);
    }

    public function destroy(Subject $subject)
    {
        $subject->delete();
        
        return $this->index();
    }

    private function automaticEnrollStudents(Subject $subject)
    {
        // Find students matching the subject's program, year_level, and section
        $students = Student::where('program', $subject->program)
            ->where('year_level', $subject->year_level)
            ->where('section', $subject->section)
            ->get();

        // Attach each student to the subject (assuming many-to-many relationship)
        foreach ($students as $student) {
            $student->subjects()->syncWithoutDetaching([$subject->id]);
        }
    }

    private function reenrollStudents(Subject $subject)
    {
        // delete existing enrollments for this subject
        DB::table('subject_student')
            ->where('subject_id', $subject->id)
            ->delete();

        $this->automaticEnrollStudents($subject);
    }

    public function show(Subject $subject)
    {
        // Get all students that are not yet enrolled in this subject
        $students = Student::whereDoesntHave('subjects', function ($query) use ($subject) {
            $query->where('subjects.id', $subject->id);
        })->get();

        return view('instructor.subjects.show', compact('subject', 'students'));
    }

    public function getStudents(Subject $subject)
    {
        $date = request()->query('date', now()->toDateString());

        $students = $subject->students()
            ->whereDoesntHave('attendances', function ($query) use ($subject, $date) {
                $query->where('subject_id', $subject->id)
                    ->whereDate('date', $date);
            })
            ->select('students.id', 'students.first_name', 'students.last_name', 'students.student_id')
            ->get();

        return response()->json(['students' => $students]);
    }
}
