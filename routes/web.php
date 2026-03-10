<?php

use App\Http\Controllers\AdmissionPaymentsController;
use App\Http\Controllers\AdmissionsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClassAttendanceController;
use App\Http\Controllers\ClassHallsController;
use App\Http\Controllers\ClassRoomController;
use App\Http\Controllers\EmailsController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\InstitutePaymentController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PaymentReasonController;
use App\Http\Controllers\PaymentsController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingsCodeController;
use App\Http\Controllers\StudentAttendancesController;
use App\Http\Controllers\SystemUserController;
use App\Http\Controllers\UserTypesController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\StudentIdCardController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\TeacherLedgerSummaryController;
use App\Http\Controllers\TeacherPaymentsController;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;

// Welcome Page Route
Route::get('/', function () {
    if (auth()->check()) {
        return view('welcome');
        // return redirect('/dashboard');
    }
    return view('welcome');
})->name('welcome');

Route::get('/student_regiter', function () {
    return view('student_register');
})->name('student_register');

Route::get('/interactive-learning', function () {
    return view('interactive-learning');
})->name('interactive-learning');

Route::get('/mobile-app', function () {
    return view('mobile-app');
})->name('mobile-app');

Route::get('/web-platform', function () {
    return view('web-platform');
})->name('web-platform');

Route::get('/pricing', function () {
    return view('pricing');
})->name('pricing');

// Authentication routes - guest users සඳහා පමණි
Route::middleware(['guest'])->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

