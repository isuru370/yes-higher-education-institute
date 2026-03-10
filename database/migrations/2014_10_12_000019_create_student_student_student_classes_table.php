<?php
// database/migrations/xxxx_xx_xx_000015_create_student_student_student_classes_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentStudentStudentClassesTable extends Migration
{
    public function up()
    {
        Schema::create('student_student_student_classes', function (Blueprint $table) {
            $table->bigIncrements('id');

            // Changed to foreignId with constraints
            $table->foreignId('student_id')
                ->constrained('students');

            $table->foreignId('student_classes_id')
                ->constrained('student_classes');

            $table->unsignedBigInteger('class_category_has_student_class_id');

            $table->foreign(
                'class_category_has_student_class_id',
                'fk_cc_hs_class'
            )->references('id')
                ->on('class_category_has_student_class');


            $table->boolean('status');
            $table->boolean('is_free_card')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('student_student_student_classes');
    }
}
