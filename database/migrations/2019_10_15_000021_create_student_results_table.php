<?php
// database/migrations/xxxx_xx_xx_000017_create_student_results_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentResultsTable extends Migration
{
    public function up()
    {
        Schema::create('student_results', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('marks');
            $table->string('reason')->nullable();
            $table->boolean('is_updated')->default(false);
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('exam_id')->constrained('exams')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('student_results');
    }
}