@extends('layouts.app')

@section('title', 'Manage Students')
@section('page-title', 'Students Management')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Students</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-xl-3 col-md-6">
                    <div class="card stat-card bg-primary bg-gradient">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <h4 class="card-title text-white">Total Students</h4>
                                    <h2 class="text-white" id="totalStudents">0</h2>
                                </div>
                                <div class="flex-shrink-0">
                                    <i class="fas fa-users fa-2x text-white-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card stat-card bg-success bg-gradient">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <h4 class="card-title text-white">Active</h4>
                                    <h2 class="text-white" id="activeStudents">0</h2>
                                </div>
                                <div class="flex-shrink-0">
                                    <i class="fas fa-graduation-cap fa-2x text-white-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card stat-card bg-info bg-gradient">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <h4 class="card-title text-white">Inactive</h4>
                                    <h2 class="text-white" id="inactiveStudents">0</h2>
                                </div>
                                <div class="flex-shrink-0">
                                    <i class="fas fa-male fa-2x text-white-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card stat-card bg-warning bg-gradient">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <h4 class="card-title text-white">Admission Not Paid</h4>
                                    <h2 class="text-white" id="notPaidStudents">0</h2>
                                </div>
                                <div class="flex-shrink-0">
                                    <i class="fas fa-female fa-2x text-white-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Card -->
            <div class="card custom-card">
                <div class="card-header bg-transparent">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-1">Students Management</h5>
                            <p class="text-muted mb-0">Manage all students and their information</p>
                        </div>
                        <div class="d-flex gap-2">
                            <button class="btn btn-outline-primary" onclick="loadStudents()" title="Refresh">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                            <a href="{{ route('students.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Add New Student
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body position-relative">
                    <!-- Loading Spinner -->
                    <div id="loadingSpinner" class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2 text-muted">Loading students...</p>
                    </div>

                    <!-- Error Message -->
                    <div id="errorMessage" class="alert alert-danger d-none" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <span id="errorText"></span>
                    </div>

                    <!-- Action Bar -->
                    <div class="d-none" id="actionBar">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <!-- Filter Buttons -->
                                <div class="btn-group btn-group-sm me-2">
                                    <button type="button" class="btn btn-outline-secondary active" id="filterAll"
                                        data-status="">All</button>
                                    <button type="button" class="btn btn-outline-success" id="filterActive"
                                        data-status="active">Active</button>
                                    <button type="button" class="btn btn-outline-secondary" id="filterInactive"
                                        data-status="inactive">Inactive</button>
                                </div>

                                <!-- Grade Filter -->
                                <div class="d-flex align-items-center me-2">
                                    <label for="gradeFilter" class="form-label text-muted mb-0 me-2">Grade:</label>
                                    <select class="form-select form-select-sm" id="gradeFilter" style="width: 120px;">
                                        <option value="">All Grades</option>
                                        <!-- Grades will be dynamically populated via JavaScript -->
                                    </select>
                                </div>

                                <!-- Rows Per Page -->
                                <div class="d-flex align-items-center me-2">
                                    <label for="rowsPerPage" class="form-label text-muted mb-0 me-2">Show:</label>
                                    <select class="form-select form-select-sm" id="rowsPerPage" style="width: 80px;">
                                        <option value="10">10</option>
                                        <option value="25">25</option>
                                        <option value="50">50</option>
                                        <option value="100">100</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Search Box -->
                            <div class="input-group input-group-sm" style="width: 280px;">
                                <span class="input-group-text bg-transparent">
                                    <i class="fas fa-search"></i>
                                </span>
                                <input type="text" class="form-control" placeholder="Search students..." id="searchInput"
                                    autocomplete="off">
                                <button class="btn btn-outline-secondary" type="button" id="clearSearchBtn" title="Clear">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Students Table -->
                    <div class="table-responsive" id="studentsTableContainer">
                        <table class="table table-hover" id="studentsTable">
                            <thead class="table-primary">
                                <tr>
                                    <th width="60" class="text-center">#</th>
                                    <th>Student</th>
                                    <th>Contact</th>
                                    <th class="text-center">Grade</th>
                                    <th class="text-center">Gender</th>
                                    <th class="text-center">Type</th>
                                    <th class="text-center">Status</th>
                                    <th width="140" class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="studentsTableBody">
                                <!-- Students will be loaded here via JavaScript -->
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="row mt-3 d-none" id="paginationSection">
                        <div class="col-md-6">
                            <div class="text-muted" id="paginationInfo">
                                Showing <span id="startRecord">0</span> to <span id="endRecord">0</span> of <span
                                    id="totalRecords">0</span> entries
                            </div>
                        </div>
                        <div class="col-md-6">
                            <nav aria-label="Students pagination">
                                <ul class="pagination justify-content-end mb-0" id="paginationLinks">
                                </ul>
                            </nav>
                        </div>
                    </div>

                    <!-- Empty State -->
                    <div id="emptyState" class="text-center py-5 d-none">
                        <div class="empty-state-icon">
                            <i class="fas fa-users fa-4x text-muted mb-4"></i>
                        </div>
                        <h4 class="text-muted">No Students Found</h4>
                        <p class="text-muted mb-4">There are no students in the database yet.</p>
                        <a href="{{ route('students.create') }}" class="btn btn-primary btn-lg">
                            <i class="fas fa-plus me-2"></i>Add First Student
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Activate Student Modal -->
    <div class="modal fade" id="activateStudentModal" tabindex="-1" aria-labelledby="activateStudentModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-user-check me-2"></i>Activate Student
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to activate this student?</p>
                    <div class="student-info bg-light p-3 rounded">
                        <strong id="activateStudentName"></strong><br>
                        <small class="text-muted" id="activateStudentEmail"></small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancel
                    </button>
                    <button type="button" class="btn btn-success" id="confirmActivateBtn">
                        <i class="fas fa-user-check me-2"></i>Activate Student
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Deactivate Student Modal -->
    <div class="modal fade" id="deactivateStudentModal" tabindex="-1" aria-labelledby="deactivateStudentModalLabel"
        aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title" id="deactivateStudentModalLabel">
                        <i class="fas fa-user-slash me-2"></i>Deactivate Student
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to deactivate this student?</p>
                    <div class="student-info bg-light p-3 rounded">
                        <strong id="deactivateStudentName"></strong><br>
                        <small class="text-muted" id="deactivateStudentEmail"></small>
                    </div>
                    <div class="alert alert-warning mt-3">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        This student will no longer be able to access the system.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancel
                    </button>
                    <button type="button" class="btn btn-warning" id="confirmDeactivateBtn"
                        aria-describedby="deactivateWarning">
                        <i class="fas fa-user-slash me-2"></i>Deactivate Student
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .stat-card {
            border: none;
            border-radius: 15px;
            transition: transform 0.2s ease-in-out;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .custom-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .table th {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            font-weight: 600;
            border: none;
            padding: 1rem 0.75rem;
            font-size: 0.9rem;
        }

        .table td {
            vertical-align: middle;
            padding: 1rem 0.75rem;
            border-color: #f8f9fa;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(0, 123, 255, 0.05);
            transform: translateY(-1px);
            transition: all 0.2s ease;
        }

        .badge.rounded-pill {
            padding: 0.5em 0.8em;
            font-size: 0.75rem;
        }

        .avatar-sm {
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .text-pink {
            color: #e83e8c !important;
        }

        .btn-group .btn {
            border-radius: 0;
        }

        .btn-group .btn:first-child {
            border-top-left-radius: 6px;
            border-bottom-left-radius: 6px;
        }

        .btn-group .btn:last-child {
            border-top-right-radius: 6px;
            border-bottom-right-radius: 6px;
        }

        .badge {
            font-size: 0.75rem;
            padding: 0.5em 0.75em;
            border-radius: 10px;
        }

        .pagination .page-link {
            border-radius: 8px;
            margin: 0 2px;
            border: 1px solid #dee2e6;
        }

        .pagination .page-item.active .page-link {
            background: linear-gradient(135deg, #2c3e50, #34495e);
            border-color: #2c3e50;
        }

        .btn-group .btn.active {
            background-color: #2c3e50;
            color: white;
            border-color: #2c3e50;
        }
    </style>
@endpush

@push('scripts')
    <script>
        // Global variables for pagination
        let currentPage = 1;
        let totalPages = 1;
        let rowsPerPage = 10;
        let totalRecords = 0;
        let currentStatusFilter = '';
        let currentGradeFilter = '';
        let currentSearch = '';
        let allGrades = []; // Store grades for filtering

        // Wait for the DOM to be loaded
        document.addEventListener('DOMContentLoaded', function () {
            initializeStudentsPage();
        });

        function initializeStudentsPage() {
            // Load grades on page load
            loadGrades();

            // Initial load of students
            loadStudents(currentPage);

            // Event listeners
            const rowsPerPageEl = document.getElementById('rowsPerPage');
            const gradeFilterEl = document.getElementById('gradeFilter');
            const searchInputEl = document.getElementById('searchInput');
            const clearSearchBtn = document.getElementById('clearSearchBtn');
            const filterAllEl = document.getElementById('filterAll');
            const filterActiveEl = document.getElementById('filterActive');
            const filterInactiveEl = document.getElementById('filterInactive');
            const confirmActivateBtn = document.getElementById('confirmActivateBtn');
            const confirmDeactivateBtn = document.getElementById('confirmDeactivateBtn');

            if (rowsPerPageEl) {
                rowsPerPageEl.addEventListener('change', function () {
                    rowsPerPage = parseInt(this.value);
                    currentPage = 1;
                    loadStudents(currentPage);
                });
            }

            if (gradeFilterEl) {
                gradeFilterEl.addEventListener('change', function () {
                    currentGradeFilter = this.value;
                    currentPage = 1;
                    loadStudents(currentPage);
                });
            }

            if (searchInputEl) {
                searchInputEl.addEventListener('input', debounce(function (e) {
                    currentSearch = e.target.value;
                    currentPage = 1;
                    loadStudents(currentPage);
                }, 300));
            }

            if (clearSearchBtn) {
                clearSearchBtn.addEventListener('click', function () {
                    document.getElementById('searchInput').value = '';
                    currentSearch = '';
                    currentPage = 1;
                    loadStudents(currentPage);
                });
            }

            // Filter functionality
            if (filterAllEl) {
                filterAllEl.addEventListener('click', function () {
                    setActiveFilter(this, '');
                });
            }

            if (filterActiveEl) {
                filterActiveEl.addEventListener('click', function () {
                    setActiveFilter(this, 'active');
                });
            }

            if (filterInactiveEl) {
                filterInactiveEl.addEventListener('click', function () {
                    setActiveFilter(this, 'inactive');
                });
            }

            // Activate/Deactivate modal events
            if (confirmActivateBtn) {
                confirmActivateBtn.addEventListener('click', confirmActivateStudent);
            }

            if (confirmDeactivateBtn) {
                confirmDeactivateBtn.addEventListener('click', confirmDeactivateStudent);
            }
        }

        // Load grades from API
        function loadGrades() {
            const gradeFilter = document.getElementById('gradeFilter');
            if (!gradeFilter) return;

            fetch("{{ url('/api/grades/dropdown') }}")
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.status === 'success') {
                        allGrades = data.data;
                        populateGradeFilter(allGrades);
                    } else {
                        throw new Error(data.message || 'Failed to load grades');
                    }
                })
                .catch(error => {
                    console.error('Error loading grades:', error);
                });
        }

        // Populate grade filter dropdown
        function populateGradeFilter(grades) {
            const gradeFilter = document.getElementById('gradeFilter');
            if (!gradeFilter) return;

            // Clear existing options except the first one ("All Grades")
            while (gradeFilter.options.length > 1) {
                gradeFilter.remove(1);
            }

            // Add grade options from API
            grades.forEach(grade => {
                const option = document.createElement('option');
                option.value = grade.id;
                option.textContent = `Grade ${grade.grade_name}`;
                gradeFilter.appendChild(option);
            });
        }

        function setActiveFilter(button, status) {
            // Remove active class from all filter buttons
            document.querySelectorAll('#actionBar .btn-group .btn').forEach(btn => {
                btn.classList.remove('active');
            });

            // Add active class to clicked button
            button.classList.add('active');

            currentStatusFilter = status;
            currentPage = 1;
            loadStudents(currentPage);
        }

        // Main function to load students from API with pagination
        function loadStudents(page = 1) {
            showLoadingState();

            // Build query parameters
            let params = new URLSearchParams({
                page: page,
                per_page: rowsPerPage
            });

            // Add search parameter
            if (currentSearch) {
                params.append('search', currentSearch);
            }

            // Add Active/Inactive filter parameter
            if (currentStatusFilter === 'active') {
                params.append('is_active', '1');
            } else if (currentStatusFilter === 'inactive') {
                params.append('is_active', '0');
            }

            // Add Grade filter parameter
            if (currentGradeFilter && currentGradeFilter !== '') {
                params.append('grade_id', currentGradeFilter);
            }

            fetch(`{{ url('/api/students') }}?${params.toString()}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.status === 'success') {
                        const students = data.data.students || [];
                        const pagination = data.data.pagination;

                        // Update pagination variables
                        currentPage = pagination.current_page;
                        totalPages = pagination.last_page;
                        totalRecords = pagination.total;
                        rowsPerPage = pagination.per_page;

                        renderStudentsTable(students);
                        updatePagination();
                        updateStatistics(students);
                        showContentState();
                    } else {
                        throw new Error(data.message || 'Failed to load students');
                    }
                })
                .catch(error => {
                    console.error('Error loading students:', error);
                    showErrorState('Error loading students: ' + error.message);
                });
        }

        function renderStudentsTable(students) {
            const tbody = document.getElementById('studentsTableBody');
            const tableContainer = document.getElementById('studentsTableContainer');
            const emptyState = document.getElementById('emptyState');
            const paginationSection = document.getElementById('paginationSection');

            if (!tbody) return;

            tbody.innerHTML = '';

            if (students.length === 0) {
                if (tableContainer) tableContainer.classList.add('d-none');
                if (paginationSection) paginationSection.classList.add('d-none');
                if (emptyState) emptyState.classList.remove('d-none');
                return;
            }

            if (tableContainer) tableContainer.classList.remove('d-none');
            if (paginationSection) paginationSection.classList.remove('d-none');
            if (emptyState) emptyState.classList.add('d-none');

            students.forEach((student, index) => {
                // Calculate row number based on pagination
                const rowNumber = ((currentPage - 1) * rowsPerPage) + index + 1;

                const isActive = student.is_active;
                const statusBadge = isActive ?
                    '<span class="badge bg-success rounded-pill"><i class="fas fa-circle me-1"></i>Active</span>' :
                    '<span class="badge bg-secondary rounded-pill"><i class="fas fa-circle me-1"></i>Inactive</span>';

                const gender = (student.gender || '').toLowerCase();
                const genderIcon = gender === 'male' ?
                    '<i class="fas fa-mars text-primary"></i>' :
                    gender === 'female' ?
                        '<i class="fas fa-venus text-pink"></i>' :
                        gender === 'other' ?
                            '<i class="fas fa-genderless text-muted"></i>' :
                            '<i class="fas fa-question text-secondary"></i>';

                // Determine class type (online/offline)
                const classType = student.class_type || 'offline';
                const typeBadge = classType === 'online' ?
                    '<span class="badge bg-info rounded-pill"><i class="fas fa-globe me-1"></i>Online</span>' :
                    '<span class="badge bg-secondary rounded-pill"><i class="fas fa-building me-1"></i>Offline</span>';

                // Use placeholder icon if img_url is null or invalid
                const avatarContent = student.img_url && isValidImageUrl(student.img_url) ?
                    `<img src="${student.img_url}" alt="${student.initial_name}" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">` :
                    `<div class="avatar-sm bg-primary bg-gradient rounded-circle text-white d-flex align-items-center justify-content-center">
                            <span class="fw-bold">${student.full_name ? student.initial_name.charAt(0) : ''}${student.full_name ? student.initial_name.charAt(0) : ''}</span>
                        </div>`;

                const row = `
                        <tr class="align-middle">
                            <td class="text-center fw-bold text-muted">${rowNumber}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    ${avatarContent}
                                    <div class="ms-3">
                                        <h6 class="mb-0 fw-bold">${student.initial_name || ''}</h6>
                                        <small class="text-muted">${student.custom_id || 'No ID'}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <div class="mb-1">
                                        <i class="fas fa-envelope text-muted me-2"></i>
                                        <small>${student.email || 'No email'}</small>
                                    </div>
                                    <div>
                                        <i class="fas fa-phone text-muted me-2"></i>
                                        <small>${student.mobile || 'No phone'}</small>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-light text-dark border">
                                    <i class="fas fa-graduation-cap me-1 text-primary"></i>
                                    ${student.grade ? student.grade.grade_name : 'N/A'}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="fs-5">${genderIcon}</span>
                            </td>
                            <td class="text-center">
                                ${typeBadge}
                            </td>
                            <td class="text-center">
                                ${statusBadge}
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary rounded-start" title="View" 
                                            onclick="viewStudent('${student.id}')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-outline-warning" title="Edit" 
                                            onclick="editStudent('${student.id}')">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    ${isActive ?
                        `<button class="btn btn-outline-danger rounded-end" title="Deactivate" 
                                                onclick="showDeactivateModal(${student.id}, '${escapeHtml(student.full_name)} ${escapeHtml(student.lname)}', '${escapeHtml(student.email || 'No email')}')">
                                            <i class="fas fa-user-slash"></i>
                                        </button>` :
                        `<button class="btn btn-outline-success rounded-end" title="Activate" 
                                                onclick="showActivateModal(${student.id}, '${escapeHtml(student.initial_name)} ${escapeHtml(student.lname)}', '${escapeHtml(student.email || 'No email')}')">
                                            <i class="fas fa-user-check"></i>
                                        </button>`
                    }
                                </div>
                            </td>
                        </tr>
                    `;
                tbody.innerHTML += row;
            });
        }

        function updatePagination() {
            const startRecord = totalRecords > 0 ? ((currentPage - 1) * rowsPerPage) + 1 : 0;
            const endRecord = Math.min(currentPage * rowsPerPage, totalRecords);

            const startRecordEl = document.getElementById('startRecord');
            const endRecordEl = document.getElementById('endRecord');
            const totalRecordsEl = document.getElementById('totalRecords');

            if (startRecordEl) startRecordEl.textContent = startRecord;
            if (endRecordEl) endRecordEl.textContent = endRecord;
            if (totalRecordsEl) totalRecordsEl.textContent = totalRecords;

            renderPaginationLinks();
        }

        function renderPaginationLinks() {
            const paginationLinks = document.getElementById('paginationLinks');
            if (!paginationLinks) return;

            paginationLinks.innerHTML = '';

            // Previous button
            const prevLi = document.createElement('li');
            prevLi.className = `page-item ${currentPage === 1 ? 'disabled' : ''}`;
            prevLi.innerHTML = `
                    <a class="page-link" href="#" onclick="changePage(${currentPage - 1}); return false;" aria-label="Previous">
                        <span aria-hidden="true">Previous</span>
                    </a>
                `;
            paginationLinks.appendChild(prevLi);

            // Page numbers
            const maxVisiblePages = 5;
            let startPage = Math.max(1, currentPage - Math.floor(maxVisiblePages / 2));
            let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);

            if (endPage - startPage + 1 < maxVisiblePages) {
                startPage = Math.max(1, endPage - maxVisiblePages + 1);
            }

            for (let i = startPage; i <= endPage; i++) {
                const li = document.createElement('li');
                li.className = `page-item ${currentPage === i ? 'active' : ''}`;
                li.innerHTML = `<a class="page-link" href="#" onclick="changePage(${i}); return false;">${i}</a>`;
                paginationLinks.appendChild(li);
            }

            // Next button
            const nextLi = document.createElement('li');
            nextLi.className = `page-item ${currentPage === totalPages ? 'disabled' : ''}`;
            nextLi.innerHTML = `
                    <a class="page-link" href="#" onclick="changePage(${currentPage + 1}); return false;" aria-label="Next">
                        <span aria-hidden="true">Next</span>
                    </a>
                `;
            paginationLinks.appendChild(nextLi);
        }

        function changePage(page) {
            if (page < 1 || page > totalPages) return;
            loadStudents(page);
        }

        function updateStatistics(students) {
            // Counts from current page data only
            const activeStudents = students.filter(s => s.is_active).length;
            const inactiveStudents = students.filter(s => !s.is_active).length;
            const notPaidStudents = students.filter(s => s.admission == 0).length;

            // Elements
            const totalStudentsEl = document.getElementById('totalStudents');
            const activeStudentsEl = document.getElementById('activeStudents');
            const inactiveStudentsEl = document.getElementById('inactiveStudents');
            const notPaidStudentsEl = document.getElementById('notPaidStudents');

            if (totalStudentsEl) totalStudentsEl.textContent = totalRecords; // Use total from pagination
            if (activeStudentsEl) activeStudentsEl.textContent = activeStudents;
            if (inactiveStudentsEl) inactiveStudentsEl.textContent = inactiveStudents;
            if (notPaidStudentsEl) notPaidStudentsEl.textContent = notPaidStudents;
        }

        function showActivateModal(studentId, studentName, studentEmail) {
            const activateStudentName = document.getElementById('activateStudentName');
            const activateStudentEmail = document.getElementById('activateStudentEmail');
            const confirmActivateBtn = document.getElementById('confirmActivateBtn');

            if (activateStudentName) activateStudentName.textContent = studentName;
            if (activateStudentEmail) activateStudentEmail.textContent = studentEmail;

            const modal = new bootstrap.Modal(document.getElementById('activateStudentModal'));
            modal.show();

            // Store student ID for the confirm action
            if (confirmActivateBtn) confirmActivateBtn.setAttribute('data-student-id', studentId);
        }

        function showDeactivateModal(studentId, studentName, studentEmail) {
            const deactivateStudentName = document.getElementById('deactivateStudentName');
            const deactivateStudentEmail = document.getElementById('deactivateStudentEmail');
            const confirmDeactivateBtn = document.getElementById('confirmDeactivateBtn');

            if (deactivateStudentName) deactivateStudentName.textContent = studentName;
            if (deactivateStudentEmail) deactivateStudentEmail.textContent = studentEmail;

            const modal = new bootstrap.Modal(document.getElementById('deactivateStudentModal'));
            modal.show();

            // Store student ID for the confirm action
            if (confirmDeactivateBtn) confirmDeactivateBtn.setAttribute('data-student-id', studentId);
        }

        function confirmActivateStudent() {
            const confirmActivateBtn = document.getElementById('confirmActivateBtn');
            if (!confirmActivateBtn) return;

            const studentId = confirmActivateBtn.getAttribute('data-student-id');

            fetch(`/api/students/${studentId}/reactivate`, {
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                    'Content-Type': 'application/json'
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        // Close modal
                        const modal = bootstrap.Modal.getInstance(document.getElementById('activateStudentModal'));
                        if (modal) modal.hide();

                        // Show success message and reload
                        showAlert('Student activated successfully!', 'success');
                        loadStudents(currentPage);
                    } else {
                        throw new Error(data.message || 'Failed to activate student');
                    }
                })
                .catch(error => {
                    console.error('Error activating student:', error);
                    showAlert('Error activating student: ' + error.message, 'danger');
                });
        }

        function confirmDeactivateStudent() {
            const confirmDeactivateBtn = document.getElementById('confirmDeactivateBtn');
            if (!confirmDeactivateBtn) return;

            const studentId = confirmDeactivateBtn.getAttribute('data-student-id');

            fetch(`/api/students/${studentId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        // Close modal
                        const modal = bootstrap.Modal.getInstance(document.getElementById('deactivateStudentModal'));
                        if (modal) modal.hide();

                        // Show success message and reload
                        showAlert('Student deactivated successfully!', 'success');
                        loadStudents(currentPage);
                    } else {
                        throw new Error(data.message || 'Failed to deactivate student');
                    }
                })
                .catch(error => {
                    console.error('Error deactivating student:', error);
                    showAlert('Error deactivating student: ' + error.message, 'danger');
                });
        }

        function viewStudent(studentId) {
            window.location.href = `/students/${studentId}`;
        }

        function editStudent(studentId) {
            window.location.href = `/students/${studentId}/edit`;
        }

        // Helper functions
        function showLoadingState() {
            const loadingSpinner = document.getElementById('loadingSpinner');
            const actionBar = document.getElementById('actionBar');
            const studentsTableContainer = document.getElementById('studentsTableContainer');
            const paginationSection = document.getElementById('paginationSection');
            const emptyState = document.getElementById('emptyState');
            const errorMessage = document.getElementById('errorMessage');

            if (loadingSpinner) loadingSpinner.classList.remove('d-none');
            if (actionBar) actionBar.classList.add('d-none');
            if (studentsTableContainer) studentsTableContainer.classList.add('d-none');
            if (paginationSection) paginationSection.classList.add('d-none');
            if (emptyState) emptyState.classList.add('d-none');
            if (errorMessage) errorMessage.classList.add('d-none');
        }

        function showContentState() {
            const loadingSpinner = document.getElementById('loadingSpinner');
            const actionBar = document.getElementById('actionBar');

            if (loadingSpinner) loadingSpinner.classList.add('d-none');
            if (actionBar) actionBar.classList.remove('d-none');
        }

        function showErrorState(message) {
            const loadingSpinner = document.getElementById('loadingSpinner');
            const errorMessage = document.getElementById('errorMessage');
            const errorText = document.getElementById('errorText');

            if (loadingSpinner) loadingSpinner.classList.add('d-none');
            if (errorMessage) errorMessage.classList.remove('d-none');
            if (errorText) errorText.textContent = message;
        }

        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }

        function isValidImageUrl(url) {
            return url &&
                typeof url === 'string' &&
                url.length > 0 &&
                !url.includes('undefined') &&
                !url.includes('null');
        }

        function escapeHtml(unsafe) {
            if (unsafe === null || unsafe === undefined) return '';
            return String(unsafe)
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }

        function showAlert(message, type) {
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
            alertDiv.innerHTML = `
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                `;

            const container = document.querySelector('.container') || document.querySelector('.card-body');
            if (container) {
                container.insertBefore(alertDiv, container.firstChild);

                setTimeout(() => {
                    if (alertDiv.parentNode) {
                        alertDiv.remove();
                    }
                }, 5000);
            }
        }
    </script>
@endpush