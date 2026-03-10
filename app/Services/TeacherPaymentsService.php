<?php

namespace App\Services;

use App\Models\ClassRoom;
use App\Models\Payments;
use App\Models\StudentStudentStudentClass;
use App\Models\Teacher;
use App\Models\TeacherPayment;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;

class TeacherPaymentsService
{
    public function fetchTeacherPaymentsByMonth($yearMonth)
    {
        try {

            $startOfMonth = Carbon::createFromFormat('Y-m', $yearMonth)->startOfMonth();
            $endOfMonth   = Carbon::createFromFormat('Y-m', $yearMonth)->endOfMonth();

            // Get active teachers
            $teachers = Teacher::where('is_active', 1)->get();


            $result = [];

            foreach ($teachers as $teacher) {


                // Get payments for teacher in this month
                $payments = Payments::where('status', 1)
                    ->whereBetween('payment_date', [$startOfMonth, $endOfMonth])
                    ->whereHas('studentStudentClass.studentClass', function ($q) use ($teacher) {
                        $q->where('teacher_id', $teacher->id);
                    })
                    ->with(['studentStudentClass.studentClass'])
                    ->get();


                $classWiseTotals = [];
                $teacherEarning  = 0;
                $totalForMonth   = 0;

                foreach ($payments as $payment) {

                    // Safe relationship handling
                    $class = optional(optional($payment->studentStudentClass)->studentClass);

                    if (!$class || !$class->id) {
                        continue;
                    }

                    $amount     = (float) $payment->amount;
                    $percentage = (float) ($class->teacher_percentage ?? 0);

                    // Safe money calculation
                    $teacherCut     = round(($amount * $percentage) / 100, 2);
                    $institutionCut = round($amount - $teacherCut, 2);


                    $teacherEarning += $teacherCut;
                    $totalForMonth  += $amount;

                    if (!isset($classWiseTotals[$class->id])) {
                        $classWiseTotals[$class->id] = [
                            'class_id'           => $class->id,
                            'class_name'         => $class->class_name,
                            'teacher_percentage' => $percentage,
                            'total_amount'       => 0,
                            'teacher_earning'    => 0,
                            'institution_cut'    => 0,
                        ];
                    }

                    $classWiseTotals[$class->id]['total_amount']    += $amount;
                    $classWiseTotals[$class->id]['teacher_earning'] += $teacherCut;
                    $classWiseTotals[$class->id]['institution_cut'] += $institutionCut;
                }

                $classWiseTotals = array_values($classWiseTotals);

                // Get teacher payments already paid
                $teacherPaidList = TeacherPayment::with('reasonDetail')
                    ->where('teacher_id', $teacher->id)
                    ->where('status', 1)
                    ->whereBetween('date', [$startOfMonth, $endOfMonth])
                    ->get();

                $alreadyPaid = (float) $teacherPaidList->sum('payment');

                $paidDetails = $teacherPaidList->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'date' => $item->date,
                        'payment' => (float) $item->payment,
                        'reason_detail' => [
                            'id' => $item->reasonDetail->id ?? null,
                            'reason_code' => $item->reasonDetail->reason_code ?? null,
                            'reason' => $item->reasonDetail->reason ?? null,
                        ]
                    ];
                });

                // Final calculations
                $teacherEarning     = round($teacherEarning, 2);
                $totalForMonth      = round($totalForMonth, 2);
                $institutionIncome  = round($totalForMonth - $teacherEarning, 2);
                $finalPayable       = round(max($teacherEarning - $alreadyPaid, 0), 2);


