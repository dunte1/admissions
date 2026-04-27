<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Password Reset</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
        .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }
        .password-box { background: white; border: 2px dashed #667eea; padding: 20px; text-align: center; margin: 20px 0; border-radius: 8px; }
        .password { font-family: monospace; font-size: 24px; font-weight: bold; color: #667eea; letter-spacing: 2px; }
        .warning { background: #fff3cd; border: 1px solid #ffc107; padding: 15px; border-radius: 8px; margin-top: 20px; }
        .button { display: inline-block; background: #667eea; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; margin-top: 20px; }
        .footer { text-align: center; margin-top: 20px; color: #666; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Password Reset</h1>
        </div>
        <div class="content">
            <p>Hello {{ $user->first_name }},</p>
            
            <p>Your password has been reset by an administrator. Here is your new temporary password:</p>
            
            <div class="password-box">
                <p style="margin: 0 0 10px; color: #666; font-size: 14px;">Your New Password:</p>
                <p class="password">{{ $newPassword }}</p>
            </div>
            
            <div class="warning">
                <strong><i class="fas fa-exclamation-triangle"></i> Security Notice:</strong>
                <ul style="margin: 10px 0 0; padding-left: 20px;">
                    <li>Please change this password immediately after logging in</li>
                    <li>You have been logged out of all other devices</li>
                    <li>If you did not request this change, contact your administrator immediately</li>
                </ul>
            </div>
            
            <p style="margin-top: 20px;">
                <a href="{{ url('/login') }}" class="button">Login Now</a>
            </p>
            
            <div class="footer">
                <p>{{ system_setting('system_name', 'Admission Portal') }}</p>
                <p>This is an automated message. Please do not reply to this email.</p>
            </div>
        </div>
    </div>
</body>
</html>
