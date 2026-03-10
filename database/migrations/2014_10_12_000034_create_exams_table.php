<?php
// database/migrations/xxxx_xx_xx_000016_create_exam_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExamsTable extends Migration
{
    public function up()
    {
        Schema::create('exams', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title');
            $table->string('date');
            $table->string('start_time')->nullable();
            $table->string('end_time')->nullable();
            $table->foreignId('class_category_has_student_class_id')
                ->constrained('class_category_has_student_class');
            $table->foreignId('class_hall_id')
                ->constrained('class_halls');
            $table->boolean('is_canceled')->default(false);
            $table->timestamps();

            
        });
    }

    public function down()
    {
        Schema::dropIfExists('exam');
    }
}
