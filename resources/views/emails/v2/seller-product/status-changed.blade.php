<!doctype html>
<html>
<body style="font-family:Arial,sans-serif;background:#f7f7f7;margin:0;padding:24px;color:#111827;">
  <div style="max-width:640px;margin:0 auto;background:#fff;border:1px solid #e5e7eb;border-radius:16px;padding:24px;">
    <h2 style="margin:0 0 16px;color:#cc1414;">Product Status Updated</h2>
    <p style="margin:0 0 12px;">Hi {{ $requestRecord->getUser?->name ?? 'Seller' }}, the status of your product <strong>{{ $requestRecord->name }}</strong> is now <strong>{{ strtoupper(str_replace('_', ' ', $requestRecord->request_status)) }}</strong>.</p>
    <p style="margin:0 0 12px;">Reference: <strong>{{ $requestRecord->request_reference }}</strong></p>
    @if($requestRecord->review_note)
      <p style="margin:0 0 12px;">Note: {{ $requestRecord->review_note }}</p>
    @endif
    <p style="margin:0;">If it was rejected, you can edit and resubmit the product from your seller dashboard.</p>
  </div>
</body>
</html>
