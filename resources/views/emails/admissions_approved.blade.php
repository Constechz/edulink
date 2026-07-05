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
        
        <p>Dear <strong>{{ $parentUser->name }}</strong>,</p>
        
        <p>Congratulations! Your child's online application has been **approved** at <strong>{{ $school->name }}</strong>, and active student profiles have been generated.</p>
        
        <p>Below are the login credentials for both the **Parent Portal** and the **Student Portal** accounts to access class report cards, attendance logs, and school fees securely.</p>
        
        <!-- Parent credentials -->
        <div style="background-color: #f4f6f9; border-radius: 8px; padding: 20px; margin: 25px 0;">
            <h4 style="margin-top: 0; color: #003366; border-bottom: 1px solid #e2e8f0; padding-bottom: 8px;">1. Parent Portal Access</h4>
            <table style="width: 100%; font-size: 14px;">
                <tr>
                    <td style="color: #777; width: 120px; padding: 6px 0;">Login Email:</td>
                    <td style="font-weight: bold; color: #333; padding: 6px 0;">{{ $parentUser->email }}</td>
                </tr>
                <tr>
                    <td style="color: #777; padding: 6px 0;">Password:</td>
                    <td style="font-weight: bold; color: #333; padding: 6px 0;">{{ $parentPassword }}</td>
                </tr>
            </table>
        </div>

        <!-- Student credentials -->
        <div style="background-color: #f4f6f9; border-radius: 8px; padding: 20px; margin: 25px 0;">
            <h4 style="margin-top: 0; color: #003366; border-bottom: 1px solid #e2e8f0; padding-bottom: 8px;">2. Student Portal Access</h4>
            <table style="width: 100%; font-size: 14px;">
                <tr>
                    <td style="color: #777; width: 120px; padding: 6px 0;">Login Username:</td>
                    <td style="font-weight: bold; color: #333; padding: 6px 0;">{{ $studentUser->email }}</td>
                </tr>
                <tr>
                    <td style="color: #777; padding: 6px 0;">Password (DOB):</td>
                    <td style="font-weight: bold; color: #333; padding: 6px 0;">{{ $studentPassword }}</td>
                </tr>
            </table>
        </div>

        <p style="font-size: 14px; text-align: center; margin: 25px 0;">
            <a href="{{ url('/login') }}" style="background-color: #003366; color: #ffffff; padding: 12px 30px; text-decoration: none; border-radius: 8px; font-weight: bold; display: inline-block;">Click Here to Access Portal Login</a>
        </p>
        
        <p style="font-size: 13px; color: #777;">* We recommend parents log in and change their password immediately after first access.</p>
        
        <hr style="border: 0; border-top: 1px solid #eef2f6; margin: 30px 0;">
        
        <div style="text-align: center; font-size: 12px; color: #999;">
            <p>This is an automated notification from the {{ $school->name }} Administration.</p>

        </div>
    </div>
</body>
</html>
