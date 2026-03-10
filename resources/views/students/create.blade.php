@extends('layouts.app')

@section('title', 'Student Registration')
@section('page-title', 'Student Registration')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('students.index') }}">Students</a></li>
    <li class="breadcrumb-item active">Student Registration</li>
@endsection

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white">
                    <strong><i class="fas fa-user-plus me-2"></i>Student Registration</strong>
                </div>
                <div class="card-body">
                    <form id="studentRegistrationForm">
                        <!-- Image Upload Section -->
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-header bg-secondary text-white">
                                        <strong>Student Image</strong>
                                    </div>
                                    <div class="card-body">
                                        <!-- Image Preview -->
                                        <div class="text-center mb-3">
                                            <img id="studentImagePreview" class="img-thumbnail rounded-circle"
                                                style="width: 200px; height: 200px; object-fit: cover; display: none;">
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
                                        <!-- Hidden input for quick_image_id -->
                                        <input type="hidden" name="quick_image_id" id="quick_image_id" value="">
                                    </div>
                                </div>
                            </div>

                            <!-- Student Details Form -->
                            <div class="col-md-8">
                                <div class="row">
                                    <!-- QR Code Information -->
                                    <div class="col-12">
                                        <h5 class="border-bottom pb-2 mb-3">QR Code Information</h5>
                                    </div>

                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">Temporary QR Code <span class="text-danger">*</span></label>
                                        <input type="text" name="temporary_qr_code" class="form-control" 
                                            placeholder="Enter temporary QR code" required>
                                        <small class="text-muted">Enter the temporary QR code provided to the student</small>
                                    </div>

                                    <!-- Personal Information -->
                                    <div class="col-12 mt-4">
                                        <h5 class="border-bottom pb-2 mb-3">Personal Information</h5>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Full Name <span class="text-danger">*</span></label>
                                        <input type="text" name="full_name" class="form-control" required>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Name with Initials <span class="text-danger">*</span></label>
                                        <input type="text" name="initial_name" class="form-control" required>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Mobile <span class="text-danger">*</span></label>
                                        <input type="text" name="mobile" class="form-control" required>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">WhatsApp Mobile <span class="text-danger">*</span></label>
                                        <input type="text" name="whatsapp_mobile" class="form-control" required>
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
                                        <label class="form-label">Birthday</label>
                                        <input type="date" name="bday" class="form-control">
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
                                        <label class="form-label">Address Line 2 <span class="text-danger">*</span></label>
                                        <input type="text" name="address2" class="form-control" required>
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
                                        <label class="form-label">Guardian First Name <span class="text-danger">*</span></label>
                                        <input type="text" name="guardian_fname" class="form-control" required>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Guardian Last Name <span class="text-danger">*</span></label>
                                        <input type="text" name="guardian_lname" class="form-control" required>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Guardian Mobile</label>
                                        <input type="text" name="guardian_mobile" class="form-control">
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
                                        <select name="grade_id" class="form-select" required>
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

                                    <!-- Hidden Fields -->
                                    <input type="hidden" name="is_active" value="1">

                                    <!-- Submit Button -->
                                    <div class="col-12 mt-4">
                                        <button type="submit" class="btn btn-success btn-lg w-100" id="submitBtn">
                                            <i class="fas fa-user-plus me-2"></i>Register Student
                                        </button>
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
        let studentImageUrl = null;
        let cameraStream = null;
        let selectedQuickImageId = null;
        let currentAlert = null;

        // ================= INITIALIZATION =================
        document.addEventListener('DOMContentLoaded', function () {
            loadGrades();
            initializeEventListeners();
        });

        // ================= LOAD GRADES =================
        async function loadGrades() {
            try {
                const response = await fetch('/api/grades/dropdown');
                if (!response.ok) throw new Error('Failed to fetch grades');

                const res = await response.json();
                const data = res.data || res;

                const gradeSelect = document.querySelector('select[name="grade_id"]');
                gradeSelect.innerHTML = '<option value="">Select Grade</option>';

                if (Array.isArray(data) && data.length > 0) {
                    data.forEach(g => {
                        gradeSelect.innerHTML += `<option value="${g.id}">Grade ${g.grade_name}</option>`;
                    });
                }
            } catch (e) {
                console.error('Error loading grades:', e);
            }
        }

        // ================= EVENT LISTENERS =================
        function initializeEventListeners() {
            // Camera functionality
            document.getElementById('openCameraBtn').addEventListener('click', openCamera);
            document.getElementById('closeCameraBtn').addEventListener('click', closeCamera);
            document.getElementById('captureBtn').addEventListener('click', captureImage);

            // File upload
            document.getElementById('fileInput').addEventListener('change', handleFileUpload);

            // Drag and drop for file upload area
            const uploadArea = document.querySelector('.file-upload-area');
            uploadArea.addEventListener('dragover', handleDragOver);
            uploadArea.addEventListener('drop', handleDrop);

            // Quick image search
            document.getElementById('searchQuickImage').addEventListener('click', searchQuickImages);
            document.getElementById('quickImageSearch').addEventListener('keypress', function (e) {
                if (e.key === 'Enter') {
                    searchQuickImages();
                }
            });

            // Form submission
            document.getElementById('studentRegistrationForm').addEventListener('submit', handleFormSubmit);
        }

        // ================= DRAG AND DROP HANDLERS =================
        function handleDragOver(e) {
            e.preventDefault();
            e.stopPropagation();
            e.currentTarget.style.borderColor = '#0d6efd';
            e.currentTarget.style.backgroundColor = '#e7f1ff';
        }

        function handleDrop(e) {
            e.preventDefault();
            e.stopPropagation();

            const uploadArea = e.currentTarget;
            uploadArea.style.borderColor = '#dee2e6';
            uploadArea.style.backgroundColor = '#f8f9fa';

            const files = e.dataTransfer.files;
            if (files.length > 0) {
                const file = files[0];
                if (file.type.startsWith('image/')) {
                    if (file.size > 5 * 1024 * 1024) {
                        showAlert('Image size should be less than 5MB', 'danger');
                        return;
                    }
                    uploadImage(file, 'file');
                } else {
                    showAlert('Please select a valid image file', 'danger');
                }
            }
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
                cameraView.srcObject = cameraStream;

                document.getElementById('cameraWrapper').style.display = 'block';
                document.getElementById('openCameraBtn').style.display = 'none';
                document.getElementById('cameraError').style.display = 'none';

            } catch (e) {
                const cameraError = document.getElementById('cameraError');
                cameraError.innerText = 'Camera access denied or not available. Please check permissions.';
                cameraError.style.display = 'block';
                console.error('Camera error:', e);
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
            const canvas = document.createElement('canvas');
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            const ctx = canvas.getContext('2d');
            ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

            canvas.toBlob(blob => {
                const file = new File([blob], "student_capture_" + Date.now() + ".jpg", {
                    type: "image/jpeg",
                    lastModified: Date.now()
                });
                uploadImage(file, 'camera');
                closeCamera();
            }, "image/jpeg", 0.8);
        }

        // ================= FILE UPLOAD =================
        function handleFileUpload(e) {
            const file = e.target.files[0];
            if (file) {
                if (!file.type.startsWith('image/')) {
                    showAlert('Please select a valid image file', 'danger');
                    return;
                }
                if (file.size > 5 * 1024 * 1024) {
                    showAlert('Image size should be less than 5MB', 'danger');
                    return;
                }
                uploadImage(file, 'file');
            }
            // Reset the input so the same file can be uploaded again
            e.target.value = '';
        }

        // ================= QUICK IMAGE FUNCTIONS =================
        async function searchQuickImages() {
            const searchTerm = document.getElementById('quickImageSearch').value.trim();
            if (!searchTerm) {
                showAlert('Please enter a custom ID to search', 'warning');
                return;
            }

            try {
                showAlert('Searching quick images...', 'info');
                const response = await fetch('/api/quick-photos/active');
                if (!response.ok) throw new Error('Failed to fetch quick images');

                const res = await response.json();
                const quickImages = res.data || res;

                const filteredImages = quickImages.filter(img =>
                    img.custom_id && img.custom_id.toLowerCase().includes(searchTerm.toLowerCase())
                );

                displayQuickImages(filteredImages);

                if (filteredImages.length === 0) {
                    showAlert('No quick images found for: ' + searchTerm, 'warning');
                } else {
                    showAlert(`Found ${filteredImages.length} quick image(s)`, 'success');
                }
            } catch (e) {
                console.error('Error searching quick images:', e);
                showAlert('Failed to search quick images', 'danger');
            }
        }

        function selectQuickImage(id, imageUrl, customId) {
            // Remove previous selection
            document.querySelectorAll('.quick-image-item').forEach(item => {
                item.classList.remove('selected');
            });

            // Add selection to clicked item
            event.currentTarget.classList.add('selected');

            // Set student image and quick image ID
            studentImageUrl = imageUrl;
            selectedQuickImageId = id;
            document.getElementById('quick_image_id').value = id;

            // Update preview
            updateImagePreview(imageUrl, `Quick Image: ${customId}`);

            showAlert(`Quick image "${customId}" selected`, 'success');
        }

        // ================= IMAGE UPLOAD =================
        async function uploadImage(file, source) {
            // Clear any existing alert first
            if (currentAlert) {
                currentAlert.remove();
            }

            try {
                showAlert('Uploading image...', 'info');

                const fd = new FormData();
                fd.append('image', file);

                const res = await fetch('/api/image-upload/upload', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: fd
                });

                const data = await res.json();
                console.log('Upload response:', data);

                if (data.status === 'success') {
                    studentImageUrl = data.image_url;
                    // Clear quick image selection if any
                    selectedQuickImageId = null;
                    document.getElementById('quick_image_id').value = '';
                    updateImagePreview(studentImageUrl, `Uploaded via ${source}`);
                    showAlert('Image uploaded successfully!', 'success');
                } else {
                    throw new Error(data.message || 'Upload failed');
                }
            } catch (e) {
                console.error('Upload error:', e);
                showAlert('Failed to upload image: ' + e.message, 'danger');
            }
        }

        function updateImagePreview(imageUrl, source) {
            const preview = document.getElementById('studentImagePreview');
            const placeholder = document.getElementById('imagePlaceholder');
            const imageInfo = document.getElementById('selectedImageInfo');

            // Add cache busting to prevent cached images
            const cacheBuster = '?t=' + Date.now();

            preview.src = imageUrl + cacheBuster;
            preview.style.display = 'block';
            placeholder.style.display = 'none';
            imageInfo.style.display = 'block';
            document.getElementById('imageSource').textContent = source;

            // Handle image load error
            preview.onerror = function () {
                console.error('Failed to load image:', imageUrl);
                showAlert('Failed to load image preview. The image may have been moved or deleted.', 'warning');
                preview.style.display = 'none';
                placeholder.style.display = 'block';
                imageInfo.style.display = 'none';
                studentImageUrl = null;
            };
        }

        // ================= FORM SUBMISSION =================
        async function handleFormSubmit(e) {
            e.preventDefault();

            // Validate image
            if (!studentImageUrl) {
                showAlert('Please upload or select a student image', 'warning');
                return;
            }

            // Validate temporary QR code
            const tempQRCode = document.querySelector('input[name="temporary_qr_code"]').value.trim();
            if (!tempQRCode) {
                showAlert('Please enter the temporary QR code', 'warning');
                return;
            }

            // Validate required fields
            const requiredFields = ['full_name', 'initial_name', 'mobile', 'whatsapp_mobile', 'gender', 'address1', 'address2', 'guardian_fname', 'guardian_lname', 'grade_id', 'class_type'];
            const missingFields = [];

            requiredFields.forEach(field => {
                const input = document.querySelector(`[name="${field}"]`);
                if (!input.value.trim()) {
                    missingFields.push(field.replace('_', ' '));
                }
            });

            if (missingFields.length > 0) {
                showAlert(`Please fill in required fields: ${missingFields.join(', ')}`, 'danger');
                return;
            }

            const formData = new FormData(e.target);
            const studentData = {
                img_url: studentImageUrl,
                is_active: true,
                temporary_qr_code: tempQRCode
            };

            // Add quick_image_id if selected
            if (selectedQuickImageId) {
                studentData.quick_image_id = selectedQuickImageId;
            }

            // Convert FormData to object with proper type conversion
            for (let [key, value] of formData.entries()) {
                if (value && !studentData.hasOwnProperty(key)) {
                    // Convert string '0'/'1' to boolean for specific fields
                    if (['admission'].includes(key)) {
                        studentData[key] = value === '1';
                    } else if (key === 'grade_id') {
                        studentData[key] = parseInt(value) || value;
                    } else {
                        studentData[key] = value;
                    }
                }
            }

            try {
                const submitBtn = document.getElementById('submitBtn');
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Registering...';

                console.log('Submitting student data:', studentData);

                // Register student
                const studentResponse = await fetch('/api/students', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(studentData)
                });

                const studentResult = await studentResponse.json();
                console.log('Registration response:', studentResult);

                if (studentResult.status === 'success' || studentResponse.ok) {
                    // Safely get the custom_id
                    const studentId = studentResult.data?.custom_id
                        || studentResult.data?.student?.custom_id
                        || studentResult.custom_id
                        || 'N/A';

                    showAlert(`Student registered successfully! Student ID: ${studentId}`, 'success');

                    // Reset form after delay
                    setTimeout(() => {
                        resetForm();
                    }, 2000);
                }
                else if (studentResult.status === 'error' && studentResult.errors) {
                    // Handle validation errors
                    const errorMessages = Object.values(studentResult.errors).flat().join(', ');
                    throw new Error('Validation failed: ' + errorMessages);
                } else if (studentResult.message && studentResult.message.includes('Duplicate entry')) {
                    throw new Error('Student ID already exists. Please use a different ID.');
                } else {
                    throw new Error(studentResult.message || 'Registration failed');
                }

            } catch (e) {
                console.error('Registration error:', e);
                showAlert('Failed to register student: ' + e.message, 'danger');
            } finally {
                const submitBtn = document.getElementById('submitBtn');
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-user-plus me-2"></i>Register Student';
            }
        }

        // ================= UTILITY FUNCTIONS =================
        function resetForm() {
            document.getElementById('studentRegistrationForm').reset();
            studentImageUrl = null;
            selectedQuickImageId = null;
            document.getElementById('quick_image_id').value = '';

            const preview = document.getElementById('studentImagePreview');
            const placeholder = document.getElementById('imagePlaceholder');
            const imageInfo = document.getElementById('selectedImageInfo');

            preview.style.display = 'none';
            placeholder.style.display = 'block';
            imageInfo.style.display = 'none';

            // Clear quick image results
            document.getElementById('quickImageResults').innerHTML = '';
            document.getElementById('quickImageSearch').value = '';

            // Reset camera if open
            closeCamera();
        }

        function showAlert(message, type) {
            // Remove existing alert
            if (currentAlert) {
                currentAlert.remove();
            }

            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
            alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px; max-width: 500px;';

            const icon = type === 'success' ? 'fa-check-circle' :
                type === 'warning' ? 'fa-exclamation-triangle' :
                    type === 'info' ? 'fa-info-circle' : 'fa-times-circle';

            alertDiv.innerHTML = `
                <div class="d-flex align-items-start">
                    <i class="fas ${icon} fa-lg me-3 mt-1"></i>
                    <div class="flex-grow-1">
                        <strong>${type === 'success' ? 'Success!' : type === 'warning' ? 'Warning!' : type === 'info' ? 'Info:' : 'Error!'}</strong> 
                        <span>${message}</span>
                    </div>
                    <button type="button" class="btn-close ms-2" data-bs-dismiss="alert"></button>
                </div>
            `;

            document.body.appendChild(alertDiv);
            currentAlert = alertDiv;

            // Auto remove after delay
            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.remove();
                    currentAlert = null;
                }
            }, type === 'success' ? 3000 : 5000);
        }

        function displayQuickImages(images) {
            const resultsContainer = document.getElementById('quickImageResults');

            if (images.length === 0) {
                resultsContainer.innerHTML = '<p class="text-muted text-center">No quick images found</p>';
                return;
            }

            resultsContainer.innerHTML = images.map(img => {
                const imageUrl = img.quick_img || img.image_url || '';

                if (!imageUrl) {
                    return '';
                }

                return `
                    <div class="quick-image-item card mb-2 p-2" onclick="selectQuickImage(${img.id}, '${imageUrl}', '${img.custom_id || 'No ID'}')">
                        <div class="row g-2 align-items-center">
                            <div class="col-3">
                                <img src="${imageUrl}" class="img-fluid rounded" style="height: 60px; object-fit: cover; width: 100%;">
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
    </script>
@endpush