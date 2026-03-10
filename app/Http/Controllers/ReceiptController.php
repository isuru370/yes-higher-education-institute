<?php

namespace App\Http\Controllers;

use App\Services\StudentPaymentService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;

class ReceiptController extends Controller
{
    protected $paymentService;

    public function __construct(StudentPaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * View receipt in browser
     */
    public function viewReceipt($paymentId)
    {
        try {
            $payment = $this->paymentService->receiptPrint($paymentId);

            if ($payment['status'] != 'success') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Receipt not found'
                ], 404);
            }

            $data = $payment['data'];

            return view('receipt.template', [
                'data' => $data,
                'print' => request()->has('print')
            ]);

        } catch (\Exception $e) {
            Log::error('Receipt view error: ' . $e->getMessage());
            
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to load receipt. Please try again.'
            ], 500);
        }
    }

    /**
     * Download receipt as PDF
     */
    public function downloadReceipt($paymentId)
    {
        try {
            $payment = $this->paymentService->receiptPrint($paymentId);

            if ($payment['status'] != 'success') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Receipt not found'
                ], 404);
            }

            $data = $payment['data'];

            $pdf = PDF::loadView('receipt.pdf-template', [
                'data' => $data,
                'date' => now()->format('Y-m-d H:i:s')
            ]);

            $pdf->setPaper([0, 0, 226.77, 800], 'portrait'); // 80mm width
            $pdf->setOptions([
                'defaultFont' => 'Helvetica',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true
            ]);

            $filename = 'Receipt_' . $data['student']['custom_id'] . '_' . date('Ymd_His') . '.pdf';
            
            return $pdf->download($filename);

        } catch (\Exception $e) {
            Log::error('PDF download error: ' . $e->getMessage());
            
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to generate PDF.'
            ], 500);
        }
    }

    /**
     * Print to thermal printer
     */
    public function thermalPrint($paymentId)
    {
        // try {
        //     $payment = $this->paymentService->receiptPrint($paymentId);

        //     if ($payment['status'] != 'success') {
        //         return response()->json([
        //             'status' => 'error',
        //             'message' => 'Payment not found'
        //         ], 404);
        //     }

        //     $data = $payment['data'];

        //     // Thermal printer connection
        //     $connector = new WindowsPrintConnector("POS-58");
        //     $printer = new Printer($connector);

        //     // Print receipt
        //     $printer->setJustification(Printer::JUSTIFY_CENTER);
        //     $printer->text("SUCCESS ACADEMY\n");
        //     $printer->text("PAYMENT RECEIPT\n");
        //     $printer->text("================\n");

        //     $printer->setJustification(Printer::JUSTIFY_LEFT);
        //     $printer->text("Receipt: #" . $data['id'] . "\n");
        //     $printer->text("Date: " . date('d/m/Y', strtotime($data['payment_date'])) . "\n");
        //     $printer->text("----------------\n");
            
        //     $printer->text("STUDENT:\n");
        //     $printer->text($data['student']['fname'] . " " . $data['student']['lname'] . "\n");
        //     $printer->text("ID: " . $data['student']['custom_id'] . "\n");
            
        //     $printer->text("----------------\n");
        //     $printer->text("CLASS: " . $data['student_class']['class_name'] . "\n");
        //     $printer->text("GRADE: " . $data['student_class']['grade'] . "\n");
        //     $printer->text("SUBJECT: " . $data['student_class']['subject'] . "\n");
        //     $printer->text("CATEGORY: " . $data['class_category_has_student_class']['class_category']['category_name'] . "\n");
        //     $printer->text("FOR: " . $data['payment_for'] . "\n");
            
        //     $printer->text("----------------\n");
        //     $printer->text("Class Fees: Rs." . $data['class_category_has_student_class']['fees'] . "\n");
        //     $printer->text("Hall Price: Rs." . $data['hall_price'] . "\n");
        //     $printer->text("----------------\n");
            
        //     $printer->setJustification(Printer::JUSTIFY_CENTER);
        //     $printer->text("TOTAL: Rs." . $data['total'] . "\n");
        //     $printer->text("================\n");
        //     $printer->text("Thank You!\n");
        //     $printer->text("Success Academy\n");
            
        //     $printer->feed(2);
        //     $printer->cut();
        //     $printer->close();

        //     return response()->json([
        //         'status' => 'success',
        //         'message' => 'Receipt printed successfully'
        //     ]);

        // } catch (\Exception $e) {
        //     Log::error('Thermal print error: ' . $e->getMessage());
            
        //     return response()->json([
        //         'status' => 'error',
        //         'message' => 'Failed to print receipt. Please download instead.',
        //         'error' => $e->getMessage()
        //     ], 500);
        // }
    }
}