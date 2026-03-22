<!DOCTYPE html>
<html lang="si">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <title>@yield('title', 'Student Management System')</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Enhanced Custom CSS -->
    <style>
        :root {
            --primary: #3498db;
            --primary-dark: #2980b9;
            --secondary: #2c3e50;
            --secondary-light: #34495e;
            --accent: #1abc9c;
            --light: #ecf0f1;
            --dark: #2c3e50;
            --success: #2ecc71;
            --warning: #f39c12;
            --danger: #e74c3c;
            --gray: #95a5a6;
            --sidebar-width: 280px;
            --sidebar-collapsed: 70px;
            --navbar-height: 76px;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            overflow-x: hidden;
        }

        /* Enhanced Sidebar */
        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            background: linear-gradient(180deg, var(--secondary) 0%, var(--secondary-light) 100%);
            color: white;
            transition: all 0.3s ease;
            z-index: 1030;
            box-shadow: 3px 0 15px rgba(0, 0, 0, 0.15);
        }

        .sidebar.collapsed {
            width: var(--sidebar-collapsed);
        }

        .sidebar-header {
            padding: 20px;
            background: rgba(0, 0, 0, 0.2);
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            position: relative;
        }

        .sidebar-logo {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 10px;
        }

        .logo-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, var(--accent), var(--primary));
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .sidebar-menu {
            padding: 20px 0;
            height: calc(100vh - 80px);
            overflow-y: auto;
        }

        .sidebar-menu::-webkit-scrollbar {
            width: 5px;
        }

        .sidebar-menu::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 10px;
        }

        .sidebar .nav-link {
            color: var(--light);
            padding: 12px 20px;
            border-left: 4px solid transparent;
            transition: all 0.2s ease;
            margin: 2px 10px;
            border-radius: 8px;
            position: relative;
        }

        .sidebar .nav-link:hover {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            border-left-color: var(--accent);
            transform: translateX(5px);
        }

        .sidebar .nav-link.active {
            background: rgba(52, 152, 219, 0.2);
            color: white;
            border-left-color: var(--primary);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .sidebar .nav-link i {
            width: 20px;
            margin-right: 10px;
            text-align: center;
            font-size: 1.1rem;
        }

        .sidebar.collapsed .nav-link span,
        .sidebar.collapsed .sidebar-header span {
            display: none;
        }

        .sidebar.collapsed .nav-link {
            text-align: center;
            padding: 12px 5px;
            margin: 2px 5px;
        }

        .sidebar.collapsed .nav-link i {
            margin-right: 0;
            font-size: 1.3rem;
        }

        /* Bootstrap 5 dropdown fix */
        .sidebar .dropdown-toggle::after {
            float: right;
            margin-top: 8px;
        }

        .sidebar.collapsed .dropdown-toggle::after {
            display: none;
        }

        .sidebar .dropdown-menu {
            background: rgba(44, 62, 80, 0.95);
            border: none;
            border-radius: 8px;
            margin: 5px 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .sidebar .dropdown-item {
            color: var(--light);
            padding: 10px 15px;
            border-radius: 5px;
            margin: 2px 5px;
            width: auto;
            transition: all 0.2s ease;
        }

        .sidebar .dropdown-item:hover {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            transform: translateX(5px);
        }

        /* Enhanced Navbar */
        .navbar-custom {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            margin-left: var(--sidebar-width);
            transition: all 0.3s ease;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
            height: var(--navbar-height);
            z-index: 1020;
        }

        .navbar-custom.collapsed {
            margin-left: var(--sidebar-collapsed);
        }

        /* Enhanced Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            margin-top: var(--navbar-height);
            padding: 25px;
            transition: all 0.3s ease;
            min-height: calc(100vh - var(--navbar-height));
            background-color: #f8f9fa;
        }

        .main-content.collapsed {
            margin-left: var(--sidebar-collapsed);
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .navbar-custom,
            .main-content {
                margin-left: 0 !important;
            }
        }

        /* Enhanced Bootstrap 5 compatibility */
        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            border: none;
            border-radius: 8px;
            padding: 10px 20px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, var(--primary-dark), var(--primary));
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(52, 152, 219, 0.3);
        }

        /* Bootstrap 5 form enhancements */
        .form-control:focus,
        .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
        }

        /* Modal enhancements for Bootstrap 5 */
        .modal-content {
            border: none;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        /* Ensure proper z-index stacking */
        .modal {
            z-index: 1060 !important;
        }

        .modal-backdrop {
            z-index: 1050 !important;
        }

        /* Enhanced Breadcrumb */
        .breadcrumb {
            background-color: white;
            border-radius: 10px;
            padding: 12px 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        /* Enhanced Cards */
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            margin-bottom: 20px;
            overflow: hidden;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            border-bottom: none;
            padding: 15px 20px;
            font-weight: 600;
        }

        /* Enhanced Tables */
        .table thead th {
            background: var(--primary);
            color: white;
            border: none;
            padding: 15px;
            font-weight: 600;
        }

        .table tbody tr {
            transition: all 0.2s ease;
        }

        .table tbody tr:hover {
            background-color: rgba(52, 152, 219, 0.05);
            transform: translateY(-1px);
        }

        /* Enhanced Footer */
        .app-footer {
            background: white;
            padding: 15px 25px;
            margin-left: var(--sidebar-width);
            transition: all 0.3s ease;
            border-top: 1px solid rgba(0, 0, 0, 0.05);
            color: var(--gray);
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.05);
        }

        .app-footer.collapsed {
            margin-left: var(--sidebar-collapsed);
        }

        @media (max-width: 768px) {
            .app-footer {
                margin-left: 0 !important;
            }
        }

        /* Enhanced User Avatar */
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .user-avatar:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: scale(1.05);
        }

        /* Enhanced Dropdown Menu */
        .dropdown-menu {
            border: none;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            padding: 10px;
        }

        .dropdown-item {
            border-radius: 5px;
            transition: all 0.2s ease;
        }

        .dropdown-item:hover {
            background-color: rgba(52, 152, 219, 0.1);
        }

        /* Enhanced Toggle Buttons */
        #sidebarToggleMobile,
        #sidebarToggleDesktop {
            border: none;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        #sidebarToggleMobile:hover,
        #sidebarToggleDesktop:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        /* Subtle animations */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .main-content>* {
            animation: fadeIn 0.5s ease;
        }
    </style>
