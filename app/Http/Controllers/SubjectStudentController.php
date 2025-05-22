<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SubjectStudentController extends Controller
{
    public function enroll(Request $request, Subject $subject)
    {
        try {
            $request->validate([
                'student_ids' => 'required|string',
                'student_ids.*' => 'exists:students,id',
            ]);

            // Parse the JSON string back to array
            $studentIds = json_decode($request->student_ids, true);

            if (empty($studentIds)) {
                return redirect()->back()->with('error', 'No students selected for enrollment.');
            }

            DB::beginTransaction();

            try {
                // Use syncWithoutDetaching to add new students without removing existing ones
                $subject->students()->syncWithoutDetaching($studentIds);

                DB::commit();

                return redirect()->back()->with('success', 'Selected students enrolled successfully!');
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Enrollment failed: ' . $e->getMessage());
                return redirect()->back()->with('error', 'Failed to enroll students. Please try again.');
            }
        } catch (\Exception $e) {
            Log::error('Enrollment validation failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Invalid student data. Please try again.');
        }
    }

    public function unenroll(Request $request, Subject $subject)
    {
        try {
            $request->validate([
                'student_ids' => 'required|string',
                'student_ids.*' => 'exists:students,id',
            ]);

            // Parse the JSON string back to array
            $studentIds = json_decode($request->student_ids, true);

            if (empty($studentIds)) {
                return redirect()->back()->with('error', 'No students selected for unenrollment.');
            }

            DB::beginTransaction();

            try {
                // Detach the students from the subject
                $subject->students()->detach($studentIds);

                DB::commit();

                return redirect()->back()->with('success', 'Selected students unenrolled successfully!');
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Unenrollment failed: ' . $e->getMessage());
                return redirect()->back()->with('error', 'Failed to unenroll students. Please try again.');
            }
        } catch (\Exception $e) {
            Log::error('Unenrollment validation failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Invalid student data. Please try again.');
        }
    }
}
