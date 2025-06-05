<?php

namespace App\Http\Controllers;

use App\Models\Instructor;
use App\Models\Student;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Department;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use App\Notifications\InstructorCredentials;
use Illuminate\Support\Facades\Notification;


class InstructorController extends Controller
{
    /**
     * Show the instructor dashboard with statistics and subject overview.
     */
    public function index()
    {
        $instructor = Instructor::where('email', auth()->user()->email)->first();

        // Get overall statistics for all students
        $totalStudents = Student::count();
        $maleCount = Student::where('gender', 'male')->count();
        $femaleCount = Student::where('gender', 'female')->count();

        // Get today's present and absent counts for this instructor's subjects only
        $presentToday = Attendance::where('instructor_id', $instructor->id)
            ->whereHas('subjectAdvisedbelongs')
            ->whereDate('attendances.created_at', today())
            ->where('status', 'present')
            ->count();

        $absentToday = Attendance::where('instructor_id', $instructor->id)
            ->whereHas('subjectAdvisedbelongs')
            ->whereDate('attendances.created_at', today())
            ->where('status', 'absent')
            ->count();

        // Get subject overview for the logged-in instructor
        $subjects = \App\Models\SubjectAdvised::where('instructor_id', $instructor->id)
            ->with(['subject', 'students'])
            ->get()
            ->map(function ($subjectAdvised) {
                // Get today's attendances for this subject
                $todayAttendances = Attendance::where('subject_advised_id', $subjectAdvised->id)
                    ->whereDate('created_at', today())
                    ->get();

                // Count present and absent for today
                $presentCount = $todayAttendances->where('status', 'present')->count();
                $absentCount = $todayAttendances->where('status', 'absent')->count();

                // Get enrolled students
                $enrolledStudents = $subjectAdvised->students;
                $totalStudents = $enrolledStudents->count();
                $maleCount = $enrolledStudents->where('gender', 'male')->count();
                $femaleCount = $enrolledStudents->where('gender', 'female')->count();

                return [
                    'id' => $subjectAdvised->id,
                    'code' => $subjectAdvised->subject->code,
                    'name' => $subjectAdvised->subject->name,
                    'present_count' => $presentCount,
                    'absent_count' => $absentCount,
                    'total_students' => $totalStudents,
                    'male_count' => $maleCount,
                    'female_count' => $femaleCount,
                ];
            });
        
        return view('instructor.index', compact(
            'totalStudents',
            'presentToday',
            'absentToday',
            'maleCount',
            'femaleCount',
            'subjects'
        ));
    }


    /**
     * Show the table of all instructors (admin view).
     */
    public function showTable()
    {
        $instructors = Instructor::with('department')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.instructor.index', ["instructors" => $instructors]);
    }


    /**
     * Show the form to create a new instructor (admin view).
     */
    public function create()
    {
        $departments = Department::all();
        
        return view('admin.instructor.create', [
            'departments' => $departments,
        ]);
    }

    /**
     * Store a new instructor and create a corresponding user account.
     * Sends credentials via email notification.
     */
    public function store(Request $request)
    {
        $instructor = null;
        
        try {
            // Validate the request data and department id from departments table
            $data = $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:instructors,email',
                'department_id' => 'required|exists:departments,id', // Ensure department exists
            ]);

            // Create a new Instructor record and a corresponding user account for login
            $instructor = Instructor::create($data);
            User::create([
                'name' => $instructor->last_name,
                'email' => $instructor->email,
                'password' =>  Hash::make("$instructor->last_name-instructor"),
                'role' => 'instructor'
            ]);

            // Send email notification with credentials
            $credentials = [
                'email' => $instructor->email,
                'password' => "$instructor->last_name-instructor"
            ];

            // Notification::route('mail', $instructor->email)
            //     ->notify(new InstructorCredentials($credentials));
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while adding the instructor.',
                'error' => $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'message' => 'Instructor added successfully.',
            'instructor' => [
                'name' => $instructor->only(['first_name', 'last_name']), 
                'password' => "$instructor->last_name-instructor"
            ],
        ], 200);
    }


    /**
     * Show the form to edit an instructor (admin view).
     */
    public function edit(Instructor $instructor)
    {
        $departments = Department::all();
        
        return view('admin.instructor.edit', ["instructor" => $instructor, 'departments' => $departments]);
    }


    /**
     * Update an instructor's details (admin view).
     */
    public function update(Request $request, Instructor $instructor)
    {
        try {
            $data = $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:students,student_id,' . $instructor->id,
                'department_id' => 'required|exists:departments,id', // Ensure department exists
            ]);

            $instructor->update($data);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while adding the instructor.',
                'error' => $e->getMessage(),
            ], 500);
        }

        return $this->showTable();
    }


    /**
     * Update the password for the currently authenticated instructor.
     */
    public function updatePassword(Request $request)
    {
        try {
            $data = $request->validate([
                'current_password' => 'required|string',
                'new_password' => 'required|string|min:8|confirmed',
            ]);

            $user = auth()->user();

            // Check if the current password matches
            if (!Hash::check($data['current_password'], $user->password)) {
                return back()->withErrors(['current_password' => 'Current password is incorrect.']);
            }

            // Update the password
            $user->password = $data['new_password'];
            $user->save();

            return back()->with('status', 'Password updated successfully!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors());
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'An error occurred while updating the password.']);
        }
    }


    /**
     * Delete multiple instructors and their corresponding user accounts.
     */
    public function destroyMany(Request $request)
    {
        $ids = $request->input('ids');

        if (!$ids || !is_array($ids)) {
            return response()->json([
                'success' => false,
                'message' => 'No records selected for deletion.',
            ], 400);
        }

        // Get the emails of instructors to be deleted
        $instructorEmails = Instructor::whereIn('id', $ids)->pluck('email');

        // Delete the instructors
        Instructor::whereIn('id', $ids)->delete();

        // Delete the corresponding user accounts
        User::whereIn('email', $instructorEmails)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Selected instructor records and their user accounts deleted successfully!',
        ]);
    }
}
