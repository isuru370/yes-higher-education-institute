@extends('layouts.app')

@section('title', 'Teacher Income Details')
@section('page-title', 'Teacher Income Details')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('teacher_payment.index') }}">Teacher Payments</a></li>
    <li class="breadcrumb-item active">Teacher Income Details</li>
@endsection

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="container-fluid">
        <!-- Current Month Display -->
        <div class="row mb-3">
            <div class="col-md-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body py-2">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3"
                                        style="width: 40px; height: 40px;">
                                        <i class="fas fa-calendar-alt"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-bold" id="selectedMonthYear">{{ date('F Y') }}</h6>
                                        <small class="text-muted">Showing data for current month</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 text-end">
                                <div class="badge bg-light text-dark border py-2 px-3 rounded">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Teacher ID: <span id="teacherIdDisplay">-</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Teacher Information Card -->
        <div class="row mb-3">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-bottom py-3">
                        <div class="d-flex align-items-center">
                            <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-2"
                                style="width: 32px; height: 32px;">
                                <i class="fas fa-user-graduate text-white" style="font-size: 0.9rem;"></i>
                            </div>
                            <h6 class="mb-0 fw-bold">Teacher Information</h6>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="text-muted small mb-1 d-block">Name</label>
                            <h5 class="fw-bold mb-0" id="teacherName">-</h5>
                        </div>
                        <div class="row">
                            <div class="col-6 mb-2">
                                <label class="text-muted small mb-1 d-block">ID</label>
                                <p class="fw-bold mb-0" id="teacherId">-</p>
                            </div>
                            <div class="col-6 mb-2">
                                <label class="text-muted small mb-1 d-block">Subject</label>
                                <p class="fw-bold mb-0" id="subjectName">-</p>
                            </div>
                        </div>
                        <div class="mt-3">
                            <label class="text-muted small mb-1 d-block">Salary Status</label>
                            <span class="badge bg-warning px-3 py-2 rounded" id="salaryStatus">-</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Financial Summary -->
            <div class="col-md-8">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-bottom py-3">
                        <div class="d-flex align-items-center">
                            <div class="bg-success rounded-circle d-flex align-items-center justify-content-center me-2"
                                style="width: 32px; height: 32px;">
                                <i class="fas fa-chart-bar text-white" style="font-size: 0.9rem;"></i>
                            </div>
                            <h6 class="mb-0 fw-bold">Financial Summary</h6>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Top Row - Total Collections & Advance -->
                        <div class="row mb-3">
                            <div class="col-md-6 mb-3">
                                <div class="card border-0 bg-light h-100">
                                    <div class="card-body p-3">
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-2"
                                                style="width: 28px; height: 28px;">
                                                <i class="fas fa-money-bill-wave text-white" style="font-size: 0.8rem;"></i>
                                            </div>
                                            <h6 class="mb-0 small text-muted">Total Collections</h6>
                                        </div>
                                        <h4 class="fw-bold text-primary mb-1" id="totalCollections">LKR 0.00</h4>
                                        <small class="text-muted">From student payments</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="card border-0 bg-light h-100">
                                    <div class="card-body p-3">
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="bg-warning rounded-circle d-flex align-items-center justify-content-center me-2"
                                                style="width: 28px; height: 28px;">
                                                <i class="fas fa-hand-holding-usd text-white"
                                                    style="font-size: 0.8rem;"></i>
                                            </div>
                                            <h6 class="mb-0 small text-muted">Advance Payments</h6>
                                        </div>
                                        <h4 class="fw-bold text-warning mb-1" id="advancePayments">LKR 0.00</h4>
                                        <small class="text-muted">Paid in advance</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Percentage Split Visualization -->
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <div class="card border-0 bg-light">
                                    <div class="card-body p-3">
                                        <h6 class="small text-muted mb-2">Percentage Split</h6>
                                        <div class="progress mb-2" style="height: 20px; border-radius: 10px;">
                                            <div class="progress-bar bg-primary" id="teacherPercentageBar"
                                                style="width: 0%; border-radius: 10px 0 0 10px;">
                                                <span class="small fw-bold" id="teacherPercentageTextBar">Teacher: 0%</span>
                                            </div>
                                            <div class="progress-bar bg-secondary" id="institutionPercentageBar"
                                                style="width: 0%; border-radius: 0 10px 10px 0;">
                                                <span class="small fw-bold" id="institutionPercentageTextBar">Institution:
                                                    0%</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-6 text-center">
                                                <small class="text-primary fw-bold" id="teacherPercentageText">0%</small>
                                            </div>
                                            <div class="col-6 text-center">
                                                <small class="text-secondary fw-bold"
                                                    id="institutionPercentageText">0%</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Shares Row -->
                        <div class="row mb-3">
                            <div class="col-md-6 mb-3">
                                <div class="card border-0 border-start border-primary border-4 h-100">
                                    <div class="card-body p-3">
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-2"
                                                style="width: 28px; height: 28px;">
                                                <i class="fas fa-user-tie text-white" style="font-size: 0.8rem;"></i>
                                            </div>
                                            <h6 class="mb-0 small text-muted">Teacher's Share</h6>
                                        </div>
                                        <h4 class="fw-bold text-primary mb-1" id="teacherShare">LKR 0.00</h4>
                                        <small class="text-muted" id="teacherSharePercentage">0% of total</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="card border-0 border-start border-secondary border-4 h-100">
                                    <div class="card-body p-3">
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center me-2"
                                                style="width: 28px; height: 28px;">
                                                <i class="fas fa-building text-white" style="font-size: 0.8rem;"></i>
                                            </div>
                                            <h6 class="mb-0 small text-muted">Institution's Share</h6>
                                        </div>
                                        <h4 class="fw-bold text-secondary mb-1" id="institutionShare">LKR 0.00</h4>
                                        <small class="text-muted" id="institutionSharePercentage">0% of total</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Net Payable Section -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card border-0 bg-warning bg-opacity-10">
                                    <div class="card-body p-3">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-1 text-muted">
                                                    <i class="fas fa-money-check-alt me-1"></i> Net Payable to Teacher
                                                </h6>
                                                <h2 class="fw-bold mb-1" id="netPayable">LKR 0.00</h2>
                                                <small class="text-muted">(Teacher's Share - Advance Payments)</small>
                                            </div>
                                            <div>
                                                <button class="btn btn-success px-4 py-2" id="payTeacherBtn" disabled
                                                    style="border-radius: 8px;">
                                                    <i class="fas fa-money-check-alt me-1"></i> Pay Teacher
                                                </button>
                                                <button class="btn btn-dark px-4 py-2 ms-2" id="printSlipBtn" disabled
                                                    style="border-radius: 8px;">
                                                    <i class="fas fa-print me-1"></i> Print Slip
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Classes Breakdown -->
        <div class="row mb-3">
            <div class="col-md-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-3">
                        <div class="d-flex align-items-center">
                            <div class="bg-info rounded-circle d-flex align-items-center justify-content-center me-2"
                                style="width: 32px; height: 32px;">
                                <i class="fas fa-chalkboard-teacher text-white" style="font-size: 0.9rem;"></i>
                            </div>
                            <h6 class="mb-0 fw-bold">Classes Breakdown</h6>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row" id="classesCards">
                            <!-- Classes will be populated here -->
                            <div class="col-12 text-center py-4">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading classes...</span>
                                </div>
                                <p class="text-muted mt-2">Loading classes data...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Payment Records -->
        <div class="row mb-3">
            <div class="col-md-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <div class="bg-dark rounded-circle d-flex align-items-center justify-content-center me-2"
                                    style="width: 32px; height: 32px;">
                                    <i class="fas fa-table text-white" style="font-size: 0.9rem;"></i>
                                </div>
                                <h6 class="mb-0 fw-bold">Detailed Payment Records</h6>
                            </div>
                            <div class="btn-group">
                                <button class="btn btn-sm btn-outline-primary px-3 py-1" id="exportTableExcelBtn">
                                    <i class="fas fa-file-excel me-1"></i> Excel
                                </button>
                                <button class="btn btn-sm btn-outline-danger px-3 py-1" id="exportTablePdfBtn">
                                    <i class="fas fa-file-pdf me-1"></i> PDF
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Loading Spinner -->
                        <div id="tableLoadingSpinner" class="text-center d-none">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2 small text-muted">Loading payment data...</p>
                        </div>

                        <!-- Table -->
                        <div class="table-responsive">
                            <table class="table table-hover table-borderless" id="paymentTable">
                                <thead class="bg-light" id="paymentTableHeader">
                                    <!-- Dynamic header will be populated here -->
                                </thead>
                                <tbody id="paymentTableBody">
                                    <!-- Data will be populated by JavaScript -->
                                </tbody>
                                <tfoot class="bg-light fw-bold" id="paymentTableFooter">
                                    <!-- Dynamic footer will be populated here -->
                                </tfoot>
                            </table>
                        </div>

                        <!-- Empty State -->
                        <div id="tableEmptyState" class="text-center d-none">
                            <div class="alert alert-light border py-5">
                                <i class="fas fa-info-circle fa-2x text-muted mb-3"></i>
                                <h6 class="mb-1 text-muted">No Payment Data</h6>
                                <p class="mb-0 small text-muted">No payment records found for the current month.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Advance Payment History -->
        <div class="row mb-3">
            <div class="col-md-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-3">
                        <div class="d-flex align-items-center">
                            <div class="bg-danger rounded-circle d-flex align-items-center justify-content-center me-2"
                                style="width: 32px; height: 32px;">
                                <i class="fas fa-history text-white" style="font-size: 0.9rem;"></i>
                            </div>
                            <h6 class="mb-0 fw-bold">Advance Payment History</h6>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover" id="advancePaymentsTable">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="small text-muted py-2">Date & Time</th>
                                        <th class="small text-muted py-2">Amount</th>
                                        <th class="small text-muted py-2">Reason Code</th>
                                        <th class="small text-muted py-2">Payment For</th>
                                        <th class="small text-muted py-2">Status</th>
                                        <th class="small text-muted py-2">Processed By</th>
                                    </tr>
                                </thead>
                                <tbody id="advancePaymentsTableBody">
                                    <!-- Data will be populated by JavaScript -->
                                </tbody>
                            </table>
                        </div>
                        <div id="advanceEmptyState" class="text-center d-none">
                            <div class="alert alert-light border py-5">
                                <i class="fas fa-info-circle fa-2x text-muted mb-3"></i>
                                <h6 class="mb-1 text-muted">No Advance Payments</h6>
                                <p class="mb-0 small text-muted">No advance payments found for this teacher.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        :root {
            --primary-color: #4e73df;
            --success-color: #1cc88a;
            --warning-color: #f6c23e;
            --danger-color: #e74a3b;
            --info-color: #36b9cc;
            --light-color: #f8f9fc;
            --dark-color: #5a5c69;
        }

        body {
            background-color: #f8f9fc;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .card {
            border-radius: 0.5rem;
            border: 1px solid #e3e6f0;
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15) !important;
        }

        .card-header {
            background-color: white;
            border-bottom: 1px solid #e3e6f0;
        }

        .table {
            font-size: 0.85rem;
            margin-bottom: 0;
        }

        .table th {
            font-weight: 600;
            color: #5a5c69;
            background-color: #f8f9fc;
            border-bottom: 2px solid #e3e6f0;
            padding: 0.75rem 1rem;
        }

        .table td {
            padding: 0.75rem 1rem;
            vertical-align: middle;
            border-bottom: 1px solid #e3e6f0;
        }

        .table tbody tr:hover {
            background-color: #f8f9fc;
        }

        .table-borderless td,
        .table-borderless th {
            border: none;
        }

        .btn {
            border-radius: 0.35rem;
            font-size: 0.85rem;
            font-weight: 600;
            padding: 0.375rem 1rem;
            transition: all 0.3s ease;
        }

        .btn-success {
            background-color: var(--success-color);
            border-color: var(--success-color);
        }

        .btn-dark {
            background-color: var(--dark-color);
            border-color: var(--dark-color);
        }

        .btn-success:hover {
            background-color: #17a673;
            border-color: #17a673;
            transform: translateY(-1px);
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.1);
        }

        .btn-dark:hover {
            background-color: #424549;
            border-color: #424549;
            transform: translateY(-1px);
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.1);
        }

        .btn-outline-primary,
        .btn-outline-danger {
            border-width: 1px;
        }

        .progress {
            border-radius: 10px;
            background-color: #e3e6f0;
        }

        .progress-bar {
            border-radius: 10px;
        }

        .badge {
            font-size: 0.75em;
            font-weight: 600;
            padding: 0.35em 0.65em;
            border-radius: 0.35rem;
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            color: #5a5c69;
        }

        .text-primary {
            color: var(--primary-color) !important;
        }

        .text-success {
            color: var(--success-color) !important;
        }

        .text-warning {
            color: var(--warning-color) !important;
        }

        .text-danger {
            color: var(--danger-color) !important;
        }

        .text-info {
            color: var(--info-color) !important;
        }

        .bg-primary {
            background-color: var(--primary-color) !important;
        }

        .bg-success {
            background-color: var(--success-color) !important;
        }

        .bg-warning {
            background-color: var(--warning-color) !important;
        }

        .bg-danger {
            background-color: var(--danger-color) !important;
        }

        .bg-info {
            background-color: var(--info-color) !important;
        }

        .spinner-border {
            width: 1.5rem;
            height: 1.5rem;
        }

        .alert-light {
            background-color: #f8f9fc;
            border-color: #e3e6f0;
        }

        /* Number formatting */
        .currency {
            font-family: 'SF Mono', 'Monaco', 'Inconsolata', 'Fira Code', monospace;
            letter-spacing: -0.5px;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .card-body {
                padding: 1rem;
            }

            .table {
                font-size: 0.8rem;
            }

            .btn {
                padding: 0.25rem 0.5rem;
                font-size: 0.8rem;
            }

            h4 {
                font-size: 1.25rem;
            }

            h6 {
                font-size: 0.9rem;
            }

            #payTeacherBtn,
            #printSlipBtn {
                padding: 0.375rem 0.75rem;
                font-size: 0.8rem;
            }
        }

        /* Print styles */
        @media print {
            .no-print {
                display: none !important;
            }

            .card {
                border: 1px solid #ddd !important;
                box-shadow: none !important;
            }

            .table {
                font-size: 0.8rem;
            }
        }
    </style>
