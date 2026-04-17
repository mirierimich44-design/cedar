<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Checkout — {{ config('app.name') }}</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f8fafc; color: #1e293b; }
.pnav { background: #0f172a; padding: 0 48px; height: 64px; display: flex; align-items: center; justify-content: space-between; }
.pnav-brand { display: flex; align-items: center; gap: 10px; text-decoration: none; }
.pnav-brand img { width: 32px; height: 32px; border-radius: 8px; background: white; padding: 3px; object-fit: contain; }
.pnav-brand span { color: white; font-size: 1rem; font-weight: 800; }
.pnav a { color: rgba(255,255,255,0.7); font-size: 0.875rem; text-decoration: none; }
.container { max-width: 960px; margin: 48px auto; padding: 0 24px; display: grid; grid-template-columns: 1fr 360px; gap: 32px; align-items: start; }
@media (max-width: 768px) { .container { grid-template-columns: 1fr; } }
.card { background: white; border: 1px solid #e2e8f0; border-radius: 16px; padding: 28px; }
.card h2 { font-size: 1.2rem; font-weight: 800; color: #0f172a; margin-bottom: 22px; }
.form-group { margin-bottom: 18px; }
label { display: block; font-size: 0.84rem; font-weight: 600; color: #374151; margin-bottom: 6px; }
input, select { width: 100%; height: 46px; border: 1.5px solid #cbd5e1; border-radius: 10px; padding: 0 14px; font-size: 0.93rem; color: #0f172a; background: white; outline: none; transition: border-color 0.18s; }
input:focus, select:focus { border-color: #0d9488; box-shadow: 0 0 0 3px rgba(13,148,136,0.1); }
.submit-btn { width: 100%; height: 52px; background: linear-gradient(135deg, #0f766e, #0369a1); border: none; border-radius: 12px; color: white; font-size: 1rem; font-weight: 700; cursor: pointer; transition: all 0.2s; margin-top: 8px; }
.submit-btn:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(15,118,110,0.4); }
.order-item { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #f1f5f9; font-size: 0.875rem; }
.order-item:last-of-type { border: none; }
.order-total { display: flex; justify-content: space-between; font-weight: 800; font-size: 1.1rem; padding-top: 14px; border-top: 2px solid #e2e8f0; margin-top: 10px; }
.badge-cycle { display: inline-block; background: #dbeafe; color: #1d4ed8; font-size: 0.72rem; font-weight: 700; padding: 3px 10px; border-radius: 50px; margin-bottom: 16px; text-transform: capitalize; }
.badge-hosting { display: inline-block; background: #f0fdfa; color: #065f46; font-size: 0.72rem; font-weight: 700; padding: 3px 10px; border-radius: 50px; margin-bottom: 16px; margin-left: 6px; }
.back-link { display: inline-flex; align-items: center; gap: 6px; color: #64748b; font-size: 0.875rem; text-decoration: none; margin-bottom: 24px; }
.back-link:hover { color: #0d9488; }
</style>
</head>
<body>
<nav class="pnav">
    <a href="{{ url('/') }}" class="pnav-brand">
        <img src="{{ asset('img/logo-small.png') }}" alt="logo">
        <span>{{ config('app.name') }}</span>
    </a>
    <a href="{{ route('saas.pricing') }}">← Back to Pricing</a>
</nav>

<div class="container">
    <div>
        <a href="{{ route('saas.pricing') }}" class="back-link"><i class="fas fa-arrow-left"></i> Change features</a>
        <div class="card">
            <h2>Your Details</h2>
            <form method="POST" action="{{ route('saas.order.submit') }}">
                @csrf
                <input type="hidden" name="cycle" value="{{ $cycle }}">
                <input type="hidden" name="hosting" value="{{ $hosting }}">
                <input type="hidden" name="total" value="{{ $total }}">
                @foreach($featureIds as $fid)
                <input type="hidden" name="feature_ids[]" value="{{ $fid }}">
                @endforeach

                <div class="form-group">
                    <label>Business / Company Name *</label>
                    <input type="text" name="business_name" required placeholder="e.g. Reenson Pharmacy">
                </div>
                <div class="form-group">
                    <label>Email Address *</label>
                    <input type="email" name="email" required placeholder="you@example.com">
                </div>
                <div class="form-group">
                    <label>Phone Number *</label>
                    <input type="text" name="phone" required placeholder="+254 700 000 000">
                </div>
                <div class="form-group">
                    <label>Country</label>
                    <select name="country">
                        <option value="KE" selected>Kenya</option>
                        <option value="UG">Uganda</option>
                        <option value="TZ">Tanzania</option>
                        <option value="RW">Rwanda</option>
                        <option value="OTHER">Other</option>
                    </select>
                </div>
                <button type="submit" class="submit-btn">Submit Order →</button>
                <p style="text-align:center; margin-top:12px; font-size:0.78rem; color:#94a3b8;">
                    We'll contact you within 24 hours to complete setup and payment.
                </p>
            </form>
        </div>
    </div>

    <div class="card">
        <h2>Order Summary</h2>
        <span class="badge-cycle">{{ ucfirst($cycle) }}</span>
        <span class="badge-hosting">{{ $hosting === 'cloud' ? '☁️ Cloud Hosted' : '🖥️ Self-Hosted' }}</span>

        @foreach($features as $feature)
        <div class="order-item">
            <span>{{ $feature->name }}</span>
            <span style="font-weight:600;">
                {{ $feature->priceFor($cycle) == 0 ? 'Free' : 'KES ' . number_format($feature->priceFor($cycle), 0) }}
            </span>
        </div>
        @endforeach

        <div class="order-total">
            <span>Total</span>
            <span style="color:#0d9488;">KES {{ number_format($total, 0) }}</span>
        </div>

        @if($cycle !== 'once')
        <p style="font-size:0.78rem; color:#64748b; margin-top:10px;">Billed {{ $cycle }}. Cancel anytime.</p>
        @else
        <p style="font-size:0.78rem; color:#64748b; margin-top:10px;">One-off payment. Annual hosting fee billed separately.</p>
        @endif
    </div>
</div>
</body>
</html>
