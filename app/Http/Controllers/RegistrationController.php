<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class RegistrationController extends Controller
{
    public function create()
    {
        return view('student.registration.create');
    }

    public function show($encryptedStudentId, Request $request)
    {
        $student = Student::where('student_id', Crypt::decryptString($encryptedStudentId))->firstOrFail();

        $qrCodeUrl = $request->query('qrCodeUrl') . "&bgcolor=fafafa";

        return view('student.registration.show', compact('student', 'qrCodeUrl'));
    }

    private function automaticEnrollStudent(Student $student)
    {
        // Automatically enroll the student in all subjects for their program and year level
        $subjects = $student->program->subjects()
            ->where('year_level', $student->year_level)
            ->get();

        foreach ($subjects as $subject) {
            $student->subjects()->attach($subject->id);
        }
    }

    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'program' => 'required|in:BSIT',
                'year_level' => 'required|integer|min:1|max:4',
                'section' => 'required|in:A,B,C,D,E',
                'gender' => 'required|in:male,female',
                'student_id' => 'required|string|max:20|unique:students,student_id',
                'email_address' => 'required|email|max:255|unique:students,email_address',
            ]);

            $student = Student::create($data);

            // Create user account with password pattern: surname-student-id
            $password = strtolower($data['last_name']) . '-' . $data['student_id'];
            $student->createUserAccount($password);

            // Generate the GoQR API URL with the encrypted student_id as the QR code content
            $qrCodeUrl = "https://api.qrserver.com/v1/create-qr-code/?size=400x400&data=" . urlencode($student->student_id);

            // Redirect to the generated QR with the encrypted student id and qr code url
            return response()->json([
                'redirect' => route('registration.show', [
                    'encryptedStudentId' => Crypt::encryptString($student->student_id),
                    'qrCodeUrl' => $qrCodeUrl,
                ]),
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while registering the student.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
