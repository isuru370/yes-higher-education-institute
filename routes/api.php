<?php

use App\Http\Controllers\AdmissionPaymentsController;
use App\Http\Controllers\AdmissionsController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ImageUploadController;
use App\Http\Controllers\BankController;
use App\Http\Controllers\BankBranchController;
use App\Http\Controllers\ClassAttendanceController;
use App\Http\Controllers\ClassCategoryController;
use App\Http\Controllers\ClassCategoryHasStudentClassController;
use App\Http\Controllers\ClassHallsController;
use App\Http\Controllers\ClassRoomController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\LedgerSummaryController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\GradeController;
use App\Http\Controllers\InstitutePaymentController;
use App\Http\Controllers\MobileDashboardController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PaymentReasonController;
use App\Http\Controllers\PaymentsController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\QuickPhotoController;
use App\Http\Controllers\ReadQRCodeController;
use App\Http\Controllers\SmsController;
use App\Http\Controllers\StudentAttendancesController;
use App\Http\Controllers\StudentClassSeparateController;
use App\Http\Controllers\StudentResultController;
use App\Http\Controllers\StudentStudentStudentClassController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\SystemUserController;
use App\Http\Controllers\UserTypesController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\TeacherPaymentsController;
use App\Http\Controllers\TituteController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/public', [StudentController::class, 'publicStudentRegister']);
Route::get('/dropdown', [GradeController::class, 'fetchPublicDropdownGrade']);
Route::post('/upload', [ImageUploadController::class, 'publickUpload']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::get('/user', [AuthController::class, 'user']);
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/mobile-dashboard', [MobileDashboardController::class, 'index']);

    // Image Upload
    Route::prefix('image-upload')->group(function () {
        Route::post('/upload', [ImageUploadController::class, 'upload']);
    });

    // System Users
    Route::prefix('system-users')->group(function () {
        Route::get('/', [SystemUserController::class, 'getSystemUsers']);
        Route::get('/{id}', [SystemUserController::class, 'getSystemUser']);
        Route::post('/', [SystemUserController::class, 'store']);
        Route::put('/{id}', [SystemUserController::class, 'update']);
        Route::delete('/{id}', [SystemUserController::class, 'destroy']);
        Route::patch('/{id}/reactivate', [SystemUserController::class, 'reactivate']);
    });

    // User Types
    Route::prefix('user-types')->group(function () {
        Route::get('/', [UserTypesController::class, 'getUserTypes']);
        Route::get('/dropdown', [UserTypesController::class, 'getDropdownUserTypes']);
        Route::get('/{id}', [UserTypesController::class, 'getUserType']);
        Route::post('/', [UserTypesController::class, 'store']);
        Route::put('/{id}', [UserTypesController::class, 'update']);
    });

    // Teachers - FIXED: Specific routes before parameterized routes
    Route::prefix('teachers')->group(function () {
        Route::get('/dropdown', [TeacherController::class, 'getDropdownTeachers']);
        Route::get('/active', [TeacherController::class, 'fetchActiveTeachers']);
        Route::post('/check-email', [TeacherController::class, 'checkEmailUnique']);
        Route::post('/check-nic', [TeacherController::class, 'checkNicUnique']);

        // CRUD routes
        Route::get('/', [TeacherController::class, 'fetchTeachers']);
        Route::post('/', [TeacherController::class, 'store']);

        // Parameterized routes - MUST be last
        Route::get('/{id}', [TeacherController::class, 'fetchTeacher']);
        Route::put('/{id}', [TeacherController::class, 'update']);
        Route::delete('/{id}', [TeacherController::class, 'destroy']);
        Route::put('/{id}/reactivate', [TeacherController::class, 'reactivate']);
    });

    // Students
    Route::prefix('students')->group(function () {
        Route::get('/custom_ids', [StudentController::class, 'fetchAllStudentCustomIDs']);
        Route::get('/active', [StudentController::class, 'fetchActiveStudents']);
        Route::get('/temp_qr', [StudentController::class, 'fetchTempQrCode']);
        Route::get('/filter-by-date', [StudentController::class, 'filterByCreatedDate']);
        Route::post('/admission', [StudentController::class, 'fetchNotPaidAdmissionStudent']);
        Route::post('/custom_id', [StudentController::class, 'generateCustomIdAPI']);
        Route::get('/search/{customId}', [StudentController::class, 'fetchStudentCustomId']);
        Route::post('/update_image/{customId}', [StudentController::class, 'updateStudentImage']);
        Route::get('/analytics/{id}', [StudentController::class, 'analytics']);

        // CRUD routes
        Route::get('/', [StudentController::class, 'fetchStudents']);
        Route::post('/', [StudentController::class, 'store']);

        // Parameterized routes
        Route::get('/{id}', [StudentController::class, 'fetchstudent']);
        Route::put('/{custom_id}', [StudentController::class, 'update']);
        Route::delete('/{id}', [StudentController::class, 'destroy']);
        Route::put('/{id}/reactivate', [StudentController::class, 'reactivate']);
    });

    // Class Rooms
    Route::prefix('class-rooms')->group(function () {
        Route::get('/active', [ClassRoomController::class, 'fetchActiveClasses']);
        Route::get('/teacher/{teacherId}', [ClassRoomController::class, 'fetchTeacherClasse']);
        Route::get('/all', [ClassRoomController::class, 'fetchAllClassRoom']);
        // CRUD routes
        Route::get('/', [ClassRoomController::class, 'fetchClasses']);

        Route::post('/', [ClassRoomController::class, 'store']);

        // Parameterized routes
        Route::get('/{id}', [ClassRoomController::class, 'fetchSingleClasse']);
        Route::put('/{id}', [ClassRoomController::class, 'update']);
        Route::delete('/{id}/deactivate-active', [ClassRoomController::class, 'deactivateClassActive']);
        Route::delete('/{id}/deactivate-ongoing', [ClassRoomController::class, 'deactivateClassOngoing']);
        Route::put('/{id}/reactivate-active', [ClassRoomController::class, 'reactivateClassActive']);
        Route::put('/{id}/reactivate-ongoing', [ClassRoomController::class, 'reactivateClassOngoing']);
    });

    // Categories
    Route::prefix('categories')->group(function () {
        Route::get('/dropdown', [ClassCategoryController::class, 'fetchDropdownCategory']);
        Route::get('/', [ClassCategoryController::class, 'fetchClassCategory']);
        Route::get('/{id}', [ClassCategoryController::class, 'fetchSingleCategory']);
        Route::put('/{id}', [ClassCategoryController::class, 'update']);
        Route::post('/', [ClassCategoryController::class, 'store']);
    });

    // Class Category Has Student Classes
    Route::prefix('class-has-category-classes')->group(function () {
        // Specific routes FIRST
        Route::get('/dropdown', [ClassCategoryHasStudentClassController::class, 'getDropdownCategory']);
        Route::get('/details', [ClassCategoryHasStudentClassController::class, 'classCategoryHasStudentDropdown']);
        Route::get('/class-category-class/{classId}', [ClassCategoryHasStudentClassController::class, 'fetchByClassId']);

        // CRUD routes
        Route::get('/', [ClassCategoryHasStudentClassController::class, 'index']);
        Route::post('/', [ClassCategoryHasStudentClassController::class, 'store']);
        Route::get('/classes/search', [ClassCategoryHasStudentClassController::class, 'searchClasses']);
        // Parameterized routes LAST
        Route::put('/{id}', [ClassCategoryHasStudentClassController::class, 'update']);
    });

    // Class Attendance
    Route::prefix('class-attendances')->group(function () {
        // Specific routes first (before parameter routes)
        Route::get('/attendance', [ClassAttendanceController::class, 'fetchClassAttendanceByStudent']);
        Route::get('/by-date', [ClassAttendanceController::class, 'fetchClassesByDate']);
        Route::get('/test', [ClassAttendanceController::class, 'testConnection']);
        Route::post('/bulk', [ClassAttendanceController::class, 'storeBulk']);
        Route::post('/single', [ClassAttendanceController::class, 'store']);
        Route::post('/bulk-delete', [ClassAttendanceController::class, 'bulkDelete']);

        // Parameter routes last
        Route::get('/{classCategoryHasStudentClassId}', [ClassAttendanceController::class, 'fetchByClassCategoryHasStudentClasses']);
        Route::put('/{id}', [ClassAttendanceController::class, 'update']);
    });


    Route::prefix('student-classes')->group(function () {
        Route::post('/bulk', [StudentStudentStudentClassController::class, 'bulkStore']);
        Route::post('/single', [StudentStudentStudentClassController::class, 'storeSingleStudentClass']);
        //Get students by class and category
        Route::get('/{classId}/category/{categoryId}', [StudentStudentStudentClassController::class, 'getStudentsByClassAndCategory']);
        Route::get('/all/{classId}/category/{categoryId}', [StudentStudentStudentClassController::class, 'allDetailsGetStudentsByClassAndCategory']);
        Route::get('/student/{studentId}/filter', [StudentStudentStudentClassController::class, 'getStudentClassessFilterDetails']);
        Route::get('/student/{studentId}', [StudentStudentStudentClassController::class, 'getStudentClassessDetails']);
        //Activate a single student class record
        Route::put('/{id}/activate', [StudentStudentStudentClassController::class, 'activateStudentClass']);
        Route::put('/bulk-deactivate', [StudentStudentStudentClassController::class, 'bulkDeactivateStudentClasses']);
        //Deactivate a single student class record
        Route::delete('/{id}/deactivate', [StudentStudentStudentClassController::class, 'deactivateStudentClass']);
    });

    Route::prefix('student-class-separate')->group(function () {
        Route::get('/{studentId}', [StudentClassSeparateController::class, 'showStudentCategories']); // test karanna hada gaththa api ekak
    });

    Route::prefix('attendances')->group(function () {
        Route::get('/student', [StudentAttendancesController::class, 'getAllAttendances']);
        Route::get('/read-attendance', [StudentAttendancesController::class, 'readAttendance']);
        Route::get('/attend/{studentId}/{classCategoryHasStudentClassId}', [StudentAttendancesController::class, 'getStudentAttendance']);
        Route::get('/monthly/{student_id}/{student_class_id}/{yearMonth}', [StudentAttendancesController::class, 'monthStudentAttendanceCount']);
        Route::get('/daily/{student_class_id}/{attendance_id}/{class_category_student_class_id}/details', [StudentAttendancesController::class, 'studentAttendClass']);
        Route::put('/update/{id}', [StudentAttendancesController::class, 'updateAttendance']);
        Route::delete('/delete/{id}', [StudentAttendancesController::class, 'attendanceRecoadDelete']);
        Route::post('/', [StudentAttendancesController::class, 'storeAttendance']);
    });



    Route::prefix('exams')->group(function () {

        Route::get('/', [ExamController::class, 'index']);
        // GET /exams → List exams

        Route::post('/', [ExamController::class, 'store']);
        // POST /exams → Create exam

        Route::post('/results', [StudentResultController::class, 'store']);

        Route::get('/results/{classCategoryHasStudentClassId}/{studentId}', [StudentResultController::class, 'fetchStudentExamChart']);

        Route::get('/{exam_id}', [ExamController::class, 'studentClassMiniDetails']);
        // POST /exams/{exam_id} → Get students with results for the exam

        Route::put('/{exam_id}', [ExamController::class, 'update']);
        // PUT /exams/5 → Update exam

        Route::patch('/{exam_id}/cancel', [ExamController::class, 'cancel']);
        // PATCH /exams/5/cancel → Cancel exam

    });

    // Grades
    Route::prefix('grades')->group(function () {
        Route::get('/dropdown', [GradeController::class, 'fetchDropdownGrade']);
        Route::get('/', [GradeController::class, 'fetchAllGrade']);
        Route::post('/', [GradeController::class, 'store']);
        Route::put('/{id}', [GradeController::class, 'update']);
    });

    // Subjects
    Route::prefix('subjects')->group(function () {
        Route::get('/dropdown', [SubjectController::class, 'getDropdownSubject']);
        Route::get('/', [SubjectController::class, 'fetchAllSubject']);
        Route::post('/', [SubjectController::class, 'store']);
        Route::put('/{id}', [SubjectController::class, 'update']);
    });

    Route::prefix('halls')->group(function () {
        Route::get('/dropdown', [ClassHallsController::class, 'fetchDropdownHalls']);
        Route::get('/', [ClassHallsController::class, 'fetchClassHalls']);
        Route::get('/{id}', [ClassHallsController::class, 'fetchClassHall']);
        Route::put('/{id}', [ClassHallsController::class, 'updateClassHall']);
        Route::post('/', [ClassHallsController::class, 'storeClassHall']);
    });

    // Banks
    Route::prefix('banks')->group(function () {
        Route::get('/dropdown', [BankController::class, 'fetchDropdownBanks']);
        Route::get('/', [BankController::class, 'fetchBanks']);
    });

    // Bank Branches
    Route::prefix('bank-branches')->group(function () {
        Route::get('/{bankId}/dropdown', [BankBranchController::class, 'fetchDropdownBranches']);
        Route::get('/{bankId}', [BankBranchController::class, 'fetchBranches']);
    });

    // Quick Photos
    Route::prefix('quick-photos')->group(function () {
        Route::get('/active', [QuickPhotoController::class, 'fetchActiveQuickPhoto']);
        Route::post('/', [QuickPhotoController::class, 'store']);
        Route::delete('/{id}', [QuickPhotoController::class, 'destroy']);
    });

    Route::prefix('read-qr-code')->group(function () {
        Route::get('/student-id', [ReadQRCodeController::class, 'readQRCode']);
        Route::get('/activated/{customId}', [ReadQRCodeController::class, 'studentIdCardActive']);
    });

    /*=================================================
     Student tute API Sections
    /*================================================= */

    Route::prefix('tute')->group(function () {

        Route::get(
            '/class-wise/{customId}',
            [TituteController::class, 'readClassWiseTute']
        );

        Route::get(
            '/student/{studentId}/class/{classCategoryStudentClassId}',
            [TituteController::class, 'getStudentWithAllTutes']
        );

        Route::get(
            '/check/{studentId}/{classCategoryStudentClassId}',
            [TituteController::class, 'checkTute']
        );

        Route::post(
            '/',
            [TituteController::class, 'createTitute']
        );

        Route::patch(
            '/{id}/toggle-status',
            [TituteController::class, 'toggleStatus']
        );
    });

    /*=================================================
    Permission API Sections
    /*================================================= */

    Route::prefix('permission')->group(function () {
        Route::get('/{userTypeId}', [PermissionController::class, 'getUserPermissions']);
        Route::post('/store', [PermissionController::class, 'assignPermissions']);
        Route::get('/', [PageController::class, 'allPages']);
    });

    /*=================================================
     SMS API Sections
    /*================================================= */

    Route::prefix('send-sms')->group(function () {
        Route::post('/', [SmsController::class, 'sendSMS']);
    });

    /*=================================================
     Payment API Sections
    /*================================================= */

    Route::prefix('payment-reason')->group(function () {
        Route::get('/dropdown', [PaymentReasonController::class, 'getDropdown']);
        Route::get('/', [PaymentReasonController::class, 'fetchAllPaymentReason']);
        Route::put('/{id}', [PaymentReasonController::class, 'update']);
        Route::delete('/{id}', [PaymentReasonController::class, 'destroy']);
        Route::post('/', [PaymentReasonController::class, 'store']);
    });

    Route::prefix('admissions')->group(function () {
        Route::get('/dropdown', [AdmissionsController::class, 'getDropdownAdmissions']);
        Route::put('/{id}', [AdmissionsController::class, 'updateAdmission']);
        Route::get('/{id}', [AdmissionsController::class, 'showAdmission']); // Add this line
        Route::get('/', [AdmissionsController::class, 'fetchAdmissions']);
        Route::post('/', [AdmissionsController::class, 'storeAdmission']);
    });

    Route::prefix('payment-admissions')->group(function () {
        Route::get('/chart/{year}/{month}', [AdmissionPaymentsController::class, 'fetchPayAdmissionsStaticCart']);
        Route::get('/students/paid', [AdmissionPaymentsController::class, 'fetchPayAdmissions']);
        Route::post('/store-pay-admission/bulk', [AdmissionPaymentsController::class, 'storeBulkAdmissionPayment']);
        Route::get('/student', [AdmissionPaymentsController::class, 'fetchStudentAdmissions']);
    });
    Route::prefix('payments')->group(function () {
        Route::post('/', [PaymentsController::class, 'storePayment']);
        Route::get('/by-date/{date}', [PaymentsController::class, 'getPaymentsByDate']);
        Route::get('/receipt/{payment_id}', [PaymentsController::class, 'receiptPrint']);
        Route::get('/mobile/{custom_id}', [PaymentsController::class, 'mobileReadStudentPayment']); // ✅ fixed
        Route::get('/teacher', [PaymentsController::class, 'getTeacherPayments']);
        Route::get('/{student_id}/{student_class_id}', [PaymentsController::class, 'fetchStudentPayments']);
        Route::put('/{id}', [PaymentsController::class, 'updatePayment']);
        Route::delete('/{id}', [PaymentsController::class, 'deletePayment']);
    });


    Route::prefix('teacher-payments')->group(function () {
        Route::get('/monthly-income', [TeacherPaymentsController::class, 'fetchTeacherPaymentsCurrentMonth']);
        Route::get('/monthly-income/{teacherId}/{yearMonth}', [TeacherPaymentsController::class, 'fetchTeacherClassPayments']);
        Route::get('/class-wise/{teacherId}/{yearMonth}', [TeacherPaymentsController::class, 'getTeacherClassWiseStudentPaymentStatus']);
        Route::get('/student-pay/{teacherId}/{yearMonth}', [TeacherPaymentsController::class, 'studentPaymentMonthCheck']);
        Route::get('/salary-slip/{teacherId}/{yearMonth}', [TeacherPaymentsController::class, 'fetchSalarySlipDataTest']);
        Route::get('/expenses/{yearMonth}', [TeacherPaymentsController::class, 'teachersExpenses']);
        Route::get('/monthly-income/{yearMonth}', [TeacherPaymentsController::class, 'getMonthlyPayments']);
        Route::post('/{id}/toggle-status', [TeacherPaymentsController::class, 'togglePaymentStatus']);
        Route::post('/', [TeacherPaymentsController::class, 'storeTeacherPayments']);
    });

    Route::prefix('institute-payments')->group(function () {
        Route::get('/monthly-income/{yearMonth}', [InstitutePaymentController::class, 'fetchInstitutePaymentByMonth']);
        Route::get('/extra-income/{yearMonth}', [InstitutePaymentController::class, 'fetchExtraIncome']);
        Route::get('/institute-expenses/{yearMonth}', [InstitutePaymentController::class, 'fetchInstituteExpenses']);
        Route::get('/yearly-income-chart/{year}', [InstitutePaymentController::class, 'fetchYearlyIncomeChart']);
        Route::post('/store', [InstitutePaymentController::class, 'institutePaymentStore']);
        Route::delete('/destroy/{id}', [InstitutePaymentController::class, 'institutePaymentDestroy']);
        Route::post('/extra-income/store', [InstitutePaymentController::class, 'extraIncomeStore']);
        Route::delete('/extra-income/delete/{id}', [InstitutePaymentController::class, 'extraIncomeDelete']);
    });

    Route::prefix('ledger')->group(function () {
        Route::get('/monthly/{yearMonth}', [LedgerSummaryController::class, 'getMonthlySummary'])
            ->where('yearMonth', '[0-9]{4}-[0-9]{2}');
        Route::get('/test/{yearMonth}', [LedgerSummaryController::class, 'testMonth']);
    });
});
