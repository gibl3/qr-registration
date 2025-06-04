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
        'department_id',        
    ];

    public function instructors()
    {
        return $this->belongsToMany(Instructor::class, "subject_advised");;
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
    
    public function getProgramAttribute()
    {
        return \App\Models\Program::find($this->pivot->program_id ?? null);
    }
}
