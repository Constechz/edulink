<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Welcome to {{ $school->name }}</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f9f9f9;">
    <div style="background-color: #ffffff; padding: 30px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); border: 1px solid #eef2f6;">
        <div style="text-align: center; margin-bottom: 25px;">
            <h2 style="color: #003366; margin-bottom: 5px;">{{ $school->name }}</h2>
            <span style="font-size: 13px; color: #777; letter-spacing: 1px; text-transform: uppercase;">EduLink Portal Notification</span>
        </div>
        
        <p>Dear <strong>{{ $user->name }}</strong>,</p>
        
        <p>Welcome! A guardian account has been successfully created for you at <strong>{{ $school->name }}</strong> on our EduLink ERP platform.</p>
        
        <p>You can log in to your Parent Portal to monitor your child's academic report cards, attendance logs, and pay school fees online securely.</p>
        
        <div style="background-color: #f4f6f9; border-radius: 8px; padding: 20px; margin: 25px 0;">
            <h4 style="margin-top: 0; color: #003366; border-bottom: 1px solid #e2e8f0; padding-bottom: 8px;">Your Portal Login Credentials</h4>
            <table style="width: 100%; font-size: 14px;">
                <tr>
                    <td style="color: #777; width: 100px; padding: 6px 0;">Username:</td>
                    <td style="font-weight: bold; color: #333; padding: 6px 0;">{{ $user->email }}</td>
                </tr>
                <tr>
                    <td style="color: #777; padding: 6px 0;">Password:</td>
                    <td style="font-weight: bold; color: #333; padding: 6px 0;">{{ $temporaryPassword }}</td>
                </tr>
                <tr>
                    <td style="color: #777; padding: 6px 0;">Portal URL:</td>
                    <td style="padding: 6px 0;"><a href="{{ url('/login') }}" style="color: #003366; font-weight: bold; text-decoration: none;">Click Here to Login</a></td>
                </tr>
            </table>
        </div>
        
        <p style="font-size: 13px; color: #777;">* For security reasons, we strongly recommend that you change your password immediately after your first successful login.</p>
        
        <hr style="border: 0; border-top: 1px solid #eef2f6; margin: 30px 0;">
        
        <div style="text-align: center; font-size: 12px; color: #999;">
            <p>This is an automated notification from the {{ $school->name }} Administration.</p>

        </div>
    </div>
</body>
</html>
