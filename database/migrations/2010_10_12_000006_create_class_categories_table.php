<?php
// database/migrations/xxxx_xx_xx_000005_create_class_categories_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClassCategoriesTable extends Migration
{
    public function up()
    {
        Schema::create('class_categories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('category_name');
            $table->timestamps();
        });
    }

    
    public function down()
    {
        Schema::dropIfExists('class_categories');
    }
}