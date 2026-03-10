<?php

namespace App\Services;

use App\Models\Student;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;

class ReadQRCodeService
{
    public function readQRCode(Request $request)
    {
        $request->validate([
            'qr_code' => 'required|string',
        ]);

        try {
            $qrCode = $request->qr_code;
            $now = Carbon::now();

            // 1️⃣ Check if it's a temporary QR (starts with TMP)
            if (str_starts_with($qrCode, 'TMP')) {
                // Temporary QR → find student by temporary_qr_code
                $student = Student::where('temporary_qr_code', $qrCode)
                    ->where('student_disable', false)
                    ->first();

                if (!$student) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Temporary QR code invalid'
                    ], 404);
                }

                // Check if expired
                if ($student->temporary_qr_code_expire_date && $now->gt($student->temporary_qr_code_expire_date)) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Temporary QR code has expired'
                    ], 403);
                }

                return response()->json([
                    'status' => 'success',
                    'message' => 'Temporary QR code valid',
                    'student_id' => $student->id
                ], 200);
            }

            // 2️⃣ Otherwise → permanent QR (original QR = custom_id)
            $student = Student::where('custom_id', $qrCode)
                ->where('student_disable', false)
                ->first();

            if (!$student) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'QR code invalid'
                ], 404);
            }

            if (!$student->permanent_qr_active) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Permanent QR code is inactive'
                ], 403);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Original QR code valid',
                'student_id' => $student->id
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function studentIdCardActive($custom_id)
    {
        try {
            // Find the student by custom_id
            $student = Student::where('custom_id', $custom_id)->first();

            if (!$student) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Student not found',
                ], 404);
            }

            // Update the flags
            $student->update([
                'permanent_qr_active' => true,
                'student_disable' => false,
                'is_active' => true,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Student QR activated successfully',
                'student_id' => $student->id,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to activate student QR',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