// Logout route
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected routes (login වී ඇති users සඳහා පමණි)
Route::middleware(['auth', 'check.permission'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/home', [DashboardController::class, 'index'])->name('home');

    // System Users - Web Routes
    Route::prefix('system-users')->group(function () {
        // Page routes
        Route::get('/', [SystemUserController::class, 'viewPage'])->name('system-users.index');
        Route::get('/create', [SystemUserController::class, 'createPage'])->name('system-users.create');
        Route::get('/{id}/view', [SystemUserController::class, 'showPage'])->name('system-users.showPage');
        Route::get('/{id}/edit', [SystemUserController::class, 'editPage'])->name('system-users.edit');
        // AJAX fetch from web
        Route::get('/list', [SystemUserController::class, 'getSystemUsers'])->name('system-users.list');
    });

    // routes/web.php
    Route::prefix('user-types')->group(function () {
        Route::get('/', [UserTypesController::class, 'index'])->name('user-types.index');
        Route::get('/create', [UserTypesController::class, 'createPage'])->name('user-types.create');
        Route::get('/{id}/view', [UserTypesController::class, 'showPage'])->name('user-types.show');

        // AJAX routes for web
        Route::get('/list', [UserTypesController::class, 'getUserTypes'])->name('user-types.list');
    });

    Route::prefix('class-attendances')->group(function () {
        Route::get('/{classCategoryHasStudentClassId}', [ClassAttendanceController::class, 'indexPage'])->name('class-attendance.index');
        Route::get('/create/{classCategoryHasStudentClassId}', [ClassAttendanceController::class, 'createPage'])->name('class-attendance.create');
    });

    Route::prefix('students')->group(function () {
        Route::get('/', [StudentController::class, 'index'])->name('students.index');
        Route::get('/import', [StudentController::class, 'showImportForm'])->name('students.import.form');
        Route::post('/import', [StudentController::class, 'import'])->name('students.import');
        Route::get('/create', [StudentController::class, 'create'])->name('students.create');

        // PUT THIS ABOVE THE {id} ROUTE
        Route::get('/studentImages', [StudentController::class, 'studentImages'])->name('students.studentImages');
        Route::get('/images', [StudentController::class, 'allImages'])->name('students.images');

        // ✅ FIX: Remove the duplicate 'students' from the URL
        Route::get('/add_student_to_class/{class_id}', [StudentController::class, 'addStudentToClass'])->name('students.add_student_to_class');
        Route::get('/add_student_to_single_class/{student_id}', [StudentController::class, 'addStudentToSingleClass'])->name('students.add_student_to_single_class');
        Route::get('/student_analytic/{student_id}', [StudentController::class, 'studentAnalytic'])->name('students.student_analytic');

        Route::get('/{student_id}/edit', [StudentController::class, 'editPage'])->name('students.edit');
        Route::get('/{student_id}', [StudentController::class, 'show'])->name('students.show');
        Route::get('/{classCategoryHasStudentClassId}/{student_id}/exam-results', [StudentController::class, 'examResults'])->name('students.exam_results');
    });

    // ====================================================
    // STUDENT ID CARD ROUTES (All ID card related routes)
    // ====================================================
    Route::prefix('student-id-card')->group(function () {
        // ID card generation page (with search/sort parameters)
        Route::get('/ganarateStudentId', [StudentIdCardController::class, 'ganarateStudentId'])
            ->name('student-id-card.ganarateStudentId');

        // Single student ID card preview
        Route::get('/idcard/{custom_id}', [StudentIdCardController::class, 'previewCard'])
            ->name('idcard.design1');

        // Generate ID cards for selected students
        Route::post('/generate-bulk', [StudentIdCardController::class, 'generateBulkCards'])
            ->name('student-id-card.generate.bulk');

        // Generate ID cards for all students
        Route::get('/generate-all', [StudentIdCardController::class, 'generateAllCards'])
            ->name('student-id-card.generate.all');

        // Clear search filters
        Route::get('/clear-filters', [StudentIdCardController::class, 'clearFilters'])
            ->name('student-id-card.clear-filters');
    });




    Route::prefix('class-rooms')->group(function () {
        Route::get('/', [ClassRoomController::class, 'index'])->name('class_rooms.index');
        Route::get('/create', [ClassRoomController::class, 'create'])->name('class_rooms.create');
        Route::get('/schedule', [ClassRoomController::class, 'schedule'])->name('class_rooms.schedule');   // <-- FIX
        Route::get('/add_class_category/{id}', [ClassRoomController::class, 'classCategoryAdd'])->name('class_rooms.add_class_category');
        Route::get('/{id}/edit', [ClassRoomController::class, 'edit'])->name('class_rooms.edit');
        Route::get('/{id}', [ClassRoomController::class, 'show'])->name('class_rooms.show');
    });

    Route::prefix('student-exam')->group(function () {
        Route::get('/', [ExamController::class, 'indexPage'])->name('student_exam.index');
        Route::get('/create', [ExamController::class, 'createPage'])->name('student_exam.create');

        // Dynamic route must come after static ones
        Route::get('/{exam_id}/marks/create', [ExamController::class, 'enterMarks'])->name('student_exam.marks');
    });

    Route::prefix('halls')->group(function () {
        Route::get('/', [ClassHallsController::class, 'indexPage'])->name('class_halls.index');
    });



    Route::prefix('teachers')->group(function () {

        Route::get('/', [TeacherController::class, 'index'])->name('teachers.index');
        Route::get('/create', [TeacherController::class, 'create'])->name('teachers.create');
        Route::get('/classes/{id}', [TeacherController::class, 'classes'])->name('teachers.classes');

        // 👇 Specific route (should be ABOVE /{id})
        Route::get('/view_student/{id}', [TeacherController::class, 'viewStudents'])
            ->name('teachers.view_student');

        // Dynamic routes MUST stay at the bottom
        Route::get('/{id}/edit', [TeacherController::class, 'editPage'])->name('teachers.edit');
        Route::get('/{id}', [TeacherController::class, 'show'])->name('teachers.show');
    });

    Route::prefix('admissions')->group(function () {
        Route::get('/', [AdmissionsController::class, 'indexPage'])->name('admissions.index');
    });

    Route::prefix('pay-admissions')->group(function () {
        Route::get('/', [AdmissionPaymentsController::class, 'payAdmissionPage'])
            ->name('pay-admissions.admission_payment');
    });

    Route::prefix('student-payment')->name('student-payment.')->group(function () {
        Route::get('/', [PaymentsController::class, 'indexPage'])
            ->name('index');
        Route::get('/create', [PaymentsController::class, 'createPage'])
            ->name('create');
        Route::get('/details/{student_id}/{student_class_id}', [PaymentsController::class, 'detailsPage'])
            ->name('details');
    });
    Route::prefix('student_attendance')->name('student_attendance.')->group(function () {
        Route::get('/', [StudentAttendancesController::class, 'indexPage'])
            ->name('index');
        Route::get('/daily', [StudentAttendancesController::class, 'dailyMarkPage'])
            ->name('daily');
        // FIXED: Add slashes between parameters
        Route::get(
            '/{class_id}/{attendance_id}/{class_category_student_class_id}/details',
            [StudentAttendancesController::class, 'detailsPage']
        )->name('details');
    });

    Route::prefix('payment-reason')->name('payment_reason.')->group(function () {
        Route::get('/', [PaymentReasonController::class, 'indexPage'])->name('index');
    });

    Route::prefix('send-mail')->name('emails.')->group(function () {

        // ✅ PDF download – MUST be first
        Route::get('/pdf/{teacherId}/{yearMonth}', [EmailsController::class, 'downloadPaymentReport'])
            ->where('yearMonth', '\d{4}-\d{2}');

        // ✅ Send email – generic route AFTER
        Route::get('/{teacherId}/{yearMonth}', [EmailsController::class, 'sendPaymentReport'])
            ->where('yearMonth', '\d{4}-\d{2}');

        Route::get('/test-email-connection', function () {
            try {
                Mail::raw('This is a test email from Student Management System', function ($message) {
                    $message->to('isurufernando000@gmail.com')
                        ->subject('Test Email Connection');
                });

                return response()->json([
                    'success' => true,
                    'message' => 'Test email sent successfully!'
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'error' => $e->getMessage()
                ], 500);
            }
        });
    });

    // routes/web.php
    Route::prefix('receipt')->name('receipt.')->group(function () {
        Route::get('/{id}', [ReceiptController::class, 'viewReceipt'])->name('view');
        Route::get('/{id}/download', [ReceiptController::class, 'downloadReceipt'])->name('download');
        Route::post('/{id}/print', [ReceiptController::class, 'thermalPrint'])->name('thermal-print');
    });

    Route::prefix('teacher-payment')->name('teacher_payment.')->group(function () {
        Route::get('/', [TeacherPaymentsController::class, 'indexPage'])->name('index');
        Route::get('/expenses', [TeacherPaymentsController::class, 'expensesPage'])->name('expenses');
        Route::get('/pay/{teacherId}', [TeacherPaymentsController::class, 'paymentPage'])->name('salary');
        Route::get('/history/{teacherId}', [TeacherPaymentsController::class, 'historyPage'])->name('history');
        Route::get('/view/{teacherId}', [TeacherPaymentsController::class, 'viewPage'])->name('view');
        Route::get('/salary-slip/{teacherId}/{yearMonth}', [TeacherPaymentsController::class, 'showSalarySlip'])->name('salary-slip-exact');
    });

    Route::prefix('institute-payment')->name('institute_payment.')->group(function () {
        Route::get('/', [InstitutePaymentController::class, 'indexPage'])->name('index');
        Route::get('/extra', [InstitutePaymentController::class, 'extraIncomePage'])->name('extra');
        Route::get('/expenses', [InstitutePaymentController::class, 'expensesPage'])->name('expenses');
        Route::get('/ledger', [InstitutePaymentController::class, 'ledgerPage'])->name('ledger');
    });



    // Other Pages
    Route::get('/classes', [DashboardController::class, 'classes'])->name('classes');
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'indexPage'])->name('index');
        Route::get('/daily-pdf/{day}', [ReportController::class, 'downloadDailyReportPdf'])
            ->name('daily.pdf');
    });
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [SettingsCodeController::class, 'indexPage'])->name('index');
    });

    Route::prefix('teacher-ledger-summary')->name('teacher_ledger_summary.')->group(function () {
        Route::get('/', [TeacherLedgerSummaryController::class, 'index'])->name('index');
        Route::get('/export-excel', [TeacherLedgerSummaryController::class, 'exportExcel'])->name('export.excel');
        Route::get('/export-pdf', [TeacherLedgerSummaryController::class, 'exportPDF'])->name('export.pdf');
    });


    /*=================================================
    Permission  Sections
    /*================================================= */

    Route::prefix('permission')->name('permission.')->group(function () {
        Route::get('/{userId}', [PageController::class, 'index'])->name('index');
    });
});
