<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexesToStudentsPaymentsAndAttendancesTables extends Migration
{
    public function up()
    {
        Schema::table('students', function (Blueprint $table) {
            $table->index('mobile', 'students_mobile_idx'); // කෙටි නම්
            $table->index('guardian_mobile', 'students_guardian_mobile_idx');
            $table->index('grade_id', 'students_grade_id_idx');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->index('payment_date', 'payments_date_idx');
            
            // කෙටි index names
            $table->index(['student_id', 'payment_date'], 'payments_student_date_idx');
            $table->index(['student_student_student_classes_id', 'payment_date'], 'payments_class_date_idx');
        });

        Schema::table('student_attendances', function (Blueprint $table) {
            // කෙටි index names
            $table->index(['student_id', 'at_date'], 'attendance_student_date_idx');
            $table->index(['student_student_student_classes_id', 'at_date'], 'attendance_class_date_idx');

            // unique constraint එකට කෙටි නමක්
            $table->unique(['student_id', 'attendance_id'], 'attendance_student_unique');
        });
    }

    public function down()
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropIndex('students_mobile_idx');
            $table->dropIndex('students_guardian_mobile_idx');
            $table->dropIndex('students_grade_id_idx');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex('payments_date_idx');
            $table->dropIndex('payments_student_date_idx');
            $table->dropIndex('payments_class_date_idx');
        });

        Schema::table('student_attendances', function (Blueprint $table) {
            $table->dropIndex('attendance_student_date_idx');
            $table->dropIndex('attendance_class_date_idx');
            $table->dropUnique('attendance_student_unique');
        });
    }
}