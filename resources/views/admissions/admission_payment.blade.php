@extends('layouts.app')

@section('title', 'Admission Payments')
@section('page-title', 'Admission Payments Management')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Admission Payments</li>
@endsection

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-money-bill-wave me-2"></i>
                        Admission Payments Management
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Chart Section at the Top -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-gradient-primary text-white">
                                    <h5 class="mb-0">
                                        <i class="fas fa-chart-line me-2"></i>
                                        Admission Payments Chart
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-3">
                                        <div class="col-md-3">
                                            <label class="form-label">Select Year</label>
                                            <select class="form-select" id="chartYear">
                                                @php
                                                    $currentYear = date('Y');
                                                    $years = range($currentYear, $currentYear - 5);
                                                @endphp
                                                @foreach($years as $year)
                                                    <option value="{{ $year }}" {{ $year == $currentYear ? 'selected' : '' }}>
                                                        {{ $year }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Select Month</label>
                                            <select class="form-select" id="chartMonth">
                                                @php
                                                    $months = [
                                                        1 => 'January',
                                                        2 => 'February',
                                                        3 => 'March',
                                                        4 => 'April',
                                                        5 => 'May',
                                                        6 => 'June',
                                                        7 => 'July',
                                                        8 => 'August',
                                                        9 => 'September',
                                                        10 => 'October',
                                                        11 => 'November',
                                                        12 => 'December'
                                                    ];
                                                    $currentMonth = date('n');
                                                @endphp
                                                @foreach($months as $num => $name)
                                                    <option value="{{ $num }}" {{ $num == $currentMonth ? 'selected' : '' }}>
                                                        {{ $name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-2 align-self-end">
                                            <button class="btn btn-primary w-100" onclick="loadChartData()">
                                                <i class="fas fa-sync me-2"></i>Load Chart
                                            </button>
                                        </div>
                                        <div class="col-md-4 align-self-end text-end">
                                            <div class="alert alert-info mb-0 py-2">
                                                <i class="fas fa-info-circle me-2"></i>
                                                Total: <span class="fw-bold" id="chartTotal">Rs. 0.00</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="chart-container">
                                        <canvas id="admissionsChart" height="100"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filters Section -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <label class="form-label">Filter by Date</label>
                            <input type="date" class="form-control" id="filterDate">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Admission Status</label>
                            <select class="form-select" id="admissionStatusFilter">
                                <option value="all">All Students</option>
                                <option value="paid">Paid Only</option>
                                <option value="not_paid">Not Paid Only</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <button class="btn btn-primary w-100" onclick="applyFilters()">
                                <i class="fas fa-filter me-2"></i>Apply Filters
                            </button>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <button class="btn btn-outline-secondary w-100" onclick="clearFilters()">
                                <i class="fas fa-times me-2"></i>Clear
                            </button>
                        </div>
                        <div class="col-md-2 text-end">
                            <label class="form-label">&nbsp;</label>
                            <div>
                                <button class="btn btn-success" onclick="loadAllStudents()">
                                    <i class="fas fa-sync me-2"></i>Refresh
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Date Range Filter -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <label class="form-label">Date Range From</label>
                            <input type="date" class="form-control" id="dateRangeFrom">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Date Range To</label>
                            <input type="date" class="form-control" id="dateRangeTo">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <button class="btn btn-outline-primary w-100" onclick="applyDateRangeFilter()">
                                <i class="fas fa-calendar-alt me-2"></i>Apply Range
                            </button>
                        </div>
                        <div class="col-md-4 text-end">
                            <div class="alert alert-warning mb-0 py-2">
                                <i class="fas fa-chart-bar me-2"></i>
                                <span id="statsSummary">Paid: 0 | Not Paid: 0 | Total: 0</span>
                            </div>
                        </div>
                    </div>

                    <!-- Bulk Actions Section -->
                    <div class="row mb-4" id="bulkActionsSection" style="display: none;">
                        <div class="col-12">
                            <div class="card border-warning">
                                <div class="card-header bg-warning text-dark">
                                    <h6 class="mb-0">
                                        <i class="fas fa-bulk me-2"></i>
                                        Bulk Admission Payments
                                        <span class="badge bg-dark ms-2" id="selectedCount">0 students selected</span>
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label class="form-label">Admission Type</label>
                                            <select class="form-select" id="admissionType">
                                                <option value="">Select Admission Type</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Amount</label>
                                            <input type="text" class="form-control" id="paymentAmount" readonly>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">&nbsp;</label>
                                            <button class="btn btn-success w-100" id="processBulkPaymentsBtn"
                                                onclick="processBulkPayments()" disabled>
                                                <i class="fas fa-money-bill me-2"></i>
                                                Process Payments
                                            </button>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-12">
                                            <div class="d-flex gap-2">
                                                <button class="btn btn-outline-primary btn-sm"
                                                    onclick="selectAllOnCurrentPage()">
                                                    <i class="fas fa-check-square me-1"></i>Select Page
                                                </button>
                                                <button class="btn btn-outline-primary btn-sm"
                                                    onclick="selectAllStudents()">
                                                    <i class="fas fa-check-double me-1"></i>Select All
                                                </button>
                                                <button class="btn btn-outline-secondary btn-sm"
                                                    onclick="deselectAllStudents()">
                                                    <i class="fas fa-times-circle me-1"></i>Deselect All
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Loading Spinner -->
                    <div id="loadingSpinner" class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Loading students...</p>
                    </div>

                    <!-- Students Table -->
                    <div id="studentsTableSection" style="display: none;">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <span class="text-muted" id="tableInfo">Showing 0 students</span>
                                <span class="badge bg-secondary ms-2" id="dateRangeInfo"></span>
                            </div>
                            <div class="d-flex align-items-center">
                                <span class="me-2 text-muted">Show:</span>
                                <select class="form-select form-select-sm" id="pageSize" style="width: auto;"
                                    onchange="changePageSize()">
                                    <option value="10">10</option>
                                    <option value="25" selected>25</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-hover table-striped">
                                <thead class="table-dark">
                                    <tr>
                                        <th width="50">
                                            <input type="checkbox" id="selectAllCheckbox" onchange="toggleSelectAll(this)">
                                        </th>
                                        <th>Student ID</th>
                                        <th>Name</th>
                                        <th>Grade</th>
                                        <th>Mobile</th>
                                        <th>Register Date</th> <!-- Changed from Created Date -->
                                        <th>Admission Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="studentsTableBody">
                                    <!-- Students will be populated here -->
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <nav aria-label="Student pagination" id="paginationSection">
                            <ul class="pagination justify-content-center" id="paginationContainer">
                                <!-- Pagination will be generated here -->
                            </ul>
                        </nav>
                    </div>

                    <!-- No Students Message -->
                    <div id="noStudentsMessage" class="text-center py-5" style="display: none;">
                        <i class="fas fa-users fa-3x text-muted mb-3"></i>
                        <h4 class="text-muted">No Students Found</h4>
                        <p class="text-muted">No students match your filter criteria.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- View Admissions Modal -->
    <div class="modal fade" id="viewAdmissionsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-receipt me-2"></i>
                        Student Admission Payments
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Student ID:</strong> <span id="modalStudentId" class="badge bg-primary"></span>
                        </div>
                        <div class="col-md-6">
                            <strong>Name:</strong> <span id="modalStudentName" class="fw-bold"></span>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Grade:</strong> <span id="modalStudentGrade" class="text-muted"></span>
                        </div>
                        <div class="col-md-6">
                            <strong>Mobile:</strong> <span id="modalStudentMobile" class="text-muted"></span>
                        </div>
                    </div>

                    <div id="admissionsLoading" class="text-center py-3">
                        <div class="spinner-border spinner-border-sm" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Loading admission payments...</p>
                    </div>

                    <div id="admissionsContent" style="display: none;">
                        <h6>Admission Payment History</h6>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Admission Type</th>
                                        <th>Amount</th>
                                        <th>Payment Date</th>
                                        <th>Time</th>
                                    </tr>
                                </thead>
                                <tbody id="admissionsTableBody">
                                    <!-- Admissions will be populated here -->
                                </tbody>
                            </table>
                        </div>
                        <div id="noAdmissionsMessage" class="text-center py-3" style="display: none;">
                            <p class="text-muted">No admission payments found for this student.</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .student-row {
            cursor: pointer;
        }

        .student-row:hover {
            background-color: #f8f9fa;
        }

        .payment-badge {
            font-size: 0.8rem;
        }

        .pagination .page-link {
            color: #0d6efd;
        }

        .pagination .page-item.active .page-link {
            background-color: #0d6efd;
            border-color: #0d6efd;
        }

        .admission-paid {
            background-color: #d1edff;
        }

        .admission-not-paid {
            background-color: #fff3cd;
        }

        .sl-date {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .sl-time {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .chart-container {
            position: relative;
            height: 300px;
            width: 100%;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        let allStudents = [];
        let filteredStudents = [];
        let selectedStudents = [];
        let admissionTypes = [];
        let currentModalStudentId = null;
        let admissionsChart = null;

        // Pagination variables
        let currentPage = 1;
        let pageSize = 25;
        let totalPages = 1;

        // Date range variables
        let dateRangeFrom = null;
        let dateRangeTo = null;

        // Initialize when page loads
        document.addEventListener('DOMContentLoaded', function () {
            loadAllStudents();
            loadAdmissionTypes();
            initializeChart();

            // Set default date range to current month
            const today = new Date();
            const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
            const lastDay = new Date(today.getFullYear(), today.getMonth() + 1, 0);

            document.getElementById('dateRangeFrom').valueAsDate = firstDay;
            document.getElementById('dateRangeTo').valueAsDate = lastDay;

            dateRangeFrom = firstDay.toISOString().split('T')[0];
            dateRangeTo = lastDay.toISOString().split('T')[0];

            // Load chart data
            setTimeout(() => {
                loadChartData();
            }, 500);
        });

        // ================= CHART FUNCTIONS =================
        function initializeChart() {
            const ctx = document.getElementById('admissionsChart');
            if (!ctx) {
                console.error('Chart canvas not found');
                return;
            }

            // Create empty chart initially
            admissionsChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Daily Collections',
                        data: [],
                        borderColor: '#0d6efd',
                        backgroundColor: 'rgba(13, 110, 253, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#0d6efd',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 5
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                            labels: {
                                font: {
                                    size: 14
                                }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function (context) {
                                    return 'Rs. ' + context.parsed.y.toLocaleString('en-LK');
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function (value) {
                                    return 'Rs. ' + value.toLocaleString('en-LK');
                                },
                                font: {
                                    size: 12
                                }
                            },
                            title: {
                                display: true,
                                text: 'Amount (LKR)',
                                font: {
                                    size: 14,
                                    weight: 'bold'
                                }
                            }
                        },
                        x: {
                            ticks: {
                                font: {
                                    size: 12
                                }
                            },
                            title: {
                                display: true,
                                text: 'Date',
                                font: {
                                    size: 14,
                                    weight: 'bold'
                                }
                            }
                        }
                    }
                }
            });
        }

        async function loadChartData() {
            const year = document.getElementById('chartYear').value;
            const month = document.getElementById('chartMonth').value;

            try {
                // Show loading state
                document.getElementById('chartTotal').innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';

                const response = await fetch(`/api/payment-admissions/chart/${year}/${month}`);
                if (!response.ok) throw new Error('Failed to fetch chart data');

                const result = await response.json();
                console.log('Chart API Response:', result); // Debug log

                if (result.status === true && result.data) {
                    updateChart(result.data);
                } else {
                    throw new Error(result.message || 'Invalid chart data');
                }
            } catch (error) {
                console.error('Error loading chart data:', error);
                showAlert('Failed to load chart data: ' + error.message, 'danger');
                // Reset chart total display
                document.getElementById('chartTotal').textContent = 'Rs. 0.00';
            }
        }

        function updateChart(chartData) {
            console.log('Chart data for update:', chartData); // Debug log

            if (!admissionsChart) {
                console.error('Chart not initialized');
                return;
            }

            // Update total amount
            if (chartData.summary && chartData.summary.total_amount !== undefined) {
                const totalAmount = parseFloat(chartData.summary.total_amount);
                document.getElementById('chartTotal').textContent =
                    'Rs. ' + totalAmount.toLocaleString('en-LK', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
            } else if (chartData.total_amount !== undefined) {
                const totalAmount = parseFloat(chartData.total_amount);
                document.getElementById('chartTotal').textContent =
                    'Rs. ' + totalAmount.toLocaleString('en-LK', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
            }

            // Prepare labels and data for chart
            let labels = [];
            let data = [];

            // Check different possible data formats
            if (chartData.chart_data && chartData.chart_data.labels && chartData.chart_data.datasets) {
                // Format 1: chart_data structure
                labels = chartData.chart_data.labels;
                data = chartData.chart_data.datasets[0].data;
            } else if (chartData.daily_collections && Array.isArray(chartData.daily_collections)) {
                // Format 2: daily_collections structure
                chartData.daily_collections.forEach(item => {
                    if (item.date && item.amount !== undefined) {
                        const date = new Date(item.date);
                        labels.push(date.getDate() + ' ' + date.toLocaleDateString('en-US', { month: 'short' }));
                        data.push(parseFloat(item.amount));
                    }
                });
            } else if (chartData.payments && Array.isArray(chartData.payments)) {
                // Format 3: Group payments by date
                const dailyMap = {};
                chartData.payments.forEach(payment => {
                    if (payment.created_at) {
                        const date = new Date(payment.created_at);
                        const dateKey = date.toISOString().split('T')[0];
                        const amount = parseFloat(payment.amount) || 0;

                        if (!dailyMap[dateKey]) {
                            dailyMap[dateKey] = 0;
                        }
                        dailyMap[dateKey] += amount;
                    }
                });

                // Convert to arrays
                Object.keys(dailyMap).sort().forEach(dateKey => {
                    const date = new Date(dateKey);
                    labels.push(date.getDate() + ' ' + date.toLocaleDateString('en-US', { month: 'short' }));
                    data.push(dailyMap[dateKey]);
                });
            }

            console.log('Final chart labels:', labels);
            console.log('Final chart data:', data);

            // Update chart
            admissionsChart.data.labels = labels;
            admissionsChart.data.datasets[0].data = data;
            admissionsChart.update();

            // If no data, show message
            if (data.length === 0) {
                console.warn('No chart data available');
            }
        }

        // ================= STUDENT FUNCTIONS =================
        async function loadAllStudents() {
            try {
                showLoadingState();

                const response = await fetch('/api/students/active');
                if (!response.ok) throw new Error('Failed to fetch students');

                const result = await response.json();

                if (result.status === 'success' && result.data) {
                    // Convert student data with proper boolean handling
                    allStudents = convertStudentData(result.data.data || result.data);

                    // Ensure all students have proper created_at
                    allStudents = allStudents.map(student => ({
                        ...student,
                        created_at: student.created_at || student.created_date || new Date().toISOString()
                    }));

                    applyFilters();
                } else {
                    throw new Error(result.message || 'No students found');
                }
            } catch (error) {
                console.error('Error loading students:', error);
                showErrorState('Failed to load students: ' + error.message);
            }
        }

        // ================= UTILITY FUNCTIONS FOR SRI LANKAN FORMAT =================
        // Format date to Sri Lankan format (DD-MM-YYYY)
        function formatDateToSriLankan(dateString) {
            if (!dateString) return 'N/A';

            try {
                const date = new Date(dateString);
                if (isNaN(date.getTime())) return dateString;

                const day = date.getDate().toString().padStart(2, '0');
                const month = (date.getMonth() + 1).toString().padStart(2, '0');
                const year = date.getFullYear();

                return `${day}-${month}-${year}`;
            } catch (error) {
                console.error('Error formatting date:', error, dateString);
                return dateString;
            }
        }

        // Format time to Sri Lankan 12-hour format
        function formatTimeToSriLankan(dateString) {
            if (!dateString) return '';

            try {
                const date = new Date(dateString);
                if (isNaN(date.getTime())) return '';

                let hours = date.getHours();
                let minutes = date.getMinutes().toString().padStart(2, '0');
                const ampm = hours >= 12 ? 'PM' : 'AM';
                hours = hours % 12;
                hours = hours ? hours : 12; // Convert 0 to 12

                return `${hours}:${minutes} ${ampm}`;
            } catch (error) {
                console.error('Error formatting time:', error);
                return '';
            }
        }

        // Convert student data with proper boolean handling for admission status
        function convertStudentData(students) {
            return students.map(student => ({
                ...student,
                id: parseInt(student.id) || student.id,
                // 🔥 CRITICAL FIX: Handle boolean admission status correctly
                admission: getBooleanValue(student.admission),
                custom_id: student.custom_id || student.student_id || 'N/A',
                lname: student.initial_name || student.last_name || '',
                mobile: student.mobile || student.phone || student.telephone || 'N/A',
                created_at: student.created_at || student.created_date || new Date().toISOString(),
                grade: student.grade || student.grade_info || { grade_name: 'N/A' }
            }));
        }

        // 🔥 FUNCTION TO CONVERT ANY VALUE TO BOOLEAN
        function getBooleanValue(value) {
            if (typeof value === 'boolean') return value;
            if (typeof value === 'number') return value === 1;
            if (typeof value === 'string') {
                if (value.toLowerCase() === 'true') return true;
                if (value.toLowerCase() === 'false') return false;
                return value === '1';
            }
            return false;
        }

        // ================= FILTER FUNCTIONS =================
        function applyFilters() {
            const filterDate = document.getElementById('filterDate').value;
            const admissionStatus = document.getElementById('admissionStatusFilter').value;

            let filtered = [...allStudents];

            // Apply date filter (single date)
            if (filterDate) {
                filtered = filtered.filter(student => {
                    const studentDate = new Date(student.created_at).toISOString().split('T')[0];
                    return studentDate === filterDate;
                });
            }

            // Apply date range filter
            if (dateRangeFrom && dateRangeTo) {
                filtered = filtered.filter(student => {
                    const studentDate = new Date(student.created_at).toISOString().split('T')[0];
                    return studentDate >= dateRangeFrom && studentDate <= dateRangeTo;
                });

                // Update date range info badge
                const fromFormatted = formatDateToSriLankan(dateRangeFrom);
                const toFormatted = formatDateToSriLankan(dateRangeTo);
                document.getElementById('dateRangeInfo').textContent = `${fromFormatted} to ${toFormatted}`;
            } else {
                document.getElementById('dateRangeInfo').textContent = '';
            }

            // Apply admission status filter - USING BOOLEAN COMPARISON
            if (admissionStatus === 'paid') {
                filtered = filtered.filter(student => {
                    return student.admission === true;
                });
            } else if (admissionStatus === 'not_paid') {
                filtered = filtered.filter(student => {
                    return student.admission === false;
                });
            }

            filteredStudents = filtered;
            currentPage = 1;
            displayStudents();
            showContentState();
            updateStatsSummary();

            if (filteredStudents.length === 0) {
                showNoStudentsMessage();
            }
        }

        // Update statistics summary
        function updateStatsSummary() {
            if (!filteredStudents || filteredStudents.length === 0) {
                document.getElementById('statsSummary').textContent = 'Paid: 0 | Not Paid: 0 | Total: 0';
                return;
            }

            const paidCount = filteredStudents.filter(student => student.admission === true).length;
            const notPaidCount = filteredStudents.filter(student => student.admission === false).length;
            const totalCount = filteredStudents.length;

            document.getElementById('statsSummary').textContent =
                `Paid: ${paidCount} | Not Paid: ${notPaidCount} | Total: ${totalCount}`;
        }

        // Apply date range filter
        function applyDateRangeFilter() {
            dateRangeFrom = document.getElementById('dateRangeFrom').value;
            dateRangeTo = document.getElementById('dateRangeTo').value;

            if (dateRangeFrom && dateRangeTo && dateRangeFrom > dateRangeTo) {
                showAlert('Start date must be before end date', 'warning');
                return;
            }

            applyFilters();
        }

        // Clear all filters
        function clearFilters() {
            document.getElementById('filterDate').value = '';
            document.getElementById('admissionStatusFilter').value = 'all';
            document.getElementById('dateRangeFrom').valueAsDate = null;
            document.getElementById('dateRangeTo').valueAsDate = null;

            dateRangeFrom = null;
            dateRangeTo = null;

            filteredStudents = [...allStudents];
            currentPage = 1;
            displayStudents();
            showContentState();
            updateStatsSummary();
        }

        // ================= ADMISSION TYPES =================
        async function loadAdmissionTypes() {
            try {
                const response = await fetch('/api/admissions/dropdown');
                if (!response.ok) throw new Error('Failed to fetch admission types');

                const result = await response.json();

                let typesArray = [];

                if (Array.isArray(result)) {
                    typesArray = result;
                } else if (result.data && Array.isArray(result.data)) {
                    typesArray = result.data;
                }

                // Convert admission type amounts to numeric
                admissionTypes = typesArray.map(type => ({
                    ...type,
                    id: type.id,
                    amount: parseFloat(type.amount) || 0
                }));

                populateAdmissionTypesDropdown();
            } catch (error) {
                console.error('Error loading admission types:', error);
                showAlert('Failed to load admission types', 'danger');
            }
        }

        function populateAdmissionTypesDropdown() {
            const dropdown = document.getElementById('admissionType');
            let options = '<option value="">Select Admission Type</option>';

            admissionTypes.forEach(type => {
                options += `<option value="${type.id}" data-amount="${type.amount}">${type.name} - Rs. ${type.amount.toLocaleString('en-LK')}</option>`;
            });

            dropdown.innerHTML = options;

            dropdown.addEventListener('change', function () {
                const selectedOption = this.options[this.selectedIndex];
                const amount = selectedOption.getAttribute('data-amount');
                document.getElementById('paymentAmount').value = amount ? 'Rs. ' + parseFloat(amount).toLocaleString('en-LK') : '';
                updateProcessButton();
            });
        }

        // ================= DISPLAY STUDENTS =================
        function displayStudents() {
            const tableBody = document.getElementById('studentsTableBody');
            const tableInfo = document.getElementById('tableInfo');

            if (filteredStudents.length === 0) {
                showNoStudentsMessage();
                return;
            }

            // Calculate pagination
            totalPages = Math.ceil(filteredStudents.length / pageSize);
            const paginatedStudents = filteredStudents.slice(
                (currentPage - 1) * pageSize,
                currentPage * pageSize
            );

            // Update table info
            const startIndex = (currentPage - 1) * pageSize + 1;
            const endIndex = Math.min(startIndex + pageSize - 1, filteredStudents.length);
            tableInfo.textContent = `Showing ${startIndex}-${endIndex} of ${filteredStudents.length} students`;

            let html = '';

            paginatedStudents.forEach(student => {
                const isSelected = selectedStudents.includes(student.id);

                // Format date to Sri Lankan format - CHANGED FROM Created Date to Register Date
                const registerDate = formatDateToSriLankan(student.created_at);

                // 🔥 ADMISSION STATUS - USING BOOLEAN
                const admissionStatus = student.admission; // This is boolean true/false
                const statusText = admissionStatus === true ? 'Paid' : 'Not Paid';
                const statusBadge = admissionStatus === true ?
                    '<span class="badge bg-success payment-badge"><i class="fas fa-check me-1"></i>Paid</span>' :
                    '<span class="badge bg-danger payment-badge"><i class="fas fa-times me-1"></i>Not Paid</span>';

                const rowClass = admissionStatus === true ? 'admission-paid' : 'admission-not-paid';

                html += `
                        <tr class="student-row ${rowClass}">
                            <td>
                                <input type="checkbox" ${isSelected ? 'checked' : ''} 
                                       onchange="toggleStudentSelection(${student.id}, this)">
                            </td>
                            <td>${student.custom_id}</td>
                            <td>${student.lname}</td>
                            <td>${student.grade ? student.grade.grade_name : 'N/A'}</td>
                            <td>${student.mobile || 'N/A'}</td>
                            <td class="sl-date">${registerDate}</td> <!-- Changed to Register Date -->
                            <td>${statusBadge}</td>
                            <td>
                                <button class="btn btn-sm btn-outline-info" onclick="viewStudentAdmissions(${student.id}, '${student.custom_id}', '${student.lname}')">
                                    <i class="fas fa-eye me-1"></i>View Admissions
                                </button>
                            </td>
                        </tr>
                    `;
            });

            tableBody.innerHTML = html;
            generatePagination();
            updateBulkActions();
        }

        // ================= PAGINATION FUNCTIONS =================
        function generatePagination() {
            const paginationContainer = document.getElementById('paginationContainer');

            if (totalPages <= 1) {
                paginationContainer.innerHTML = '';
                return;
            }

            let paginationHtml = '';

            // Previous button
            paginationHtml += `
                    <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                        <a class="page-link" href="#" onclick="changePage(${currentPage - 1})" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>
                `;

            // Page numbers
            for (let i = 1; i <= totalPages; i++) {
                if (i === 1 || i === totalPages || (i >= currentPage - 2 && i <= currentPage + 2)) {
                    paginationHtml += `
                            <li class="page-item ${i === currentPage ? 'active' : ''}">
                                <a class="page-link" href="#" onclick="changePage(${i})">${i}</a>
                            </li>
                        `;
                } else if (i === currentPage - 3 || i === currentPage + 3) {
                    paginationHtml += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
                }
            }

            // Next button
            paginationHtml += `
                    <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                        <a class="page-link" href="#" onclick="changePage(${currentPage + 1})" aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>
                `;

            paginationContainer.innerHTML = paginationHtml;
        }

        function changePage(page) {
            if (page < 1 || page > totalPages) return;
            currentPage = page;
            displayStudents();
        }

        function changePageSize() {
            pageSize = parseInt(document.getElementById('pageSize').value);
            currentPage = 1;
            displayStudents();
        }

        // ================= VIEW STUDENT ADMISSIONS =================
        async function viewStudentAdmissions(studentId, customId, studentName) {
            currentModalStudentId = studentId;

            // Find the student to get additional details
            const student = allStudents.find(s => s.id == studentId);

            document.getElementById('modalStudentId').textContent = customId;
            document.getElementById('modalStudentName').textContent = studentName;
            document.getElementById('modalStudentGrade').textContent = student && student.grade ? student.grade.grade_name : 'N/A';
            document.getElementById('modalStudentMobile').textContent = student && student.mobile ? student.mobile : 'N/A';

            document.getElementById('admissionsLoading').style.display = 'block';
            document.getElementById('admissionsContent').style.display = 'none';
            document.getElementById('noAdmissionsMessage').style.display = 'none';

            try {
                const response = await fetch(`/api/payment-admissions/student?student_id=${studentId}`);
                if (!response.ok) throw new Error('Failed to fetch admissions');

                const result = await response.json();

                document.getElementById('admissionsLoading').style.display = 'none';
                document.getElementById('admissionsContent').style.display = 'block';

                if (result.status && result.data && result.data.length > 0) {
                    const admissionsBody = document.getElementById('admissionsTableBody');
                    let admissionsHtml = '';

                    result.data.forEach(payment => {
                        // Format dates to Sri Lankan format
                        const paymentDate = formatDateToSriLankan(payment.created_at);
                        const paymentTime = formatTimeToSriLankan(payment.created_at);

                        admissionsHtml += `
                                <tr>
                                    <td>${payment.admission_name}</td>
                                    <td>Rs. ${parseFloat(payment.amount).toLocaleString('en-LK')}</td>
                                    <td class="sl-date">${paymentDate}</td>
                                    <td class="sl-time">${paymentTime}</td>
                                </tr>
                            `;
                    });

                    admissionsBody.innerHTML = admissionsHtml;
                    document.getElementById('noAdmissionsMessage').style.display = 'none';
                } else {
                    document.getElementById('noAdmissionsMessage').style.display = 'block';
                    document.getElementById('admissionsTableBody').innerHTML = '';
                }
            } catch (error) {
                console.error('Error loading admissions:', error);
                document.getElementById('admissionsLoading').style.display = 'none';
                document.getElementById('admissionsContent').style.display = 'block';
                document.getElementById('noAdmissionsMessage').style.display = 'block';
                document.getElementById('noAdmissionsMessage').innerHTML = '<p class="text-danger">Failed to load admission payments</p>';
            }

            const modal = new bootstrap.Modal(document.getElementById('viewAdmissionsModal'));
            modal.show();
        }

        // ================= SELECTION FUNCTIONS =================
        function toggleSelectAll(checkbox) {
            const startIndex = (currentPage - 1) * pageSize;
            const endIndex = Math.min(startIndex + pageSize, filteredStudents.length);
            const currentPageStudents = filteredStudents.slice(startIndex, endIndex);

            if (checkbox.checked) {
                currentPageStudents.forEach(student => {
                    if (!selectedStudents.includes(student.id)) {
                        selectedStudents.push(student.id);
                    }
                });
            } else {
                currentPageStudents.forEach(student => {
                    selectedStudents = selectedStudents.filter(id => id !== student.id);
                });
            }
            displayStudents();
        }

        function toggleStudentSelection(studentId, checkbox) {
            if (checkbox.checked) {
                if (!selectedStudents.includes(studentId)) {
                    selectedStudents.push(studentId);
                }
            } else {
                selectedStudents = selectedStudents.filter(id => id !== studentId);
            }
            updateBulkActions();
        }

        function selectAllOnCurrentPage() {
            const startIndex = (currentPage - 1) * pageSize;
            const endIndex = Math.min(startIndex + pageSize, filteredStudents.length);
            const currentPageStudents = filteredStudents.slice(startIndex, endIndex);

            currentPageStudents.forEach(student => {
                if (!selectedStudents.includes(student.id)) {
                    selectedStudents.push(student.id);
                }
            });
            displayStudents();
        }

        function selectAllStudents() {
            selectedStudents = filteredStudents.map(student => student.id);
            displayStudents();
        }

        function deselectAllStudents() {
            selectedStudents = [];
            displayStudents();
        }

        function updateBulkActions() {
            const bulkSection = document.getElementById('bulkActionsSection');
            const selectedCount = document.getElementById('selectedCount');

            if (selectedStudents.length > 0) {
                bulkSection.style.display = 'block';
                selectedCount.textContent = `${selectedStudents.length} students selected`;
            } else {
                bulkSection.style.display = 'none';
            }

            updateProcessButton();
        }

        function updateProcessButton() {
            const button = document.getElementById('processBulkPaymentsBtn');
            const admissionType = document.getElementById('admissionType').value;

            button.disabled = !(selectedStudents.length > 0 && admissionType);
        }

        // ================= BULK PAYMENT PROCESSING =================
        async function processBulkPayments() {
            const admissionTypeId = document.getElementById('admissionType').value;
            const admissionType = admissionTypes.find(type => type.id == admissionTypeId);

            if (!admissionType) {
                showAlert('Please select a valid admission type', 'warning');
                return;
            }

            if (selectedStudents.length === 0) {
                showAlert('Please select at least one student', 'warning');
                return;
            }

            const amount = admissionType.amount;

            // Show confirmation dialog
            const confirmMessage = `Are you sure you want to process admission payments for ${selectedStudents.length} student(s)?\n\n` +
                `Admission Type: ${admissionType.name}\n` +
                `Amount per student: Rs. ${amount.toLocaleString('en-LK')}\n` +
                `Total Amount: Rs. ${(amount * selectedStudents.length).toLocaleString('en-LK')}`;

            if (!confirm(confirmMessage)) {
                return;
            }

            // Prepare payment data
            const payments = selectedStudents.map(studentId => ({
                student_id: studentId,
                admission_id: parseInt(admissionTypeId),
                amount: amount
            }));

            const paymentData = {
                payments: payments
            };

            try {
                // Disable button and show loading
                const button = document.getElementById('processBulkPaymentsBtn');
                button.disabled = true;
                button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';

                const response = await fetch('/api/payment-admissions/store-pay-admission/bulk', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(paymentData)
                });

                const result = await response.json();

                if (response.ok) {
                    showAlert(`Successfully processed ${selectedStudents.length} admission payments!`, 'success');

                    // Reset selections and reload data
                    selectedStudents = [];
                    document.getElementById('admissionType').value = '';
                    document.getElementById('paymentAmount').value = '';
                    await loadAllStudents();
                    await loadChartData(); // Reload chart data
                } else {
                    throw new Error(result.message || 'Payment processing failed');
                }
            } catch (error) {
                console.error('Error processing bulk payments:', error);
                showAlert('Failed to process payments: ' + error.message, 'danger');
            } finally {
                // Reset button
                const button = document.getElementById('processBulkPaymentsBtn');
                button.disabled = false;
                button.innerHTML = '<i class="fas fa-money-bill me-2"></i>Process Payments';
            }
        }

        // ================= UTILITY FUNCTIONS =================
        function showLoadingState() {
            document.getElementById('loadingSpinner').style.display = 'block';
            document.getElementById('studentsTableSection').style.display = 'none';
            document.getElementById('noStudentsMessage').style.display = 'none';
            document.getElementById('bulkActionsSection').style.display = 'none';
        }

        function showContentState() {
            document.getElementById('loadingSpinner').style.display = 'none';
            document.getElementById('studentsTableSection').style.display = 'block';
            document.getElementById('noStudentsMessage').style.display = 'none';
        }

        function showNoStudentsMessage() {
            document.getElementById('loadingSpinner').style.display = 'none';
            document.getElementById('studentsTableSection').style.display = 'none';
            document.getElementById('noStudentsMessage').style.display = 'block';
            document.getElementById('bulkActionsSection').style.display = 'none';
        }

        function showErrorState(message) {
            document.getElementById('loadingSpinner').style.display = 'none';
            document.getElementById('studentsTableSection').style.display = 'none';
            document.getElementById('noStudentsMessage').style.display = 'block';
            document.getElementById('noStudentsMessage').innerHTML = `
                    <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i>
                    <h4 class="text-danger">Error Loading Students</h4>
                    <p class="text-muted">${message}</p>
                    <button class="btn btn-primary mt-3" onclick="loadAllStudents()">
                        <i class="fas fa-redo me-2"></i>Try Again
                    </button>
                `;
        }

        function showAlert(message, type) {
            // Remove existing alerts
            document.querySelectorAll('.alert').forEach(alert => alert.remove());

            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
            alertDiv.innerHTML = `
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                `;

            document.querySelector('.card-body').insertBefore(alertDiv, document.querySelector('.card-body').firstChild);

            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.remove();
                }
            }, 5000);
        }
    </script>
@endpush