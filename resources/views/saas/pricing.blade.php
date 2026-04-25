@extends('layouts.auth2')
@section('title', 'Select Your Plan · ' . config('app.name', 'Apex POS'))

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
<style>
html, body, .main-wrapper, .right-col {
    background: linear-gradient(180deg, #f8fafc 0%, #eef2f7 100%) !important;
    margin:0 !important; padding:0 !important; border:none !important;
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
    color: #0f172a;
}
.wizard {
    max-width: 1100px;
    margin: 0 auto;
    padding: 30px 24px 100px;
}

/* TOP NAV */
.top-nav {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 40px;
    background: rgba(255, 255, 255, 0.8);
    backdrop-filter: blur(12px);
    position: sticky;
    top: 0;
    z-index: 100;
    border-bottom: 1px solid #e2e8f0;
}
.brand { display: flex; align-items: center; gap: 10px; text-decoration: none; }
.brand img { width: 32px; height: 32px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,.1); }
.brand span { font-weight: 800; color: #0f172a; font-size: 1.05rem; }
.nav-link { color: #64748b; font-weight: 600; text-decoration: none; font-size: .9rem; }
.nav-link:hover { color: #0d9488; }

/* STEPS */
.step-header { text-align: center; margin-bottom: 40px; animation: slideIn 0.4s ease; }
.step-header h1 { font-weight: 900; font-size: clamp(1.8rem, 3vw, 2.6rem); margin: 0 0 10px; letter-spacing: -0.02em; }
.step-header p { color: #64748b; font-size: 1.05rem; max-width: 500px; margin: 0 auto; line-height: 1.6; }
@keyframes slideIn { from{opacity:0; transform:translateY(15px);} to{opacity:1; transform:none;} }

.panel { display: none; }
.panel.active { display: block; }

/* BIZ GRID */
.biz-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; max-width: 900px; margin: 0 auto; }
@media(max-width: 768px) { .biz-grid { grid-template-columns: repeat(2, 1fr); } }
@media(max-width: 480px) { .biz-grid { grid-template-columns: 1fr; } }
.biz-card { background: #fff; border: 2px solid #e2e8f0; border-radius: 16px; padding: 30px 20px; text-align: center; cursor: pointer; transition: 0.2s; position: relative; }
.biz-card:hover { border-color: #0d9488; transform: translateY(-3px); box-shadow: 0 12px 24px -8px rgba(13,148,136,0.2); }
.biz-card.selected { border-color: #0d9488; background: #f0fdfa; box-shadow: 0 0 0 1px #0d9488; }
.biz-card.selected::after { content: '✓'; position: absolute; top: 12px; right: 12px; width: 24px; height: 24px; background: #0d9488; color: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: .8rem; }
.biz-icon { font-size: 2.8rem; margin-bottom: 14px; line-height: 1; }
.biz-card h3 { font-weight: 800; font-size: 1.1rem; margin: 0 0 8px; color: #0f172a; }
.biz-card p { font-size: .85rem; color: #64748b; margin: 0; line-height: 1.5; }

/* PRICING GRID */
.pricing-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; }
@media(max-width: 980px) { .pricing-grid { grid-template-columns: repeat(2, 1fr); } }
@media(max-width: 580px) { .pricing-grid { grid-template-columns: 1fr; } }

.plan-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 20px; padding: 32px 24px; display: flex; flex-direction: column; position: relative; box-shadow: 0 4px 12px rgba(15,23,42,0.03); transition: 0.3s; }
.plan-card:hover { transform: translateY(-5px); box-shadow: 0 16px 32px -10px rgba(15,23,42,0.1); border-color: #cbd5e1; }
.plan-card.popular { border: 2px solid #0d9488; box-shadow: 0 16px 32px -10px rgba(13,148,136,0.2); }
.plan-card.lifetime { background: linear-gradient(180deg, #0f172a, #1e293b); color: #fff; border: none; }
.plan-badge { position: absolute; top: -12px; left: 50%; transform: translateX(-50%); background: #0d9488; color: #fff; font-size: .7rem; font-weight: 800; padding: 5px 12px; border-radius: 50px; text-transform: uppercase; letter-spacing: 1px; white-space: nowrap; }

.plan-name { font-weight: 800; font-size: 1.1rem; margin-bottom: 12px; color: #0f172a; }
.plan-card.lifetime .plan-name { color: #fff; }
.plan-price { font-size: 2.2rem; font-weight: 900; color: #0d9488; letter-spacing: -0.03em; margin-bottom: 6px; }
.plan-card.lifetime .plan-price { color: #5eead4; }
.plan-cycle { font-size: .85rem; color: #64748b; font-weight: 600; margin-bottom: 24px; }
.plan-card.lifetime .plan-cycle { color: #94a3b8; }

.plan-features { flex: 1; list-style: none; margin: 0 0 28px; padding: 0; }
.plan-features li { display: flex; align-items: flex-start; gap: 10px; font-size: .88rem; color: #475569; margin-bottom: 12px; line-height: 1.5; }
.plan-card.lifetime .plan-features li { color: #cbd5e1; }
.plan-features i { color: #10b981; font-size: .8rem; margin-top: 4px; }

.btn-select { width: 100%; padding: 14px; border-radius: 12px; font-weight: 800; font-size: 1rem; border: none; cursor: pointer; transition: 0.2s; text-align: center; text-decoration: none; font-family: inherit; }
.btn-outline { background: #f1f5f9; color: #0f172a; }
.btn-outline:hover { background: #e2e8f0; }
.btn-solid { background: #0d9488; color: #fff; }
.btn-solid:hover { background: #0f766e; }
.plan-card.lifetime .btn-outline { background: rgba(255,255,255,0.1); color: #fff; border: 1px solid rgba(255,255,255,0.2); }
.plan-card.lifetime .btn-outline:hover { background: rgba(255,255,255,0.2); }

/* BOTTOM ACTIONS */
.actions { text-align: center; margin-top: 40px; animation: slideIn 0.5s ease; }
.btn-next { background: #0f172a; color: #fff; font-weight: 800; font-size: 1.05rem; padding: 16px 40px; border-radius: 50px; border: none; cursor: pointer; transition: 0.2s; box-shadow: 0 10px 24px -6px rgba(15,23,42,0.4); font-family: inherit; display: inline-flex; align-items: center; gap: 10px; }
.btn-next:hover:not(:disabled) { background: #1e293b; transform: translateY(-2px); }
.btn-next:disabled { opacity: 0.5; cursor: not-allowed; box-shadow: none; transform: none; }
.btn-back { display: inline-flex; align-items: center; gap: 8px; color: #64748b; font-weight: 600; font-size: .9rem; cursor: pointer; text-decoration: none; margin-bottom: 20px; transition: 0.2s; }
.btn-back:hover { color: #0f172a; }
</style>

<nav class="top-nav">
    <a href="{{ url('/') }}" class="brand">
        <img src="{{ asset('img/logo-small.png') }}" alt="logo">
        <span>{{ config('app.name') }}</span>
    </a>
    <a href="{{ url('/login') }}" class="nav-link">Sign In <i class="fas fa-arrow-right"></i></a>
</nav>

<div class="wizard">
    
    {{-- STEP 1: BUSINESS TYPE --}}
    <div class="panel active" id="step-1">
        <div class="step-header">
            <h1 style="color: #0f172a;">What type of business do you run?</h1>
            <p>We will tailor the platform and features to match your industry.</p>
        </div>

        <div class="biz-grid">
            <div class="biz-card" data-biz="retail" onclick="selectBiz('retail', this)">
                <div class="biz-icon">🏪</div>
                <h3>Retail / Shop</h3>
                <p>Supermarkets, Hardware, General Shops</p>
            </div>
            <div class="biz-card" data-biz="pharmacy" onclick="selectBiz('pharmacy', this)">
                <div class="biz-icon">💊</div>
                <h3>Pharmacy / Chemist</h3>
                <p>Prescriptions & DDA Compliance</p>
            </div>
            <div class="biz-card" data-biz="hospital" onclick="selectBiz('hospital', this)">
                <div class="biz-icon">🏥</div>
                <h3>Hospital / Clinic</h3>
                <p>Patient Records, Lab & IPD</p>
            </div>
            <div class="biz-card" data-biz="restaurant" onclick="selectBiz('restaurant', this)">
                <div class="biz-icon">🍽️</div>
                <h3>Restaurant / Cafe</h3>
                <p>Table Management & KOT</p>
            </div>
            <div class="biz-card" data-biz="logistics" onclick="selectBiz('logistics', this)">
                <div class="biz-icon">🚚</div>
                <h3>Distribution / Logistics</h3>
                <p>Wholesale, Vans & Waybills</p>
            </div>
            <div class="biz-card" data-biz="other" onclick="selectBiz('other', this)">
                <div class="biz-icon">🧩</div>
                <h3>Other / Mixed</h3>
                <p>I do a little bit of everything</p>
            </div>
        </div>

        <div class="actions">
            <button class="btn-next" id="btn-next-1" disabled onclick="goStep(2)">
                Continue <i class="fas fa-arrow-right"></i>
            </button>
        </div>
    </div>

    {{-- STEP 2: PRICING --}}
    <div class="panel" id="step-2">
        <div style="text-align: center;">
            <a onclick="goStep(1)" class="btn-back"><i class="fas fa-arrow-left"></i> Change Business Type</a>
        </div>
        
        <div class="step-header">
            <h1 style="color: #0f172a;">Choose your plan</h1>
            <p>Simple, transparent pricing. Upgrade, downgrade, or cancel anytime.</p>
        </div>

        <div class="pricing-grid">
            
            {{-- BASIC --}}
            <div class="plan-card">
                <div class="plan-name">Basic Plan</div>
                <div class="plan-price">500</div>
                <div class="plan-cycle">KES / month</div>
                <ul class="plan-features">
                    <li><i class="fas fa-check"></i> Standard POS System</li>
                    <li><i class="fas fa-check"></i> Basic Inventory & Stock</li>
                    <li><i class="fas fa-check"></i> Receipts & Invoices</li>
                    <li><i class="fas fa-check"></i> Sales Reporting</li>
                    <li><i class="fas fa-check"></i> 1 Business Location</li>
                </ul>
                <button class="btn-select btn-outline" onclick="selectPlan('basic')">Select Basic</button>
            </div>

            {{-- PRO --}}
            <div class="plan-card popular">
                <div class="plan-badge">Most Popular</div>
                <div class="plan-name">Pro Plan</div>
                <div class="plan-price">1,200</div>
                <div class="plan-cycle">KES / month</div>
                <ul class="plan-features">
                    <li><i class="fas fa-check"></i> Everything in Basic</li>
                    <li><i class="fas fa-check"></i> <b>Pharmacy DDA Features</b></li>
                    <li><i class="fas fa-check"></i> Multi-branch Support</li>
                    <li><i class="fas fa-check"></i> KRA eTIMS Integration</li>
                    <li><i class="fas fa-check"></i> Automated M-Pesa Till</li>
                    <li><i class="fas fa-check"></i> Profit & Loss Analytics</li>
                </ul>
                <button class="btn-select btn-solid" onclick="selectPlan('pro')">Select Pro</button>
            </div>

            {{-- ENTERPRISE --}}
            <div class="plan-card">
                <div class="plan-name">Enterprise Plan</div>
                <div class="plan-price">3,500</div>
                <div class="plan-cycle">KES / month</div>
                <ul class="plan-features">
                    <li><i class="fas fa-check"></i> Everything in Pro</li>
                    <li><i class="fas fa-check"></i> <b>Full Hospital System</b></li>
                    <li><i class="fas fa-check"></i> Patient Records (EHR)</li>
                    <li><i class="fas fa-check"></i> Lab & Radiology Workflows</li>
                    <li><i class="fas fa-check"></i> Ward / Bed Management</li>
                    <li><i class="fas fa-check"></i> Unlimited Users</li>
                </ul>
                <button class="btn-select btn-outline" onclick="selectPlan('enterprise')">Select Enterprise</button>
            </div>

            {{-- LIFETIME --}}
            <div class="plan-card lifetime">
                <div class="plan-name">Lifetime Deal</div>
                <div class="plan-price">16,000</div>
                <div class="plan-cycle">KES / one-time</div>
                <ul class="plan-features">
                    <li><i class="fas fa-check"></i> Access to ALL Features</li>
                    <li><i class="fas fa-check"></i> <b>Pay once, own it forever</b></li>
                    <li><i class="fas fa-check"></i> Cloud or On-Premise</li>
                    <li><i class="fas fa-check"></i> Free lifetime updates</li>
                    <li><i class="fas fa-check"></i> VIP Priority Support</li>
                    <li><i class="fas fa-check"></i> Zero monthly fees</li>
                </ul>
                <button class="btn-select btn-outline" onclick="selectPlan('lifetime')">Get Lifetime</button>
            </div>

        </div>
    </div>

</div>

<script>
let currentBiz = null;

function selectBiz(biz, el) {
    currentBiz = biz;
    document.querySelectorAll('.biz-card').forEach(c => c.classList.remove('selected'));
    el.classList.add('selected');
    document.getElementById('btn-next-1').disabled = false;
}

function goStep(step) {
    document.querySelectorAll('.panel').forEach(p => p.classList.remove('active'));
    document.getElementById('step-' + step).classList.add('active');
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function selectPlan(plan) {
    if (!currentBiz) currentBiz = 'retail'; // fallback
    window.location.href = `{{ route('saas.checkout') }}?biz=${currentBiz}&plan=${plan}`;
}
</script>
@endsection