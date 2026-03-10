<?php
// database/migrations/xxxx_xx_xx_000004_create_bank_branch_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBankBranchTable extends Migration
{
    public function up()
    {
        Schema::create('bank_branch', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('bank_id');
            $table->string('branch_name');
            $table->string('branch_code');
            $table->timestamps();

            
            $table->foreign('bank_id')->references('id')->on('banks');
        });
    }

    public function down()
    {
        Schema::dropIfExists('bank_branch');
    }
}