<?php

namespace App\Http\Controllers;

use App\Models\Instructor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SettingsController extends Controller
{
    public function edit()
    {
        $user = auth()->user();
        $defaultPassword = strtolower($user->name) . '-instructor';
        $isDefaultPassword = Hash::check($defaultPassword, $user->password);

        return view('instructor.settings.edit', [
            'isDefaultPassword' => $isDefaultPassword
        ]);
    }
}