</head>

<body>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="sidebar-logo">
                <div class="logo-icon">
                    <i class="fas fa-graduation-cap text-white"></i>
                </div>
            </div>
            <h5 class="mb-0">
                <span>Student Management</span>
            </h5>
        </div>

        <div class="sidebar-menu">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                        href="{{ route('dashboard') }}">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#attendanceSubmenu" data-bs-toggle="collapse"
                        role="button" aria-expanded="false" aria-controls="attendanceSubmenu">
                        <i class="fas fa-chalkboard-teacher"></i> <span>Manage Attendance</span>
                    </a>
                    <div class="collapse {{ request()->routeIs('student_attendance.*') ? 'show' : '' }}"
                        id="attendanceSubmenu">
                        <ul class="nav flex-column ms-3">
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('student_attendance.index') ? 'active' : '' }}"
                                    href="{{ route('student_attendance.index') }}">
                                    <i class="fas fa-qrcode me-2"></i> Mark Attendance
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('student_attendance.daily') ? 'active' : '' }}"
                                    href="{{ route('student_attendance.daily') }}">
                                    <i class="fas fa-calendar-day me-2"></i> Daily
                                </a>
                            </li>
                            <!-- Additional menu items can be added here -->
                        </ul>
                    </div>
                </li>
                <!-- System Users -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#systemUsersSubmenu" data-bs-toggle="collapse"
                        role="button" aria-expanded="false" aria-controls="systemUsersSubmenu">
                        <i class="fas fa-user-cog"></i> <span>Manage Sys.Users</span>
                    </a>
                    <div class="collapse {{ request()->routeIs('system-users.*') || request()->routeIs('user-types.*') ? 'show' : '' }}"
                        id="systemUsersSubmenu">
                        <ul class="nav flex-column ms-3">
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('system-users.create') ? 'active' : '' }}"
                                    href="{{ route('system-users.create') }}">
                                    <i class="fas fa-user-plus"></i> Add New User
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('system-users.index') ? 'active' : '' }}"
                                    href="{{ route('system-users.index') }}">
                                    <i class="fas fa-users-cog"></i> View All Users
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('user-types.create') ? 'active' : '' }}"
                                    href="{{ route('user-types.create') }}">
                                    <i class="fas fa-users-cog"></i> Add User Type
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('user-types.index') ? 'active' : '' }}"
                                    href="{{ route('user-types.index') }}">
                                    <i class="fas fa-users-cog"></i> View User Type
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <!-- Students -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#studentsSubmenu" data-bs-toggle="collapse" role="button"
                        aria-expanded="false" aria-controls="studentsSubmenu">
                        <i class="fas fa-users"></i> <span>Manage Students</span>
                    </a>
                    <div class="collapse {{ request()->routeIs('students.*') ? 'show' : '' }}" id="studentsSubmenu">
                        <ul class="nav flex-column ms-3">
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('students.create') ? 'active' : '' }}"
                                    href="{{ route('students.create') }}">
                                    <i class="fas fa-plus-circle"></i> Add New Student
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('students.index') ? 'active' : '' }}"
                                    href="{{ route('students.index') }}">
                                    <i class="fas fa-list"></i> View All Students
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('students.images') ? 'active' : '' }}"
                                    href="{{ route('students.images') }}">
                                    <i class="fas fa-image"></i> Student Images
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('students.studentImages') ? 'active' : '' }}"
                                    href="{{ route('students.studentImages') }}">
                                    <i class="fas fa-camera"></i> Quick Images
                                </a>
                            </li>
                            {{-- <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('student-id-card.*') ? 'active' : '' }}"
                                    href="{{ route('student-id-card.ganarateStudentId') }}">
                                    <i class="fa fa-id-card"></i> Generate Student ID
                                </a>
                            </li> --}}
                        </ul>

                    </div>
                </li>

                <!--Exams-->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#examsSubmenu" data-bs-toggle="collapse" role="button"
                        aria-expanded="false" aria-controls="examsSubmenu">
                        <i class="fas fa-users"></i> <span>Manage Exams</span>
                    </a>
                    <div class="collapse {{ request()->routeIs('student_exam.*') ? 'show' : '' }}" id="examsSubmenu">
                        <ul class="nav flex-column ms-3">
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('student_exam.create') ? 'active' : '' }}"
                                    href="{{ route('student_exam.create') }}">
                                    <i class="fas fa-plus-circle"></i> Add New Exam
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('student_exam.index') ? 'active' : '' }}"
                                    href="{{ route('student_exam.index') }}">
                                    <i class="fas fa-list"></i> View All Exams
                                </a>
                            </li>
                        </ul>

                    </div>
                </li>

                <!-- Teachers -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#teachersSubmenu" data-bs-toggle="collapse" role="button"
                        aria-expanded="false" aria-controls="teachersSubmenu">
                        <i class="fas fa-chalkboard-teacher"></i> <span>Manage Teachers</span>
                    </a>
                    <div class="collapse {{ request()->routeIs('teachers.*') ? 'show' : '' }}" id="teachersSubmenu">
                        <ul class="nav flex-column ms-3">
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('teachers.create') ? 'active' : '' }}"
                                    href="{{ route('teachers.create') }}">
                                    <i class="fas fa-plus-circle"></i> Add New Teacher
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('teachers.index') ? 'active' : '' }}"
                                    href="{{ route('teachers.index') }}">
                                    <i class="fas fa-list"></i> View All Teachers
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <!-- Manage Class Room -->
                <li class="nav-item">
                    <a class="nav-link dropdown-toggle" href="#classRoomsSubmenu" data-bs-toggle="collapse"
                        aria-controls="classRoomsSubmenu">
                        <i class="fas fa-chalkboard"></i>
                        <span>Manage Class Rooms</span>
                    </a>
                    <div class="collapse {{ request()->routeIs('class_rooms.*') ? 'show' : '' }}"
                        id="classRoomsSubmenu">
                        <ul class="nav flex-column ms-3">
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('class_rooms.create') ? 'active' : '' }}"
                                    href="{{ route('class_rooms.create') }}">
                                    <i class="fas fa-plus-circle"></i> Add New Class Room
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('class_rooms.index') ? 'active' : '' }}"
                                    href="{{ route('class_rooms.index') }}">
                                    <i class="fas fa-list"></i> View All Class Rooms
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('class_rooms.schedule') ? 'active' : '' }}"
                                    href="{{ route('class_rooms.schedule') }}">
                                    <i class="fas fa-clock"></i> Class Schedule
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('class_halls.index') ? 'active' : '' }}"
                                    href="{{ route('class_halls.index') }}">
                                    <i class="fas fa-clock"></i> Class Halls
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#financialMenu" data-bs-toggle="collapse" role="button"
                        aria-expanded="false" aria-controls="financialMenu">
                        <i class="fas fa-money-bill"></i> <span>Manage Financial</span>
                    </a>

                    <div class="collapse {{ request()->routeIs('admissions.*') ? 'show' : '' }}" id="financialMenu">
                        <ul class="nav flex-column ms-3">

                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('payment_reason.index') ? 'active' : '' }}"
                                    href="{{ route('payment_reason.index') }}">
                                    <i class="fas fa-plus-circle"></i> Payment Reson
                                </a>
                            </li>
                            {{-- Submenu: Admissions --}}
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#admissionSubmenu" data-bs-toggle="collapse"
                                    role="button" aria-expanded="false" aria-controls="admissionSubmenu">
                                    <i class="fas fa-book"></i> Admissions
                                </a>

                                <div class="collapse {{ request()->routeIs('admissions.*') ? 'show' : '' }}"
                                    id="admissionSubmenu">
                                    <ul class="nav flex-column ms-3">
                                        <li class="nav-item">
                                            <a class="nav-link {{ request()->routeIs('admissions.index') ? 'active' : '' }}"
                                                href="{{ route('admissions.index') }}">
                                                <i class="fas fa-plus-circle"></i> Admissions
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link {{ request()->routeIs('pay-admissions.admission_payment') ? 'active' : '' }}"
                                                href="{{ route('pay-admissions.admission_payment') }}">
                                                <i class="fas fa-list"></i> Pay Admissions
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                            {{-- Submenu: Student Payment --}}
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#paymentSubmenu" data-bs-toggle="collapse"
                                    role="button" aria-expanded="false" aria-controls="paymentSubmenu">
                                    <i class="fas fa-book"></i> Student Payment
                                </a>

                                <div class="collapse {{ request()->routeIs('student-payment.*') ? 'show' : '' }}"
                                    id="paymentSubmenu">
                                    <ul class="nav flex-column ms-3">
                                        <li class="nav-item">
                                            <a class="nav-link {{ request()->routeIs('student-payment.index') ? 'active' : '' }}"
                                                href="{{ route('student-payment.index') }}">
                                                <i class="fas fa-plus-circle"></i> View Class Fee
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link {{ request()->routeIs('student-payment.create') ? 'active' : '' }}"
                                                href="{{ route('student-payment.create') }}">
                                                <i class="fas fa-list"></i> Pay Class Fee
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </li>

                            {{-- Submenu: Teacher Payment --}}
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#teachersSubmenu" data-bs-toggle="collapse"
                                    role="button" aria-expanded="false" aria-controls="teachersSubmenu">
                                    <i class="fas fa-book"></i> Teachers Payment
                                </a>

                                <div class="collapse {{ request()->routeIs('teacher_payment.*') ? 'show' : '' }}"
                                    id="teachersSubmenu">
                                    <ul class="nav flex-column ms-3">
                                        <li class="nav-item">
                                            <a class="nav-link {{ request()->routeIs('teacher_payment.index') ? 'active' : '' }}"
                                                href="{{ route('teacher_payment.index') }}">
                                                <i class="fas fa-plus-circle"></i> Teacher Income
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link {{ request()->routeIs('teacher_payment.expenses') ? 'active' : '' }}"
                                                href="{{ route('teacher_payment.expenses') }}">
                                                <i class="fas fa-plus-circle"></i> Teacher Advance
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link {{ request()->routeIs('teacher_ledger_summary.index') ? 'active' : '' }}"
                                                href="{{ route('teacher_ledger_summary.index') }}">
                                                <i class="fas fa-plus-circle"></i> Ledger
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                            {{-- Submenu: Institute Payment --}}
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#instituteSubmenu" data-bs-toggle="collapse"
                                    role="button" aria-expanded="false" aria-controls="instituteSubmenu">
                                    <i class="fas fa-book"></i> Institute Payment
                                </a>

                                <div class="collapse {{ request()->routeIs('institute_payment.*') ? 'show' : '' }}"
                                    id="instituteSubmenu">
                                    <ul class="nav flex-column ms-3">
                                        <li class="nav-item">
                                            <a class="nav-link {{ request()->routeIs('institute_payment.index') ? 'active' : '' }}"
                                                href="{{ route('institute_payment.index') }}">
                                                <i class="fas fa-plus-circle"></i> Income
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link {{ request()->routeIs('institute_payment.ledger') ? 'active' : '' }}"
                                                href="{{ route('institute_payment.ledger') }}">
                                                <i class="fas fa-plus-circle"></i> ledger
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </li>


                        </ul>
                    </div>
                </li>

                <!-- Other menu items -->
                {{-- <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('classes') ? 'active' : '' }}"
                        href="{{ route('classes') }}">
                        <i class="fas fa-book"></i> <span>Classes</span>
                    </a>
                </li> --}}
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}"
                        href="{{ route('reports.index') }}">
                        <i class="fas fa-chart-bar"></i> <span>Reports</span>
                    </a>
                </li> 
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}"
                        href="{{ route('settings.index') }}">
                        <i class="fas fa-cog"></i>
                        <span>Settings</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom fixed-top" id="navbar">
        <div class="container-fluid">
            <div class="d-flex align-items-center">
                <button class="btn btn-light me-3 d-md-none" id="sidebarToggleMobile" type="button">
                    <i class="fas fa-bars"></i>
                </button>
                <button class="btn btn-light me-3 d-none d-md-block" id="sidebarToggleDesktop" type="button">
                    <i class="fas fa-bars"></i>
                </button>
                <span class="navbar-brand mb-0 h1">@yield('page-title', 'Dashboard')</span>
            </div>

            <div class="navbar-nav ms-auto">
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="me-2 text-end d-none d-md-block">
                            <div class="fw-semibold">Admin User</div>
                            <small class="text-light opacity-75">Administrator</small>
                        </div>
                        <div class="user-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i>Profile</a></li>
                        <li><a class="dropdown-item" href="{{ route('settings.index') }}"><i
                                    class="fas fa-cog me-2"></i>Settings</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <a class="dropdown-item text-danger" href="{{ route('logout') }}"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fas fa-sign-out-alt me-2"></i>Logout
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="main-content" id="mainContent">
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('dashboard') }}">
                        <i class="fas fa-home me-1"></i>Home
                    </a>
                </li>
                @yield('breadcrumb')
            </ol>
        </nav>

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="app-footer" id="appFooter">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <small>&copy; 2026 Nexora IT Software Soluation. All rights reserved.</small>
                </div>
                <div class="col-md-6 text-md-end">
                    <small>Version 2.2.5</small>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 JS Bundle (includes Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Enhanced Dashboard JS -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const sidebar = document.getElementById('sidebar');
            const navbar = document.getElementById('navbar');
            const mainContent = document.getElementById('mainContent');
            const appFooter = document.getElementById('appFooter');
            const sidebarToggleMobile = document.getElementById('sidebarToggleMobile');
            const sidebarToggleDesktop = document.getElementById('sidebarToggleDesktop');

            // Mobile toggle
            if (sidebarToggleMobile) {
                sidebarToggleMobile.addEventListener('click', function () {
                    sidebar.classList.toggle('show');
                });
            }

            // Desktop toggle
            if (sidebarToggleDesktop) {
                sidebarToggleDesktop.addEventListener('click', function () {
                    sidebar.classList.toggle('collapsed');
                    navbar.classList.toggle('collapsed');
                    mainContent.classList.toggle('collapsed');
                    appFooter.classList.toggle('collapsed');

                    // Save state
                    const isCollapsed = sidebar.classList.contains('collapsed');
                    localStorage.setItem('sidebarCollapsed', isCollapsed);
                });
            }

            // Restore sidebar state
            const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
            if (isCollapsed && window.innerWidth > 768) {
                sidebar.classList.add('collapsed');
                navbar.classList.add('collapsed');
                mainContent.classList.add('collapsed');
                appFooter.classList.add('collapsed');
            }

            // Handle resize
            window.addEventListener('resize', function () {
                if (window.innerWidth > 768) {
                    sidebar.classList.remove('show');
                }
            });

            // Bootstrap 5 modal cleanup
            document.addEventListener('hidden.bs.modal', function (event) {
                // Reset form if needed when modal closes
                const form = event.target.querySelector('form');
                if (form) {
                    form.reset();
                }
            });
        });

        // Global modal functions for Bootstrap 5 compatibility
        function closeEditModal() {
            const modal = bootstrap.Modal.getInstance(document.getElementById('editTypeModal'));
            if (modal) {
                modal.hide();
            }
        }

        function emergencyModalClose() {
            const modals = document.querySelectorAll('.modal.show');
            modals.forEach(modal => {
                const modalInstance = bootstrap.Modal.getInstance(modal);
                if (modalInstance) {
                    modalInstance.hide();
                }
            });
        }

        // Simple modal fix function
        function fixModal() {
            const modals = document.querySelectorAll('.modal');
            const backdrops = document.querySelectorAll('.modal-backdrop');

            modals.forEach(modal => {
                modal.style.display = 'none';
            });

            backdrops.forEach(backdrop => {
                backdrop.remove();
            });

            document.body.classList.remove('modal-open');
            document.body.style.overflow = 'auto';
            document.body.style.paddingRight = '';
        }

        // Global function for emergency modal close
        window.closeAllModals = function () {
            fixModal();
        };
    </script>

    <!-- Page specific scripts -->
    @stack('scripts')
</body>

</html>