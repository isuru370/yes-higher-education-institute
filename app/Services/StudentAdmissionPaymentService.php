<?php

namespace App\Services;

use App\Models\AdmissionPayments;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;

class StudentAdmissionPaymentService
{
    public function storeBulkAdmissionPayment(Request $request)
    {
        try {

            // Force JSON response
            $request->headers->set('Accept', 'application/json');

            // Validate
            $validated = $request->validate([
                'payments' => 'required|array|min:1',
                'payments.*.student_id' => 'required|integer|exists:students,id',
                'payments.*.admission_id' => 'required|integer|exists:admissions,id',
                'payments.*.amount' => 'required|numeric|min:0',
            ]);

            // Process inside transaction
            DB::transaction(function () use ($validated) {

                $dataToInsert = [];
                $studentIdsToUpdate = [];

                foreach ($validated['payments'] as $payment) {

                    $dataToInsert[] = [
                        'student_id'   => $payment['student_id'],
                        'admission_id' => $payment['admission_id'],
                        'amount'       => $payment['amount'],
                        'created_at'   => now(),
                        'updated_at'   => now(),
                    ];

                    $studentIdsToUpdate[] = $payment['student_id'];
                }

                // Bulk insert
                AdmissionPayments::insert($dataToInsert);

                // Update admission status
                Student::whereIn('id', $studentIdsToUpdate)
                    ->update(['admission' => 1]);
            });

            return response()->json([
                'success' => true,
                'message' => 'Admission payments stored successfully',
                'inserted_count' => count($validated['payments']),
            ], 201);
        } catch (ValidationException $e) {

            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors'  => $e->errors()
            ], 422);
        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Bulk admission payment failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function fetchPayAdmissions()
    {
        $result = AdmissionPayments::with(['student', 'admission'])
            ->get()
            ->map(function ($payment) {
                return [
                    'id' => $payment->id,
                    'student_id' => $payment->student_id,
                    'student_name' => $payment->student ?  $payment->student->initial_name : 'N/A',
                    'admission_name' => $payment->admission ? $payment->admission->name : 'N/A',
                    'amount' => $payment->amount,
                    'created_at' => $payment->created_at->toDateTimeString(),
                ];
            });
        return response()->json([
            'status' => true,
            'data' => $result
        ], 200);
    }

    public function fetchPayAdmissionsStaticCart($year, $month)
    {
        // Get payments for the selected year and month
        $payments = AdmissionPayments::with('admission')
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->get();

        // Calculate daily collections
        $dailyCollections = [];
        $totalAmount = 0;

        // Group payments by day
        foreach ($payments as $payment) {
            $day = $payment->created_at->format('Y-m-d');
            $amount = (float) $payment->amount;

            // Add to total
            $totalAmount += $amount;

            // Add to daily collections
            if (!isset($dailyCollections[$day])) {
                $dailyCollections[$day] = 0;
            }
            $dailyCollections[$day] += $amount;
        }

        // Format daily collections for chart
        $formattedDailyCollections = [];
        foreach ($dailyCollections as $date => $amount) {
            $formattedDailyCollections[] = [
                'date' => $date,
                'amount' => $amount
            ];
        }

        // Sort daily collections by date
        usort($formattedDailyCollections, function ($a, $b) {
            return strcmp($a['date'], $b['date']);
        });

        // Format response data
        $result = $payments->map(function ($payment) {
            return [
                'id' => $payment->id,
                'admission_name' => $payment->admission ? $payment->admission->name : 'N/A',
                'amount' => $payment->amount,
                'created_at' => $payment->created_at->toDateTimeString(),
            ];
        });

        return response()->json([
            'status' => true,
            'data' => [
                'payments' => $result,
                'summary' => [
                    'total_amount' => $totalAmount,
                    'payment_count' => $payments->count(),
                    'selected_year' => $year,
                    'selected_month' => $month,
                    'month_name' => date('F', mktime(0, 0, 0, $month, 1))
                ],
                'daily_collections' => $formattedDailyCollections,
                'chart_data' => $this->formatChartData($formattedDailyCollections)
            ]
        ], 200);
    }

    // Helper method to format data for line chart
    private function formatChartData($dailyCollections)
    {
        $labels = [];
        $data = [];

        foreach ($dailyCollections as $collection) {
            // Format date for display (e.g., "Jan 01")
            $date = \Carbon\Carbon::parse($collection['date']);
            $labels[] = $date->format('M d');
            $data[] = $collection['amount'];
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Daily Collections',
                    'data' => $data,
                    'borderColor' => 'rgb(75, 192, 192)',
                    'backgroundColor' => 'rgba(75, 192, 192, 0.2)',
                    'tension' => 0.1
                ]
            ]
        ];
    }
    public function fetchStudentAdmissions(Request $request)
    {
        // Validate that student_id exists and is an integer
        $validator = Validator::make($request->all(), [
            'student_id' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $studentId = $request->student_id;

        try {
            $result = AdmissionPayments::with(['student', 'admission'])
                ->where('student_id', $studentId)
                ->latest()
                ->get()
                ->map(function ($payment) {
                    return [
                        'id' => $payment->id,
                        'student_id' => $payment->student_id,
                        'student_name' => $payment->student ? $payment->student->initial_name : 'N/A',
                        'admission_name' => $payment->admission ? $payment->admission->name : 'N/A',
                        'amount' => $payment->amount,
                        'created_at' => $payment->created_at->toDateTimeString(),
                    ];
                });

            return response()->json([
                'status' => true,
                'message' => 'Student admissions fetched successfully',
                'data' => $result,
                'count' => $result->count()
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }
}
