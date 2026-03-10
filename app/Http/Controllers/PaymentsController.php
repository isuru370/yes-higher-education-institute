<?php

namespace App\Http\Controllers;


use App\Services\StudentPaymentService;
use Illuminate\Http\Request;


class PaymentsController extends Controller
{
    protected $paymentService;

    public function __construct(StudentPaymentService $studentPaymentService)
    {
        $this->paymentService = $studentPaymentService;
    }

    public function fetchStudentPayments($student_id, $student_class_id)
    {
        return $this->paymentService->fetchStudentPayments($student_id, $student_class_id);
    }

    public function mobileReadStudentPayment(Request $request)
    {
        return $this->paymentService->fetchPaymentsByQRCode($request);
    }

    public function receiptPrint($payment_id)
    {
        return $this->paymentService->receiptPrint($payment_id);
    }

    public function deletePayment($id)
    {
        return $this->paymentService->deletePayment($id);
    }

    public function updatePayment(Request $request, $id)
    {
        return $this->paymentService->updatePayment($request, $id);
    }

    public function getPaymentsByDate($date)
    {
        return $this->paymentService->getPaymentsByDate($date);
    }

    public function getTeacherPayments(Request $request)
    {
        return $this->paymentService->getTeacherPayments($request);
    }

    public function storePayment(Request $request)
    {
        return $this->paymentService->storePayment($request);
    }


    /*
     * web page route
    */

    public function indexPage()
    {
        return view('student_payment.index');
    }

    public function createPage()
    {
        return view('student_payment.create');
    }

    public function detailsPage($student_id, $student_class_id)
    {
        return view('student_payment.details', compact('student_id', 'student_class_id'));
    }
}
