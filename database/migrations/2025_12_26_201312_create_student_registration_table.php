<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentRegistrationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('student_registration', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('course_id')->constrained('course')->onDelete('cascade');
            $table->date('registration_date');
            $table->decimal('total_fee', 10, 2)->default(0.00);
            $table->boolean('compulsory_paid')->default(false);
            $table->date('compulsory_paid_date')->nullable();
            $table->decimal('compulsory_amount', 10, 2)->default(0.00);
            $table->decimal('remaining_balance', 10, 2)->default(0.00);
            $table->decimal('monthly_amount', 10, 2)->default(0.00);
            $table->integer('total_months');
            $table->integer('months_paid')->default(0);
            $table->integer('months_remaining')->virtualAs('total_months - months_paid');
            $table->enum('payment_status', ['pending', 'active', 'completed', 'overdue', 'cancelled'])->default('pending');
            $table->enum('registration_status', ['registered', 'in_progress', 'completed', 'dropped'])->default('registered');
            $table->text('notes')->nullable();
            $table->date('course_start_date')->nullable();
            $table->date('course_end_date')->nullable();
            $table->date('next_payment_date')->nullable();
            $table->timestamps();
            
            // Add unique constraint to prevent duplicate registrations
            $table->unique(['student_id', 'course_id'], 'unique_student_course');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('student_registration');
    }
}