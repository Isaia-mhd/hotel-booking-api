<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reset Your Password</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f7f7f7; padding: 20px;">
    <div style="max-width: 600px; margin: auto; background-color: white; border-radius: 8px; padding: 30px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <h2 style="color: #333;">Hello!</h2>
        <p>Click the button below to reset your password:</p>
        <p style="text-align: center; margin: 30px 0;">
            <a href="{{ $resetUrl }}" style="padding: 12px 25px; background-color: rgb(0, 85, 255); color: white; text-decoration: none; border-radius: 5px;">
                Reset Password
            </a>
        </p>
        <p>If you did not request a password reset, no further action is required.</p>
        <p style="margin-top: 30px;">Regards,<br>{{ config('app.name') }}</p>
    </div>
</body>
</html>
