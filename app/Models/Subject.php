<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = [
        'admin_id',
        'code',
        'name',
        'program_id',
        'year_level',
    ];

    public function students()
    {
        return $this->belongsToMany(Student::class, "subject_student");
    }

    public function instructors()
    {
        return $this->belongsToMany(Instructor::class, "subject_advised");;
    }

    public function program()
    {
        return $this->belongsTo(Program::class);
    }
}
