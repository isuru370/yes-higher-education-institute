<?php
// database/migrations/xxxx_xx_xx_000018_create_student_attendances_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentAttendancesTable extends Migration
{
    public function up()
    {
        Schema::create('student_attendances', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->dateTime('at_date');
            $table->foreignId('student_student_student_classes_id')
                ->constrained('student_student_student_classes');
            $table->foreignId('student_id')
                ->constrained('students');
            $table->foreignId('attendance_id')->constrained('class_attendances');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('student_attendances');
    }
}
