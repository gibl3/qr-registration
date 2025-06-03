<?php

namespace App\Http\Controllers;

use App\Models\Program;
use App\Http\Requests\StoreProgramRequest;
use App\Http\Requests\UpdateProgramRequest;
use App\Models\Department;
use Illuminate\Http\Request;

class ProgramController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //include the department names in the program listing
        $programs = Program::with('department')->get();
        $departments = Department::all();

        return view('admin.program.index', compact('programs', 'departments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:programs,name',
            'department_id' => 'required|exists:departments,id',
            'abbreviation' => 'required|string|max:10|unique:programs,abbreviation',
        ]);

        Program::create($validatedData);

        return redirect()->route('admin.program.index')->with('success', 'Program created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Program $program)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Program $program)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Program $program)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:programs,name,' . $program->id,
            'department_id' => 'required|exists:departments,id',
            'abbreviation' => 'required|string|max:10|unique:programs,abbreviation,' . $program->id,
        ]);

        $program->update($validatedData);

        return redirect()->route('admin.program.index')->with('success', 'Program updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Program $program)
    {
        $program->delete();

        return redirect()->route('admin.program.index')->with('success', 'Program deleted successfully.');
    }
}
