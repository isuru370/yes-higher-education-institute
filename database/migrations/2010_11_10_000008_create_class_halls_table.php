<?php
// database/migrations/xxxx_xx_xx_000008_create_class_halls_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClassHallsTable extends Migration
{
    public function up()
    {
        Schema::create('class_halls', function (Blueprint $table) {
            $table->id();
            $table->string('hall_id')->unique();
            $table->string('hall_name');
            $table->string('hall_type')->nullable();
            $table->decimal('hall_price', 10, 2)->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('class_halls');
    }
}
