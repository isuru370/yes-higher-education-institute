<?php
// database/migrations/xxxx_xx_xx_000003_create_banks_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBanksTable extends Migration
{
    public function up()
    {
        Schema::create('banks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('bank_name');
            $table->string('bank_code');
            $table->timestamps();
        });
    }

    
    public function down()
    {
        Schema::dropIfExists('banks');
    }
}