<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Receipt</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="text-align: center; margin-bottom: 30px;">
        <h1 style="color: #7C3AED; margin-bottom: 10px;">Payment Receipt</h1>
        <p style="color: #666;">Thank you for your payment!</p>
    </div>

    <div style="background: #f9f9f9; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="padding: 8px 0; font-weight: bold; color: #555;">Receipt Number:</td>
                <td style="padding: 8px 0;">{{ $payment->receipt_number }}</td>
            </tr>
            <tr>
                <td style="padding: 8px 0; font-weight: bold; color: #555;">Student Name:</td>
                <td style="padding: 8px 0;">{{ $application->first_name }} {{ $application->middle_name }} {{ $application->last_name }}</td>
            </tr>
            <tr>
                <td style="padding: 8px 0; font-weight: bold; color: #555;">Application Number:</td>
                <td style="padding: 8px 0;">{{ $application->application_number }}</td>
            </tr>
            <tr>
                <td style="padding: 8px 0; font-weight: bold; color: #555;">Amount Paid:</td>
                <td style="padding: 8px 0; font-weight: bold; color: #7C3AED;">{{ $payment->formatted_amount }}</td>
            </tr>
            <tr>
                <td style="padding: 8px 0; font-weight: bold; color: #555;">Payment Method:</td>
                <td style="padding: 8px 0;">{{ ucfirst($payment->payment_method) }}</td>
            </tr>
            <tr>
                <td style="padding: 8px 0; font-weight: bold; color: #555;">Date Paid:</td>
                <td style="padding: 8px 0;">{{ $payment->paid_at->format('F j, Y \a\t h:i A') }}</td>
            </tr>
            <tr>
                <td style="padding: 8px 0; font-weight: bold; color: #555;">Status:</td>
                <td style="padding: 8px 0;"><span style="background: #10B981; color: white; padding: 4px 12px; border-radius: 12px; font-size: 12px;">CONFIRMED & PAID</span></td>
            </tr>
        </table>
    </div>

    <p style="margin-bottom: 20px;">
        Your payment has been successfully processed. Please find your payment receipt attached to this email.
    </p>

    <div style="text-align: center; padding: 20px; border-top: 1px solid #e5e5e5; margin-top: 30px;">
        <p style="color: #666; font-size: 14px; margin-bottom: 10px;">
            <strong>{{ $branding['name'] }}</strong>
        </p>
        <p style="color: #999; font-size: 12px;">
            {{ $branding['email'] }} | {{ $branding['phone'] }}
        </p>
        <p style="color: #999; font-size: 11px; margin-top: 10px;">
            Powered by {{ $system_name }}
        </p>
    </div>
</body>
</html>