<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function index()
    {
        return view('auth.login');
    }

    public function storeAdmin()
    {
        // User::create([
        //     'name' => 'bgboi',
        //     'email' => 'admin@123.com',
        //     'password' => Hash::make('admin'),
        //     'role' => 'admin'
        // ]);
    }

    public function authenticate(Request $request)
    {
        try {
            $data = $request->validate([
                "email" => "required|email",
                "password" => "required",
                "role" => "required|in:admin,instructor"
            ]);

            if (Auth::attempt($data)) {
                $request->session()->regenerate();

                if (Auth::user()->role === 'admin') {
                    return response()->json([
                        'redirect' => route('admin.index'),
                    ]);
                }

                if (Auth::user()->role === 'instructor') {
                    return response()->json([
                        'redirect' => route('instructor.index'),
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
