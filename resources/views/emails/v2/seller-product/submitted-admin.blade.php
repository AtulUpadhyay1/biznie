<!doctype html>
<html>
<body style="font-family:Arial,sans-serif;background:#f7f7f7;margin:0;padding:24px;color:#111827;">
  <div style="max-width:640px;margin:0 auto;background:#fff;border:1px solid #e5e7eb;border-radius:16px;padding:24px;">
    <h2 style="margin:0 0 16px;color:#cc1414;">New Product Submitted</h2>
    <p style="margin:0 0 12px;">A seller has submitted a new product for review on Biznie.</p>
    <table style="width:100%;border-collapse:collapse;">
      <tr><td style="padding:6px 0;font-weight:bold;">Reference</td><td>{{ $requestRecord->request_reference }}</td></tr>
      <tr><td style="padding:6px 0;font-weight:bold;">Product</td><td>{{ $requestRecord->name }}</td></tr>
      <tr><td style="padding:6px 0;font-weight:bold;">Seller</td><td>{{ $requestRecord->getUser?->name ?? '--' }}</td></tr>
      <tr><td style="padding:6px 0;font-weight:bold;">HSN Code</td><td>{{ $requestRecord->hsn_code ?? '--' }}</td></tr>
      <tr><td style="padding:6px 0;font-weight:bold;">Status</td><td>{{ strtoupper(str_replace('_', ' ', $requestRecord->request_status)) }}</td></tr>
    </table>
    <p style="margin:18px 0 0;">Please review and approve/reject from the admin panel.</p>
  </div>
</body>
</html>
