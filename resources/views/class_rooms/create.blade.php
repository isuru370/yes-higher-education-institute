@extends('layouts.app')

@section('title', 'Create Class Room')
@section('page-title', 'Create New Class Room')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('class_rooms.index') }}">Class Rooms</a></li>
    <li class="breadcrumb-item active">Class Room Create</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <!-- Create Class Room Card -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Class Room Information</h5>
                </div>
                <div class="card-body">
                    <form id="createClassRoomForm" action="{{ url('/api/class-rooms') }}" method="POST">
                        @csrf
                        <div class="row">
                            <!-- Left Column -->
                            <div class="col-md-6">
                                <!-- Class Name -->
                                <div class="mb-3">
                                    <label for="class_name" class="form-label">Class Name <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="class_name" name="class_name" required>
                                    <div class="invalid-feedback" id="class_name_error"></div>
                                </div>

                                <!-- Teacher Percentage -->
                                <div class="mb-3">
                                    <label for="teacher_percentage" class="form-label">
                                        Teacher Percentage (%) <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" class="form-control" id="teacher_percentage"
                                        name="teacher_percentage" min="0" max="100" step="0.01"
                                        placeholder="Enter percentage (eg: 30)" required>
                                    <div class="invalid-feedback" id="teacher_percentage_error"></div>
                                </div>

                                <!-- Teacher Dropdown -->
                                <div class="mb-3">
                                    <label for="teacher_id" class="form-label">Teacher <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select" id="teacher_id" name="teacher_id" required>
                                        <option value="">Select Teacher</option>
                                    </select>
                                    <div class="invalid-feedback" id="teacher_id_error"></div>
                                </div>
                            </div>

                            <!-- Right Column -->
                            <div class="col-md-6">
                                <!-- Class Type -->
                                <div class="mb-3">
                                    <label for="class_type" class="form-label">
                                        Class Type <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select" id="class_type" name="class_type" required>
                                        <option value="">Select Class Type</option>
                                        <option value="offline">Offline</option>
                                        <option value="online">Online</option>
                                    </select>
                                    <div class="invalid-feedback" id="class_type_error"></div>
                                </div>

                                <!-- NEW: Medium Dropdown -->
                                <div class="mb-3">
                                    <label for="medium" class="form-label">
                                        <i class="fas fa-language me-2"></i>Medium <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select" id="medium" name="medium" required>
                                        <option value="">Select Medium</option>
                                        <option value="sinhala">සිංහල (Sinhala)</option>
                                        <option value="english">English</option>
                                        <option value="tamil">தமிழ் (Tamil)</option>
                                    </select>
                                    <div class="invalid-feedback" id="medium_error"></div>
                                </div>

                                <!-- Grade Dropdown with Add Button -->
                                <div class="mb-3">
                                    <label for="grade_id" class="form-label">Grade <span
                                            class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <select class="form-select" id="grade_id" name="grade_id" required>
                                            <option value="">Select Grade</option>
                                        </select>
                                        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal"
                                            data-bs-target="#addGradeModal">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                    <div class="invalid-feedback" id="grade_id_error"></div>
                                </div>

                                <!-- Subject Dropdown with Add Button -->
                                <div class="mb-3">
                                    <label for="subject_id" class="form-label">Subject <span
                                            class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <select class="form-select" id="subject_id" name="subject_id" required>
                                            <option value="">Select Subject</option>
                                        </select>
                                        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal"
                                            data-bs-target="#addSubjectModal">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                    <div class="invalid-feedback" id="subject_id_error"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('class_rooms.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-times me-2"></i>Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary" id="submitBtn">
                                        <i class="fas fa-save me-2"></i>Create Class Room
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Grades Table Card -->
            <div class="card mb-4">
                <div class="card-header bg-transparent">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-1">Grades Management</h5>
                            <p class="text-muted mb-0">Manage all grades</p>
                        </div>
                        <div class="d-flex gap-2">
                            <button class="btn btn-outline-primary" onclick="loadGradesTable()" title="Refresh">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Search and Actions -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-text bg-transparent">
                                    <i class="fas fa-search"></i>
                                </span>
                                <input type="text" class="form-control" placeholder="Search grades..." id="gradeSearch">
                            </div>
                        </div>
                    </div>

                    <!-- Grades Table -->
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-primary">
                                <tr>
                                    <th width="60">#</th>
                                    <th>Grade Name</th>
                                    <th>Created At</th>
                                    <th width="120" class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="gradesTableBody">
                                <!-- Grades will be loaded here -->
                            </tbody>
                        </table>
                    </div>

                    <!-- Loading State -->
                    <div id="gradesLoading" class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2 text-muted">Loading grades...</p>
                    </div>

                    <!-- Empty State -->
                    <div id="gradesEmpty" class="text-center py-5 d-none">
                        <div class="empty-state-icon">
                            <i class="fas fa-graduation-cap fa-4x text-muted mb-4"></i>
                        </div>
                        <h4 class="text-muted">No Grades Found</h4>
                        <p class="text-muted mb-4">There are no grades in the database yet.</p>
                    </div>
                </div>
            </div>

            <!-- Subjects Table Card -->
            <div class="card">
                <div class="card-header bg-transparent">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-1">Subjects Management</h5>
                            <p class="text-muted mb-0">Manage all subjects</p>
                        </div>
                        <div class="d-flex gap-2">
                            <button class="btn btn-outline-primary" onclick="loadSubjectsTable()" title="Refresh">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Search and Actions -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-text bg-transparent">
                                    <i class="fas fa-search"></i>
                                </span>
                                <input type="text" class="form-control" placeholder="Search subjects..." id="subjectSearch">
                            </div>
                        </div>
                    </div>

                    <!-- Subjects Table -->
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-primary">
                                <tr>
                                    <th width="60">#</th>
                                    <th>Subject Name</th>
                                    <th>Created At</th>
                                    <th width="120" class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="subjectsTableBody">
                                <!-- Subjects will be loaded here -->
                            </tbody>
                        </table>
                    </div>

                    <!-- Loading State -->
                    <div id="subjectsLoading" class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2 text-muted">Loading subjects...</p>
                    </div>

                    <!-- Empty State -->
                    <div id="subjectsEmpty" class="text-center py-5 d-none">
                        <div class="empty-state-icon">
                            <i class="fas fa-book fa-4x text-muted mb-4"></i>
                        </div>
                        <h4 class="text-muted">No Subjects Found</h4>
                        <p class="text-muted mb-4">There are no subjects in the database yet.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Grade Modal -->
    <div class="modal fade" id="addGradeModal" tabindex="-1" aria-labelledby="addGradeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="addGradeModalLabel">
                        <i class="fas fa-plus me-2"></i>Add New Grade
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addGradeForm">
                        <div class="mb-3">
                            <label for="grade_name" class="form-label">Grade Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="grade_name" name="grade_name" required>
                            <div class="invalid-feedback" id="grade_name_error"></div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancel
                    </button>
                    <button type="button" class="btn btn-primary" id="saveGradeBtn">
                        <i class="fas fa-save me-2"></i>Save Grade
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Grade Modal -->
    <div class="modal fade" id="editGradeModal" tabindex="-1" aria-labelledby="editGradeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title" id="editGradeModalLabel">
                        <i class="fas fa-edit me-2"></i>Edit Grade
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editGradeForm">
                        <input type="hidden" id="edit_grade_id">
                        <div class="mb-3">
                            <label for="edit_grade_name" class="form-label">Grade Name <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_grade_name" name="grade_name" required>
                            <div class="invalid-feedback" id="edit_grade_name_error"></div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancel
                    </button>
                    <button type="button" class="btn btn-warning" id="updateGradeBtn">
                        <i class="fas fa-save me-2"></i>Update Grade
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Subject Modal -->
    <div class="modal fade" id="addSubjectModal" tabindex="-1" aria-labelledby="addSubjectModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="addSubjectModalLabel">
                        <i class="fas fa-plus me-2"></i>Add New Subject
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addSubjectForm">
                        <div class="mb-3">
                            <label for="subject_name" class="form-label">Subject Name <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="subject_name" name="subject_name" required>
                            <div class="invalid-feedback" id="subject_name_error"></div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancel
                    </button>
                    <button type="button" class="btn btn-primary" id="saveSubjectBtn">
                        <i class="fas fa-save me-2"></i>Save Subject
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Subject Modal -->
    <div class="modal fade" id="editSubjectModal" tabindex="-1" aria-labelledby="editSubjectModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title" id="editSubjectModalLabel">
                        <i class="fas fa-edit me-2"></i>Edit Subject
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editSubjectForm">
                        <input type="hidden" id="edit_subject_id">
                        <div class="mb-3">
                            <label for="edit_subject_name" class="form-label">Subject Name <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_subject_name" name="subject_name" required>
                            <div class="invalid-feedback" id="edit_subject_name_error"></div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancel
                    </button>
                    <button type="button" class="btn btn-warning" id="updateSubjectBtn">
                        <i class="fas fa-save me-2"></i>Update Subject
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            border-radius: 15px 15px 0 0 !important;
            border: none;
            padding: 1.5rem;
        }

        .card-header.bg-transparent {
            background: transparent !important;
            color: inherit;
            border-bottom: 1px solid #dee2e6;
        }

        .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 0.5rem;
        }

        .form-control,
        .form-select {
            border-radius: 8px;
            border: 1px solid #ced4da;
            padding: 0.75rem 1rem;
            transition: all 0.2s ease;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        .input-group .btn {
            border-radius: 0 8px 8px 0;
            border-left: none;
        }

        .input-group .form-select {
            border-radius: 8px 0 0 8px;
        }

        .btn {
            border-radius: 8px;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .btn-primary {
            background: linear-gradient(135deg, #007bff, #0056b3);
            border: none;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 123, 255, 0.3);
        }

        .table th {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            font-weight: 600;
            border: none;
        }

        .table td {
            vertical-align: middle;
            border-color: #f8f9fa;
        }

        .empty-state-icon {
            opacity: 0.5;
        }
    </style>
@endpush

@push('scripts')
    <script>
        let allGrades = [];
        let allSubjects = [];

        document.addEventListener('DOMContentLoaded', function () {
            // Load dropdown data
            loadTeachers();
            loadGrades();
            loadSubjects();

            // Load tables
            loadGradesTable();
            loadSubjectsTable();

            // Form submission
            const createClassRoomForm = document.getElementById('createClassRoomForm');
            const submitBtn = document.getElementById('submitBtn');

            createClassRoomForm.addEventListener('submit', function (e) {
                e.preventDefault();
                submitForm();
            });

            // Class type change event for default percentages
            const classTypeSelect = document.getElementById('class_type');
            const percentageInput = document.getElementById('teacher_percentage');

            classTypeSelect.addEventListener('change', function () {
                if (this.value === 'online') {
                    percentageInput.value = 80; // default online
                } else if (this.value === 'offline') {
                    percentageInput.value = 75; // default offline
                } else {
                    percentageInput.value = '';
                }
            });

            // Grade modal events
            const saveGradeBtn = document.getElementById('saveGradeBtn');
            saveGradeBtn.addEventListener('click', saveGrade);

            const updateGradeBtn = document.getElementById('updateGradeBtn');
            updateGradeBtn.addEventListener('click', updateGrade);

            // Subject modal events
            const saveSubjectBtn = document.getElementById('saveSubjectBtn');
            saveSubjectBtn.addEventListener('click', saveSubject);

            const updateSubjectBtn = document.getElementById('updateSubjectBtn');
            updateSubjectBtn.addEventListener('click', updateSubject);

            // Search functionality
            const gradeSearch = document.getElementById('gradeSearch');
            gradeSearch.addEventListener('input', debounce(function () {
                filterGradesTable();
            }, 300));

            const subjectSearch = document.getElementById('subjectSearch');
            subjectSearch.addEventListener('input', debounce(function () {
                filterSubjectsTable();
            }, 300));
        });

        // Dropdown Loading Functions
        function loadTeachers() {
            fetch("{{ url('/api/teachers/dropdown') }}")
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.status === 'success') {
                        const teacherSelect = document.getElementById('teacher_id');
                        data.data.forEach(teacher => {
                            const option = document.createElement('option');
                            option.value = teacher.id;
                            option.textContent = `${teacher.fname} ${teacher.lname}`;
                            teacherSelect.appendChild(option);
                        });
                    } else {
                        throw new Error(data.message || 'Failed to load teachers');
                    }
                })
                .catch(error => {
                    console.error('Error loading teachers:', error);
                    showAlert('Error loading teachers: ' + error.message, 'danger');
                });
        }

        function loadGrades() {
            fetch("{{ url('/api/grades/dropdown') }}")
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.status === 'success') {
                        const gradeSelect = document.getElementById('grade_id');
                        // Clear existing options except the first one
                        while (gradeSelect.options.length > 1) {
                            gradeSelect.remove(1);
                        }
                        data.data.forEach(grade => {
                            const option = document.createElement('option');
                            option.value = grade.id;
                            option.textContent = `Grade ${grade.grade_name}`;
                            gradeSelect.appendChild(option);
                        });
                    } else {
                        throw new Error(data.message || 'Failed to load grades');
                    }
                })
                .catch(error => {
                    console.error('Error loading grades:', error);
                    showAlert('Error loading grades: ' + error.message, 'danger');
                });
        }

        function loadSubjects() {
            fetch("{{ url('/api/subjects/dropdown') }}")
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.status === 'success') {
                        const subjectSelect = document.getElementById('subject_id');
                        // Clear existing options except the first one
                        while (subjectSelect.options.length > 1) {
                            subjectSelect.remove(1);
                        }
                        data.data.forEach(subject => {
                            const option = document.createElement('option');
                            option.value = subject.id;
                            option.textContent = subject.subject_name;
                            subjectSelect.appendChild(option);
                        });
                    } else {
                        throw new Error(data.message || 'Failed to load subjects');
                    }
                })
                .catch(error => {
                    console.error('Error loading subjects:', error);
                    showAlert('Error loading subjects: ' + error.message, 'danger');
                });
        }

        // Grades Table Functions
        function loadGradesTable() {
            showGradesLoading();

            fetch("{{ url('/api/grades') }}")
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    // Check different possible response structures
                    if (data.status === 'success' && data.data) {
                        allGrades = data.data;
                    } else if (Array.isArray(data)) {
                        // If API returns direct array
                        allGrades = data;
                    } else if (data.grades) {
                        // If API returns {grades: [...]}
                        allGrades = data.grades;
                    } else {
                        throw new Error('Invalid response format from grades API');
                    }

                    renderGradesTable(allGrades);
                    hideGradesLoading();
                })
                .catch(error => {
                    console.error('Error loading grades:', error);
                    console.log('Full error details:', error);
                    showAlert('Error loading grades. Please check console for details.', 'danger');
                    hideGradesLoading();

                    // Show empty state
                    const emptyState = document.getElementById('gradesEmpty');
                    if (emptyState) emptyState.classList.remove('d-none');
                });
        }

        function renderGradesTable(grades) {
            const tbody = document.getElementById('gradesTableBody');
            const emptyState = document.getElementById('gradesEmpty');

            if (!tbody) return;

            tbody.innerHTML = '';

            if (grades.length === 0) {
                emptyState.classList.remove('d-none');
                return;
            }

            emptyState.classList.add('d-none');

            grades.forEach((grade, index) => {
                const row = `
                    <tr>
                        <td class="fw-bold text-muted">${index + 1}</td>
                        <td>Grade ${grade.grade_name}</td>
                        <td>${new Date(grade.created_at).toLocaleDateString()}</td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-warning" title="Edit" 
                                        onclick="showEditGradeModal(${grade.id}, '${escapeHtml(grade.grade_name)}')">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
                tbody.innerHTML += row;
            });
        }

        function filterGradesTable() {
            const searchTerm = document.getElementById('gradeSearch').value.toLowerCase();
            const filteredGrades = allGrades.filter(grade =>
                grade.grade_name.toLowerCase().includes(searchTerm)
            );
            renderGradesTable(filteredGrades);
        }

        function showGradesLoading() {
            document.getElementById('gradesLoading').classList.remove('d-none');
            document.getElementById('gradesTableBody').closest('.table-responsive').classList.add('d-none');
        }

        function hideGradesLoading() {
            document.getElementById('gradesLoading').classList.add('d-none');
            document.getElementById('gradesTableBody').closest('.table-responsive').classList.remove('d-none');
        }

        // Subjects Table Functions
        function loadSubjectsTable() {
            showSubjectsLoading();

            fetch("{{ url('/api/subjects') }}")
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    // Check different possible response structures
                    if (data.status === 'success' && data.data) {
                        allSubjects = data.data;
                    } else if (Array.isArray(data)) {
                        // If API returns direct array
                        allSubjects = data;
                    } else if (data.subjects) {
                        // If API returns {subjects: [...]}
                        allSubjects = data.subjects;
                    } else {
                        throw new Error('Invalid response format from subjects API');
                    }

                    renderSubjectsTable(allSubjects);
                    hideSubjectsLoading();
                })
                .catch(error => {
                    console.error('Error loading subjects:', error);
                    console.log('Full error details:', error);
                    showAlert('Error loading subjects. Please check console for details.', 'danger');
                    hideSubjectsLoading();

                    // Show empty state
                    const emptyState = document.getElementById('subjectsEmpty');
                    if (emptyState) emptyState.classList.remove('d-none');
                });
        }

        function renderSubjectsTable(subjects) {
            const tbody = document.getElementById('subjectsTableBody');
            const emptyState = document.getElementById('subjectsEmpty');

            if (!tbody) return;

            tbody.innerHTML = '';

            if (subjects.length === 0) {
                emptyState.classList.remove('d-none');
                return;
            }

            emptyState.classList.add('d-none');

            subjects.forEach((subject, index) => {
                const row = `
                    <tr>
                        <td class="fw-bold text-muted">${index + 1}</td>
                        <td>${subject.subject_name}</td>
                        <td>${new Date(subject.created_at).toLocaleDateString()}</td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-warning" title="Edit" 
                                        onclick="showEditSubjectModal(${subject.id}, '${escapeHtml(subject.subject_name)}')">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
                tbody.innerHTML += row;
            });
        }

        function filterSubjectsTable() {
            const searchTerm = document.getElementById('subjectSearch').value.toLowerCase();
            const filteredSubjects = allSubjects.filter(subject =>
                subject.subject_name.toLowerCase().includes(searchTerm)
            );
            renderSubjectsTable(filteredSubjects);
        }

        function showSubjectsLoading() {
            document.getElementById('subjectsLoading').classList.remove('d-none');
            document.getElementById('subjectsTableBody').closest('.table-responsive').classList.add('d-none');
        }

        function hideSubjectsLoading() {
            document.getElementById('subjectsLoading').classList.add('d-none');
            document.getElementById('subjectsTableBody').closest('.table-responsive').classList.remove('d-none');
        }

        // Edit Modal Functions
        function showEditGradeModal(gradeId, gradeName) {
            document.getElementById('edit_grade_id').value = gradeId;
            document.getElementById('edit_grade_name').value = gradeName;

            const modal = new bootstrap.Modal(document.getElementById('editGradeModal'));
            modal.show();
        }

        function showEditSubjectModal(subjectId, subjectName) {
            document.getElementById('edit_subject_id').value = subjectId;
            document.getElementById('edit_subject_name').value = subjectName;

            const modal = new bootstrap.Modal(document.getElementById('editSubjectModal'));
            modal.show();
        }

        function updateGrade() {
            const gradeId = document.getElementById('edit_grade_id').value;
            const gradeName = document.getElementById('edit_grade_name').value.trim();
            const updateGradeBtn = document.getElementById('updateGradeBtn');
            const originalText = updateGradeBtn.innerHTML;

            if (!gradeName) {
                showAlert('Please enter grade name', 'warning');
                return;
            }

            // Show loading state
            updateGradeBtn.disabled = true;
            updateGradeBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Updating...';

            fetch(`/api/grades/${gradeId}`, {
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ grade_name: gradeName })
            })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => { throw err; });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.status === 'success') {
                        // Close modal
                        const modal = bootstrap.Modal.getInstance(document.getElementById('editGradeModal'));
                        modal.hide();

                        // Reload grades table and dropdown
                        loadGradesTable();
                        loadGrades();

                        showAlert('Grade updated successfully!', 'success');
                    } else {
                        throw new Error(data.message || 'Failed to update grade');
                    }
                })
                .catch(error => {
                    console.error('Error updating grade:', error);
                    showAlert('Error updating grade: ' + error.message, 'danger');
                })
                .finally(() => {
                    // Restore button state
                    updateGradeBtn.disabled = false;
                    updateGradeBtn.innerHTML = originalText;
                });
        }

        function updateSubject() {
            const subjectId = document.getElementById('edit_subject_id').value;
            const subjectName = document.getElementById('edit_subject_name').value.trim();
            const updateSubjectBtn = document.getElementById('updateSubjectBtn');
            const originalText = updateSubjectBtn.innerHTML;

            if (!subjectName) {
                showAlert('Please enter subject name', 'warning');
                return;
            }

            // Show loading state
            updateSubjectBtn.disabled = true;
            updateSubjectBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Updating...';

            fetch(`/api/subjects/${subjectId}`, {
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ subject_name: subjectName })
            })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => { throw err; });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.status === 'success') {
                        // Close modal
                        const modal = bootstrap.Modal.getInstance(document.getElementById('editSubjectModal'));
                        modal.hide();

                        // Reload subjects table and dropdown
                        loadSubjectsTable();
                        loadSubjects();

                        showAlert('Subject updated successfully!', 'success');
                    } else {
                        throw new Error(data.message || 'Failed to update subject');
                    }
                })
                .catch(error => {
                    console.error('Error updating subject:', error);
                    showAlert('Error updating subject: ' + error.message, 'danger');
                })
                .finally(() => {
                    // Restore button state
                    updateSubjectBtn.disabled = false;
                    updateSubjectBtn.innerHTML = originalText;
                });
        }

        // Add New Grade/Subject Functions
        function saveGrade() {
            const gradeName = document.getElementById('grade_name').value.trim();
            const saveGradeBtn = document.getElementById('saveGradeBtn');
            const originalText = saveGradeBtn.innerHTML;

            if (!gradeName) {
                showAlert('Please enter grade name', 'warning');
                return;
            }

            // Show loading state
            saveGradeBtn.disabled = true;
            saveGradeBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Saving...';

            fetch("{{ url('/api/grades') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ grade_name: gradeName })
            })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => { throw err; });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.status === 'success') {
                        // Close modal
                        const modal = bootstrap.Modal.getInstance(document.getElementById('addGradeModal'));
                        modal.hide();

                        // Clear form
                        document.getElementById('grade_name').value = '';

                        // Reload grades dropdown and table
                        loadGrades();
                        loadGradesTable();

                        // Select the newly created grade
                        setTimeout(() => {
                            const gradeSelect = document.getElementById('grade_id');
                            gradeSelect.value = data.data.id;
                        }, 500);

                        showAlert('Grade created successfully!', 'success');
                    } else {
                        throw new Error(data.message || 'Failed to create grade');
                    }
                })
                .catch(error => {
                    console.error('Error creating grade:', error);
                    showAlert('Error creating grade: ' + error.message, 'danger');
                })
                .finally(() => {
                    // Restore button state
                    saveGradeBtn.disabled = false;
                    saveGradeBtn.innerHTML = originalText;
                });
        }

        function saveSubject() {
            const subjectName = document.getElementById('subject_name').value.trim();
            const saveSubjectBtn = document.getElementById('saveSubjectBtn');
            const originalText = saveSubjectBtn.innerHTML;

            if (!subjectName) {
                showAlert('Please enter subject name', 'warning');
                return;
            }

            // Show loading state
            saveSubjectBtn.disabled = true;
            saveSubjectBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Saving...';

            fetch("{{ url('/api/subjects') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ subject_name: subjectName })
            })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => { throw err; });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.status === 'success') {
                        // Close modal
                        const modal = bootstrap.Modal.getInstance(document.getElementById('addSubjectModal'));
                        modal.hide();

                        // Clear form
                        document.getElementById('subject_name').value = '';

                        // Reload subjects dropdown and table
                        loadSubjects();
                        loadSubjectsTable();

                        // Select the newly created subject
                        setTimeout(() => {
                            const subjectSelect = document.getElementById('subject_id');
                            subjectSelect.value = data.data.id;
                        }, 500);

                        showAlert('Subject created successfully!', 'success');
                    } else {
                        throw new Error(data.message || 'Failed to create subject');
                    }
                })
                .catch(error => {
                    console.error('Error creating subject:', error);
                    showAlert('Error creating subject: ' + error.message, 'danger');
                })
                .finally(() => {
                    // Restore button state
                    saveSubjectBtn.disabled = false;
                    saveSubjectBtn.innerHTML = originalText;
                });
        }

        // Class Room Form Submission - UPDATED to include medium
        function submitForm() {
            const submitBtn = document.getElementById('submitBtn');
            const originalText = submitBtn.innerHTML;

            // Validate medium field
            const medium = document.getElementById('medium').value;
            if (!medium) {
                showAlert('Please select a medium', 'warning');
                document.getElementById('medium').classList.add('is-invalid');
                return;
            }

            // Show loading state
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Creating...';

            // Get form data
            const formData = new FormData(document.getElementById('createClassRoomForm'));

            // Add all form values including medium and teacher_percentage
            const data = {
                class_name: formData.get('class_name'),
                class_type: formData.get('class_type'),
                medium: formData.get('medium'),  // Added medium field
                teacher_id: formData.get('teacher_id'),
                subject_id: formData.get('subject_id'),
                grade_id: formData.get('grade_id'),
                teacher_percentage: formData.get('teacher_percentage'),
                is_active: 1,  // Automatically set to 1
                is_ongoing: 0  // Automatically set to 0
            };

            console.log('Submitting class room data:', data); // Debug log

            fetch("{{ url('/api/class-rooms') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => { throw err; });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.status === 'success') {
                        showAlert('Class room created successfully!', 'success');
                        // Redirect to class rooms list after 2 seconds
                        setTimeout(() => {
                            window.location.href = "{{ route('class_rooms.index') }}";
                        }, 2000);
                    } else {
                        throw new Error(data.message || 'Failed to create class room');
                    }
                })
                .catch(error => {
                    console.error('Error creating class room:', error);

                    if (error.errors) {
                        // Display validation errors
                        displayValidationErrors(error.errors);
                    } else {
                        showAlert('Error creating class room: ' + error.message, 'danger');
                    }

                    // Restore button state
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                });
        }

        function displayValidationErrors(errors) {
            // Clear previous errors
            document.querySelectorAll('.invalid-feedback').forEach(el => {
                el.style.display = 'none';
            });
            document.querySelectorAll('.is-invalid').forEach(el => {
                el.classList.remove('is-invalid');
            });

            // Display new errors
            for (const field in errors) {
                // Handle nested field names
                const fieldName = field.replace(/\[/g, '_').replace(/\]/g, '');
                const errorElement = document.getElementById(fieldName + '_error');
                const inputElement = document.getElementById(fieldName);

                if (errorElement && inputElement) {
                    errorElement.textContent = errors[field][0];
                    errorElement.style.display = 'block';
                    inputElement.classList.add('is-invalid');
                } else {
                    // Fallback for field names that might not match exactly
                    console.warn(`Error element not found for field: ${field}`);
                }
            }
        }

        // Helper Functions
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
            alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed top-0 end-0 m-3`;
            alertDiv.style.zIndex = '9999';
            alertDiv.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;

            document.body.appendChild(alertDiv);

            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.remove();
                }
            }, 5000);
        }
    </script>
@endpush