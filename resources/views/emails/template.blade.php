<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject ?? 'Notification' }}</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; font-size: 16px; line-height: 1.6; color: #333; background-color: #f4f4f4;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f4f4f4; padding: 20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, {{ primary_color() }} 0%, {{ system_setting('secondary_color', '#1e40af') }} 100%); padding: 30px; text-align: center;">
                            <h1 style="color: #ffffff; margin: 0; font-size: 24px; font-weight: bold;">{{ strtoupper(app_name()) }}</h1>
                            <p style="color: #ffffff; margin: 10px 0 0; font-size: 14px; opacity: 0.9;">Admissions Portal</p>
                        </td>
                    </tr>
                    
                    <!-- Content -->
                    <tr>
                        <td style="padding: 40px 30px;">
                            @yield('content')
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f8f9fa; padding: 20px 30px; border-top: 1px solid #e9ecef;">
                            <p style="margin: 0 0 10px; font-size: 14px; color: #666; text-align: center;">
                                <strong>{{ app_name() }}</strong><br>
                                {{ system_setting('contact_email', 'support@example.com') }}<br>
                                {{ system_setting('contact_phone', '+254 700 000 000') }}
                            </p>
                            <p style="margin: 0; font-size: 12px; color: #999; text-align: center;">
                                This is an automated message from the {{ app_name() }} Admissions Portal.<br>
                                Please do not reply directly to this email.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
