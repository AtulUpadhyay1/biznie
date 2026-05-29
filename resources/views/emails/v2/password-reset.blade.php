<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Reset your Biznie password</title>
</head>
<body style="margin:0;padding:0;background:#f5f5f5;font-family:Arial,Helvetica,sans-serif;color:#1f2937;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f5f5f5;padding:32px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="560" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,0.06);">
                    <tr>
                        <td style="background:#CC1414;padding:20px 32px;color:#ffffff;font-size:20px;font-weight:700;">
                            Biznie
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:32px;">
                            <h1 style="margin:0 0 16px 0;font-size:22px;color:#111827;">Reset your password</h1>
                            <p style="margin:0 0 16px 0;font-size:14px;line-height:1.6;color:#374151;">
                                Hi {{ $recipientName }},
                            </p>
                            <p style="margin:0 0 24px 0;font-size:14px;line-height:1.6;color:#374151;">
                                We received a request to reset the password for your Biznie account. Click the button below to set a new one. This link will expire in {{ $expiresInMinutes }} minutes.
                            </p>
                            <p style="margin:0 0 24px 0;text-align:center;">
                                <a href="{{ $resetUrl }}" style="background:#CC1414;color:#ffffff;text-decoration:none;padding:12px 28px;border-radius:8px;font-weight:600;font-size:14px;display:inline-block;">
                                    Reset Password
                                </a>
                            </p>
                            <p style="margin:0 0 8px 0;font-size:12px;color:#6b7280;">
                                If the button doesn’t work, copy and paste this link:
                            </p>
                            <p style="margin:0 0 24px 0;font-size:12px;color:#374151;word-break:break-all;">
                                {{ $resetUrl }}
                            </p>
                            <p style="margin:0;font-size:12px;color:#6b7280;line-height:1.6;">
                                If you didn’t request a password reset, you can safely ignore this email — your password won’t change.
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="background:#f9fafb;padding:16px 32px;font-size:11px;color:#9ca3af;text-align:center;">
                            © {{ date('Y') }} Biznie. All rights reserved.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
