<?php
// database/migrations/xxxx_xx_xx_000019_create_payments_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->dateTime('payment_date');
            $table->boolean('status');
            $table->string('amount');
            $table->string('payment_for');
            $table->foreignId('student_id')
                ->constrained('students');
            $table->foreignId('student_student_student_classes_id')
                ->constrained('student_student_student_classes');
            $table->foreignId('user_id')->constrained('users');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('payments');
    }
}
