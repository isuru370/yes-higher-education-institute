<?php
// database/migrations/xxxx_xx_xx_000020_create_teacher_payment_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTeacherPaymentTable extends Migration
{
    public function up()
    {
        Schema::create('teacher_payment', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->double('payment');
            $table->dateTime('date');
            $table->string('reason');
            $table->string('reason_code');
            $table->foreign('reason_code')
                ->references('reason_code')
                ->on('payment_reason');
            $table->string('payment_for');
            $table->boolean('status');
            $table->foreignId('user_id')
                ->constrained('users');
            $table->foreignId('teacher_id')
                ->constrained('teachers');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('teacher_payment');
    }
}
