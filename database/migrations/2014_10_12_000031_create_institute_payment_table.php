<?php
// database/migrations/xxxx_xx_xx_000021_create_institute_payment_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInstitutePaymentTable extends Migration
{
    public function up()
    {
        Schema::create('institute_payment', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->double('payment');
            $table->dateTime('date');
            $table->string('reason');
            $table->string('reason_code');
            $table->boolean('status');
            $table->foreignId('user_id')
                ->constrained('users');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('institute_payment');
    }
}
