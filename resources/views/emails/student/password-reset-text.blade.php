Password Reset Request - {{ $appName ?? 'Learning Platform' }}

@if($student)
Hello {{ $student->name ?? $student->username }},
@else
Hello,
@endif

We received a request to reset your password for your student account. If you made this request, please use the information below to reset your password.

RESET TOKEN:
{{ $token }}

RESET URL:
{{ $resetUrl }}

INSTRUCTIONS:
1. Click the link above or copy and paste it into your browser
2. Enter the reset token: {{ $token }}
3. Create a new password
4. Save your changes

IMPORTANT SECURITY INFORMATION:
- This password reset link will expire in 60 minutes
- If you did not request this password reset, please ignore this email
- Your password will not be changed until you complete the reset process
- Never share this reset token with anyone
- For security, this link can only be used once

If you're having trouble with the link above, you can manually navigate to the password reset page and enter the token provided.

If you have any questions or concerns, please contact our support team.

---
This email was sent from {{ $appName ?? 'Learning Platform' }}
© {{ date('Y') }} {{ $appName ?? 'Learning Platform' }}. All rights reserved.
