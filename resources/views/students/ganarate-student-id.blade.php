@extends('layouts.app')

@section('title', 'Generate Student ID')
@section('page-title', 'Generate Student ID')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('students.index') }}">Students</a></li>
    <li class="breadcrumb-item active">Generate Student ID</li>
@endsection

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        /* Define Monbaiti font */
        @font-face {
            font-family: 'Monbaiti';
            src: url('{{ asset('fonts/monbaiti.ttf') }}') format('truetype');
            font-weight: normal;
            font-style: normal;
            font-display: swap;
        }

        .student-id-card {
            width: 86mm;
            height: 54mm;
            background: url('{{ asset('uploads/id/idcard_bg.png') }}') no-repeat center;
            background-size: cover;
            border-radius: 3mm;
            padding: 3mm;
            box-shadow: 0 2mm 5mm rgba(0, 0, 0, .25);
            margin: 0 auto;
            position: relative;
            font-family: 'Monbaiti', serif !important;
            /* Force Monbaiti font */
        }

        .id-card-profile-box {
            width: 18mm;
            height: 22mm;
            border: 0.3mm solid #ccc;
            border-radius: 1mm;
            overflow: hidden;
            background: #fff;
        }

        .id-card-profile-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .id-card-student-id {
            font-family: 'Monbaiti', serif !important;
            font-size: 4.5mm;
            font-weight: bold;
            line-height: 1.1;
            color: #000;
        }

        .id-card-student-name {
            font-family: 'Monbaiti', serif !important;
            font-size: 4.3mm;
            line-height: 1.2;
            color: #000;
            margin-top: 0.5mm;
        }

        .id-card-address {
            font-family: 'Monbaiti', serif !important;
            font-size: 3mm;
            line-height: 1.2;
            color: #000;
            margin-top: 0.5mm;
            max-width: 45mm;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            /* Show only 2 lines */
            -webkit-box-orient: vertical;
            word-wrap: break-word;
        }

        .id-card-qr-img {
            width: 18mm;
            height: 18mm;
            background: #fff;
            padding: 1mm;
            border-radius: 1mm;
        }

        .id-card-logo {
            width: 30mm;
        }
    </style>

    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6">
                    <h4 class="card-title mb-0">Student ID Cards</h4>
                    <p class="text-muted mb-0">Click preview to view and download ID cards</p>
                </div>
            </div>
        </div>

        <div class="card-body">
            <!-- Simple Date Search Form -->
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <form method="GET" action="{{ route('student-id-card.ganarateStudentId') }}"
                                class="row g-3 align-items-center">
                                <div class="col-md-4">
                                    <label for="search_date" class="form-label">Search by Creation Date</label>
                                    <input type="date" class="form-control" id="search_date" name="search_date"
                                        value="{{ request('search_date') }}" placeholder="Select date">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">&nbsp;</label>
                                    <div>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-search me-1"></i> Search
                                        </button>
                                        @if(request('search_date'))
                                            <a href="{{ route('student-id-card.ganarateStudentId') }}"
                                                class="btn btn-secondary ms-2">
                                                <i class="fas fa-times me-1"></i> Clear
                                            </a>
                                        @endif
                                    </div>
                                </div>
                                @if(request('search_date'))
                                    <div class="col-md-6">
                                        <div class="alert alert-info py-2 mb-0">
                                            <i class="fas fa-info-circle me-2"></i>
                                            Showing students created on: <strong>{{ request('search_date') }}</strong>
                                        </div>
                                    </div>
                                @endif
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats -->
            <div class="row mb-3">
                <div class="col-md-12">
                    <div class="alert alert-info py-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-info-circle me-2"></i>
                                Total: <span id="totalCount">{{ $students->count() }}</span> students
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Students Grid -->
            <div class="row" id="studentsGrid">
                @if($students->count() > 0)
                    @foreach($students as $student)
                        <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12 mb-4 student-card" data-id="{{ $student['custom_id'] }}"
                            data-student='@json($student)'>
                            <div class="card h-100">
                                <div class="card-body p-3">
                                    <!-- Student ID -->
                                    <div class="mb-3">
                                        <h6 class="fw-bold text-primary">{{ $student['custom_id'] }}</h6>
                                    </div>

                                    <!-- Student ID Card Embedded -->
                                    <div class="mb-3">
                                        <div class="student-id-card">
                                            <div class="row h-100">
                                                <!-- LEFT -->
                                                <div class="col-8 d-flex flex-column">
                                                    <div class="id-card-profile-box mt-1 ms-1">
                                                        @php
                                                            $defaultImage = asset('uploads/logo/white_logo.png');
                                                            $studentImage = $student['img_url'] ?? $defaultImage;
                                                        @endphp
                                                        <img src="{{ $studentImage }}" alt="Student Photo"
                                                            onerror="this.onerror=null;this.src='{{ $defaultImage }}'"
                                                            id="student-img-{{ $student['custom_id'] }}">
                                                    </div>

                                                    <div class="ms-1 mt-3">
                                                        <div class="id-card-student-id">{{ $student['custom_id'] ?? 'N/A' }}</div>
                                                        <div class="id-card-student-name mt-1">
                                                            {{ $student['lname'] }}
                                                        </div>
                                                        <div class="id-card-address mt-1"
                                                            style="max-width: 45mm; overflow: hidden; text-overflow: ellipsis; max-lines: 2;">
                                                            {{ $student['address'] ?? 'Address not available' }}
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- RIGHT -->
                                                <div class="col-4 d-flex flex-column align-items-center">
                                                    @php
                                                        $qrData = $student['custom_id'] ?? 'N/A';
                                                        $qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=600x600&data=' . urlencode($qrData);
                                                    @endphp
                                                    <img src="{{ $qrUrl }}" class="id-card-qr-img mt-1" alt="QR Code"
                                                        id="qr-img-{{ $student['custom_id'] }}">
                                                    <img src="{{ asset('uploads/logo/white_logo.png') }}"
                                                        class="id-card-logo mt-auto mb-1" alt="Logo"
                                                        id="logo-img-{{ $student['custom_id'] }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Student Details -->
                                    <div class="student-details mb-3">
                                        <div class="d-flex justify-content-between mb-1">
                                            <small class="text-muted">
                                                <i class="fas fa-user me-1"></i>
                                                {{ $student['fname'] }} {{ $student['lname'] }}
                                            </small>
                                            @if(isset($student['created_at']))
                                                <small class="text-muted">
                                                    <i class="far fa-calendar me-1"></i>
                                                    {{ \Carbon\Carbon::parse($student['created_at'])->format('Y-m-d') }}
                                                </small>
                                            @endif
                                        </div>
                                        <small class="text-muted d-block">
                                            <i class="fas fa-map-marker-alt me-1"></i>
                                            {{ Str::limit($student['address'], 50) }}
                                        </small>
                                    </div>

                                    <!-- Action Button -->
                                    <div class="d-flex justify-content-center mt-2">
                                        <button type="button" class="btn btn-primary preview-single-card"
                                            data-student-id="{{ $student['custom_id'] }}">
                                            <i class="fas fa-eye me-1"></i> Preview & Download
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="col-12">
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            No students found.
                            {{ request('search_date') ? 'No students created on this date.' : 'Please add students first.' }}
                        </div>
                    </div>
                @endif
            </div>

            <!-- Pagination -->
            @if($students->hasPages())
                <div class="row mt-4">
                    <div class="col-md-12">
                        <nav aria-label="Page navigation">
                            <ul class="pagination justify-content-center">
                                {{-- Previous Page Link --}}
                                @if ($students->onFirstPage())
                                    <li class="page-item disabled">
                                        <span class="page-link">&laquo;</span>
                                    </li>
                                @else
                                    <li class="page-item">
                                        <a class="page-link"
                                            href="{{ $students->previousPageUrl() }}{{ request('search_date') ? '&search_date=' . request('search_date') : '' }}"
                                            rel="prev">&laquo;</a>
                                    </li>
                                @endif

                                {{-- Pagination Elements --}}
                                @foreach ($students->getUrlRange(1, $students->lastPage()) as $page => $url)
                                    @if ($page == $students->currentPage())
                                        <li class="page-item active">
                                            <span class="page-link">{{ $page }}</span>
                                        </li>
                                    @else
                                        <li class="page-item">
                                            <a class="page-link"
                                                href="{{ $url }}{{ request('search_date') ? '&search_date=' . request('search_date') : '' }}">{{ $page }}</a>
                                        </li>
                                    @endif
                                @endforeach

                                {{-- Next Page Link --}}
                                @if ($students->hasMorePages())
                                    <li class="page-item">
                                        <a class="page-link"
                                            href="{{ $students->nextPageUrl() }}{{ request('search_date') ? '&search_date=' . request('search_date') : '' }}"
                                            rel="next">&raquo;</a>
                                    </li>
                                @else
                                    <li class="page-item disabled">
                                        <span class="page-link">&raquo;</span>
                                    </li>
                                @endif
                            </ul>
                        </nav>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Single Card Preview Modal -->
    <div class="modal fade" id="singleCardModal" tabindex="-1" aria-labelledby="singleCardModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="singleCardModalLabel">Student ID Card Preview</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <div id="modalCardContainer"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="downloadCardAsPNG()">
                        <i class="fas fa-download me-1"></i> Download as PNG
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
@endpush

