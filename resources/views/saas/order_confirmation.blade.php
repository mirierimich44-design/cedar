<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Order Received — {{ config('app.name') }}</title>
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f8fafc; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 24px; }
.card { background: white; border-radius: 20px; padding: 48px 40px; max-width: 520px; width: 100%; text-align: center; box-shadow: 0 10px 40px rgba(0,0,0,0.08); }
.icon { width: 72px; height: 72px; background: #d1fae5; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 24px; }
.icon svg { width: 36px; height: 36px; }
h1 { font-size: 1.6rem; font-weight: 800; color: #0f172a; margin-bottom: 10px; }
p { color: #64748b; font-size: 0.95rem; line-height: 1.6; margin-bottom: 8px; }
.invoice-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; margin: 24px 0; text-align: left; }
.inv-row { display: flex; justify-content: space-between; padding: 6px 0; font-size: 0.875rem; border-bottom: 1px solid #f1f5f9; }
.inv-row:last-child { border: none; font-weight: 700; font-size: 1rem; color: #0f172a; padding-top: 12px; }
.btn { display: inline-block; background: linear-gradient(135deg, #0f766e, #0369a1); color: white !important; padding: 14px 32px; border-radius: 12px; font-weight: 700; text-decoration: none; margin-top: 8px; font-size: 0.95rem; }
</style>
</head>
<body>
<div class="card">
    <div class="icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M20 6L9 17l-5-5"/>
        </svg>
    </div>
    <h1>Order Received!</h1>
    <p>Thank you. We'll contact you within <strong>24 hours</strong> to complete your setup.</p>

    <div class="invoice-box">
        <div class="inv-row"><span>Invoice</span><span>{{ $invoice->invoice_no }}</span></div>
        <div class="inv-row"><span>Billing</span><span style="text-transform:capitalize;">{{ $sub->billing_cycle }}</span></div>
        <div class="inv-row"><span>Hosting</span><span>{{ $sub->hosting_type === 'cloud' ? 'Cloud' : 'Self-Hosted' }}</span></div>
        @foreach($features as $f)
        <div class="inv-row"><span>{{ $f->name }}</span><span>KES {{ number_format($f->priceFor($sub->billing_cycle), 0) }}</span></div>
        @endforeach
        <div class="inv-row"><span>Total Due</span><span style="color:#0d9488;">KES {{ number_format($total, 0) }}</span></div>
    </div>

    <p style="font-size:0.82rem;">Payment can be made via <strong>M-Pesa</strong>, bank transfer, or card when our team contacts you.</p>
    <a href="{{ url('/') }}" class="btn">Back to Home</a>
</div>
</body>
</html>
