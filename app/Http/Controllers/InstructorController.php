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
        // Get overall statistics for all students
        $totalStudents = Student::count();
        $maleCount = Student::where('gender', 'male')->count();
        $femaleCount = Student::where('gender', 'female')->count();

        // Get today's present and absent counts for this instructor's subjects only
        $presentToday = Attendance::whereHas('subject', function ($query) {
            $query->where('instructor_id', auth()->id());
        })
            ->whereDate('attendances.created_at', today())
            ->where('status', 'present')
            ->count();

        $absentToday = Attendance::whereHas('subject', function ($query) {
            $query->where('instructor_id', auth()->id());
        })
            ->whereDate('attendances.created_at', today())
            ->where('status', 'absent')
            ->count();

        // Get subject overview for the logged-in instructor
        $instructor = auth()->user();
        $subjects = Subject::where('instructor_id', $instructor->id)
            ->with(['students'])
            ->get()
            ->map(function ($subject) {
                // Get all student IDs enrolled in this subject
                $studentIds = $subject->students->pluck('id');

                // Get today's attendances for this subject's students and this subject only
                // This ensures we only count attendances for the current subject, not for other subjects the student may be enrolled in
                $todayAttendances = Attendance::whereIn('student_id', $studentIds)
                    ->where('subject_id', $subject->id)
                    ->whereDate('created_at', today())
                    ->get();

                // Count present and absent for today in this subject
                $subject->present_count = $todayAttendances->where('status', 'present')->count();
                $subject->absent_count = $todayAttendances->where('status', 'absent')->count();

                // Total students enrolled in this subject
                $subject->total_students = $subject->students->count();

                // Gender breakdown for this subject
                $subject->male_count = $subject->students->where('gender', 'male')->count();
                $subject->female_count = $subject->students->where('gender', 'female')->count();

                return $subject;
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
        $instructors = Instructor::all();
        // Format department for display
        $instructors->transform(function ($instructor) {
            $instructor->department = $instructor->department === "computer_studies"
                ? "Computer Studies"
                : "Unknown Department";
            return $instructor;
        });

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
        try {
            // Validate the request data and department id from departments table
            $data = $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:instructors,email',
                'department' => 'required|exists:departments,id', // Ensure department exists
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

            Notification::route('mail', $instructor->email)
                ->notify(new InstructorCredentials($credentials));

            return response()->json([
                'message' => 'Instructor added successfully.',
                'instructor' => ['name' => $instructor->only(['first_name', 'last_name']), 'password' => "$instructor->last_name-instructor"],
            ], 200);
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
    }


    /**
     * Show the form to edit an instructor (admin view).
     */
    public function edit(Instructor $instructor)
    {
        return view('admin.instructor.edit', ["instructor" => $instructor]);
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
                'department' => 'required|in:computer_studies',
            ]);

            $instructor->update($data);

            return redirect()
                ->route('admin.instructor.edit', ['instructor' => $instructor])
                ->with('status', 'Instructor details updated successfully!');
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
