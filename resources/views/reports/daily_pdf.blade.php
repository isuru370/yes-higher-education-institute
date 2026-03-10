<!DOCTYPE html>
<html>
<head>
    <title>Daily Payment Report</title>
    <style>
        /* A4 Page Setup */
        @page {
            size: A4;
            margin: 15mm;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 0;
            max-width: 210mm;
            max-height: 297mm;
        }
        
        /* Header Section */
        .letterhead {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: black;
            padding: 12px 20px;
            border-radius: 6px;
            margin-bottom: 18px;
            text-align: center;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
        }
        
        .letterhead h2 {
            margin: 0;
            font-size: 22px;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        
        .letterhead h4 {
            margin: 6px 0 0 0;
            font-size: 14px;
            font-weight: 400;
            opacity: 0.9;
        }
        
        .report-info {
            display: flex;
            justify-content: space-between;
            background: #f8f9fa;
            padding: 10px 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            border-left: 4px solid #2a5298;
        }
        
        .report-info p {
            margin: 0;
            font-weight: 500;
        }
        
        /* Table Design */
        .summary-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin: 0 0 25px 0;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }
        
        .summary-table th {
            background: #2a5298;
            color: white;
            font-weight: 600;
            padding: 12px 10px;
            text-align: left;
            border: none;
        }
        
        .summary-table td {
            padding: 10px;
            border: none;
            border-bottom: 1px solid #eaeaea;
        }
        
        .summary-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        .summary-table tr:hover {
            background-color: #f0f5ff;
        }
        
        .section-header {
            background: #e8efff !important;
            font-weight: 600;
            color: #1e3c72;
        }
        
        .total-row {
            background: #f0f7ff !important;
            font-weight: 600;
            border-top: 2px solid #2a5298 !important;
        }
        
        .balance-row {
            background: #e8f7f0 !important;
            font-weight: 700;
            font-size: 12px;
            color: #1a5d1a;
        }
        
        .amount {
            text-align: right;
            font-family: 'Courier New', monospace;
            font-weight: 500;
        }
        
        /* Footer Section */
        .footer {
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px dashed #ccc;
        }
        
        .printed-date {
            font-size: 10px;
            color: #666;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .form-section {
            margin-bottom: 15px;
        }
        
        .form-section strong {
            display: block;
            margin-bottom: 5px;
            color: #2a5298;
        }
        
        .dotted-line {
            border-bottom: 1px dashed #999;
            padding-bottom: 4px;
            margin-bottom: 8px;
            min-height: 20px;
        }
        
        .signature-area {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
        }
        
        .signature-box {
            width: 48%;
        }
        
        .signature-line {
            border-top: 1px solid #333;
            margin-top: 40px;
            width: 80%;
        }
        
        /* Ensure single page */
        .page-container {
            height: 267mm; /* 297mm - 2*15mm margins */
            display: flex;
            flex-direction: column;
        }
        
        .content {
            flex: 1;
        }
        
        /* Responsive adjustments */
        @media print {
            body {
                font-size: 10px;
            }
            
            .letterhead {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .summary-table th {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .no-break {
                page-break-inside: avoid;
            }
        }
    </style>
</head>

<body>
    <div class="page-container">
        <!-- HEADER -->
        <div class="letterhead">
            <h2>Yes Education</h2>
            <h4>Daily Payment Report</h4>
        </div>
        
        <div class="report-info">
            <p><strong>Report Date:</strong> {{ $report['date'] }}</p>
            <p><strong>Report ID:</strong> DPR-{{ date('Ymd', strtotime($report['date'])) }}</p>
        </div>
        
        <!-- REPORT TABLE -->
        <div class="content">
            <table class="summary-table">
                <!-- RECEIPTS SECTION -->
                <tr class="section-header">
                    <th colspan="2">Receipts</th>
                </tr>
                <tr>
                    <td>Student Payments</td>
                    <td class="amount">{{ number_format($report['student_payments_total'], 2) }}</td>
                </tr>
                <tr>
                    <td>Admission Payments</td>
                    <td class="amount">{{ number_format($report['admission_payments_total'], 2) }}</td>
                </tr>
                <tr>
                    <td>Extra Income</td>
                    <td class="amount">{{ number_format($report['extra_incomes_total'], 2) }}</td>
                </tr>
                <tr class="total-row">
                    <td><strong>Total Receipts</strong></td>
                    <td class="amount"><strong>{{ number_format($report['total_receipts'], 2) }}</strong></td>
                </tr>
                
                <!-- PAYMENTS SECTION -->
                <tr class="section-header">
                    <th colspan="2">Payments</th>
                </tr>
                <tr>
                    <td>Teacher Payments</td>
                    <td class="amount">{{ number_format($report['teacher_payments_total'], 2) }}</td>
                </tr>
                <tr>
                    <td>Institute Payments</td>
                    <td class="amount">{{ number_format($report['institute_payments_total'], 2) }}</td>
                </tr>
                <tr class="total-row">
                    <td><strong>Total Payments</strong></td>
                    <td class="amount"><strong>{{ number_format($report['total_payments'], 2) }}</strong></td>
                </tr>
                
                <!-- BALANCE SECTION -->
                <tr class="balance-row">
                    <td><strong>Daily Balance</strong></td>
                    <td class="amount"><strong>{{ number_format($report['balance'], 2) }}</strong></td>
                </tr>
            </table>
            
            <!-- FOOTER -->
            <div class="footer no-break">
                <div class="printed-date">
                    <p>Printed on: {{ date('Y-m-d h:i A') }}</p>
                </div>
                
                <div class="form-section">
                    <strong>Reason for Daily Collection:</strong>
                    <div class="dotted-line"></div>
                </div>
                
                <div class="form-section">
                    <strong>Daily Collection Notes (Hand Written):</strong>
                    <div class="dotted-line" style="min-height: 40px;"></div>
                </div>
                
                <div class="signature-area">
                    <div class="signature-box">
                        <strong>Prepared By:</strong>
                        <div class="signature-line"></div>
                        <p style="margin-top: 5px; font-size: 10px;">Name & Signature</p>
                    </div>
                    
                    <div class="signature-box">
                        <strong>Approved By:</strong>
                        <div class="signature-line"></div>
                        <p style="margin-top: 5px; font-size: 10px;">Name & Signature</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>