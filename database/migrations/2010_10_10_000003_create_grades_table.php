<?php
// database/migrations/xxxx_xx_xx_000011_create_grades_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGradesTable extends Migration
{
    public function up()
    {
        Schema::create('grades', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('grade_name');
            $table->timestamps();
        });
    }

    
    public function down()
    {
        Schema::dropIfExists('grades');
    }
}