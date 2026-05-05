<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Account Activated</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 40px auto; background: #fff; border-radius: 8px; overflow: hidden; }
        .header { background: #1e293b; color: #fff; padding: 32px; text-align: center; }
        .header h1 { margin: 0; font-size: 24px; }
        .body { padding: 32px; color: #374151; }
        .body p { line-height: 1.6; margin: 0 0 16px; }
        .button { display: inline-block; background: #1e293b; color: #fff; text-decoration: none; padding: 12px 28px; border-radius: 6px; font-weight: bold; }
        .footer { padding: 24px 32px; color: #9ca3af; font-size: 13px; text-align: center; border-top: 1px solid #e5e7eb; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Account Activated</h1>
        </div>
        <div class="body">
            <p>Hello {{ $user->name }},</p>
            <p>Your account has been activated. You can now log in with your email address.</p>
            <p>
                <a href="{{ url('/') }}" class="button">Log in</a>
            </p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} {{ config('app.name') }}
        </div>
    </div>
</body>
</html>
