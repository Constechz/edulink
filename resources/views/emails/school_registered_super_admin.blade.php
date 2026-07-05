<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New School Registered</title>
</head>
<body style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f7fafc; color: #2d3748; padding: 40px 20px; margin: 0;">

    <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05); border: 1px solid rgba(0, 51, 102, 0.08);">
        <!-- Top Banner -->
        <div style="background-color: #003366; padding: 30px; text-align: center;">
            <h1 style="color: #ffffff; margin: 0; font-size: 1.5rem; font-weight: 700; letter-spacing: -0.02em;">EduLink Ghana ERP</h1>
            <p style="color: #ffd700; margin: 5px 0 0 0; font-size: 0.9rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">Platform Administration</p>
        </div>

        <!-- Body Content -->
        <div style="padding: 40px 30px;">
            <h2 style="color: #003366; font-size: 1.3rem; font-weight: 700; margin-top: 0; margin-bottom: 20px;">New School Registration Pending</h2>
            
            <p style="font-size: 1rem; line-height: 1.6; color: #4a5568; margin-bottom: 30px;">
                Hello Super Admin, <br><br>
                A new school tenant has just self-registered on the platform. The registration is currently **pending approval and activation** from the admin dashboard.
            </p>

            <!-- Metadata Box -->
            <div style="background-color: #f7fafc; border-radius: 8px; padding: 20px; border: 1px solid #edf2f7; margin-bottom: 30px;">
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="padding: 6px 0; font-weight: 600; color: #718096; font-size: 0.85rem; text-transform: uppercase; width: 35%;">School Name</td>
                        <td style="padding: 6px 0; color: #2d3748; font-weight: 700;">{{ $school->name }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 6px 0; font-weight: 600; color: #718096; font-size: 0.85rem; text-transform: uppercase;">Subdomain</td>
                        <td style="padding: 6px 0; color: #2d3748;"><code>{{ $school->subdomain }}.edulink.local</code></td>
                    </tr>
                    <tr>
                        <td style="padding: 6px 0; font-weight: 600; color: #718096; font-size: 0.85rem; text-transform: uppercase;">School Code</td>
                        <td style="padding: 6px 0; color: #2d3748; font-weight: 600;">{{ $school->school_code }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 6px 0; font-weight: 600; color: #718096; font-size: 0.85rem; text-transform: uppercase;">Administrator</td>
                        <td style="padding: 6px 0; color: #2d3748;">{{ $school->owner_name }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 6px 0; font-weight: 600; color: #718096; font-size: 0.85rem; text-transform: uppercase;">Admin Email</td>
                        <td style="padding: 6px 0; color: #003366; font-weight: 600;">{{ $school->owner_email }}</td>
                    </tr>
                </table>
            </div>

            <!-- Action Button -->
            <div style="text-align: center; margin-bottom: 20px;">
                <a href="{{ url('/super-admin/analytics') }}" style="background-color: #003366; color: #ffffff; text-decoration: none; padding: 12px 30px; border-radius: 8px; font-weight: 600; display: inline-block; font-size: 0.95rem; box-shadow: 0 4px 10px rgba(0, 51, 102, 0.15);">
                    Review & Approve Tenant
                </a>
            </div>
        </div>

        <!-- Footer -->
        <div style="background-color: #f7fafc; padding: 20px 30px; text-align: center; border-top: 1px solid #edf2f7; font-size: 0.8rem; color: #a0aec0;">
            {{ config('app.name', 'EduLink') }} Ghana SaaS Platform System Notifications
        </div>
    </div>

</body>
</html>
