<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School Approved & Activated</title>
</head>
<body style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f7fafc; color: #2d3748; padding: 40px 20px; margin: 0;">

    <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05); border: 1px solid rgba(0, 51, 102, 0.08);">
        <!-- Top Banner -->
        <div style="background-color: #003366; padding: 30px; text-align: center;">
            <h1 style="color: #ffffff; margin: 0; font-size: 1.5rem; font-weight: 700; letter-spacing: -0.02em;">EduLink Ghana ERP</h1>
            <p style="color: #ffd700; margin: 5px 0 0 0; font-size: 0.9rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">Account Approved & Active</p>
        </div>

        <!-- Body Content -->
        <div style="padding: 40px 30px;">
            <h2 style="color: #10b981; font-size: 1.4rem; font-weight: 700; margin-top: 0; margin-bottom: 20px;">Congratulations, {{ $school->owner_name }}!</h2>
            
            <p style="font-size: 1.05rem; line-height: 1.6; color: #2d3748; margin-bottom: 20px;">
                We are thrilled to inform you that your registration for **{{ $school->name }}** has been approved and activated by the platform administration.
            </p>
            
            <p style="font-size: 1rem; line-height: 1.6; color: #4a5568; margin-bottom: 30px;">
                Your dedicated workspace has been provisioned and is ready for use. You can now log in, complete the 5-step onboarding wizard, configure your academic year structure, and start adding campus administrators and students.
            </p>

            <!-- Success Box -->
            <div style="background-color: #f0fdf4; border-left: 4px solid #10b981; padding: 20px; border-radius: 4px; margin-bottom: 35px;">
                <span style="font-weight: 700; color: #10b981; text-transform: uppercase; font-size: 0.8rem; letter-spacing: 0.05em; display: block; margin-bottom: 5px;">Workspace Status</span>
                <span style="font-size: 1.1rem; font-weight: 700; color: #14532d;">Active & Ready</span>
                
                <table style="width: 100%; border-collapse: collapse; font-size: 0.95rem; margin-top: 15px; border-top: 1px solid rgba(16, 185, 129, 0.15); padding-top: 15px;">
                    <tr>
                        <td style="padding: 6px 0; color: #15803d; width: 35%;">Workspace Code</td>
                        <td style="padding: 6px 0; color: #14532d; font-weight: 700;">{{ $school->school_code }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 6px 0; color: #15803d;">Workspace URL</td>
                        <td style="padding: 6px 0; color: #14532d;"><code>http://{{ $school->subdomain }}.edulink.local/login</code></td>
                    </tr>
                </table>
            </div>

            <!-- Action Button -->
            <div style="text-align: center; margin-bottom: 30px;">
                <a href="{{ url('/login') }}" style="background-color: #003366; color: #ffffff; text-decoration: none; padding: 12px 35px; border-radius: 8px; font-weight: 600; display: inline-block; font-size: 1rem; box-shadow: 0 4px 12px rgba(0, 51, 102, 0.15);">
                    Log In to Your Portal
                </a>
            </div>

            <p style="font-size: 0.9rem; line-height: 1.6; color: #718096; border-top: 1px solid #edf2f7; padding-top: 20px; margin-top: 30px;">
                *Note: You can log in using your registered admin email address (<strong>{{ $school->owner_email }}</strong>) and the password you created during sign up.*
            </p>
        </div>

        <!-- Footer -->
        <div style="background-color: #f7fafc; padding: 20px 30px; text-align: center; border-top: 1px solid #edf2f7; font-size: 0.8rem; color: #a0aec0;">
            {{ config('app.name', 'EduLink') }} Ghana ERP Administration Team
        </div>
    </div>

</body>
</html>
