@extends('layouts.auth2')
@section('title', config('app.name', 'Reenson Pharmacy'))
@inject('request', 'Illuminate\Http\Request')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<style>
    /* Force dark background regardless of auth2 gradient */
    html, body {
        background: #0a1628 !important;
        margin: 0; padding: 0;
    }
    .right-col { padding: 0 !important; margin: 0 !important; }
    .container-fluid, .row.eq-height-row { padding: 0 !important; margin: 0 !important; }

    .landing-wrap {
        position: fixed;
        top: 0; left: 0; right: 0; bottom: 0;
        z-index: 9990;
        overflow-y: auto;
        background:
            linear-gradient(rgba(5, 15, 35, 0.72), rgba(5, 20, 45, 0.85)),
            url('{{ asset("img/home-bg.jpg") }}') center center / cover no-repeat;
        background-color: #0a1628;
    }

    /* ── TOP NAV ── */
    .lp-nav {
        position: sticky;
        top: 0;
        z-index: 1000;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 48px;
        background: rgba(0,0,0,0.3);
        backdrop-filter: blur(8px);
        border-bottom: 1px solid rgba(255,255,255,0.08);
    }
    .lp-nav-brand { display: flex; align-items: center; gap: 10px; text-decoration: none; }
    .lp-nav-brand img { width: 34px; height: 34px; border-radius: 8px; background: white; padding: 3px; object-fit: contain; }
    .lp-nav-brand span { color: white; font-size: 1.05rem; font-weight: 800; }
    .lp-signin-btn {
        background: #0d9488;
        color: white !important;
        font-weight: 700;
        font-size: 0.875rem;
        padding: 9px 24px;
        border-radius: 50px;
        text-decoration: none;
        transition: background 0.2s;
    }
    .lp-signin-btn:hover { background: #0f766e; text-decoration: none; color: white !important; }

    /* ── HERO ── */
    .lp-hero {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        padding: 80px 24px 20px;
    }
    .lp-logo-ring {
        width: 88px; height: 88px;
        background: rgba(255,255,255,0.12);
        border: 2px solid rgba(255,255,255,0.25);
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        margin-bottom: 28px;
        backdrop-filter: blur(4px);
    }
    .lp-logo-ring img { width: 56px; height: 56px; object-fit: contain; }
    .lp-title {
        font-size: clamp(2rem, 5vw, 3.6rem);
        font-weight: 900;
        color: #ffffff;
        margin: 0 0 12px;
        line-height: 1.15;
        text-shadow: 0 2px 16px rgba(0,0,0,0.6);
    }
    .lp-subtitle {
        font-size: clamp(0.95rem, 2vw, 1.15rem);
        color: rgba(255,255,255,0.85);
        max-width: 520px;
        margin: 0 auto 20px;
        line-height: 1.65;
        text-shadow: 0 1px 6px rgba(0,0,0,0.5);
    }
    .lp-divider { width: 52px; height: 4px; background: #0d9488; border-radius: 2px; margin: 0 auto 36px; }
    .lp-cta {
        display: inline-block;
        background: #0d9488;
        color: #ffffff !important;
        font-size: 1rem;
        font-weight: 800;
        padding: 15px 42px;
        border-radius: 50px;
        text-decoration: none;
        box-shadow: 0 8px 24px rgba(13,148,136,0.45);
        transition: all 0.22s;
        letter-spacing: 0.3px;
        margin-bottom: 72px;
    }
    .lp-cta:hover {
        background: #0f766e;
        transform: translateY(-2px);
        box-shadow: 0 12px 30px rgba(13,148,136,0.55);
        text-decoration: none;
        color: #ffffff !important;
    }

    /* ── FEATURES ── */
    .lp-features-label {
        color: rgba(255,255,255,0.5);
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 3px;
        text-transform: uppercase;
        margin-bottom: 24px;
    }
    .lp-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
        max-width: 920px;
        width: 100%;
        margin: 0 auto;
        padding: 0 16px;
    }
    @media (max-width: 640px)  { .lp-grid { grid-template-columns: 1fr; } }
    @media (min-width: 641px) and (max-width: 900px) { .lp-grid { grid-template-columns: repeat(2, 1fr); } }

    .lp-card {
        background: rgba(255,255,255,0.07);
        border: 1px solid rgba(255,255,255,0.14);
        border-radius: 18px;
        padding: 28px 22px 24px;
        text-align: center;
        transition: all 0.25s;
        backdrop-filter: blur(6px);
    }
    .lp-card:hover {
        background: rgba(255,255,255,0.13);
        border-color: rgba(13,148,136,0.5);
        transform: translateY(-4px);
        box-shadow: 0 12px 30px rgba(0,0,0,0.3);
    }
    .lp-card-icon {
        width: 54px; height: 54px;
        background: rgba(13,148,136,0.2);
        border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.6rem;
        margin: 0 auto 16px;
    }
    .lp-card h3 {
        color: #ffffff;
        font-size: 1rem;
        font-weight: 700;
        margin: 0 0 8px;
        text-shadow: 0 1px 4px rgba(0,0,0,0.3);
    }
    .lp-card p {
        color: rgba(255,255,255,0.75);
        font-size: 0.855rem;
        line-height: 1.55;
        margin: 0;
    }

    /* ── PRICING SECTION ── */
    .pricing-section {
        max-width: 1100px;
        margin: 100px auto 40px;
        padding: 0 24px;
        text-align: left;
    }
    .pricing-header { text-align: center; margin-bottom: 48px; }
    .pricing-header h2 { font-size: 2.4rem; font-weight: 900; color: white; margin-bottom: 12px; }
    .pricing-header p { color: rgba(255,255,255,0.6); font-size: 1.1rem; }

    .cycle-wrap { display: flex; justify-content: center; background: rgba(255,255,255,0.05); border-radius: 50px; padding: 4px; gap: 2px; margin: 30px auto; max-width: max-content; }
    .cycle-btn { padding: 10px 22px; border-radius: 50px; border: none; font-size: 0.875rem; font-weight: 600; cursor: pointer; background: transparent; color: rgba(255,255,255,0.5); transition: all 0.2s; position: relative; }
    .cycle-btn.active { background: #0d9488; color: white; }
    .cycle-badge { position: absolute; top: -8px; right: -4px; background: #10b981; color: white; font-size: 0.62rem; font-weight: 700; padding: 2px 6px; border-radius: 50px; }

    .pricing-grid { display: grid; grid-template-columns: 1fr 340px; gap: 32px; align-items: start; }
    @media (max-width: 900px) { .pricing-grid { grid-template-columns: 1fr; } }

    .bundle-chip { background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 14px; padding: 16px; cursor: pointer; transition: all 0.2s; text-align: left; flex: 1; min-width: 180px; color: white; }
    .bundle-chip:hover { border-color: #0d9488; background: rgba(255,255,255,0.08); }
    .bundle-chip.selected { border-color: #0d9488; background: rgba(13,148,136,0.15); }
    .bc-name { font-weight: 700; font-size: 1rem; margin-bottom: 4px; }
    .bc-desc { font-size: 0.78rem; color: rgba(255,255,255,0.5); }

    .feat-group { background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08); border-radius: 18px; margin-bottom: 20px; overflow: hidden; }
    .feat-group-header { padding: 16px 22px; background: rgba(255,255,255,0.03); border-bottom: 1px solid rgba(255,255,255,0.08); display: flex; align-items: center; gap: 12px; font-weight: 700; color: white; }
    .feat-group-header i { color: #0d9488; }
    
    .feat-row { display: flex; align-items: center; padding: 14px 22px; border-bottom: 1px solid rgba(255,255,255,0.04); gap: 16px; transition: background 0.2s; }
    .feat-row:last-child { border-bottom: none; }
    .feat-row:hover { background: rgba(255,255,255,0.02); }
    
    .feat-check { width: 22px; height: 22px; border-radius: 6px; border: 2px solid rgba(255,255,255,0.2); cursor: pointer; display: flex; align-items: center; justify-content: center; background: transparent; transition: all 0.2s; }
    .feat-check.checked { background: #0d9488; border-color: #0d9488; }
    .feat-check.required { background: #0d9488; border-color: #0d9488; cursor: not-allowed; opacity: 0.8; }
    .feat-check svg { display: none; stroke: white; }
    .feat-check.checked svg, .feat-check.required svg { display: block; }

    .feat-info { flex: 1; }
    .feat-name { font-weight: 600; color: white; font-size: 0.95rem; }
    .feat-desc { font-size: 0.8rem; color: rgba(255,255,255,0.5); margin-top: 2px; }
    .feat-price { font-weight: 700; color: white; text-align: right; min-width: 90px; font-size: 0.95rem; }
    .feat-price.free { color: #10b981; }

    .summary-card { background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 24px; padding: 30px; position: sticky; top: 100px; backdrop-filter: blur(10px); }
    .summary-card h3 { color: white; margin-bottom: 24px; font-weight: 800; }
    .summary-item { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid rgba(255,255,255,0.05); color: rgba(255,255,255,0.8); font-size: 0.88rem; }
    .summary-item:last-child { border: none; }
    .summary-total-row { display: flex; justify-content: space-between; align-items: baseline; margin-top: 20px; }
    .summary-total-amount { font-size: 2rem; font-weight: 900; color: #0d9488; }
    .summary-savings { background: rgba(16,185,129,0.1); border: 1px solid rgba(16,185,129,0.2); color: #10b981; padding: 10px; border-radius: 10px; font-size: 0.82rem; margin-top: 16px; display: none; }
    .summary-savings.show { display: block; }
    
    .hosting-wrap { margin: 20px 0; }
    .hosting-opts { display: flex; gap: 8px; margin-top: 10px; }
    .hosting-opt { flex: 1; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; padding: 10px; text-align: center; cursor: pointer; color: white; transition: all 0.2s; }
    .hosting-opt.selected { border-color: #0d9488; background: rgba(13,148,136,0.1); }
    .hosting-opt span { display: block; font-size: 0.85rem; font-weight: 700; }
    .hosting-opt small { font-size: 0.7rem; color: rgba(255,255,255,0.4); }

    .cta-pricing { width: 100%; padding: 16px; background: #0d9488; border: none; border-radius: 14px; color: white; font-weight: 800; font-size: 1rem; cursor: pointer; transition: all 0.2s; margin-top: 20px; }
    .cta-pricing:hover:not(:disabled) { background: #0f766e; transform: translateY(-1px); }
    .cta-pricing:disabled { opacity: 0.4; cursor: not-allowed; }

    .lp-footer {
        text-align: center;
        color: rgba(255,255,255,0.3);
        font-size: 0.76rem;
        padding: 48px 0 32px;
    }
    @media (max-width: 600px) {
        .lp-nav { padding: 12px 20px; }
        .lp-hero { padding: 60px 16px 16px; }
    }
</style>

<div class="landing-wrap">

    {{-- Navbar --}}
    <nav class="lp-nav">
        <a href="{{ url('/') }}" class="lp-nav-brand">
            <img src="{{ asset('img/logo-small.png') }}" alt="logo">
            <span>{{ config('app.name', 'Reenson Pharmacy') }}</span>
        </a>
        <div class="tw-flex tw-gap-6 tw-items-center">
            <a href="#pricing" class="tw-text-white/60 hover:tw-text-white tw-text-sm tw-font-bold tw-no-underline">Pricing</a>
            <a href="{{ action([\App\Http\Controllers\Auth\LoginController::class, 'login']) }}" class="lp-signin-btn">
                Sign In
            </a>
        </div>
    </nav>

    {{-- Hero --}}
    <div class="lp-hero">
        <div class="lp-logo-ring">
            <img src="{{ asset('img/logo-small.png') }}" alt="{{ config('app.name') }}">
        </div>

        <h1 class="lp-title">{{ config('app.name', 'Reenson Pharmacy') }}</h1>
        <p class="lp-subtitle">Integrated Pharmacy &amp; DDA Drug Management System</p>
        <div class="lp-divider"></div>

        <a href="{{ action([\App\Http\Controllers\Auth\LoginController::class, 'login']) }}" class="lp-cta">
            Sign In to Dashboard &rarr;
        </a>

        <p class="lp-features-label">Everything you need</p>

        <div class="lp-grid">
            <div class="lp-card">
                <div class="lp-card-icon">💊</div>
                <h3>DDA Drug Control</h3>
                <p>Full DDA compliance — prescriptions, dispense logs, stock, and destruction records.</p>
            </div>
            <div class="lp-card">
                <div class="lp-card-icon">📋</div>
                <h3>Prescription Management</h3>
                <p>Create, track, and dispense prescriptions with a complete patient audit trail.</p>
            </div>
            <div class="lp-card">
                <div class="lp-card-icon">📦</div>
                <h3>Inventory & Stock</h3>
                <p>Real-time stock tracking with expiry alerts, low-stock warnings, and batch management.</p>
            </div>
            <div class="lp-card">
                <div class="lp-card-icon">🛒</div>
                <h3>Point of Sale</h3>
                <p>Fast POS system optimised for pharmacy counter sales and walk-in customers.</p>
            </div>
            <div class="lp-card">
                <div class="lp-card-icon">📊</div>
                <h3>Reports & Analytics</h3>
                <p>Sales, stock, DDA, and financial reports to keep your pharmacy profitable and compliant.</p>
            </div>
            <div class="lp-card">
                <div class="lp-card-icon">💬</div>
                <h3>SMS Notifications</h3>
                <p>Automated SMS alerts for prescription reminders, order updates, and promotions.</p>
            </div>
        </div>
    </div>

    {{-- Pricing Section --}}
    <div class="pricing-section" id="pricing">
        <div class="pricing-header">
            <h2>Build Your Own Plan</h2>
            <p>Select only the features you need. Pay for what you use.</p>

            <div class="cycle-wrap">
                <button class="cycle-btn active" data-cycle="monthly">Monthly</button>
                <button class="cycle-btn" data-cycle="quarterly">Quarterly <span class="cycle-badge">-10%</span></button>
                <button class="cycle-btn" data-cycle="yearly">Yearly <span class="cycle-badge">-20%</span></button>
                <button class="cycle-btn" data-cycle="once">One-Off</button>
            </div>
        </div>

        <div class="pricing-grid">
            <div class="features-col">
                {{-- Bundles --}}
                @if($bundles->count())
                <p class="lp-features-label" style="margin-bottom:16px;">Quick-Start Bundles</p>
                <div class="tw-flex tw-gap-4 tw-mb-10 tw-flex-wrap">
                    @foreach($bundles as $bundle)
                    <button class="bundle-chip" data-bundle-id="{{ $bundle->id }}"
                        data-feature-ids="{{ $bundle->features->pluck('id')->join(',') }}">
                        <div class="bc-name">{{ $bundle->name }}</div>
                        <div class="bc-desc">{{ $bundle->description }}</div>
                    </button>
                    @endforeach
                </div>
                @endif

                {{-- Feature Groups --}}
                @foreach($categories as $catKey => $catMeta)
                @if($featuresByCategory->has($catKey))
                <div class="feat-group">
                    <div class="feat-group-header">
                        <i class="fas {{ $catMeta['icon'] }}"></i>
                        {{ $catMeta['label'] }}
                    </div>
                    @foreach($featuresByCategory[$catKey] as $feature)
                    <div class="feat-row" data-feature-id="{{ $feature->id }}">
                        <div class="feat-check {{ $feature->is_required ? 'checked required' : '' }}"
                             id="check-{{ $feature->id }}"
                             onclick="{{ $feature->is_required ? '' : 'toggleFeature(' . $feature->id . ')' }}">
                            <svg width="12" height="12" viewBox="0 0 13 13" fill="none">
                                <path d="M2 6.5L5 9.5L11 3.5" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <div class="feat-info">
                            <div class="feat-name">{{ $feature->name }} @if($feature->is_required)<small class="tw-text-teal-400 tw-ml-2">(Included)</small>@endif</div>
                            @if($feature->description)<div class="feat-desc">{{ $feature->description }}</div>@endif
                        </div>
                        <div class="feat-price {{ $feature->price_monthly == 0 ? 'free' : '' }}"
                             id="price-{{ $feature->id }}"
                             data-monthly="{{ $feature->price_monthly }}"
                             data-quarterly="{{ $feature->price_quarterly }}"
                             data-yearly="{{ $feature->price_yearly }}"
                             data-once="{{ $feature->price_once }}">
                            {{ $feature->price_monthly == 0 ? 'Free' : 'KES ' . number_format($feature->price_monthly, 0) }}
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
                @endforeach
            </div>

            {{-- Summary --}}
            <div class="summary-col">
                <div class="summary-card">
                    <h3>Your Plan</h3>
                    <div id="summary-items">
                        <p class="tw-text-white/40 tw-italic tw-text-sm" id="summary-empty">Select features to see pricing...</p>
                    </div>

                    <div class="hosting-wrap">
                        <p class="tw-text-xs tw-font-bold tw-text-white/50 tw-uppercase tw-tracking-wider">Hosting Option</p>
                        <div class="hosting-opts">
                            <div class="hosting-opt selected" data-hosting="cloud" onclick="selectHosting('cloud')">
                                <span>☁️ Cloud</span>
                                <small>Fully Managed</small>
                            </div>
                            <div class="hosting-opt" data-hosting="self_hosted" onclick="selectHosting('self_hosted')">
                                <span>🖥️ On-Premise</span>
                                <small>One-off</small>
                            </div>
                        </div>
                    </div>

                    <div class="summary-savings" id="summary-savings"></div>

                    <div class="summary-total-row">
                        <span class="tw-text-white/70 tw-font-bold">Total</span>
                        <div>
                            <div class="summary-total-amount" id="summary-total">KES 0</div>
                            <div class="tw-text-right tw-text-xs tw-text-white/40" id="cycle-note">per month</div>
                        </div>
                    </div>

                    <button class="cta-pricing" id="cta-btn" onclick="goToCheckout()" disabled>
                        Proceed to Setup &rarr;
                    </button>
                    
                    <p class="tw-text-center tw-text-[10px] tw-text-white/30 tw-mt-4">
                        Instant activation after payment verification.
                    </p>
                </div>
            </div>
        </div>

        <div class="lp-footer">
            &copy; {{ date('Y') }} {{ config('app.name', 'Reenson Pharmacy') }}. All rights reserved.
        </div>
    </div>

</div>

<script>
let selectedFeatures = {};
let currentCycle     = 'monthly';
let currentHosting   = 'cloud';

// Pre-select required features
@foreach($featuresByCategory->flatten() as $feature)
@if($feature->is_required)
selectedFeatures[{{ $feature->id }}] = {
    name: "{{ addslashes($feature->name) }}",
    monthly: {{ $feature->price_monthly }},
    quarterly: {{ $feature->price_quarterly }},
    yearly: {{ $feature->price_yearly }},
    once: {{ $feature->price_once }},
    required: true
};
@endif
@endforeach

function toggleFeature(id) {
    const row     = document.querySelector(`[data-feature-id="${id}"]`);
    const check   = document.getElementById(`check-${id}`);
    const priceEl = document.getElementById(`price-${id}`);
    const name    = row.querySelector('.feat-name').textContent.replace('(Included)','').trim();

    if (selectedFeatures[id]) {
        delete selectedFeatures[id];
        check.classList.remove('checked');
    } else {
        selectedFeatures[id] = {
            name,
            monthly:   parseFloat(priceEl.dataset.monthly),
            quarterly: parseFloat(priceEl.dataset.quarterly),
            yearly:    parseFloat(priceEl.dataset.yearly),
            once:      parseFloat(priceEl.dataset.once),
            required: false
        };
        check.classList.add('checked');
    }
    updateSummary();
}

function selectCycle(cycle) {
    currentCycle = cycle;
    document.querySelectorAll('.cycle-btn').forEach(b => b.classList.remove('active'));
    document.querySelector(`[data-cycle="${cycle}"]`).classList.add('active');

    document.querySelectorAll('.feat-price').forEach(el => {
        const price = parseFloat(el.dataset[cycle] || el.dataset.monthly);
        el.textContent = price === 0 ? 'Free' : 'KES ' + price.toLocaleString();
        el.className = 'feat-price' + (price === 0 ? ' free' : '');
    });

    updateSummary();
}

function selectHosting(type) {
    currentHosting = type;
    document.querySelectorAll('.hosting-opt').forEach(o => o.classList.remove('selected'));
    document.querySelector(`[data-hosting="${type}"]`).classList.add('selected');

    if (type === 'self_hosted') {
        selectCycle('once');
    } else if (currentCycle === 'once') {
        selectCycle('monthly');
    }
    updateSummary();
}

function updateSummary() {
    const itemsEl = document.getElementById('summary-items');
    const emptyEl = document.getElementById('summary-empty');
    const totalEl = document.getElementById('summary-total');
    const noteEl  = document.getElementById('cycle-note');
    const ctaBtn  = document.getElementById('cta-btn');
    const savings = document.getElementById('summary-savings');

    const ids = Object.keys(selectedFeatures);
    let total = 0;

    if (ids.length === 0) {
        emptyEl.style.display = 'block';
        totalEl.textContent = 'KES 0';
        ctaBtn.disabled = true;
        savings.classList.remove('show');
        return;
    }

    emptyEl.style.display = 'none';
    itemsEl.querySelectorAll('.summary-item').forEach(e => e.remove());

    ids.forEach(id => {
        const f     = selectedFeatures[id];
        const price = f[currentCycle] || f.monthly;
        total += price;

        const div = document.createElement('div');
        div.className = 'summary-item';
        div.innerHTML = `<span>${f.name}</span><span class="tw-font-bold tw-text-white">${price === 0 ? 'Free' : 'KES ' + price.toLocaleString()}</span>`;
        itemsEl.appendChild(div);
    });

    totalEl.textContent = 'KES ' + total.toLocaleString();

    const notes = { monthly: 'per month', quarterly: 'per quarter', yearly: 'per year', once: 'one-off payment' };
    noteEl.textContent = notes[currentCycle] || '';

    if (currentCycle === 'quarterly' || currentCycle === 'yearly') {
        const monthlyTotal = ids.reduce((s, id) => s + (selectedFeatures[id].monthly || 0), 0);
        const months = currentCycle === 'quarterly' ? 3 : 12;
        const saved = (monthlyTotal * months) - total;
        if (saved > 0) {
            savings.textContent = `💰 Saving KES ${saved.toLocaleString()} vs monthly`;
            savings.classList.add('show');
        } else {
            savings.classList.remove('show');
        }
    } else {
        savings.classList.remove('show');
    }

    ctaBtn.disabled = false;
}

function goToCheckout() {
    const ids = Object.keys(selectedFeatures).join(',');
    window.location = `{{ route('saas.checkout') }}?feature_ids=${ids}&cycle=${currentCycle}&hosting=${currentHosting}`;
}

document.querySelectorAll('.bundle-chip').forEach(chip => {
    chip.addEventListener('click', () => {
        document.querySelectorAll('.bundle-chip').forEach(c => c.classList.remove('selected'));
        chip.classList.add('selected');
        const ids = chip.dataset.featureIds.split(',').map(Number).filter(Boolean);
        Object.keys(selectedFeatures).forEach(id => {
            if (!selectedFeatures[id].required) {
                delete selectedFeatures[id];
                const check = document.getElementById(`check-${id}`);
                if (check) check.classList.remove('checked');
            }
        });
        ids.forEach(id => {
            const priceEl = document.getElementById(`price-${id}`);
            const row = document.querySelector(`[data-feature-id="${id}"]`);
            if (!row) return;
            const name = row.querySelector('.feat-name').textContent.replace('(Included)','').trim();
            selectedFeatures[id] = { name, monthly: parseFloat(priceEl?.dataset.monthly || 0), quarterly: parseFloat(priceEl?.dataset.quarterly || 0), yearly: parseFloat(priceEl?.dataset.yearly || 0), once: parseFloat(priceEl?.dataset.once || 0), required: false };
            const check = document.getElementById(`check-${id}`);
            if (check) check.classList.add('checked');
        });
        updateSummary();
    });
});

document.querySelectorAll('.cycle-btn').forEach(btn => {
    btn.addEventListener('click', () => selectCycle(btn.dataset.cycle));
});

updateSummary();
</script>
@endsection
