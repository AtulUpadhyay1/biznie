<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body style="margin: 0; padding: 0; font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #f5f5f5; line-height: 1.5; padding: 15px 0;">
    <div style="max-width: 580px; margin: 0 auto; background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 8px 32px rgba(220, 53, 69, 0.12); border: 1px solid rgba(220, 53, 69, 0.1);">

        <!-- Header -->
        <div style="background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); padding: 24px 20px; text-align: center; color: white; position: relative;">
            <div style="display: inline-flex; align-items: center; justify-content: center; width: 48px; height: 48px; background: rgba(255,255,255,0.2); border-radius: 12px; margin-bottom: 12px; backdrop-filter: blur(8px);">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 2L15.09 8.26L22 9L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9L8.91 8.26L12 2Z" fill="currentColor"/>
                </svg>
            </div>
            <h1 style="margin: 0; font-size: 24px; font-weight: 700; letter-spacing: -0.5px;">{{ config('app.name', 'Biznie') }}</h1>
            <p style="margin: 6px 0 0 0; font-size: 14px; opacity: 0.9; font-weight: 400;">Email Verification</p>
        </div>

        <!-- Content -->
        <div style="padding: 32px 24px; text-align: center; background: #ffffff;">
            <div style="color: #2d2d2d; font-size: 22px; font-weight: 600; margin-bottom: 10px; letter-spacing: -0.3px;">
                Hello {{ $userName ?? 'Valued User' }}!
            </div>

            <div style="font-size: 15px; color: #666; margin-bottom: 24px; line-height: 1.5; max-width: 440px; margin-left: auto; margin-right: auto;">
                We've received a request to verify your email address. Please use the verification code below to complete your verification process.
            </div>

            <!-- OTP Container -->
            <div style="background: linear-gradient(135deg, #fff5f5 0%, #fef2f2 100%); border: 2px solid #dc3545; border-radius: 12px; padding: 24px 20px; margin: 24px auto; display: inline-block; position: relative; overflow: hidden; max-width: 280px;">
                <div style="position: absolute; top: -15px; right: -15px; width: 40px; height: 40px; background: radial-gradient(circle, rgba(220, 53, 69, 0.15) 0%, transparent 70%); border-radius: 50%;"></div>
                <div style="position: relative; z-index: 2;">
                    <div style="font-size: 12px; color: #666; margin-bottom: 12px; text-transform: uppercase; letter-spacing: 0.8px; font-weight: 500;">
                        Verification Code
                    </div>
                    <div style="font-size: 36px; font-weight: 800; color: #dc3545; letter-spacing: 6px; font-family: 'Inter', monospace; margin: 12px 0; line-height: 1; text-align: center;">
                        {{ $otp }}
                    </div>
                    <div style="font-size: 13px; color: #666; margin-top: 12px; font-weight: 500;">
                        Expires in {{ $expiryMinutes ?? 10 }} minutes
                    </div>
                </div>
            </div>

            <!-- Instructions -->
            <div style="background: #fff8f8; border: 2px solid #fecaca; border-radius: 12px; padding: 20px; margin: 24px auto; text-align: left; max-width: 440px;">
                <h3 style="margin: 0 0 12px 0; color: #dc3545; font-size: 16px; display: flex; align-items: center; font-weight: 600;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="margin-right: 6px;">
                        <path d="M13 2L3 14H12L11 22L21 10H12L13 2Z" fill="currentColor"/>
                    </svg>
                    Quick Steps
                </h3>
                <div style="color: #666; font-size: 14px; line-height: 1.5;">
                    <div style="display: flex; align-items: flex-start; margin-bottom: 8px;">
                        <span style="background: #dc3545; color: white; border-radius: 50%; width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 600; margin-right: 10px; flex-shrink: 0;">1</span>
                        <span>Copy the verification code above</span>
                    </div>
                    <div style="display: flex; align-items: flex-start; margin-bottom: 8px;">
                        <span style="background: #dc3545; color: white; border-radius: 50%; width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 600; margin-right: 10px; flex-shrink: 0;">2</span>
                        <span>Return to the verification page</span>
                    </div>
                    <div style="display: flex; align-items: flex-start; margin-bottom: 8px;">
                        <span style="background: #dc3545; color: white; border-radius: 50%; width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 600; margin-right: 10px; flex-shrink: 0;">3</span>
                        <span>Enter the code in the verification field</span>
                    </div>
                    <div style="display: flex; align-items: flex-start;">
                        <span style="background: #dc3545; color: white; border-radius: 50%; width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 600; margin-right: 10px; flex-shrink: 0;">4</span>
                        <span>Click <strong style="color: #dc3545;">"Verify"</strong> to complete</span>
                    </div>
                </div>
            </div>

            <!-- Security Notice -->
            <div style="background: linear-gradient(135deg, #fff5f5 0%, #fef2f2 100%); border-left: 4px solid #dc3545; border-radius: 10px; padding: 18px 22px; margin: 24px auto; text-align: left; max-width: 440px; box-shadow: 0 2px 8px rgba(220, 53, 69, 0.1);">
                <h3 style="margin: 0 0 10px 0; color: #dc3545; font-size: 16px; display: flex; align-items: center; font-weight: 600;">
                    <span style="font-size: 16px; margin-right: 6px;">🛡️</span>
                    Security Notice
                </h3>
                <div style="color: #555; font-size: 14px; line-height: 1.5;">
                    <p style="margin: 0 0 8px 0;"><strong style="color: #dc3545;">🚫 Never share this code</strong> with anyone. Our support team will never ask for your verification code.</p>
                    <p style="margin: 0 0 8px 0;">❗ If you didn't request this verification, please ignore this email or contact our support team immediately.</p>
                    <p style="margin: 0; background: rgba(220, 53, 69, 0.08); padding: 8px; border-radius: 6px;">⚡ This code can only be used <strong style="color: #dc3545;">once</strong> and will expire automatically for your security.</p>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div style="background: #fafafa; padding: 24px 20px; text-align: center; border-top: 1px solid #f0f0f0;">
            <div style="margin-bottom: 16px;">
                <p style="margin: 0 0 6px 0; color: #333; font-size: 14px; font-weight: 500;">
                    This is an automated message from
                    <span style="font-weight: 700; color: #dc3545;">{{ config('app.name', 'Biznie') }}</span>
                </p>
                <p style="margin: 0; color: #666; font-size: 13px;">Please do not reply to this email address.</p>
            </div>

            <div style="border-top: 1px solid #f0f0f0; padding-top: 16px;">
                <div style="background: #fff5f5; border: 1px solid #fecaca; border-radius: 8px; padding: 16px; margin-bottom: 16px;">
                    <p style="margin: 0 0 6px 0; color: #dc3545; font-size: 14px; font-weight: 600;">
                        Need assistance? We're here to help!
                    </p>
                    <p style="margin: 0; color: #666; font-size: 13px;">
                        Email: <a href="mailto:{{ 'support@'.config('app.domain', 'biznie.com') }}" style="color: #dc3545; text-decoration: none; font-weight: 500;">{{ 'support@'.config('app.domain', 'biznie.com') }}</a>
                    </p>
                </div>

                <div style="color: #999; font-size: 12px; line-height: 1.4;">
                    <p style="margin: 0 0 3px 0;">
                        <span style="font-weight: 600; color: #dc3545;">{{ config('app.name', 'Biznie') }}</span> - Email Verification System
                    </p>
                    <p style="margin: 0;">
                        &copy; {{ date('Y') }} {{ config('app.name', 'Biznie') }}. All rights reserved.
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
