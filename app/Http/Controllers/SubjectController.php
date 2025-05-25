<?php

namespace App\Http\Controllers;

use App\Models\Instructor;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SubjectController extends Controller
{
    public function index()
    {
        $subjects = Subject::where('instructor_id', auth()->id())->get();
        return view('instructor.subjects.index', ['subjects' => $subjects]);
    }

    public function store(Request $request)
    {
        $subject = null;
        try {
            $data = $request->validate([
                'subject_code' => 'required|string|max:255',
                'subject_name' => 'required|string|max:255',
                'description' => 'nullable|string|max:1000',
                'program' => 'required|string|max:255',
                'year_level' => 'required|integer|min:1|max:4',
                'section' => 'required|in:A,B,C,D,E'
            ]);
            $data['instructor_id'] = auth()->id();

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

        if ($subject) {
            $this->automaticEnrollStudents($subject);
        }

        return response()->json([
            'message' => 'Subject added successfully.',
            'subject' => $subject
        ], 200);
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

    public function update(Request $request, Subject $subject)
    {
        try {
            $data = $request->validate([
                'subject_code' => 'required|string|max:255',
                'subject_name' => 'required|string|max:255',
                'description' => 'nullable|string|max:1000'
            ]);

            $subject->update($data);

            return response()->json([
                'message' => 'Subject updated successfully.',
                'subject' => ['data' => $subject->only(['subject_code', 'subject_name'])],
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while adding the subject.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show(Subject $subject)
    {
        // Get all students that are not yet enrolled in this subject
        $students = Student::whereDoesntHave('subjects', function ($query) use ($subject) {
            $query->where('subjects.id', $subject->id);
        })->get();

        return view('instructor.subjects.show', compact('subject', 'students'));
    }

    public function destroy(Subject $subject)
    {
        $subject->delete();
        return response()->json([
            'message' => 'Subject deleted successfully!'
        ]);
    }
}
