<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Build Your Plan — {{ config('app.name') }}</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
body{font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;background:#f8fafc;color:#1e293b;min-height:100vh}

/* NAV */
.pnav{background:#0f172a;padding:0 48px;height:64px;display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;z-index:100}
.pnav-brand{display:flex;align-items:center;gap:10px;text-decoration:none}
.pnav-brand img{width:32px;height:32px;border-radius:8px;background:white;padding:3px;object-fit:contain}
.pnav-brand span{color:white;font-size:1rem;font-weight:800}
.pnav-links{display:flex;align-items:center;gap:20px}
.pnav-links a{color:rgba(255,255,255,0.7);font-size:0.9rem;text-decoration:none}
.pnav-links a:hover{color:white}
.pnav-login{background:#0d9488;color:white !important;padding:8px 20px;border-radius:8px;font-weight:600;font-size:0.875rem}

/* PROGRESS BAR */
.progress-wrap{background:white;border-bottom:1px solid #e2e8f0;padding:24px 16px}
.progress-inner{max-width:900px;margin:0 auto;display:flex;align-items:center;justify-content:space-between;position:relative}
.progress-line{position:absolute;top:22px;left:40px;right:40px;height:3px;background:#e2e8f0;z-index:0}
.progress-line-fill{height:100%;background:linear-gradient(90deg,#0d9488,#0369a1);width:0;transition:width 0.4s}
.progress-step{position:relative;z-index:1;text-align:center;flex:1}
.progress-circle{width:44px;height:44px;border-radius:50%;background:#e2e8f0;color:#64748b;font-weight:700;display:flex;align-items:center;justify-content:center;margin:0 auto 8px;border:3px solid white;transition:all 0.3s;font-size:0.95rem}
.progress-step.active .progress-circle{background:#0d9488;color:white;transform:scale(1.08);box-shadow:0 0 0 4px rgba(13,148,136,0.15)}
.progress-step.done .progress-circle{background:#0d9488;color:white}
.progress-label{font-size:0.78rem;font-weight:600;color:#64748b}
.progress-step.active .progress-label{color:#0d9488}
.progress-step.done .progress-label{color:#0f172a}

/* WIZARD */
.wizard{max-width:1080px;margin:36px auto;padding:0 20px}
.step-panel{display:none;animation:fadeIn 0.35s ease}
.step-panel.active{display:block}
@keyframes fadeIn{from{opacity:0;transform:translateY(8px)}to{opacity:1;transform:translateY(0)}}
.step-title{font-size:clamp(1.5rem,3vw,2rem);font-weight:800;color:#0f172a;margin-bottom:8px;text-align:center}
.step-subtitle{color:#64748b;text-align:center;margin-bottom:32px;font-size:1rem}

/* STEP 1 */
.btype-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:18px;margin-bottom:24px}
.btype-card{background:white;border:2px solid #e2e8f0;border-radius:16px;padding:26px 20px;cursor:pointer;transition:all 0.2s;text-align:center}
.btype-card:hover{border-color:#0d9488;transform:translateY(-3px);box-shadow:0 10px 25px rgba(0,0,0,0.08)}
.btype-card.selected{border-color:#0d9488;background:#f0fdfa;box-shadow:0 8px 20px rgba(13,148,136,0.15)}
.btype-icon{width:64px;height:64px;margin:0 auto 14px;background:#f0fdfa;border-radius:16px;display:flex;align-items:center;justify-content:center;font-size:1.75rem;color:#0d9488}
.btype-card.selected .btype-icon{background:#0d9488;color:white}
.btype-name{font-size:1rem;font-weight:700;color:#0f172a;margin-bottom:6px}
.btype-desc{font-size:0.82rem;color:#64748b;line-height:1.4}

/* STEP 2 */
.feat-layout{display:grid;grid-template-columns:1fr 320px;gap:24px;align-items:start}
@media(max-width:900px){.feat-layout{grid-template-columns:1fr}}
.bundles-row{display:flex;gap:12px;margin-bottom:22px;flex-wrap:wrap}
.bundle-chip{border:2px solid #e2e8f0;border-radius:12px;padding:12px 16px;cursor:pointer;transition:all 0.18s;background:white;text-align:left;flex:1;min-width:150px}
.bundle-chip:hover{border-color:#0d9488}
.bundle-chip.selected{border-color:#0d9488;background:#f0fdfa}
.bundle-chip .bc-name{font-size:0.9rem;font-weight:700;color:#0f172a;margin-bottom:3px}
.bundle-chip .bc-desc{font-size:0.74rem;color:#64748b;line-height:1.4}
.bundle-popular{display:inline-block;font-size:0.62rem;font-weight:700;background:#0d9488;color:white;padding:2px 6px;border-radius:50px;margin-bottom:4px}
.section-label{font-size:0.72rem;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:#64748b;margin-bottom:12px;margin-top:4px}
.feat-group{background:white;border:1px solid #e2e8f0;border-radius:14px;margin-bottom:14px;overflow:hidden;transition:opacity 0.25s}
.feat-group-header{padding:14px 18px;background:#f8fafc;border-bottom:1px solid #e2e8f0;display:flex;align-items:center;gap:10px;font-weight:700;color:#0f172a;font-size:0.92rem}
.feat-group-header i{color:#0d9488;width:18px;text-align:center}
.feat-row{display:flex;align-items:center;padding:12px 18px;border-bottom:1px solid #f1f5f9;gap:12px;transition:background 0.15s}
.feat-row:last-child{border:none}
.feat-row:hover{background:#f8fafc}
.feat-check{width:22px;height:22px;min-width:22px;border-radius:6px;border:2px solid #cbd5e1;cursor:pointer;display:flex;align-items:center;justify-content:center;background:white;transition:all 0.15s}
.feat-check.checked{background:#0d9488;border-color:#0d9488}
.feat-check.required{background:#0d9488;border-color:#0d9488;cursor:not-allowed}
.feat-check svg{display:none}
.feat-check.checked svg,.feat-check.required svg{display:block}
.feat-info{flex:1}
.feat-name{font-size:0.9rem;font-weight:600;color:#0f172a}
.feat-desc{font-size:0.76rem;color:#64748b;margin-top:2px}
.feat-req-badge{font-size:0.62rem;font-weight:700;background:#dbeafe;color:#1d4ed8;padding:2px 7px;border-radius:50px;margin-left:6px}
.feat-recommended{font-size:0.62rem;font-weight:700;background:#fef3c7;color:#92400e;padding:2px 7px;border-radius:50px;margin-left:6px}
.feat-price{font-size:0.9rem;font-weight:700;color:#0f172a;white-space:nowrap;min-width:80px;text-align:right}
.feat-price.free{color:#10b981}

/* STEP 3 */
.cycle-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:14px;max-width:820px;margin:0 auto 24px}
.cycle-card{background:white;border:2px solid #e2e8f0;border-radius:14px;padding:24px 18px;cursor:pointer;transition:all 0.2s;text-align:center;position:relative}
.cycle-card:hover{border-color:#0d9488}
.cycle-card.selected{border-color:#0d9488;background:#f0fdfa}
.cycle-label{font-size:1.05rem;font-weight:700;color:#0f172a;margin-bottom:6px}
.cycle-sublabel{font-size:0.82rem;color:#64748b;margin-bottom:10px}
.cycle-savings{display:inline-block;font-size:0.7rem;font-weight:700;background:#10b981;color:white;padding:3px 10px;border-radius:50px}
.hosting-section{background:white;border:1px solid #e2e8f0;border-radius:14px;padding:22px;max-width:820px;margin:0 auto}
.hosting-section h4{font-size:1rem;font-weight:700;color:#0f172a;margin-bottom:14px}
.hosting-opts{display:grid;grid-template-columns:1fr 1fr;gap:12px}
@media(max-width:600px){.hosting-opts{grid-template-columns:1fr}}
.hosting-opt{border:2px solid #e2e8f0;border-radius:12px;padding:18px 16px;cursor:pointer;transition:all 0.15s;text-align:left}
.hosting-opt:hover{border-color:#0d9488}
.hosting-opt.selected{border-color:#0d9488;background:#f0fdfa}
.hosting-opt .ho-title{font-size:0.95rem;font-weight:700;color:#0f172a;margin-bottom:4px}
.hosting-opt .ho-sub{font-size:0.78rem;color:#64748b}

/* STEP 4 */
.review-wrap{max-width:780px;margin:0 auto;display:grid;grid-template-columns:1fr 1fr;gap:20px}
@media(max-width:700px){.review-wrap{grid-template-columns:1fr}}
.review-card{background:white;border:1px solid #e2e8f0;border-radius:14px;padding:22px}
.review-card h4{font-size:0.95rem;font-weight:700;color:#0f172a;margin-bottom:14px;display:flex;align-items:center;gap:8px}
.review-card h4 i{color:#0d9488}
.review-row{display:flex;justify-content:space-between;padding:8px 0;font-size:0.86rem;border-bottom:1px solid #f1f5f9}
.review-row:last-child{border:none}
.review-total-box{background:linear-gradient(135deg,#0f766e,#0369a1);color:white;border-radius:14px;padding:24px;text-align:center;margin:20px auto 0;max-width:780px}
.review-total-label{font-size:0.85rem;opacity:0.85;margin-bottom:4px;text-transform:uppercase;letter-spacing:1px}
.review-total-amount{font-size:2.4rem;font-weight:900;margin-bottom:4px}
.review-cycle-note{font-size:0.85rem;opacity:0.85}

/* SUMMARY */
.summary-card{background:white;border:1px solid #e2e8f0;border-radius:16px;padding:22px;position:sticky;top:88px}
.summary-card h3{font-size:0.98rem;font-weight:800;color:#0f172a;margin-bottom:14px}
.summary-items{min-height:50px;margin-bottom:14px;max-height:260px;overflow-y:auto}
.summary-item{display:flex;justify-content:space-between;align-items:center;padding:6px 0;border-bottom:1px solid #f1f5f9;font-size:0.82rem}
.summary-item:last-child{border:none}
.summary-empty{color:#94a3b8;font-size:0.82rem;font-style:italic}
.summary-total{display:flex;justify-content:space-between;align-items:baseline;padding-top:12px;border-top:2px solid #e2e8f0;margin-top:6px}
.summary-total-label{font-weight:700;color:#0f172a;font-size:0.9rem}
.summary-total-amount{font-size:1.5rem;font-weight:900;color:#0d9488}

/* NAV BUTTONS */
.wizard-nav{max-width:1080px;margin:32px auto 48px;padding:0 20px;display:flex;justify-content:space-between;align-items:center;gap:12px}
.btn-wiz{padding:14px 32px;border-radius:12px;font-size:0.95rem;font-weight:700;cursor:pointer;border:none;transition:all 0.2s;text-decoration:none;display:inline-flex;align-items:center;gap:8px}
.btn-back{background:white;color:#64748b;border:1.5px solid #cbd5e1}
.btn-back:hover{background:#f1f5f9}
.btn-next{background:linear-gradient(135deg,#0f766e,#0369a1);color:white;margin-left:auto}
.btn-next:hover:not(:disabled){transform:translateY(-1px);box-shadow:0 8px 20px rgba(15,118,110,0.35)}
.btn-next:disabled{opacity:0.5;cursor:not-allowed}

/* FOOTER */
.pfooter{text-align:center;padding:30px 20px;color:#94a3b8;font-size:0.82rem;background:#f8fafc;border-top:1px solid #e2e8f0;margin-top:40px}
.pfooter a{color:#0d9488;text-decoration:none}

.flash{padding:12px 18px;border-radius:10px;margin:14px auto;max-width:700px;font-size:0.9rem;font-weight:500;text-align:center}
.flash.success{background:#d1fae5;color:#065f46;border:1px solid #a7f3d0}
.flash.error{background:#fee2e2;color:#991b1b;border:1px solid #fca5a5}
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

<div class="progress-wrap">
    <div class="progress-inner">
        <div class="progress-line"><div class="progress-line-fill" id="progFill"></div></div>
        <div class="progress-step active" data-step="1"><div class="progress-circle">1</div><div class="progress-label">Business</div></div>
        <div class="progress-step"        data-step="2"><div class="progress-circle">2</div><div class="progress-label">Features</div></div>
        <div class="progress-step"        data-step="3"><div class="progress-circle">3</div><div class="progress-label">Billing</div></div>
        <div class="progress-step"        data-step="4"><div class="progress-circle">4</div><div class="progress-label">Review</div></div>
    </div>
</div>

@if(session('success'))<div class="flash success">{{ session('success') }}</div>@endif
@if(session('error'))<div class="flash error">{{ session('error') }}</div>@endif

<div class="wizard">

    {{-- ─── STEP 1: BUSINESS TYPE ─────────────────────────────── --}}
    <div class="step-panel active" id="step-1">
        <h2 class="step-title">What kind of business do you run?</h2>
        <p class="step-subtitle">We'll suggest the features that fit you best. You can change anything on the next step.</p>

        <div class="btype-grid">
            <div class="btype-card" data-btype="pharmacy"   data-categories="core,inventory,pharmacy,reporting">
                <div class="btype-icon"><i class="fas fa-pills"></i></div>
                <div class="btype-name">Pharmacy</div>
                <div class="btype-desc">Chemists, drugstores, DDA dispensers</div>
            </div>
            <div class="btype-card" data-btype="retail"     data-categories="core,inventory,reporting,communication">
                <div class="btype-icon"><i class="fas fa-store"></i></div>
                <div class="btype-name">Retail / Shop</div>
                <div class="btype-desc">Supermarkets, boutiques, general merchandise</div>
            </div>
            <div class="btype-card" data-btype="restaurant" data-categories="core,inventory,restaurant,reporting">
                <div class="btype-icon"><i class="fas fa-utensils"></i></div>
                <div class="btype-name">Restaurant / Café</div>
                <div class="btype-desc">Eateries, hotels, bars with kitchen</div>
            </div>
            <div class="btype-card" data-btype="clinic"     data-categories="core,pharmacy,reporting,communication">
                <div class="btype-icon"><i class="fas fa-stethoscope"></i></div>
                <div class="btype-name">Clinic / Hospital</div>
                <div class="btype-desc">Medical practices, diagnostic labs</div>
            </div>
            <div class="btype-card" data-btype="service"    data-categories="core,reporting,communication">
                <div class="btype-icon"><i class="fas fa-briefcase"></i></div>
                <div class="btype-name">Service Business</div>
                <div class="btype-desc">Consulting, agencies, professional services</div>
            </div>
            <div class="btype-card" data-btype="other"      data-categories="core,inventory,pharmacy,reporting,communication,restaurant">
                <div class="btype-icon"><i class="fas fa-th"></i></div>
                <div class="btype-name">Something Else</div>
                <div class="btype-desc">Show me all available features</div>
            </div>
        </div>
    </div>

    {{-- ─── STEP 2: FEATURES ──────────────────────────────────── --}}
    <div class="step-panel" id="step-2">
        <h2 class="step-title">Pick your features</h2>
        <p class="step-subtitle">We've pre-selected what most <span id="btype-label" style="color:#0d9488;font-weight:700;">businesses</span> start with. Add or remove any.</p>

        <div class="feat-layout">
            <div>
                @if($bundles->count())
                <div class="section-label">Quick-Start Bundles</div>
                <div class="bundles-row">
                    @foreach($bundles as $bundle)
                    <button type="button" class="bundle-chip" data-bundle-id="{{ $bundle->id }}"
                            data-feature-ids="{{ $bundle->features->pluck('id')->join(',') }}">
                        @if($bundle->is_popular)<span class="bundle-popular">POPULAR</span>@endif
                        <div class="bc-name">{{ $bundle->name }}</div>
                        <div class="bc-desc">{{ $bundle->description }}</div>
                    </button>
                    @endforeach
                </div>
                @endif

                <div class="section-label">Individual Features</div>

                @foreach($categories as $catKey => $catMeta)
                @if($featuresByCategory->has($catKey))
                <div class="feat-group" data-category="{{ $catKey }}">
                    <div class="feat-group-header">
                        <i class="fas {{ $catMeta['icon'] }}"></i>
                        {{ $catMeta['label'] }}
                    </div>
                    @foreach($featuresByCategory[$catKey] as $feature)
                    <div class="feat-row" data-feature-id="{{ $feature->id }}" data-category="{{ $catKey }}">
                        <div class="feat-check {{ $feature->is_required ? 'checked required' : '' }}"
                             id="check-{{ $feature->id }}"
                             onclick="{{ $feature->is_required ? '' : 'toggleFeature(' . $feature->id . ')' }}">
                            <svg width="13" height="13" viewBox="0 0 13 13" fill="none">
                                <path d="M2 6.5L5 9.5L11 3.5" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <div class="feat-info">
                            <div class="feat-name">
                                <span class="feat-name-text">{{ $feature->name }}</span>
                                @if($feature->is_required)<span class="feat-req-badge">Included</span>@endif
                                <span class="feat-recommended" style="display:none;" id="rec-{{ $feature->id }}">Recommended</span>
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

            <div>
                <div class="summary-card">
                    <h3>Your Selection</h3>
                    <div class="summary-items" id="summary-items">
                        <div class="summary-empty" id="summary-empty">No features selected yet.</div>
                    </div>
                    <div class="summary-total">
                        <span class="summary-total-label">Subtotal</span>
                        <span class="summary-total-amount" id="summary-total">KES 0</span>
                    </div>
                    <div style="font-size:0.74rem;color:#94a3b8;margin-top:6px;text-align:right;" id="summary-cycle-note">per month</div>
                </div>
            </div>
        </div>
    </div>

    {{-- ─── STEP 3: BILLING + HOSTING ─────────────────────────── --}}
    <div class="step-panel" id="step-3">
        <h2 class="step-title">Choose how you'd like to pay</h2>
        <p class="step-subtitle">Longer commitments save you money. You can switch later.</p>

        <div class="cycle-grid">
            <div class="cycle-card selected" data-cycle="monthly">
                <div class="cycle-label">Monthly</div>
                <div class="cycle-sublabel">Most flexible</div>
                <div style="color:#64748b;font-size:0.78rem;">Billed every month</div>
            </div>
            <div class="cycle-card" data-cycle="quarterly">
                <div class="cycle-label">Quarterly</div>
                <div class="cycle-sublabel">Every 3 months</div>
                <div class="cycle-savings">Save 10%</div>
            </div>
            <div class="cycle-card" data-cycle="yearly">
                <div class="cycle-label">Yearly</div>
                <div class="cycle-sublabel">Best value</div>
                <div class="cycle-savings">Save 20%</div>
            </div>
            <div class="cycle-card" data-cycle="once">
                <div class="cycle-label">One-Off</div>
                <div class="cycle-sublabel">Lifetime license</div>
                <div style="color:#64748b;font-size:0.78rem;">Self-hosted only</div>
            </div>
        </div>

        <div class="hosting-section">
            <h4><i class="fas fa-server" style="color:#0d9488;margin-right:6px;"></i> Where do you want it hosted?</h4>
            <div class="hosting-opts">
                <div class="hosting-opt selected" data-hosting="cloud">
                    <div class="ho-title">☁️ Cloud Hosted <span style="font-size:0.68rem;background:#0d9488;color:white;padding:2px 7px;border-radius:50px;margin-left:4px;">RECOMMENDED</span></div>
                    <div class="ho-sub">We host and manage everything. Backups, updates, 24/7 uptime.</div>
                </div>
                <div class="hosting-opt" data-hosting="self_hosted">
                    <div class="ho-title">🖥️ Self-Hosted</div>
                    <div class="ho-sub">Install on your own server. One-off license + yearly support fee.</div>
                </div>
            </div>
        </div>
    </div>

    {{-- ─── STEP 4: REVIEW ────────────────────────────────────── --}}
    <div class="step-panel" id="step-4">
        <h2 class="step-title">Review your plan</h2>
        <p class="step-subtitle">Looks good? Continue to create your account and complete setup.</p>

        <div class="review-wrap">
            <div class="review-card">
                <h4><i class="fas fa-list-check"></i> Selected Features</h4>
                <div id="review-features"></div>
            </div>
            <div class="review-card">
                <h4><i class="fas fa-receipt"></i> Plan Details</h4>
                <div class="review-row"><span>Business Type</span><strong id="review-btype">—</strong></div>
                <div class="review-row"><span>Billing Cycle</span><strong id="review-cycle" style="text-transform:capitalize;">—</strong></div>
                <div class="review-row"><span>Hosting</span><strong id="review-hosting">—</strong></div>
                <div class="review-row"><span>Features</span><strong id="review-count">0</strong></div>
            </div>
        </div>

        <div class="review-total-box">
            <div class="review-total-label">Total</div>
            <div class="review-total-amount" id="review-total">KES 0</div>
            <div class="review-cycle-note" id="review-total-note">per month</div>
        </div>
    </div>

</div>

<div class="wizard-nav">
    <button class="btn-wiz btn-back" id="btn-back" onclick="goBack()" style="visibility:hidden;">
        <i class="fas fa-arrow-left"></i> Back
    </button>
    <button class="btn-wiz btn-next" id="btn-next" onclick="goNext()" disabled>
        Continue <i class="fas fa-arrow-right"></i>
    </button>
</div>

<footer class="pfooter">
    &copy; {{ date('Y') }} {{ config('app.name') }}.
    Questions? <a href="mailto:{{ config('mail.from.address', 'info@apexpos.co.ke') }}">Contact us</a>
</footer>

<script>
/* ═══════════════════ STATE ═══════════════════ */
let currentStep      = 1;
let selectedBtype    = null;
let selectedCats     = [];
let selectedFeatures = {};
let currentCycle     = 'monthly';
let currentHosting   = 'cloud';

const btypeLabels = {
    pharmacy:'pharmacies',retail:'retailers',restaurant:'restaurants',
    clinic:'clinics',service:'service businesses',other:'businesses'
};

/* Pre-select required features */
@foreach($featuresByCategory->flatten() as $feature)
@if($feature->is_required)
selectedFeatures[{{ $feature->id }}] = {
    name: "{{ addslashes($feature->name) }}",
    monthly: {{ $feature->price_monthly }},
    quarterly: {{ $feature->price_quarterly }},
    yearly: {{ $feature->price_yearly }},
    once: {{ $feature->price_once }},
    required: true,
    category: "{{ $feature->category }}"
};
@endif
@endforeach

/* ═══════════════════ NAVIGATION ═══════════════════ */
function showStep(n) {
    document.querySelectorAll('.step-panel').forEach(el => el.classList.remove('active'));
    document.getElementById('step-' + n).classList.add('active');
    document.querySelectorAll('.progress-step').forEach(el => {
        const s = parseInt(el.dataset.step);
        el.classList.remove('active','done');
        if (s < n) el.classList.add('done');
        if (s === n) el.classList.add('active');
    });
    document.getElementById('progFill').style.width = ((n - 1) / 3 * 100) + '%';
    document.getElementById('btn-back').style.visibility = n === 1 ? 'hidden' : 'visible';

    const btnNext = document.getElementById('btn-next');
    btnNext.innerHTML = (n === 4)
        ? 'Create Account &amp; Continue <i class="fas fa-arrow-right"></i>'
        : 'Continue <i class="fas fa-arrow-right"></i>';

    currentStep = n;
    updateNextButton();
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function goNext() {
    if (currentStep === 1) {
        if (!selectedBtype) return;
        applyBusinessTypePreset();
        showStep(2);
    } else if (currentStep === 2) {
        if (Object.keys(selectedFeatures).length === 0) return;
        showStep(3);
    } else if (currentStep === 3) {
        showStep(4);
        renderReview();
    } else if (currentStep === 4) {
        goToCheckout();
    }
}

function goBack() {
    if (currentStep > 1) showStep(currentStep - 1);
}

function updateNextButton() {
    const btn = document.getElementById('btn-next');
    if (currentStep === 1)      btn.disabled = !selectedBtype;
    else if (currentStep === 2) btn.disabled = Object.keys(selectedFeatures).length === 0;
    else                         btn.disabled = false;
}

/* ═══════════════════ STEP 1 ═══════════════════ */
document.querySelectorAll('.btype-card').forEach(card => {
    card.addEventListener('click', () => {
        document.querySelectorAll('.btype-card').forEach(c => c.classList.remove('selected'));
        card.classList.add('selected');
        selectedBtype = card.dataset.btype;
        selectedCats  = card.dataset.categories.split(',');
        updateNextButton();
    });
});

function applyBusinessTypePreset() {
    document.getElementById('btype-label').textContent = btypeLabels[selectedBtype] || 'businesses';

    document.querySelectorAll('.feat-group').forEach(g => {
        const cat = g.dataset.category;
        g.style.opacity = (selectedBtype === 'other' || selectedCats.includes(cat)) ? '1' : '0.55';
    });

    document.querySelectorAll('.feat-row').forEach(row => {
        const cat = row.dataset.category;
        const fid = row.dataset.featureId;
        const rec = document.getElementById('rec-' + fid);
        if (rec && selectedCats.includes(cat) && cat !== 'core' && selectedBtype !== 'other') {
            rec.style.display = 'inline-block';
        } else if (rec) {
            rec.style.display = 'none';
        }
    });

    updateSummary();
}

/* ═══════════════════ STEP 2 ═══════════════════ */
function toggleFeature(id) {
    const row     = document.querySelector(`[data-feature-id="${id}"]`);
    const check   = document.getElementById(`check-${id}`);
    const priceEl = document.getElementById(`price-${id}`);
    const name    = row.querySelector('.feat-name-text').textContent.trim();

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
            required:  false,
            category:  row.dataset.category
        };
        check.classList.add('checked');
    }
    updateSummary();
    updateNextButton();
}

document.querySelectorAll('.bundle-chip').forEach(chip => {
    chip.addEventListener('click', () => {
        document.querySelectorAll('.bundle-chip').forEach(c => c.classList.remove('selected'));
        chip.classList.add('selected');

        const ids = chip.dataset.featureIds.split(',').map(Number).filter(Boolean);

        Object.keys(selectedFeatures).forEach(id => {
            if (!selectedFeatures[id].required) {
                delete selectedFeatures[id];
                const c = document.getElementById(`check-${id}`);
                if (c) c.classList.remove('checked');
            }
        });

        ids.forEach(id => {
            const priceEl = document.getElementById(`price-${id}`);
            const row = document.querySelector(`[data-feature-id="${id}"]`);
            if (!row || !priceEl) return;
            const name = row.querySelector('.feat-name-text').textContent.trim();
            selectedFeatures[id] = {
                name,
                monthly:   parseFloat(priceEl.dataset.monthly || 0),
                quarterly: parseFloat(priceEl.dataset.quarterly || 0),
                yearly:    parseFloat(priceEl.dataset.yearly || 0),
                once:      parseFloat(priceEl.dataset.once || 0),
                required:  false,
                category:  row.dataset.category
            };
            const c = document.getElementById(`check-${id}`);
            if (c) c.classList.add('checked');
        });

        updateSummary();
        updateNextButton();
    });
});

function updateSummary() {
    const itemsEl = document.getElementById('summary-items');
    const emptyEl = document.getElementById('summary-empty');
    const totalEl = document.getElementById('summary-total');
    const noteEl  = document.getElementById('summary-cycle-note');

    const ids = Object.keys(selectedFeatures);
    let total = 0;

    itemsEl.querySelectorAll('.summary-item').forEach(e => e.remove());

    if (ids.length === 0) {
        emptyEl.style.display = 'block';
        totalEl.textContent = 'KES 0';
        return;
    }

    emptyEl.style.display = 'none';

    ids.forEach(id => {
        const f     = selectedFeatures[id];
        const price = f[currentCycle] ?? f.monthly;
        total += price;
        const div = document.createElement('div');
        div.className = 'summary-item';
        div.innerHTML = `<span>${f.name}</span><span style="font-weight:600;">${price === 0 ? 'Free' : 'KES ' + price.toLocaleString()}</span>`;
        itemsEl.appendChild(div);
    });

    totalEl.textContent = 'KES ' + total.toLocaleString();
    const notes = { monthly:'per month', quarterly:'per quarter', yearly:'per year', once:'one-off payment' };
    noteEl.textContent = notes[currentCycle] || '';
}

/* ═══════════════════ STEP 3 ═══════════════════ */
document.querySelectorAll('.cycle-card').forEach(card => {
    card.addEventListener('click', () => {
        document.querySelectorAll('.cycle-card').forEach(c => c.classList.remove('selected'));
        card.classList.add('selected');
        currentCycle = card.dataset.cycle;

        document.querySelectorAll('.feat-price').forEach(el => {
            const price = parseFloat(el.dataset[currentCycle] ?? el.dataset.monthly);
            el.textContent = price === 0 ? 'Free' : 'KES ' + price.toLocaleString();
            el.className = 'feat-price' + (price === 0 ? ' free' : '');
        });

        updateSummary();

        if (currentCycle === 'once') selectHostingInternal('self_hosted');
    });
});

document.querySelectorAll('.hosting-opt').forEach(opt => {
    opt.addEventListener('click', () => selectHostingInternal(opt.dataset.hosting));
});

function selectHostingInternal(type) {
    currentHosting = type;
    document.querySelectorAll('.hosting-opt').forEach(o => o.classList.remove('selected'));
    const match = document.querySelector(`.hosting-opt[data-hosting="${type}"]`);
    if (match) match.classList.add('selected');
}

/* ═══════════════════ STEP 4 ═══════════════════ */
function renderReview() {
    const revFeats = document.getElementById('review-features');
    revFeats.innerHTML = '';

    let total = 0;
    Object.keys(selectedFeatures).forEach(id => {
        const f = selectedFeatures[id];
        const price = f[currentCycle] ?? f.monthly;
        total += price;
        const div = document.createElement('div');
        div.className = 'review-row';
        div.innerHTML = `<span>${f.name}</span><strong>${price === 0 ? 'Free' : 'KES ' + price.toLocaleString()}</strong>`;
        revFeats.appendChild(div);
    });

    document.getElementById('review-btype').textContent =
        (selectedBtype || '').charAt(0).toUpperCase() + (selectedBtype || '').slice(1);
    document.getElementById('review-cycle').textContent   = currentCycle;
    document.getElementById('review-hosting').textContent = currentHosting === 'cloud' ? '☁️ Cloud' : '🖥️ Self-Hosted';
    document.getElementById('review-count').textContent   = Object.keys(selectedFeatures).length;
    document.getElementById('review-total').textContent   = 'KES ' + total.toLocaleString();

    const notes = { monthly:'per month', quarterly:'per quarter', yearly:'per year', once:'one-off payment' };
    document.getElementById('review-total-note').textContent = notes[currentCycle] || '';
}

function goToCheckout() {
    const ids = Object.keys(selectedFeatures).join(',');
    if (!ids) return;
    const params = new URLSearchParams({
        feature_ids: ids,
        cycle:       currentCycle,
        hosting:     currentHosting,
        btype:       selectedBtype || ''
    });
    window.location = `{{ route('saas.checkout') }}?${params.toString()}`;
}

/* INIT */
updateSummary();
updateNextButton();
</script>
</body>
</html>
