<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Receipt - {{ $payment->receipt_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            background: #fff;
        }
        .receipt-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 30px;
            position: relative;
        }
        .watermark {
            position: absolute;
            top: 40%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-30deg);
            font-size: 80px;
            font-weight: bold;
            color: rgba(124, 58, 237, 0.1);
            z-index: 0;
            pointer-events: none;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid {{ $branding['primary_color'] }};
            position: relative;
            z-index: 1;
        }
        .logo-section {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .logo {
            max-width: 80px;
            max-height: 80px;
        }
        .school-info h1 {
            font-size: 20px;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 4px;
        }
        .school-info p {
            font-size: 11px;
            color: #666;
        }
        .receipt-info {
            text-align: right;
        }
        .receipt-info h2 {
            font-size: 24px;
            font-weight: 700;
            color: {{ $branding['primary_color'] }};
            margin-bottom: 8px;
        }
        .receipt-number {
            font-size: 14px;
            font-weight: 600;
            color: #333;
            margin-bottom: 4px;
        }
        .receipt-date {
            font-size: 11px;
            color: #666;
        }
        .section {
            margin-bottom: 25px;
            position: relative;
            z-index: 1;
        }
        .section-title {
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            color: {{ $branding['primary_color'] }};
            margin-bottom: 12px;
            padding-bottom: 6px;
            border-bottom: 1px solid #e5e5e5;
        }
        .info-grid {
            display: table;
            width: 100%;
        }
        .info-row {
            display: table-row;
        }
        .info-label {
            display: table-cell;
            width: 35%;
            padding: 6px 0;
            font-weight: 600;
            color: #555;
        }
        .info-value {
            display: table-cell;
            padding: 6px 0;
            color: #1a1a1a;
        }
        .payment-summary {
            background: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            margin-top: 15px;
        }
        .payment-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px dashed #e5e5e5;
        }
        .payment-row:last-child {
            border-bottom: none;
        }
        .payment-label {
            font-weight: 600;
            color: #555;
        }
        .payment-value {
            font-weight: 600;
            color: #1a1a1a;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 15px 0 0 0;
            margin-top: 10px;
            border-top: 2px solid {{ $branding['primary_color'] }};
        }
        .total-label {
            font-size: 16px;
            font-weight: 700;
            color: #1a1a1a;
        }
        .total-value {
            font-size: 18px;
            font-weight: 700;
            color: {{ $branding['primary_color'] }};
        }
        .status-badge {
            display: inline-block;
            padding: 6px 16px;
            background: {{ $branding['primary_color'] }};
            color: white;
            font-weight: 700;
            font-size: 12px;
            border-radius: 20px;
            text-transform: uppercase;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e5e5e5;
            text-align: center;
            position: relative;
            z-index: 1;
        }
        .footer-thanks {
            font-size: 14px;
            font-weight: 600;
            color: #1a1a1a;
            margin-bottom: 10px;
        }
        .footer-contact {
            font-size: 11px;
            color: #666;
            margin-bottom: 6px;
        }
        .footer-powered {
            font-size: 10px;
            color: #999;
        }
        .footer-copyright {
            font-size: 10px;
            color: #999;
            margin-top: 8px;
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        <div class="watermark">PAID</div>
        
        <div class="header">
            <div class="logo-section">
                <img src="{{ $branding['logo'] }}" alt="School Logo" class="logo">
                <div class="school-info">
                    <h1>{{ $branding['name'] }}</h1>
                    <p>{{ $branding['tagline'] }}</p>
                </div>
            </div>
            <div class="receipt-info">
                <h2>PAYMENT RECEIPT</h2>
                <div class="receipt-number">{{ $payment->receipt_number }}</div>
                <div class="receipt-date">
                    {{ $payment->paid_at->format('F j, Y \a\t h:i A') }}
                </div>
                <div style="margin-top: 8px;">
                    <span class="status-badge">CONFIRMED & PAID</span>
                </div>
            </div>
        </div>

        <div class="section">
            <div class="section-title">Student Details</div>
            <div class="info-grid">
                <div class="info-row">
                    <div class="info-label">Full Name:</div>
                    <div class="info-value">{{ $application->first_name }} {{ $application->middle_name }} {{ $application->last_name }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Application Number:</div>
                    <div class="info-value">{{ $application->application_number }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Email Address:</div>
                    <div class="info-value">{{ $application->user->email }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Phone Number:</div>
                    <div class="info-value">{{ $payment->phone_number ?? $application->user->phone }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Intake:</div>
                    <div class="info-value">{{ $application->intake->name ?? 'N/A' }}</div>
                </div>
            </div>
        </div>

        <div class="section">
            <div class="section-title">Payment Details</div>
            <div class="payment-summary">
                <div class="payment-row">
                    <span class="payment-label">Amount Paid:</span>
                    <span class="payment-value">{{ $payment->formatted_amount }}</span>
                </div>
                <div class="payment-row">
                    <span class="payment-label">Payment Method:</span>
                    <span class="payment-value">{{ ucfirst($payment->payment_method) }}</span>
                </div>
                <div class="payment-row">
                    <span class="payment-label">Transaction ID:</span>
                    <span class="payment-value">{{ $payment->transaction_id }}</span>
                </div>
                @if($payment->mpesa_receipt)
                <div class="payment-row">
                    <span class="payment-label">M-PESA Receipt:</span>
                    <span class="payment-value">{{ $payment->mpesa_receipt }}</span>
                </div>
                @endif
                <div class="payment-row">
                    <span class="payment-label">Academic Year:</span>
                    <span class="payment-value">{{ $application->intake->academic_year ?? date('Y') }}</span>
                </div>
                <div class="payment-row">
                    <span class="payment-label">Fee Description:</span>
                    <span class="payment-value">Application Fee</span>
                </div>
                <div class="total-row">
                    <span class="total-label">Total Amount</span>
                    <span class="total-value">{{ $payment->formatted_amount }}</span>
                </div>
            </div>
        </div>

        <div class="footer">
            <div class="footer-thanks">Thank you for your payment!</div>
            <div class="footer-contact">
                {{ $branding['name'] }} | {{ $branding['email'] }} | {{ $branding['phone'] }}
                @if($branding['address'])
                    | {{ $branding['address'] }}
                @endif
            </div>
            <div class="footer-powered">Powered by {{ $system_name }}</div>
            <div class="footer-copyright">&copy; {{ date('Y') }} {{ $branding['name'] }}. All rights reserved.</div>
        </div>
    </div>
</body>
</html>