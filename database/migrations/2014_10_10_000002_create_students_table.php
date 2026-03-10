<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentsTable extends Migration
{
    public function up()
    {
        Schema::create('students', function (Blueprint $table) {

            $table->id();

            // Student Identity
            $table->string('custom_id')->unique(); // Printed student number
            $table->string('temporary_qr_code')->nullable()->unique();
            $table->dateTime('temporary_qr_code_expire_date')->nullable();

            $table->string('full_name');
            $table->string('initial_name');

            // Contact
            $table->string('mobile');
            $table->string('whatsapp_mobile')->nullable();
            $table->string('email')->nullable()->index();

            // Personal Information
            $table->string('nic')->nullable()->index();
            $table->date('bday');
            $table->enum('gender', ['male', 'female', 'other'])->nullable();

            // Address
            $table->string('address1');
            $table->string('address2');
            $table->string('address3')->nullable();

            // Guardian Details
            $table->string('guardian_fname');
            $table->string('guardian_lname');
            $table->string('guardian_nic')->nullable();
            $table->string('guardian_mobile');

            // Student Status
            $table->boolean('is_active')->default(true);
            $table->boolean('permanent_qr_active')->default(false);
            $table->boolean('student_disable')->default(false);

            // Other Details
            $table->mediumText('img_url')->nullable();

            $table->foreignId('grade_id')
                ->constrained('grades')
                ->cascadeOnDelete();

            $table->enum('class_type', ['online', 'offline'])->default('offline');

            $table->boolean('admission')->default(false);

            $table->string('student_school')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('students');
    }
}
