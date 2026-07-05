<!doctype html>
<html>
<body style="font-family:Arial,sans-serif;background:#f7f7f7;margin:0;padding:24px;color:#111827;">
  <div style="max-width:640px;margin:0 auto;background:#fff;border:1px solid #e5e7eb;border-radius:16px;padding:24px;">
    <h2 style="margin:0 0 16px;color:#cc1414;">New Seller Request Submitted</h2>
    <p style="margin:0 0 12px;">A new seller request has been submitted on Biznie.</p>
    <table style="width:100%;border-collapse:collapse;">
      <tr><td style="padding:6px 0;font-weight:bold;">Reference</td><td>{{ $requestRecord->request_reference }}</td></tr>
      <tr><td style="padding:6px 0;font-weight:bold;">Name</td><td>{{ $requestRecord->contact_person }}</td></tr>
      <tr><td style="padding:6px 0;font-weight:bold;">Company</td><td>{{ $requestRecord->company_name }}</td></tr>
      <tr><td style="padding:6px 0;font-weight:bold;">Mobile</td><td>{{ $requestRecord->mobile }}</td></tr>
      <tr><td style="padding:6px 0;font-weight:bold;">Status</td><td>{{ strtoupper($requestRecord->request_status) }}</td></tr>
    </table>
    <p style="margin:18px 0 0;">Please review and approve/reject from the admin panel.</p>
  </div>
</body>
</html>
