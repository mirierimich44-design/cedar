<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Checkout — {{ config('app.name') }}</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
<style>
*, *::before, *::after { box-sizing:border-box; margin:0; padding:0; }
body { font-family:'Inter',-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif; background:linear-gradient(180deg,#f8fafc,#eef2f7); color:#0f172a; min-height:100vh; }
.pnav { background:#fff; border-bottom:1px solid #e2e8f0; padding:0 40px; height:60px; display:flex; align-items:center; justify-content:space-between; }
.pnav-brand { display:flex; align-items:center; gap:10px; text-decoration:none; }
.pnav-brand img { width:30px; height:30px; border-radius:7px; background:#fff; padding:2px; object-fit:contain; box-shadow:0 1px 3px rgba(0,0,0,.06); }
.pnav-brand span { color:#0f172a; font-weight:800; font-size:1rem; }
.pnav a { color:#64748b; font-size:.88rem; text-decoration:none; font-weight:500; }
.pnav a:hover { color:#0d9488; }
.container { max-width:1020px; margin:36px auto; padding:0 24px; display:grid; grid-template-columns:1fr 360px; gap:28px; align-items:start; }
@media (max-width:900px) { .container { grid-template-columns:1fr; } }
.card { background:#fff; border:1px solid #e2e8f0; border-radius:16px; padding:28px; box-shadow:0 4px 20px -8px rgba(15,23,42,.06); }
.card h2 { font-size:1.15rem; font-weight:800; color:#0f172a; margin-bottom:6px; letter-spacing:-.01em; }
.card .sub { color:#64748b; font-size:.88rem; margin-bottom:22px; line-height:1.5; }
.grid-2 { display:grid; grid-template-columns:1fr 1fr; gap:12px; }
@media (max-width:520px) { .grid-2 { grid-template-columns:1fr; } }
.form-group { margin-bottom:16px; }
label { display:block; font-size:.82rem; font-weight:600; color:#334155; margin-bottom:6px; }
label .req { color:#ef4444; }
input, select { width:100%; height:44px; border:1.5px solid #cbd5e1; border-radius:9px; padding:0 13px; font-size:.92rem; color:#0f172a; background:#fff; outline:none; transition:all .18s; font-family:inherit; }
input:focus, select:focus { border-color:#0d9488; box-shadow:0 0 0 3px rgba(13,148,136,.12); }
.hint { font-size:.74rem; color:#94a3b8; margin-top:4px; }

/* Plan picker */
.plans { display:grid; grid-template-columns:1fr 1fr; gap:12px; margin:10px 0 6px; }
@media (max-width:520px) { .plans { grid-template-columns:1fr; } }
.plan { background:#fff; border:2px solid #e2e8f0; border-radius:12px; padding:18px; cursor:pointer; transition:.18s; position:relative; }
.plan:hover { border-color:#0d9488; }
.plan.selected { border-color:#0d9488; background:#f0fdfa; box-shadow:0 0 0 1px #0d9488; }
.plan.selected::after { content:'✓'; position:absolute; top:10px; right:12px; width:22px; height:22px; background:#0d9488; color:#fff; border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:900; font-size:.78rem; }
.plan .ptop { display:flex; align-items:center; gap:10px; margin-bottom:8px; }
.plan .pic { font-size:1.2rem; }
.plan h3 { font-size:.98rem; font-weight:800; color:#0f172a; margin:0; }
.plan .psub { color:#64748b; font-size:.8rem; line-height:1.5; margin:0 0 8px; }
.plan .ppay { color:#0d9488; font-size:.82rem; font-weight:700; }
.plan.pay-now .ppay { color:#047857; }
.plan .pbadge { position:absolute; top:-9px; left:16px; background:#0d9488; color:#fff; font-size:.62rem; font-weight:800; letter-spacing:.6px; padding:3px 8px; border-radius:50px; text-transform:uppercase; }

/* CTA */
.cta { width:100%; height:52px; background:linear-gradient(135deg,#0d9488,#0f766e); border:none; border-radius:12px; color:#fff; font-size:1rem; font-weight:800; cursor:pointer; transition:all .2s; margin-top:10px; font-family:inherit; box-shadow:0 8px 20px -6px rgba(13,148,136,.45); }
.cta:hover { transform:translateY(-1px); box-shadow:0 12px 24px -6px rgba(13,148,136,.55); }
.cta .save-tag { background:rgba(255,255,255,.25); font-size:.72rem; padding:3px 8px; border-radius:50px; margin-left:8px; font-weight:700; }
.trust { text-align:center; margin-top:14px; font-size:.78rem; color:#64748b; line-height:1.6; }
.trust i { color:#10b981; margin-right:5px; }

/* Summary card */
.order-item { display:flex; justify-content:space-between; padding:9px 0; border-bottom:1px solid #f1f5f9; font-size:.86rem; color:#334155; }
.order-item:last-of-type { border:none; }
.order-item b { color:#0f172a; }
.order-total { display:flex; justify-content:space-between; font-weight:800; font-size:1.1rem; padding-top:14px; border-top:2px solid #e2e8f0; margin-top:10px; color:#0f172a; }
.badge { display:inline-block; background:#dbeafe; color:#1d4ed8; font-size:.7rem; font-weight:700; padding:3px 10px; border-radius:50px; margin-right:6px; margin-bottom:14px; text-transform:capitalize; }
.badge.hosting { background:#f0fdfa; color:#047857; }
.back-link { display:inline-flex; align-items:center; gap:6px; color:#64748b; font-size:.85rem; text-decoration:none; margin-bottom:20px; font-weight:500; }
.back-link:hover { color:#0d9488; }

/* Errors */
.errors { background:#fee2e2; border:1px solid #fca5a5; color:#b91c1c; padding:12px 14px; border-radius:10px; margin-bottom:16px; font-size:.85rem; }
.errors ul { list-style:none; margin:0; padding:0; }
.errors li { margin:2px 0; }
</style>
</head>
<body>
<nav class="pnav">
    <a href="{{ url('/') }}" class="pnav-brand">
        <img src="{{ asset('img/logo-small.png') }}" alt="logo">
        <span>{{ config('app.name') }}</span>
    </a>
    <a href="{{ route('saas.pricing') }}"><i class="fas fa-arrow-left"></i> Back to wizard</a>
</nav>

<div class="container">
    <div>
        <a href="{{ route('saas.pricing') }}" class="back-link"><i class="fas fa-arrow-left"></i> Change features</a>
        <div class="card">
            <h2>Create your account</h2>
            <p class="sub">You'll be logged in immediately. No credit card required.</p>

            @if($errors->any())
            <div class="errors">
                <ul>@foreach($errors->all() as $e)<li>• {{ $e }}</li>@endforeach</ul>
            </div>
            @endif

            <form method="POST" action="{{ route('saas.order.submit') }}" id="checkout-form">
                @csrf
                <input type="hidden" name="cycle" value="{{ $cycle }}">
                <input type="hidden" name="hosting" value="{{ $hosting }}">
                <input type="hidden" name="total" value="{{ $total }}">
                <input type="hidden" name="biz_type" value="{{ request('biz') }}">
                <input type="hidden" name="action" id="action-input" value="trial">
                @foreach($featureIds as $fid)
                <input type="hidden" name="feature_ids[]" value="{{ $fid }}">
                @endforeach

                <div class="form-group">
                    <label>Business / Company Name <span class="req">*</span></label>
                    <input type="text" name="business_name" required value="{{ old('business_name') }}" placeholder="e.g. Apex Pharmacy">
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label>Your First Name <span class="req">*</span></label>
                        <input type="text" name="first_name" required value="{{ old('first_name') }}" placeholder="Jane">
                    </div>
                    <div class="form-group">
                        <label>Your Last Name</label>
                        <input type="text" name="last_name" value="{{ old('last_name') }}" placeholder="Doe">
                    </div>
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label>Email <span class="req">*</span></label>
                        <input type="email" name="email" required value="{{ old('email') }}" placeholder="you@business.co.ke">
                    </div>
                    <div class="form-group">
                        <label>M-Pesa Phone <span class="req">*</span></label>
                        <input type="text" name="phone" required value="{{ old('phone') }}" placeholder="0712 345 678">
                    </div>
                </div>

                <div class="form-group">
                    <label>Set a Password <span class="req">*</span></label>
                    <input type="password" name="password" required minlength="6" placeholder="At least 6 characters">
                    <div class="hint">You'll use this to sign in.</div>
                </div>

                <div style="margin:22px 0 8px;">
                    <label style="font-size:.92rem; margin-bottom:10px; color:#0f172a; font-weight:700;">How would you like to start?</label>
                    <div class="plans">
                        <div class="plan selected" data-action="trial" onclick="pickPlan(this)">
                            <span class="pbadge">Recommended</span>
                            <div class="ptop"><span class="pic">🎉</span><h3>Start Free 3-Day Trial</h3></div>
                            <p class="psub">Full access to everything you picked. No card, no payment now.</p>
                            <div class="ppay">Pay nothing today</div>
                        </div>
                        <div class="plan pay-now" data-action="pay_now" onclick="pickPlan(this)">
                            <div class="ptop"><span class="pic">💳</span><h3>Activate Now (M-Pesa)</h3></div>
                            <p class="psub">Skip the trial. STK push sent to your phone — done in 10 seconds.</p>
                            <div class="ppay">Pay KES {{ number_format($total, 0) }} now</div>
                        </div>
                    </div>
                </div>

                <button type="submit" class="cta" id="cta-btn">
                    Start My 3-Day Free Trial <i class="fas fa-arrow-right"></i>
                </button>

                <div class="trust">
                    <div><i class="fas fa-check-circle"></i> Your account is created instantly</div>
                    <div><i class="fas fa-check-circle"></i> Cancel any time — we only charge when you're ready</div>
                    <div><i class="fas fa-check-circle"></i> M-Pesa / card / bank transfer accepted</div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <h2>Your Plan</h2>
        <div style="margin-bottom:4px;">
            <span class="badge">{{ ucfirst($cycle) }}</span>
            <span class="badge hosting">{{ $hosting === 'cloud' ? '☁️ Cloud' : '🖥️ On-Premise' }}</span>
        </div>

        @foreach($features as $feature)
        <div class="order-item">
            <span>{{ $feature->name }}</span>
            <b>{{ $feature->priceFor($cycle) == 0 ? 'Free' : 'KES ' . number_format($feature->priceFor($cycle), 0) }}</b>
        </div>
        @endforeach

        <div class="order-total">
            <span>Total</span>
            <span style="color:#0d9488;">KES {{ number_format($total, 0) }}</span>
        </div>

        @if($cycle !== 'once')
        <p style="font-size:.78rem; color:#64748b; margin-top:10px;">Billed {{ $cycle }} after your trial. Cancel anytime.</p>
        @else
        <p style="font-size:.78rem; color:#64748b; margin-top:10px;">One-off payment. Annual hosting fee billed separately.</p>
        @endif
    </div>
</div>

<script>
function pickPlan(el) {
    document.querySelectorAll('.plan').forEach(p => p.classList.remove('selected'));
    el.classList.add('selected');
    const action = el.dataset.action;
    document.getElementById('action-input').value = action;
    const btn = document.getElementById('cta-btn');
    if (action === 'trial') {
        btn.innerHTML = 'Start My 3-Day Free Trial <i class="fas fa-arrow-right"></i>';
    } else {
        btn.innerHTML = 'Pay KES {{ number_format($total, 0) }} via M-Pesa <i class="fas fa-mobile-alt"></i>';
    }
}
</script>
</body>
</html>