@push('scripts')
    <!-- Load html2canvas for image generation -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <!-- Load SweetAlert2 for alerts -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        let selectedStudentData = null;

        document.addEventListener('DOMContentLoaded', function () {
            // Event delegation for preview buttons
            document.addEventListener('click', function (e) {
                if (e.target.classList.contains('preview-single-card') ||
                    e.target.closest('.preview-single-card')) {
                    const button = e.target.classList.contains('preview-single-card') ?
                        e.target : e.target.closest('.preview-single-card');
                    const studentId = button.getAttribute('data-student-id');
                    previewSingleCard(studentId);
                }
            });
        });

        function previewSingleCard(studentId) {
            const cardElement = document.querySelector(`.student-card[data-id="${studentId}"]`);
            if (!cardElement) return;

            try {
                const studentData = JSON.parse(cardElement.getAttribute('data-student'));
                selectedStudentData = studentData;

                // Generate card HTML
                const cardHTML = generateCardHTML(studentData);
                document.getElementById('modalCardContainer').innerHTML = cardHTML;

                // Show modal
                const modal = new bootstrap.Modal(document.getElementById('singleCardModal'));
                modal.show();

                // Load images before showing
                setTimeout(loadImagesForDownload, 100);
            } catch (error) {
                console.error('Error previewing card:', error);
                showAlert('error', 'Error', 'Failed to preview ID card.');
            }
        }

        function generateCardHTML(student) {
            const defaultImage = '{{ asset('uploads/logo/white_logo.png') }}';
            let studentImage = student.img_url || defaultImage;

            // Ensure image is a full URL
            if (studentImage && !studentImage.startsWith('http')) {
                studentImage = '{{ asset('') }}' + studentImage.replace(/^\//, '');
            }

            const qrUrl = `https://api.qrserver.com/v1/create-qr-code/?size=600x600&data=${encodeURIComponent(student.custom_id || 'N/A')}`;

            return `
    <div class="student-id-card" id="downloadableCard">

        <!-- INNER FLEX WRAPPER -->
        <div style="display:flex; flex-direction:row; width:100%; height:100%;">

            <!-- LEFT SIDE -->
            <div style="width:70%; display:flex; flex-direction:column; align-items:flex-start;">

                <div class="id-card-profile-box" style="margin-top:1mm; margin-left:1mm;">
                    <img src="${studentImage}"
                         alt="Student Photo"
                         crossorigin="anonymous"
                         onerror="this.onerror=null;this.src='${defaultImage}'">
                </div>

                <div style="margin-left:1mm; margin-top:3mm; text-align:left;">
                    <div class="id-card-student-id">${student.custom_id || 'N/A'}</div>
                    <div class="id-card-student-name" style="margin-top:1mm;">
                        ${student.lname || ''}
                    </div>
                    <div class="id-card-address"
                         style="margin-top:1mm; max-width:45mm; overflow:hidden;">
                        ${student.address || 'Address not available'}
                    </div>
                </div>
            </div>

            <!-- RIGHT SIDE -->
            <div style="width:30%; display:flex; flex-direction:column; align-items:center;">

                <img src="${qrUrl}"
                     class="id-card-qr-img"
                     alt="QR Code"
                     crossorigin="anonymous"
                     style="margin-top:1mm;">

                <img src="{{ asset('uploads/logo/white_logo.png') }}"
                     class="id-card-logo"
                     alt="Logo"
                     crossorigin="anonymous"
                     style="margin-top:auto; margin-bottom:1mm;">
            </div>

        </div>
    </div>
    `;

        }

        function loadImagesForDownload() {
            // Ensure all images are loaded before download
            const images = document.querySelectorAll('#downloadableCard img');
            images.forEach(img => {
                img.crossOrigin = 'anonymous';
            });
        }

        function downloadCardAsPNG() {
            if (!selectedStudentData) {
                showAlert('warning', 'No Card Selected', 'Please select a card first.');
                return;
            }

            const cardElement = document.getElementById('downloadableCard');
            if (!cardElement) {
                showAlert('error', 'Error', 'Card element not found.');
                return;
            }

            showLoading('Preparing download...');

            setTimeout(() => {
                html2canvas(cardElement, {
                    scale: 3,
                    useCORS: true,
                    allowTaint: true,
                    backgroundColor: null,
                    logging: false,
                    imageTimeout: 15000,

                    onclone: function (clonedDoc) {

                        /* ===============================
                           FORCE STUDENT IMAGE CROP
                           =============================== */
                        clonedDoc
                            .querySelectorAll('.id-card-profile-box img')
                            .forEach(img => {
                                img.style.width = '100%';
                                img.style.height = '100%';
                                img.style.objectFit = 'cover';
                                img.style.objectPosition = 'center';
                            });

                        /* ===============================
                           CORS SAFETY
                           =============================== */
                        clonedDoc.querySelectorAll('img').forEach(img => {
                            img.crossOrigin = 'anonymous';
                        });

                        /* ===============================
                           FONT SAFETY
                           =============================== */
                        const style = clonedDoc.createElement('style');
                        style.textContent = `
                        @font-face {
                            font-family: 'Monbaiti';
                            src: url('{{ asset('fonts/monbaiti.ttf') }}') format('truetype');
                        }

                        .student-id-card,
                        .id-card-student-id,
                        .id-card-student-name,
                        .id-card-address {
                            font-family: 'Monbaiti', serif !important;
                        }
                    `;
                        clonedDoc.head.appendChild(style);
                    }

                }).then(canvas => {
                    const link = document.createElement('a');
                    link.href = canvas.toDataURL('image/png');
                    link.download = `ID_${selectedStudentData.custom_id}_${Date.now()}.png`;

                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);

                    hideLoading();

                    showAlert(
                        'success',
                        'Download Complete',
                        'ID card downloaded successfully as PNG!',
                        2000
                    );
                }).catch(error => {
                    console.error('Download failed:', error);
                    hideLoading();

                    showAlert(
                        'error',
                        'Download Failed',
                        'Failed to download ID card. Please try again.<br><small>Note: Make sure all images are properly loaded.</small>'
                    );
                });
            }, 500);
        }


        function showLoading(message = 'Please wait...') {
            Swal.fire({
                title: 'Processing',
                text: message,
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });
        }

        function hideLoading() {
            Swal.close();
        }

        function showAlert(icon, title, text, timer = null) {
            const config = {
                icon: icon,
                title: title,
                html: text,
                confirmButtonColor: icon === 'error' ? '#d33' : '#3085d6',
            };

            if (timer) {
                config.timer = timer;
                config.showConfirmButton = false;
            }

            Swal.fire(config);
        }
    </script>
@endpush