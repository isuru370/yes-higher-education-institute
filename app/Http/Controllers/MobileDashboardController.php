<?php

namespace App\Http\Controllers;

use App\Models\ClassAttendance;
use App\Models\Payments;
use App\Models\Student;
use Illuminate\Http\Request;

class MobileDashboardController extends Controller
{
    public function index()
    {
        // Daily payment collection
        $dailyCollection = Payments::whereBetween('created_at', [
            now()->startOfDay(),
            now()->endOfDay()
        ])
            ->sum('amount');

        // Today class schedule
        $todayClasses = ClassAttendance::with([
            'classCategoryStudentClass.classCategory',
            'classCategoryStudentClass.studentClass.subject',
            'classCategoryStudentClass.studentClass.grade',
            'hall'
        ])
            ->where('date', now()->toDateString())
            ->get()
            ->map(function ($attendance) {

                $studentClass = optional($attendance->classCategoryStudentClass)->studentClass;

                return [
                    'attendance_id' => $attendance->id,
                    'status' => $attendance->status ?? null,

                    'class_category_has_student_class' => [
                        'id' => optional($attendance->classCategoryStudentClass)->id,
                        'fees' => optional($attendance->classCategoryStudentClass)->fees,
                    ],

                    'student_class' => [
                        'id' => optional($studentClass)->id,
                        'class_name' => optional($studentClass)->class_name,
                    ],

                    // ✅ FIXED HERE
                    'subject' => [
                        'subject_name' => optional(optional($studentClass)->subject)->subject_name,
                    ],

                    'grade' => [
                        'grade_name' => optional(optional($studentClass)->grade)->grade_name,
                    ],

                    'category' => [
                        'category_name' => optional($attendance->classCategoryStudentClass->classCategory)->category_name,
                    ],

                    'start_time' => $attendance->start_time,
                    'end_time' => $attendance->end_time,

                    'is_ongoing' => now()->between(
                        \Carbon\Carbon::parse($attendance->start_time),
                        \Carbon\Carbon::parse($attendance->end_time)
                    ),

                    'class_hall' => [
                        'hall_name' => optional($attendance->hall)->hall_name,
                        'hall_type' => optional($attendance->hall)->hall_type,
                    ],

                    'date' => $attendance->date,
                ];
            });

        // Today registered students count
        $todayStudents = Student::whereBetween('created_at', [
            now()->startOfDay(),
            now()->endOfDay()
        ])
            ->select(
                'id',
                'custom_id',
                'full_name',
                'initial_name',
                'img_url',
                'class_type',
                'admission'
            )
            ->get();

        $todayStudentsCount = $todayStudents->count();


        return response()->json([
            'status' => true,
            'data' => [
                'daily_collection' => $dailyCollection,
                'today_classes' => $todayClasses,
                'today_registered_students_count' => $todayStudentsCount,
                'today_registered_students' => $todayStudents,
            ]
        ]);
    }
}
