@extends('layouts.app')

@section('title', 'Student Details')
@section('page-title', 'Student Details')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('students.index') }}">Students</a></li>
    <li class="breadcrumb-item active">Show Student</li>
@endsection

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-user-graduate me-2"></i>Student Details
                    </h5>
                    <div class="no-print">
                        <button class="btn btn-warning btn-sm me-2" id="editStudentBtn">
                            <i class="fas fa-edit me-1"></i>Edit Student
                        </button>
                        <button class="btn btn-info btn-sm me-2" id="addClassBtn">
                            <i class="fas fa-plus-circle me-1"></i>Add Class
                        </button>
                        <button class="btn btn-success btn-sm" id="viewAnalyticBtn">
                            <i class="fa fa-line-chart me-1" aria-hidden="true"></i>View Analytic
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Loading Spinner -->
                    <div id="loadingSpinner" class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Loading student details...</p>
                    </div>

                    <!-- Student Details -->
                    <div id="studentDetails" style="display: none;">
                        <div class="row">
                            <!-- Student Photo & Basic Info -->
                            <div class="col-md-4 mb-4">
                                <div class="card h-100">
                                    <div class="card-header bg-secondary text-white">
                                        <strong>Student Photo</strong>
                                    </div>
                                    <div class="card-body text-center">
                                        <img id="studentPhoto" class="img-thumbnail rounded-circle mb-3"
                                            style="width: 200px; height: 200px; object-fit: cover;"
                                            onerror="this.onerror=null; this.src='/uploads/logo/logo.png'">
                                        <h4 id="studentName" class="mb-1"></h4>
                                        <h5 class="text-primary mb-2" id="studentId"></h5>
                                        <p class="text-muted mb-1" id="studentGrade"></p>
                                        <p class="text-muted mb-0" id="studentStatus"></p>
                                        <!-- Portal Username Display -->
                                        <div class="mt-3" id="portalUsernameSection" style="display: none;">
                                            <hr>
                                            <p class="mb-1"><strong>Portal Username:</strong></p>
                                            <span class="badge bg-info" id="portalUsernameBadge">Loading...</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Personal Information -->
                            <div class="col-md-8">
                                <div class="card mb-4">
                                    <div class="card-header bg-primary text-white">
                                        <strong><i class="fas fa-user me-2"></i>Personal Information</strong>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6 mb-2">
                                                <strong>Full Name:</strong> <span id="fullName"></span>
                                            </div>
                                            <div class="col-md-6 mb-2">
                                                <strong>Initial Name:</strong> <span id="initialName"></span>
                                            </div>
                                            <div class="col-md-6 mb-2">
                                                <strong>Mobile:</strong> <span id="mobile"></span>
                                            </div>
                                            <div class="col-md-6 mb-2">
                                                <strong>WhatsApp:</strong> <span id="whatsapp_mobile"></span>
                                            </div>
                                            <div class="col-md-6 mb-2">
                                                <strong>Email:</strong> <span id="email"></span>
                                            </div>
                                            <div class="col-md-6 mb-2">
                                                <strong>NIC:</strong> <span id="nic"></span>
                                            </div>
                                            <div class="col-md-6 mb-2">
                                                <strong>Birthday:</strong> <span id="bday"></span>
                                            </div>
                                            <div class="col-md-6 mb-2">
                                                <strong>Gender:</strong> <span id="gender"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Address Information -->
                                <div class="card mb-4">
                                    <div class="card-header bg-success text-white">
                                        <strong><i class="fas fa-home me-2"></i>Address Information</strong>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-12 mb-2">
                                                <strong>Address Line 1:</strong> <span id="address1"></span>
                                            </div>
                                            <div class="col-12 mb-2">
                                                <strong>Address Line 2:</strong> <span id="address2"></span>
                                            </div>
                                            <div class="col-12 mb-2">
                                                <strong>Address Line 3:</strong> <span id="address3"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Guardian Information -->
                        <div class="row">
                            <div class="col-12">
                                <div class="card mb-4">
                                    <div class="card-header bg-info text-white">
                                        <strong><i class="fas fa-users me-2"></i>Guardian Information</strong>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6 mb-2">
                                                <strong>Guardian First Name:</strong> <span id="guardian_fname"></span>
                                            </div>
                                            <div class="col-md-6 mb-2">
                                                <strong>Guardian Last Name:</strong> <span id="guardian_lname"></span>
                                            </div>
                                            <div class="col-md-6 mb-2">
                                                <strong>Guardian Mobile:</strong> <span id="guardian_mobile"></span>
                                            </div>
                                            <div class="col-md-6 mb-2">
                                                <strong>Guardian NIC:</strong> <span id="guardian_nic"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Academic Information -->
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header bg-warning text-dark">
                                        <strong><i class="fas fa-graduation-cap me-2"></i>Academic Information</strong>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6 mb-2">
                                                <strong>Grade:</strong> <span id="gradeName"></span>
                                            </div>
                                            <div class="col-md-6 mb-2">
                                                <strong>Class Type:</strong>
                                                <span id="classType" class="badge fs-6"></span>
                                            </div>
                                            <div class="col-md-6 mb-2">
                                                <strong>School:</strong> <span id="student_school"></span>
                                            </div>
                                            <div class="col-md-6 mb-2">
                                                <strong>Admission Status:</strong> <span id="admissionStatus"></span>
                                            </div>
                                            <div class="col-md-6 mb-2">
                                                <strong>Permanent QR Active:</strong> <span id="permanentQrActive"></span>
                                            </div>
                                            <div class="col-md-6 mb-2">
                                                <strong>Student Disabled:</strong> <span id="studentDisabled"></span>
                                            </div>
                                            <div class="col-md-6 mb-2">
                                                <strong>Registration Date:</strong> <span id="createdAt"></span>
                                            </div>
                                            <div class="col-md-6 mb-2">
                                                <strong>Status:</strong> <span id="activeStatus"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- QR Code Information -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card border-info">
                                    <div class="card-header bg-info text-white">
                                        <strong><i class="fas fa-qrcode me-2"></i>QR Code Information</strong>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6 mb-2">
                                                <strong>Temporary QR Code:</strong> <span id="temporary_qr_code"></span>
                                            </div>
                                            <div class="col-md-6 mb-2">
                                                <strong>QR Expire Date:</strong> <span id="temporary_qr_code_expire_date"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Send Credentials Section -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card border-primary">
                                    <div class="card-header bg-primary text-white">
                                        <strong><i class="fas fa-key me-2"></i>Portal Access</strong>
                                    </div>
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col-md-8">
                                                <p class="mb-2">
                                                    <strong>Portal Status:</strong>
                                                    <span id="portalAccessStatus" class="badge bg-secondary">Checking...</span>
                                                </p>
                                                <div id="portalCredentialInfo" class="text-muted">
                                                    Send login credentials to the student via email/SMS.
                                                </div>
                                            </div>
                                            <div class="col-md-4 text-end">
                                                <button class="btn btn-danger btn-lg" id="sendCredentialsBtn">
                                                    <i class="fas fa-paper-plane me-2"></i>Send User Credentials
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Error Message -->
                    <div id="errorMessage" class="text-center py-5" style="display: none;">
                        <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i>
                        <h4 class="text-danger">Failed to Load Student Details</h4>
                        <p class="text-muted" id="errorText"></p>
                        <a href="{{ route('students.index') }}" class="btn btn-primary mt-3">
                            <i class="fas fa-arrow-left me-2"></i>Back to Students
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // DOM Elements
            const loadingSpinner = document.getElementById('loadingSpinner');
            const studentDetails = document.getElementById('studentDetails');
            const errorMessage = document.getElementById('errorMessage');
            const errorText = document.getElementById('errorText');
            const editStudentBtn = document.getElementById('editStudentBtn');
            const addClassBtn = document.getElementById('addClassBtn');
            const viewAnalyticBtn = document.getElementById('viewAnalyticBtn');
            const portalUsernameSection = document.getElementById('portalUsernameSection');
            const portalUsernameBadge = document.getElementById('portalUsernameBadge');
            const sendCredentialsBtn = document.getElementById('sendCredentialsBtn');
            const portalAccessStatus = document.getElementById('portalAccessStatus');
            const portalCredentialInfo = document.getElementById('portalCredentialInfo');

            let studentId = null;
            let studentData = null;

            // Get student ID from URL
            const pathSegments = window.location.pathname.split('/');
            const urlStudentId = pathSegments[pathSegments.length - 1];

            // Load student details
            loadStudentDetails(urlStudentId);

            // Event Listeners
            editStudentBtn.addEventListener('click', function() {
                if (studentId) {
                    window.location.href = `/students/${studentId}/edit`;
                } else {
                    showAlert('Student ID not found', 'error');
                }
            });

            addClassBtn.addEventListener('click', function() {
                if (studentId) {
                    window.location.href = `/students/add_student_to_single_class/${studentId}`;
                } else {
                    showAlert('Student ID not found', 'error');
                }
            });

            viewAnalyticBtn.addEventListener('click', function() {
                if (studentId) {
                    window.location.href = `/students/student_analytic/${studentId}`;
                } else {
                    showAlert('Student ID not found', 'error');
                }
            });

            sendCredentialsBtn.addEventListener('click', function() {
                if (studentId) {
                    sendStudentCredentials();
                } else {
                    showAlert('Student ID not found', 'error');
                }
            });

            // Load Student Details Function
            async function loadStudentDetails(id) {
                try {
                    // Show loading spinner
                    loadingSpinner.style.display = 'block';
                    studentDetails.style.display = 'none';
                    errorMessage.style.display = 'none';

                    const response = await fetch(`/api/students/${id}`, {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });

                    const result = await response.json();

                    if (result.status === 'success' && result.data) {
                        studentData = result.data;
                        studentId = studentData.id;
                        displayStudentDetails(studentData);
                    } else {
                        throw new Error(result.message || 'Failed to load student details');
                    }
                } catch (error) {
                    console.error('Error loading student:', error);
                    showError(error.message);
                } finally {
                    loadingSpinner.style.display = 'none';
                }
            }

            // Display Student Details Function
            function displayStudentDetails(student) {
                // Student Photo & Basic Info
                const studentPhoto = document.getElementById('studentPhoto');
                if (student.img_url && student.img_url !== 'null' && student.img_url !== 'undefined') {
                    studentPhoto.src = student.img_url;
                } else {
                    studentPhoto.src = '/uploads/logo/logo.png';
                }

                document.getElementById('studentName').textContent = student.initial_name || student.full_name || 'N/A';
                document.getElementById('studentId').textContent = student.custom_id || 'N/A';
                document.getElementById('studentGrade').textContent = student.grade?.grade_name ? `Grade ${student.grade.grade_name}` : 'Grade N/A';
                document.getElementById('studentStatus').innerHTML = student.is_active ?
                    '<span class="badge bg-success">Active</span>' :
                    '<span class="badge bg-danger">Inactive</span>';

                // Personal Information
                document.getElementById('fullName').textContent = student.full_name || 'N/A';
                document.getElementById('initialName').textContent = student.initial_name || 'N/A';
                document.getElementById('mobile').textContent = student.mobile || 'N/A';
                document.getElementById('whatsapp_mobile').textContent = student.whatsapp_mobile || 'N/A';
                document.getElementById('email').textContent = student.email || 'N/A';
                document.getElementById('nic').textContent = student.nic || 'N/A';

                // Birthday Formatting
                if (student.bday) {
                    try {
                        const date = new Date(student.bday);
                        document.getElementById('bday').textContent = date.toLocaleDateString('en-US', {
                            year: 'numeric',
                            month: 'long',
                            day: 'numeric'
                        });
                    } catch (e) {
                        document.getElementById('bday').textContent = student.bday;
                    }
                } else {
                    document.getElementById('bday').textContent = 'N/A';
                }

                // Gender
                document.getElementById('gender').textContent = student.gender ?
                    student.gender.charAt(0).toUpperCase() + student.gender.slice(1) : 'N/A';

                // Address Information
                document.getElementById('address1').textContent = student.address1 || 'N/A';
                document.getElementById('address2').textContent = student.address2 || 'N/A';
                document.getElementById('address3').textContent = student.address3 || 'N/A';

                // Guardian Information
                document.getElementById('guardian_fname').textContent = student.guardian_fname || 'N/A';
                document.getElementById('guardian_lname').textContent = student.guardian_lname || 'N/A';
                document.getElementById('guardian_mobile').textContent = student.guardian_mobile || 'N/A';
                document.getElementById('guardian_nic').textContent = student.guardian_nic || 'N/A';

                // Academic Information
                document.getElementById('gradeName').textContent = student.grade?.grade_name ?
                    `Grade ${student.grade.grade_name}` : 'N/A';

                // Class Type with Badge
                const classTypeElement = document.getElementById('classType');
                if (student.class_type) {
                    const classType = student.class_type.toUpperCase();
                    classTypeElement.textContent = classType;
                    classTypeElement.className = 'badge ' +
                        (student.class_type.toLowerCase() === 'online' ? 'bg-success' :
                         student.class_type.toLowerCase() === 'offline' ? 'bg-primary' : 'bg-info') +
                        ' fs-6';
                } else {
                    classTypeElement.textContent = 'NOT SET';
                    classTypeElement.className = 'badge bg-secondary fs-6';
                }

                document.getElementById('student_school').textContent = student.student_school || 'N/A';
                document.getElementById('admissionStatus').innerHTML = student.admission ?
                    '<span class="badge bg-success">Yes</span>' :
                    '<span class="badge bg-danger">No</span>';

                document.getElementById('permanentQrActive').innerHTML = student.permanent_qr_active ?
                    '<span class="badge bg-success">Yes</span>' :
                    '<span class="badge bg-secondary">No</span>';

                document.getElementById('studentDisabled').innerHTML = student.student_disable ?
                    '<span class="badge bg-danger">Disabled</span>' :
                    '<span class="badge bg-success">Active</span>';

                // Registration Date
                if (student.created_at) {
                    try {
                        const date = new Date(student.created_at);
                        document.getElementById('createdAt').textContent = date.toLocaleDateString('en-US', {
                            year: 'numeric',
                            month: 'long',
                            day: 'numeric'
                        });
                    } catch (e) {
                        document.getElementById('createdAt').textContent = student.created_at;
                    }
                } else {
                    document.getElementById('createdAt').textContent = 'N/A';
                }

                document.getElementById('activeStatus').innerHTML = student.is_active ?
                    '<span class="badge bg-success">Active</span>' :
                    '<span class="badge bg-danger">Inactive</span>';

                // QR Code Information
                document.getElementById('temporary_qr_code').textContent = student.temporary_qr_code || 'N/A';

                if (student.temporary_qr_code_expire_date) {
                    try {
                        const date = new Date(student.temporary_qr_code_expire_date);
                        document.getElementById('temporary_qr_code_expire_date').textContent = date.toLocaleDateString('en-US', {
                            year: 'numeric',
                            month: 'long',
                            day: 'numeric'
                        });
                    } catch (e) {
                        document.getElementById('temporary_qr_code_expire_date').textContent = student.temporary_qr_code_expire_date;
                    }
                } else {
                    document.getElementById('temporary_qr_code_expire_date').textContent = 'N/A';
                }

                // Portal Access Information
                updatePortalStatus(student);

                // Show student details
                studentDetails.style.display = 'block';
            }

            // Update Portal Status Function
            function updatePortalStatus(student) {
                const hasPortalLogin = student.portal_login && student.portal_login !== null;
                const portalUsername = student.portal_login?.username || null;
                const isPortalVerified = student.portal_login?.is_verify || false;
                const isPortalActive = student.portal_login?.is_active || false;

                if (hasPortalLogin && portalUsername && isPortalActive) {
                    // Portal is active
                    portalAccessStatus.textContent = 'Active';
                    portalAccessStatus.className = 'badge bg-success';

                    portalUsernameBadge.textContent = portalUsername;
                    portalUsernameSection.style.display = 'block';

                    portalCredentialInfo.innerHTML = `
                        <span class="text-success">✓ Portal access is enabled</span><br>
                        <small class="text-muted">Username: <strong>${portalUsername}</strong></small><br>
                        <small class="text-success">✓ Verified: ${isPortalVerified ? 'Yes' : 'No'} | ✓ Active: Yes</small>
                    `;

                    sendCredentialsBtn.innerHTML = '<i class="fas fa-redo me-2"></i>Resend Credentials';
                    sendCredentialsBtn.classList.remove('btn-danger');
                    sendCredentialsBtn.classList.add('btn-warning');
                } else if (hasPortalLogin && portalUsername) {
                    // Portal exists but not active
                    portalAccessStatus.textContent = 'Inactive';
                    portalAccessStatus.className = 'badge bg-warning';

                    portalUsernameBadge.textContent = portalUsername + ' (Inactive)';
                    portalUsernameSection.style.display = 'block';

                    portalCredentialInfo.innerHTML = `
                        <span class="text-warning">⚠ Portal account exists but is not active</span><br>
                        <small class="text-muted">Username: <strong>${portalUsername}</strong></small><br>
                        <small class="text-danger">Verified: ${isPortalVerified ? 'Yes' : 'No'} | Active: No</small>
                    `;

                    sendCredentialsBtn.innerHTML = '<i class="fas fa-paper-plane me-2"></i>Send Activation Credentials';
                    sendCredentialsBtn.classList.remove('btn-warning');
                    sendCredentialsBtn.classList.add('btn-primary');
                } else {
                    // No portal access
                    portalAccessStatus.textContent = 'Not Enabled';
                    portalAccessStatus.className = 'badge bg-danger';

                    portalUsernameSection.style.display = 'none';

                    portalCredentialInfo.innerHTML = `
                        <span class="text-danger">✗ Portal access is not enabled</span><br>
                        <small class="text-muted">Click the button to create and send credentials</small>
                    `;

                    sendCredentialsBtn.innerHTML = '<i class="fas fa-paper-plane me-2"></i>Create & Send Credentials';
                    sendCredentialsBtn.classList.remove('btn-warning', 'btn-primary');
                    sendCredentialsBtn.classList.add('btn-danger');
                }
            }

            // Send Student Credentials Function
            async function sendStudentCredentials() {
                try {
                    // Disable button and show loading
                    sendCredentialsBtn.disabled = true;
                    const originalText = sendCredentialsBtn.innerHTML;
                    sendCredentialsBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Sending...';

                    const response = await fetch(`/api/students/${studentId}/send-credentials`, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });

                    const result = await response.json();

                    if (result.status === 'success') {
                        showAlert(result.message || 'Credentials sent successfully!', 'success');

                        // Update portal status if credentials were created
                        if (result.data) {
                            // Refresh student data to get updated portal info
                            await loadStudentDetails(urlStudentId);
                        }

                        // Show temporary password if provided
                        if (result.data && result.data.temporary_password) {
                            portalCredentialInfo.innerHTML = `
                                <div class="alert alert-info mt-2">
                                    <strong>Temporary Password:</strong> <code>${result.data.temporary_password}</code><br>
                                    <small class="text-danger">Note: Student should change this password on first login.</small>
                                </div>
                            `;
                        }
                    } else {
                        throw new Error(result.message || 'Failed to send credentials');
                    }
                } catch (error) {
                    console.error('Error sending credentials:', error);
                    showAlert('Failed to send credentials: ' + error.message, 'error');
                } finally {
                    // Re-enable button
                    sendCredentialsBtn.disabled = false;
                    sendCredentialsBtn.innerHTML = originalText;
                }
            }

            // Show Error Function
            function showError(message) {
                errorText.textContent = message;
                errorMessage.style.display = 'block';
                studentDetails.style.display = 'none';
            }

            // Show Alert Function
            function showAlert(message, type) {
                // Create alert element
                const alertDiv = document.createElement('div');
                alertDiv.className = `alert alert-${type === 'error' ? 'danger' : 'success'} alert-dismissible fade show position-fixed top-0 end-0 m-3`;
                alertDiv.style.zIndex = '9999';
                alertDiv.innerHTML = `
                    <strong>${type === 'error' ? 'Error!' : 'Success!'}</strong> ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                `;

                document.body.appendChild(alertDiv);

                // Auto remove after 5 seconds
                setTimeout(() => {
                    if (alertDiv.parentNode) {
                        alertDiv.remove();
                    }
                }, 5000);
            }
        });
    </script>
@endpush