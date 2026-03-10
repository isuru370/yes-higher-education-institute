<?php
// database/migrations/xxxx_xx_xx_000013_create_teachers_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTeachersTable extends Migration
{
    public function up()
    {
        Schema::create('teachers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('custom_id');
            $table->string('fname');
            $table->string('lname');
            $table->string('email');
            $table->string('mobile');
            $table->string('nic');
            $table->date('bday');
            $table->string('gender');
            $table->string('address1');
            $table->string('address2');
            $table->string('address3')->nullable();
            $table->boolean('is_active');
            $table->longText('graduation_details')->nullable();
            $table->longText('experience')->nullable();
            $table->string('account_number')->nullable();
            $table->bigInteger('bank_branch_id')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('teachers');
    }
}