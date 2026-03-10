<?php

namespace App\Services;

use App\Jobs\SendPaymentSms;
use App\Models\ClassAttendance;
use App\Models\Student;
use App\Models\StudentStudentStudentClass;
use App\Models\ClassCategoryHasStudentClass;
use App\Models\Payments;
use App\Models\StudentAttendances;
use App\Models\Titute;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Exception;

class StudentAttendanceService
{
    public function readAttendance(Request $request)
    {
        $request->validate([
            'qr_code' => 'required|string',
        ]);

        try {
            $qrCode = $request->qr_code;
            $now = Carbon::now();


            // 1️⃣ Temporary QR (starts with TMP)
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
            } else {
                // 2️⃣ Permanent QR (custom_id)
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

            // 3️⃣ Student inactive check
            if ($student->is_active == 0) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Student is inactive'
                ], 403);
            }

            // 4️⃣ Fetch attendance details
            $result = $this->getStudentClassesDetails($student->id);

            return response()->json([
                'status' => 'success',
                'student_id' => $student->id,
                'data' => $result
            ], 200);
        } catch (Exception $e) {

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch student attendance',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function getStudentClassesDetails($student_id)
    {

        if (!$student_id) {
            return [];
        }

        // 1) Student enrollments
        $enrollments = StudentStudentStudentClass::with('studentClass', 'student')
            ->where('student_id', $student_id)
            ->get();

        $studentClassIds = $enrollments->pluck('studentClass.id')->unique();
        if ($studentClassIds->isEmpty()) {
            return [];
        }

        // 2) Fetch categories for enrolled classes
        $allCategories = ClassCategoryHasStudentClass::with('classCategory')
            ->whereIn('student_classes_id', $studentClassIds)
            ->get();

        $categoryIds = $allCategories->pluck('id')->unique();
        if ($categoryIds->isEmpty()) {
            return [];
        }

        $today = Carbon::today()->toDateString();
        $now = Carbon::now();

        // 3) Fetch today's classes
        $todaysClasses = ClassAttendance::with('hall')
            ->whereIn('class_category_has_student_class_id', $categoryIds)
            ->whereDate('date', $today)
            ->get();

        // 4) Get student payment and attendance information
        $lastPaymentRecord = Payments::where('student_id', $student_id)->latest()->first();

        // Check if tute exists for this month (format: Y-m)
        $thisMonthAlreadyTute = Titute::where('student_id', $student_id)
            ->whereIn('class_category_has_student_class_id', $categoryIds)
            ->where('titute_for', $now->format('M Y'))
            ->exists();

        // Get all attendance IDs from today's classes for counting
        $todaysClasses->pluck('id')->toArray();

        // Count attendance for this month for ALL classes (or specific if needed)
        $attendanceCountThisMonth = StudentAttendances::where('student_id', $student_id)
            ->whereBetween('at_date', [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()])
            ->count();

        $result = [];

        foreach ($enrollments as $enrollment) {
            $studentClass = $enrollment->studentClass;
            if (!$studentClass) {
                continue;
            }

            $categories = $allCategories->where('student_classes_id', $studentClass->id);


            foreach ($categories as $cat) {
                $todaysClass = $todaysClasses->first(
                    fn($c) => $c->class_category_has_student_class_id == $cat->id
                );

                if (!$todaysClass) {
                    continue;
                }

                // FIX: parse UTC timestamp to date only for Y-m-d
                $cleanDate = Carbon::parse($todaysClass->date)->format('Y-m-d');

                $start = $this->parseDateTime($cleanDate, $todaysClass->start_time);
                $end   = $this->parseDateTime($cleanDate, $todaysClass->end_time);

                if (!$start || !$end) {
                    continue;
                }

                $oneHourBefore = $start->copy()->subHour();

                // Check if current time is inside attendance window (1 hour before → end)
                if (!$now->between($oneHourBefore, $end)) {
                    continue;
                }

                // Count attendance for THIS SPECIFIC class for this month
                $attendanceCountForThisClass = StudentAttendances::where('student_id', $student_id)
                    ->where('attendance_id', $todaysClass->id)  // For this specific class
                    ->whereBetween('at_date', [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()])
                    ->count();

                // Add to result
                $result[] = [
                    'category_name' => $cat->classCategory->category_name ?? 'N/A',
                    'student_class_name' => $studentClass->class_name ?? 'N/A',
                    'studentStudentStudentClass' => [
                        'student_student_student_class_id' => $enrollment->id,
                        'student_class_status' => $enrollment->status,
                    ],
                    'student' => [
                        'id' => $student_id,
                        'img_url' => $enrollment->student->img_url ?? null,
                        'custom_id' => $enrollment->student->custom_id,
                        'first_name' => $enrollment->student->full_name,
                        'last_name' => $enrollment->student->initial_name,
                        'guardian_mobile' => $enrollment->student->guardian_mobile
                    ],
                    'ongoing_class' => [
                        'id' => $todaysClass->id,
                        'class_category_has_student_class_id' => $todaysClass->class_category_has_student_class_id,
                        'start_time' => $todaysClass->start_time,
                        'end_time' => $todaysClass->end_time,
                        'class_hall_id' => $todaysClass->class_hall_id,
                        'class_hall_name' => $todaysClass->hall->hall_name ?? null,
                        'class_hall_price' => $todaysClass->hall->hall_price ?? null,
                        'date' => Carbon::parse($todaysClass->date)->format('Y-m-d'),
                        'is_ongoing' => $todaysClass->is_ongoing,
                        'current_time' => $now->format('h:i A'),
                    ],
                    // Add payment and attendance information
                    'payment_info' => $lastPaymentRecord ? [
                        'last_payment_date' => $lastPaymentRecord->created_at->format('Y-m-d'),
                        'payment_for' => $lastPaymentRecord->payment_for,
                        'last_payment_amount' => $lastPaymentRecord->amount,
                        'payment_status' => $lastPaymentRecord->status,
                    ] : null,
                    'tute_info' => [
                        'has_tute_for_this_month' => $thisMonthAlreadyTute,
                        'current_month' => $now->format('F Y'),
                    ],
                    'attendance_info' => [
                        'attendance_count_this_month_total' => $attendanceCountThisMonth,
                        'attendance_count_for_this_class' => $attendanceCountForThisClass,
                        'current_month' => $now->format('F Y'),
                    ]
                ];
            }
        }

        return $result;
    }

    private function parseDateTime(string $date, string $timeString): ?Carbon
    {
        $time = trim($timeString);

        // Parse formats like: "2 PM", "2:00 PM", "14:00", "2.00 PM"
        $formats = ['h:i A', 'h A', 'H:i', 'h.i A'];

        foreach ($formats as $f) {
            try {
                return Carbon::createFromFormat('Y-m-d ' . $f, $date . ' ' . $time);
            } catch (Exception $e) {
                // try next format
            }
        }

        return null;
    }

    // ================================
    // STORE ATTENDANCE
    // ================================
    public function storeAttendance(Request $request)
    {
        try {
            $request->validate([
                'student_id' => 'required|integer',
                'student_student_student_classes_id' => 'required|integer',
                'attendance_id' => 'required|integer',
                'tute' => 'required|boolean',
                'class_category_has_student_class_id' => 'integer',
                'guardian_mobile' => 'required|string',
            ]);

            $date = now()->toDateString();
            $studentId = $request->student_id;
            $studentClassId = $request->student_student_student_classes_id;
            $attendanceId = $request->attendance_id;

            // Check duplicate attendance
            $exists = StudentAttendances::whereDate('at_date', $date)
                ->where('student_id', $studentId)
                ->where('student_student_student_classes_id', $studentClassId)
                ->where('attendance_id', $attendanceId)
                ->exists();

            if ($exists) {
                return response()->json([
                    'status' => 'duplicate',
                    'message' => 'Attendance already marked for today',
                    'attendance_marked' => false,
                    'tute_marked' => false,
                ], 409);
            }

            // Mark attendance
            StudentAttendances::create([
                'at_date' => $date,
                'student_student_student_classes_id' => $studentClassId,
                'student_id' => $studentId,
                'attendance_id' => $attendanceId,
            ]);

            $attendanceMarked = true;
            $tuteMarked = false;

            if ($attendanceMarked) {
                $this->classAttendanceStatusUpdate($attendanceId);
            }

            // Mark tute if true
            if ($request->tute === true && $request->class_category_has_student_class_id) {
                $month = now()->startOfMonth();

                $tuteExists = Titute::where('student_id', $studentId)
                    ->where('class_category_has_student_class_id', $request->class_category_has_student_class_id)
                    ->whereMonth('created_at', $month->month)
                    ->whereYear('created_at', $month->year)
                    ->exists();

                if (!$tuteExists) {
                    $tute = Titute::create([
                        'student_id' => $studentId,
                        'class_category_has_student_class_id' => $request->class_category_has_student_class_id,
                        'titute_for' => $month->format('M Y'),
                        'status' => 1,
                        'created_at' => $month,
                    ]);

                    $tuteMarked = true;
                }
            }

            // ✅ SMS sending via Job queue
            $childInfo = StudentStudentStudentClass::with([
                'student',
                'studentClass',
                'classCategoryHasStudentClass.classCategory'
            ])->where('id', $studentClassId)->first();

            if ($childInfo) {
                $guardianNumber = $request->guardian_mobile;
                $studentName = $childInfo->student->initial_name ?? '';
                $className = $childInfo->studentClass->class_name ?? '';
                $categoryName = optional($childInfo->classCategoryHasStudentClass->classCategory)->category_name ?? '';

                $message = "Attendance marked for {$studentName} ({$className} - {$categoryName}) on {$date}. Thank you.";

                // Dispatch SMS job async
                SendPaymentSms::dispatch($guardianNumber, $message)->onQueue('sms');
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Attendance marked successfully',
                'attendance_marked' => $attendanceMarked,
                'tute_marked' => $tuteMarked,
            ]);
        } catch (Exception $e) {

            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong while saving attendance',
                'error' => $e->getMessage(),
            ], 500);
        }
    }



    /**
     * Update the class attendance status after student attendance is added
     */
    private function classAttendanceStatusUpdate($class_attendance_id)
    {
        if ($class_attendance_id) {
            $classAttendance = ClassAttendance::find($class_attendance_id);
            if ($classAttendance) {
                // Example: mark as ongoing (1) if not already
                $classAttendance->status = "1";
                $classAttendance->save();
            }
        }
    }

    // ================================
    // Frtch All ATTENDANCE
    // ================================
    public function getAllAttendances(Request $request)
    {
        $request->validate([
            'student_id' => 'required|integer',
            'student_student_student_classes_id' => 'required|integer'
        ]);

        $student_id = $request->student_id;
        $student_student_student_classes_id = $request->student_student_student_classes_id;

        try {
            $records = StudentAttendances::with([
                'student',
                'studentStudentClass.studentClass' // use camelCase function name
            ])
                ->where('student_id', $student_id)
                ->where('student_student_student_classes_id', $student_student_student_classes_id)
                ->orderBy('at_date', 'asc')
                ->get();



            return response()->json([
                'status' => 'success',
                'total_records' => $records->count(),
                'data' => $records
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch attendance records.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getStudentAttendances($studentId, $classCategoryHasStudentClassId)
    {
        try {
            // Step 1: Get the class record
            $studentClass = StudentStudentStudentClass::where('student_id', $studentId)
                ->where('class_category_has_student_class_id', $classCategoryHasStudentClassId)
                ->firstOrFail();

            // Step 2: Get all active ongoing attendance records for this class
            $classAttendances = ClassAttendance::where('class_category_has_student_class_id', $classCategoryHasStudentClassId)
                ->where('status', 1)
                ->where('is_ongoing', 1)
                ->orderBy('date', 'asc')
                ->get();

            $attendanceData = [];
            $presentCount = 0;

            foreach ($classAttendances as $attendance) {
                $studentAttendance = StudentAttendances::where('student_id', $studentId)
                    ->where('student_student_student_classes_id', $studentClass->id)
                    ->where('attendance_id', $attendance->id)
                    ->first();

                $status = $studentAttendance ? 'Present' : 'Absent';

                if ($status === 'Present') {
                    $presentCount++;
                }

                $attendanceData[] = [
                    'date' => $attendance->date,
                    'status' => $status,
                ];
            }

            $totalClasses = count($classAttendances);
            $percentage = $totalClasses > 0 ? round(($presentCount / $totalClasses) * 100, 2) : 0;

            return response()->json([
                'status' => 'success',
                'total_records' => $totalClasses,
                'present_count' => $presentCount,
                'attendance_percentage' => $percentage,
                'data' => $attendanceData
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch attendance records.',
                'error' => $e->getMessage()
            ], 500);
        }
    }



    // ================================
    // UPDATE ATTENDANCE
    // ================================
    public function updateAttendance(Request $request, $id)
    {
        try {

            // Validate input
            $request->validate([
                'student_id' => 'required|integer',
                'student_student_student_classes_id' => 'required|integer',
                'status' => 'required|integer' // status = class_attendance id
            ]);

            $attendance = StudentAttendances::find($id);

            if (!$attendance) {
                return response()->json([
                    'status' => 'not_found',
                    'message' => 'Attendance record not found.'
                ], 404);
            }

            $date = $attendance->at_date;

            // Check duplicate except current record
            $duplicate = StudentAttendances::whereDate('at_date', $date)
                ->where('student_id', $request->student_id)
                ->where('student_student_student_classes_id', $request->student_student_student_classes_id)
                ->where('status', $request->status)
                ->where('id', '!=', $id)
                ->exists();

            if ($duplicate) {
                return response()->json([
                    'status' => 'duplicate',
                    'message' => 'Another attendance record already exists for this student in this class with the same status on this date.'
                ], 409);
            }

            // Update record
            $attendance->update([
                'student_id' => $request->student_id,
                'student_student_student_classes_id' => $request->student_student_student_classes_id,
                'status' => $request->status
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Attendance updated successfully!',
                'data' => $attendance
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong while updating attendance. Please try again.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function monthStudentAttendanceCount($student_id, $student_class_id, $yearMonth)
    {
        try {
            [$year, $month] = explode('-', $yearMonth);

            $start = Carbon::create($year, $month, 1)->startOfMonth();
            $end   = Carbon::create($year, $month, 1)->endOfMonth();

            // Attendance Count
            $count = StudentAttendances::where('student_id', $student_id)
                ->where('student_student_student_classes_id', $student_class_id)
                ->whereBetween('at_date', [$start, $end])
                ->count();

            // Number of days in this month
            $daysInMonth = $start->daysInMonth;

            // Calculate number of weeks in this month
            $weeksInMonth = (int) ceil($daysInMonth / 7);

            return response()->json([
                'status' => 'success',
                'message' => 'Monthly attendance count fetched successfully',
                'data' => [
                    'student_id' => $student_id,
                    'student_class_id' => $student_class_id,
                    'year_month' => $yearMonth,
                    'attendance_count' => $count,
                    'weeks_in_month' => $weeksInMonth
                ]
            ], 200);
        } catch (Exception $e) {

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch monthly attendance count',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function studentAttendClass($class_id, $attendance_id, $class_category_student_class_id)
    {
        try {
            if (is_null($class_id) || is_null($attendance_id) || is_null($class_category_student_class_id)) {
                return response()->json([
                    'status'  => false,
                    'message' => 'One or more required parameters are missing.',
                ], 400);
            }

            // Process category mapping and student grouping
            $processedData = $this->processStudentCategories($class_id);

            // Find the requested category group
            $matchedGroup = collect($processedData['grouped_results'])
                ->firstWhere('class_category_has_student_class_id', $class_category_student_class_id);

            if (!$matchedGroup) {
                return response()->json([
                    'status' => false,
                    'message' => 'No matching category group found.',
                    'requested_id' => $class_category_student_class_id,
                    'available_ids' => collect($processedData['grouped_results'])
                        ->pluck('class_category_has_student_class_id')
                        ->toArray(),
                ], 404);
            }

            // Extract students belonging to that category
            $studentIds = collect($matchedGroup['students'])
                ->pluck('student_id')
                ->unique()
                ->values()
                ->toArray();

            $today = now()->toDateString();

            // Today's attendance records for these students - matching the requested status
            $attendanceRecords = StudentAttendances::with('student')
                ->whereIn('student_id', $studentIds)
                ->whereDate('at_date', $today)
                ->where('attendance_id', $attendance_id)
                ->get()
                ->keyBy('student_id');

            // Build attendance list with default ABSENT
            $attendanceList = [];

            foreach ($studentIds as $studentId) {
                if (isset($attendanceRecords[$studentId])) {
                    $record = $attendanceRecords[$studentId];
                    $attendanceList[] = [
                        'student_id' => $studentId,
                        'attendance_status' => 'present',
                        'status' => $record->status,
                        'attendance_id' => $record->id,
                        'attendance_date' => $record->at_date,
                        'is_present' => true,
                    ];
                } else {
                    $attendanceList[] = [
                        'student_id' => $studentId,
                        'attendance_status' => 'absent',
                        'status' => null,
                        'attendance_id' => null,
                        'attendance_date' => $today,
                        'is_present' => false,
                    ];
                }
            }

            // Append student details
            $students = Student::whereIn('id', array_column($attendanceList, 'student_id'))
                ->get(['id', 'custom_id', 'full_name', 'initial_name', 'guardian_mobile'])
                ->keyBy('id');

            $finalResult = [];

            foreach ($attendanceList as $entry) {
                $student = $students[$entry['student_id']] ?? null;

                $finalResult[] = [
                    'student_id' => $entry['student_id'],
                    'custom_id' => $student->custom_id ?? null,
                    'fname' => $student->full_name ?? null,
                    'lname' => $student->initial_name ?? null,
                    'guardian_mobile' => $student->guardian_mobile ?? null,
                    'attendance_status' => $entry['attendance_status'],
                    'status' => $entry['status'],
                    'attendance_id' => $entry['attendance_id'],
                    'attendance_date' => $entry['attendance_date'],
                    'is_present' => $entry['is_present'],
                ];
            }

            // Counts
            $presentCount = count(array_filter($finalResult, fn($x) => $x['is_present']));
            $absentCount = count(array_filter($finalResult, fn($x) => !$x['is_present']));

            return response()->json([
                'status' => true,
                'data' => [
                    'matched_group' => [
                        'class_category_has_student_class_id' => $matchedGroup['class_category_has_student_class_id'],
                        'category_name' => $matchedGroup['category_name'],
                    ],
                    'attendance_list' => $finalResult,
                    'summary' => [
                        'date' => $today,
                        'requested_status' => $attendance_id,
                        'total_students' => count($studentIds),
                        'present' => $presentCount,
                        'absent' => $absentCount,
                        'attendance_percentage' =>
                        count($studentIds) > 0 ? round(($presentCount / count($studentIds)) * 100, 2) : 0,
                    ],
                ],
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'An unexpected error occurred while processing the request.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }



    private function processStudentCategories($class_id)
    {
        // 1. Get all students in the class and their categories
        $students = StudentStudentStudentClass::with([
            'classCategoryHasStudentClass',
            'classCategoryHasStudentClass.classCategory'
        ])
            ->where('student_classes_id', $class_id)
            ->where('status', 1)
            ->get()
            ->map(function ($item) {
                return [
                    'class_category_has_student_class_id' => $item->class_category_has_student_class_id,
                    'category_name' => $item->classCategoryHasStudentClass->classCategory->category_name ?? null,
                    'student_id' => $item->student_id,
                ];
            });

        // 2. Extract unique category names
        $studentCategories = $students->pluck('category_name')->unique()->filter()->toArray();

        // 3. Split combined categories such as "Theory+Revision"
        $individualCategories = [];

        foreach ($studentCategories as $category) {
            if (strpos($category, '+') !== false) {
                foreach (explode('+', $category) as $part) {
                    $part = trim($part);
                    if ($part !== '') {
                        $individualCategories[] = $part;
                    }
                }
            } else {
                $individualCategories[] = $category;
            }
        }

        $individualCategories = array_unique($individualCategories);

        // 4. Get single (non-combined) categories from DB
        $classCategories = ClassCategoryHasStudentClass::with('classCategory')
            ->where('student_classes_id', $class_id)
            ->get()
            ->filter(fn($item) => strpos($item->classCategory->category_name ?? '', '+') === false)
            ->map(function ($item) {
                return [
                    'class_category_has_student_class_id' => $item->id,
                    'category_name' => $item->classCategory->category_name ?? null,
                ];
            });

        // 5. Match student category names with real category IDs
        $matchedCategories = [];

        foreach ($individualCategories as $studentCat) {
            foreach ($classCategories as $classCat) {
                if ($studentCat === $classCat['category_name']) {
                    $matchedCategories[] = [
                        'student_category' => $studentCat,
                        'class_category_has_student_class_id' => $classCat['class_category_has_student_class_id'],
                        'category_name' => $classCat['category_name'],
                    ];
                    break;
                }
            }
        }

        // 6. Group students by matched category
        $groupedResults = [];

        foreach ($matchedCategories as $matched) {
            $categoryId = $matched['class_category_has_student_class_id'];

            $categoryStudents = $students->filter(function ($student) use ($matched, $categoryId) {
                $catName = $student['category_name'];

                if (strpos($catName, '+') !== false) {
                    $parts = array_map('trim', explode('+', $catName));
                    return in_array($matched['student_category'], $parts);
                }

                return $student['class_category_has_student_class_id'] == $categoryId;
            })->values();

            $groupedResults[] = [
                'class_category_has_student_class_id' => $categoryId,
                'category_name' => $matched['category_name'],
                'students' => $categoryStudents,
                'student_count' => $categoryStudents->count(),
            ];
        }

        return [
            'all_students' => $students,
            'student_categories' => $studentCategories,
            'individual_student_categories' => array_values($individualCategories),
            'class_categories_without_combined' => $classCategories,
            'matched_categories' => $matchedCategories,
            'grouped_results' => $groupedResults,
        ];
    }

    public function attendanceRecoadDelete($id)
    {
        try {
            // Find the attendance record
            $attendance = StudentAttendances::find($id);

            if (!$attendance) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Attendance record not found'
                ], 404);
            }

            // Delete the attendance record
            $attendance->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Attendance record deleted successfully'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete attendance record',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
