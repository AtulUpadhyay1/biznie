<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>New user registration on Biznie</title>
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
                            <h1 style="margin:0 0 16px 0;font-size:22px;color:#111827;">New user registered</h1>
                            <p style="margin:0 0 16px 0;font-size:14px;line-height:1.6;color:#374151;">
                                A new user has completed registration on Biznie.
                            </p>
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="font-size:14px;color:#374151;border-collapse:collapse;">
                                <tr>
                                    <td style="padding:8px 0;width:140px;font-weight:600;">Name</td>
                                    <td style="padding:8px 0;">{{ $user->name }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:8px 0;width:140px;font-weight:600;">Email</td>
                                    <td style="padding:8px 0;">{{ $user->email }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:8px 0;width:140px;font-weight:600;">Phone</td>
                                    <td style="padding:8px 0;">{{ $user->phone ?: 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:8px 0;width:140px;font-weight:600;">Registered At</td>
                                    <td style="padding:8px 0;">{{ $user->created_at?->format('d M Y, h:i A') }}</td>
                                </tr>
                            </table>
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
