@extends('layouts.app')

@section('title', 'Student Payment')
@section('page-title', 'Student Payment')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('student-payment.index') }}">Payment</a></li>
    <li class="breadcrumb-item active">Student Payment</li>
@endsection

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <!-- Success/Error Messages -->
                <div id="paymentMessages"></div>
                
                <!-- QR Scanner and Student ID Input Section -->
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-qrcode me-2"></i>Student Identification
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="qr-scanner-container text-center p-4 border rounded">
                                    <i class="fas fa-camera fa-3x text-muted mb-3"></i>
                                    <h5>QR Code Scanner</h5>
                                    <p class="text-muted">Scan student QR code to auto-fill information</p>
                                    <button class="btn btn-outline-primary" id="startScanner">
                                        <i class="fas fa-camera me-2"></i>Start QR Scanner
                                    </button>
                                    <div id="qr-reader" class="mt-3" style="display: none;"></div>
                                    <div id="qr-error" class="mt-2 text-danger small" style="display: none;"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="manual-input-container">
                                    <h5>Or Enter Student ID Manually</h5>
                                    <div class="input-group mb-3">
                                        <input type="text" class="form-control" id="studentCustomId"
                                            placeholder="Enter Student Custom ID (e.g., SA03004)" autocomplete="off">
                                        <button class="btn btn-primary" type="button" id="searchStudent">
                                            <i class="fas fa-search me-2"></i>Search
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Student Information Display -->
                <div class="card student-info-card mt-4" id="studentInfoCard" style="display: none;">
                    <div class="card-header bg-success text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-user-graduate me-2"></i>Student Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row" id="studentDetails">
                            <!-- Student details will be loaded here -->
                        </div>
                    </div>
                </div>

                <!-- Classes and Payment Information -->
                <div class="card mt-4" id="classesCard" style="display: none;">
                    <div class="card-header bg-info text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-chalkboard-teacher me-2"></i>Student Classes & Fees
                        </h5>
                    </div>
                    <div class="card-body">
                        <div id="classesList">
                            <!-- Classes will be loaded here -->
                        </div>
                    </div>
                </div>

                <!-- Loading Spinner -->
                <div class="text-center mt-4" id="loadingSpinner" style="display: none;">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Loading student information...</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Modal -->
    <div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="paymentModalLabel">
                        <i class="fas fa-money-bill-wave me-2"></i>Process Payment
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="paymentForm">
                        @csrf
                        <input type="hidden" id="payment_student_id" name="student_id">
                        <input type="hidden" id="payment_student_class_id" name="student_student_student_classes_id">
                        <input type="hidden" id="payment_guardian_mobile" name="guardian_mobile">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="card mb-3">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">Payment Information</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="payment_month" class="form-label">Payment Month & Year</label>
                                            <div class="input-group">
                                                <select class="form-select" id="payment_month" name="month">
                                                    @for($i = 1; $i <= 12; $i++)
                                                        <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}" 
                                                                {{ $i == date('m') ? 'selected' : '' }}>
                                                            {{ date('F', mktime(0, 0, 0, $i, 1)) }}
                                                        </option>
                                                    @endfor
                                                </select>
                                                <select class="form-select" id="payment_year" name="year">
                                                    @for($i = date('Y') - 1; $i <= date('Y') + 1; $i++)
                                                        <option value="{{ $i }}" {{ $i == date('Y') ? 'selected' : '' }}>
                                                            {{ $i }}
                                                        </option>
                                                    @endfor
                                                </select>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="payment_date" class="form-label">Payment Date</label>
                                            <input type="date" class="form-control" id="payment_date" name="payment_date"
                                                value="{{ date('Y-m-d') }}" required>
                                        </div>

                                        <div class="mb-3">
                                            <label for="payment_amount" class="form-label">Amount</label>
                                            <div class="input-group">
                                                <span class="input-group-text">LKR</span>
                                                <input type="number" class="form-control" id="payment_amount" name="amount"
                                                    step="0.01" required>
                                            </div>
                                            <div class="form-text">
                                                Default fee: <span id="defaultFee">0.00</span> |
                                                <button type="button" class="btn btn-sm btn-outline-primary" id="useDefaultFee">
                                                    Use Default
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="card mb-3">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">Attendance Information</h6>
                                    </div>
                                    <div class="card-body">
                                        <div id="attendanceLoading" class="text-center py-3" style="display: none;">
                                            <div class="spinner-border spinner-border-sm text-primary" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                            <p class="mt-2 text-muted">Loading attendance...</p>
                                        </div>
                                        <div id="attendanceInfo" style="display: none;">
                                            <div class="text-center mb-3">
                                                <h5 id="attendanceMonthYear" class="text-primary"></h5>
                                            </div>
                                            <div class="row text-center">
                                                <div class="col-6">
                                                    <div class="border rounded p-3 bg-success text-white">
                                                        <h4 id="attendanceCount" class="mb-0">0</h4>
                                                        <small>Days Present</small>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="border rounded p-3 bg-info text-white">
                                                        <h4 id="weeksInMonth" class="mb-0">0</h4>
                                                        <small>Weeks in Month</small>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mt-3 text-center">
                                                <small class="text-muted" id="attendanceLastUpdated"></small>
                                            </div>
                                        </div>
                                        <div id="noAttendanceInfo" class="text-center py-3" style="display: none;">
                                            <i class="fas fa-calendar-times fa-2x text-muted mb-2"></i>
                                            <p class="text-muted mb-0">No attendance data available</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Student & Class Info -->
                        <div class="card">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">Student & Class Details</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>Student:</strong> <span id="modalStudentName">-</span></p>
                                        <p class="mb-1"><strong>Student ID:</strong> <span id="modalStudentId">-</span></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>Class:</strong> <span id="modalClassName">-</span></p>
                                        <p class="mb-1"><strong>Grade:</strong> <span id="modalGrade">-</span></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancel
                    </button>
                    <button type="button" class="btn btn-success" id="processPaymentBtn">
                        <i class="fas fa-credit-card me-2"></i>Process Payment
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .qr-scanner-container {
            border: 2px dashed #dee2e6;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            background: #f8f9fa;
            margin-bottom: 20px;
        }

        .student-info-card {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .class-card {
            border: none;
            border-radius: 15px;
            transition: all 0.3s ease;
            background: white;
            overflow: hidden;
            margin-bottom: 20px;
        }

        .class-card:hover {
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
            transform: translateY(-5px);
        }

        .hover-lift:hover {
            transform: translateY(-5px);
        }

        .card-header.bg-gradient {
            background: linear-gradient(135deg, var(--bs-primary) 0%, var(--bs-primary-dark) 100%) !important;
        }

        .bg-gradient.bg-success {
            background: linear-gradient(135deg, #198754 0%, #146c43 100%) !important;
        }

        .bg-gradient.bg-info {
            background: linear-gradient(135deg, #0dcaf0 0%, #0aa2c0 100%) !important;
        }

        .bg-gradient.bg-warning {
            background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%) !important;
        }

        .bg-gradient.bg-danger {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%) !important;
        }

        .bg-gradient.bg-secondary {
            background: linear-gradient(135deg, #6c757d 0%, #545b62 100%) !important;
        }

        .class-icon {
            opacity: 0.8;
        }

        .avatar-sm {
            width: 40px;
            height: 40px;
        }

        .icon-wrapper {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .detail-item {
            border-bottom: 1px solid #f8f9fa;
            padding-bottom: 12px;
        }

        .detail-item:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .fee-section {
            border-left: 4px solid #28a745;
            background: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
        }

        .btn-pay,
        .btn-view {
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-pay:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
        }

        .free-card-section {
            transition: all 0.3s ease;
            border: 2px dashed #28a745 !important;
            padding: 15px;
            border-radius: 10px;
            text-align: center;
        }

        .free-card-section:hover {
            background: rgba(40, 167, 69, 0.15) !important;
            transform: scale(1.02);
        }

        .card-title {
            color: white;
            font-size: 1.1rem;
        }

        #qr-reader {
            width: 100%;
            max-width: 300px;
            margin: 0 auto;
        }

        #qr-reader__dashboard_section {
            text-align: center;
        }

        .payment-status {
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 0.85em;
            font-weight: bold;
        }

        .status-paid {
            background-color: #d4edda;
            color: #155724;
        }

        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-overdue {
            background-color: #f8d7da;
            color: #721c24;
        }

        @media (max-width: 768px) {
            .class-card {
                margin-bottom: 1rem;
            }

            .avatar-sm {
                width: 35px;
                height: 35px;
            }
            
            .qr-scanner-container {
                margin-bottom: 20px;
            }
        }

        :root {
            --bs-primary-dark: #0056b3;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <script>
        class PaymentSystem {
            constructor() {
                this.studentId = null;
                this.currentStudentData = null;
                this.qrScanner = null;
                this.csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                this.init();
            }

            init() {
                this.setupEventListeners();
                this.setCurrentDate();
            }

            setupEventListeners() {
                // QR Scanner
                document.getElementById('startScanner').addEventListener('click', () => this.toggleQRScanner());
                
                // Manual Search
                document.getElementById('searchStudent').addEventListener('click', () => this.searchStudent());
                document.getElementById('studentCustomId').addEventListener('keypress', (e) => {
                    if (e.key === 'Enter') this.searchStudent();
                });

                // Payment Modal
                document.getElementById('payment_month').addEventListener('change', () => this.loadAttendanceData());
                document.getElementById('payment_year').addEventListener('change', () => this.loadAttendanceData());
                document.getElementById('useDefaultFee').addEventListener('click', () => this.useDefaultFee());
                document.getElementById('processPaymentBtn').addEventListener('click', () => this.submitPayment());
            }

            setCurrentDate() {
                const currentDate = new Date();
                document.getElementById('payment_month').value = String(currentDate.getMonth() + 1).padStart(2, '0');
                document.getElementById('payment_year').value = currentDate.getFullYear();
                document.getElementById('payment_date').value = currentDate.toISOString().split('T')[0];
            }

            async toggleQRScanner() {
                const scannerBtn = document.getElementById('startScanner');
                const qrReader = document.getElementById('qr-reader');
                const qrError = document.getElementById('qr-error');

                if (this.qrScanner) {
                    this.stopQRScanner();
                    scannerBtn.innerHTML = '<i class="fas fa-camera me-2"></i>Start QR Scanner';
                    return;
                }

                try {
                    scannerBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Loading Scanner...';
                    
                    if (typeof Html5QrcodeScanner === 'undefined') {
                        throw new Error('QR Scanner library not available');
                    }

                    qrReader.style.display = 'block';
                    qrError.style.display = 'none';
                    scannerBtn.innerHTML = '<i class="fas fa-stop me-2"></i>Stop Scanner';

                    this.qrScanner = new Html5QrcodeScanner("qr-reader", {
                        fps: 10,
                        qrbox: { width: 250, height: 250 },
                        rememberLastUsedCamera: true,
                        showTorchButtonIfSupported: true
                    });

                    this.qrScanner.render(
                        (decodedText) => this.onQRCodeScanned(decodedText),
                        (error) => console.log('QR Scanner error:', error)
                    );

                } catch (error) {
                    console.error('QR Scanner initialization error:', error);
                    qrError.textContent = 'Unable to start QR scanner. Please refresh and try again.';
                    qrError.style.display = 'block';
                    scannerBtn.innerHTML = '<i class="fas fa-camera me-2"></i>Start QR Scanner';
                    this.qrScanner = null;
                }
            }

            stopQRScanner() {
                if (this.qrScanner) {
                    this.qrScanner.clear().then(() => {
                        document.getElementById('qr-reader').style.display = 'none';
                        this.qrScanner = null;
                    }).catch(console.error);
                }
            }

            onQRCodeScanned(decodedText) {
                const customId = this.extractCustomIdFromQR(decodedText);
                if (customId) {
                    document.getElementById('studentCustomId').value = customId;
                    this.searchStudent(customId);
                    this.stopQRScanner();
                }
            }

            extractCustomIdFromQR(qrText) {
                const match = qrText.match(/SA\d+/);
                return match ? match[0] : qrText;
            }

            searchStudent(customId = null) {
                const searchId = customId || document.getElementById('studentCustomId').value.trim();
                
                if (!searchId) {
                    this.showAlert('Please enter a student custom ID', 'warning');
                    return;
                }

                this.showLoading(true);
                this.hideStudentInfo();

                fetch(`/api/read-qr-code/student-id?qr_code=${encodeURIComponent(searchId)}`)
                    .then(response => {
                        if (!response.ok) throw new Error('Student not found');
                        return response.json();
                    })
                    .then(data => {
                        if (data.status === 'success') {
                            this.studentId = data.student_id;
                            return fetch(`/api/student-classes/student/${this.studentId}/filter`);
                        } else {
                            throw new Error('Student not found');
                        }
                    })
                    .then(response => {
                        if (!response.ok) throw new Error('Unable to fetch student classes');
                        return response.json();
                    })
                    .then(data => {
                        if (data.status === 'success' && data.data.length > 0) {
                            this.currentStudentData = data.data[0];
                            this.displayStudentInfo(this.currentStudentData);
                            this.displayClasses(data.data);
                            this.showAlert('Student information loaded successfully', 'success');
                        } else {
                            throw new Error('No active classes found for this student');
                        }
                    })
                    .catch(error => {
                        console.error('Search error:', error);
                        this.showAlert('Error: ' + error.message, 'danger');
                    })
                    .finally(() => {
                        this.showLoading(false);
                    });
            }

            displayStudentInfo(studentData) {
                const student = studentData.student;
                const studentDetails = document.getElementById('studentDetails');

                studentDetails.innerHTML = `
                    <div class="col-md-2 text-center">
                        <img src="${student.img_url || '/uploads/logo/logo.png'}" alt="Student Photo" 
                             class="img-thumbnail rounded-circle" style="width: 100px; height: 100px; object-fit: cover;"
                             onerror="this.src='/uploads/logo/logo.png'">
                    </div>
                    <div class="col-md-5">
                        <h5>${student.last_name}</h5>
                        <p class="mb-1"><strong>Student ID:</strong> ${student.student_custom_id}</p>
                        <p class="mb-1"><strong>Guardian Mobile:</strong> ${student.guardian_mobile || 'N/A'}</p>
                        <p class="mb-1"><strong>Grade:</strong> ${studentData.student_class.grade.grade_name}</p>
                    </div>
                    <div class="col-md-5">
                        <p class="mb-0"><strong>Status:</strong> 
                            <span class="badge ${student.student_status == 1 ? 'bg-success' : 'bg-danger'}">
                                ${student.student_status == 1 ? 'Active' : 'Inactive'}
                            </span>
                        </p>
                    </div>
                `;

                document.getElementById('studentInfoCard').style.display = 'block';
            }

            displayClasses(classesData) {
                const classesList = document.getElementById('classesList');
                let html = '<div class="row g-4">';

                classesData.forEach(classData => {
                    const isFreeCard = classData.is_free_card == 1;
                    const classInactive = classData.status == 0;
                    const classFee = classData.classCategoryHasStudentClass?.class_fee;

                    const colors = ['primary', 'success', 'info', 'warning', 'danger', 'secondary'];
                    const colorIndex = classData.student_class.class_name.length % colors.length;
                    const themeColor = colors[colorIndex];

                    html += `
                        <div class="col-xl-4 col-lg-6 col-md-6">
                            <div class="class-card card h-100 shadow-sm border-0 hover-lift ${classInactive ? 'opacity-75' : ''}">
                                <div class="card-header bg-gradient bg-${classInactive ? 'secondary' : themeColor} text-white py-3 border-0">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <h6 class="card-title mb-1 fw-bold text-truncate">${classData.student_class.class_name}</h6>
                                            <div class="d-flex gap-2 align-items-center">
                                                <span class="badge bg-white bg-opacity-20 text-dark border-0">${classData.class_category.category_name}</span>
                                                ${classInactive ? '<span class="badge bg-danger bg-opacity-90 text-white border-0"><i class="fas fa-ban me-1"></i>Inactive</span>' : ''}
                                            </div>
                                        </div>
                                        <div class="class-icon">
                                            <i class="fas fa-graduation-cap fa-lg"></i>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-body">
                                    <div class="position-relative mb-4">
                                        <span class="badge bg-${classInactive ? 'secondary' : themeColor} px-3 py-2">
                                            <i class="fas fa-layer-group me-1"></i>${classData.student_class.grade.grade_name}
                                        </span>
                                    </div>

                                    <div class="class-details mb-3">
                                        <div class="detail-item d-flex align-items-center mb-2">
                                            <div class="icon-wrapper bg-${classInactive ? 'secondary' : themeColor} bg-opacity-10 rounded p-2 me-3">
                                                <i class="fas fa-book text-${classInactive ? 'secondary' : themeColor}"></i>
                                            </div>
                                            <div>
                                                <small class="text-muted d-block">Subject</small>
                                                <span class="fw-semibold">${classData.student_class.subject.subject_name}</span>
                                            </div>
                                        </div>

                                        <div class="detail-item d-flex align-items-center mb-2">
                                            <div class="icon-wrapper bg-${classInactive ? 'secondary' : themeColor} bg-opacity-10 rounded p-2 me-3">
                                                <i class="fas fa-chalkboard-teacher text-${classInactive ? 'secondary' : themeColor}"></i>
                                            </div>
                                            <div>
                                                <small class="text-muted d-block">Teacher</small>
                                                <span class="fw-semibold">${classData.student_class.teacher.first_name} ${classData.student_class.teacher.last_name}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="fee-section bg-light bg-opacity-50 rounded p-3 mb-3">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="text-muted small">Monthly Fee</span>
                                            <span class="h5 mb-0 ${classInactive ? 'text-secondary' : 'text-success'} fw-bold">
                                                LKR ${this.formatCurrency(classFee)}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="action-buttons mt-4">
                                        ${classInactive ? `
                                            <div class="inactive-class-section">
                                                <div class="text-center p-3 bg-secondary bg-opacity-10 border border-secondary border-2 rounded mb-3">
                                                    <i class="fas fa-pause-circle fa-2x text-secondary mb-2"></i>
                                                    <h6 class="text-secondary mb-1 fw-bold">Class Inactive</h6>
                                                    <small class="text-muted">Payments currently disabled</small>
                                                </div>
                                                <button class="btn btn-outline-secondary btn-view-previous w-100 py-2" 
                                                        onclick="paymentSystem.viewPaymentDetails('${this.studentId}', '${classData.student_student_student_class_id}')">
                                                    <i class="fas fa-history me-2"></i>View Previous Payments
                                                </button>
                                            </div>
                                        ` : isFreeCard ? `
                                            <div class="free-card-section text-center p-3 bg-success bg-opacity-10 border border-success border-2 rounded">
                                                <i class="fas fa-crown fa-2x text-success mb-2"></i>
                                                <h6 class="text-success mb-1 fw-bold">Free Card</h6>
                                                <small class="text-muted">No payment required</small>
                                            </div>
                                        ` : `
                                            <div class="d-grid gap-2">
                                                <button class="btn btn-success btn-pay shadow-sm py-2" 
                                                        data-class-id="${classData.student_student_student_class_id}"
                                                        data-fee="${classFee}"
                                                        data-class-name="${classData.student_class.class_name}"
                                                        data-grade="${classData.student_class.grade.grade_name}"
                                                        data-guardian-mobile="${classData.student.guardian_mobile || ''}">
                                                    <i class="fas fa-credit-card me-2"></i>Pay Now
                                                </button>
                                                <button class="btn btn-outline-${themeColor} btn-view py-2" 
                                                        onclick="paymentSystem.viewPaymentDetails('${this.studentId}', '${classData.student_student_student_class_id}')">
                                                    <i class="fas fa-eye me-2"></i>View Details
                                                </button>
                                            </div>
                                        `}
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                });

                html += '</div>';
                classesList.innerHTML = html;
                document.getElementById('classesCard').style.display = 'block';

                // Add event listeners to Pay Now buttons
                document.querySelectorAll('.btn-pay').forEach(btn => {
                    btn.addEventListener('click', (e) => {
                        e.preventDefault();
                        const studentClassId = btn.getAttribute('data-class-id');
                        const fee = btn.getAttribute('data-fee');
                        const className = btn.getAttribute('data-class-name');
                        const grade = btn.getAttribute('data-grade');
                        const guardianMobile = btn.getAttribute('data-guardian-mobile');
                        this.openPaymentModal(studentClassId, fee, className, grade, guardianMobile);
                    });
                });
            }

            openPaymentModal(studentClassId, fee, className, grade, guardianMobile) {
                // Set form values
                document.getElementById('payment_student_id').value = this.studentId;
                document.getElementById('payment_student_class_id').value = studentClassId;
                document.getElementById('payment_guardian_mobile').value = guardianMobile;
                document.getElementById('payment_amount').value = fee;
                document.getElementById('defaultFee').textContent = this.formatCurrency(fee);

                // Set student and class info
                document.getElementById('modalStudentName').textContent = this.currentStudentData?.student?.last_name || '-';
                document.getElementById('modalStudentId').textContent = this.currentStudentData?.student?.student_custom_id || '-';
                document.getElementById('modalClassName').textContent = className;
                document.getElementById('modalGrade').textContent = grade;

                // Load attendance data
                this.loadAttendanceData();

                // Show modal
                const modal = new bootstrap.Modal(document.getElementById('paymentModal'));
                modal.show();
            }

            loadAttendanceData() {
                const studentId = document.getElementById('payment_student_id').value;
                const studentClassId = document.getElementById('payment_student_class_id').value;
                const month = document.getElementById('payment_month').value;
                const year = document.getElementById('payment_year').value;

                if (!studentId || !studentClassId) return;

                // Show loading
                document.getElementById('attendanceLoading').style.display = 'block';
                document.getElementById('attendanceInfo').style.display = 'none';
                document.getElementById('noAttendanceInfo').style.display = 'none';

                const yearMonth = `${year}-${month}`;

                fetch(`/api/attendances/monthly/${studentId}/${studentClassId}/${yearMonth}`)
                    .then(response => response.ok ? response.json() : Promise.reject('Network error'))
                    .then(data => {
                        document.getElementById('attendanceLoading').style.display = 'none';

                        if (data.status === 'success') {
                            document.getElementById('attendanceInfo').style.display = 'block';
                            document.getElementById('attendanceCount').textContent = data.data.attendance_count;
                            document.getElementById('weeksInMonth').textContent = data.data.weeks_in_month;
                            document.getElementById('attendanceMonthYear').textContent =
                                new Date(year, month - 1).toLocaleDateString('en-US', { month: 'long', year: 'numeric' });
                            document.getElementById('attendanceLastUpdated').textContent =
                                `Last updated: ${new Date().toLocaleTimeString()}`;
                        } else {
                            document.getElementById('noAttendanceInfo').style.display = 'block';
                        }
                    })
                    .catch(error => {
                        console.error('Error loading attendance:', error);
                        document.getElementById('attendanceLoading').style.display = 'none';
                        document.getElementById('noAttendanceInfo').style.display = 'block';
                    });
            }

            useDefaultFee() {
                const defaultFee = document.getElementById('defaultFee').textContent.replace(/,/g, '');
                document.getElementById('payment_amount').value = defaultFee;
            }

            async submitPayment() {
                const btn = document.getElementById('processPaymentBtn');
                const modalElement = document.getElementById('paymentModal');
                const modal = bootstrap.Modal.getInstance(modalElement);

                const originalText = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';

                try {
                    const month = document.getElementById('payment_month').value;
                    const year = document.getElementById('payment_year').value;
                    const paymentDate = document.getElementById('payment_date').value;
                    const amount = parseFloat(document.getElementById('payment_amount').value);
                    const studentId = parseInt(document.getElementById('payment_student_id').value);
                    const studentClassId = parseInt(document.getElementById('payment_student_class_id').value);
                    const guardianMobile = document.getElementById('payment_guardian_mobile').value;

                    const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 
                                       'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                    const paymentFor = `${year} ${monthNames[parseInt(month) - 1]}`;

                    const paymentData = {
                        payment_date: paymentDate,
                        payment_for: paymentFor,
                        status: 1,
                        amount: amount,
                        student_id: studentId,
                        student_student_student_classes_id: studentClassId,
                        guardian_mobile: guardianMobile
                    };

                    console.log('Sending payment data:', paymentData);

                    const response = await fetch('/api/payments', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': this.csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(paymentData)
                    });

                    const data = await response.json();

                    if (!response.ok) {
                        throw new Error(data.message || `HTTP error! status: ${response.status}`);
                    }

                    if (data.status === 'success') {
                        this.showAlert('✅ Payment processed successfully!', 'success');

                        if (modal) {
                            modal.hide();
                        }

                        document.getElementById('paymentForm').reset();
                        this.setCurrentDate();

                        setTimeout(() => {
                            if (this.currentStudentData && this.currentStudentData.student) {
                                this.searchStudent(this.currentStudentData.student.student_custom_id);
                            }
                        }, 1500);

                    } else {
                        throw new Error(data.message || 'Payment failed.');
                    }

                } catch (error) {
                    console.error('Payment error:', error);
                    
                    if (error.message.includes('Duplicate')) {
                        this.showAlert('⚠️ There is already an active payment for this month!', 'warning');
                        if (modal) {
                            modal.hide();
                        }
                    } else {
                        this.showAlert('❌ Payment error: ' + error.message, 'danger');
                    }
                } finally {
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                }
            }

            formatCurrency(amount) {
                const num = parseFloat(amount) || 0;
                return num.toLocaleString('en-US', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            }

            viewPaymentDetails(student_id, student_class_id) {
                if (student_id && student_class_id) {
                    window.location.href = `/student-payment/details/${student_id}/${student_class_id}`;
                }
            }

            showLoading(show) {
                document.getElementById('loadingSpinner').style.display = show ? 'block' : 'none';
            }

            hideStudentInfo() {
                document.getElementById('studentInfoCard').style.display = 'none';
                document.getElementById('classesCard').style.display = 'none';
            }

            showAlert(message, type) {
                let alertContainer = document.getElementById('paymentMessages');
                if (!alertContainer) {
                    alertContainer = document.createElement('div');
                    alertContainer.id = 'paymentMessages';
                    document.querySelector('.container-fluid').prepend(alertContainer);
                }

                const alertId = 'alert-' + Date.now();
                const alertDiv = document.createElement('div');
                alertDiv.id = alertId;
                alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
                alertDiv.innerHTML = `
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                `;

                alertContainer.innerHTML = '';
                alertContainer.appendChild(alertDiv);

                setTimeout(() => {
                    const alert = document.getElementById(alertId);
                    if (alert) {
                        alert.remove();
                    }
                }, 5000);
            }
        }

        // Initialize the payment system when DOM is loaded
        document.addEventListener('DOMContentLoaded', () => {
            window.paymentSystem = new PaymentSystem();
        });
    </script>
@endpush