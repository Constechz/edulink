<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Pending Approval</title>
</head>
<body style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f7fafc; color: #2d3748; padding: 40px 20px; margin: 0;">

    <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05); border: 1px solid rgba(0, 51, 102, 0.08);">
        <!-- Top Banner -->
        <div style="background-color: #003366; padding: 30px; text-align: center;">
            <h1 style="color: #ffffff; margin: 0; font-size: 1.5rem; font-weight: 700; letter-spacing: -0.02em;">EduLink Ghana ERP</h1>
            <p style="color: #ffd700; margin: 5px 0 0 0; font-size: 0.9rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">Registration Received</p>
        </div>

        <!-- Body Content -->
        <div style="padding: 40px 30px;">
            <h2 style="color: #003366; font-size: 1.3rem; font-weight: 700; margin-top: 0; margin-bottom: 20px;">Welcome to EduLink, {{ $school->owner_name }}!</h2>
            
            <p style="font-size: 1rem; line-height: 1.6; color: #4a5568; margin-bottom: 20px;">
                Thank you for choosing EduLink Ghana ERP to power your school's administrative and learning systems. We are excited to partner with you!
            </p>
            
            <p style="font-size: 1rem; line-height: 1.6; color: #4a5568; margin-bottom: 30px;">
                Your registration for **{{ $school->name }}** has been received successfully. To safeguard our ecosystem, all new school registrations require a quick review and approval by the platform Super Admin.
            </p>

            <!-- Status Indicator -->
            <div style="background-color: #fffaf0; border-left: 4px solid #dd6b20; padding: 20px; border-radius: 4px; margin-bottom: 30px;">
                <span style="font-weight: 700; color: #dd6b20; text-transform: uppercase; font-size: 0.8rem; letter-spacing: 0.05em; display: block; margin-bottom: 5px;">Current Status</span>
                <span style="font-size: 1.05rem; font-weight: 700; color: #2d3748;">Pending Administrative Approval</span>
                <p style="margin: 8px 0 0 0; font-size: 0.9rem; color: #718096; line-height: 1.5;">
                    The platform administration is reviewing your onboarding details. You will receive another email containing your access links as soon as your workspace is activated (usually within 1-2 hours).
                </p>
            </div>

            <!-- Workspace Preview -->
            <div style="background-color: #f7fafc; border-radius: 8px; padding: 20px; border: 1px solid #edf2f7; margin-bottom: 30px;">
                <h4 style="margin-top: 0; margin-bottom: 15px; color: #003366; font-size: 0.95rem; text-transform: uppercase; letter-spacing: 0.02em;">Pending Workspace Details</h4>
                <table style="width: 100%; border-collapse: collapse; font-size: 0.95rem; table-layout: fixed;">
                    <tr>
                        <td style="padding: 6px 0; color: #718096; width: 130px; vertical-align: top; font-weight: 500;">School Name</td>
                        <td style="padding: 6px 0; color: #2d3748; font-weight: 600; word-break: break-word; vertical-align: top;">{{ $school->name }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 6px 0; color: #718096; width: 130px; vertical-align: top; font-weight: 500;">Subdomain Target</td>
                        <td style="padding: 6px 0; color: #2d3748; vertical-align: top;">
                            <code style="word-break: break-all; white-space: pre-wrap; font-family: Courier, monospace; background-color: #edf2f7; padding: 2px 6px; border-radius: 4px; font-size: 0.9em;">{{ $school->subdomain }}.edulink.local</code>
                        </td>
                    </tr>
                </table>
            </div>

            <p style="font-size: 0.9rem; line-height: 1.6; color: #718096;">
                If you have any questions or require custom onboarding support, feel free to reply directly to this email or reach our support team at support@edulink.com.
            </p>
        </div>

        <!-- Footer -->
        <div style="background-color: #f7fafc; padding: 20px 30px; text-align: center; border-top: 1px solid #edf2f7; font-size: 0.8rem; color: #a0aec0;">
            {{ config('app.name', 'EduLink') }} Ghana ERP Support Panel
        </div>
    </div>

</body>
</html>
