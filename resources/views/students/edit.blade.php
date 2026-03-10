@extends('layouts.app')

@section('title', 'Edit Student')
@section('page-title', 'Edit Student')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('students.index') }}">Students</a></li>
    <li class="breadcrumb-item active">Edit Student</li>
@endsection

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white">
                    <strong><i class="fas fa-user-edit me-2"></i>Edit Student</strong>
                </div>
                <div class="card-body">
                    <!-- Loading Spinner -->
                    <div id="loadingSpinner" class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Loading student data...</p>
                    </div>

                    <!-- Error Message -->
                    <div id="errorMessage" class="text-center py-5" style="display: none;">
                        <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i>
                        <h4 class="text-danger">Failed to Load Student Data</h4>
                        <p class="text-muted" id="errorText"></p>
                        <a href="{{ route('students.index') }}" class="btn btn-primary mt-3">
                            <i class="fas fa-arrow-left me-2"></i>Back to Students
                        </a>
                    </div>

                    <!-- Student Edit Form -->
                    <form id="studentEditForm" style="display: none;">
                        @csrf
                        @method('PUT')

                        <div class="row mb-4">
                            <!-- Image Upload Section -->
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-header bg-secondary text-white">
                                        <strong>Student Image</strong>
                                    </div>
                                    <div class="card-body">
                                        <!-- Image Preview -->
                                        <div class="text-center mb-3">
                                            <img id="studentImagePreview" class="img-thumbnail rounded-circle"
                                                style="width: 200px; height: 200px; object-fit: cover; display: none;"
                                                onerror="this.onerror=null; this.src='/uploads/logo/logo.png'">
                                            <div id="imagePlaceholder" class="text-muted p-4 border rounded">
                                                <i class="fas fa-user fa-3x mb-3"></i>
                                                <p class="mb-0">Student image will appear here</p>
                                            </div>
                                        </div>

                                        <!-- Image Upload Tabs -->
                                        <ul class="nav nav-tabs" id="imageUploadTabs" role="tablist">
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link active" id="camera-tab" data-bs-toggle="tab"
                                                    data-bs-target="#camera" type="button" role="tab">
                                                    <i class="fas fa-camera me-1"></i>Camera
                                                </button>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link" id="upload-tab" data-bs-toggle="tab"
                                                    data-bs-target="#upload" type="button" role="tab">
                                                    <i class="fas fa-upload me-1"></i>Upload
                                                </button>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link" id="quick-image-tab" data-bs-toggle="tab"
                                                    data-bs-target="#quick-image" type="button" role="tab">
                                                    <i class="fas fa-bolt me-1"></i>Quick Image
                                                </button>
                                            </li>
                                        </ul>

                                        <!-- Tab Content -->
                                        <div class="tab-content p-3 border border-top-0" id="imageUploadTabsContent">
                                            <!-- Camera Tab -->
                                            <div class="tab-pane fade show active" id="camera" role="tabpanel">
                                                <div id="cameraWrapper" style="display: none">
                                                    <video id="cameraView" width="100%" autoplay muted
                                                        class="rounded border" style="max-height: 200px;"></video>
                                                    <div class="d-flex gap-2 mt-2">
                                                        <button class="btn btn-success flex-fill" type="button"
                                                            id="captureBtn">
                                                            <i class="fas fa-camera me-2"></i>Capture
                                                        </button>
                                                        <button class="btn btn-secondary" type="button" id="closeCameraBtn">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                <button class="btn btn-outline-primary w-100" type="button"
                                                    id="openCameraBtn">
                                                    <i class="fas fa-camera me-2"></i>Enable Camera
                                                </button>
                                                <p id="cameraError" class="text-danger mt-2 small" style="display: none">
                                                </p>
                                            </div>

                                            <!-- File Upload Tab -->
                                            <div class="tab-pane fade" id="upload" role="tabpanel">
                                                <div class="file-upload-area border rounded p-3 text-center bg-light">
                                                    <i class="fas fa-cloud-upload-alt fa-2x text-muted mb-3"></i>
                                                    <p class="text-muted mb-2">Click to browse or drag & drop</p>
                                                    <input type="file" id="fileInput" accept="image/*" class="d-none">
                                                    <button class="btn btn-outline-secondary btn-sm" type="button"
                                                        onclick="document.getElementById('fileInput').click()">
                                                        <i class="fas fa-folder-open me-2"></i>Browse Files
                                                    </button>
                                                </div>
                                            </div>

                                            <!-- Quick Image Tab -->
                                            <div class="tab-pane fade" id="quick-image" role="tabpanel">
                                                <div class="mb-3">
                                                    <label class="form-label">Search Quick Image by Custom ID</label>
                                                    <div class="input-group">
                                                        <input type="text" id="quickImageSearch" class="form-control"
                                                            placeholder="Enter custom ID...">
                                                        <button class="btn btn-outline-primary" type="button"
                                                            id="searchQuickImage">
                                                            <i class="fas fa-search"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                <div id="quickImageResults" class="mt-3">
                                                    <!-- Quick images will be displayed here -->
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Selected Image Info -->
                                        <div id="selectedImageInfo" class="mt-3 p-2 bg-light rounded" style="display: none">
                                            <small class="text-muted" id="imageSource"></small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Student Details Form -->
                            <div class="col-md-8">
                                <div class="row">
                                    <!-- Student ID Display (Read-only) -->
                                    <div class="col-12 mb-4">
                                        <div class="card bg-light">
                                            <div class="card-body py-2">
                                                <div class="row align-items-center">
                                                    <div class="col-md-6">
                                                        <strong class="text-muted">Student ID:</strong>
                                                        <h5 class="text-primary mb-0" id="displayCustomId">Loading...</h5>
                                                        <input type="hidden" name="custom_id" id="customId">
                                                    </div>
                                                    <div class="col-md-6 text-end">
                                                        <small class="text-muted">This ID cannot be changed</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Personal Information -->
                                    <div class="col-12">
                                        <h5 class="border-bottom pb-2 mb-3">Personal Information</h5>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Full Name <span class="text-danger">*</span></label>
                                        <input type="text" name="full_name" class="form-control" required>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Initial Name <span class="text-danger">*</span></label>
                                        <input type="text" name="initial_name" class="form-control" required>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Mobile <span class="text-danger">*</span></label>
                                        <input type="text" name="mobile" class="form-control" required>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">WhatsApp Mobile</label>
                                        <input type="text" name="whatsapp_mobile" class="form-control">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Email</label>
                                        <input type="email" name="email" class="form-control">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">NIC</label>
                                        <input type="text" name="nic" class="form-control">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Birthday <span class="text-danger">*</span></label>
                                        <input type="date" name="bday" class="form-control" required>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Gender <span class="text-danger">*</span></label>
                                        <select name="gender" class="form-select" required>
                                            <option value="">Select Gender</option>
                                            <option value="male">Male</option>
                                            <option value="female">Female</option>
                                            <option value="other">Other</option>
                                        </select>
                                    </div>

                                    <!-- Address Information -->
                                    <div class="col-12 mt-4">
                                        <h5 class="border-bottom pb-2 mb-3">Address Information</h5>
                                    </div>

                                    <div class="col-12 mb-3">
                                        <label class="form-label">Address Line 1 <span class="text-danger">*</span></label>
                                        <input type="text" name="address1" class="form-control" required>
                                    </div>

                                    <div class="col-12 mb-3">
                                        <label class="form-label">Address Line 2</label>
                                        <input type="text" name="address2" class="form-control">
                                    </div>

                                    <div class="col-12 mb-3">
                                        <label class="form-label">Address Line 3</label>
                                        <input type="text" name="address3" class="form-control">
                                    </div>

                                    <!-- Guardian Information -->
                                    <div class="col-12 mt-4">
                                        <h5 class="border-bottom pb-2 mb-3">Guardian Information</h5>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Guardian First Name <span
                                                class="text-danger">*</span></label>
                                        <input type="text" name="guardian_fname" class="form-control" required>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Guardian Last Name</label>
                                        <input type="text" name="guardian_lname" class="form-control">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Guardian Mobile <span class="text-danger">*</span></label>
                                        <input type="text" name="guardian_mobile" class="form-control" required>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Guardian NIC</label>
                                        <input type="text" name="guardian_nic" class="form-control">
                                    </div>

                                    <!-- Academic Information -->
                                    <div class="col-12 mt-4">
                                        <h5 class="border-bottom pb-2 mb-3">Academic Information</h5>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Grade <span class="text-danger">*</span></label>
                                        <select name="grade_id" id="gradeSelect" class="form-select" required>
                                            <option value="">Select Grade</option>
                                            <!-- Grades will be populated via JavaScript -->
                                        </select>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Class Type <span class="text-danger">*</span></label>
                                        <select name="class_type" class="form-select" required>
                                            <option value="">Select Class Type</option>
                                            <option value="online">Online</option>
                                            <option value="offline">Offline</option>
                                        </select>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">School</label>
                                        <input type="text" name="student_school" class="form-control">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Admission</label>
                                        <select name="admission" class="form-select">
                                            <option value="0">No</option>
                                            <option value="1">Yes</option>
                                        </select>
                                    </div>

                                    <!-- Status Information -->
                                    <div class="col-12 mt-4">
                                        <h5 class="border-bottom pb-2 mb-3">Status Information</h5>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Active Status</label>
                                        <select name="is_active" class="form-select">
                                            <option value="1">Active</option>
                                            <option value="0">Inactive</option>
                                        </select>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Permanent QR Active</label>
                                        <select name="permanent_qr_active" class="form-select" disabled>
                                            <option value="1">Yes</option>
                                            <option value="0">No</option>
                                        </select>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Student Disabled</label>
                                        <select name="student_disable" class="form-select" disabled>
                                            <option value="0">No</option>
                                            <option value="1">Yes</option>
                                        </select>
                                    </div>

                                    <!-- QR Code Information (Read-only) -->
                                    <div class="col-12 mt-4">
                                        <h5 class="border-bottom pb-2 mb-3">QR Code Information</h5>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Temporary QR Code</label>
                                        <input type="text" name="temporary_qr_code" class="form-control" readonly>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">QR Expire Date</label>
                                        <input type="datetime-local" name="temporary_qr_code_expire_date"
                                            class="form-control" readonly>
                                    </div>

                                    <!-- Submit Button -->
                                    <div class="col-12 mt-4">
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('students.index') }}"
                                                class="btn btn-secondary btn-lg flex-fill">
                                                <i class="fas fa-arrow-left me-2"></i>Cancel
                                            </a>
                                            <button type="submit" class="btn btn-success btn-lg flex-fill" id="submitBtn">
                                                <i class="fas fa-save me-2"></i>Update Student
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .file-upload-area {
            transition: all 0.3s ease;
            cursor: pointer;
            border: 2px dashed #dee2e6;
        }

        .file-upload-area:hover {
            border-color: #0d6efd;
            background-color: #f8f9fa;
        }

        .quick-image-item {
            cursor: pointer;
            transition: all 0.2s ease;
            border: 2px solid transparent;
        }

        .quick-image-item:hover {
            border-color: #0d6efd;
            transform: scale(1.02);
        }

        .quick-image-item.selected {
            border-color: #198754;
            background-color: #f8fff9;
        }

        .nav-tabs .nav-link {
            font-size: 0.85rem;
        }

        .tab-content {
            min-height: 200px;
        }
    </style>