@endpush

@push('scripts')
    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Font Awesome -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
    <!-- SheetJS for Excel export -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <!-- jsPDF for PDF export -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>

    <script>
        (function () {
            'use strict';

            // Configuration
            const CONFIG = {
                API_TIMEOUT: 30000,
                MAX_RETRIES: 2,
                RETRY_DELAY: 1000,
                AUTO_CLOSE_TIMEOUT: 15000,
                TOAST_DURATION: 3000,
                PRINT_WINDOW_DELAY: 1000,
                REFRESH_DELAY: 2000
            };

            // State management
            const state = {
                teacherData: null,
                allPayments: [],
                allGrades: [],
                currentFetchId: 0,
                abortController: null,
                isProcessingPayment: false
            };

            // DOM elements cache
            const elements = {
                teacherNameTitle: document.getElementById('teacherNameTitle'),
                selectedMonthYear: document.getElementById('selectedMonthYear'),
                summaryMonthYear: document.getElementById('summaryMonthYear'),
                teacherId: document.getElementById('teacherId'),
                teacherName: document.getElementById('teacherName'),
                subjectName: document.getElementById('subjectName'),
                salaryStatus: document.getElementById('salaryStatus'),
                totalCollections: document.getElementById('totalCollections'),
                advancePayments: document.getElementById('advancePayments'),
                teacherShare: document.getElementById('teacherShare'),
                institutionShare: document.getElementById('institutionShare'),
                netPayable: document.getElementById('netPayable'),
                teacherPercentageBar: document.getElementById('teacherPercentageBar'),
                institutionPercentageBar: document.getElementById('institutionPercentageBar'),
                teacherPercentageText: document.getElementById('teacherPercentageText'),
                institutionPercentageText: document.getElementById('institutionPercentageText'),
                teacherSharePercentage: document.getElementById('teacherSharePercentage'),
                institutionSharePercentage: document.getElementById('institutionSharePercentage'),
                teacherPercentageTextBar: document.getElementById('teacherPercentageTextBar'),
                institutionPercentageTextBar: document.getElementById('institutionPercentageTextBar'),
                classesCards: document.getElementById('classesCards'),
                paymentTableBody: document.getElementById('paymentTableBody'),
                paymentTableHeader: document.getElementById('paymentTableHeader'),
                paymentTableFooter: document.getElementById('paymentTableFooter'),
                tableLoadingSpinner: document.getElementById('tableLoadingSpinner'),
                tableEmptyState: document.getElementById('tableEmptyState'),
                advancePaymentsTableBody: document.getElementById('advancePaymentsTableBody'),
                advanceEmptyState: document.getElementById('advanceEmptyState'),
                exportTableExcelBtn: document.getElementById('exportTableExcelBtn'),
                exportTablePdfBtn: document.getElementById('exportTablePdfBtn'),
                payTeacherBtn: document.getElementById('payTeacherBtn'),
                printSlipBtn: document.getElementById('printSlipBtn'),
                teacherIdDisplay: document.getElementById('teacherIdDisplay')
            };

            // Utility functions
            const utils = {
                csrfToken: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',

                teacherId: (() => {
                    const pathParts = window.location.pathname.split('/').filter(part => part);
                    return pathParts[pathParts.length - 1] || '{{ $teacher_id ?? 18 }}';
                })(),

                now: new Date(),
                currentMonth: (new Date().getMonth() + 1).toString().padStart(2, '0'),
                currentYear: new Date().getFullYear().toString(),

                formatCurrency(amount) {
                    let numericAmount = amount;
                    if (typeof amount === 'string') {
                        numericAmount = amount.toString()
                            .replace(/[^\d.-]/g, '')
                            .replace(/,/g, '');
                    }
                    numericAmount = parseFloat(numericAmount);
                    if (isNaN(numericAmount) || numericAmount === null || numericAmount === undefined) {
                        numericAmount = 0;
                    }
                    return new Intl.NumberFormat('en-LK', {
                        style: 'currency',
                        currency: 'LKR',
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }).format(numericAmount);
                },

                formatNumber(num) {
                    if (num == null || num === '' || num === undefined) return '0';
                    const n = parseFloat(num);
                    if (isNaN(n)) return '0';
                    return new Intl.NumberFormat('en-LK', {
                        minimumFractionDigits: 0,
                        maximumFractionDigits: 0
                    }).format(n);
                },

                formatDate(dateString) {
                    try {
                        const date = new Date(dateString);
                        return date.toLocaleDateString('en-GB', {
                            day: '2-digit',
                            month: 'short',
                            year: 'numeric'
                        });
                    } catch (error) {
                        console.warn('Invalid date format:', dateString);
                        return dateString;
                    }
                },

                formatDateTime(dateTimeString) {
                    try {
                        const date = new Date(dateTimeString);
                        return date.toLocaleString('en-GB', {
                            day: '2-digit',
                            month: 'short',
                            year: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit',
                            hour12: true
                        });
                    } catch (error) {
                        console.warn('Invalid datetime format:', dateTimeString);
                        return dateTimeString;
                    }
                },

                formatDateTable(dateString) {
                    try {
                        const date = new Date(dateString);
                        return date.toLocaleDateString('en-GB', {
                            day: '2-digit',
                            month: '2-digit',
                            year: '2-digit'
                        }).replace(/\//g, '/');
                    } catch (error) {
                        console.warn('Invalid date format for table:', dateString);
                        return dateString;
                    }
                },

                getMonthName(monthNumber) {
                    const months = [
                        'January', 'February', 'March', 'April', 'May', 'June',
                        'July', 'August', 'September', 'October', 'November', 'December'
                    ];
                    const monthIndex = parseInt(monthNumber) - 1;
                    return months[monthIndex] || 'Unknown';
                },

                toNumber(value) {
                    if (value == null || value === '' || value === undefined) return 0;
                    if (typeof value === 'number') return value;
                    const cleaned = String(value)
                        .replace(/[^\d.-]/g, '')
                        .replace(/,/g, '');
                    const num = parseFloat(cleaned);
                    return isNaN(num) ? 0 : num;
                },

                toInt(value) {
                    return Math.floor(this.toNumber(value));
                },

                showToast(message, type = 'info') {
                    const toast = document.createElement('div');
                    const bgColor = {
                        success: '#1cc88a',
                        error: '#e74a3b',
                        warning: '#f6c23e',
                        info: '#36b9cc'
                    }[type] || '#36b9cc';

                    const icon = {
                        success: 'fa-check-circle',
                        error: 'fa-exclamation-circle',
                        warning: 'fa-exclamation-triangle',
                        info: 'fa-info-circle'
                    }[type] || 'fa-info-circle';

                    toast.style.cssText = `
                        position: fixed;
                        top: 20px;
                        right: 20px;
                        background: ${bgColor};
                        color: white;
                        padding: 12px 20px;
                        border-radius: 8px;
                        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                        z-index: 999999;
                        animation: slideIn 0.3s ease-out;
                        font-size: 0.875rem;
                        font-weight: 500;
                    `;

                    toast.innerHTML = `
                        <div style="display: flex; align-items: center;">
                            <i class="fas ${icon} me-2"></i>
                            <span>${message}</span>
                        </div>
                    `;

                    document.body.appendChild(toast);

                    setTimeout(() => {
                        toast.style.animation = 'slideOut 0.3s ease-out';
                        setTimeout(() => toast.remove(), 300);
                    }, CONFIG.TOAST_DURATION);

                    return toast;
                },

                // Check if teacher receipt printing is enabled
                checkPrintingEnabled() {
                    try {
                        // First check localStorage
                        const teacherReceiptSettings = localStorage.getItem('teacher_receipt_settings');
                        if (teacherReceiptSettings) {
                            const settings = JSON.parse(teacherReceiptSettings);
                            return settings.teacher_receipt_enabled === true;
                        }

                        // Check if global function exists (from settings page)
                        if (typeof window.getTeacherReceiptStatus === 'function') {
                            return window.getTeacherReceiptStatus();
                        }

                        return false; // Default to false if no setting found
                    } catch (error) {
                        console.error('Error checking printing status:', error);
                        return false;
                    }
                },

                formatMonthYearForURL(monthYear) {
                    const parts = monthYear.split(' ');
                    if (parts.length === 2) {
                        const year = parts[0];
                        const monthName = parts[1];
                        const monthMap = {
                            'Jan': '01', 'Feb': '02', 'Mar': '03', 'Apr': '04',
                            'May': '05', 'Jun': '06', 'Jul': '07', 'Aug': '08',
                            'Sep': '09', 'Oct': '10', 'Nov': '11', 'Dec': '12'
                        };
                        const monthNumber = monthMap[monthName] || '01';
                        return `${year}-${monthNumber}`;
                    }
                    return monthYear;
                },

                debounce(func, wait) {
                    let timeout;
                    return function executedFunction(...args) {
                        const later = () => {
                            clearTimeout(timeout);
                            func(...args);
                        };
                        clearTimeout(timeout);
                        timeout = setTimeout(later, wait);
                    };
                },

                // Open salary slip for printing
                openSalarySlip(teacherId, year, month) {
                    const formattedMonth = month.toString().padStart(2, '0');
                    const yearMonth = `${year}-${formattedMonth}`;

                    // This opens the PRINTABLE salary slip page - NOT an email
                    const salarySlipUrl = `/teacher-payment/salary-slip/${teacherId}/${yearMonth}`;

                    const printWindow = window.open(salarySlipUrl, '_blank', 'width=900,height=700,scrollbars=yes');

                    if (printWindow) {
                        printWindow.focus();

                        printWindow.onload = function () {
                            // Auto-print after loading
                            setTimeout(() => {
                                printWindow.print();

                                // Optional: close window after printing (some browsers block this)
                                try {
                                    printWindow.onafterprint = function () {
                                        setTimeout(() => {
                                            printWindow.close();
                                        }, 500);
                                    };
                                } catch (e) {
                                    console.log('Could not close print window automatically');
                                }
                            }, CONFIG.PRINT_WINDOW_DELAY);
                        };
                    } else {
                        utils.showToast('Failed to open print window. Please check popup blocker.', 'error');
                    }
                }
            };

            // UI State Management
            const ui = {
                showTableLoading(show) {
                    if (elements.tableLoadingSpinner) {
                        elements.tableLoadingSpinner.classList.toggle('d-none', !show);
                    }
                },

                showTableEmptyState(show) {
                    if (elements.tableEmptyState) {
                        elements.tableEmptyState.classList.toggle('d-none', !show);
                    }
                },

                showAdvanceEmptyState(show) {
                    if (elements.advanceEmptyState) {
                        elements.advanceEmptyState.classList.toggle('d-none', !show);
                    }
                },

                updateSelectedMonthYear() {
                    if (elements.selectedMonthYear) {
                        elements.selectedMonthYear.textContent =
                            `${utils.getMonthName(utils.currentMonth)} ${utils.currentYear}`;
                    }
                    if (elements.summaryMonthYear) {
                        elements.summaryMonthYear.textContent =
                            `${utils.getMonthName(utils.currentMonth)} ${utils.currentYear}`;
                    }
                },

                enablePayButton(enable) {
                    if (elements.payTeacherBtn) {
                        elements.payTeacherBtn.disabled = !enable;
                    }
                },

                enablePrintButton(enable) {
                    if (elements.printSlipBtn) {
                        elements.printSlipBtn.disabled = !enable;
                    }
                },

                setPayButtonLoading(loading) {
                    if (elements.payTeacherBtn) {
                        if (loading) {
                            elements.payTeacherBtn.disabled = true;
                            elements.payTeacherBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Processing...';
                        } else {
                            elements.payTeacherBtn.innerHTML = '<i class="fas fa-money-check-alt me-1"></i> Pay Teacher';
                        }
                    }
                }
            };

            // Data fetching
            async function fetchTeacherData(retryCount = 0) {
                const fetchId = ++state.currentFetchId;

                if (state.abortController) {
                    state.abortController.abort();
                }

                state.abortController = new AbortController();
                const timeoutId = setTimeout(() => {
                    state.abortController.abort();
                }, CONFIG.API_TIMEOUT);

                try {
                    ui.showTableLoading(true);

                    const url = `/api/teacher-payments/monthly-income/${utils.teacherId}/${utils.currentYear}-${utils.currentMonth}`;

                    const response = await fetch(url, {
                        signal: state.abortController.signal,
                        headers: {
                            'Accept': 'application/json',
                            'Cache-Control': 'no-cache'
                        }
                    });

                    clearTimeout(timeoutId);

                    if (fetchId !== state.currentFetchId) {
                        return;
                    }

                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }

                    const data = await response.json();

                    if (data.status === 'success') {
                        state.teacherData = data;
                        renderAllData();
                    } else {
                        throw new Error(data.message || 'Failed to load teacher data');
                    }
                } catch (error) {
                    if (error.name === 'AbortError') {
                        console.log('Fetch aborted');
                        return;
                    }

                    console.error('Error fetching teacher data:', error);

                    if (retryCount < CONFIG.MAX_RETRIES) {
                        console.log(`Retrying... (${retryCount + 1}/${CONFIG.MAX_RETRIES})`);
                        await new Promise(resolve => setTimeout(resolve, CONFIG.RETRY_DELAY * (retryCount + 1)));
                        return fetchTeacherData(retryCount + 1);
                    }

                    ui.showTableEmptyState(true);
                    utils.showToast('Failed to load teacher data. Please try again.', 'error');
                } finally {
                    clearTimeout(timeoutId);
                    ui.showTableLoading(false);
                }
            }

            // Render functions
            function renderAllData() {
                if (!state.teacherData) return;

                renderTeacherData();
                renderClassesCards();
                renderPaymentTable();
                renderAdvancePayments();
                ui.updateSelectedMonthYear();
            }

            function renderTeacherData() {
                if (!state.teacherData) return;

                const data = state.teacherData;

                // Update teacher information
                if (elements.teacherNameTitle) {
                    elements.teacherNameTitle.textContent = `${data.teacher_name}'s Income`;
                }

                if (elements.teacherId) elements.teacherId.textContent = data.teacher_id || '-';
                if (elements.teacherIdDisplay) elements.teacherIdDisplay.textContent = data.teacher_id || '-';
                if (elements.teacherName) elements.teacherName.textContent = data.teacher_name || '-';

                // Extract subject from classes if not in main data
                let subject = data.subject_name;
                if (!subject && data.classes && data.classes.length > 0) {
                    subject = data.classes[0].subject_name;
                }
                if (elements.subjectName) elements.subjectName.textContent = subject || '-';

                if (elements.salaryStatus) {
                    elements.salaryStatus.textContent = data.is_salary_paid ? 'Salary Paid' : 'Salary Not Paid';
                    elements.salaryStatus.className = `badge bg-${data.is_salary_paid ? 'success' : 'warning'} px-3 py-2 rounded`;
                }

                // Update financial summary with new API structure
                const totalCollections = utils.toNumber(data.total_payments_this_month || 0);
                const advancePayments = utils.toNumber(data.advance_payment_this_month || 0);
                const teacherShare = utils.toNumber(data.total_teacher_share || 0);
                const institutionShare = utils.toNumber(data.total_institution_share || 0);
                const netPayable = utils.toNumber(data.net_payable || 0);

                if (elements.totalCollections) {
                    elements.totalCollections.textContent = utils.formatCurrency(totalCollections);
                }

                if (elements.advancePayments) {
                    elements.advancePayments.textContent = utils.formatCurrency(advancePayments);
                }

                if (elements.teacherShare) {
                    elements.teacherShare.textContent = utils.formatCurrency(teacherShare);
                }

                if (elements.institutionShare) {
                    elements.institutionShare.textContent = utils.formatCurrency(institutionShare);
                }

                if (elements.netPayable) {
                    elements.netPayable.textContent = utils.formatCurrency(netPayable);
                }

                // Calculate percentages based on total collections
                let teacherPercentage = 0;
                let institutionPercentage = 0;

                if (totalCollections > 0) {
                    teacherPercentage = Math.round((teacherShare / totalCollections) * 100);
                    institutionPercentage = Math.round((institutionShare / totalCollections) * 100);
                }

                // Update percentage texts
                if (elements.teacherPercentageText) {
                    elements.teacherPercentageText.textContent = `${teacherPercentage}%`;
                }
                if (elements.institutionPercentageText) {
                    elements.institutionPercentageText.textContent = `${institutionPercentage}%`;
                }
                if (elements.teacherSharePercentage) {
                    elements.teacherSharePercentage.textContent = `${teacherPercentage}% of total collections`;
                }
                if (elements.institutionSharePercentage) {
                    elements.institutionSharePercentage.textContent = `${institutionPercentage}% of total collections`;
                }

                // Update progress bars
                if (elements.teacherPercentageBar) {
                    elements.teacherPercentageBar.style.width = `${teacherPercentage}%`;
                    if (elements.teacherPercentageTextBar) {
                        elements.teacherPercentageTextBar.textContent = `Teacher: ${teacherPercentage}%`;
                    }
                }

                if (elements.institutionPercentageBar) {
                    elements.institutionPercentageBar.style.width = `${institutionPercentage}%`;
                    if (elements.institutionPercentageTextBar) {
                        elements.institutionPercentageTextBar.textContent = `Institution: ${institutionPercentage}%`;
                    }
                }

                // Update Pay Teacher button - ALWAYS ENABLED WHEN netPayable >= 0
                if (elements.payTeacherBtn) {
                    const shouldEnable = netPayable >= 0 && !data.is_salary_paid;
                    elements.payTeacherBtn.disabled = !shouldEnable;
                    elements.payTeacherBtn.title = shouldEnable ?
                        `Pay ${utils.formatCurrency(netPayable)}` :
                        (data.is_salary_paid ? 'Salary already paid' : 'No amount payable');

                    // Update button styling
                    if (netPayable === 0 && !data.is_salary_paid) {
                        elements.payTeacherBtn.innerHTML = '<i class="fas fa-check-circle me-1"></i> Mark as Paid (LKR 0)';
                    }
                }

                // Update Print Slip button - enable only if salary is paid AND printing is enabled in settings
                if (elements.printSlipBtn) {
                    const isSalaryPaid = data.is_salary_paid || false;
                    const isPrintingEnabled = utils.checkPrintingEnabled();
                    const shouldEnablePrint = isSalaryPaid && isPrintingEnabled;

                    ui.enablePrintButton(shouldEnablePrint);

                    elements.printSlipBtn.title = shouldEnablePrint ?
                        'Print Salary Slip' :
                        (isSalaryPaid ? 'Printing disabled in settings' : 'Salary not paid yet');
                }
            }

            function renderClassesCards() {
                if (!state.teacherData || !elements.classesCards || !state.teacherData.classes) return;

                elements.classesCards.innerHTML = '';

                if (state.teacherData.classes.length === 0) {
                    elements.classesCards.innerHTML = `
                        <div class="col-12 text-center py-4">
                            <i class="fas fa-chalkboard-teacher fa-2x text-muted mb-3"></i>
                            <p class="text-muted">No classes found for this teacher.</p>
                        </div>
                    `;
                    return;
                }

                state.teacherData.classes.forEach((cls, index) => {
                    const totalStudents = utils.toInt(cls.total_students || 0);
                    const paidStudents = utils.toInt(cls.paid_students || 0);
                    const unpaidStudents = utils.toInt(cls.unpaid_students || 0);
                    const freeCardStudents = utils.toInt(cls.free_card_students || 0);
                    const percentagePaid = totalStudents > 0 ? Math.round((paidStudents / totalStudents) * 100) : 0;

                    // Calculate total from daily payments
                    let totalCollection = 0;
                    if (cls.daily_payments && typeof cls.daily_payments === 'object') {
                        Object.values(cls.daily_payments).forEach(val => {
                            totalCollection += utils.toNumber(val);
                        });
                    }

                    const teacherShare = utils.toNumber(cls.teacher_share || 0);
                    const teacherPercentage = cls.teacher_percentage || '0';

                    const card = document.createElement('div');
                    card.className = 'col-md-6 col-lg-3 mb-3';
                    card.innerHTML = `
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white border-bottom py-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0 small fw-bold text-truncate" title="${cls.class_name || 'Class'}">
                                        ${cls.class_name || 'Class'}
                                    </h6>
                                    <span class="badge bg-primary">Grade ${cls.grade_name || 'N/A'}</span>
                                </div>
                                <small class="text-muted">${cls.subject_name || 'Subject'}</small>
                            </div>
                            <div class="card-body">
                                <!-- Students Stats -->
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="text-muted small">Students:</span>
                                        <span class="fw-bold small">${utils.formatNumber(totalStudents)}</span>
                                    </div>
                                    <div class="progress mb-1" style="height: 6px;">
                                        <div class="progress-bar bg-success" style="width: ${percentagePaid}%"></div>
                                    </div>
                                    <div class="row small text-center">
                                        <div class="col-4">
                                            <span class="text-success fw-bold">${utils.formatNumber(paidStudents)}</span>
                                            <div class="text-muted">Paid</div>
                                        </div>
                                        <div class="col-4">
                                            <span class="text-danger fw-bold">${utils.formatNumber(unpaidStudents)}</span>
                                            <div class="text-muted">Unpaid</div>
                                        </div>
                                        <div class="col-4">
                                            <span class="text-info fw-bold">${utils.formatNumber(freeCardStudents)}</span>
                                            <div class="text-muted">Free</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Financial Stats -->
                                <div class="border-top pt-2">
                                    <div class="mb-1">
                                        <div class="d-flex justify-content-between">
                                            <span class="text-muted small">Total:</span>
                                            <span class="fw-bold small">${utils.formatCurrency(totalCollection)}</span>
                                        </div>
                                    </div>
                                    <div class="mb-1">
                                        <div class="d-flex justify-content-between">
                                            <span class="text-muted small">Teacher (${teacherPercentage}%):</span>
                                            <span class="fw-bold text-success small">${utils.formatCurrency(teacherShare)}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;

                    elements.classesCards.appendChild(card);
                });
            }

            // අලුත් කරපු renderPaymentTable function එක
            function renderPaymentTable() {
                if (!state.teacherData || !elements.paymentTableBody) {
                    ui.showTableEmptyState(true);
                    return;
                }

                elements.paymentTableBody.innerHTML = '';
                state.allPayments = [];
                state.allGrades = new Set();

                // --- Step 1: Create a map for quick class data access by grade ---
                const classDataByGrade = {};
                if (state.teacherData.classes) {
                    state.teacherData.classes.forEach(cls => {
                        const grade = cls.grade_name;
                        if (grade) {
                            classDataByGrade[grade] = {
                                teacherPercentage: parseFloat(cls.teacher_percentage) || 0,
                            };
                            state.allGrades.add(grade);
                        }
                    });
                }
                state.allGrades = Array.from(state.allGrades).sort();
                // ----------------------------------------------------------------

                // Collect all unique dates from daily_payments
                const dateMap = new Map();

                state.teacherData.classes.forEach(cls => {
                    const grade = cls.grade_name;
                    if (cls.daily_payments && typeof cls.daily_payments === 'object') {
                        Object.entries(cls.daily_payments).forEach(([date, amount]) => {
                            if (!dateMap.has(date)) {
                                dateMap.set(date, {
                                    date: date,
                                    gradePayments: {},
                                    gradePercentages: {}, // Store percentage for each grade's payment
                                    totalCollection: 0
                                });
                            }
                            const dayData = dateMap.get(date);
                            const numericAmount = utils.toNumber(amount);
                            dayData.gradePayments[grade] = (dayData.gradePayments[grade] || 0) + numericAmount;
                            dayData.gradePercentages[grade] = classDataByGrade[grade]?.teacherPercentage || 0;
                            dayData.totalCollection += numericAmount;
                        });
                    }
                });

                // Convert to array and sort by date
                const sortedDates = Array.from(dateMap.values()).sort((a, b) =>
                    new Date(a.date) - new Date(b.date)
                );

                if (sortedDates.length === 0) {
                    ui.showTableEmptyState(true);
                    return;
                }

                ui.showTableEmptyState(false);
                renderTableHeader();

                // Calculate totals
                const totals = {
                    gradeTotals: {},
                    totalCollection: 0,
                    institutionShare: 0,
                    teacherShare: 0
                };

                state.allGrades.forEach(grade => {
                    totals.gradeTotals[grade] = 0;
                });

                // Render table rows
                sortedDates.forEach(dayData => {
                    const row = document.createElement('tr');
                    let rowHTML = `<td class="fw-bold">${utils.formatDateTable(dayData.date)}</td>`;

                    // For each grade, display the amount
                    state.allGrades.forEach(grade => {
                        const amount = dayData.gradePayments[grade] || 0;
                        totals.gradeTotals[grade] += amount;
                        rowHTML += `<td>${amount > 0 ? utils.formatCurrency(amount) : '-'}</td>`;
                    });

                    totals.totalCollection += dayData.totalCollection;

                    // --- Step 2: Calculate shares based on each grade's actual percentage ---
                    let dailyTeacherShare = 0;
                    let dailyInstitutionShare = 0;

                    Object.entries(dayData.gradePayments).forEach(([grade, amount]) => {
                        const teacherPercentage = dayData.gradePercentages[grade] || 0;
                        const institutionPercentage = 100 - teacherPercentage;

                        const teacherPortion = amount * (teacherPercentage / 100);
                        const institutionPortion = amount * (institutionPercentage / 100);

                        dailyTeacherShare += teacherPortion;
                        dailyInstitutionShare += institutionPortion;
                    });

                    // Add to overall totals
                    totals.teacherShare += dailyTeacherShare;
                    totals.institutionShare += dailyInstitutionShare;
                    // --------------------------------------------------------------------

                    // Calculate percentages for display in this row
                    const teacherPercentageForRow = dayData.totalCollection > 0
                        ? (dailyTeacherShare / dayData.totalCollection) * 100
                        : 0;
                    const institutionPercentageForRow = 100 - teacherPercentageForRow;

                    rowHTML += `
                        <td class="fw-bold text-primary">${utils.formatCurrency(dayData.totalCollection)}</td>
                        <td class="text-secondary">
                            ${utils.formatCurrency(dailyInstitutionShare)}
                            <small class="d-block">(${institutionPercentageForRow.toFixed(1)}%)</small>
                        </td>
                        <td class="fw-bold text-success">
                            ${utils.formatCurrency(dailyTeacherShare)}
                            <small class="d-block">(${teacherPercentageForRow.toFixed(1)}%)</small>
                        </td>
                    `;

                    row.innerHTML = rowHTML;
                    elements.paymentTableBody.appendChild(row);

                    // Store for export
                    state.allPayments.push({
                        date: dayData.date,
                        gradePayments: dayData.gradePayments,
                        totalCollection: dayData.totalCollection,
                        institutionShare: dailyInstitutionShare,
                        teacherShare: dailyTeacherShare,
                    });
                });

                // Calculate overall percentages for footer
                const overallTeacherPercentage = totals.totalCollection > 0
                    ? (totals.teacherShare / totals.totalCollection) * 100
                    : 0;
                const overallInstitutionPercentage = 100 - overallTeacherPercentage;

                renderTableFooter(totals, overallTeacherPercentage, overallInstitutionPercentage);
            }

            // renderTableHeader function එක (වෙනසක් නැහැ)
            function renderTableHeader() {
                if (!elements.paymentTableHeader) return;

                elements.paymentTableHeader.innerHTML = `
                    <tr>
                        <th class="py-2">Date</th>
                        ${state.allGrades.map(grade => `<th class="py-2">Grade ${grade}</th>`).join('')}
                        <th class="py-2 text-primary">Total</th>
                        <th class="py-2 text-secondary">Institution</th>
                        <th class="py-2 text-success">Teacher</th>
                    </tr>
                `;
            }

            // අලුත් කරපු renderTableFooter function එක
            function renderTableFooter(totals, teacherPercentage, institutionPercentage) {
                if (!elements.paymentTableFooter) return;

                elements.paymentTableFooter.innerHTML = `
                    <tr>
                        <td class="fw-bold py-2">Totals</td>
                        ${state.allGrades.map(grade => `
                            <td class="fw-bold py-2">${utils.formatCurrency(totals.gradeTotals[grade] || 0)}</td>
                        `).join('')}
                        <td class="fw-bold py-2 text-primary">${utils.formatCurrency(totals.totalCollection)}</td>
                        <td class="fw-bold py-2 text-secondary">
                            ${utils.formatCurrency(totals.institutionShare)}
                            <small class="d-block">(${institutionPercentage.toFixed(1)}%)</small>
                        </td>
                        <td class="fw-bold py-2 text-success">
                            ${utils.formatCurrency(totals.teacherShare)}
                            <small class="d-block">(${teacherPercentage.toFixed(1)}%)</small>
                        </td>
                    </tr>
                `;
            }

            function renderAdvancePayments() {
                if (!state.teacherData || !elements.advancePaymentsTableBody) return;

                elements.advancePaymentsTableBody.innerHTML = '';

                // Check if we have advance_payment_records in response
                if (state.teacherData.advance_payment_records &&
                    Array.isArray(state.teacherData.advance_payment_records) &&
                    state.teacherData.advance_payment_records.length > 0) {

                    state.teacherData.advance_payment_records.forEach(record => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td>${utils.formatDateTime(record.date)}</td>
                            <td class="fw-bold">${utils.formatCurrency(record.payment)}</td>
                            <td>
                                <span class="badge bg-info">
                                    ${record.reason_code || 'N/A'}
                                </span>
                                ${record.reason ? `<br><small class="text-muted">${record.reason}</small>` : ''}
                            </td>
                            <td>${record.payment_for || 'N/A'}</td>
                            <td>
                                <span class="badge ${record.status ? 'bg-success' : 'bg-danger'}">
                                    ${record.status ? 'Active' : 'Deleted'}
                                </span>
                            </td>
                            <td>${record.user_name || 'System'}</td>
                        `;
                        elements.advancePaymentsTableBody.appendChild(row);
                    });

                    ui.showAdvanceEmptyState(false);
                } else {
                    ui.showAdvanceEmptyState(true);
                }
            }

            // Payment processing
            function setupPayTeacherButton() {
                if (!elements.payTeacherBtn) return;

                elements.payTeacherBtn.addEventListener('click', function () {
                    if (!state.teacherData || state.teacherData.is_salary_paid) {
                        utils.showToast('Salary already paid for this month', 'warning');
                        return;
                    }

                    const amount = utils.toNumber(state.teacherData.net_payable || 0);
                    const teacherName = state.teacherData.teacher_name;
                    const teacherId = state.teacherData.teacher_id;
                    const monthYear = `${utils.getMonthName(utils.currentMonth)} ${utils.currentYear}`;

                    // Always allow payment even if amount is 0
                    showPaymentConfirmation(teacherName, amount, monthYear, function (confirmed) {
                        if (confirmed) {
                            processPayment(teacherId, teacherName, amount, monthYear);
                        }
                    });
                });
            }

            // Print slip functionality
            function setupPrintSlipButton() {
                if (!elements.printSlipBtn) return;

                elements.printSlipBtn.addEventListener('click', function () {
                    if (!state.teacherData || !state.teacherData.is_salary_paid) {
                        utils.showToast('Salary not paid yet for this month', 'warning');
                        return;
                    }

                    // Check if printing is enabled in settings
                    const isPrintingEnabled = utils.checkPrintingEnabled();
                    if (!isPrintingEnabled) {
                        utils.showToast('Teacher receipt printing is disabled in system settings', 'error');
                        return;
                    }

                    const teacherName = state.teacherData.teacher_name;
                    const teacherId = state.teacherData.teacher_id;
                    const monthYear = `${utils.getMonthName(utils.currentMonth)} ${utils.currentYear}`;
                    const amount = utils.toNumber(state.teacherData.net_payable || 0);

                    // Show printing confirmation
                    const confirmed = confirm(`Print salary slip for ${teacherName}?\nAmount: ${utils.formatCurrency(amount)}\nPeriod: ${monthYear}`);

                    if (confirmed) {
                        showPrintingInProgress();
                        utils.openSalarySlip(teacherId, utils.currentYear, utils.currentMonth);

                        // Hide progress after delay
                        setTimeout(() => {
                            hidePrintingInProgress();
                        }, 2000);
                    }
                });
            }

            function showPrintingInProgress() {
                utils.showToast('Opening salary slip for printing...', 'info');

                // Optional: Show a small overlay
                const overlay = document.createElement('div');
                overlay.id = 'printingOverlay';
                overlay.style.cssText = `
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background: rgba(0, 0, 0, 0.5);
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    z-index: 9999;
                `;
                overlay.innerHTML = `
                    <div style="
                        background: white;
                        padding: 20px;
                        border-radius: 8px;
                        text-align: center;
                    ">
                        <i class="fas fa-print fa-2x text-primary mb-2"></i>
                        <p style="margin: 10px 0 0 0; font-weight: 500;">Printing salary slip...</p>
                        <p style="margin: 5px 0 0 0; font-size: 0.9rem; color: #666;">Please check your print dialog</p>
                    </div>
                `;
                document.body.appendChild(overlay);
            }

            function hidePrintingInProgress() {
                const overlay = document.getElementById('printingOverlay');
                if (overlay) {
                    overlay.remove();
                }
            }

            function showPaymentConfirmation(teacherName, amount, monthYear, callback) {
                const modal = document.createElement('div');
                modal.id = 'paymentConfirmation';
                modal.style.cssText = `
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background: rgba(0, 0, 0, 0.5);
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    z-index: 9998;
                    backdrop-filter: blur(2px);
                `;

                modal.innerHTML = `
                    <div style="
                        background: white;
                        padding: 25px;
                        border-radius: 12px;
                        max-width: 400px;
                        width: 90%;
                        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
                    ">
                        <div style="text-align: center; margin-bottom: 20px;">
                            <div style="
                                width: 60px;
                                height: 60px;
                                background: #4e73df;
                                border-radius: 50%;
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                margin: 0 auto 15px;
                            ">
                                <i class="fas fa-money-check-alt" style="font-size: 24px; color: white;"></i>
                            </div>
                            <h5 style="margin: 0 0 5px 0; color: #333; font-weight: 600;">Confirm Payment</h5>
                            <p style="color: #666; font-size: 14px; margin: 0;">Please review the payment details</p>
                        </div>

                        <div style="
                            background: #f8f9fc;
                            padding: 15px;
                            border-radius: 8px;
                            margin-bottom: 20px;
                        ">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 10px; padding-bottom: 10px; border-bottom: 1px solid #e3e6f0;">
                                <span style="color: #5a5c69; font-weight: 500;">Teacher:</span>
                                <strong>${teacherName}</strong>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 10px; padding-bottom: 10px; border-bottom: 1px solid #e3e6f0;">
                                <span style="color: #5a5c69; font-weight: 500;">Amount:</span>
                                <strong style="color: #1cc88a; font-size: 18px;">${utils.formatCurrency(amount)}</strong>
                            </div>
                            <div style="display: flex; justify-content: space-between;">
                                <span style="color: #5a5c69; font-weight: 500;">Period:</span>
                                <strong>${monthYear}</strong>
                            </div>
                        </div>

                        <div style="display: flex; gap: 10px;">
                            <button id="confirmBtn" style="
                                background: #1cc88a;
                                color: white;
                                border: none;
                                padding: 12px 20px;
                                border-radius: 8px;
                                cursor: pointer;
                                font-size: 14px;
                                font-weight: 600;
                                flex: 1;
                                transition: all 0.3s;
                            " onmouseover="this.style.backgroundColor='#17a673'" onmouseout="this.style.backgroundColor='#1cc88a'">
                                <i class="fas fa-check-circle me-1"></i> Confirm
                            </button>

                            <button id="cancelBtn" style="
                                background: #e74a3b;
                                color: white;
                                border: none;
                                padding: 12px 20px;
                                border-radius: 8px;
                                cursor: pointer;
                                font-size: 14px;
                                font-weight: 600;
                                flex: 1;
                                transition: all 0.3s;
                            " onmouseover="this.style.backgroundColor='#d62c1a'" onmouseout="this.style.backgroundColor='#e74a3b'">
                                <i class="fas fa-times-circle me-1"></i> Cancel
                            </button>
                        </div>
                    </div>
                `;

                document.body.appendChild(modal);

                document.getElementById('confirmBtn').addEventListener('click', function () {
                    modal.remove();
                    callback(true);
                });

                document.getElementById('cancelBtn').addEventListener('click', function () {
                    modal.remove();
                    callback(false);
                });

                modal.addEventListener('click', function (e) {
                    if (e.target === modal) {
                        modal.remove();
                        callback(false);
                    }
                });
            }

            function processPayment(teacherId, teacherName, amount, monthYear) {
                if (state.isProcessingPayment) return;

                state.isProcessingPayment = true;
                ui.setPayButtonLoading(true);

                showPaymentProcessing(teacherName, amount, monthYear);

                const paymentData = {
                    teacher_id: teacherId,
                    payment: amount,
                    reason_code: 'salary',
                    month_year: monthYear,
                    net_payable: amount
                };

                fetch('/api/teacher-payments', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': utils.csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(paymentData)
                })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.status === 'success') {
                            hidePaymentProcessing();

                            const isPrintingEnabled = utils.checkPrintingEnabled();

                            // PRINT SALARY SLIP if enabled in settings (NOT email)
                            if (isPrintingEnabled) {
                                setTimeout(() => {
                                    // This opens and prints the salary slip
                                    utils.openSalarySlip(teacherId, utils.currentYear, utils.currentMonth);
                                }, 500);
                            }

                            showPaymentSuccess(data, teacherId, teacherName, amount, monthYear, isPrintingEnabled);

                            // Refresh data
                            setTimeout(() => {
                                fetchTeacherData();
                            }, CONFIG.REFRESH_DELAY);

                        } else {
                            throw new Error(data.message || 'Payment failed');
                        }
                    })
                    .catch(error => {
                        hidePaymentProcessing();
                        showPaymentError(error.message, teacherName, amount);
                        state.isProcessingPayment = false;
                        ui.setPayButtonLoading(false);
                    });
            }

            // Export functions
            function setupExportTableExcel() {
                if (!elements.exportTableExcelBtn) return;

                elements.exportTableExcelBtn.addEventListener('click', utils.debounce(function () {
                    if (!state.teacherData || state.allPayments.length === 0) {
                        utils.showToast('No data to export', 'warning');
                        return;
                    }

                    try {
                        const exportData = state.allPayments.map(payment => {
                            const rowData = {
                                'Date': utils.formatDateTable(payment.date)
                            };

                            state.allGrades.forEach(grade => {
                                rowData[`Grade ${grade}`] = payment.gradePayments[grade] || 0;
                            });

                            rowData['Total Collection'] = payment.totalCollection || 0;
                            rowData['Institution Share'] = payment.institutionShare || 0;
                            rowData['Teacher Share'] = payment.teacherShare || 0;

                            return rowData;
                        });

                        const ws = XLSX.utils.json_to_sheet(exportData);
                        const wb = XLSX.utils.book_new();
                        XLSX.utils.book_append_sheet(wb, ws, 'Teacher Payments');

                        const filename = `${state.teacherData.teacher_name}_${utils.getMonthName(utils.currentMonth)}_${utils.currentYear}_Payments.xlsx`;
                        XLSX.writeFile(wb, filename);

                        utils.showToast('Excel file exported successfully', 'success');
                    } catch (error) {
                        console.error('Error exporting to Excel:', error);
                        utils.showToast('Failed to export Excel file', 'error');
                    }
                }, 300));
            }

            function setupExportTablePdf() {
                if (!elements.exportTablePdfBtn) return;

                elements.exportTablePdfBtn.addEventListener('click', utils.debounce(function () {
                    if (!state.teacherData || state.allPayments.length === 0) {
                        utils.showToast('No data to export', 'warning');
                        return;
                    }

                    try {
                        const { jsPDF } = window.jspdf;
                        const doc = new jsPDF('landscape');

                        doc.setFontSize(14);
                        doc.text(`${state.teacherData.teacher_name} - Payment Report`, 14, 10);
                        doc.setFontSize(10);
                        doc.text(`Period: ${utils.getMonthName(utils.currentMonth)} ${utils.currentYear}`, 14, 16);
                        doc.text(`Generated: ${new Date().toLocaleDateString()}`, 14, 22);

                        const headers = ['Date'];
                        state.allGrades.forEach(grade => {
                            headers.push(`Grade ${grade}`);
                        });
                        headers.push('Total', 'Institution', 'Teacher');

                        const tableData = state.allPayments.map(payment => {
                            const row = [utils.formatDateTable(payment.date)];

                            state.allGrades.forEach(grade => {
                                row.push(utils.formatCurrency(payment.gradePayments[grade] || 0));
                            });

                            row.push(
                                utils.formatCurrency(payment.totalCollection),
                                utils.formatCurrency(payment.institutionShare),
                                utils.formatCurrency(payment.teacherShare)
                            );

                            return row;
                        });

                        doc.autoTable({
                            head: [headers],
                            body: tableData,
                            startY: 30,
                            styles: { fontSize: 8 },
                            headStyles: { fillColor: [78, 115, 223] }
                        });

                        const filename = `${state.teacherData.teacher_name}_${utils.getMonthName(utils.currentMonth)}_${utils.currentYear}_Payments.pdf`;
                        doc.save(filename);

                        utils.showToast('PDF file exported successfully', 'success');
                    } catch (error) {
                        console.error('Error exporting to PDF:', error);
                        utils.showToast('Failed to export PDF file', 'error');
                    }
                }, 300));
            }

            // Helper functions for payment processing
            function showPaymentProcessing(teacherName, amount, monthYear) {
                const overlay = document.createElement('div');
                overlay.id = 'paymentProcessing';
                overlay.style.cssText = `
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background: rgba(0, 0, 0, 0.7);
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    z-index: 9999;
                `;

                const isPrintingEnabled = utils.checkPrintingEnabled();

                let printMessage = '';
                let emailMessage = '';

                if (isPrintingEnabled) {
                    printMessage = '<br>• Salary slip will be printed';
                }

                const featuresMessage = isPrintingEnabled
                    ? `After payment:${printMessage}${emailMessage}`
                    : 'Payment will be processed (no additional actions)';

                overlay.innerHTML = `
                    <div style="
                        background: white;
                        padding: 20px;
                        border-radius: 8px;
                        max-width: 300px;
                        width: 90%;
                        text-align: center;
                    ">
                        <div style="font-size: 30px; color: #4e73df; margin-bottom: 10px;">
                            <i class="fas fa-spinner fa-spin"></i>
                        </div>
                        <h5 style="margin-bottom: 15px; color: #333;">Processing Payment</h5>
                        <div style="margin-bottom: 15px;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                                <span style="color: #666;">Teacher:</span>
                                <strong>${teacherName}</strong>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                                <span style="color: #666;">Amount:</span>
                                <strong>${utils.formatCurrency(amount)}</strong>
                            </div>
                            <div style="display: flex; justify-content: space-between;">
                                <span style="color: #666;">Period:</span>
                                <strong>${monthYear}</strong>
                            </div>
                        </div>
                        <div style="
                            background: #e8f4fd;
                            padding: 8px;
                            border-radius: 4px;
                            margin-top: 15px;
                            border-left: 3px solid #4e73df;
                        ">
                            <p style="margin: 0; color: #2e59d9; font-size: 12px;">
                                <i class="fas fa-info-circle me-1"></i>
                                ${featuresMessage}
                            </p>
                        </div>
                        <p style="color: #666; font-size: 13px; margin: 10px 0 0 0;">
                            Please wait...
                        </p>
                    </div>
                `;

                document.body.appendChild(overlay);
            }

            function hidePaymentProcessing() {
                const overlay = document.getElementById('paymentProcessing');
                if (overlay) {
                    overlay.remove();
                }
            }

            function showPaymentSuccess(data, teacherId, teacherName, amount, monthYear, isPrintingEnabled) {
                const modal = document.createElement('div');
                modal.id = 'paymentSuccess';
                modal.style.cssText = `
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background: rgba(0, 0, 0, 0.7);
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    z-index: 99999;
                `;

                const formattedAmount = utils.formatCurrency(amount);
                const paymentDate = new Date().toLocaleTimeString('en-LK', {
                    hour: '2-digit',
                    minute: '2-digit'
                });

                let printStatusMessage = '';
                let emailStatusMessage = '';
                let actionsHTML = '';

                if (isPrintingEnabled) {
                    printStatusMessage = `
                        <div style="
                            background: #d4edda;
                            padding: 8px;
                            border-radius: 4px;
                            margin-bottom: 8px;
                            border-left: 3px solid #1cc88a;
                        ">
                            <p style="margin: 0; color: #155724; font-size: 12px;">
                                <i class="fas fa-check-circle me-1"></i>
                                Salary slip has been printed
                            </p>
                        </div>
                    `;
                    actionsHTML += `
                        <button id="printAgainBtn" style="
                            background: #4e73df;
                            color: white;
                            border: none;
                            padding: 8px 15px;
                            border-radius: 4px;
                            cursor: pointer;
                            font-size: 14px;
                            flex: 1;
                        ">
                            <i class="fas fa-print me-1"></i> Print Slip Again
                        </button>
                    `;
                }


                actionsHTML += `
                    <button id="closeBtn" style="
                        background: #5a5c69;
                        color: white;
                        border: none;
                        padding: 8px 15px;
                        border-radius: 4px;
                        cursor: pointer;
                        font-size: 14px;
                        flex: 1;
                    ">
                        Close
                    </button>
                `;

                modal.innerHTML = `
                    <div style="
                        background: white;
                        padding: 20px;
                        border-radius: 8px;
                        max-width: 400px;
                        width: 90%;
                    ">
                        <div style="text-align: center; margin-bottom: 15px;">
                            <div style="
                                width: 50px;
                                height: 50px;
                                background: #1cc88a;
                                border-radius: 50%;
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                margin: 0 auto 10px;
                            ">
                                <i class="fas fa-check" style="font-size: 20px; color: white;"></i>
                            </div>
                            <h5 style="margin: 0 0 5px 0; color: #1cc88a;">Payment Successful</h5>
                            <p style="color: #666; font-size: 13px; margin: 0;">${teacherName}</p>
                        </div>

                        <div style="margin-bottom: 15px;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                                <span style="color: #666;">Amount:</span>
                                <strong style="color: #1cc88a;">${formattedAmount}</strong>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                                <span style="color: #666;">Period:</span>
                                <strong>${monthYear}</strong>
                            </div>
                            <div style="display: flex; justify-content: space-between;">
                                <span style="color: #666;">Time:</span>
                                <strong>${paymentDate}</strong>
                            </div>
                        </div>

                        <!--Status messages-->
                        ${printStatusMessage}
                        ${emailStatusMessage}

                        <div style="display: flex; gap: 10px; margin-top: 20px;">
                            ${actionsHTML}
                        </div>
                    </div>
                `;

                document.body.appendChild(modal);

                // Event listeners
                if (isPrintingEnabled) {
                    document.getElementById('printAgainBtn').addEventListener('click', function () {
                        utils.openSalarySlip(teacherId, utils.currentYear, utils.currentMonth);
                        utils.showToast('Printing salary slip again...', 'info');
                    });
                }

                document.getElementById('closeBtn').addEventListener('click', function () {
                    modal.remove();
                    state.isProcessingPayment = false;
                    ui.setPayButtonLoading(false);
                });

                // Auto close after timeout
                setTimeout(() => {
                    if (document.getElementById('paymentSuccess')) {
                        modal.remove();
                        state.isProcessingPayment = false;
                        ui.setPayButtonLoading(false);
                    }
                }, CONFIG.AUTO_CLOSE_TIMEOUT);
            }

            function showPaymentError(errorMessage, teacherName, amount) {
                const modal = document.createElement('div');
                modal.id = 'paymentError';
                modal.style.cssText = `
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background: rgba(0, 0, 0, 0.7);
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    z-index: 99999;
                `;

                modal.innerHTML = `
                    <div style="
                        background: white;
                        padding: 20px;
                        border-radius: 8px;
                        max-width: 350px;
                        width: 90%;
                    ">
                        <div style="text-align: center; margin-bottom: 15px;">
                            <div style="font-size: 30px; color: #e74a3b; margin-bottom: 10px;">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                            <h5 style="margin: 0; color: #e74a3b;">Payment Failed</h5>
                        </div>

                        <div style="margin-bottom: 15px;">
                            <p style="color: #721c24; font-size: 14px; margin: 0 0 10px 0;">
                                ${errorMessage}
                            </p>
                            <div style="display: flex; justify-content: space-between;">
                                <span style="color: #666;">Teacher:</span>
                                <strong>${teacherName}</strong>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin-top: 5px;">
                                <span style="color: #666;">Amount:</span>
                                <strong>${utils.formatCurrency(amount)}</strong>
                            </div>
                        </div>

                        <button id="errorCloseBtn" style="
                            background: #e74a3b;
                            color: white;
                            border: none;
                            padding: 8px 20px;
                            border-radius: 4px;
                            cursor: pointer;
                            font-size: 14px;
                            width: 100%;
                        ">
                            Try Again
                        </button>
                    </div>
                `;

                document.body.appendChild(modal);

                document.getElementById('errorCloseBtn').addEventListener('click', function () {
                    modal.remove();
                    state.isProcessingPayment = false;
                    ui.setPayButtonLoading(false);
                });

                setTimeout(() => {
                    if (document.getElementById('paymentError')) {
                        modal.remove();
                        state.isProcessingPayment = false;
                        ui.setPayButtonLoading(false);
                    }
                }, 10000);
            }

            function sendEmailToTeacher(teacherId, monthYear) {
                const formattedMonthYear = utils.formatMonthYearForURL(monthYear);

                fetch(`/send-mail/${teacherId}/${formattedMonthYear}`, {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': utils.csrfToken,
                        'Accept': 'application/json'
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            console.log('Report email sent successfully:', data);
                        } else {
                            console.warn('Report email may not have been sent:', data);
                        }
                    })
                    .catch(error => {
                        console.error('Error sending report email:', error);
                    });
            }

            // Initialize application
            function init() {
                console.log('Initializing Teacher Income Details...');

                setupPayTeacherButton();
                setupPrintSlipButton();
                setupExportTableExcel();
                setupExportTablePdf();

                fetchTeacherData();
                ui.updateSelectedMonthYear();

                console.log('Teacher Income Details initialized successfully');
            }

            // Start application
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', init);
            } else {
                init();
            }

            // Global error handling
            window.addEventListener('error', function (event) {
                console.error('Global error:', event.error);
                utils.showToast('An unexpected error occurred', 'error');
            });

            window.addEventListener('unhandledrejection', function (event) {
                console.error('Unhandled promise rejection:', event.reason);
                utils.showToast('A network error occurred', 'error');
            });

        })();
    </script>
@endpush