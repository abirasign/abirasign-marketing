<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>AbiraSign — Payment confirmed</title>
  <style>
    *, *::before, *::after { box-sizing: border-box; }
    body { margin:0; padding:0; background:#f5f5f3; font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif; color:#2C2C2A; }
    .wrap { max-width:580px; margin:0 auto; padding:60px 20px; text-align:center; }
    .logo { font-size:22px; font-weight:600; color:#534AB7; margin-bottom:40px; display:block; }
    .logo span { color:#AFA9EC; }
    .card { background:#fff; border-radius:12px; border:1px solid #D3D1C7; padding:40px 32px; }
    .icon { font-size:48px; margin-bottom:16px; }
    h1 { font-size:24px; font-weight:700; margin:0 0 12px; }
    p { font-size:14px; color:#5F5E5A; line-height:1.7; margin:0 0 16px; }
    .detail { background:#F1EFE8; border-radius:8px; padding:16px 20px; margin:20px 0; text-align:left; }
    .detail p { margin:0 0 6px; font-size:13px; }
    .detail p:last-child { margin:0; }
    .hipaa-note { background:#ede9ff; border-radius:8px; padding:14px 18px; font-size:13px; color:#534AB7; font-weight:500; margin-top:16px; }
    .footer { margin-top:32px; font-size:12px; color:#888780; }
  </style>
</head>
<body>
<div class="wrap">
  <span class="logo"><span>Abira</span>Sign</span>

  <div class="card">
    <div class="icon">✅</div>
    <h1>Payment confirmed!</h1>
    <p>Thank you — your payment has been received. Your AbiraSign Enterprise account is being set up and you'll receive a welcome email with next steps shortly.</p>

    @if($quote)
      <div class="detail">
        <p><strong>Client:</strong> {{ $quote->client_name }}</p>
        <p><strong>Quote ID:</strong> {{ $quote->quote_id }}</p>
        <p><strong>Billing term:</strong> {{ $quote->billing_term === 'triennial' ? 'Triennial (3 years)' : 'Annual (1 year)' }}</p>
        <p><strong>Annual total:</strong> ${{ number_format($quote->annual_total, 2) }}</p>
      </div>

      @if($quote->hipaa_required)
        <div class="hipaa-note">
          🔒 A Business Associate Agreement (BAA) will be sent to {{ $quote->contact_email }} for signature before your account goes live.
        </div>
      @endif
    @endif

    <p style="margin-top:24px;">Questions? Contact us at <a href="mailto:support@abirasign.com" style="color:#534AB7;">support@abirasign.com</a></p>
  </div>

  <div class="footer">&copy; AbiraSign &mdash; a product of BrightNet Technologies LLC</div>
</div>
</body>
</html>