@endpush

@push('scripts')
    <script>
        // Global variables
        let studentImageUrl = null;
        let cameraStream = null;
        let selectedQuickImageId = null;
        let currentStudentId = null;

        document.addEventListener('DOMContentLoaded', function () {
            // Get student ID from URL (last segment)
            const pathSegments = window.location.pathname.split('/');
            currentStudentId = pathSegments[pathSegments.length - 2]; // Get ID from /students/{id}/edit

            console.log('Loading student with ID:', currentStudentId);

            // Initialize
            loadGrades().then(() => {
                loadStudentData(currentStudentId);
            });
            initializeEventListeners();
        });

        // ================= LOAD GRADES =================
        async function loadGrades() {
            try {
                console.log('Loading grades...');
                const response = await fetch('/api/grades/dropdown');
                if (!response.ok) throw new Error('Failed to fetch grades');

                const result = await response.json();
                console.log('Grades response:', result);

                const grades = result.data || result;

                const gradeSelect = document.getElementById('gradeSelect');
                if (!gradeSelect) return;

                let options = '<option value="">Select Grade</option>';

                if (Array.isArray(grades)) {
                    grades.forEach(grade => {
                        const gradeName = grade.grade_name || grade.name || grade.grade;
                        options += `<option value="${grade.id}">Grade ${gradeName}</option>`;
                    });
                } else if (grades && typeof grades === 'object') {
                    // Handle object format
                    Object.values(grades).forEach(grade => {
                        const gradeName = grade.grade_name || grade.name || grade.grade;
                        options += `<option value="${grade.id}">Grade ${gradeName}</option>`;
                    });
                }

                gradeSelect.innerHTML = options;
                console.log('Grades loaded successfully');
            } catch (error) {
                console.error('Error loading grades:', error);
            }
        }

        // ================= LOAD STUDENT DATA =================
        async function loadStudentData(studentId) {
            try {
                showLoadingState();

                const response = await fetch(`/api/students/${studentId}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                const result = await response.json();
                console.log('Student API Response:', result);

                if (result.status === 'success' && result.data) {
                    console.log('Student data loaded:', result.data);
                    // Give a small delay for grades to be populated
                    setTimeout(() => {
                        populateForm(result.data);
                    }, 100);
                    showContentState();
                } else {
                    throw new Error(result.message || 'Student not found');
                }
            } catch (error) {
                console.error('Error loading student:', error);
                showErrorState('Failed to load student data: ' + error.message);
            }
        }

        // ================= POPULATE FORM =================
        function populateForm(student) {
            try {
                console.log('Populating form with student:', student);

                // Display Student ID
                document.getElementById('displayCustomId').textContent = student.custom_id || 'N/A';
                document.getElementById('customId').value = student.custom_id || '';

                // Set image
                if (student.img_url) {
                    studentImageUrl = student.img_url;
                    updateImagePreview(student.img_url, 'Existing Image');
                }

                // Personal Information
                setFieldValue('full_name', student.full_name);
                setFieldValue('initial_name', student.initial_name);
                setFieldValue('mobile', student.mobile);
                setFieldValue('whatsapp_mobile', student.whatsapp_mobile);
                setFieldValue('email', student.email);
                setFieldValue('nic', student.nic);

                // Birthday - Convert to YYYY-MM-DD for date input
                if (student.bday) {
                    const bdayInput = document.querySelector('input[name="bday"]');
                    if (bdayInput) {
                        let formattedDate = '';
                        if (student.bday.match(/^\d{4}-\d{2}-\d{2}$/)) {
                            formattedDate = student.bday;
                        } else {
                            const date = new Date(student.bday);
                            if (!isNaN(date.getTime())) {
                                formattedDate = date.toISOString().split('T')[0];
                            }
                        }
                        bdayInput.value = formattedDate;
                        console.log('Birthday set to:', formattedDate);
                    }
                }

                // Gender
                setSelectValue('gender', student.gender ? student.gender.toLowerCase() : '');

                // Address
                setFieldValue('address1', student.address1);
                setFieldValue('address2', student.address2);
                setFieldValue('address3', student.address3);

                // Guardian
                setFieldValue('guardian_fname', student.guardian_fname);
                setFieldValue('guardian_lname', student.guardian_lname);
                setFieldValue('guardian_mobile', student.guardian_mobile);
                setFieldValue('guardian_nic', student.guardian_nic);

                // Academic - Grade
                console.log('Setting grade_id:', student.grade_id);
                setSelectValue('grade_id', student.grade_id);

                // Class Type
                console.log('Setting class_type:', student.class_type);
                setSelectValue('class_type', student.class_type ? student.class_type.toLowerCase() : '');

                // School
                setFieldValue('student_school', student.student_school);

                // Boolean fields - Make sure they're properly set
                console.log('Setting admission:', student.admission);
                setSelectValue('admission', student.admission ? '1' : '0');

                console.log('Setting is_active:', student.is_active);
                setSelectValue('is_active', student.is_active ? '1' : '0');

                console.log('Setting permanent_qr_active:', student.permanent_qr_active);
                setSelectValue('permanent_qr_active', student.permanent_qr_active ? '1' : '0');

                console.log('Setting student_disable:', student.student_disable);
                setSelectValue('student_disable', student.student_disable ? '1' : '0');

                // QR Code Information (Read-only)
                setFieldValue('temporary_qr_code', student.temporary_qr_code);

                if (student.temporary_qr_code_expire_date) {
                    const expireInput = document.querySelector('input[name="temporary_qr_code_expire_date"]');
                    if (expireInput) {
                        const date = new Date(student.temporary_qr_code_expire_date);
                        if (!isNaN(date.getTime())) {
                            expireInput.value = date.toISOString().slice(0, 16);
                        }
                    }
                }

                console.log('Form populated successfully');
            } catch (error) {
                console.error('Error populating form:', error);
            }
        }

        // Helper function to set input field value
        function setFieldValue(name, value) {
            const input = document.querySelector(`input[name="${name}"]`);
            if (input) {
                input.value = value || '';
            }
        }

        // Helper function to set select field value
        function setSelectValue(name, value) {
            const select = document.querySelector(`select[name="${name}"]`);
            if (select) {
                // Handle null/undefined
                if (value === null || value === undefined) {
                    select.value = '';
                    return;
                }

                // Convert to string for comparison
                const stringValue = String(value);

                // Check if this value exists in options
                let optionExists = false;
                for (let i = 0; i < select.options.length; i++) {
                    if (String(select.options[i].value) === stringValue) {
                        optionExists = true;
                        break;
                    }
                }

                if (optionExists) {
                    select.value = stringValue;
                    console.log(`Set ${name} to:`, stringValue);
                } else {
                    console.warn(`Value ${stringValue} not found in ${name} options`);
                }
            }
        }

        // ================= EVENT LISTENERS =================
        function initializeEventListeners() {
            // Camera
            document.getElementById('openCameraBtn')?.addEventListener('click', openCamera);
            document.getElementById('closeCameraBtn')?.addEventListener('click', closeCamera);
            document.getElementById('captureBtn')?.addEventListener('click', captureImage);

            // File upload
            document.getElementById('fileInput')?.addEventListener('change', handleFileUpload);

            // Quick image search
            document.getElementById('searchQuickImage')?.addEventListener('click', searchQuickImages);

            // Form submission
            document.getElementById('studentEditForm')?.addEventListener('submit', handleFormSubmit);
        }

        // ================= CAMERA FUNCTIONS =================
        async function openCamera() {
            try {
                cameraStream = await navigator.mediaDevices.getUserMedia({
                    video: {
                        width: { ideal: 1280 },
                        height: { ideal: 720 },
                        facingMode: 'environment'
                    }
                });

                const cameraView = document.getElementById('cameraView');
                if (cameraView) {
                    cameraView.srcObject = cameraStream;
                }

                document.getElementById('cameraWrapper').style.display = 'block';
                document.getElementById('openCameraBtn').style.display = 'none';
                document.getElementById('cameraError').style.display = 'none';
            } catch (error) {
                const cameraError = document.getElementById('cameraError');
                if (cameraError) {
                    cameraError.innerText = 'Camera access denied or not available. Please check permissions.';
                    cameraError.style.display = 'block';
                }
                console.error('Camera error:', error);
            }
        }

        function closeCamera() {
            if (cameraStream) {
                cameraStream.getTracks().forEach(track => track.stop());
                cameraStream = null;
            }
            document.getElementById('cameraWrapper').style.display = 'none';
            document.getElementById('openCameraBtn').style.display = 'block';
        }

        function captureImage() {
            const video = document.getElementById('cameraView');
            if (!video) return;

            const canvas = document.createElement('canvas');
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            canvas.getContext('2d').drawImage(video, 0, 0);

            canvas.toBlob(blob => {
                const file = new File([blob], 'student_capture.jpg', { type: 'image/jpeg' });
                uploadImage(file, 'camera');
                closeCamera();
            }, 'image/jpeg', 0.8);
        }

        // ================= FILE UPLOAD =================
        function handleFileUpload(e) {
            const file = e.target.files[0];
            if (!file) return;

            // Validate file type
            if (!file.type.startsWith('image/')) {
                showAlert('Please select a valid image file', 'danger');
                return;
            }

            // Validate file size (5MB max)
            if (file.size > 5 * 1024 * 1024) {
                showAlert('Image size should be less than 5MB', 'danger');
                return;
            }

            uploadImage(file, 'file upload');
        }

        // ================= IMAGE UPLOAD =================
        async function uploadImage(file, source) {
            try {
                showAlert('Uploading image...', 'info');

                const formData = new FormData();
                formData.append('image', file);

                const response = await fetch('/api/image-upload/upload', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: formData
                });

                const result = await response.json();

                if (result.status === 'success') {
                    const baseUrl = '{{ url("/") }}';
                    studentImageUrl = result.image_url.startsWith('http')
                        ? result.image_url
                        : baseUrl + '/uploads/images/' + result.image_url;

                    updateImagePreview(studentImageUrl, `Uploaded via ${source}`);
                    showAlert('Image uploaded successfully!', 'success');
                } else {
                    throw new Error(result.message || 'Upload failed');
                }
            } catch (error) {
                console.error('Upload error:', error);
                showAlert('Failed to upload image: ' + error.message, 'danger');
            }
        }

        // ================= QUICK IMAGE FUNCTIONS =================
        async function searchQuickImages() {
            const searchTerm = document.getElementById('quickImageSearch')?.value.trim();
            if (!searchTerm) {
                showAlert('Please enter a custom ID to search', 'warning');
                return;
            }

            try {
                const response = await fetch('/api/quick-photos/active');
                if (!response.ok) throw new Error('Failed to fetch quick images');

                const result = await response.json();
                const quickImages = result.data || result;

                // Filter images by custom_id
                const filtered = quickImages.filter(img =>
                    img.custom_id && img.custom_id.toLowerCase().includes(searchTerm.toLowerCase())
                );

                displayQuickImages(filtered);
            } catch (error) {
                console.error('Error searching quick images:', error);
                showAlert('Failed to search quick images', 'danger');
            }
        }

        function displayQuickImages(images) {
            const resultsContainer = document.getElementById('quickImageResults');
            if (!resultsContainer) return;

            if (images.length === 0) {
                resultsContainer.innerHTML = '<p class="text-muted text-center">No quick images found</p>';
                return;
            }

            const baseUrl = '{{ url("/") }}';

            resultsContainer.innerHTML = images.map(img => {
                const imageUrl = img.quick_img.startsWith('http')
                    ? img.quick_img
                    : baseUrl + '/uploads/images/' + img.quick_img;

                return `
                        <div class="quick-image-item card mb-2 p-2" onclick="selectQuickImage(${img.id}, '${imageUrl}', '${img.custom_id || 'No ID'}')">
                            <div class="row g-2 align-items-center">
                                <div class="col-3">
                                    <img src="${imageUrl}" class="img-fluid rounded" style="height: 60px; object-fit: cover;">
                                </div>
                                <div class="col-9">
                                    <small class="fw-bold">ID: ${img.custom_id || 'No ID'}</small><br>
                                    <small class="text-muted">Grade: ${img.grade?.grade_name || 'N/A'}</small>
                                </div>
                            </div>
                        </div>
                    `;
            }).join('');
        }

        // Make selectQuickImage globally available
        window.selectQuickImage = function (id, imageUrl, customId) {
            // Remove previous selection
            document.querySelectorAll('.quick-image-item').forEach(item => {
                item.classList.remove('selected');
            });

            // Add selection to clicked item
            event.currentTarget.classList.add('selected');

            // Set student image
            studentImageUrl = imageUrl;
            selectedQuickImageId = id;

            // Update preview
            updateImagePreview(imageUrl, `Quick Image: ${customId}`);

            showAlert(`Quick image "${customId}" selected`, 'success');
        };

        // ================= FORM SUBMISSION =================
        async function handleFormSubmit(e) {
            e.preventDefault();

            // Validate image
            if (!studentImageUrl) {
                showAlert('Please upload a student image', 'warning');
                return;
            }

            // Collect form data
            const formData = new FormData(e.target);
            const studentData = {
                img_url: studentImageUrl
            };

            // Convert FormData to object with proper type conversion
            for (let [key, value] of formData.entries()) {
                if (key !== '_token' && key !== '_method') {
                    // Convert boolean fields - ensure they're properly set
                    if (['admission', 'is_active', 'permanent_qr_active', 'student_disable'].includes(key)) {
                        studentData[key] = value === '1' ? true : false;
                        console.log(`Setting ${key} to:`, studentData[key]);
                    } else if (value) {
                        studentData[key] = value;
                    } else {
                        studentData[key] = null;
                    }
                }
            }

            console.log('Submitting student data:', studentData);

            try {
                // Disable submit button
                const submitBtn = document.getElementById('submitBtn');
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Updating...';

                // Send update request
                const response = await fetch(`/api/students/${currentStudentId}`, {
                    method: 'PUT',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(studentData)
                });

                const result = await response.json();
                console.log('Update response:', result);

                if (result.status === 'success') {
                    // Deactivate quick image if used
                    if (selectedQuickImageId) {
                        await deactivateQuickImage(selectedQuickImageId);
                    }

                    showAlert('Student updated successfully!', 'success');

                    // Redirect after delay
                    setTimeout(() => {
                        window.location.href = '{{ route("students.index") }}';
                    }, 1500);
                } else {
                    throw new Error(result.message || 'Update failed');
                }
            } catch (error) {
                console.error('Update error:', error);
                showAlert('Failed to update student: ' + error.message, 'danger');

                // Re-enable submit button
                const submitBtn = document.getElementById('submitBtn');
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-save me-2"></i>Update Student';
            }
        }

        // ================= DEACTIVATE QUICK IMAGE =================
        async function deactivateQuickImage(quickImageId) {
            try {
                await fetch(`/api/quick-photos/${quickImageId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });
            } catch (error) {
                console.warn('Failed to deactivate quick image:', error);
            }
        }

        // ================= UTILITY FUNCTIONS =================
        function updateImagePreview(imageUrl, source) {
            const preview = document.getElementById('studentImagePreview');
            const placeholder = document.getElementById('imagePlaceholder');
            const imageInfo = document.getElementById('selectedImageInfo');
            const imageSource = document.getElementById('imageSource');

            if (preview && placeholder && imageInfo && imageSource) {
                preview.src = imageUrl;
                preview.style.display = 'block';
                placeholder.style.display = 'none';
                imageInfo.style.display = 'block';
                imageSource.textContent = source;
            }
        }

        function showLoadingState() {
            document.getElementById('loadingSpinner').style.display = 'block';
            document.getElementById('errorMessage').style.display = 'none';
            document.getElementById('studentEditForm').style.display = 'none';
        }

        function showContentState() {
            document.getElementById('loadingSpinner').style.display = 'none';
            document.getElementById('errorMessage').style.display = 'none';
            document.getElementById('studentEditForm').style.display = 'block';
        }

        function showErrorState(message) {
            document.getElementById('loadingSpinner').style.display = 'none';
            document.getElementById('studentEditForm').style.display = 'none';
            document.getElementById('errorMessage').style.display = 'block';
            document.getElementById('errorText').textContent = message;
        }

        function showAlert(message, type) {
            // Remove existing alerts
            const existingAlerts = document.querySelectorAll('.alert');
            existingAlerts.forEach(alert => alert.remove());

            // Create new alert
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed top-0 end-0 m-3`;
            alertDiv.style.zIndex = '9999';
            alertDiv.innerHTML = `
                    <strong>${type === 'success' ? 'Success!' : type === 'warning' ? 'Warning!' : 'Error!'}</strong> ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                `;

            document.body.appendChild(alertDiv);

            // Auto remove after 5 seconds
            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.remove();
                }
            }, 5000);
        }
    </script>
@endpush