<!doctype html>
<html>
<body style="font-family:Arial,sans-serif;background:#f7f7f7;margin:0;padding:24px;color:#111827;">
  <div style="max-width:640px;margin:0 auto;background:#fff;border:1px solid #e5e7eb;border-radius:16px;padding:24px;">
    <h2 style="margin:0 0 16px;color:#cc1414;">Seller Request Received</h2>
    <p style="margin:0 0 12px;">Hi {{ $requestRecord->contact_person }}, your seller request has been submitted successfully.</p>
    <p style="margin:0 0 12px;">Reference: <strong>{{ $requestRecord->request_reference }}</strong></p>
    <p style="margin:0;">Our team will review it and update the status soon.</p>
  </div>
</body>
</html>
