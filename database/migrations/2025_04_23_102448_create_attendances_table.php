<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade'); // Foreign key to students table
            $table->foreignId('subject_id')->constrained()->onDelete('cascade');
            $table->string('first_name')->nullable(); // Add first_name column
            $table->string('last_name')->nullable();  // Add last_name column
            $table->date('date'); // Date of attendance
            $table->time('time_in')->nullable(); // Time the student checked in
            $table->time('time_out')->nullable(); // Time the student checked out
            $table->string('status')->default('present'); // Status (e.g., present, absent, late)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
