@extends('layouts.app')

@section('title', 'Payment Details')
@section('page-title', 'Payment Details')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('student-payment.create') }}">Add Payment</a></li>
    <li class="breadcrumb-item active">Payment Details</li>
@endsection

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="container-fluid">
        <!-- Student Information Card -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-user-graduate me-2"></i>Student Information
                </h5>
            </div>
            <div class="card-body">
                <div class="row" id="studentInfo">
                    <div class="col-md-12 text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Loading student information...</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Summary -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="mb-0" id="totalPaid">LKR 0</h4>
                                <p class="mb-0">Total Paid</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-check-circle fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="mb-0" id="totalPayments">0</h4>
                                <p class="mb-0">Total Payments</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-receipt fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="mb-0" id="activePayments">0</h4>
                                <p class="mb-0">Active Payments</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-clock fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h5 class="card-title mb-0">
                    <i class="fas fa-filter me-2"></i>Filter Payments
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="monthFilter" class="form-label">Month</label>
                        <select class="form-select" id="monthFilter">
                            <option value="all">All Months</option>
                            
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="statusFilter" class="form-label">Payment Status</label>
                        <select class="form-select" id="statusFilter">
                            <option value="all">All Status</option>
                            <option value="true">Active (True)</option>
                            <option value="false">Deleted (False)</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="searchFilter" class="form-label">Search</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="searchFilter" placeholder="Search payments...">
                            <button class="btn btn-outline-secondary" type="button" id="clearFilters">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button class="btn btn-primary w-100" id="refreshPayments">
                            <i class="fas fa-sync-alt me-1"></i>Refresh
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Monthly Payment View -->
        <div class="card">
            <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-calendar-alt me-2"></i>Payment History
                </h5>
                <div class="filter-info" id="filterInfo" style="display: none;">
                    <small>Showing filtered results</small>
                </div>
            </div>
            <div class="card-body">
                <div id="paymentsContainer">
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Loading payment history...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Payment Modal -->
    <div class="modal fade" id="editPaymentModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-edit me-2"></i>Edit Payment
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editPaymentForm">
                        <input type="hidden" id="editPaymentId">
                        <div class="mb-3">
                            <label for="editAmount" class="form-label">Payment Amount (LKR)</label>
                            <input type="number" class="form-control" id="editAmount" step="0.01" min="0" required>
                        </div>
                        <div class="mb-3">
                            <label for="editPaymentDate" class="form-label">Payment Date</label>
                            <input type="date" class="form-control" id="editPaymentDate" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="saveEditPayment">
                        <i class="fas fa-save me-1"></i>Save Changes
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Global variables
    let studentId = {{ $student_id }};
    let studentClassId = {{ $student_class_id }};
    let allPaymentData = [];
    let filteredPaymentData = [];

    document.addEventListener('DOMContentLoaded', function() {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // Set global csrfToken
        window.csrfToken = csrfToken;

        // Fetch initial data
        fetchStudentInfo(studentId);
        fetchPaymentData(studentId, studentClassId);

        // Add event listeners
        document.getElementById('monthFilter').addEventListener('change', applyFilters);
        document.getElementById('statusFilter').addEventListener('change', applyFilters);
        document.getElementById('searchFilter').addEventListener('input', applyFilters);
        document.getElementById('clearFilters').addEventListener('click', clearFilters);
        document.getElementById('refreshPayments').addEventListener('click', function() {
            fetchPaymentData(studentId, studentClassId);
        });

        // Edit payment modal event listener
        document.getElementById('saveEditPayment').addEventListener('click', function() {
            updatePayment();
        });
    });

    // Helper function to convert values to boolean
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

    function fetchStudentInfo(studentId) {
        fetch(`/api/student-classes/student/${studentId}/filter`)
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success' && data.data.length > 0) {
                    displayStudentInfo(data.data[0]);
                }
            })
            .catch(error => {
                console.error('Error fetching student info:', error);
                document.getElementById('studentInfo').innerHTML = `
                    <div class="col-md-12 text-center text-danger">
                        <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                        <p>Failed to load student information</p>
                    </div>
                `;
            });
    }

    function displayStudentInfo(studentData) {
        const student = studentData.student;
        const studentStatus = getBooleanValue(student.student_status);
        const statusText = studentStatus ? 'Active' : 'Inactive';
        const statusClass = studentStatus ? 'bg-success' : 'bg-danger';
        
        document.getElementById('studentInfo').innerHTML = `
            <div class="col-md-2 text-center">
                <img src="${student.img_url}" alt="Student Photo" 
                     class="img-thumbnail rounded-circle" style="width: 80px; height: 80px; object-fit: cover;"
                     onerror="this.src='/uploads/logo/logo.png'">
            </div>
            <div class="col-md-5">
                <h5>${student.last_name}</h5>
                <p class="mb-1"><strong>Student ID:</strong> ${student.student_custom_id}</p>
                <p class="mb-1"><strong>Guardian Mobile:</strong> ${student.guardian_mobile}</p>
                <p class="mb-1"><strong>Class:</strong> ${studentData.student_class.class_name}</p>
            </div>
            <div class="col-md-5">
                <p class="mb-1"><strong>Subject:</strong> ${studentData.student_class.subject.subject_name}</p>
                <p class="mb-1"><strong>Teacher:</strong> ${studentData.student_class.teacher.first_name}</p>
                <p class="mb-0"><strong>Status:</strong> 
                    <span class="badge ${statusClass}">
                        ${statusText}
                    </span>
                </p>
            </div>
        `;
    }

    function fetchPaymentData(studentId, studentClassId) {
        const paymentsContainer = document.getElementById('paymentsContainer');
        paymentsContainer.innerHTML = `
            <div class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2">Loading payment history...</p>
            </div>
        `;

        fetch(`/api/payments/${studentId}/${studentClassId}`)
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    // Process payment data to ensure proper boolean values and dates
                    allPaymentData = processPaymentData(data.data.monthly_view);
                    filteredPaymentData = [...allPaymentData];
                    displayPaymentSummary(data.data.summary);
                    displayMonthlyPayments(allPaymentData);
                    
                    // Populate month filter with unique months
                    populateMonthFilter(allPaymentData);
                } else {
                    throw new Error(data.message);
                }
            })
            .catch(error => {
                console.error('Error fetching payments:', error);
                paymentsContainer.innerHTML = `
                    <div class="text-center py-4 text-danger">
                        <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                        <p>Failed to load payment history</p>
                        <small class="text-muted">${error.message}</small>
                    </div>
                `;
            });
    }

    function processPaymentData(monthlyData) {
        return monthlyData.map(month => ({
            ...month,
            year_month: month.year_month,
            month: month.month,
            total_amount: month.total_amount,
            payment_count: month.payment_count,
            payments: month.payments.map(payment => ({
                ...payment,
                // 🔥 CRITICAL: Convert status to boolean
                status: getBooleanValue(payment.status),
                status_text: getBooleanValue(payment.status) ? 'Active' : 'Deleted',
                created_at: payment.created_at || payment.payment_date,
                display_date: formatDateTimeToSriLankan(payment.payment_date)
            }))
        }));
    }

    function formatDateTimeToSriLankan(dateString) {
        const datePart = formatDateToSriLankan(dateString);
        const timePart = formatTimeToSriLankan(dateString);
        
        return timePart ? `${datePart} ${timePart}` : datePart;
    }

    function displayPaymentSummary(summary) {
        document.getElementById('totalPaid').textContent = `LKR ${summary.total_paid.toLocaleString('en-LK')}`;
        document.getElementById('totalPayments').textContent = summary.total_payments;
        document.getElementById('activePayments').textContent = summary.active_payments || '0';
    }

    function populateMonthFilter(monthlyData) {
        const monthFilter = document.getElementById('monthFilter');
        let options = '<option value="all">All Months</option>';
        
        monthlyData.forEach(month => {
            options += `<option value="${month.year_month}">${month.month}</option>`;
        });
        
        monthFilter.innerHTML = options;
    }

    function applyFilters() {
        const monthFilter = document.getElementById('monthFilter').value;
        const statusFilter = document.getElementById('statusFilter').value;
        const searchFilter = document.getElementById('searchFilter').value.toLowerCase();

        filteredPaymentData = allPaymentData.filter(month => {
            if (monthFilter !== 'all' && month.year_month !== monthFilter) {
                return false;
            }

            const filteredPayments = month.payments.filter(payment => {
                // 🔥 FIXED: Handle status as boolean comparison
                if (statusFilter !== 'all') {
                    const filterBoolean = statusFilter === 'true';
                    if (payment.status !== filterBoolean) {
                        return false;
                    }
                }

                if (searchFilter && !payment.payment_for.toLowerCase().includes(searchFilter)) {
                    return false;
                }

                return true;
            });

            month.filteredPayments = filteredPayments;
            return filteredPayments.length > 0;
        });

        displayMonthlyPayments(filteredPaymentData);
        updateFilterInfo();
    }

    function clearFilters() {
        document.getElementById('monthFilter').value = 'all';
        document.getElementById('statusFilter').value = 'all';
        document.getElementById('searchFilter').value = '';
        
        filteredPaymentData = [...allPaymentData];
        displayMonthlyPayments(allPaymentData);
        document.getElementById('filterInfo').style.display = 'none';
    }

    function updateFilterInfo() {
        const filterInfo = document.getElementById('filterInfo');
        const monthFilter = document.getElementById('monthFilter').value;
        const statusFilter = document.getElementById('statusFilter').value;
        const searchFilter = document.getElementById('searchFilter').value;

        const activeFilters = [];
        
        if (monthFilter !== 'all') {
            const monthName = document.getElementById('monthFilter').options[document.getElementById('monthFilter').selectedIndex].text;
            activeFilters.push(`Month: ${monthName}`);
        }
        
        if (statusFilter !== 'all') {
            const statusName = document.getElementById('statusFilter').options[document.getElementById('statusFilter').selectedIndex].text;
            activeFilters.push(`Status: ${statusName}`);
        }
        
        if (searchFilter) {
            activeFilters.push(`Search: "${searchFilter}"`);
        }

        if (activeFilters.length > 0) {
            filterInfo.innerHTML = `<small>${activeFilters.join(' • ')}</small>`;
            filterInfo.style.display = 'block';
        } else {
            filterInfo.style.display = 'none';
        }
    }

    function displayMonthlyPayments(monthlyData) {
        const paymentsContainer = document.getElementById('paymentsContainer');
        
        if (monthlyData.length === 0) {
            paymentsContainer.innerHTML = `
                <div class="text-center py-4">
                    <i class="fas fa-receipt fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No Payments Found</h5>
                    <p class="text-muted">No payments match your filter criteria.</p>
                    <button class="btn btn-primary mt-2" onclick="clearFilters()">
                        <i class="fas fa-times me-1"></i>Clear Filters
                    </button>
                </div>
            `;
            return;
        }

        let html = '';
        
        monthlyData.forEach(month => {
            const paymentsToShow = month.filteredPayments || month.payments;
            
            html += `
                <div class="month-section mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-3 p-3 bg-light rounded">
                        <h5 class="mb-0">
                            <i class="fas fa-calendar me-2"></i>${month.month}
                        </h5>
                        <div class="text-end">
                            <span class="badge bg-primary fs-6">
                                LKR ${month.total_amount.toLocaleString('en-LK')}
                            </span>
                            <small class="text-muted d-block">${month.payment_count} payment(s)</small>
                        </div>
                    </div>
                    
                    <div class="row g-3">
            `;

            paymentsToShow.forEach(payment => {
                // 🔥 FIXED: Use boolean status directly
                const statusClass = payment.status === true ? 'bg-success' : 'bg-danger';
                const statusText = payment.status === true ? 'Active' : 'Deleted';
                
                const canEditDelete = payment.can_edit_delete !== undefined 
                    ? payment.can_edit_delete 
                    : isPaymentWithin7Days(payment.created_at);
                
                // 🔥 FIXED: Only show edit/delete for active payments
                const actionButtons = payment.status === true 
                    ? canEditDelete
                        ? `
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-primary btn-edit-payment" 
                                        data-payment-id="${payment.id}"
                                        data-amount="${payment.amount}"
                                        data-payment-date="${payment.payment_date}">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <button class="btn btn-outline-danger btn-delete-payment" 
                                        data-payment-id="${payment.id}">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </div>
                        `
                        : `
                            <small class="text-muted">Actions expired</small>
                        `
                    : `
                        <span class="badge bg-secondary">${statusText}</span>
                    `;

                const timeIndicator = canEditDelete && payment.status === true
                    ? '<span class="badge bg-info me-1"><i class="fas fa-clock me-1"></i>Editable</span>'
                    : payment.status === true 
                        ? '<span class="badge bg-secondary me-1"><i class="fas fa-lock me-1"></i>Locked</span>'
                        : '';

                html += `
                    <div class="col-md-6 col-lg-4">
                        <div class="card payment-card h-100 ${payment.status === false ? 'opacity-75' : ''}">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h6 class="card-title mb-0">${payment.payment_for}</h6>
                                    <div class="text-end">
                                        ${timeIndicator}
                                        <span class="badge ${statusClass}">${statusText}</span>
                                    </div>
                                </div>
                                <p class="card-text mb-1">
                                    <i class="fas fa-calendar me-2 text-muted"></i>
                                    ${payment.display_date}
                                </p>
                                <p class="card-text mb-2">
                                    <i class="fas fa-money-bill me-2 text-muted"></i>
                                    <strong class="text-success">LKR ${payment.amount.toLocaleString('en-LK')}</strong>
                                </p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">ID: ${payment.id}</small>
                                    ${actionButtons}
                                </div>
                                ${!canEditDelete && payment.status === true ? `
                                    <div class="mt-2">
                                        <small class="text-muted">
                                            <i class="fas fa-info-circle me-1"></i>
                                            Edit/Delete period expired
                                        </small>
                                    </div>
                                ` : ''}
                            </div>
                        </div>
                    </div>
                `;
            });

            html += `
                    </div>
                </div>
            `;
        });

        paymentsContainer.innerHTML = html;
        addPaymentEventListeners();
    }

    function isPaymentWithin7Days(paymentDate) {
        if (!paymentDate) return false;
        
        const paymentDateTime = new Date(paymentDate);
        const currentTime = new Date();
        const timeDifference = currentTime - paymentDateTime;
        const daysDifference = timeDifference / (1000 * 60 * 60 * 24);
        
        return daysDifference <= 7;
    }

    function getDaysRemaining(paymentDate) {
        if (!paymentDate) return 0;
        
        const paymentDateTime = new Date(paymentDate);
        const currentTime = new Date();
        const timeDifference = currentTime - paymentDateTime;
        const daysDifference = timeDifference / (1000 * 60 * 60 * 24);
        const daysRemaining = Math.max(0, 7 - Math.floor(daysDifference));
        
        return daysRemaining;
    }

    function addPaymentEventListeners() {
        // Edit payment buttons
        document.querySelectorAll('.btn-edit-payment').forEach(btn => {
            btn.addEventListener('click', function() {
                const paymentId = this.getAttribute('data-payment-id');
                const currentAmount = this.getAttribute('data-amount');
                const paymentDate = this.getAttribute('data-payment-date');
                openEditPaymentModal(paymentId, currentAmount, paymentDate);
            });
        });

        // Delete payment buttons
        document.querySelectorAll('.btn-delete-payment').forEach(btn => {
            btn.addEventListener('click', function() {
                const paymentId = this.getAttribute('data-payment-id');
                deletePayment(paymentId);
            });
        });
    }

    function openEditPaymentModal(paymentId, currentAmount, paymentDate) {
        const daysRemaining = getDaysRemaining(paymentDate);
        
        document.getElementById('editPaymentId').value = paymentId;
        document.getElementById('editAmount').value = currentAmount;
        document.getElementById('editPaymentDate').value = paymentDate;
        
        const modalTitle = document.querySelector('#editPaymentModal .modal-title');
        modalTitle.innerHTML = `<i class="fas fa-edit me-2"></i>Edit Payment <span class="badge bg-info">${daysRemaining} days left</span>`;
        
        const modal = new bootstrap.Modal(document.getElementById('editPaymentModal'));
        modal.show();
    }

    function updatePayment() {
        const paymentId = document.getElementById('editPaymentId').value;
        const newAmount = document.getElementById('editAmount').value;
        const paymentDate = document.getElementById('editPaymentDate').value;
        const btn = document.getElementById('saveEditPayment');

        if (!newAmount || newAmount <= 0) {
            alert('Please enter a valid amount');
            return;
        }

        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Saving...';

        const updateData = {
            amount: parseFloat(newAmount),
            payment_date: paymentDate
        };

        fetch(`/api/payments/${paymentId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.csrfToken,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(updateData)
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.status === 'success') {
                showAlert('Payment updated successfully!', 'success');
                const modal = bootstrap.Modal.getInstance(document.getElementById('editPaymentModal'));
                modal.hide();
                fetchPaymentData(studentId, studentClassId);
            } else {
                throw new Error(data.message || 'Update failed');
            }
        })
        .catch(error => {
            console.error('Update error:', error);
            showAlert('Update error: ' + error.message, 'danger');
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-save me-1"></i>Save Changes';
        });
    }

    function deletePayment(paymentId) {
        if (!confirm('Are you sure you want to delete this payment? This action cannot be undone.')) {
            return;
        }

        fetch(`/api/payments/${paymentId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.csrfToken,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.status === 'success') {
                showAlert('Payment deleted successfully!', 'success');
                fetchPaymentData(studentId, studentClassId);
            } else {
                throw new Error(data.message || 'Delete failed');
            }
        })
        .catch(error => {
            console.error('Delete error:', error);
            showAlert('Delete error: ' + error.message, 'danger');
        });
    }

    function showAlert(message, type) {
        document.querySelectorAll('.alert-dismissible').forEach(alert => alert.remove());

        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;

        document.querySelector('.container-fluid').insertBefore(alertDiv, document.querySelector('.container-fluid').firstChild);

        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.remove();
            }
        }, 5000);
    }
</script>

<style>
    .payment-card {
        border: 1px solid #e9ecef;
        border-radius: 8px;
        transition: all 0.3s ease;
    }

    .payment-card:hover {
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        transform: translateY(-2px);
    }

    .month-section {
        border-bottom: 2px solid #f8f9fa;
        padding-bottom: 20px;
    }

    .month-section:last-child {
        border-bottom: none;
    }

    .card.bg-success, .card.bg-info, .card.bg-warning {
        border: none;
        border-radius: 10px;
    }

    .icon {
        opacity: 0.8;
    }

    .filter-info {
        background: rgba(255,255,255,0.2);
        padding: 5px 10px;
        border-radius: 5px;
    }

    .btn-group-sm .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }

    .payment-card .badge.bg-info {
        font-size: 0.7rem;
    }

    .payment-card .badge.bg-secondary {
        font-size: 0.7rem;
    }
</style>
@endpush