                $result[] = [
                    'teacher_id'                => $teacher->id,
                    'teacher_name'              => $teacher->fname . " " . $teacher->lname,
                    'total_payments_this_month' => $totalForMonth,
                    'teacher_earning'           => $teacherEarning,
                    'institution_income'        => $institutionIncome,
                    'already_paid'              => $alreadyPaid,
                    'final_payable'             => $finalPayable,
                    'class_wise_totals'         => $classWiseTotals,
                    'teacher_paid_details'      => $paidDetails,
                ];
            }


            return response()->json([
                'status' => 'success',
                'year_month' => $yearMonth,
                'data' => $result
            ]);
        } catch (Exception $e) {

            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong while calculating teacher payments.'
            ], 500);
        }
    }



    public function fetchTeacherPaymentsCurrentMonth()
    {
        try {

            $now = Carbon::now();
            $currentYearMonth = $now->format('Y-m');
            $startOfMonth = $now->copy()->startOfMonth();
            $endOfMonth   = $now->copy()->endOfMonth();

            /* ---------------- TEACHERS ---------------- */

            $teachers = Teacher::where('is_active', 1)->get()->keyBy('id');
            $teacherIds = $teachers->keys();

            /* ---------------- PAYMENTS ---------------- */

            $payments = Payments::where('status', 1)
                ->whereBetween('payment_date', [$startOfMonth, $endOfMonth])
                ->whereHas('studentStudentClass.studentClass', function ($q) use ($teacherIds) {
                    $q->whereIn('teacher_id', $teacherIds)
                        ->where('is_active', 1);
                })
                ->with([
                    'studentStudentClass.studentClass:id,teacher_id,class_name,teacher_percentage'
                ])
                ->get();

            /*
        Group payments by teacher_id
        This removes expensive filtering per teacher
        */

            $paymentsByTeacher = [];

            foreach ($payments as $p) {

                $class = optional(optional($p->studentStudentClass)->studentClass);

                if (!$class || !$class->teacher_id) {
                    continue;
                }

                $paymentsByTeacher[$class->teacher_id][] = $p;
            }

            /* ---------------- ADVANCE PAYMENTS ---------------- */

            $currentMonthYear = $now->format('m Y');

            $advancePayments = TeacherPayment::selectRaw('teacher_id, SUM(payment) as advance_total')
                ->whereIn('teacher_id', $teacherIds)
                ->where('status', 1)
                ->where('payment_for', $currentMonthYear)
                ->groupBy('teacher_id')
                ->get()
                ->keyBy('teacher_id');

            /* ---------------- RESULT ---------------- */

            $result = [];

            foreach ($teachers as $teacher) {

                $teacherPayments = $paymentsByTeacher[$teacher->id] ?? [];

                $totalForMonth = 0.0;
                $grossTeacherEarning = 0.0;
                $classWise = [];

                foreach ($teacherPayments as $p) {

                    $class = $p->studentStudentClass->studentClass;

                    $amount = (float) $p->amount;
                    $percentage = (float) ($class->teacher_percentage ?? 0);

                    $teacherCut = round(($amount * $percentage) / 100, 2);
                    $institutionCut = round($amount - $teacherCut, 2);

                    $totalForMonth += $amount;
                    $grossTeacherEarning += $teacherCut;

                    if (!isset($classWise[$class->id])) {

                        $classWise[$class->id] = [
                            'class_id'           => $class->id,
                            'class_name'         => $class->class_name,
                            'teacher_percentage' => $percentage,
                            'total_amount'       => 0.0,
                            'teacher_cut'        => 0.0,
                            'institution_cut'    => 0.0,
                        ];
                    }

                    $classWise[$class->id]['total_amount'] += $amount;
                    $classWise[$class->id]['teacher_cut'] += $teacherCut;
                    $classWise[$class->id]['institution_cut'] += $institutionCut;
                }

                $advanceDeducted = (float) ($advancePayments[$teacher->id]->advance_total ?? 0);

                $netPayable = round(max($grossTeacherEarning - $advanceDeducted, 0), 2);

                $result[] = [
                    'teacher_id' => $teacher->id,
                    'teacher_name' => $teacher->fname . ' ' . $teacher->lname,
                    'total_payments_this_month' => round($totalForMonth, 2),
                    'gross_teacher_earning' => round($grossTeacherEarning, 2),
                    'advance_deducted_this_month' => round($advanceDeducted, 2),
                    'net_teacher_payable' => $netPayable,
                    'institution_income' => round($totalForMonth - $grossTeacherEarning, 2),
                    'class_wise_breakdown' => array_values($classWise),
                ];
            }

            return response()->json([
                'status' => 'success',
                'year_month' => $currentYearMonth,
                'data' => $result
            ]);
        } catch (Exception $e) {

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to calculate teacher payments.'
            ], 500);
        }
    }


    public function fetchTeacherPaymentsByTeacher($teacherId, $yearMonth)
    {
        try {

            $startOfMonth = Carbon::createFromFormat('Y-m', $yearMonth)->startOfMonth();
            $endOfMonth   = Carbon::createFromFormat('Y-m', $yearMonth)->endOfMonth();
            $monthYear    = $startOfMonth->format('m Y');

            /* ---------------- TEACHER CLASSES ---------------- */

            $classes = ClassRoom::with([
                'subject:id,subject_name',
                'teacher:id,fname,lname',
                'grade:id,grade_name'
            ])
                ->where('is_active', 1)
                ->where('teacher_id', $teacherId)
                ->select('id', 'class_name', 'grade_id', 'subject_id', 'teacher_id', 'teacher_percentage')
                ->get();

            if ($classes->isEmpty()) {
                return response()->json([
                    'status' => 'success',
                    'teacher_id' => $teacherId,
                    'teacher_name' => null,
                    'total_payments_this_month' => 0,
                    'total_teacher_share' => 0,
                    'total_institution_share' => 0,
                    'classes' => []
                ]);
            }

            $teacherName = optional($classes->first()->teacher)->fname . ' ' .
                optional($classes->first()->teacher)->lname;

            $classIds = $classes->pluck('id');

            /* ---------------- PAYMENTS GROUPED ---------------- */

            $payments = Payments::selectRaw("
                student_student_student_classes.student_classes_id AS class_id,
                DATE(payments.payment_date) AS pay_date,
                SUM(payments.amount) AS total_amount
            ")
                ->join(
                    'student_student_student_classes',
                    'payments.student_student_student_classes_id',
                    '=',
                    'student_student_student_classes.id'
                )
                ->whereIn('student_student_student_classes.student_classes_id', $classIds)
                ->where('payments.status', 1)
                ->whereBetween('payments.payment_date', [$startOfMonth, $endOfMonth])
                ->groupBy('student_student_student_classes.student_classes_id', 'pay_date')
                ->get()
                ->groupBy('class_id');

            /* ---------------- STUDENT COUNTS (ONE QUERY) ---------------- */

            $studentCounts = StudentStudentStudentClass::selectRaw("
                student_classes_id,
                COUNT(*) as total_students,
                SUM(is_free_card = 1) as free_students
            ")
                ->whereIn('student_classes_id', $classIds)
                ->groupBy('student_classes_id')
                ->get()
                ->keyBy('student_classes_id');

            /* ---------------- PAID STUDENTS ---------------- */

            $paidStudents = Payments::selectRaw("
                student_student_student_classes.student_classes_id as class_id,
                COUNT(DISTINCT payments.student_student_student_classes_id) as paid_count
            ")
                ->join(
                    'student_student_student_classes',
                    'payments.student_student_student_classes_id',
                    '=',
                    'student_student_student_classes.id'
                )
                ->whereIn('student_student_student_classes.student_classes_id', $classIds)
                ->where('payments.status', 1)
                ->whereBetween('payments.payment_date', [$startOfMonth, $endOfMonth])
                ->groupBy('student_student_student_classes.student_classes_id')
                ->get()
                ->keyBy('class_id');

            /* ---------------- TEACHER PAYMENTS ---------------- */

            $teacherPayments = TeacherPayment::with('user:id,name')
                ->where('teacher_id', $teacherId)
                ->where('status', 1)
                ->where('payment_for', $monthYear)
                ->get();

            $salaryPayment = $teacherPayments
                ->where('reason_code', 'salary')
                ->sum('payment');

            $advanceRecords = $teacherPayments
                ->where('reason_code', '!=', 'salary')
                ->values()
                ->map(function ($p) {
                    return [
                        'id' => $p->id,
                        'payment' => $p->payment,
                        'date' => $p->date,
                        'reason' => $p->reason,
                        'reason_code' => $p->reason_code,
                        'payment_for' => $p->payment_for,
                        'status' => $p->status,
                        'user_name' => optional($p->user)->name
                    ];
                });

            $advancePayment = $advanceRecords->sum('payment');

            /* ---------------- CALCULATIONS ---------------- */

            $result = [];
            $totalPayments = 0;
            $teacherShare = 0;
            $institutionShare = 0;

            foreach ($classes as $cls) {

                $classPayments = $payments[$cls->id] ?? collect();

                $dailyPayments = [];

                foreach ($classPayments->sortBy('pay_date') as $p) {
                    $dailyPayments[$p->pay_date] = (float) $p->total_amount;
                }

                $classTotalPayments = $classPayments->sum('total_amount');

                $percentage = (float) ($cls->teacher_percentage ?? 0);

                $classTeacherShare = round($classTotalPayments * ($percentage / 100), 2);
                $classInstitutionShare = round($classTotalPayments - $classTeacherShare, 2);

                $studentCount = $studentCounts[$cls->id]->total_students ?? 0;
                $freeStudents = $studentCounts[$cls->id]->free_students ?? 0;
                $paidStudentCount = $paidStudents[$cls->id]->paid_count ?? 0;

                $unpaidStudentCount = max(0, $studentCount - $paidStudentCount - $freeStudents);

                $totalPayments += $classTotalPayments;
                $teacherShare += $classTeacherShare;
                $institutionShare += $classInstitutionShare;

                $result[] = [
                    'class_id' => $cls->id,
                    'class_name' => $cls->class_name,
                    'grade_name' => optional($cls->grade)->grade_name,
                    'subject_name' => optional($cls->subject)->subject_name,
                    'daily_payments' => $dailyPayments,
                    'total_students' => $studentCount,
                    'paid_students' => $paidStudentCount,
                    'unpaid_students' => $unpaidStudentCount,
                    'free_card_students' => $freeStudents,
                    'teacher_percentage' => $percentage,
                    'teacher_share' => $classTeacherShare,
                    'institution_share' => $classInstitutionShare
                ];
            }

            $netPayable = max(0, $teacherShare - ($salaryPayment + $advancePayment));

            return response()->json([
                'status' => 'success',
                'teacher_id' => $teacherId,
                'teacher_name' => $teacherName,
                'is_salary_paid' => $salaryPayment > 0,
                'total_payments_this_month' => round($totalPayments, 2),
                'total_teacher_share' => round($teacherShare, 2),
                'total_institution_share' => round($institutionShare, 2),
                'advance_payment_this_month' => round($advancePayment, 2),
                'advance_payment_records' => $advanceRecords,
                'net_payable' => $netPayable,
                'classes' => $result
            ]);
        } catch (Exception $e) {

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch teacher payments.'
            ], 500);
        }
    }


    public function getTeacherClassWiseStudentPaymentStatus($teacherId, $yearMonth)
    {
        try {

            $startOfMonth = Carbon::createFromFormat('Y-m', $yearMonth)->startOfMonth();
            $endOfMonth   = Carbon::createFromFormat('Y-m', $yearMonth)->endOfMonth();

            /* ---------- CLASSES ---------- */

            $classes = ClassRoom::with(['grade:id,grade_name', 'subject:id,subject_name'])
                ->where('teacher_id', $teacherId)
                ->where('is_active', 1)
                ->get();

            $classIds = $classes->pluck('id');

            /* ---------- STUDENT CLASS RELATIONS ---------- */

            $studentClasses = StudentStudentStudentClass::with('student:id,custom_id,full_name,initial_name,is_active')
                ->whereIn('student_classes_id', $classIds)
                ->where('status', 1)
                ->get();

            $sscIds = $studentClasses->pluck('id');

            /* ---------- PAYMENTS ---------- */

            $payments = Payments::whereIn('student_student_student_classes_id', $sscIds)
                ->where('status', 1)
                ->whereBetween('payment_date', [$startOfMonth, $endOfMonth])
                ->get()
                ->groupBy('student_student_student_classes_id');

            /* ---------- GROUP STUDENTS BY CLASS ---------- */

            $studentsByClass = $studentClasses->groupBy('student_classes_id');

            $totalClasses = $classes->count();

            $grandTotalStudents = 0;
            $grandPaid = 0;
            $grandUnpaid = 0;
            $grandFree = 0;

            $classResults = [];

            foreach ($classes as $class) {

                $studentList = $studentsByClass[$class->id] ?? collect();

                $paid = 0;
                $unpaid = 0;
                $free = 0;

                $studentsData = [];

                foreach ($studentList as $ssc) {

                    if (!$ssc->student || $ssc->student->is_active != 1) continue;

                    $studentPayments = $payments[$ssc->id] ?? collect();

                    $totalPaidAmount = $studentPayments->sum('amount');

                    if ($ssc->is_free_card) {
                        $status = 'Free Card';
                        $free++;
                    } elseif ($totalPaidAmount > 0) {
                        $status = 'Paid';
                        $paid++;
                    } else {
                        $status = 'Unpaid';
                        $unpaid++;
                    }

                    $studentsData[] = [
                        'student_id' => $ssc->student->id,
                        'custom_id' => $ssc->student->custom_id,
                        'name' =>  $ssc->student->initial_name,
                        'status' => $status,
                        'total_paid' => (float) $totalPaidAmount,
                        'payments' => $studentPayments->map(function ($p) {
                            return [
                                'amount' => $p->amount,
                                'date' => $p->payment_date,
                                'payment_for' => $p->payment_for
                            ];
                        })
                    ];
                }

                $totalStudents = $paid + $unpaid + $free;

                $grandTotalStudents += $totalStudents;
                $grandPaid += $paid;
                $grandUnpaid += $unpaid;
                $grandFree += $free;

                $classResults[] = [
                    'class_id' => $class->id,
                    'class_name' => $class->class_name,
                    'grade' => $class->grade->grade_name ?? 'N/A',
                    'subject' => $class->subject->subject_name ?? 'N/A',
                    'total_students' => $totalStudents,
                    'paid_students' => $paid,
                    'unpaid_students' => $unpaid,
                    'free_card_students' => $free,
                    'students' => $studentsData
                ];
            }

            return response()->json([
                'status' => 'success',
                'teacher_id' => $teacherId,
                'year_month' => $yearMonth,
                'summary' => [
                    'total_classes' => $totalClasses,
                    'total_students' => $grandTotalStudents,
                    'paid_students' => $grandPaid,
                    'unpaid_students' => $grandUnpaid,
                    'free_card_students' => $grandFree
                ],
                'classes' => $classResults
            ]);
        } catch (Exception $e) {

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch student payment status.'
            ], 500);
        }
    }



    public function fetchSalarySlipData($teacherId, $yearMonth)
    {
        try {
            // Validate Year-Month
            if (!preg_match('/^\d{4}-\d{2}$/', $yearMonth)) {
                return [
                    "status" => "error",
                    "message" => "Year-Month format must be YYYY-MM"
                ];
            }

            $start = Carbon::createFromFormat('Y-m', $yearMonth)->startOfMonth();
            $end   = Carbon::createFromFormat('Y-m', $yearMonth)->endOfMonth();
            [$year, $month] = explode('-', $yearMonth);

            $teacher = Teacher::find($teacherId);
            if (!$teacher) {
                return [
                    "status" => "error",
                    "message" => "Teacher not found"
                ];
            }

            // Fetch active classes
            $classes = ClassRoom::with(['subject:id,subject_name', 'grade:id,grade_name'])
                ->where('teacher_id', $teacherId)
                ->where('is_active', 1)
                ->select('id', 'teacher_percentage', 'grade_id', 'subject_id')
                ->get();

            if ($classes->isEmpty()) {
                return [
                    "status" => "success",
                    "teacher_id" => $teacher->id,
                    "teacher_name" => trim($teacher->fname . ' ' . $teacher->lname),
                    "month_year" => "$month $year",
                    "month_year_display" => date('F Y', strtotime($yearMonth . '-01')),
                    "date_generated" => now()->format('Y-m-d H:i:s'),
                    "earnings" => [],
                    "total_addition" => 0,
                    "deductions" => [],
                    "total_deductions" => 0,
                    "net_salary" => 0,
                    "payment_method" => "Cash / Bank Deposit"
                ];
            }

            $classIds = $classes->pluck('id');

            // Fetch all payments for all classes at once
            $payments = Payments::join('student_student_student_classes as sssc', 'payments.student_student_student_classes_id', '=', 'sssc.id')
                ->whereIn('sssc.student_classes_id', $classIds)
                ->where('payments.status', 1)
                ->whereBetween('payments.payment_date', [$start, $end])
                ->selectRaw('sssc.student_classes_id as class_id, SUM(payments.amount) as total_amount')
                ->groupBy('sssc.student_classes_id')
                ->get()
                ->keyBy('class_id');

            $earnings = [];
            $totalTeacherEarnings = 0;
            $totalInstitutionShare = 0;

            foreach ($classes as $class) {
                $classTotal = (float) ($payments[$class->id]->total_amount ?? 0);
                $percentage = (float) ($class->teacher_percentage ?? 0);

                $teacherShare = round($classTotal * ($percentage / 100), 2);
                $institutionShare = round($classTotal - $teacherShare, 2);

                $earnings[] = [
                    "class_id" => $class->id,
                    "description" => ($class->grade->grade_name ?? 'N/A') . " - " . ($class->subject->subject_name ?? 'N/A'),
                    "class_total" => $classTotal,
                    "teacher_percentage" => $percentage,
                    "teacher_share" => $teacherShare,
                    "institution_share" => $institutionShare
                ];

                $totalTeacherEarnings += $teacherShare;
                $totalInstitutionShare += $institutionShare;
            }

            // Advance payments (non-salary)
            $advancePayments = TeacherPayment::where('teacher_id', $teacherId)
                ->where('status', 1)
                ->where('reason_code', '!=', 'salary')
                ->where('payment_for', $start->format('m Y'))
                ->sum('payment');

            // Salary paid flag
            $salaryPayment = TeacherPayment::where('teacher_id', $teacherId)
                ->where('reason_code', 'salary')
                ->where('status', 1)
                ->where('payment_for', $start->format('m Y'))
                ->sum('payment');

            $isSalaryPaid = $salaryPayment > 0;

            // Deductions: Advance + institution share
            $deductions = [];
            if ($advancePayments > 0) {
                $deductions[] = ["description" => "Advance Payment", "amount" => round($advancePayments, 2)];
            }
            if ($totalInstitutionShare > 0) {
                $deductions[] = ["description" => "Institution Fees", "amount" => round($totalInstitutionShare, 2)];
            }

            $totalDeductions = $advancePayments + $totalInstitutionShare;

            $netSalary = max(0, round($totalTeacherEarnings - $totalDeductions, 2));

            return [
                "status" => "success",
                "teacher_id" => $teacher->id,
                "teacher_name" => trim($teacher->fname . ' ' . $teacher->lname),
                "month_year" => "$month $year",
                "month_year_display" => date('F Y', strtotime($yearMonth . '-01')),
                "date_generated" => now()->format('Y-m-d H:i:s'),
                "is_salary_paid" => $isSalaryPaid,
                "earnings" => $earnings,
                "total_addition" => round($totalTeacherEarnings, 2),
                "deductions" => $deductions,
                "total_deductions" => round($totalDeductions, 2),
                "net_salary" => $netSalary,
                "payment_method" => "Cash / Bank Deposit"
            ];
        } catch (\Throwable $e) {

            return [
                "status" => "error",
                "message" => "Failed to fetch salary slip data."
            ];
        }
    }


    public function fetchSalarySlipDataTest($teacherId, $yearMonth)
    {
        try {

            // ✅ Validate input
            if (!$teacherId || !preg_match('/^\d{4}-\d{2}$/', $yearMonth)) {
                return response()->json([
                    "status" => "error",
                    "message" => "Invalid input"
                ], 400);
            }

            $start = Carbon::createFromFormat('Y-m', $yearMonth)->startOfMonth();
            $end   = Carbon::createFromFormat('Y-m', $yearMonth)->endOfMonth();

            // ✅ Load teacher
            $teacher = Teacher::find($teacherId);
            if (!$teacher) {
                return response()->json([
                    "status" => "error",
                    "message" => "Teacher not found"
                ], 404);
            }

            // ✅ Load ALL active classes of teacher
            $classes = ClassRoom::with(['subject', 'grade'])
                ->where('teacher_id', $teacherId)
                ->where('is_active', 1)
                ->get();

            if ($classes->isEmpty()) {
                return response()->json([
                    "status" => "success",
                    "teacher_id" => $teacherId,
                    "teacher_name" => $teacher->fname . ' ' . $teacher->lname,
                    "month" => $yearMonth,
                    "earnings" => [],
                    "total_teacher_share" => 0,
                    "total_institution_share" => 0,
                    "advance_payment" => 0,
                    "net_salary" => 0
                ]);
            }

            $classIds = $classes->pluck('id');

            // ✅ Load ALL payments in ONE query (grouped by class)
            $payments = Payments::selectRaw("
                sssc.student_classes_id AS class_id,
                COALESCE(SUM(payments.amount), 0) AS total_amount
            ")
                ->join(
                    'student_student_student_classes as sssc',
                    'payments.student_student_student_classes_id',
                    '=',
                    'sssc.id'
                )
                ->whereIn('sssc.student_classes_id', $classIds)
                ->where('payments.status', 1)
                ->whereBetween('payments.payment_date', [$start, $end])
                ->groupBy('sssc.student_classes_id')
                ->get()
                ->keyBy('class_id');

            // ✅ Build earnings
            $earnings = [];
            $totalTeacherShare = 0;
            $totalInstitutionShare = 0;

            foreach ($classes as $class) {

                // If class has no payments → default to 0
                $classTotal = $payments[$class->id]->total_amount ?? 0;

                $percentage = (float) ($class->teacher_percentage ?? 0);

                $teacherShare = round($classTotal * ($percentage / 100), 2);
                $institutionShare = round($classTotal - $teacherShare, 2);

                $earnings[] = [
                    "class_id" => $class->id,
                    "description" => ($class->grade->grade_name ?? 'N/A') . " - " .
                        ($class->subject->subject_name ?? 'N/A'),
                    "class_collection" => round($classTotal, 2),
                    "teacher_percentage" => $percentage,
                    "teacher_share" => $teacherShare,
                    "institution_share" => $institutionShare
                ];

                $totalTeacherShare += $teacherShare;
                $totalInstitutionShare += $institutionShare;
            }

            // ✅ Advance payments (non-salary)
            $advanceTotal = TeacherPayment::where('teacher_id', $teacherId)
                ->where('status', 1)
                ->where('reason_code', '!=', 'salary')
                ->where('payment_for', $start->format('m Y'))
                ->sum('payment');

            $netSalary = max(0, $totalTeacherShare - $advanceTotal);

            // ✅ Final response
            return response()->json([
                "status" => "success",
                "teacher_id" => $teacherId,
                "teacher_name" => $teacher->fname . ' ' . $teacher->lname,
                "month" => $yearMonth,
                "earnings" => $earnings,
                "total_teacher_share" => round($totalTeacherShare, 2),
                "total_institution_share" => round($totalInstitutionShare, 2),
                "advance_payment" => round($advanceTotal, 2),
                "net_salary" => round($netSalary, 2),
                "payment_method" => "Cash / Bank Deposit"
            ]);
        } catch (Exception $e) {
            return response()->json([
                "status" => "error",
                "message" => $e->getMessage()
            ], 500);
        }
    }


    public function storeTeacherPayments(Request $request)
    {
        try {
            // Validate required fields
            $request->validate([
                'teacher_id' => 'required|exists:teachers,id',
                'payment' => 'required|numeric|min:0',
                'reason_code' => 'required|exists:payment_reason,reason_code',
            ]);

            $paymentDate = now(); // Current date/time

            // Get paymentFor from request or default to current month
            $paymentFor = $request->input('paymentFor', $paymentDate->format('M Y')); // e.g., "Dec 2025"

            // Convert month name to numeric month using Carbon
            if (preg_match('/^[A-Za-z]{3,9} \d{4}$/', $paymentFor)) {
                // Try parsing with short month first
                $carbonDate = Carbon::createFromFormat('M Y', $paymentFor);
                if (!$carbonDate) {
                    // Fallback for full month name, e.g., "June 2025"
                    $carbonDate = Carbon::createFromFormat('F Y', $paymentFor);
                }
                $paymentFor = $carbonDate->format('m Y'); // "06 2025"
            }

            $teacherPayment = TeacherPayment::create([
                'teacher_id' => $request->teacher_id,
                'payment' => $request->payment,
                'reason_code' => $request->reason_code,
                'reason' => '', // leave empty
                'payment_for' => $paymentFor,
                'date' => $paymentDate,
                'status' => 1, // active
                'user_id' => auth()->id() ?? null, // current logged in user
            ]);

            return response()->json([
                'status' => 'success',
                'data' => $teacherPayment,
                'message' => 'Teacher payment stored successfully.'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // send email repost


    public function studentPaymentMonthFlat($teacherId, $yearMonth)
    {
        try {
            // 1️⃣ Validation
            if (!$teacherId) {
                return [
                    'success' => false,
                    'message' => "Teacher ID is required"
                ];
            }

            $yearMonth = Carbon::parse($yearMonth)->format('Y-m');
            $startOfMonth = Carbon::createFromFormat('Y-m', $yearMonth)->startOfMonth();
            $endOfMonth   = Carbon::createFromFormat('Y-m', $yearMonth)->endOfMonth();

            // 2️⃣ Active teacher
            $teacher = Teacher::where('id', $teacherId)
                ->where('is_active', 1)
                ->select('id', 'custom_id', 'fname', 'lname', 'email')
                ->first();

            if (!$teacher) {
                return [
                    'success' => false,
                    'message' => "Active teacher not found"
                ];
            }

            // 3️⃣ Active classes with their percentages
            $classes = ClassRoom::where('is_active', 1)
                ->where('teacher_id', $teacherId)
                ->select('id', 'class_name', 'teacher_percentage')
                ->get();

            if ($classes->isEmpty()) {
                return [
                    'success' => true,
                    'teacher' => [
                        'id' => $teacher->id,
                        'name' => $teacher->fname . ' ' . $teacher->lname,
                        'email' => $teacher->email,
                    ],
                    'year_month' => $yearMonth,
                    'classes' => [],
                    'students' => []
                ];
            }

            $classIds = $classes->pluck('id');

            // 4️⃣ Fetch student-class assignments
            $studentClasses = StudentStudentStudentClass::with([
                'student:id,initial_name,custom_id,is_active',
                'studentClass:id,class_name,teacher_percentage'
            ])
                ->where('status', 1)
                ->whereIn('student_classes_id', $classIds)
                ->get();

            if ($studentClasses->isEmpty()) {
                return [
                    'success' => true,
                    'teacher' => [
                        'id' => $teacher->id,
                        'name' => $teacher->fname . ' ' . $teacher->lname,
                        'email' => $teacher->email,
                    ],
                    'year_month' => $yearMonth,
                    'classes' => [],
                    'students' => []
                ];
            }

            $studentClassIds = $studentClasses->pluck('id');

            // 5️⃣ Fetch payments
            $payments = Payments::where('status', 1)
                ->whereBetween('payment_date', [$startOfMonth, $endOfMonth])
                ->whereIn('student_student_student_classes_id', $studentClassIds)
                ->get()
                ->groupBy('student_student_student_classes_id');

            // 6️⃣ Build class-wise response
            $rows = [];
            $allStudents = []; // For flat students list

            foreach ($studentClasses as $sc) {
                if (!$sc->student || !$sc->student->is_active) continue;

                $classId = $sc->studentClass->id;
                $className = $sc->studentClass->class_name;
                $classPercentage = $sc->studentClass->teacher_percentage ?? 0;

                if (!isset($rows[$classId])) {
                    $rows[$classId] = [
                        'class_id' => $classId,
                        'class_name' => $className,
                        'teacher_percentage' => $classPercentage,
                        'total_students' => 0,
                        'paid_students' => 0,
                        'unpaid_students' => 0,
                        'free_students' => 0,
                        'paid_amount_total' => 0,
                        'teacher_earning' => 0,
                        'institution_income' => 0,
                        'students' => []
                    ];
                }

                $rows[$classId]['total_students']++;

                $studentPayments = $payments[$sc->id] ?? collect();

                // Free student
                $isFreeCard = $sc->is_free_card ?? false;
                if ($isFreeCard ) {
                    $rows[$classId]['free_students']++;

                    $studentData = [
                        'student_id' => $sc->student->id,
                        'student_name' => $sc->student->initial_name,
                        'custom_id' => $sc->student->custom_id,
                        'class_name' => $className,
                        'payment_status' => 'free',
                        'amount' => 0,
                        'teacher_earning' => 0,
                        'institution_income' => 0,
                        'date' => null,
                        'payment_for' => 'N/A'
                    ];

                    $rows[$classId]['students'][] = $studentData;
                    $allStudents[] = $studentData;
                }
                // Paid student
                elseif (!$studentPayments->isEmpty()) {
                    $rows[$classId]['paid_students']++;

                    foreach ($studentPayments as $pay) {
                        $amount = (float) $pay->amount;
                        $teacherCut = round(($amount * $classPercentage) / 100, 2);
                        $institutionCut = round($amount - $teacherCut, 2);

                        $rows[$classId]['paid_amount_total'] += $amount;
                        $rows[$classId]['teacher_earning'] += $teacherCut;
                        $rows[$classId]['institution_income'] += $institutionCut;

                        $studentData = [
                            'student_id' => $sc->student->id,
                            'student_name' => $sc->student->initial_name,
                            'custom_id' => $sc->student->custom_id,
                            'class_name' => $className,
                            'payment_status' => 'paid',
                            'amount' => $amount,
                            'teacher_earning' => $teacherCut,
                            'institution_income' => $institutionCut,
                            'date' => $pay->payment_date,
                            'payment_for' => $pay->payment_for
                        ];

                        $rows[$classId]['students'][] = $studentData;
                        $allStudents[] = $studentData;
                    }
                }
                // Unpaid student
                else {
                    $rows[$classId]['unpaid_students']++;

                    $studentData = [
                        'student_id' => $sc->student->id,
                        'student_name' => $sc->student->initial_name,
                        'custom_id' => $sc->student->custom_id,
                        'class_name' => $className,
                        'payment_status' => 'unpaid',
                        'amount' => 0,
                        'teacher_earning' => 0,
                        'institution_income' => 0,
                        'date' => null,
                        'payment_for' => 'N/A'
                    ];

                    $rows[$classId]['students'][] = $studentData;
                    $allStudents[] = $studentData;
                }
            }

            // Calculate totals for all classes
            $totalPaidAmount = 0;
            $totalTeacherEarning = 0;
            $totalInstitutionIncome = 0;

            foreach ($rows as $classId => $classData) {
                $totalPaidAmount += $classData['paid_amount_total'];
                $totalTeacherEarning += $classData['teacher_earning'];
                $totalInstitutionIncome += $classData['institution_income'];
            }

            // Return data array (NOT JsonResponse)
            return [
                'success' => true,
                'teacher' => [
                    'id' => $teacher->id,
                    'name' => $teacher->fname . ' ' . $teacher->lname,
                    'email' => $teacher->email,
                ],
                'year_month' => $yearMonth,
                'students' => $allStudents, // Add flat students list
                'totals' => [
                    'total_paid_amount' => round($totalPaidAmount, 2),
                    'total_teacher_earning' => round($totalTeacherEarning, 2),
                    'total_institution_income' => round($totalInstitutionIncome, 2)
                ],
                'classes' => array_values($rows)
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }




    public function teachersExpenses($yearMonth)
    {
        try {
            // Parse the year-month parameter (e.g., "2024-01")
            $startDate = Carbon::parse($yearMonth)->startOfMonth();
            $endDate = Carbon::parse($yearMonth)->endOfMonth();

            // Get all payments for the month
            $result = TeacherPayment::whereBetween('date', [$startDate, $endDate])
                ->where('reason_code', '!=', 'salary') // Alternative syntax
                ->with('user:id,name')
                ->with('teacher:id,custom_id,fname,lname,email')
                ->get(['id', 'payment', 'date', 'reason', 'reason_code', 'status', 'user_id', 'teacher_id']);

            // Calculate total
            $totalExpenses = $result->sum('payment');

            return response()->json([
                'status' => 'success',
                'year_month' => $yearMonth,
                'date_range' => [
                    'start' => $startDate->format('Y-m-d'),
                    'end' => $endDate->format('Y-m-d')
                ],
                'summary' => [
                    'total_expenses' => $totalExpenses,
                    'expense_count' => $result->count(),
                    'average_expense' => $result->count() > 0 ? $totalExpenses / $result->count() : 0
                ],
                'expenses' => $result
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle payment status (0 ↔ 1)
     */
    public function togglePaymentStatus(Request $request, $id)
    {
        try {
            // Validate the request - get reason from user input
            $validated = $request->validate([
                'reason' => 'required|string|min:3|max:500'
            ]);

            $payment = TeacherPayment::findOrFail($id);

            // Store old status for message
            $oldStatus = $payment->status;

            // Toggle status
            $payment->status = $oldStatus == 1 ? 0 : 1;

            // Update the reason field with user input
            $payment->reason = $validated['reason'];

            $payment->save();

            $action = $payment->status == 1 ? 'activated' : 'deactivated';

            return response()->json([
                'status' => 'success',
                'message' => "Payment {$action} successfully",
                'data' => [
                    'id' => $payment->id,
                    'status' => $payment->status,
                    'old_status' => $oldStatus,
                    'teacher_id' => $payment->teacher_id,
                    'amount' => $payment->payment,
                    'reason' => $payment->reason
                ]
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Payment not found'
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }


    public function studentPaymentMonthCheck($teacherId, $yearMonth)
    {
        //     try {
        //         if (!$teacherId) {
        //             return response()->json([
        //                 "status" => "error",
        //                 "message" => "Teacher ID is required"
        //             ], 400);
        //         }

        //         // Use the SAME date format as working function
        //         if (!preg_match('/^\d{4}-\d{2}$/', $yearMonth)) {
        //             // Try to convert if needed
        //             try {
        //                 $yearMonth = Carbon::parse($yearMonth)->format('Y-m');
        //             } catch (Exception $e) {
        //                 return response()->json([
        //                     "status" => "error",
        //                     "message" => "Year-Month format must be YYYY-MM"
        //                 ], 400);
        //             }
        //         }

        //         // Use EXACTLY the same date range logic as working function
        //         $startOfMonth = Carbon::createFromFormat('Y-m', $yearMonth)->startOfMonth();
        //         $endOfMonth   = Carbon::createFromFormat('Y-m', $yearMonth)->endOfMonth();
        //         // Get teacher details
        //         $teacher = Teacher::select('id', 'custom_id', 'fname', 'lname', 'email', 'precentage')
        //             ->find($teacherId);

        //         if (!$teacher) {
        //             return response()->json([
        //                 "status" => "error",
        //                 "message" => "Teacher not found"
        //             ], 404);
        //         }

        //         // Load teacher classes - SAME as working function
        //         $classes = ClassRoom::with(['subject', 'teacher', 'grade'])
        //             ->where('teacher_id', $teacherId)
        //             ->select('id', 'class_name', 'subject_id', 'teacher_id', 'grade_id')
        //             ->get();


        //         if ($classes->isEmpty()) {
        //             return response()->json([
        //                 'status' => 'success',
        //                 'teacher_id' => $teacherId,
        //                 'year_month' => $yearMonth,
        //                 'total_classes' => 0,
        //                 'total_students' => 0,
        //                 'total_paid_students' => 0,
        //                 'total_unpaid_students' => 0,
        //                 'payment_rate' => 0,
        //                 'total_collection' => 0,
        //                 'teacher_percentage' => $teacher->precentage ?? 0,
        //                 'total_teacher_amount' => 0,
        //                 'classes' => []
        //             ]);
        //         }

        //         $teacherName = $classes->first()->teacher->fname ?? 'Unknown Teacher';
        //         $subjectName = $classes->first()->subject->subject_name ?? 'Unknown Subject';

        //         $result = [];
        //         $totalClassAmount = 0;
        //         $totalStudents = 0;
        //         $totalPaidStudents = 0;
        //         $totalUnpaidStudents = 0;

        //         foreach ($classes as $cls) {
        //             // EXACTLY the same query as working function
        //             $classStudents = StudentStudentStudentClass::with(['student' => function ($q) {
        //                 $q->select('id', 'custom_id', 'fname', 'lname', 'img_url', 'whatsapp_mobile', 'guardian_mobile');
        //             }])
        //                 ->where('status', 1)
        //                 ->where('student_classes_id', $cls->id)
        //                 ->get();


        //             $paidStudents = [];
        //             $unpaidStudents = [];

        //             foreach ($classStudents as $studentClass) {
        //                 $student = $studentClass->student;
        //                 $studentId = $student->id ?? null;
        //                 $studentName = ($student->fname ?? '') . ' ' . ($student->lname ?? '');
        //                 $customId = $student->custom_id ?? 'N/A';

        //                 // EXACTLY the same payment check as working function
        //                 $payment = Payments::where('student_student_student_classes_id', $studentClass->id)
        //                     ->where('status', 1)
        //                     ->whereBetween('payment_date', [$startOfMonth, $endOfMonth])
        //                     ->select('amount', 'payment_date')
        //                     ->first();

        //                 $studentData = [
        //                     'id' => $studentId,
        //                     'custom_id' => $customId,
        //                     'name' => $studentName,
        //                     'is_free_card' => $studentClass->is_free_card ?? 0,
        //                 ];

        //                 if ($payment && $payment->amount > 0) {
        //                     // Student has paid (amount > 0)
        //                     $studentData['amount_paid'] = $payment->amount;
        //                     $studentData['payment_date'] = $payment->payment_date;
        //                     $studentData['paid_status'] = 'paid';
        //                     $paidStudents[] = $studentData;
        //                 } else {
        //                     // Student has not paid or amount is 0 or negative
        //                     $studentData['amount_paid'] = 0;
        //                     $studentData['payment_date'] = null;

        //                     if ($studentClass->is_free_card == 1) {
        //                         $studentData['paid_status'] = 'free_card';
        //                     } else {
        //                         $studentData['paid_status'] = 'unpaid';
        //                     }

        //                     $unpaidStudents[] = $studentData;
        //                 }
        //             }

        //             $totalClassStudents = count($classStudents);
        //             $paidCount = count($paidStudents);
        //             $unpaidCount = count($unpaidStudents);

        //             // Get total collection for this class (only amount > 0) - SAME as working function
        //             $totalCollection = Payments::whereHas('studentStudentClass', function ($q) use ($cls) {
        //                 $q->where('student_classes_id', $cls->id);
        //             })
        //                 ->where('status', 1)
        //                 ->where('amount', '>', 0) // Only positive amounts
        //                 ->whereBetween('payment_date', [$startOfMonth, $endOfMonth])
        //                 ->sum('amount');

        //             // Get payment summary by date (only amount > 0)
        //             $paymentsSummary = Payments::whereHas('studentStudentClass', function ($q) use ($cls) {
        //                 $q->where('student_classes_id', $cls->id);
        //             })
        //                 ->where('status', 1)
        //                 ->where('amount', '>', 0) // Only positive amounts
        //                 ->whereBetween('payment_date', [$startOfMonth, $endOfMonth])
        //                 ->selectRaw("DATE(payment_date) as pay_date, SUM(amount) as total_amount")
        //                 ->groupBy('pay_date')
        //                 ->get()
        //                 ->pluck('total_amount', 'pay_date');

        //             $result[] = [
        //                 'class_id' => $cls->id,
        //                 'class_name' => $cls->class_name,
        //                 'grade_name' => $cls->grade->grade_name ?? 'N/A',
        //                 'subject_name' => $cls->subject->subject_name ?? 'N/A',
        //                 'total_students' => $totalClassStudents,
        //                 'paid_students_count' => $paidCount,
        //                 'unpaid_students_count' => $unpaidCount,
        //                 'total_collection' => $totalCollection,
        //                 'class_total_paid' => $totalCollection,
        //                 'paid_students' => $paidStudents,
        //                 'unpaid_students' => $unpaidStudents,
        //                 'payments_summary' => $paymentsSummary
        //             ];

        //             // Update totals
        //             $totalStudents += $totalClassStudents;
        //             $totalPaidStudents += $paidCount;
        //             $totalUnpaidStudents += $unpaidCount;
        //             $totalClassAmount += $totalCollection;
        //         }

        //         // Calculate payment rate
        //         $paymentRate = $totalStudents > 0 ? round(($totalPaidStudents / $totalStudents) * 100, 2) : 0;

        //         // Calculate teacher's amount
        //         $teacherPercentage = $teacher->precentage ?? 0;
        //         $totalTeacherAmount = $totalClassAmount * ($teacherPercentage / 100);

        //         return response()->json([
        //             'status' => 'success',
        //             'success' => true,

        //             // Teacher information
        //             'teacher_id' => $teacherId,
        //             'teacher_custom_id' => $teacher->custom_id ?? '',
        //             'teacher_name' => trim($teacher->fname . ' ' . $teacher->lname),
        //             'teacher_email' => $teacher->email,
        //             'teacher_percentage' => $teacherPercentage,
        //             'subject_name' => $subjectName,

        //             // Report information
        //             'year_month' => $yearMonth,
        //             'date_range' => [
        //                 'start' => $startOfMonth->format('Y-m-d 00:00:00'),
        //                 'end' => $endOfMonth->format('Y-m-d 23:59:59')
        //             ],

        //             // Summary statistics
        //             'total_classes' => $classes->count(),
        //             'total_students' => $totalStudents,
        //             'total_paid_students' => $totalPaidStudents,
        //             'total_unpaid_students' => $totalUnpaidStudents,
        //             'payment_rate' => $paymentRate,

        //             // Financial summary
        //             'total_class_amount' => $totalClassAmount,
        //             'total_teacher_amount' => $totalTeacherAmount,
        //             'total_collection' => $totalClassAmount,
        //             'net_payable' => $totalTeacherAmount,
        //             'teacher_share' => $totalTeacherAmount,
        //             'institution_share' => $totalClassAmount - $totalTeacherAmount,
        //             'institution_percentage' => 100 - $teacherPercentage,
        //             'advance_payment_this_month' => 0,
        //             'is_salary_paid' => false,
        //             'salary_payments' => [],

        //             // For debugging
        //             'debug_info' => [
        //                 'expected_total' => 119300,
        //                 'actual_total' => $totalClassAmount,
        //                 'difference' => $totalClassAmount - 119300,
        //                 'payment_rate_percentage' => $paymentRate
        //             ],

        //             // Detailed data
        //             'classes' => $result,
        //             'data' => $result,

        //             // Additional metadata
        //             'report_generated_at' => now()->format('Y-m-d H:i:s'),
        //             'report_id' => 'PAY-' . date('Ymd') . '-' . $teacherId
        //         ]);
        //     } catch (Exception $e) {

        //         return response()->json([
        //             'status' => 'error',
        //             'success' => false,
        //             'message' => 'An error occurred: ' . $e->getMessage(),
        //             'error' => env('APP_DEBUG') ? $e->getMessage() : null
        //         ], 500);
        //     }
    }
}
