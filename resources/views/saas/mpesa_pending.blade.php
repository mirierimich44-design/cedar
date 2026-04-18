<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Complete Payment — {{ config('app.name') }}</title>
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #f1f5f9; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 24px; color: #0f172a; }
.card { background: #fff; border-radius: 20px; padding: 44px 36px; max-width: 540px; width: 100%; text-align: center; box-shadow: 0 10px 40px rgba(2,6,23,0.08); border: 1px solid #e2e8f0; }
.mpesa-logo { width: 80px; height: 80px; background: linear-gradient(135deg, #10b981, #059669); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 22px; color: #fff; font-weight: 900; font-size: 1rem; letter-spacing: 0.5px; box-shadow: 0 8px 22px rgba(16,185,129,0.35); }
h1 { font-size: 1.55rem; font-weight: 800; margin-bottom: 10px; }
.sub { color: #64748b; font-size: 0.95rem; line-height: 1.55; margin-bottom: 22px; }
.steps { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 14px; padding: 20px; text-align: left; margin-bottom: 22px; }
.step { display: flex; gap: 12px; padding: 8px 0; font-size: 0.9rem; color: #334155; }
.step .n { flex: 0 0 24px; height: 24px; background: #0f766e; color: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.75rem; font-weight: 700; }
.amount-box { background: linear-gradient(135deg, #ecfdf5, #f0fdfa); border: 1px solid #a7f3d0; border-radius: 14px; padding: 18px; margin-bottom: 22px; }
.amount-box .label { font-size: 0.78rem; color: #047857; text-transform: uppercase; letter-spacing: 0.6px; font-weight: 700; }
.amount-box .amount { font-size: 2rem; font-weight: 800; color: #065f46; margin-top: 4px; }
.poll { display: flex; align-items: center; justify-content: center; gap: 10px; padding: 14px; background: #fffbeb; border: 1px solid #fde68a; border-radius: 12px; color: #92400e; font-size: 0.88rem; font-weight: 600; margin-bottom: 16px; }
.spinner { width: 16px; height: 16px; border: 2px solid #fcd34d; border-top-color: #b45309; border-radius: 50%; animation: spin 0.8s linear infinite; }
@keyframes spin { to { transform: rotate(360deg); } }
.info-row { display: flex; justify-content: space-between; padding: 6px 0; font-size: 0.85rem; color: #475569; border-bottom: 1px dashed #e2e8f0; }
.info-row:last-child { border: none; }
.btn-skip { display: inline-block; margin-top: 14px; color: #64748b; font-size: 0.82rem; text-decoration: none; border-bottom: 1px dotted #94a3b8; }
.success-state { display: none; }
.success-state.show { display: block; }
.poll.done { background: #d1fae5; border-color: #6ee7b7; color: #065f46; }
.poll.done .spinner { border-color: #6ee7b7; border-top-color: #059669; animation: none; background: #10b981; border-radius: 50%; }
</style>
</head>
<body>
<div class="card">
    <div class="mpesa-logo">M-PESA</div>
    <h1>Complete Your Payment</h1>
    <p class="sub">We're waiting for your M-Pesa payment confirmation. Keep this page open — we'll detect it automatically.</p>

    <div class="amount-box">
        <div class="label">Amount Due</div>
        <div class="amount">KES {{ number_format($invoice->amount, 0) }}</div>
    </div>

    <div class="steps">
        <div class="step"><span class="n">1</span><span>On your phone, open <strong>M-PESA</strong> → Lipa na M-Pesa → Paybill.</span></div>
        <div class="step"><span class="n">2</span><span>Business No: <strong>{{ \App\SaasSetting::mpesaPaybill() }}</strong></span></div>
        <div class="step"><span class="n">3</span><span>Account No: <strong>{{ $invoice->invoice_no }}</strong></span></div>
        <div class="step"><span class="n">4</span><span>Amount: <strong>KES {{ number_format($invoice->amount, 0) }}</strong> → Enter PIN → Send.</span></div>
    </div>

    <div id="poll" class="poll">
        <div class="spinner"></div>
        <span id="poll-text">Waiting for payment confirmation…</span>
    </div>

    <div class="info-row"><span>Invoice</span><strong>{{ $invoice->invoice_no }}</strong></div>
    <div class="info-row"><span>Plan</span><strong style="text-transform:capitalize;">{{ $invoice->subscription->billing_cycle ?? '-' }}</strong></div>
    <div class="info-row"><span>Status</span><strong id="inv-status" style="text-transform:capitalize;">{{ $invoice->status }}</strong></div>

    <a href="{{ url('/home') }}" class="btn-skip">Skip for now — I'll pay later</a>
</div>

<script>
const statusUrl = "{{ route('saas.mpesa.status', ['invoice' => $invoice->id]) }}";
const pollEl   = document.getElementById('poll');
const textEl   = document.getElementById('poll-text');
const invEl    = document.getElementById('inv-status');

async function poll() {
    try {
        const r = await fetch(statusUrl, { headers: { 'Accept': 'application/json' } });
        if (!r.ok) return;
        const j = await r.json();
        invEl.textContent = j.status;
        if (j.paid) {
            pollEl.classList.add('done');
            textEl.textContent = 'Payment received! Redirecting…';
            setTimeout(() => window.location.href = "{{ url('/home') }}", 1500);
            return;
        }
    } catch (e) { /* ignore transient errors */ }
    setTimeout(poll, 4000);
}
poll();
</script>
</body>
</html>
