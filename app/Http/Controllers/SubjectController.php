<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Instructor;
use App\Models\Program;
use App\Models\Student;
use App\Models\Subject;
use App\Models\SubjectAdvised;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SubjectController extends Controller
{
    public function index()
    {
        $subjects = Subject::with('department')->get();
        $departments = Department::all();
        return view('admin.subject.index', compact('subjects', 'departments'));
    }

    public function instructorIndex()
    {
        $instructor = Instructor::where('email', auth()->user()->email)->first();
        $subjectsAdvised = $instructor->subjects()->get();
        $programs = Program::where('department_id', $instructor->department_id)->get();

        // For each subject, get the count of enrolled students for this instructor's advised subject
        foreach ($subjectsAdvised as $subject) {
            $subject->enrolled_count = \App\Models\SubjectAdvised::where('subject_id', $subject->id)
                ->where('instructor_id', $instructor->id)
                ->withCount('students')
                ->first()
                ?->students_count ?? 0;
        }

        // get the only the subjects where the subject's department_id matches the instructor's department_id OR the subject's department_id is null
        $subjectsAdvisable = Subject::all()->filter(function ($subject) use ($instructor) {
            return $subject->department_id === $instructor->department_id || is_null($subject->department_id);
        });

        return view('instructor.subjects.index', compact('subjectsAdvised', 'instructor', 'subjectsAdvisable', 'programs'));
    }

    public function store(Request $request)
    {
        $subject = null;
        try {
            $request->validate([
                'code' => 'required|string|max:255',
                'name' => 'required|string|max:255',
            ]);

            $request['admin_id'] = auth()->id();

            $subject = Subject::create($request->only([
                'code', 
                'name', 
                'department_id',
                'admin_id',
            ]));
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

    private function automaticEnrollStudents(SubjectAdvised $subject)
    {        
        // Find students matching the subject's program, year_level, and section
        $students = Student::where('program_id', $subject->program->id)
            ->where('year_level', $subject->year_level)
            ->where('section', $subject->section)
            ->get();

        // Attach each student to the subject (assuming many-to-many relationship)
        foreach ($students as $student) {
            $student->subjects()->syncWithoutDetaching([$subject->id]);
        }
    }

    public function adviseSubject(Request $request)
    {
        $request->validate([
            'code' => 'required|exists:subjects,code',
            'program_id' => 'required|exists:programs,id',
            'year_level' => 'required|integer|min:1|max:4',
            'section' => 'required|in:A,B,C,D,E',
        ]);
        
        $instructor = Instructor::where('email', auth()->user()->email)->first();
        if (!$instructor) {
            return response()->json([
                'message' => 'Unauthorized.',
            ], 401);
        }
        
        $subject = Subject::where('code', $request->input('code'))->first();
        if (!$subject) {
            return response()->json([
                'message' => 'Subject not found.',
            ], 404);
        }

        $subjectAdvised = SubjectAdvised::create([
            'subject_id' => $subject->id,
            'instructor_id' => $instructor->id,
            'program_id' => $request->input('program_id'),
            'year_level' => $request->input('year_level'),
            'section' => $request->input('section'),
        ]);

        $this->automaticEnrollStudents($subjectAdvised);

        return response()->json([
            'message' => 'Subject advised successfully.',
            'subject' => $subject,
        ], 200);
    }

    public function updateAdvisedSubject(Request $request)
    {
        $request->validate([
            'code' => 'required|exists:subjects,code',
            'program_id' => 'required|exists:programs,id',
            'year_level' => 'required|integer|min:1|max:4',
            'section' => 'required|in:A,B,C,D,E',
        ]);

        $instructor = Instructor::where('email', auth()->user()->email)->first();
        if (!$instructor) {
            return response()->json([
                'message' => 'Unauthorized.',
            ], 401);
        }

        $subject = SubjectAdvised::where('subject_id', function ($query) use ($request) {
            $query->select('id')
                ->from('subjects')
                ->where('code', $request->input('code'));
        });
        if (!$subject) {
            return response()->json([
                'message' => 'Subject not found.',
            ], 404);
        }

        // update the section in the pivot table
        $instructor->subjects()->updateExistingPivot($subject->id, ['section' => $request->input('section')]);

        return response()->json([
            'message' => 'Advised subject updated successfully.',
            'subject' => $subject,
        ], 200);
    }

    public function destroyAdvisedSubject(Subject $subject)
    {
        $instructor = Instructor::where('email', auth()->user()->email)->first();
        if (!$instructor) {
            return response()->json([
                'message' => 'Unauthorized.',
            ], 401);
        }

        // detach the subject from the instructor
        $instructor->subjects()->detach($subject->id);

        return response()->json([
            'message' => 'Advised subject removed successfully.',
        ], 200);
    }

    public function update(Request $request, Subject $subject)
    {
        try {
            $request->validate([
                'code' => 'required|string|max:255',
                'name' => 'required|string|max:255',
            ]);

            // $didProgramYearSectionChanged = $subject->program !== $request->input('program') ||
            //     $subject->year_level !== $request->input('year_level') ||
            //     $subject->section !== $request->input('section');

            $subject->update($request->only([
                'code', 
                'name', 
                'department_id',
            ]));

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
        
        return redirect()->route('admin.subject.index');
    }

    private function reenrollStudents(Subject $subject)
    {
        // delete existing enrollments for this subject
        DB::table('subject_student')
            ->where('subject_id', $subject->id)
            ->delete();

        // $this->automaticEnrollStudents($subject);
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
