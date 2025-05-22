<?php

namespace App\Http\Controllers;

use App\Models\Instructor;

class AdminController extends Controller
{
    public function index()
    {
        $metrics = $this->getDashboardMetrics();
        return view('admin.index', $metrics);
    }

    private function getDashboardMetrics()
    {
        return [
            'totalInstructors' => Instructor::count()
        ];
    }
}
