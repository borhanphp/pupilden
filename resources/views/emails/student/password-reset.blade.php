<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset Request</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .email-container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #e9ecef;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #007bff;
            margin-bottom: 10px;
        }
        .content {
            margin-bottom: 30px;
        }
        .greeting {
            font-size: 18px;
            margin-bottom: 20px;
            color: #495057;
        }
        .message {
            font-size: 16px;
            margin-bottom: 25px;
            color: #6c757d;
        }
        .reset-button {
            display: inline-block;
            background-color: #007bff;
            color: #ffffff;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            text-align: center;
            margin: 20px 0;
            transition: background-color 0.3s;
        }
        .reset-button:hover {
            background-color: #0056b3;
            color: #ffffff;
        }
        .token-info {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            border-left: 4px solid #007bff;
        }
        .token-label {
            font-weight: bold;
            color: #495057;
            margin-bottom: 5px;
        }
        .token-value {
            font-family: monospace;
            background-color: #e9ecef;
            padding: 8px;
            border-radius: 3px;
            word-break: break-all;
        }
        .security-note {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            color: #856404;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
            color: #6c757d;
            font-size: 14px;
        }
        .expiry-info {
            background-color: #d1ecf1;
            border: 1px solid #bee5eb;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            color: #0c5460;
        }
        @media (max-width: 600px) {
            body {
                padding: 10px;
            }
            .email-container {
                padding: 20px;
            }
            .reset-button {
                display: block;
                width: 100%;
                box-sizing: border-box;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <div class="logo">{{ $appName ?? 'Learning Platform' }}</div>
            <h1>Password Reset Request</h1>
        </div>

        <div class="content">
            <div class="greeting">
                @if($student)
                    Hello {{ $student->name ?? $student->username }},
                @else
                    Hello,
                @endif
            </div>

            <div class="message">
                We received a request to reset your password for your student account. If you made this request, please click the button below to reset your password.
            </div>

            <div style="text-align: center;">
                <a href="{{ $resetUrl }}" class="reset-button">Reset My Password</a>
            </div>

            <div class="token-info">
                <div class="token-label">Reset Token:</div>
                <div class="token-value">{{ $token }}</div>
                <div style="margin-top: 10px; font-size: 14px; color: #6c757d;">
                    You can also copy and paste this token into the password reset form.
                </div>
            </div>

            <div class="expiry-info">
                <strong>⏰ Important:</strong> This password reset link will expire in 60 minutes for security reasons.
            </div>

            <div class="security-note">
                <strong>🔒 Security Notice:</strong>
                <ul style="margin: 10px 0; padding-left: 20px;">
                    <li>If you did not request this password reset, please ignore this email.</li>
                    <li>Your password will not be changed until you click the link above.</li>
                    <li>Never share this reset token with anyone.</li>
                    <li>For security, this link can only be used once.</li>
                </ul>
            </div>

            <div class="message">
                If you're having trouble clicking the button above, you can copy and paste the following URL into your browser:
                <br><br>
                <a href="{{ $resetUrl }}" style="color: #007bff; word-break: break-all;">{{ $resetUrl }}</a>
            </div>
        </div>

        <div class="footer">
            <p>This email was sent from {{ $appName ?? 'Learning Platform' }}</p>
            <p>If you have any questions, please contact our support team.</p>
            <p style="margin-top: 15px; font-size: 12px; color: #adb5bd;">
                © {{ date('Y') }} {{ $appName ?? 'Learning Platform' }}. All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>
