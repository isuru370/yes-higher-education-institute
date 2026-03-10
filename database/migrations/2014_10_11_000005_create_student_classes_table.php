<?php
// database/migrations/xxxx_xx_xx_000006_create_student_classes_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\ClassType;

class CreateStudentClassesTable extends Migration
{
    public function up()
    {
        Schema::create('student_classes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('class_name');
            $table->string('class_type')
                ->default(ClassType::OFFLINE);
            $table->string('medium')->default('Sinhala');
            $table->decimal('teacher_percentage', 5, 2);
            $table->boolean('is_active');
            $table->boolean('is_ongoing');
            $table->foreignId('teacher_id')
                ->constrained('teachers');
            $table->foreignId('subject_id')
                ->constrained('subjects');
            $table->foreignId('grade_id')
                ->constrained('grades');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('student_classes');
    }
}
