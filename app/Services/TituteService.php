<?php

namespace App\Services;

use App\Models\ClassCategoryHasStudentClass;
use App\Models\Student;
use App\Models\StudentStudentStudentClass;
use App\Models\Titute;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TituteService
{
    /**
     * Fetch titute records with filters
     */

    public function readClassWiseTuteByQR(Request $request)
    {
        $request->validate([
            'qr_code' => 'required|string',
        ]);

        $qrCode = $request->qr_code;
        $now = Carbon::now();

        try {
            // 1️⃣ Temporary QR
            if (str_starts_with($qrCode, 'TMP')) {
                $student = Student::where('temporary_qr_code', $qrCode)
                    ->where('student_disable', false)
                    ->first();

                if (!$student) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Temporary QR code invalid'
                    ], 404);
                }

                if ($student->temporary_qr_code_expire_date && $now->gt($student->temporary_qr_code_expire_date)) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Temporary QR code has expired'
                    ], 403);
                }
            }
            // 2️⃣ Permanent QR
            else {
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
            }

            // ✅ Fetch student classes
            $stuClasses = StudentStudentStudentClass::with([
                'student:id,custom_id,full_name,initial_name,guardian_mobile,img_url',
                'studentClass:id,class_name,grade_id',
                'classCategoryHasStudentClass.classCategory:id,category_name',
            ])
                ->where('student_id', $student->id)
                ->where('status', 1)
                ->get()
                ->map(function ($item) {
                    return [
                        'student' => [
                            'id' => $item->student_id,
                            'custom_id' => $item->student->custom_id ?? null,
                            'first_name' => $item->student->full_name ?? null,
                            'last_name' => $item->student->initial_name ?? null,
                            'guardian_mobile' => $item->student->guardian_mobile ?? null,
                            'img_url' => $item->student->img_url ?? null,
                        ],
                        'class_name' => optional($item->studentClass)->class_name,
                        'class_category_has_student_class_id' => $item->class_category_has_student_class_id,
                        'grade_name' => optional($item->studentClass->grade)->grade_name,
                        'category_name' => optional($item->classCategoryHasStudentClass->classCategory)->category_name,
                    ];
                });

            return response()->json([
                'status' => 'success',
                'qr_type' => str_starts_with($qrCode, 'TMP') ? 'temporary' : 'permanent',
                'total' => $stuClasses->count(),
                'data' => $stuClasses
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getStudentWithAllTutes(
        int $studentId,
        int $classCategoryStudentClassId
    ) {


        $query = Titute::query()
            ->with([
                'classCategoryHasStudentClass.studentClass:id,class_name,grade_id',
                'classCategoryHasStudentClass.classCategory:id,category_name',
                'classCategoryHasStudentClass.studentClass.grade:id,grade_name'
            ])
            ->where('student_id', $studentId)
            ->where('class_category_has_student_class_id', $classCategoryStudentClassId);


        $tutes = $query->latest()
            ->get()
            ->map(function ($titute) {
                return [
                    'id' => $titute->id,
                    'tute_for' => $titute->titute_for,
                    'status' => $titute->status,
                    'created_at' => $titute->created_at->format('Y-m-d'),
                    'class' => [
                        'class_name' => optional($titute->classCategoryHasStudentClass->studentClass)->class_name,
                        'category_name' => optional($titute->classCategoryHasStudentClass->classCategory)->category_name,
                        'grade_name' => optional($titute->classCategoryHasStudentClass->studentClass->grade)->grade_name
                    ]

                ];
            });

        return response()->json([
            'status' => 'success',
            'total'  => $tutes->count(),
            'data'   => $tutes
        ]);
    }


    public function checkTute(
        Request $request,
        int $studentId,
        int $classCategoryStudentClassId
    ) {

        // Validate optional year & month
        $validated = $request->validate([
            'year'  => ['nullable', 'integer', 'digits:4'],
            'month' => ['nullable', 'integer', 'between:1,12'],
        ]);

        // Default current year/month
        $year  = $validated['year']  ?? now()->year;
        $month = $validated['month'] ?? now()->month;

        // Create "Feb 2026" format
        $tituteFor = Carbon::create($year, $month, 1)->format('M Y');

        $query = Titute::query()
            ->where('student_id', $studentId)
            ->where('class_category_has_student_class_id', $classCategoryStudentClassId)
            ->where('titute_for', $tituteFor)
            ->where('status', 1);

        if (!$query->exists()) {
            return response()->json([
                'status' => 'success',
                'titute_for' => $tituteFor,
                'exists' => false,
                'data' => null
            ]);
        }

        $tutes = $query->with([
            'student:id,custom_id,full_name,initial_name',
            'classCategoryHasStudentClass.studentClass:id,class_name',
            'classCategoryHasStudentClass.classCategory:id,category_name'
        ])
            ->get()
            ->map(function ($titute) {
                return [
                    'id' => $titute->id,
                    'titute_for' => $titute->titute_for,
                    'student_custom_id' => $titute->student->custom_id ?? null,
                    'student_name' => trim(
                        ($titute->student->full_name ?? '') . ' ' .
                            ($titute->student->initial_name ?? '')
                    ),
                    'class_name' => optional($titute->classCategoryHasStudentClass->studentClass)->class_name,
                    'class_category_name' => optional($titute->classCategoryHasStudentClass->classCategory)->category_name,
                ];
            });

        return response()->json([
            'status' => 'success',
            'titute_for' => $tituteFor,
            'exists' => true,
            'data' => $tutes
        ]);
    }
    /**
     * Store new titute
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => ['required', 'integer', 'exists:students,id'],
            'class_category_has_student_class_id' => ['required', 'integer', 'exists:class_category_has_student_class,id'],
            'year' => ['required', 'integer', 'digits:4'],
            'month' => ['required', 'integer', 'between:1,12'],
        ]);

        $tituteFor = Carbon::create($validated['year'], $validated['month'], 1)->format('M Y');

        $exists = Titute::where('student_id', $validated['student_id'])
            ->where('class_category_has_student_class_id', $validated['class_category_has_student_class_id'])
            ->where('titute_for', $tituteFor)
            ->exists();

        if ($exists) {
            return response()->json([
                'status' => 'error',
                'message' => 'Titute already exists for this month.'
            ], 409);
        }

        Titute::create([
            'student_id' => $validated['student_id'],
            'class_category_has_student_class_id' => $validated['class_category_has_student_class_id'],
            'titute_for' => $tituteFor,
            'status' => true
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Titute created successfully.',
        ], 200);
    }

    /**
     * Soft delete (status = 0)
     */

    public function toggleStatus(int $id)
    {
        $titute = Titute::findOrFail($id);

        $titute->status = !$titute->status;
        $titute->save();

        return response()->json([
            'status' => 'success',
            'message' => $titute->status ? 'Titute activated.' : 'Titute deactivated.',
        ]);
    }
}
