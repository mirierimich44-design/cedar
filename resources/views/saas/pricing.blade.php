<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Pricing — {{ config('app.name') }}</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f8fafc; color: #1e293b; }

/* NAV */
.pnav { background: #0f172a; padding: 0 48px; height: 64px; display: flex; align-items: center; justify-content: space-between; position: sticky; top: 0; z-index: 100; }
.pnav-brand { display: flex; align-items: center; gap: 10px; text-decoration: none; }
.pnav-brand img { width: 32px; height: 32px; border-radius: 8px; background: white; padding: 3px; object-fit: contain; }
.pnav-brand span { color: white; font-size: 1rem; font-weight: 800; }
.pnav-links { display: flex; align-items: center; gap: 20px; }
.pnav-links a { color: rgba(255,255,255,0.7); font-size: 0.9rem; text-decoration: none; }
.pnav-links a:hover { color: white; }
.pnav-login { background: #0d9488; color: white !important; padding: 8px 20px; border-radius: 8px; font-weight: 600; font-size: 0.875rem; }

/* HERO */
.hero { background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 100%); padding: 72px 24px 60px; text-align: center; }
.hero h1 { font-size: clamp(2rem, 4vw, 3rem); font-weight: 900; color: white; margin-bottom: 14px; }
.hero p { color: rgba(255,255,255,0.75); font-size: 1.1rem; max-width: 560px; margin: 0 auto 40px; line-height: 1.7; }

/* CYCLE TOGGLE */
.cycle-wrap { display: inline-flex; background: rgba(255,255,255,0.1); border-radius: 50px; padding: 4px; gap: 2px; }
.cycle-btn { padding: 10px 22px; border-radius: 50px; border: none; font-size: 0.875rem; font-weight: 600; cursor: pointer; background: transparent; color: rgba(255,255,255,0.7); transition: all 0.2s; position: relative; }
.cycle-btn.active { background: white; color: #0f172a; }
.cycle-badge { position: absolute; top: -8px; right: -4px; background: #10b981; color: white; font-size: 0.62rem; font-weight: 700; padding: 2px 6px; border-radius: 50px; }

/* MAIN LAYOUT */
.main-wrap { max-width: 1200px; margin: 0 auto; padding: 48px 24px; display: grid; grid-template-columns: 1fr 340px; gap: 32px; align-items: start; }
@media (max-width: 900px) { .main-wrap { grid-template-columns: 1fr; } }

/* BUNDLES */
.section-label { font-size: 0.72rem; font-weight: 700; letter-spacing: 2px; text-transform: uppercase; color: #64748b; margin-bottom: 16px; }
.bundles-row { display: flex; gap: 14px; margin-bottom: 36px; flex-wrap: wrap; }
.bundle-chip { border: 2px solid #e2e8f0; border-radius: 12px; padding: 14px 18px; cursor: pointer; transition: all 0.18s; background: white; text-align: left; flex: 1; min-width: 160px; }
.bundle-chip:hover { border-color: #0d9488; }
.bundle-chip.selected { border-color: #0d9488; background: #f0fdfa; }
.bundle-chip .bc-name { font-size: 0.95rem; font-weight: 700; color: #0f172a; margin-bottom: 4px; }
.bundle-chip .bc-desc { font-size: 0.78rem; color: #64748b; line-height: 1.4; }
.bundle-popular { display: inline-block; font-size: 0.65rem; font-weight: 700; background: #0d9488; color: white; padding: 2px 7px; border-radius: 50px; margin-bottom: 6px; }

/* FEATURE GROUPS */
.feat-group { background: white; border: 1px solid #e2e8f0; border-radius: 16px; margin-bottom: 16px; overflow: hidden; }
.feat-group-header { padding: 16px 20px; background: #f8fafc; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; gap: 10px; font-weight: 700; color: #0f172a; font-size: 0.95rem; }
.feat-group-header i { color: #0d9488; width: 18px; text-align: center; }
.feat-row { display: flex; align-items: center; padding: 14px 20px; border-bottom: 1px solid #f1f5f9; transition: background 0.15s; gap: 14px; }
.feat-row:last-child { border-bottom: none; }
.feat-row:hover { background: #f8fafc; }
.feat-check { width: 22px; height: 22px; min-width: 22px; border-radius: 6px; border: 2px solid #cbd5e1; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.15s; background: white; }
.feat-check.checked { background: #0d9488; border-color: #0d9488; }
.feat-check.required { background: #0d9488; border-color: #0d9488; cursor: not-allowed; }
.feat-check svg { display: none; }
.feat-check.checked svg, .feat-check.required svg { display: block; }
.feat-info { flex: 1; }
.feat-name { font-size: 0.93rem; font-weight: 600; color: #0f172a; }
.feat-desc { font-size: 0.78rem; color: #64748b; margin-top: 2px; }
.feat-req-badge { font-size: 0.65rem; font-weight: 700; background: #dbeafe; color: #1d4ed8; padding: 2px 7px; border-radius: 50px; margin-left: 6px; }
.feat-price { font-size: 0.93rem; font-weight: 700; color: #0f172a; white-space: nowrap; min-width: 80px; text-align: right; }
.feat-price.free { color: #10b981; }

/* STICKY SUMMARY */
.summary-card { background: white; border: 1px solid #e2e8f0; border-radius: 20px; padding: 28px 24px; position: sticky; top: 84px; }
.summary-card h3 { font-size: 1.1rem; font-weight: 800; color: #0f172a; margin-bottom: 20px; }
.summary-items { min-height: 60px; margin-bottom: 20px; }
.summary-item { display: flex; justify-content: space-between; align-items: center; padding: 7px 0; border-bottom: 1px solid #f1f5f9; font-size: 0.855rem; }
.summary-item:last-child { border: none; }
.summary-item .si-name { color: #374151; }
.summary-item .si-price { color: #0f172a; font-weight: 600; }
.summary-empty { color: #94a3b8; font-size: 0.875rem; font-style: italic; }
.summary-divider { border: none; border-top: 2px solid #e2e8f0; margin: 16px 0; }
.summary-total-row { display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 6px; }
.summary-total-label { font-weight: 700; color: #0f172a; }
.summary-total-amount { font-size: 1.8rem; font-weight: 900; color: #0d9488; }
.summary-cycle-note { font-size: 0.78rem; color: #64748b; margin-bottom: 20px; }
.summary-savings { background: #f0fdfa; border: 1px solid #a7f3d0; border-radius: 8px; padding: 8px 12px; font-size: 0.8rem; color: #065f46; font-weight: 600; margin-bottom: 16px; display: none; }
.summary-savings.show { display: block; }

/* HOSTING TOGGLE */
.hosting-wrap { margin-bottom: 20px; }
.hosting-wrap p { font-size: 0.82rem; font-weight: 600; color: #374151; margin-bottom: 10px; }
.hosting-opts { display: flex; gap: 8px; }
.hosting-opt { flex: 1; border: 2px solid #e2e8f0; border-radius: 10px; padding: 10px 12px; cursor: pointer; transition: all 0.15s; text-align: center; }
.hosting-opt.selected { border-color: #0d9488; background: #f0fdfa; }
.hosting-opt span { font-size: 0.82rem; font-weight: 600; color: #0f172a; display: block; }
.hosting-opt small { font-size: 0.72rem; color: #64748b; }

/* CTA */
.cta-btn { width: 100%; padding: 16px; background: linear-gradient(135deg, #0f766e, #0369a1); border: none; border-radius: 12px; color: white; font-size: 1rem; font-weight: 700; cursor: pointer; transition: all 0.2s; }
.cta-btn:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(15,118,110,0.4); }
.cta-btn:disabled { opacity: 0.5; cursor: not-allowed; transform: none; }

/* FOOTER */
.pfooter { text-align: center; padding: 40px 24px; color: #94a3b8; font-size: 0.83rem; background: #f8fafc; border-top: 1px solid #e2e8f0; }
.pfooter a { color: #0d9488; text-decoration: none; }

/* FLASH */
.flash { padding: 14px 20px; border-radius: 10px; margin: 16px auto; max-width: 700px; font-size: 0.9rem; font-weight: 500; }
.flash.success { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
.flash.error { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }
</style>
</head>
<body>

<nav class="pnav">
    <a href="{{ url('/') }}" class="pnav-brand">
        <img src="{{ asset('img/logo-small.png') }}" alt="logo">
        <span>{{ config('app.name') }}</span>
    </a>
    <div class="pnav-links">
        <a href="{{ url('/') }}">Home</a>
        <a href="{{ action([\App\Http\Controllers\Auth\LoginController::class, 'login']) }}" class="pnav-login">Sign In</a>
    </div>
</nav>

<div class="hero">
    @if(session('success'))
    <div class="flash success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="flash error">{{ session('error') }}</div>
    @endif

    <h1>Build Your Own Plan</h1>
    <p>Pick only the features you need. Price updates live as you select.</p>

    <div class="cycle-wrap">
        <button class="cycle-btn active" data-cycle="monthly">Monthly</button>
        <button class="cycle-btn" data-cycle="quarterly">Quarterly <span class="cycle-badge">-10%</span></button>
        <button class="cycle-btn" data-cycle="yearly">Yearly <span class="cycle-badge">-20%</span></button>
        <button class="cycle-btn" data-cycle="once">One-Off</button>
    </div>
</div>

<div class="main-wrap">

    <div class="features-col">

        {{-- Quick-Start Bundles --}}
        @if($bundles->count())
        <div class="section-label">Quick-Start Bundles</div>
        <div class="bundles-row">
            @foreach($bundles as $bundle)
            <button class="bundle-chip" data-bundle-id="{{ $bundle->id }}"
                data-feature-ids="{{ $bundle->features->pluck('id')->join(',') }}">
                @if($bundle->is_popular)<span class="bundle-popular">POPULAR</span>@endif
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
                    <svg width="13" height="13" viewBox="0 0 13 13" fill="none">
                        <path d="M2 6.5L5 9.5L11 3.5" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <div class="feat-info">
                    <div class="feat-name">
                        {{ $feature->name }}
                        @if($feature->is_required)<span class="feat-req-badge">Included</span>@endif
                    </div>
                    @if($feature->description)
                    <div class="feat-desc">{{ $feature->description }}</div>
                    @endif
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

    {{-- Summary Sidebar --}}
    <div>
        <div class="summary-card">
            <h3>Your Plan Summary</h3>

            <div class="summary-items" id="summary-items">
                <div class="summary-empty" id="summary-empty">No features selected yet.</div>
            </div>

            <hr class="summary-divider">

            <div class="hosting-wrap">
                <p>Hosting</p>
                <div class="hosting-opts">
                    <div class="hosting-opt selected" data-hosting="cloud" onclick="selectHosting('cloud')">
                        <span>☁️ Cloud</span>
                        <small>We manage it</small>
                    </div>
                    <div class="hosting-opt" data-hosting="self_hosted" onclick="selectHosting('self_hosted')">
                        <span>🖥️ Self-Hosted</span>
                        <small>One-off + yearly fee</small>
                    </div>
                </div>
            </div>

            <div class="summary-savings" id="summary-savings"></div>

            <div class="summary-total-row">
                <span class="summary-total-label">Total</span>
                <span class="summary-total-amount" id="summary-total">KES 0</span>
            </div>
            <div class="summary-cycle-note" id="cycle-note">per month</div>

            <button class="cta-btn" id="cta-btn" onclick="goToCheckout()" disabled>
                Get Started →
            </button>

            <div style="text-align:center; margin-top:12px; font-size:0.78rem; color:#94a3b8;">
                No credit card required upfront
            </div>
        </div>
    </div>

</div>

<footer class="pfooter">
    &copy; {{ date('Y') }} {{ config('app.name') }}.
    Questions? <a href="mailto:{{ config('mail.from.address', 'info@apexpos.co.ke') }}">Contact us</a>
</footer>

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
    const name    = row.querySelector('.feat-name').textContent.replace('Included','').trim();

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

    // Update all visible prices
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

    // Remove old dynamic items
    itemsEl.querySelectorAll('.summary-item').forEach(e => e.remove());

    ids.forEach(id => {
        const f     = selectedFeatures[id];
        const price = f[currentCycle] || f.monthly;
        total += price;

        const div = document.createElement('div');
        div.className = 'summary-item';
        div.innerHTML = `<span class="si-name">${f.name}</span><span class="si-price">${price === 0 ? 'Free' : 'KES ' + price.toLocaleString()}</span>`;
        itemsEl.appendChild(div);
    });

    totalEl.textContent = 'KES ' + total.toLocaleString();

    const notes = { monthly: 'per month', quarterly: 'per quarter', yearly: 'per year', once: 'one-off payment' };
    noteEl.textContent = notes[currentCycle] || '';

    // Show savings vs monthly
    if (currentCycle === 'quarterly' || currentCycle === 'yearly') {
        const monthlyTotal = ids.reduce((s, id) => s + (selectedFeatures[id].monthly || 0), 0);
        const months = currentCycle === 'quarterly' ? 3 : 12;
        const saved = (monthlyTotal * months) - total;
        if (saved > 0) {
            savings.textContent = `💰 You save KES ${saved.toLocaleString()} vs paying monthly`;
            savings.classList.add('show');
        }
    } else {
        savings.classList.remove('show');
    }

    ctaBtn.disabled = false;
}

function goToCheckout() {
    const ids = Object.keys(selectedFeatures).join(',');
    if (!ids) return;
    window.location = `{{ route('saas.checkout') }}?feature_ids=${ids}&cycle=${currentCycle}&hosting=${currentHosting}`;
}

// Bundle quick-select
document.querySelectorAll('.bundle-chip').forEach(chip => {
    chip.addEventListener('click', () => {
        document.querySelectorAll('.bundle-chip').forEach(c => c.classList.remove('selected'));
        chip.classList.add('selected');

        const ids = chip.dataset.featureIds.split(',').map(Number).filter(Boolean);

        // Clear non-required selections
        Object.keys(selectedFeatures).forEach(id => {
            if (!selectedFeatures[id].required) {
                delete selectedFeatures[id];
                const check = document.getElementById(`check-${id}`);
                if (check) check.classList.remove('checked');
            }
        });

        // Select bundle features
        ids.forEach(id => {
            const priceEl = document.getElementById(`price-${id}`);
            const row = document.querySelector(`[data-feature-id="${id}"]`);
            if (!row) return;
            const name = row.querySelector('.feat-name').textContent.replace('Included','').trim();
            selectedFeatures[id] = {
                name,
                monthly:   parseFloat(priceEl?.dataset.monthly || 0),
                quarterly: parseFloat(priceEl?.dataset.quarterly || 0),
                yearly:    parseFloat(priceEl?.dataset.yearly || 0),
                once:      parseFloat(priceEl?.dataset.once || 0),
                required:  false
            };
            const check = document.getElementById(`check-${id}`);
            if (check) check.classList.add('checked');
        });

        updateSummary();
    });
});

// Cycle buttons
document.querySelectorAll('.cycle-btn').forEach(btn => {
    btn.addEventListener('click', () => selectCycle(btn.dataset.cycle));
});

// Init
updateSummary();
</script>
</body>
</html>
