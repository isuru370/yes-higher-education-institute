<?php
// database/migrations/xxxx_xx_xx_000007_create_class_category_has_student_class_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClassCategoryHasStudentClassTable extends Migration
{
    public function up()
    {
        Schema::create('class_category_has_student_class', function (Blueprint $table) {
            $table->id();

            $table->decimal('fees', 10, 2);

            $table->foreignId('student_classes_id')
                ->constrained('student_classes');

            $table->foreignId('class_category_id')
                ->constrained('class_categories');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('class_category_has_student_class');
    }
}
