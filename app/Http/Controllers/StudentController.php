<?php

namespace App\Http\Controllers;

use App\Models\Program;
use Illuminate\Http\Request;
use App\Models\Student;

class StudentController extends Controller
{
    public function showDashboard()
    {
        return view('student.index');
    }

    public function index()
    {
        $students = Student::all();

        return view('instructor.students.index', ['students' => $students]);
    }

    public function edit(Student $student)
    {
        $programs = Program::all();

        return view('instructor.students.edit', ['student' => $student, 'programs' => $programs]);
    }

    public function update(Student $student, Request $request)
    {
        $data = $request->validate([
            'student_id' => 'required|string|max:20|unique:students,student_id,' . $student->id,
            'email_address' => 'required|email|max:255|unique:students,email_address,' . $student->id,
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'program_id' => 'required|exists:programs,id',
            'year_level' => 'required|integer|min:1|max:4',
            'section' => 'required|in:A,B,C,D,E',
            'gender' => 'required|in:male,female',
        ]);

        $student->update($data);

        // Flash a success message to the session
        return redirect()
            ->route('instructor.students.edit', ['student' => $student])
            ->with('success', 'Student details updated successfully!');
    }

    public function destroyMany(Request $request)
    {
        $ids = $request->input('ids');

        if (!$ids || !is_array($ids)) {
            return response()->json([
                'success' => false,
                'message' => 'No records selected for deletion.',
            ], 400);
        }

        Student::whereIn('id', $ids)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Selected attendance records deleted successfully!',
        ]);
    }
}
