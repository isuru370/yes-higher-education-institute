<?php
// database/migrations/xxxx_xx_xx_000010_create_class_attendances_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClassAttendancesTable extends Migration
{
    public function up()
    {
        Schema::create('class_attendances', function (Blueprint $table) {
            $table->id();

            $table->string('start');
            $table->string('end');

            $table->integer('status');

            $table->foreignId('class_category_has_student_class_id')
                ->constrained('class_category_has_student_class');

            $table->string('start_time');
            $table->string('end_time');
            $table->string('day_of_week');

            $table->boolean('is_ongoing')->default(false);

            $table->foreignId('class_hall_id')
                ->constrained('class_halls');

            $table->date('date');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('class_attendances');
    }
}
