<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function index()
    {
        return view('auth.login');
    }

    public function adminLogin()
    {
        return view('auth.admin-login');
    }

    public function storeAdmin()
    {
        User::create([
            'name' => 'bgboi',
            'email' => 'admin@evsu.edu.ph',
            'password' => Hash::make('admin'),
            'role' => 'admin'
        ]);
    }

    public function authenticate(Request $request)
    {
        try {
            $data = $request->validate([
                "email" => "required|email",
                "password" => "required",
                "role" => "sometimes|in:student,instructor,admin"
            ]);

            // If role is not provided, assume it's an admin login
            if (!isset($data['role'])) {
                $data['role'] = 'admin';
            }

            if (Auth::attempt($data)) {
                $request->session()->regenerate();

                if (Auth::user()->role === 'student') {
                    return response()->json([
                        'redirect' => route('student.showDashboard'),
                    ]);
                }

                if (Auth::user()->role === 'instructor') {
                    return response()->json([
                        'redirect' => route('instructor.index'),
                    ]);
                }

                if (Auth::user()->role === 'admin') {
                    return response()->json([
                        'redirect' => route('admin.index'),
                    ]);
                }
            }

            return response()->json([
                'message' => "Invalid email or password",
            ], 401);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => $e->errors(),
            ], 422);
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
