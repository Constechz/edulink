<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $customSubject }}</title>
</head>
<body style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f7fafc; color: #2d3748; padding: 40px 20px; margin: 0;">

    <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05); border: 1px solid rgba(0, 51, 102, 0.08);">
        <!-- Top Banner -->
        <div style="background-color: #003366; padding: 30px; text-align: center;">
            <h1 style="color: #ffffff; margin: 0; font-size: 1.5rem; font-weight: 700; letter-spacing: -0.02em;">EduLink Ghana ERP</h1>
            <p style="color: #ffd700; margin: 5px 0 0 0; font-size: 0.9rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">Platform Administration Announcement</p>
        </div>

        <!-- Body Content -->
        <div style="padding: 40px 30px; min-height: 200px;">
            <div style="font-size: 1.05rem; line-height: 1.6; color: #2d3748; white-space: pre-wrap;">{!! nl2br(e($customBody)) !!}</div>
        </div>

        <!-- Footer -->
        <div style="background-color: #f7fafc; padding: 20px 30px; text-align: center; border-top: 1px solid #edf2f7; font-size: 0.8rem; color: #a0aec0;">
            {{ config('app.name', 'EduLink') }} Ghana ERP Administration Team <br>
            This is an official system communication sent by the platform administrator.
        </div>
    </div>

</body>
</html>
