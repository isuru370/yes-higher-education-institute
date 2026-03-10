<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentCoursePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('student_course_payments', function (Blueprint $table) {

            $table->id('payment_id');

            // ✅ then create FK
            $table->foreignId('registration_id')
                ->constrained('student_registration') // references 'id'
                ->cascadeOnDelete();

            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('course_id')->constrained('course')->cascadeOnDelete();

            $table->enum('payment_type', ['compulsory', 'monthly', 'full', 'partial', 'late_fee', 'other'])->default('monthly');

            $table->decimal('amount', 10, 2);
            $table->decimal('due_amount', 10, 2)->nullable();
            $table->decimal('paid_amount', 10, 2);

            $table->decimal('balance_before', 10, 2);
            $table->decimal('balance_after', 10, 2);

            $table->date('payment_date');
            $table->date('due_date')->nullable();

            $table->string('month_year', 7)->nullable();
            $table->integer('month_number')->nullable();

            $table->string('payment_method', 50)->default('cash');

            $table->string('transaction_id', 100)->nullable()->unique();
            $table->string('receipt_number', 50)->nullable()->unique();

            $table->text('description')->nullable();
            $table->text('notes')->nullable();

            $table->enum('status', ['pending', 'completed', 'failed', 'refunded'])->default('completed');

            $table->string('collected_by', 100)->nullable();
            $table->string('verified_by', 100)->nullable();
            $table->dateTime('verified_at')->nullable();

            $table->softDeletes();
            $table->timestamps();

            $table->index(['student_id', 'course_id']);
            $table->index(['payment_date', 'status']);
            $table->index('month_year');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('student_course_payments');
    }
}
