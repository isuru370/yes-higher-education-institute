<?php

namespace App\Services;

use App\Models\ClassAttendance;
use App\Models\Payments;
use App\Models\Student;
use App\Models\StudentAttendances;
use App\Models\StudentStudentStudentClass;
use Carbon\Carbon;
use Exception;
use App\Models\Grade;
use App\Models\QuickPhoto;
use App\Models\StudentPortalLogin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str; // Add this import

class StudentService
{
    public function fetchStudents()
    {
        try {
            $perPage = request()->get('per_page', 15);
            $search = request()->get('search', '');
            $isActive = request()->get('is_active'); // Active/Inactive filter
            $gradeId = request()->get('grade_id'); // Grade filter

            $studentsQuery = Student::with([
                'grade' => function ($query) {
                    $query->select('id', 'grade_name');
                },
            ]);

            // Add search functionality if search parameter is provided
            if (!empty($search)) {
                $studentsQuery->where(function ($query) use ($search) {
                    $query->where('full_name', 'like', "%{$search}%")
                        ->orWhere('initial_name', 'like', "%{$search}%")
                        ->orWhere('custom_id', 'like', "%{$search}%")
                        ->orWhere('mobile', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhereHas('grade', function ($gradeQuery) use ($search) {
                            $gradeQuery->where('grade_name', 'like', "%{$search}%");
                        });
                });
            }

            // Add Active/Inactive filter
            if ($isActive !== null && $isActive !== '') {
                $studentsQuery->where('is_active', $isActive);
            }

            // Add Grade filter
            if ($gradeId && $gradeId !== '') {
                $studentsQuery->where('grade_id', $gradeId);
            }

            $students = $studentsQuery->orderBy('id', 'desc')->paginate($perPage);

            return response()->json([
                'status' => 'success',
                'data' => [
                    'students' => $students->items(),
                    'pagination' => [
                        'current_page' => $students->currentPage(),
                        'last_page' => $students->lastPage(),
                        'per_page' => $students->perPage(),
                        'total' => $students->total(),
                        'from' => $students->firstItem(),
                        'to' => $students->lastItem(),
                    ]
                ]
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch students',
                'error' => $e->getMessage()
            ], 500);
        }
    }



    // Optional: Get only active student
    public function fetchActiveStudents()
    {
        try {
            $teachers = Student::with([
                'grade' => function ($query) {
                    $query->select('id', 'grade_name');
                },
            ])->where('is_active', 1)
                ->orderBy('id', 'desc')
                ->get();

            return response()->json([
                'status' => 'success',
                'data' => $teachers
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch active teachers',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function fetchTempQrCode()
    {
        try {
            $students = Student::where('is_active', 1)
                ->where('student_disable', 0)
                ->orderBy('id', 'desc')
                ->get()
                ->map(function ($item) {

                    // original creation date
                    $updatedAt = $item->updated_at;

                    // move forward 2 months
                    $futureDate = $updatedAt ? $updatedAt->copy()->addMonths(2) : null;

                    // calculate days left from today to futureDate
                    $daysLeft = $futureDate ? now()->diffInDays($futureDate) : null;

                    return [
                        'student_id' => $item->id,
                        'custom_id' => $item->custom_id,
                        'full_name' => $item->full_name,
                        'initial_name' => $item->initial_name,
                        'mobile' => $item->mobile,
                        'whatsapp_mobile' => $item->whatsapp_mobile,
                        'update_at' => $updatedAt->toDateTimeString(),
                        'days_left' => $daysLeft,
                        'is_active' => $item->is_active,
                        'status' => $item->status ?? 1,
                    ];
                });

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


    public function fetchNotPaidAdmissionStudent()
    {
        try {
            $students = Student::select([
                'id',
                'custom_id',
                'full_name',
                'initial_name',
                'mobile',
                'whatsapp_mobile',
                'img_url',
                'guardian_mobile',
                'grade_id'
            ])
                ->with([
                    'grade:id,grade_name'
                ])
                ->where('is_active', 1)
                ->orderBy('id', 'desc')
                ->get();

            return response()->json([
                'status' => 'success',
                'data' => $students
            ]);
        } catch (Exception $e) {

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch active students',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    // API: fetch single student
    public function fetchStudent($id)
    {
        try {
            $student = Student::with([
                'grade' => function ($query) {
                    $query->select('id', 'grade_name');
                },
                'portalLogin' => function ($query) {
                    $query->select('id', 'student_id', 'username', 'is_verify', 'is_active');
                },
            ])->find($id);

            if (!$student) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Student not found'
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'data' => $student
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch student details',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function fetchActiveStudentsLimitPage(Request $request, $returnJson = false)
    {
        try {
            $perPage = $request->input('per_page', 20);
            $page = $request->input('page', 1);
            $search = $request->input('search', '');

            $query = Student::with(['grade:id,grade_name'])
                ->where('is_active', 1);

            // Search
            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('custom_id', 'like', "%{$search}%")
                        ->orWhere('full_name', 'like', "%{$search}%")
                        ->orWhere('initial_name', 'like', "%{$search}%")
                        ->orWhereRaw("CONCAT(full_name,' ',initial_name) LIKE ?", ["%{$search}%"]);
                });
            }

            $students = $query->orderByDesc('id')->paginate($perPage, ['*'], 'page', $page);

            // Transform image URLs
            $students->getCollection()->transform(function ($student) {
                if (
                    !empty($student->img_url) &&
                    !Str::startsWith($student->img_url, ['http://', 'https://', '//'])
                ) {
                    $student->img_url = Str::startsWith($student->img_url, 'uploads/')
                        ? asset($student->img_url)
                        : asset('uploads/' . $student->img_url);
                }
                return $student;
            });

            if ($returnJson) {
                return response()->json([
                    'status' => 'success',
                    'data' => $students->items(),
                    'pagination' => [
                        'current_page' => $students->currentPage(),
                        'last_page' => $students->lastPage(),
                        'per_page' => $students->perPage(),
                        'total' => $students->total(),
                        'next_page_url' => $students->nextPageUrl(),
                        'prev_page_url' => $students->previousPageUrl(),
                        'has_more_pages' => $students->hasMorePages(),
                    ]
                ]);
            }

            return $students; // Return paginator object for web

        } catch (Exception $e) {
            if ($returnJson) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to fetch active students',
                    'error' => $e->getMessage()
                ], 500);
            }
            throw $e; // For web, throw exception
        }
    }

    public function filterByCreatedDate(Request $request)
    {
        try {

            if ($request->has('date')) {
                $request->validate([
                    'date' => 'required|date',
                ]);

                $students = Student::with('grade:id,grade_name')
                    ->whereDate('created_at', $request->date)
                    ->get();
            } elseif ($request->has('from') && $request->has('to')) {
                $request->validate([
                    'from' => 'required|date',
                    'to'   => 'required|date|after_or_equal:from',
                ]);

                $students = Student::with('grade:id,grade_name')
                    ->whereBetween('created_at', [$request->from, $request->to])
                    ->get();
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Please provide either a "date" or a "from" and "to" date range.'
                ], 400);
            }

            return response()->json([
                'status' => 'success',
                'count' => $students->count(),
                'data' => $students
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch filtered students',
                'error' => $e->getMessage(),
            ], 500);
        }
    }




    //API: custome Id search
    public function fetchStudentByQRCode(Request $request)
    {
        $request->validate([
            'qr_code' => 'required|string',
        ]);

        try {
            $qrCode = $request->qr_code;
            $now = Carbon::now();

            // 1️⃣ Temporary QR (starts with TMP)
            if (str_starts_with($qrCode, 'TMP')) {
                $student = Student::with([
                    'grade:id,grade_name',
                    'portalLogin:id,student_id,username,is_verify,is_active'
                ])
                    ->where('temporary_qr_code', $qrCode)
                    ->where('student_disable', false)
                    ->first();

                if (!$student) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Temporary QR code invalid'
                    ], 404);
                }

                // Expire check
                if ($student->temporary_qr_code_expire_date && $now->gt($student->temporary_qr_code_expire_date)) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Temporary QR code has expired'
                    ], 403);
                }
            } else {
                // 2️⃣ Permanent QR (custom_id)
                $student = Student::with([
                    'grade:id,grade_name',
                    'portalLogin:id,student_id,username,is_verify,is_active'
                ])
                    ->where('custom_id', $qrCode)
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

            // Check student is active
            if ($student->is_active == 0) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Student is inactive'
                ], 403);
            }

            $studentData = $student->toArray();

            // Portal access info
            $hasPortalAccess = false;
            $portalUsername = null;
            if ($student->portalLogin) {
                $portalLogin = $student->portalLogin;
                $hasPortalAccess = $portalLogin->is_verify && $portalLogin->is_active;
                $portalUsername = $portalLogin->username;
            }

            $studentData['has_portal_access'] = $hasPortalAccess;
            $studentData['portal_username'] = $portalUsername;
            if ($student->portalLogin) {
                $studentData['portal_is_verify'] = (bool) $student->portalLogin->is_verify;
                $studentData['portal_is_active'] = (bool) $student->portalLogin->is_active;
            }

            return response()->json([
                'status' => 'success',
                'data' => $studentData
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch student details',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            // Handle quick photo
            if ($request->quick_image_id) {
                $quickPhoto = QuickPhoto::where('id', $request->quick_image_id)
                    ->where('is_active', 1)
                    ->first();

                if (!$quickPhoto) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Quick photo not found'
                    ], 404);
                }

                $quickPhoto->update(['is_active' => 0]);
            }

            // Validate input (without temporary_qr_code_expire_date, we'll auto-set it)
            $validator = Validator::make($request->all(), [
                'temporary_qr_code' => 'required|string|max:255',
                'full_name' => 'required|string|max:255',
                'initial_name' => 'required|string|max:255',
                'email' => ['nullable', 'email', Rule::unique('students', 'email')],
                'mobile' => 'required|string|max:15',
                'whatsapp_mobile' => 'required|string|max:15',
                'nic' => ['nullable', 'string', 'max:20', Rule::unique('students', 'nic')],
                'bday' => 'nullable|date',
                'gender' => 'required|in:male,female,other',
                'address1' => 'required|string|max:255',
                'address2' => 'required|string|max:255',
                'address3' => 'nullable|string|max:255',
                'guardian_fname' => 'required|string|max:255',
                'guardian_lname' => 'required|string|max:255',
                'guardian_nic' => 'nullable|string|max:20',
                'guardian_mobile' => 'nullable|string|max:15',
                'is_active' => 'nullable|boolean',
                'img_url' => 'required|string|max:255',
                'grade_id' => ['required', 'exists:grades,id'],
                'class_type' => 'required|in:online,offline',
                'admission' => 'nullable|boolean',
                'student_school' => 'nullable|string|max:255'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $data = $validator->validated();

            // Generate custom_id
            $data['custom_id'] = $this->generateCustomId($data['grade_id']);

            // Auto-set temporary QR code expiration to 2 months from today
            $data['temporary_qr_code_expire_date'] = Carbon::now()->addMonths(2);

            // Ensure boolean fields are properly cast
            $data['is_active'] = boolval($data['is_active'] ?? true);
            $data['admission'] = boolval($data['admission'] ?? false);

            // Create student
            $student = Student::create($data);

            // Create portal login
            $this->createStudentPortalLogin($student);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Student created successfully',
                'data' => $student
            ]);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create student',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // API: public student register

    public function publicStudentRegister(Request $request)
    {
        return $this->store($request);
    }

    // API: update student
    public function update(Request $request, $student_id)
    {
        DB::beginTransaction();

        try {
            // 1️⃣ Find student
            $student = Student::where('id', $student_id)->first();
            if (!$student) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Student not found'
                ], 404);
            }

            // 2️⃣ Validation
            $validator = Validator::make($request->all(), [
                'temporary_qr_code' => 'nullable|string|max:255',
                'full_name'         => 'required|string|max:255',
                'initial_name'      => 'required|string|max:255',
                'email'             => ['nullable', 'email', Rule::unique('students', 'email')->ignore($student->id)],
                'mobile'            => 'required|string|max:15',
                'whatsapp_mobile'   => 'required|string|max:15',
                'nic'               => ['nullable', 'string', 'max:20', Rule::unique('students', 'nic')->ignore($student->id)],
                'bday'              => 'nullable|string',
                'gender'            => 'required|in:male,female,other',
                'address1'          => 'required|string|max:255',
                'address2'          => 'required|string|max:255',
                'address3'          => 'nullable|string|max:255',
                'guardian_fname'    => 'required|string|max:255',
                'guardian_lname'    => 'required|string|max:255',
                'guardian_nic'      => 'nullable|string|max:20',
                'guardian_mobile'   => 'nullable|string|max:15',
                'is_active'         => 'nullable|boolean',
                'img_url'           => 'required|string|max:255',
                'grade_id'          => ['required', 'exists:grades,id'],
                'class_type'        => 'required|in:online,offline',
                'admission'         => 'nullable|boolean',
                'student_school'    => 'nullable|string|max:255'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $data = $validator->validated();

            // 3️⃣ Multi-format birthday parsing
            if (!empty($data['bday'])) {
                $possibleFormats = ['Y-m-d', 'd/m/Y', 'd-m-Y', 'm-d-Y', 'm/d/Y'];
                $parsed = null;
                foreach ($possibleFormats as $format) {
                    $try = \DateTime::createFromFormat($format, $data['bday']);
                    if ($try && $try->format($format) === $data['bday']) {
                        $parsed = $try->format('Y-m-d');
                        break;
                    }
                }
                if (!$parsed) {
                    try {
                        $parsed = Carbon::parse($data['bday'])->format('Y-m-d');
                    } catch (Exception $e) {
                        return response()->json([
                            'status' => 'error',
                            'message' => 'Invalid date format',
                            'formats_allowed' => $possibleFormats
                        ], 422);
                    }
                }
                $data['bday'] = $parsed;
            }

            // 4️⃣ Boolean casting
            $data['is_active'] = isset($data['is_active']) ? boolval($data['is_active']) : $student->is_active;
            $data['admission']  = isset($data['admission']) ? boolval($data['admission']) : $student->admission;

            // 5️⃣ Temporary QR expiration update (optional: reset if provided)
            if (isset($data['temporary_qr_code'])) {
                $data['temporary_qr_code_expire_date'] = Carbon::now()->addMonths(2);
            }

            // 6️⃣ Update student
            $student->update($data);

            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'Student updated successfully',
                'data'    => $student
            ]);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to update student',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    //API : student image update
    public function updateStudentImage(Request $request, $custom_id)
    {
        DB::beginTransaction();
        try {
            // ✔ Find by custom_id
            $student = Student::where('custom_id', $custom_id)->first();
            if (!$student) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Student not found'
                ], 404);
            }
            // -------------------------
            // VALIDATION
            // -------------------------
            $validated = $request->validate([
                'img_url' => 'required|string|max:255',
            ]);
            // -------------------------
            // UPDATE IMAGE
            // -------------------------
            $student->update([
                'img_url' => $validated['img_url']
            ]);

            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'Student image updated successfully',
                'data'    => $student
            ]);
        } catch (Exception $e) {

            DB::rollBack();

            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to update student image',
                'error'   => $e->getMessage()
            ], 500);
        }
    }



    // API: deactivate student
    public function destroy($id)
    {
        DB::beginTransaction();
        try {

            $student = Student::find($id);

            if (!$student) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Student not found'
                ], 404);
            }


            $student->update(['is_active' => 0]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Student deactivated successfully',
                'student' => [
                    'id' => $student->id,
                    'is_active' => $student->is_active
                ]
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to deactivate student',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    // API: reactivate student
    public function reactivate($id)
    {
        DB::beginTransaction();
        try {
            $student = Student::find($id);

            if (!$student) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Student not found'
                ], 404);
            }

            $student->update(['is_active' => 1]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Student reactivated successfully',
                'data' => $student
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to reactivate student',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Generate custom ID
    private function generateCustomId($gradeId)
    {
        try {
            $grade = Grade::find($gradeId);
            if (!$grade) {
                throw new Exception("Invalid Grade ID.");
            }

            $gradeCode = '';
            $gradeName = trim($grade->grade_name);

            // Pattern 1: Grade X (e.g., Grade 9, Grade 10)
            if (preg_match('/^Grade\s+(\d+)$/i', $gradeName, $matches)) {
                $gradeCode = str_pad($matches[1], 2, '0', STR_PAD_LEFT);
            }
            // Pattern 2: XXXX A/L (e.g., 2027 A/L, 2025 A/L)
            elseif (preg_match('/^(\d{4})\s+A\/L$/i', $gradeName, $matches)) {
                $gradeCode = substr($matches[1], -2);
            }
            // Pattern 3: XXXX O/L (e.g., 2027 O/L, 2025 O/L)
            elseif (preg_match('/^(\d{4})\s+O\/L$/i', $gradeName, $matches)) {
                $gradeCode = substr($matches[1], -2);
            }
            // Pattern 4: Any 4-digit number (assume year)
            elseif (preg_match('/^\d{4}$/', $gradeName, $matches)) {
                $gradeCode = substr($gradeName, -2);
            }
            // Pattern 5: Any other number in the name
            elseif (preg_match('/(\d+)/', $gradeName, $matches)) {
                $num = $matches[1];
                // If number is 4 digits, take last 2 (assuming year)
                if (strlen($num) == 4) {
                    $gradeCode = substr($num, -2);
                } else {
                    $gradeCode = str_pad($num, 2, '0', STR_PAD_LEFT);
                }
            }
            // Pattern 6: Fallback to grade ID
            else {
                $gradeCode = str_pad($gradeId, 2, '0', STR_PAD_LEFT);
            }

            // Rest of the function remains the same...
            $studentCount = Student::where('grade_id', $gradeId)->count();
            $nextNumber = $studentCount + 1;
            $sequenceNumber = str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
            $customId = "SA" . $gradeCode . $sequenceNumber;

            // Check for uniqueness
            $counter = 1;
            while (Student::where('custom_id', $customId)->exists()) {
                $nextNumber = $studentCount + $counter + 1;
                $sequenceNumber = str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
                $customId = "SA" . $gradeCode . $sequenceNumber;
                $counter++;

                if ($counter > 100) {
                    throw new Exception('Unable to generate unique custom ID after 100 attempts');
                }
            }

            return $customId;
        } catch (Exception $e) {
            throw new Exception('Failed to generate custom ID: ' . $e->getMessage());
        }
    }



    // check kirima sadaha me methoad eka use karanne
    public function generateCustomIdAPI(Request $request)
    {
        try {
            // Validate input
            $request->validate([
                'grade_id' => 'required|exists:grades,id'
            ]);

            $gradeId = $request->grade_id;

            // Find grade
            $grade = Grade::find($gradeId);

            if (!$grade) {
                throw new Exception("Invalid Grade ID.");
            }

            // Determine grade code
            if (is_numeric($grade->grade_name)) {
                $gradeCode = str_pad($grade->grade_name, 2, '0', STR_PAD_LEFT);
            } else {
                preg_match('/\d{4}/', $grade->grade_name, $matches);
                $year = $matches ? substr($matches[0], 2) : "00";
                $gradeCode = $year;
            }

            // Count students in this grade
            $studentCount = Student::where('grade_id', $gradeId)->count() + 1;

            // Generate the custom ID
            $customId = "SA" . $gradeCode . str_pad($studentCount, 3, '0', STR_PAD_LEFT);

            return response()->json([
                'status' => 'success',
                'custom_id' => $customId
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to generate custom ID',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function studentAnalytics($student_id)
    {
        if (!$student_id) {
            throw new \InvalidArgumentException("Student ID is required");
        }

        $today = Carbon::now();

        $studentClasses = StudentStudentStudentClass::with([
            'student',
            'student.grade',
            'classCategoryHasStudentClass',
            'classCategoryHasStudentClass.classCategory',
            'studentClass',
            'studentClass.teacher',
            'studentClass.subject',
            'studentClass.grade'
        ])
            ->where('student_id', $student_id)
            ->get()
            ->map(function ($item) use ($student_id, $today) {

                $classRelation = $item->classCategoryHasStudentClass;
                $studentClass = $item->studentClass;

                $classCategoryId = $item->class_category_has_student_class_id;

                // -------------------------
                // 1) PAYMENTS (only summary)
                // -------------------------
                $payments = $this->getPaymentsForClassEnrollment($student_id, $item->id);

                // -------------------------
                // 2) CLASS ATTENDANCE (no session_ids)
                // -------------------------
                $startDate = Carbon::parse($item->created_at)->startOfDay();
                $endDate = $today->endOfDay();

                $classAttendance = $this->getClassAttendance($classCategoryId, $startDate, $endDate);

                // -------------------------
                // 3) STUDENT ATTENDANCE
                // -------------------------
                $studentAttendance = $this->getStudentAttendance(
                    $classAttendance['ids'],
                    $student_id,
                    $classAttendance['count']
                );

                return  [
                    'enrollment_id'   => $item->id,
                    'enrollment_date' => $item->created_at->toDateTimeString(),
                    'status'          => $item->status,

                    // CLASS INFO
                    'class_info' => $studentClass ? [
                        'class_id'   => $studentClass->id,
                        'class_name' => $studentClass->class_name,
                        'teacher'    => $studentClass->teacher ? [
                            'custom_id'  => $studentClass->teacher->custom_id,
                            'full_name' => $studentClass->teacher->full_name,
                            'initial_name'  => $studentClass->teacher->initial_name
                        ] : null,
                        'subject' => $studentClass->subject ? [
                            'subject_name' => $studentClass->subject->subject_name
                        ] : null,
                        'grade' => $studentClass->grade ? [
                            'grade_name' => $studentClass->grade->grade_name
                        ] : null
                    ] : null,

                    // CATEGORY INFO
                    'category_info' => $classRelation ? [
                        'class_category_has_student_class_id'             => $classRelation->id,
                        'fees'          => $classRelation->fees,
                        'category_name' => $classRelation->classCategory->category_name ?? null
                    ] : null,

                    // PAYMENTS — ONLY SUMMARY
                    'payments' => [
                        'summary' => [
                            'payment_count' => $payments['count'],
                            'total_paid'    => $payments['total'],
                            'fees'          => $classRelation->fees ?? 0,
                            'is_fully_paid' => $payments['total'] >= ($classRelation->fees ?? 0)
                        ]
                    ],

                    // CLASS ATTENDANCE — NO session_ids
                    'class_attendance' => [
                        'total_sessions' => $classAttendance['count']
                    ],

                    // STUDENT ATTENDANCE
                    'student_attendance' => [
                        'present_count'   => $studentAttendance['present_count'],
                        'absent_count'    => $studentAttendance['absent_count'],
                        'attendance_rate' =>
                        $classAttendance['count'] > 0
                            ? round(($studentAttendance['present_count'] / $classAttendance['count']) * 100, 2)
                            : 0
                    ]
                ];
            });

        return $studentClasses;
    }


    // --------------------------------------------------------------------
    // PAYMENTS SUMMARY ONLY
    // --------------------------------------------------------------------
    private function getPaymentsForClassEnrollment($student_id, $enrollment_id)
    {
        $payments = Payments::where('student_id', $student_id)
            ->where('student_student_student_classes_id', $enrollment_id)
            ->orderBy('created_at', 'desc')
            ->get();

        return [
            'count' => $payments->count(),
            'total' => $payments->sum('amount')
        ];
    }

    // --------------------------------------------------------------------
    // CLASS ATTENDANCE
    // --------------------------------------------------------------------
    private function getClassAttendance($classCategoryId, $startDate, $endDate)
    {
        if (!$classCategoryId) {
            return ['ids' => collect([]), 'count' => 0];
        }

        $records = ClassAttendance::where('class_category_has_student_class_id', $classCategoryId)
            ->where('status', 1)
            ->where('is_ongoing', 1)
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->pluck('id');

        return [
            'ids' => $records,
            'count' => $records->count()
        ];
    }

    // --------------------------------------------------------------------
    // STUDENT ATTENDANCE
    // --------------------------------------------------------------------
    private function getStudentAttendance($attendanceIds, $student_id, $totalSessions)
    {
        if ($attendanceIds->count() === 0) {
            return [
                'present_count' => 0,
                'absent_count'  => $totalSessions
            ];
        }

        $records = StudentAttendances::whereIn('attendance_id', $attendanceIds)
            ->where('student_id', $student_id)
            ->get();

        $present = $records->count();
        $absent = max(0, $totalSessions - $present);

        return [
            'present_count' => $present,
            'absent_count'  => $absent
        ];
    }

    private function createStudentPortalLogin(Student $student): void
    {
        // Generate a random password
        $password = Str::random(8);

        if (auth()->check()) {
            // Admin is creating the student
            StudentPortalLogin::create([
                'student_id' => $student->id,
                'username'   => $student->mobile,  // or email if you prefer
                'password'   => Hash::make($password),
                'is_active'  => true,
                'is_verify'  => true,
                'otp'        => null,
                'otp_expires_at' => null,
            ]);

            //Log::info("Admin created portal login for student ID {$student->id}");
        } else {
            // Self-registration → OTP required
            $otp = rand(100000, 999999); // 6-digit OTP

            StudentPortalLogin::create([
                'student_id'      => $student->id,
                'username'        => $student->mobile,
                'password'        => Hash::make($password),
                'is_active'       => false, // account inactive until OTP verify
                'is_verify'       => false,
                'otp'             => $otp,
                'otp_expires_at'  => now()->addMinutes(5),
            ]);

            // TODO: send OTP via SMS gateway
            // Log::info("OTP {$otp} generated for student ID {$student->id} (self-registration)");
        }
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'otp' => 'required|digits:6',
        ]);

        $login = StudentPortalLogin::where('username', $request->username)
            ->where('otp', $request->otp)
            ->where('otp_expires_at', '>=', now())
            ->first();

        if (!$login) {
            return response()->json(['message' => 'Invalid or expired OTP'], 422);
        }

        $login->update([
            'is_verify' => true,
            'is_active' => true,
            'otp' => null,
            'otp_expires_at' => null,
        ]);

        return response()->json(['message' => 'OTP verified, account active']);
    }

    public function fetchAllStudentCustomIDs()
    {
        try {
            $search = request()->get('search', '');
            $month  = request()->get('month'); // format: 2026-02

            $studentsQuery = Student::with([
                'grade' => function ($query) {
                    $query->select('id', 'grade_name');
                },
            ])
                ->select(
                    'id',
                    'custom_id',
                    'full_name',
                    'initial_name',
                    'mobile',
                    'created_at',
                    'img_url',
                    'grade_id'
                );

            // ✅ Month filter
            if (!empty($month)) {
                $studentsQuery->whereYear('created_at', date('Y', strtotime($month)))
                    ->whereMonth('created_at', date('m', strtotime($month)));
            } else {
                // Default: current month
                $studentsQuery->whereYear('created_at', now()->year)
                    ->whereMonth('created_at', now()->month);
            }

            // ✅ Search functionality
            if (!empty($search)) {
                $studentsQuery->where(function ($query) use ($search) {
                    $query->where('custom_id', 'like', "%{$search}%")
                        ->orWhere('full_name', 'like', "%{$search}%")
                        ->orWhere('initial_name', 'like', "%{$search}%")
                        ->orWhere('mobile', 'like', "%{$search}%")
                        ->orWhereHas('grade', function ($gradeQuery) use ($search) {
                            $gradeQuery->where('grade_name', 'like', "%{$search}%");
                        });
                });
            }

            // ✅ Get all records (No pagination)
            $students = $studentsQuery->orderBy('created_at', 'desc')->get();

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
}
