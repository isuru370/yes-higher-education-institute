<?php

namespace App\Services;

use App\Models\Student;
use App\Models\StudentStudentStudentClass;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;

class StudentStudentStudentClassService
{

    public function readStudentClass(Request $request)
    {
        try {
            $request->validate([
                'qr_code' => 'required|string',
            ]);

            $qrCode = $request->qr_code;
            $now = Carbon::now();

            // 1️⃣ Determine temporary or permanent QR
            if (str_starts_with($qrCode, 'TMP')) {

                $student = Student::where('temporary_qr_code', $qrCode)
                    ->where('student_disable', false)
                    ->first();

                if (!$student) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Temporary QR code invalid',
                        'data' => []
                    ], 404);
                }

                if (
                    $student->temporary_qr_code_expire_date &&
                    $now->gt($student->temporary_qr_code_expire_date)
                ) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Temporary QR code expired',
                        'data' => []
                    ], 403);
                }
            } else {

                // Permanent QR
                $student = Student::where('custom_id', $qrCode)
                    ->where('student_disable', false)
                    ->first();

                if (!$student) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'QR code invalid',
                        'data' => []
                    ], 404);
                }

                if (!$student->permanent_qr_active) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Permanent QR code inactive',
                        'data' => []
                    ], 403);
                }
            }

            // 2️⃣ Get student classes (optional)
            $classes = StudentStudentStudentClass::with([
                'studentClass',
                'studentClass.grade',
                'studentClass.subject',
                'classCategoryHasStudentClass.classCategory'
            ])
                ->where('student_id', $student->id)
                ->get()
                ->map(function ($item) {

                    return [
                        'student_student_student_classes_id' => $item->id,
                        'status' => (bool) $item->status,
                        'is_free_card' => (bool) $item->is_free_card,

                        'student_class' => [
                            'id' => optional($item->studentClass)->id,
                            'class_name' => optional($item->studentClass)->class_name,
                            'medium' => optional($item->studentClass)->medium,
                        ],
                        'grade' => [
                            'id' => optional($item->studentClass->grade)->id,
                            'grade_name' => optional($item->studentClass->grade)->grade_name,
                        ],

                        'subject' => [
                            'id' => optional($item->studentClass->subject)->id,
                            'subject_name' => optional($item->studentClass->subject)->subject_name,
                        ],

                        'class_category_has_student_class' => [
                            'id' => optional($item->classCategoryHasStudentClass)->id,
                            'fees' => optional($item->classCategoryHasStudentClass)->fees,
                            'class_category' => [
                                'category_name' => optional(optional($item->classCategoryHasStudentClass)->classCategory)->category_name,
                            ],
                        ],
                    ];
                });

            // 3️⃣ Student details (always returned)
            $studentData = [
                'id' => $student->id,
                'custom_id' => $student->custom_id,
                'first_name' => $student->full_name,
                'last_name' => $student->initial_name,
                'guardian_mobile' => $student->guardian_mobile,
                'img_url' => $student->img_url,
            ];

            return response()->json([
                'status' => 'success',
                'message' => 'Student data fetched successfully',
                'data' => [
                    'student' => $studentData,
                    'classes' => $classes
                ]
            ], 200);
        } catch (Exception $e) {

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch student data',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function getStudentsByClassAndCategory($classId, $categoryId)
    {
        try {
            // Fetch all students for the given class and category
            $students = StudentStudentStudentClass::where('student_classes_id', $classId)
                ->where('class_category_has_student_class_id', $categoryId)
                ->get();

            // Check if no records found
            if ($students->isEmpty()) {
                return response()->json([
                    'status' => 'empty',
                    'message' => 'No students assigned to this class and category'
                ]);
            }

            // Return the records
            return response()->json([
                'status' => 'success',
                'data' => $students
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch students',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function allDetailsGetStudentsByClassAndCategory($classId, $categoryId)
    {
        try {
            // Fetch students with related models (Eager Loading)
            $students = StudentStudentStudentClass::with(['student', 'studentClass', 'classCategoryHasStudentClass'])
                ->where('student_classes_id', $classId)
                ->where('class_category_has_student_class_id', $categoryId)
                ->get();

            if ($students->isEmpty()) {
                return response()->json([
                    'status'  => 'empty',
                    'message' => 'No students assigned to this class and category.'
                ], 200);
            }

            return response()->json([
                'status' => 'success',
                'data'   => $students
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to fetch students.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function getStudentClassessDetails($student_id)
    {
        try {
            // Fetch all students for the given class and category with ALL required relationships
            $students = StudentStudentStudentClass::with([
                'student',
                'studentClass.teacher',      // Teacher through studentClasses
                'studentClass.subject',      // Subject through studentClasses  
                'studentClass.grade',        // Grade through studentClasses
                'classCategoryHasStudentClass.classCategory' // Category through classCategoryHasStudentClass
            ])
                ->where('student_id', $student_id)
                ->get();

            // Check if no records found
            if ($students->isEmpty()) {
                return response()->json([
                    'status' => 'empty',
                    'message' => 'No students assigned to this class and category'
                ]);
            }

            // Return the records
            return response()->json([
                'status' => 'success',
                'data' => $students
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch students',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getStudentClassessFilterDetails($student_id)
    {
        try {
            // Fetch all students for the given class and category with ALL required relationships
            $students = StudentStudentStudentClass::with([
                'student',
                'studentClass.teacher',      // Teacher through studentClasses
                'studentClass.subject',      // Subject through studentClasses  
                'studentClass.grade',        // Grade through studentClasses
                'classCategoryHasStudentClass.classCategory' // Category through classCategoryHasStudentClass
            ])
                ->where('student_id', $student_id)
                ->get()
                ->map(function ($item) {
                    return [
                        'student_student_student_class_id' => $item->id,
                        'student_id' => $item->student_id,
                        'student_classes_id' => $item->student_classes_id,
                        'class_category_has_student_class_id' => $item->class_category_has_student_class_id,
                        'status' => $item->status,
                        'is_free_card' => $item->is_free_card,
                        'joined_date' => $item->created_at->toDateString(),
                        'classCategoryHasStudentClass' => [
                            'class_fee' => $item->classCategoryHasStudentClass->fees,
                        ],
                        'student' => [
                            'student_custom_id' => $item->student->custom_id,
                            'first_name' => $item->student->full_name,
                            'last_name' => $item->student->initial_name,
                            'img_url' => $item->student->img_url,
                            'guardian_mobile' => $item->student->guardian_mobile,
                            'student_status' => $item->student->is_active,
                        ],
                        'student_class' => [
                            'class_name' => $item->studentClass->class_name,
                            'teacher' => [
                                'teacher_id' => $item->studentClass->teacher->id,
                                'first_name' => $item->studentClass->teacher->fname,
                                'last_name' => $item->studentClass->teacher->lname
                            ],
                            'subject' => [
                                'subject_name' => $item->studentClass->subject->subject_name,
                            ],
                            'grade' => [
                                'grade_name' => $item->studentClass->grade->grade_name,
                            ],
                        ],
                        'class_category' => [
                            'category_name' => $item->classCategoryHasStudentClass->classCategory->category_name,
                        ],
                    ];
                });

            // Check if no records found
            if ($students->isEmpty()) {
                return response()->json([
                    'status' => 'empty',
                    'message' => 'No students assigned to this class and category'
                ]);
            }

            // Return the records
            return response()->json([
                'status' => 'success',
                'data' => $students
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch students',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function bulkStore(Request $request)
    {
        try {
            DB::beginTransaction();

            // Validation
            $validated = $request->validate([
                'students' => 'required|array|min:1',
                'students.*.student_id' => 'required|integer|exists:students,id',
                'student_classes_id' => 'required|integer|exists:student_classes,id',
                'class_category_has_student_class_id' => 'required|integer|exists:class_category_has_student_class,id',
            ]);

            $studentClassID = $validated['student_classes_id'];
            $categoryID = $validated['class_category_has_student_class_id'];

            $created = [];
            $skipped = [];

            foreach ($validated['students'] as $studentData) {

                // 🔍 Check if record already exists
                $existingRecord = StudentStudentStudentClass::where([
                    'student_id' => $studentData['student_id'],
                    'student_classes_id' => $studentClassID,
                    'class_category_has_student_class_id' => $categoryID,
                ])->first();

                if ($existingRecord) {
                    $message = $existingRecord->status == 0
                        ? "duplicate entry — class inactive"
                        : "duplicate entry";

                    $skipped[] = [
                        'student_id' => $studentData['student_id'],
                        'message' => $message
                    ];
                    continue;
                }

                // 🟢 Create new record
                $record = StudentStudentStudentClass::create([
                    'student_id' => $studentData['student_id'],
                    'student_classes_id' => $studentClassID,
                    'class_category_has_student_class_id' => $categoryID,
                    'status' => $studentData['status'] ?? 1, // default active
                    'is_free_card' => $studentData['is_free_card'] ?? false,
                ]);

                // Mark active/inactive text
                $record->inactive_text = $record->status == 0 ? "inactive" : "active";

                $created[] = $record;
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Bulk records processed',
                'created_count' => count($created),
                'skipped' => $skipped,
                'created_records' => $created
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to save bulk records',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function storeSingleStudentClass(Request $request)
    {
        try {
            // Validation
            $validated = $request->validate([
                'student_id' => 'required|integer|exists:students,id',
                'student_classes_id' => 'required|integer|exists:student_classes,id',
                'class_category_has_student_class_id' => 'required|integer|exists:class_category_has_student_class,id',
                'status' => 'nullable|integer|in:0,1',
                'is_free_card' => 'nullable|boolean',
            ]);

            $studentId = $validated['student_id'];
            $studentClassID = $validated['student_classes_id'];
            $categoryID = $validated['class_category_has_student_class_id'];

            // 🔍 Check if record already exists
            $existingRecord = StudentStudentStudentClass::where([
                'student_id' => $studentId,
                'student_classes_id' => $studentClassID,
                'class_category_has_student_class_id' => $categoryID,
            ])->first();

            if ($existingRecord) {
                $message = $existingRecord->status == 0
                    ? "duplicate entry — class inactive"
                    : "duplicate entry";

                return response()->json([
                    'status' => 'error',
                    'message' => $message,
                    'existing_record' => $existingRecord
                ], 409);
            }

            // 🟢 Create new record
            $record = StudentStudentStudentClass::create([
                'student_id' => $studentId,
                'student_classes_id' => $studentClassID,
                'class_category_has_student_class_id' => $categoryID,
                'status' => $validated['status'] ?? 1, // default active
                'is_free_card' => $validated['is_free_card'] ?? false,
            ]);

            // Add active/inactive text for response
            $record->inactive_text = $record->status == 0 ? "inactive" : "active";

            return response()->json([
                'status' => 'success',
                'message' => 'Record created successfully',
                'record' => $record
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to save record',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Activate a student class record
    public function activateStudentClass($id)
    {
        try {
            $record = StudentStudentStudentClass::findOrFail($id);

            $record->status = 1; // set active
            $record->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Student class record activated',
                'record' => $record
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to activate record',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Deactivate a student class record
    public function deactivateStudentClass($id)
    {
        try {
            $record = StudentStudentStudentClass::findOrFail($id);

            $record->status = 0; // set inactive
            $record->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Student class record deactivated',
                'record' => $record
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to deactivate record',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function bulkDeactivateStudentClasses(Request $request)
    {
        try {
            // Validate request
            $validated = $request->validate([
                'student_class_ids' => 'required|array|min:1',
                'student_class_ids.*' => 'required|integer|exists:student_student_student_classes,id',
            ]);

            $deactivated = [];
            $skipped = [];

            foreach ($validated['student_class_ids'] as $id) {
                $record = StudentStudentStudentClass::find($id);

                if (!$record) {
                    $skipped[] = [
                        'id' => $id,
                        'message' => 'Record not found'
                    ];
                    continue;
                }

                if ($record->status == 0) {
                    $skipped[] = [
                        'id' => $id,
                        'message' => 'Already inactive'
                    ];
                    continue;
                }

                $record->status = 0;
                $record->save();
                $record->inactive_text = "inactive";

                $deactivated[] = $record;
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Bulk deactivation processed',
                'deactivated_count' => count($deactivated),
                'skipped' => $skipped,
                'deactivated_records' => $deactivated
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to deactivate records',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function toggleStudentClassStatus($id)
    {
        try {
            $record = StudentStudentStudentClass::findOrFail($id);

            // Toggle status
            $record->status = $record->status == 1 ? 0 : 1;
            $record->save();

            $message = $record->status == 1
                ? 'Student class record activated'
                : 'Student class record deactivated';

            return response()->json([
                'status' => 'success',
                'message' => $message,
                'record' => $record
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update record',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
