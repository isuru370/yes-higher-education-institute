<!DOCTYPE html>
<html>

<head>
    <title>Payment Notification - {{ $month }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background-color: #4a6fa5;
            color: white;
            padding: 20px;
            border-radius: 5px 5px 0 0;
            text-align: center;
        }

        .content {
            padding: 20px;
            background-color: #f9f9f9;
            border-left: 1px solid #ddd;
            border-right: 1px solid #ddd;
        }

        .summary-box {
            background-color: #e8f4ff;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
            border-left: 4px solid #4a6fa5;
        }

        .footer {
            margin-top: 20px;
            padding: 20px;
            border-top: 1px solid #ddd;
            font-size: 12px;
            color: #666;
            background-color: #f5f5f5;
            border-radius: 0 0 5px 5px;
        }

        .highlight {
            color: #2c3e50;
            font-weight: bold;
        }

        .amount {
            font-size: 18px;
            color: #27ae60;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1 style="margin: 0; font-size: 24px;">Student Payment Report</h1>
            <p style="margin: 5px 0 0 0; opacity: 0.9;">{{ $month }}</p>
        </div>

        <div class="content">
            <p>Dear {{ $teacherName }},</p>

            <p>Your monthly payment report for <span class="highlight">{{ $month }}</span> is ready.</p>

            <div class="summary-box">
                <h3 style="margin-top: 0; color: #2c3e50;">Report Summary</h3>

                <p><strong>Teacher ID:</strong> {{ $teacherId }}</p>
                <p><strong>Total Classes:</strong> {{ $totalClasses }}</p>
                <p><strong>Total Students:</strong> {{ $totalStudents }}</p>
                <p><strong>Total Collected Amount:</strong>
                    <span class="amount">Rs. {{ number_format($totalAmount, 2) }}</span>
                </p>
                <p><strong>Your Amount ({{ $paymentData['teacher_percentage'] ?? 0 }}%):</strong>
                    <span class="amount">Rs. {{ number_format($teacherAmount, 2) }}</span>
                </p>
            </div>

            <p>The detailed report is attached to this email as a PDF document. The PDF includes:</p>
            <ul>
                <li>Class-wise student lists</li>
                <li>Payment status of each student</li>
                <li>Payment amounts and dates</li>
                <li>Free card student information</li>
                <li>Financial summary and calculations</li>
            </ul>

            <p>Please review the attached PDF for complete details.</p>

            <p style="margin-top: 20px;">
                <strong>Note:</strong> If you have any questions about this report, please contact the administration.
            </p>

            <p>Best regards,<br>
                <strong>YES EDUCATION</strong><br>
                Accounts Department
            </p>
        </div>

        <div class="footer">
            <p><small>This is an automated email. Please do not reply to this message.</small></p>
            <p><small>Generated on: {{ date('Y-m-d H:i:s') }}</small></p>
            <p><small>Report ID: PAY-{{ date('Ymd') }}-{{ $teacherId }}</small></p>
        </div>
    </div>
</body>

</html>