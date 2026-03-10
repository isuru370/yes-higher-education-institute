<?php

namespace App\Http\Controllers;

use App\Mail\PaymentReportMail;
use App\Services\TeacherPaymentsService;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;

class EmailsController extends Controller
{
    protected $teacherPaymentsService;

    public function __construct(TeacherPaymentsService $teacherPaymentsService)
    {
        $this->teacherPaymentsService = $teacherPaymentsService;
    }

    // public function sendPaymentReport($teacherId, $yearMonth)
    // {
    //     try {
    //         // Validate YYYY-MM
    //         if (!preg_match('/^\d{4}-\d{2}$/', $yearMonth)) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Invalid date format. Use YYYY-MM'
    //             ], 400);
    //         }

    //         // Get flat payment data
    //         $paymentData = $this->teacherPaymentsService
    //             ->studentPaymentMonthFlat($teacherId, $yearMonth);

    //         if (!$paymentData['success']) {
    //             return response()->json($paymentData, 400);
    //         }

    //         /* ---------- CALCULATE TOTALS FROM FLAT DATA ---------- */
    //         $students = $paymentData['students'];
    //         $totalAmount = collect($students)->sum('amount');
    //         $totalStudents = collect($students)->pluck('student_id')->unique()->count();

    //         /* ---------- FORMAT MONTH FOR DISPLAY ---------- */
    //         $formattedMonth = date('F Y', strtotime($yearMonth . '-01')); // මෙතන define කරන්න
    //         $fileNameMonth = date('F-Y', strtotime($yearMonth . '-01'));

    //         /* ---------- PDF DATA ---------- */
    //         $pdfViewData = [
    //             'students' => $students,
    //             'teacher' => $paymentData['teacher'],
    //             'yearMonth' => $yearMonth,
    //             'month' => $formattedMonth,  // ✅ දැන් හරියටම define කරලා
    //             'totalAmount' => $totalAmount,
    //             'totalStudents' => $totalStudents
    //         ];

    //         // Generate PDF
    //         $pdf = Pdf::loadView('emails.payment_report', $pdfViewData)
    //             ->setPaper('A4', 'portrait');

    //         $fileName = "payment-report-{$teacherId}-{$fileNameMonth}.pdf";

    //         // Check teacher email
    //         $teacherEmail = $paymentData['teacher']['email'] ?? null;
    //         if (!$teacherEmail) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Teacher email not found'
    //             ], 400);
    //         }

    //         // Send email
    //         Mail::to($teacherEmail)->send(new PaymentReportMail(
    //             $paymentData,
    //             $pdf->output(),
    //             $fileName,
    //             $teacherId,
    //             $formattedMonth
    //         ));

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Payment report sent successfully',
    //             'file_name' => $fileName,
    //             'month' => $formattedMonth,
    //             'total_students' => $totalStudents,
    //             'total_amount' => $totalAmount
    //         ]);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }

    public function downloadPaymentReport($teacherId, $yearMonth)
    {
        try {
            // Validate YYYY-MM
            if (!preg_match('/^\d{4}-\d{2}$/', $yearMonth)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid date format. Use YYYY-MM'
                ], 400);
            }

            // Get flat payment data - this now returns an array, not JsonResponse
            $paymentData = $this->teacherPaymentsService
                ->studentPaymentMonthFlat($teacherId, $yearMonth);

            // Check if successful
            if (!$paymentData['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $paymentData['message'] ?? 'Failed to get payment data'
                ], 400);
            }

            /* ---------- CALCULATE TOTALS ---------- */
            $students = $paymentData['students'] ?? [];
            $totalAmount = collect($students)->sum('amount');
            $totalStudents = collect($students)->pluck('student_id')->unique()->count();

            /* ---------- FORMAT MONTH ---------- */
            $formattedMonth = date('F Y', strtotime($yearMonth . '-01'));
            $fileNameMonth = date('F-Y', strtotime($yearMonth . '-01'));

            /* ---------- PDF DATA ---------- */
            $pdfViewData = [
                'students' => $students,
                'teacher' => $paymentData['teacher'],
                'yearMonth' => $yearMonth,
                'month' => $formattedMonth,
                'totalAmount' => $totalAmount,
                'totalStudents' => $totalStudents,
                'classData' => $paymentData['classes'] ?? [] // Add class data for PDF
            ];

            // Generate PDF
            $pdf = Pdf::loadView('emails.payment_report', $pdfViewData)
                ->setPaper('A4', 'portrait');

            $fileName = "payment-report-{$teacherId}-{$fileNameMonth}.pdf";

            // Download PDF
            return $pdf->download($fileName);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
