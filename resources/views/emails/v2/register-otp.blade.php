<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Your Biznie verification code</title>
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
                            <h1 style="margin:0 0 16px 0;font-size:22px;color:#111827;">Verify your email</h1>
                            <p style="margin:0 0 16px 0;font-size:14px;line-height:1.6;color:#374151;">
                                Use the verification code below to finish creating your Biznie account. This code expires in {{ $expiresInMinutes }} minutes.
                            </p>
                            <p style="margin:24px 0;text-align:center;">
                                <span style="display:inline-block;background:#fff7f7;border:1px dashed #CC1414;color:#CC1414;font-size:32px;font-weight:700;letter-spacing:10px;padding:16px 28px;border-radius:10px;">
                                    {{ $otp }}
                                </span>
                            </p>
                            <p style="margin:0;font-size:12px;color:#6b7280;line-height:1.6;">
                                If you didn’t try to register on Biznie, you can safely ignore this email.
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
