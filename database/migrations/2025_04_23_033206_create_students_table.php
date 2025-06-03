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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('student_id');
            $table->string('email_address')->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->foreignId('program_id')
                ->constrained('programs')
                ->onDelete('cascade');
            $table->integer('year_level');
            $table->enum('section', ['A', 'B', 'C', 'D', 'E'])->default('A');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
