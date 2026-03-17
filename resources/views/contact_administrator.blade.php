<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Administrator - NEXORA EDUCATION</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Your Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/contact-admin.css') }}">
    <style>
        /* Temporary inline styles - move to contact-admin.css later */
        .contact-page {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 50px 20px;
            position: relative;
            overflow: hidden;
        }

        .contact-wrapper {
            max-width: 1300px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            backdrop-filter: blur(10px);
            position: relative;
            z-index: 1;
        }

        .admin-info-card {
            background: linear-gradient(145deg, #2c3e50, #1e2b37);
            color: white;
            height: 100%;
            padding: 40px 30px;
            border-radius: 0;
            position: relative;
            overflow: hidden;
        }

        .admin-info-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
            animation: rotate 20s linear infinite;
        }

        @keyframes rotate {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        .admin-profile {
            text-align: center;
            margin-bottom: 40px;
            position: relative;
            z-index: 2;
        }

        .admin-avatar {
            width: 120px;
            height: 120px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 50%;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 4px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }

        .admin-avatar i {
            font-size: 60px;
            color: white;
        }

        .admin-name {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 5px;
            background: linear-gradient(135deg, #fff, #e0e0e0);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .admin-title {
            color: #a0aec0;
            font-size: 16px;
            letter-spacing: 1px;
        }

        .info-section {
            margin-bottom: 35px;
            position: relative;
            z-index: 2;
        }

        .info-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
        }

        .info-title i {
            margin-right: 10px;
            color: #667eea;
        }

        .info-item {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            padding: 12px 15px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(5px);
        }

        .info-item:hover {
            transform: translateY(-3px);
            background: rgba(255, 255, 255, 0.15);
            border-color: rgba(255, 255, 255, 0.3);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }

        .info-icon {
            width: 45px;
            height: 45px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            font-size: 18px;
            color: white;
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .info-content {
            flex: 1;
        }

        .info-label {
            font-size: 13px;
            color: #a0aec0;
            margin-bottom: 3px;
        }

        .info-value {
            font-size: 16px;
            font-weight: 600;
            color: white;
            line-height: 1.4;
        }

        .info-value a {
            color: white;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .info-value a:hover {
            color: #667eea;
        }

        .office-hours-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-top: 10px;
        }

        .hour-item {
            background: rgba(255, 255, 255, 0.1);
            padding: 12px;
            border-radius: 10px;
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .day {
            font-size: 13px;
            color: #a0aec0;
            margin-bottom: 5px;
        }

        .time {
            font-size: 14px;
            font-weight: 600;
            color: white;
        }

        .response-time-badge {
            background: linear-gradient(135deg, #10b981, #059669);
            padding: 12px 20px;
            border-radius: 50px;
            display: inline-flex;
            align-items: center;
            font-weight: 600;
            margin-top: 15px;
            box-shadow: 0 5px 20px rgba(16, 185, 129, 0.3);
        }

        .response-time-badge i {
            margin-right: 10px;
            font-size: 18px;
        }

        .social-links {
            display: flex;
            gap: 15px;
            margin-top: 25px;
            position: relative;
            z-index: 2;
        }

        .social-btn {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-decoration: none;
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .social-btn:hover {
            background: linear-gradient(135deg, #667eea, #764ba2);
            transform: translateY(-5px);
            border-color: transparent;
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.4);
        }

        .contact-form-section {
            padding: 40px;
            background: white;
        }

        .form-header {
            margin-bottom: 30px;
        }

        .form-header h2 {
            color: #2d3748;
            font-weight: 700;
            margin-bottom: 10px;
            font-size: 28px;
        }

        .form-header p {
            color: #718096;
            font-size: 16px;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-label {
            font-weight: 600;
            color: #4a5568;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
        }

        .form-label i {
            margin-right: 8px;
            color: #667eea;
        }

        .form-control,
        .form-select {
            padding: 12px 15px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            transition: all 0.3s ease;
            font-size: 15px;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            outline: none;
        }

        .form-control.is-invalid {
            border-color: #f56565;
        }

        .invalid-feedback {
            color: #f56565;
            font-size: 13px;
            margin-top: 5px;
            display: flex;
            align-items: center;
        }

        .invalid-feedback i {
            margin-right: 5px;
        }

        .file-upload-wrapper {
            position: relative;
        }

        .file-upload-area {
            border: 2px dashed #e2e8f0;
            border-radius: 12px;
            padding: 30px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            background: #f8fafc;
        }

        .file-upload-area:hover {
            border-color: #667eea;
            background: #f0f5ff;
        }

        .file-upload-area i {
            font-size: 40px;
            color: #667eea;
            margin-bottom: 10px;
        }

        .file-upload-area p {
            color: #718096;
            margin-bottom: 5px;
        }

        .file-upload-area small {
            color: #a0aec0;
            font-size: 12px;
        }

        .file-info {
            margin-top: 10px;
            padding: 10px;
            background: #f0f5ff;
            border-radius: 8px;
            display: none;
        }

        .file-info.active {
            display: block;
        }

        .action-buttons {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }

        .btn {
            padding: 12px 30px;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn i {
            margin-right: 8px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            flex: 2;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.4);
        }

        .btn-outline-secondary {
            background: white;
            border: 2px solid #e2e8f0;
            color: #718096;
            flex: 1;
        }

        .btn-outline-secondary:hover {
            border-color: #667eea;
            color: #667eea;
        }

        .btn-success,
        .btn-info {
            flex: 1;
            color: white;
        }

        .btn-success {
            background: linear-gradient(135deg, #10b981, #059669);
        }

        .btn-info {
            background: linear-gradient(135deg, #3b82f6, #1e40af);
        }

        .quick-action-buttons {
            display: flex;
            gap: 10px;
            margin: 20px 0;
        }

        .floating-elements {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 0;
        }

        .floating-element {
            position: absolute;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            animation: float 8s infinite;
        }

        .floating-element:nth-child(1) {
            width: 100px;
            height: 100px;
            top: 10%;
            left: 10%;
            animation-delay: 0s;
        }

        .floating-element:nth-child(2) {
            width: 150px;
            height: 150px;
            top: 50%;
            right: 10%;
            animation-delay: 2s;
        }

        .floating-element:nth-child(3) {
            width: 80px;
            height: 80px;
            bottom: 20%;
            left: 20%;
            animation-delay: 4s;
        }

        .floating-element:nth-child(4) {
            width: 200px;
            height: 200px;
            bottom: 10%;
            right: 20%;
            animation-delay: 6s;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0) rotate(0deg);
            }

            50% {
                transform: translateY(-20px) rotate(180deg);
            }
        }
    </style>
</head>

<body>
    <div class="floating-elements">
        <div class="floating-element"></div>
        <div class="floating-element"></div>
        <div class="floating-element"></div>
        <div class="floating-element"></div>
    </div>

    <div class="contact-page">
        <div class="contact-wrapper">
            <div class="row g-0">
                <!-- Left Side - Admin Info Card -->
                <div class="col-lg-5">
                    <div class="admin-info-card">
                        <div class="admin-profile">
                            <div class="admin-avatar">
                                <i class="fas fa-user-tie"></i>
                            </div>
                            <h2 class="admin-name">Dr. Dhananjya Dharshani</h2>
                            <p class="admin-title">Chief Administrative Officer</p>
                        </div>

                        <div class="info-section">
                            <h3 class="info-title">
                                <i class="fas fa-address-card"></i>
                                Contact Information
                            </h3>

                            <div class="info-item">
                                <div class="info-icon">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                <div class="info-content">
                                    <div class="info-label">Support Email</div>
                                    <div class="info-value">
                                        <a href="mailto:info@nexorait.lk">info@nexorait.lk</a>
                                    </div>
                                </div>
                            </div>

                            <div class="info-item">
                                <div class="info-icon">
                                    <i class="fas fa-phone-alt"></i>
                                </div>
                                <div class="info-content">
                                    <div class="info-label">Hotline Number</div>
                                    <div class="info-value">
                                        <a href="tel:+94112345678">+94 76 89 71 213</a>
                                    </div>
                                </div>
                            </div>

                            <div class="info-item">
                                <div class="info-icon">
                                    <i class="fas fa-whatsapp"></i>
                                </div>
                                <div class="info-content">
                                    <div class="info-label">WhatsApp</div>
                                    <div class="info-value">
                                        <a href="https://wa.me/94766499254">+94 76 64 99 254</a>
                                    </div>
                                </div>
                            </div>

                            <div class="info-item">
                                <div class="info-icon">
                                    <i class="fas fa-map-marker-alt"></i>
                                </div>
                                <div class="info-content">
                                    <div class="info-label">Office Address</div>
                                    <div class="info-value">
                                        Mirigama,<br>
                                        Sri Lanka
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="info-section">
                            <h3 class="info-title">
                                <i class="fas fa-clock"></i>
                                Office Hours
                            </h3>

                            <div class="office-hours-grid">
                                <div class="hour-item">
                                    <div class="day">Monday - Sunday</div>
                                    <div class="time">8:00 AM - 5:00 PM</div>
                                </div>

                                <div class="hour-item">
                                    <div class="day">Public Holidays</div>
                                    <div class="time">Closed</div>
                                </div>
                            </div>

                            <div class="response-time-badge">
                                <i class="fas fa-clock"></i>
                                Typical response time: Within 24 hours
                            </div>
                        </div>

                        <div class="social-links">
                            <a href="#" class="social-btn"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" class="social-btn"><i class="fab fa-twitter"></i></a>
                            <a href="#" class="social-btn"><i class="fab fa-linkedin-in"></i></a>
                            <a href="#" class="social-btn"><i class="fab fa-instagram"></i></a>
                        </div>
                    </div>
                </div>

                <!-- Right Side - Contact Form -->
                <div class="col-lg-7">
                    <div class="contact-form-section">
                        <div class="form-header">
                            <h2><i class="fas fa-paper-plane me-2"></i>Send a Message</h2>
                            <p>Fill out the form below and we'll get back to you as soon as possible.</p>
                        </div>

                        <form id="contactForm" novalidate>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">
                                            <i class="fas fa-user"></i>Full Name *
                                        </label>
                                        <input type="text" class="form-control" id="name" name="name"
                                            placeholder="Enter your full name" required>
                                        <div class="invalid-feedback">
                                            <i class="fas fa-exclamation-circle"></i>
                                            Please enter your name
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">
                                            <i class="fas fa-envelope"></i>Email Address *
                                        </label>
                                        <input type="email" class="form-control" id="email" name="email"
                                            placeholder="Enter your email" required>
                                        <div class="invalid-feedback">
                                            <i class="fas fa-exclamation-circle"></i>
                                            Please enter a valid email address
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">
                                            <i class="fas fa-phone"></i>Phone Number *
                                        </label>
                                        <input type="tel" class="form-control" id="phone" name="phone"
                                            placeholder="Enter your phone number" pattern="[0-9]{10}" required>
                                        <div class="invalid-feedback">
                                            <i class="fas fa-exclamation-circle"></i>
                                            Please enter a valid 10-digit phone number
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">
                                            <i class="fas fa-tag"></i>Subject *
                                        </label>
                                        <select class="form-select" id="subject" name="subject" required>
                                            <option value="">Select a subject</option>
                                            <option value="general">General Inquiry</option>
                                            <option value="admissions">Admissions</option>
                                            <option value="technical">Technical Support</option>
                                            <option value="billing">Billing & Payments</option>
                                            <option value="other">Other</option>
                                        </select>
                                        <div class="invalid-feedback">
                                            <i class="fas fa-exclamation-circle"></i>
                                            Please select a subject
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="form-group">
                                        <label class="form-label">
                                            <i class="fas fa-comment"></i>Message *
                                        </label>
                                        <textarea class="form-control" id="message" name="message" rows="5"
                                            placeholder="Type your message here..." required></textarea>
                                        <div class="invalid-feedback">
                                            <i class="fas fa-exclamation-circle"></i>
                                            Please enter your message
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="form-group">
                                        <label class="form-label">
                                            <i class="fas fa-paperclip"></i>Attachment (Optional)
                                        </label>
                                        <div class="file-upload-wrapper">
                                            <div class="file-upload-area"
                                                onclick="document.getElementById('fileInput').click()">
                                                <i class="fas fa-cloud-upload-alt"></i>
                                                <p>Click to upload or drag and drop</p>
                                                <small>Supported: PDF, DOC, DOCX, JPG, PNG (Max: 5MB)</small>
                                            </div>
                                            <input type="file" class="d-none" id="fileInput" name="attachment"
                                                accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                            <div class="file-info" id="fileInfo">
                                                <i class="fas fa-file me-2"></i>
                                                <span id="fileName">No file chosen</span>
                                            </div>
                                        </div>
                                        <div class="invalid-feedback" id="fileError">
                                            <i class="fas fa-exclamation-circle"></i>
                                            File size should not exceed 5MB
                                        </div>
                                    </div>
                                </div>

                                <div class="quick-action-buttons">
                                    <button type="button" class="btn btn-success"
                                        onclick="window.location.href='tel:+94112345678'">
                                        <i class="fas fa-phone-alt"></i> Call Now
                                    </button>
                                    <button type="button" class="btn btn-info"
                                        onclick="window.location.href='mailto:admin@nexora.edu'">
                                        <i class="fas fa-envelope"></i> Email Now
                                    </button>
                                </div>

                                <div class="action-buttons">
                                    <button type="button" class="btn btn-outline-secondary" onclick="resetForm()">
                                        <i class="fas fa-redo-alt"></i> Reset
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary" onclick="clearForm()">
                                        <i class="fas fa-eraser"></i> Clear
                                    </button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-paper-plane"></i> Send Message
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Form validation and file handling
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('contactForm');
            const fileInput = document.getElementById('fileInput');
            const fileInfo = document.getElementById('fileInfo');
            const fileName = document.getElementById('fileName');
            const fileError = document.getElementById('fileError');

            // File input change handler
            fileInput.addEventListener('change', function () {
                if (this.files.length > 0) {
                    const file = this.files[0];
                    const fileSize = file.size / 1024 / 1024; // in MB

                    if (fileSize > 5) {
                        fileError.style.display = 'block';
                        fileInfo.classList.remove('active');
                        this.value = ''; // Clear the file input
                    } else {
                        fileError.style.display = 'none';
                        fileName.textContent = file.name;
                        fileInfo.classList.add('active');
                    }
                } else {
                    fileInfo.classList.remove('active');
                }
            });

            // Form submit handler
            form.addEventListener('submit', function (e) {
                e.preventDefault();

                let isValid = true;
                const inputs = form.querySelectorAll('input, select, textarea');

                inputs.forEach(input => {
                    if (input.hasAttribute('required') && !input.value.trim()) {
                        input.classList.add('is-invalid');
                        isValid = false;
                    } else if (input.type === 'email' && input.value) {
                        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                        if (!emailPattern.test(input.value)) {
                            input.classList.add('is-invalid');
                            isValid = false;
                        } else {
                            input.classList.remove('is-invalid');
                        }
                    } else if (input.type === 'tel' && input.value) {
                        const phonePattern = /^[0-9]{10}$/;
                        if (!phonePattern.test(input.value)) {
                            input.classList.add('is-invalid');
                            isValid = false;
                        } else {
                            input.classList.remove('is-invalid');
                        }
                    } else if (input.value.trim()) {
                        input.classList.remove('is-invalid');
                    }
                });

                // Check subject select
                const subject = document.getElementById('subject');
                if (!subject.value) {
                    subject.classList.add('is-invalid');
                    isValid = false;
                }

                if (isValid) {
                    // Show success message
                    alert('Form submitted successfully! (Demo)');

                    // You can add AJAX submission here
                    // submitForm();
                }
            });

            // Remove validation on input
            form.querySelectorAll('input, select, textarea').forEach(input => {
                input.addEventListener('input', function () {
                    this.classList.remove('is-invalid');
                });
            });
        });

        // Reset function (keeps validation)
        function resetForm() {
            const form = document.getElementById('contactForm');
            form.reset();
            form.querySelectorAll('.is-invalid').forEach(el => {
                el.classList.remove('is-invalid');
            });
            document.getElementById('fileInfo').classList.remove('active');
        }

        // Clear function (completely clears everything)
        function clearForm() {
            const form = document.getElementById('contactForm');
            form.reset();
            form.querySelectorAll('.is-invalid').forEach(el => {
                el.classList.remove('is-invalid');
            });
            document.getElementById('fileInfo').classList.remove('active');
            document.getElementById('fileInput').value = '';

            // Optional: Show confirmation
            alert('Form cleared!');
        }

        // Drag and drop functionality
        const dropArea = document.querySelector('.file-upload-area');

        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropArea.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            dropArea.addEventListener(eventName, highlight, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropArea.addEventListener(eventName, unhighlight, false);
        });

        function highlight() {
            dropArea.style.background = '#f0f5ff';
            dropArea.style.borderColor = '#667eea';
        }

        function unhighlight() {
            dropArea.style.background = '#f8fafc';
            dropArea.style.borderColor = '#e2e8f0';
        }

        dropArea.addEventListener('drop', handleDrop, false);

        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            document.getElementById('fileInput').files = files;

            // Trigger change event
            const event = new Event('change', { bubbles: true });
            document.getElementById('fileInput').dispatchEvent(event);
        }
    </script>
</body>

</html>