@extends('layouts.app')

@section('title', 'Attendance Management')
@section('page-title', 'Attendance Management')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Attendance</li>
@endsection

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="container-fluid px-4">
        <!-- Modern Gradient Background -->
        <div class="position-relative mb-4">
            <div class="bg-gradient-primary rounded-4 p-4 text-white shadow-lg" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="row align-items-center">
                    <div class="col">
                        <h4 class="mb-1 fw-bold"><i class="fas fa-calendar-check me-2"></i>Attendance Management</h4>
                        <p class="mb-0 opacity-75 small">Scan QR code or search student to mark attendance</p>
                    </div>
                    <div class="col-auto">
                        <div class="bg-white bg-opacity-25 rounded-3 px-3 py-2">
                            <i class="fas fa-clock me-1"></i>
                            <span id="currentDateTime"></span>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Decorative Elements -->
            <div class="position-absolute top-0 end-0 translate-middle-y d-none d-lg-block">
                <i class="fas fa-qrcode fa-4x text-white opacity-10"></i>
            </div>
        </div>

        <!-- Success/Error Messages -->
        <div id="attendanceMessages"></div>

        <!-- Main Content Grid -->
        <div class="row g-4">
            <!-- Left Column - QR Scanner & Manual Input -->
            <div class="col-lg-5">
                <!-- QR Scanner Card -->
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                    <div class="card-header bg-white border-0 pt-4 px-4">
                        <div class="d-flex align-items-center">
                            <div class="icon-circle bg-primary bg-opacity-10 text-primary me-3">
                                <i class="fas fa-qrcode"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-0">QR Code Scanner</h6>
                                <small class="text-muted">Scan student QR code to auto-fill information</small>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="qr-scanner-container text-center p-4 bg-light rounded-3">
                            <div class="scanner-icon-wrapper mb-3">
                                <div class="scanner-pulse">
                                    <i class="fas fa-camera fa-4x text-primary opacity-75"></i>
                                </div>
                            </div>
                            <h6 class="fw-bold mb-2">Ready to Scan</h6>
                            <p class="text-muted small mb-3">Position QR code within the frame to scan</p>
                            <button class="btn btn-primary px-4 rounded-pill" id="startScanner">
                                <i class="fas fa-camera me-2"></i>Start QR Scanner
                            </button>
                            <div id="qr-reader" class="mt-3" style="display: none;"></div>
                            <div id="qr-error" class="mt-2 text-danger small" style="display: none;"></div>
                        </div>
                    </div>
                </div>

                <!-- Manual Input Card -->
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-header bg-white border-0 pt-4 px-4">
                        <div class="d-flex align-items-center">
                            <div class="icon-circle bg-success bg-opacity-10 text-success me-3">
                                <i class="fas fa-keyboard"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-0">Manual Entry</h6>
                                <small class="text-muted">Enter QR code manually</small>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="manual-input-container">
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted mb-2">
                                    <i class="fas fa-qrcode me-1"></i>QR Code / Custom ID
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0">
                                        <i class="fas fa-search text-muted"></i>
                                    </span>
                                    <input type="text" class="form-control form-control-lg bg-light border-0" 
                                           id="studentQrCode" placeholder="Enter TMP... or SA..." autocomplete="off">
                                    <button class="btn btn-success px-4" type="button" id="searchStudent">
                                        Search
                                    </button>
                                </div>
                            </div>
                            <div class="qr-type-hints d-flex gap-2">
                                <span class="badge bg-warning bg-opacity-25 text-dark px-3 py-2 rounded-pill">
                                    <i class="fas fa-clock me-1"></i>TMP... (Temporary)
                                </span>
                                <span class="badge bg-success bg-opacity-25 text-dark px-3 py-2 rounded-pill">
                                    <i class="fas fa-check-circle me-1"></i>SA... (Permanent)
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column - Student Info & Classes -->
            <div class="col-lg-7">
                <!-- Student Information Card -->
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4" id="studentInfoCard" style="display: none;">
                    <div class="card-header bg-white border-0 pt-4 px-4">
                        <div class="d-flex align-items-center">
                            <div class="icon-circle bg-info bg-opacity-10 text-info me-3">
                                <i class="fas fa-user-graduate"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-0">Student Information</h6>
                                <small class="text-muted">Student details and statistics</small>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4" id="studentDetails">
                            <!-- Student details will be loaded here -->
                        </div>
                    </div>
                </div>

                <!-- Available Classes Section -->
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden" id="ongoingClassesCard" style="display: none;">
                    <div class="card-header bg-white border-0 pt-4 px-4">
                        <div class="d-flex align-items-center">
                            <div class="icon-circle bg-warning bg-opacity-10 text-warning me-3">
                                <i class="fas fa-chalkboard-teacher"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-0">Available Classes for Attendance</h6>
                                <small class="text-muted">Classes within attendance window</small>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3" id="ongoingClassesList">
                            <!-- Available classes will be loaded here -->
                        </div>
                    </div>
                </div>

                <!-- No Classes Message -->
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden" id="noClassesCard" style="display: none;">
                    <div class="card-body text-center py-5">
                        <div class="empty-state-icon mb-3">
                            <i class="fas fa-calendar-times fa-4x text-muted opacity-25"></i>
                        </div>
                        <h6 class="fw-bold mb-2">No Classes Available</h6>
                        <p class="text-muted small mb-1">This student doesn't have any classes within the attendance window.</p>
                        <p class="text-muted small">Attendance window: 1 hour before class start → class end time</p>
                    </div>
                </div>

                <!-- Loading Spinner -->
                <div class="text-center py-5" id="loadingSpinner" style="display: none;">
                    <div class="spinner-border text-primary mb-2" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="text-muted small">Loading student information...</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Attendance Confirmation Modal - Modern Design -->
    <div class="modal fade" id="attendanceModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header bg-gradient-warning text-white border-0 rounded-top-4" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                    <h6 class="modal-title fw-bold">
                        <i class="fas fa-user-check me-2"></i>Confirm Attendance
                    </h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="text-center mb-4">
                        <div class="confirmation-icon mb-3">
                            <i class="fas fa-check-circle fa-3x text-warning"></i>
                        </div>
                        <h6 class="fw-bold mb-1">Mark Attendance?</h6>
                        <p class="text-muted small mb-0">Please confirm to mark attendance for this class</p>
                    </div>

                    <!-- Class Details Card -->
                    <div class="card bg-light border-0 rounded-3 mb-4">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center mb-3">
                                <div class="icon-circle bg-primary bg-opacity-10 text-primary me-2">
                                    <i class="fas fa-info"></i>
                                </div>
                                <span class="fw-bold small">Class Details</span>
                            </div>
                            <div class="row g-2">
                                <div class="col-6">
                                    <div class="detail-item p-2 bg-white rounded-2">
                                        <small class="text-muted d-block">Student</small>
                                        <span class="fw-semibold small" id="modalStudentName">-</span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="detail-item p-2 bg-white rounded-2">
                                        <small class="text-muted d-block">Class</small>
                                        <span class="fw-semibold small" id="modalClassName">-</span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="detail-item p-2 bg-white rounded-2">
                                        <small class="text-muted d-block">Time</small>
                                        <span class="fw-semibold small" id="modalClassTime">-</span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="detail-item p-2 bg-white rounded-2">
                                        <small class="text-muted d-block">Date</small>
                                        <span class="fw-semibold small" id="modalClassDate">-</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tute Option -->
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="markTuteCheckbox">
                        <label class="form-check-label fw-medium" for="markTuteCheckbox">
                            Mark tute for this month
                        </label>
                        <small class="text-muted d-block">Check if student received tute materials</small>
                    </div>

                    <!-- SMS Notification -->
                    <div class="alert alert-info bg-info bg-opacity-10 border-0 rounded-3 py-2" id="smsNotificationInfo" style="display: none;">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-sms text-info me-2"></i>
                            <span class="small" id="smsStatusText">SMS will be sent to guardian</span>
                        </div>
                    </div>

                    <form id="attendanceForm">
                        @csrf
                        <input type="hidden" id="attendance_student_id" name="student_id">
                        <input type="hidden" id="attendance_student_class_id" name="student_student_student_classes_id">
                        <input type="hidden" id="attendance_attendance_id" name="attendance_id">
                        <input type="hidden" id="attendance_class_category_id" name="class_category_has_student_class_id">
                        <input type="hidden" id="attendance_guardian_mobile" name="guardian_mobile">
                        <input type="hidden" id="attendance_tute" name="tute" value="0">
                    </form>
                </div>
                <div class="modal-footer bg-light border-0 rounded-bottom-4 p-3">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancel
                    </button>
                    <button type="button" class="btn btn-success rounded-pill px-4" id="confirmAttendanceBtn">
                        <i class="fas fa-check me-2"></i>Confirm
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        /* Modern Design Styles */
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --warning-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }

        .bg-gradient-primary {
            background: var(--primary-gradient);
        }

        .bg-gradient-warning {
            background: var(--warning-gradient);
        }

        .icon-circle {
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 16px;
            font-size: 1.25rem;
        }

        .qr-scanner-container {
            position: relative;
            transition: all 0.3s ease;
        }

        .scanner-pulse {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
                opacity: 1;
            }
            50% {
                transform: scale(1.1);
                opacity: 0.7;
            }
            100% {
                transform: scale(1);
                opacity: 1;
            }
        }

        /* Class Card Design */
        .class-card {
            border: none;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .class-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #667eea, #764ba2);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .class-card:hover::before {
            opacity: 1;
        }

        .class-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1) !important;
        }

        .ongoing-badge {
            position: absolute;
            top: 12px;
            right: 12px;
            animation: blink 1.5s infinite;
        }

        @keyframes blink {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        .time-badge {
            background: linear-gradient(135deg, #ff6b6b 0%, #ff8e53 100%);
            color: white;
            font-size: 11px;
            padding: 4px 8px;
            border-radius: 6px;
        }

        .date-badge {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
            font-size: 11px;
            padding: 4px 8px;
            border-radius: 6px;
        }

        .attendance-time-window {
            background: linear-gradient(135deg, #f6f9fc 0%, #e6f2ff 100%);
            border-left: 3px solid #4facfe;
            padding: 8px 12px;
            border-radius: 8px;
            margin: 12px 0;
        }

        .info-item {
            border-radius: 8px;
            padding: 6px 10px;
            font-size: 11px;
            transition: all 0.2s ease;
        }

        .info-item:hover {
            transform: translateX(3px);
        }

        .payment-info {
            background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
            border: 1px solid #90caf9;
        }

        .tute-info {
            background: linear-gradient(135deg, #f3e5f5 0%, #e1bee7 100%);
            border: 1px solid #ce93d8;
        }

        .attendance-info {
            background: linear-gradient(135deg, #e8f5e8 0%, #c8e6c9 100%);
            border: 1px solid #a5d6a7;
        }

        .avatar-sm {
            width: 80px;
            height: 80px;
            border: 3px solid white;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .avatar-sm:hover {
            transform: scale(1.05);
        }

        .qr-type-badge {
            font-size: 10px;
            padding: 4px 8px;
            border-radius: 20px;
        }

        .temp-qr {
            background: #fff3cd;
            color: #856404;
        }

        .perm-qr {
            background: #d4edda;
            color: #155724;
        }

        .attendance-btn {
            border-radius: 8px;
            font-weight: 600;
            font-size: 12px;
            padding: 8px 16px;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .attendance-btn:not(:disabled):hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .empty-state-icon {
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .avatar-sm {
                width: 60px;
                height: 60px;
            }
            
            .icon-circle {
                width: 40px;
                height: 40px;
                font-size: 1rem;
            }
        }

        /* Loading Spinner */
        .spinner-border {
            width: 3rem;
            height: 3rem;
        }

        /* Modal Styles */
        .modal-content {
            border: none;
        }

        .rounded-top-4 {
            border-top-left-radius: 1rem !important;
            border-top-right-radius: 1rem !important;
        }

        .rounded-bottom-4 {
            border-bottom-left-radius: 1rem !important;
            border-bottom-right-radius: 1rem !important;
        }

        .bg-opacity-10 {
            --bs-bg-opacity: 0.1;
        }

        .bg-opacity-25 {
            --bs-bg-opacity: 0.25;
        }

        /* Form Controls */
        .form-control:focus {
            box-shadow: none;
            border-color: #667eea;
        }

        .input-group-text {
            border-radius: 10px 0 0 10px;
        }

        .form-control-lg {
            border-radius: 0 10px 10px 0;
        }

        .btn-lg {
            border-radius: 10px;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <script>
        class AttendanceSystem {
            constructor() {
                this.studentId = null;
                this.currentStudentData = null;
                this.qrScanner = null;
                this.csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                this.init();
                this.updateDateTime();
            }

            updateDateTime() {
                const update = () => {
                    const now = new Date();
                    const options = { 
                        hour: '2-digit', 
                        minute: '2-digit', 
                        second: '2-digit',
                        day: '2-digit',
                        month: '2-digit',
                        year: 'numeric'
                    };
                    document.getElementById('currentDateTime').textContent = now.toLocaleString('en-US', options);
                };
                update();
                setInterval(update, 1000);
            }

            init() {
                this.setupEventListeners();
            }

            setupEventListeners() {
                const startScannerBtn = document.getElementById('startScanner');
                if (startScannerBtn) {
                    startScannerBtn.addEventListener('click', () => this.toggleQRScanner());
                }

                const searchStudentBtn = document.getElementById('searchStudent');
                if (searchStudentBtn) {
                    searchStudentBtn.addEventListener('click', () => this.searchStudent());
                }

                const studentQrCodeInput = document.getElementById('studentQrCode');
                if (studentQrCodeInput) {
                    studentQrCodeInput.addEventListener('keypress', (e) => {
                        if (e.key === 'Enter') this.searchStudent();
                    });
                }

                const confirmAttendanceBtn = document.getElementById('confirmAttendanceBtn');
                if (confirmAttendanceBtn) {
                    confirmAttendanceBtn.addEventListener('click', () => this.markAttendance());
                }

                const markTuteCheckbox = document.getElementById('markTuteCheckbox');
                if (markTuteCheckbox) {
                    markTuteCheckbox.addEventListener('change', (e) => {
                        document.getElementById('attendance_tute').value = e.target.checked ? '1' : '0';
                    });
                }
            }

            async toggleQRScanner() {
                const scannerBtn = document.getElementById('startScanner');
                const qrReader = document.getElementById('qr-reader');
                const qrError = document.getElementById('qr-error');

                if (!scannerBtn || !qrReader) return;

                if (this.qrScanner) {
                    this.stopQRScanner();
                    scannerBtn.innerHTML = '<i class="fas fa-camera me-2"></i>Start QR Scanner';
                    return;
                }

                try {
                    scannerBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Loading...';

                    if (typeof Html5QrcodeScanner === 'undefined') {
                        throw new Error('QR Scanner library not available');
                    }

                    qrReader.style.display = 'block';
                    if (qrError) qrError.style.display = 'none';
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
                    if (qrError) {
                        qrError.textContent = 'Unable to start QR scanner. Please refresh and try again.';
                        qrError.style.display = 'block';
                    }
                    if (scannerBtn) {
                        scannerBtn.innerHTML = '<i class="fas fa-camera me-2"></i>Start QR Scanner';
                    }
                    this.qrScanner = null;
                }
            }

            stopQRScanner() {
                if (this.qrScanner) {
                    this.qrScanner.clear().then(() => {
                        const qrReader = document.getElementById('qr-reader');
                        if (qrReader) qrReader.style.display = 'none';
                        this.qrScanner = null;
                    }).catch(console.error);
                }
            }

            onQRCodeScanned(decodedText) {
                const studentQrCodeInput = document.getElementById('studentQrCode');
                if (studentQrCodeInput) {
                    studentQrCodeInput.value = decodedText.trim();
                }
                this.searchStudent(decodedText.trim());
                this.stopQRScanner();
            }

            async searchStudent(qrCode = null) {
                try {
                    const searchQrCode = qrCode || document.getElementById('studentQrCode')?.value.trim();

                    if (!searchQrCode) {
                        this.showAlert('Please enter or scan a QR code', 'warning');
                        return;
                    }

                    this.showLoading(true);
                    this.hideStudentInfo();

                    const url = `/api/attendances/read-attendance?qr_code=${encodeURIComponent(searchQrCode)}`;
                    
                    const response = await fetch(url, {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': this.csrfToken
                        }
                    });

                    const data = await response.json();

                    if (!response.ok) {
                        throw new Error(data.message || 'Failed to fetch student information');
                    }

                    if (data.status === 'success') {
                        this.studentId = data.student_id;
                        this.currentStudentData = data;
                        this.displayStudentInfo(data);
                        this.displayOngoingClasses(data.data);
                        this.showAlert('Student information loaded successfully', 'success');
                    } else {
                        throw new Error(data.message || 'No data found');
                    }

                } catch (error) {
                    console.error('Search error:', error);
                    this.showAlert('Error: ' + error.message, 'danger');
                } finally {
                    this.showLoading(false);
                }
            }

            displayStudentInfo(data) {
                if (!data.data || data.data.length === 0) return;

                const firstClass = data.data[0];
                const student = firstClass.student;
                
                if (!student) return;

                const studentDetails = document.getElementById('studentDetails');
                if (!studentDetails) return;

                const qrCode = document.getElementById('studentQrCode')?.value || '';
                const isTemporary = qrCode.startsWith('TMP');
                
                const qrTypeBadge = isTemporary ? 
                    '<span class="qr-type-badge temp-qr"><i class="fas fa-clock me-1"></i>Temporary QR</span>' : 
                    '<span class="qr-type-badge perm-qr"><i class="fas fa-check-circle me-1"></i>Permanent QR</span>';

                const paymentInfo = firstClass.payment_info;
                const paymentHTML = paymentInfo ? `
                    <div class="info-item payment-info d-flex align-items-center mb-2">
                        <i class="fas fa-money-bill-wave fa-sm me-2 text-primary"></i>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">Last Payment</small>
                                <span class="badge ${paymentInfo.payment_status ? 'bg-success' : 'bg-danger'} info-badge">
                                    ${paymentInfo.payment_status ? 'Paid' : 'Pending'}
                                </span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-bold">Rs. ${paymentInfo.last_payment_amount || 0}</span>
                                <small class="text-muted">${paymentInfo.last_payment_date || 'No payment'}</small>
                            </div>
                        </div>
                    </div>
                ` : '';

                const tuteInfo = firstClass.tute_info;
                const tuteHTML = tuteInfo ? `
                    <div class="info-item tute-info d-flex align-items-center mb-2">
                        <i class="fas fa-book fa-sm me-2 text-purple"></i>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">Tute Status</small>
                                <span class="badge ${tuteInfo.has_tute_for_this_month ? 'bg-success' : 'bg-warning'} info-badge">
                                    ${tuteInfo.has_tute_for_this_month ? '✓ Received' : '✗ Not Received'}
                                </span>
                            </div>
                            <small class="text-muted">${tuteInfo.current_month}</small>
                        </div>
                    </div>
                ` : '';

                const attendanceInfo = firstClass.attendance_info;
                const attendanceHTML = attendanceInfo ? `
                    <div class="info-item attendance-info d-flex align-items-center">
                        <i class="fas fa-calendar-check fa-sm me-2 text-success"></i>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">Attendance</small>
                                <span class="badge bg-info info-badge">${attendanceInfo.current_month}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-bold">${attendanceInfo.attendance_count_this_month_total || 0} Classes</span>
                                <small class="text-muted">this month</small>
                            </div>
                        </div>
                    </div>
                ` : '';

                studentDetails.innerHTML = `
                    <div class="col-lg-3 text-center">
                        <img src="${student.img_url || '/uploads/logo/logo.png'}" alt="Student Photo" 
                             class="img-thumbnail rounded-circle avatar-sm"
                             onerror="this.src='/uploads/logo/logo.png'">
                    </div>
                    <div class="col-lg-9">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h5 class="fw-bold mb-0">${student.last_name}</h5>
                            ${qrTypeBadge}
                        </div>
                        <div class="row g-2 mb-2">
                            <div class="col-6">
                                <small class="text-muted d-block">Student ID</small>
                                <span class="fw-semibold">${student.custom_id}</span>
                            </div>
                            <div class="col-6">
                                <small class="text-muted d-block">Guardian Mobile</small>
                                <span class="fw-semibold">${student.guardian_mobile || 'Not available'}</span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                ${paymentHTML}
                                ${tuteHTML}
                                ${attendanceHTML}
                            </div>
                        </div>
                    </div>
                `;

                const studentInfoCard = document.getElementById('studentInfoCard');
                if (studentInfoCard) studentInfoCard.style.display = 'block';
            }

            displayOngoingClasses(classesData) {
                const classesList = document.getElementById('ongoingClassesList');
                const ongoingClassesCard = document.getElementById('ongoingClassesCard');
                const noClassesCard = document.getElementById('noClassesCard');

                if (!classesList || !ongoingClassesCard || !noClassesCard) return;

                if (!classesData || classesData.length === 0) {
                    noClassesCard.style.display = 'block';
                    ongoingClassesCard.style.display = 'none';
                    return;
                }

                let html = '';

                classesData.forEach((classData) => {
                    const ongoingClass = classData.ongoing_class;
                    const studentStatus = classData.studentStudentStudentClass?.student_class_status;
                    const attendanceInfo = classData.attendance_info || {};
                    const tuteInfo = classData.tute_info || {};

                    if (!ongoingClass) return;

                    const canMarkAttendance = studentStatus == 1;
                    const startTime = ongoingClass.start_time;
                    const endTime = ongoingClass.end_time;
                    
                    const startTimeFormatted = this.formatTime(startTime);
                    const endTimeFormatted = this.formatTime(endTime);
                    const oneHourBefore = this.getOneHourBefore(startTime);
                    const classAttendanceCount = attendanceInfo.attendance_count_for_this_class || 0;
                    const hasTuteForClass = tuteInfo.has_tute_for_this_month || false;

                    html += `
                        <div class="col-xl-4 col-lg-6 col-md-6">
                            <div class="class-card card h-100 border-0 shadow-sm">
                                <div class="card-header bg-white border-0 pt-3 px-3">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="fw-bold mb-0">${classData.category_name}</h6>
                                            <small class="text-muted">${classData.student_class_name}</small>
                                        </div>
                                        ${ongoingClass.is_ongoing == 1 ?
                                            `<span class="ongoing-badge badge bg-danger rounded-pill">
                                                <i class="fas fa-circle fa-xs me-1"></i>LIVE
                                            </span>` : ''
                                        }
                                    </div>
                                </div>

                                <div class="card-body p-3">
                                    <div class="attendance-time-window">
                                        <div class="d-flex justify-content-between mb-1">
                                            <small class="text-muted">Attendance Window</small>
                                            <small class="text-muted">Now: ${ongoingClass.current_time}</small>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="text-center">
                                                <div class="fw-bold">${oneHourBefore}</div>
                                                <small class="text-muted">Start</small>
                                            </div>
                                            <i class="fas fa-arrow-right text-muted"></i>
                                            <div class="text-center">
                                                <div class="fw-bold">${endTimeFormatted}</div>
                                                <small class="text-muted">End</small>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row g-2 mb-2">
                                        <div class="col-6">
                                            <div class="time-badge text-center">
                                                <i class="fas fa-clock me-1"></i>
                                                ${startTimeFormatted} - ${endTimeFormatted}
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="date-badge text-center">
                                                <i class="fas fa-calendar me-1"></i>
                                                ${ongoingClass.date}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row g-1 mb-2">
                                        <div class="col-6">
                                            <div class="info-item attendance-info text-center p-2">
                                                <small class="text-muted d-block">Attendance</small>
                                                <span class="fw-bold">${classAttendanceCount}</span>
                                                <small class="text-muted">this month</small>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="info-item tute-info text-center p-2">
                                                <small class="text-muted d-block">Tute</small>
                                                <span class="badge ${hasTuteForClass ? 'bg-success' : 'bg-warning'} rounded-pill">
                                                    ${hasTuteForClass ? '✓' : '✗'}
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="class-info mb-2">
                                        <div class="d-flex align-items-center mb-1">
                                            <i class="fas fa-door-open text-info me-2"></i>
                                            <small class="fw-semibold">${ongoingClass.class_hall_name || 'Hall #' + ongoingClass.class_hall_id}</small>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <i class="fas ${canMarkAttendance ? 'fa-user-check text-success' : 'fa-user-times text-danger'} me-2"></i>
                                            <small class="fw-semibold ${canMarkAttendance ? 'text-success' : 'text-danger'}">
                                                ${canMarkAttendance ? 'Active Enrollment' : 'Inactive Enrollment'}
                                            </small>
                                        </div>
                                    </div>

                                    <button class="btn ${canMarkAttendance ? 'btn-success' : 'btn-secondary'} attendance-btn w-100 mt-2" 
                                            data-student-id="${classData.student.id}"
                                            data-student-class-id="${classData.studentStudentStudentClass?.student_student_student_class_id}"
                                            data-attendance-id="${ongoingClass.id}"
                                            data-class-category-id="${ongoingClass.class_category_has_student_class_id}"
                                            data-student-name="${classData.student.last_name}"
                                            data-class-name="${classData.student_class_name}"
                                            data-class-time="${startTimeFormatted} - ${endTimeFormatted}"
                                            data-class-date="${ongoingClass.date}"
                                            data-guardian-mobile="${classData.student.guardian_mobile || ''}"
                                            data-has-tute="${hasTuteForClass}"
                                            ${!canMarkAttendance ? 'disabled' : ''}>
                                        <i class="fas ${canMarkAttendance ? 'fa-user-check' : 'fa-ban'} me-2"></i>
                                        ${canMarkAttendance ? 'Mark Attendance' : 'Disabled'}
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;
                });

                classesList.innerHTML = html;
                ongoingClassesCard.style.display = 'block';
                noClassesCard.style.display = 'none';

                this.setupAttendanceButtonListeners();
            }

            formatTime(timeString) {
                if (!timeString) return '';
                if (timeString.includes(':')) {
                    const [hours, minutes] = timeString.split(':');
                    const hour = parseInt(hours);
                    const ampm = hour >= 12 ? 'PM' : 'AM';
                    const hour12 = hour % 12 || 12;
                    return `${hour12}:${minutes.substring(0,2)} ${ampm}`;
                }
                return timeString;
            }

            getOneHourBefore(timeString) {
                if (!timeString) return '';
                
                let hour, minute;
                
                if (timeString.includes(':')) {
                    const parts = timeString.split(':');
                    hour = parseInt(parts[0]);
                    minute = parts[1].substring(0, 2);
                    hour -= 1;
                    if (hour < 0) hour = 23;
                    const ampm = hour >= 12 ? 'PM' : 'AM';
                    const hour12 = hour % 12 || 12;
                    return `${hour12}:${minute} ${ampm}`;
                }
                
                return timeString;
            }

            setupAttendanceButtonListeners() {
                document.querySelectorAll('.attendance-btn:not(:disabled)').forEach(btn => {
                    btn.addEventListener('click', (e) => {
                        const studentId = e.currentTarget.getAttribute('data-student-id');
                        const studentClassId = e.currentTarget.getAttribute('data-student-class-id');
                        const attendanceId = e.currentTarget.getAttribute('data-attendance-id');
                        const classCategoryId = e.currentTarget.getAttribute('data-class-category-id');
                        const studentName = e.currentTarget.getAttribute('data-student-name');
                        const className = e.currentTarget.getAttribute('data-class-name');
                        const classTime = e.currentTarget.getAttribute('data-class-time');
                        const classDate = e.currentTarget.getAttribute('data-class-date');
                        const guardianMobile = e.currentTarget.getAttribute('data-guardian-mobile');
                        const hasTute = e.currentTarget.getAttribute('data-has-tute') === 'true';

                        this.openAttendanceModal(
                            studentId,
                            studentClassId,
                            attendanceId,
                            classCategoryId,
                            studentName,
                            className,
                            classTime,
                            classDate,
                            guardianMobile,
                            hasTute
                        );
                    });
                });
            }

            openAttendanceModal(studentId, studentClassId, attendanceId, classCategoryId, studentName, className, classTime, classDate, guardianMobile, hasTute) {
                document.getElementById('attendance_student_id').value = studentId || '';
                document.getElementById('attendance_student_class_id').value = studentClassId || '';
                document.getElementById('attendance_attendance_id').value = attendanceId || '';
                document.getElementById('attendance_class_category_id').value = classCategoryId || '';
                document.getElementById('attendance_guardian_mobile').value = guardianMobile || '';
                
                const markTuteCheckbox = document.getElementById('markTuteCheckbox');
                const attendanceTute = document.getElementById('attendance_tute');
                
                if (markTuteCheckbox) {
                    if (hasTute) {
                        markTuteCheckbox.checked = false;
                        markTuteCheckbox.disabled = true;
                        markTuteCheckbox.parentElement.classList.add('text-muted');
                    } else {
                        markTuteCheckbox.checked = false;
                        markTuteCheckbox.disabled = false;
                        markTuteCheckbox.parentElement.classList.remove('text-muted');
                    }
                }
                
                if (attendanceTute) {
                    attendanceTute.value = '0';
                }

                document.getElementById('modalStudentName').textContent = studentName || '-';
                document.getElementById('modalClassName').textContent = className || '-';
                document.getElementById('modalClassTime').textContent = classTime || '-';
                document.getElementById('modalClassDate').textContent = classDate || '-';

                const smsInfo = document.getElementById('smsNotificationInfo');
                const smsStatusText = document.getElementById('smsStatusText');

                if (smsInfo && smsStatusText) {
                    if (guardianMobile && guardianMobile.length >= 10) {
                        smsInfo.style.display = 'block';
                        smsStatusText.textContent = `SMS will be sent to ${guardianMobile}`;
                    } else {
                        smsInfo.style.display = 'none';
                    }
                }

                const modalElement = document.getElementById('attendanceModal');
                if (modalElement) {
                    const modal = new bootstrap.Modal(modalElement);
                    modal.show();
                }
            }

            async markAttendance() {
                const btn = document.getElementById('confirmAttendanceBtn');
                const modalElement = document.getElementById('attendanceModal');
                const modal = bootstrap.Modal.getInstance(modalElement);

                if (!btn) return;

                const originalText = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';

                try {
                    const formData = {
                        student_id: document.getElementById('attendance_student_id')?.value,
                        student_student_student_classes_id: document.getElementById('attendance_student_class_id')?.value,
                        attendance_id: document.getElementById('attendance_attendance_id')?.value,
                        class_category_has_student_class_id: document.getElementById('attendance_class_category_id')?.value,
                        guardian_mobile: document.getElementById('attendance_guardian_mobile')?.value,
                        tute: document.getElementById('attendance_tute')?.value === '1'
                    };

                    if (!formData.student_id || !formData.student_student_student_classes_id || !formData.attendance_id) {
                        throw new Error('Missing required attendance data');
                    }

                    const response = await fetch('/api/attendances/', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': this.csrfToken
                        },
                        body: JSON.stringify(formData)
                    });

                    const data = await response.json();

                    if (!response.ok) {
                        if (response.status === 409) {
                            this.showAlert('⚠️ ' + data.message, 'warning');
                            if (modal) modal.hide();
                            return;
                        }
                        if (response.status === 422) {
                            const errors = data.errors || {};
                            const errorMessages = Object.values(errors).flat().join(', ');
                            throw new Error(`Validation failed: ${errorMessages}`);
                        }
                        throw new Error(data.message || `HTTP error! status: ${response.status}`);
                    }

                    if (data.status === 'success') {
                        let message = '✅ Attendance marked successfully!';
                        if (data.tute_marked) {
                            message += ' Tute marked for this month.';
                        }
                        this.showAlert(message, 'success');

                        if (modal) modal.hide();

                        setTimeout(() => {
                            const qrCode = document.getElementById('studentQrCode')?.value;
                            if (qrCode) this.searchStudent(qrCode);
                        }, 1500);
                    } else {
                        throw new Error(data.message || 'Attendance marking failed.');
                    }

                } catch (error) {
                    console.error('Attendance error:', error);

                    if (error.message.includes('duplicate') || error.message.includes('Duplicate')) {
                        this.showAlert('⚠️ Attendance already marked for this class!', 'warning');
                    } else if (error.message.includes('validation') || error.message.includes('Validation')) {
                        this.showAlert(`❌ ${error.message}`, 'danger');
                    } else {
                        this.showAlert('❌ Error: ' + error.message, 'danger');
                    }
                } finally {
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                }
            }

            showLoading(show) {
                const loadingSpinner = document.getElementById('loadingSpinner');
                if (loadingSpinner) {
                    loadingSpinner.style.display = show ? 'block' : 'none';
                }
            }

            hideStudentInfo() {
                const studentInfoCard = document.getElementById('studentInfoCard');
                const ongoingClassesCard = document.getElementById('ongoingClassesCard');
                const noClassesCard = document.getElementById('noClassesCard');

                if (studentInfoCard) studentInfoCard.style.display = 'none';
                if (ongoingClassesCard) ongoingClassesCard.style.display = 'none';
                if (noClassesCard) noClassesCard.style.display = 'none';
            }

            showAlert(message, type) {
                let alertContainer = document.getElementById('attendanceMessages');
                if (!alertContainer) {
                    alertContainer = document.createElement('div');
                    alertContainer.id = 'attendanceMessages';
                    const container = document.querySelector('.container-fluid');
                    if (container) {
                        container.prepend(alertContainer);
                    }
                }

                const alertId = 'alert-' + Date.now();
                const alertDiv = document.createElement('div');
                alertDiv.id = alertId;
                alertDiv.className = `alert alert-${type} alert-dismissible fade show py-2`;
                alertDiv.innerHTML = `
                    <span class="small">${message}</span>
                    <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert"></button>
                `;

                alertContainer.innerHTML = '';
                alertContainer.appendChild(alertDiv);

                setTimeout(() => {
                    const alert = document.getElementById(alertId);
                    if (alert) alert.remove();
                }, 5000);
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            window.attendanceSystem = new AttendanceSystem();
        });
    </script>
@endpush