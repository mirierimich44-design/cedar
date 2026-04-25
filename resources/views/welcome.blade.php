@extends('layouts.auth2')
@section('title', config('app.name', 'Apex POS') . ' — Build Your Own Business System')
@inject('request', 'Illuminate\Http\Request')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<style>
html, body { background:#0a1628 !important; margin:0; padding:0; }
.right-col, .container-fluid, .row.eq-height-row { padding:0 !important; margin:0 !important; }

.lp {
    position:fixed; inset:0; z-index:9990; overflow-y:auto;
    background:
        radial-gradient(ellipse at top, rgba(13,148,136,0.18), transparent 55%),
        linear-gradient(rgba(5,15,35,0.94), rgba(5,20,45,0.97)),
        url('{{ asset("img/home-bg.jpg") }}') center/cover no-repeat;
    background-color:#0a1628;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
}

/* ── NAV ───────────────────────────────────────────── */
.lp-nav { position:sticky; top:0; z-index:1000; display:flex; align-items:center; justify-content:space-between; padding:14px 40px; background:rgba(5,15,35,0.72); backdrop-filter:blur(14px); border-bottom:1px solid rgba(255,255,255,0.06); }
.lp-brand { display:flex; align-items:center; gap:10px; text-decoration:none; }
.lp-brand img { width:34px; height:34px; border-radius:8px; background:white; padding:3px; object-fit:contain; }
.lp-brand span { color:white; font-size:1.05rem; font-weight:800; }
.lp-menu { display:flex; gap:26px; align-items:center; }
.lp-menu a { color:rgba(255,255,255,0.72); font-size:.9rem; font-weight:600; text-decoration:none; }
.lp-menu a:hover { color:white; }
.lp-btn { background:#0d9488; color:white !important; font-weight:700; font-size:.875rem; padding:9px 22px; border-radius:50px; text-decoration:none; transition:.2s; }
.lp-btn:hover { background:#0f766e; color:white !important; }
.lp-btn.ghost { background:transparent; border:1px solid rgba(255,255,255,0.2); }
.lp-btn.ghost:hover { background:rgba(255,255,255,0.08); }

/* ── HERO ──────────────────────────────────────────── */
.hero { max-width:1100px; margin:0 auto; padding:90px 24px 40px; text-align:center; }
.hero-badge { display:inline-flex; align-items:center; gap:8px; background:rgba(13,148,136,0.15); border:1px solid rgba(13,148,136,0.35); color:#5eead4; font-size:.78rem; font-weight:700; letter-spacing:1px; text-transform:uppercase; padding:7px 14px; border-radius:50px; margin-bottom:22px; }
.hero h1 { font-size:clamp(2.2rem, 5.2vw, 3.8rem); font-weight:900; color:white; margin:0 0 18px; line-height:1.1; letter-spacing:-.02em; }
.hero h1 .hl { background:linear-gradient(120deg,#5eead4,#0d9488); -webkit-background-clip:text; background-clip:text; color:transparent; }
.hero p { font-size:clamp(1rem,1.8vw,1.2rem); color:rgba(255,255,255,0.78); max-width:640px; margin:0 auto 30px; line-height:1.6; }
.hero-ctas { display:flex; gap:12px; justify-content:center; flex-wrap:wrap; margin-bottom:22px; }
.hero-cta { display:inline-flex; align-items:center; gap:8px; background:#0d9488; color:white !important; font-weight:800; font-size:1rem; padding:16px 34px; border-radius:50px; text-decoration:none; box-shadow:0 10px 28px rgba(13,148,136,0.45); transition:.2s; min-height:52px; }
.hero-cta:hover { background:#0f766e; transform:translateY(-2px); color:white !important; }
.hero-cta.outline { background:transparent; border:1.5px solid rgba(255,255,255,0.3); box-shadow:none; }
.hero-cta.outline:hover { background:rgba(255,255,255,0.06); border-color:white; }
.hero-trust { display:flex; justify-content:center; gap:26px; flex-wrap:wrap; margin-top:36px; color:rgba(255,255,255,0.55); font-size:.82rem; }
.hero-trust span i { color:#5eead4; margin-right:6px; }

/* ── STATS STRIP ───────────────────────────────────── */
.stats { max-width:1000px; margin:20px auto 60px; padding:0 24px; display:grid; grid-template-columns:repeat(4,1fr); gap:18px; }
@media(max-width:700px){ .stats{ grid-template-columns:repeat(2,1fr);} }
.stat { background:rgba(15,23,42,0.5); border:1px solid rgba(255,255,255,0.08); border-radius:16px; padding:20px; text-align:center; }
.stat-num { font-size:1.9rem; font-weight:900; color:#5eead4; margin-bottom:4px; }
.stat-lbl { font-size:.78rem; color:rgba(255,255,255,0.6); text-transform:uppercase; letter-spacing:1px; font-weight:600; }

/* ── SECTION ───────────────────────────────────────── */
.sec { max-width:1140px; margin:0 auto; padding:70px 24px; }
.sec-head { text-align:center; margin-bottom:50px; }
.sec-eyebrow { color:#5eead4; font-size:.78rem; font-weight:700; letter-spacing:2px; text-transform:uppercase; margin-bottom:12px; }
.sec-head h2 { font-size:clamp(1.8rem, 3.6vw, 2.6rem); font-weight:900; color:white; margin:0 0 14px; letter-spacing:-.02em; }
.sec-head p { color:rgba(255,255,255,0.65); font-size:1.05rem; max-width:620px; margin:0 auto; line-height:1.65; }

/* ── WHO IS IT FOR ─────────────────────────────────── */
.personas { display:grid; grid-template-columns:repeat(4,1fr); gap:16px; }
@media(max-width:900px){ .personas{ grid-template-columns:repeat(2,1fr);} }
@media(max-width:500px){ .personas{ grid-template-columns:1fr;} }
.persona { background:rgba(15,23,42,0.7); border:1px solid rgba(255,255,255,0.1); border-radius:18px; padding:26px 22px; text-align:center; transition:.25s; }
.persona:hover { border-color:rgba(13,148,136,0.5); transform:translateY(-4px); background:rgba(15,23,42,0.95); }
.persona-icon { font-size:2.2rem; margin-bottom:14px; }
.persona h4 { color:white; font-size:1.02rem; margin:0 0 6px; font-weight:800; }
.persona p { color:rgba(255,255,255,0.6); font-size:.82rem; line-height:1.55; margin:0; }

/* ── FEATURES GRID (ALL SYSTEM FEATURES) ───────────── */
.cat-nav { display:flex; justify-content:center; flex-wrap:wrap; gap:8px; margin-bottom:36px; }
.cat-pill { background:rgba(255,255,255,0.06); border:1px solid rgba(255,255,255,0.1); color:rgba(255,255,255,0.75); padding:9px 18px; border-radius:50px; font-size:.82rem; font-weight:600; cursor:pointer; transition:.2s; }
.cat-pill:hover { background:rgba(255,255,255,0.1); color:white; }
.cat-pill.active { background:#0d9488; border-color:#0d9488; color:white; }

.fgrid { display:grid; grid-template-columns:repeat(3,1fr); gap:18px; }
@media(max-width:900px){ .fgrid{ grid-template-columns:repeat(2,1fr);} }
@media(max-width:560px){ .fgrid{ grid-template-columns:1fr;} }
.fcard { background:rgba(15,23,42,0.65); border:1px solid rgba(255,255,255,0.08); border-radius:16px; padding:22px; transition:.25s; display:flex; gap:14px; align-items:flex-start; }
.fcard:hover { border-color:rgba(13,148,136,0.45); background:rgba(15,23,42,0.9); transform:translateY(-3px); }
.fcard-icon { flex-shrink:0; width:42px; height:42px; border-radius:10px; background:rgba(13,148,136,0.18); color:#5eead4; display:flex; align-items:center; justify-content:center; font-size:1.15rem; }
.fcard h4 { color:white; margin:0 0 6px; font-size:.95rem; font-weight:700; }
.fcard p { color:rgba(255,255,255,0.6); font-size:.82rem; line-height:1.55; margin:0; }

/* ── HOW IT WORKS ──────────────────────────────────── */
.steps { display:grid; grid-template-columns:repeat(4,1fr); gap:20px; counter-reset:step; }
@media(max-width:900px){ .steps{ grid-template-columns:repeat(2,1fr);} }
@media(max-width:500px){ .steps{ grid-template-columns:1fr;} }
.step { background:rgba(15,23,42,0.6); border:1px solid rgba(255,255,255,0.08); border-radius:18px; padding:24px; position:relative; counter-increment:step; }
.step::before { content:counter(step); position:absolute; top:-14px; left:20px; background:#0d9488; color:white; width:28px; height:28px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:900; font-size:.85rem; box-shadow:0 4px 12px rgba(13,148,136,0.5); }
.step h4 { color:white; margin:10px 0 8px; font-size:1rem; font-weight:700; }
.step p { color:rgba(255,255,255,0.62); font-size:.85rem; line-height:1.55; margin:0; }

/* ── PRICING TEASER ────────────────────────────────── */
.pricing-teaser { background:linear-gradient(135deg, rgba(13,148,136,0.15), rgba(13,148,136,0.05)); border:1px solid rgba(13,148,136,0.3); border-radius:24px; padding:48px 36px; text-align:center; max-width:880px; margin:0 auto; }
.pricing-teaser h3 { color:white; font-size:1.8rem; font-weight:900; margin:0 0 12px; }
.pricing-teaser p { color:rgba(255,255,255,0.75); margin:0 0 24px; font-size:1rem; }
.teaser-chips { display:flex; justify-content:center; flex-wrap:wrap; gap:10px; margin-bottom:28px; }
.teaser-chip { background:rgba(255,255,255,0.08); border:1px solid rgba(255,255,255,0.15); color:rgba(255,255,255,0.85); padding:7px 14px; border-radius:50px; font-size:.78rem; font-weight:600; }
.teaser-chip i { color:#5eead4; margin-right:6px; }

/* ── SOCIAL PROOF ──────────────────────────────────── */
.proof { background:rgba(15,23,42,0.5); border-top:1px solid rgba(255,255,255,0.06); border-bottom:1px solid rgba(255,255,255,0.06); padding:40px 24px; text-align:center; }
.proof-label { color:rgba(255,255,255,0.5); font-size:.72rem; letter-spacing:2px; font-weight:600; text-transform:uppercase; margin-bottom:18px; }
.proof-logos { display:flex; justify-content:center; gap:40px; flex-wrap:wrap; color:rgba(255,255,255,0.4); font-size:.95rem; font-weight:700; font-style:italic; }

/* ── FAQ ───────────────────────────────────────────── */
.faq-wrap { max-width:820px; margin:0 auto; }
.faq { background:rgba(15,23,42,0.55); border:1px solid rgba(255,255,255,0.08); border-radius:14px; margin-bottom:10px; overflow:hidden; transition:.2s; }
.faq[open] { border-color:rgba(13,148,136,0.4); }
.faq summary { padding:18px 22px; color:white; font-weight:700; font-size:.98rem; cursor:pointer; list-style:none; display:flex; justify-content:space-between; align-items:center; }
.faq summary::-webkit-details-marker { display:none; }
.faq summary::after { content:'+'; color:#5eead4; font-size:1.3rem; font-weight:300; transition:.2s; }
.faq[open] summary::after { transform:rotate(45deg); }
.faq-body { padding:0 22px 20px; color:rgba(255,255,255,0.7); font-size:.9rem; line-height:1.7; }

/* ── FINAL CTA ─────────────────────────────────────── */
.final-cta { text-align:center; padding:80px 24px; background:linear-gradient(135deg, rgba(13,148,136,0.12), transparent); }
.final-cta h2 { color:white; font-size:clamp(1.8rem,3.6vw,2.6rem); font-weight:900; margin:0 0 14px; letter-spacing:-.02em; }
.final-cta p { color:rgba(255,255,255,0.7); max-width:560px; margin:0 auto 28px; font-size:1.05rem; line-height:1.6; }

/* ── FOOTER ────────────────────────────────────────── */
.lp-foot { text-align:center; color:rgba(255,255,255,0.35); font-size:.78rem; padding:30px 20px; border-top:1px solid rgba(255,255,255,0.05); }
.lp-foot a { color:rgba(255,255,255,0.55); text-decoration:none; margin:0 10px; }

@media(max-width:600px){
    .lp-nav { padding:12px 18px; }
    .lp-menu { gap:14px; }
    .lp-menu a:not(.lp-btn) { display:none; }
    .hero { padding:60px 18px 24px; }
}
</style>

<div class="lp">

    {{-- NAV --}}
    <nav class="lp-nav">
        <a href="{{ url('/') }}" class="lp-brand">
            <img src="{{ asset('img/logo-small.png') }}" alt="logo">
            <span>{{ config('app.name', 'Apex POS') }}</span>
        </a>
        <div class="lp-menu">
            <a href="#features">Features</a>
            <a href="#how">How it works</a>
            <a href="#faq">FAQ</a>
            <a href="{{ route('saas.pricing') }}" class="lp-btn ghost">Pricing</a>
            <a href="{{ action([\App\Http\Controllers\Auth\LoginController::class, 'login']) }}" class="lp-btn">Sign In</a>
        </div>
    </nav>

    {{-- HERO --}}
    <section class="hero">
        <span class="hero-badge">⚡ Built for Kenyan businesses · KRA eTIMS ready</span>
        <h1>Run your whole business <span class="hl">from one system.</span></h1>
        <p>POS, inventory, invoicing, reporting, SMS and more — configured for your shop, pharmacy, restaurant or clinic. Pick only the features you need. Pay only for what you use.</p>

        <div class="hero-ctas">
            <a href="{{ route('saas.pricing') }}" class="hero-cta">
                Get Started Free <i class="fas fa-arrow-right"></i>
            </a>
            <a href="#features" class="hero-cta outline">See all features</a>
        </div>

        <div class="hero-trust">
            <span><i class="fas fa-check-circle"></i> No setup fees on cloud</span>
            <span><i class="fas fa-check-circle"></i> Cancel anytime</span>
            <span><i class="fas fa-check-circle"></i> Local support</span>
            <span><i class="fas fa-check-circle"></i> KRA eTIMS compliant</span>
        </div>
    </section>

    {{-- STATS --}}
    <div class="stats">
        <div class="stat"><div class="stat-num">30+</div><div class="stat-lbl">Modules</div></div>
        <div class="stat"><div class="stat-num">4</div><div class="stat-lbl">Business Types</div></div>
        <div class="stat"><div class="stat-num">2</div><div class="stat-lbl">Hosting Options</div></div>
        <div class="stat"><div class="stat-num">24h</div><div class="stat-lbl">Activation</div></div>
    </div>

    {{-- WHO IS IT FOR --}}
    <section class="sec">
        <div class="sec-head">
            <div class="sec-eyebrow">Built for you</div>
            <h2>One platform. Many businesses.</h2>
            <p>Whatever you sell, we've got the modules to run it end-to-end.</p>
        </div>
        <div class="personas">
            <div class="persona">
                <div class="persona-icon">🏪</div>
                <h4>Retail &amp; Shops</h4>
                <p>POS, barcode, stock, supplier orders, multi-branch.</p>
            </div>
            <div class="persona">
                <div class="persona-icon">💊</div>
                <h4>Pharmacies</h4>
                <p>DDA compliance, prescriptions, expiry alerts, dispense logs.</p>
            </div>
            <div class="persona">
                <div class="persona-icon">🍽️</div>
                <h4>Restaurants</h4>
                <p>Menus, tables, KOT printing, split bills, waiter tips.</p>
            </div>
            <div class="persona">
                <div class="persona-icon">🛠️</div>
                <h4>Service &amp; Clinics</h4>
                <p>Appointments, invoicing, patient records, SMS reminders.</p>
            </div>
        </div>
    </section>

    {{-- ALL SYSTEM FEATURES --}}
    <section class="sec" id="features">
        <div class="sec-head">
            <div class="sec-eyebrow">Every module, one roof</div>
            <h2>The complete feature library.</h2>
            <p>Mix and match any of these into your plan. No forced bundles, no bloat.</p>
        </div>

        <div class="cat-nav">
            <button class="cat-pill active" data-cat="all">All</button>
            <button class="cat-pill" data-cat="sales">Sales &amp; POS</button>
            <button class="cat-pill" data-cat="inventory">Inventory</button>
            <button class="cat-pill" data-cat="accounting">Accounting</button>
            <button class="cat-pill" data-cat="pharmacy">Pharmacy</button>
            <button class="cat-pill" data-cat="restaurant">Restaurant</button>
            <button class="cat-pill" data-cat="reports">Reports</button>
            <button class="cat-pill" data-cat="comms">Communication</button>
            <button class="cat-pill" data-cat="compliance">Compliance</button>
        </div>

        <div class="fgrid">
            {{-- Sales --}}
            <div class="fcard" data-cat="sales"><div class="fcard-icon"><i class="fas fa-cash-register"></i></div><div><h4>Point of Sale</h4><p>Lightning-fast till with barcode, discounts, multi-payment.</p></div></div>
            <div class="fcard" data-cat="sales"><div class="fcard-icon"><i class="fas fa-file-invoice"></i></div><div><h4>Invoicing &amp; Quotes</h4><p>Professional invoices, quotes, delivery notes, auto-reminders.</p></div></div>
            <div class="fcard" data-cat="sales"><div class="fcard-icon"><i class="fas fa-users"></i></div><div><h4>Customers &amp; CRM</h4><p>Customer profiles, credit limits, statements, loyalty points.</p></div></div>
            <div class="fcard" data-cat="sales"><div class="fcard-icon"><i class="fas fa-mobile-alt"></i></div><div><h4>M-Pesa Integration</h4><p>STK Push, Paybill, Till reconciliation — all automated.</p></div></div>

            {{-- Inventory --}}
            <div class="fcard" data-cat="inventory"><div class="fcard-icon"><i class="fas fa-boxes"></i></div><div><h4>Stock Control</h4><p>Real-time stock, low-stock alerts, batch/expiry tracking.</p></div></div>
            <div class="fcard" data-cat="inventory"><div class="fcard-icon"><i class="fas fa-truck"></i></div><div><h4>Suppliers &amp; Purchases</h4><p>Purchase orders, GRN, supplier credit, landed cost.</p></div></div>
            <div class="fcard" data-cat="inventory"><div class="fcard-icon"><i class="fas fa-warehouse"></i></div><div><h4>Multi-Branch Stock</h4><p>Transfer between branches, view consolidated stock live.</p></div></div>
            <div class="fcard" data-cat="inventory"><div class="fcard-icon"><i class="fas fa-barcode"></i></div><div><h4>Barcode &amp; Labels</h4><p>Generate, print and scan barcodes on any printer.</p></div></div>

            {{-- Accounting --}}
            <div class="fcard" data-cat="accounting"><div class="fcard-icon"><i class="fas fa-book"></i></div><div><h4>Accounting</h4><p>Ledgers, chart of accounts, journals, trial balance.</p></div></div>
            <div class="fcard" data-cat="accounting"><div class="fcard-icon"><i class="fas fa-wallet"></i></div><div><h4>Expenses</h4><p>Record expenses, attach receipts, approve workflows.</p></div></div>
            <div class="fcard" data-cat="accounting"><div class="fcard-icon"><i class="fas fa-user-tie"></i></div><div><h4>Payroll</h4><p>PAYE, NHIF, NSSF, SHIF calculations and payslips.</p></div></div>

            {{-- Pharmacy --}}
            <div class="fcard" data-cat="pharmacy"><div class="fcard-icon"><i class="fas fa-pills"></i></div><div><h4>DDA Drug Control</h4><p>Full DDA compliance — dispense logs, stock, destruction.</p></div></div>
            <div class="fcard" data-cat="pharmacy"><div class="fcard-icon"><i class="fas fa-prescription"></i></div><div><h4>Prescriptions</h4><p>Create, track, dispense with complete patient audit.</p></div></div>
            <div class="fcard" data-cat="pharmacy"><div class="fcard-icon"><i class="fas fa-user-md"></i></div><div><h4>Doctors &amp; Patients</h4><p>Patient records, doctor directory, prescription history.</p></div></div>

            {{-- Restaurant --}}
            <div class="fcard" data-cat="restaurant"><div class="fcard-icon"><i class="fas fa-utensils"></i></div><div><h4>Table Management</h4><p>Floor plans, reservations, table transfers, merges.</p></div></div>
            <div class="fcard" data-cat="restaurant"><div class="fcard-icon"><i class="fas fa-concierge-bell"></i></div><div><h4>Kitchen Display (KOT)</h4><p>Orders sent straight to kitchen printer or screen.</p></div></div>
            <div class="fcard" data-cat="restaurant"><div class="fcard-icon"><i class="fas fa-receipt"></i></div><div><h4>Split Bills &amp; Tips</h4><p>Split by seat, by item, or even by amount. Tip tracking.</p></div></div>

            {{-- Reports --}}
            <div class="fcard" data-cat="reports"><div class="fcard-icon"><i class="fas fa-chart-bar"></i></div><div><h4>Sales Reports</h4><p>Daily Z-reports, product performance, margin analysis.</p></div></div>
            <div class="fcard" data-cat="reports"><div class="fcard-icon"><i class="fas fa-chart-pie"></i></div><div><h4>Financial Reports</h4><p>P&amp;L, cashflow, balance sheet, tax summaries.</p></div></div>
            <div class="fcard" data-cat="reports"><div class="fcard-icon"><i class="fas fa-tachometer-alt"></i></div><div><h4>Live Dashboard</h4><p>Owner dashboard with real-time KPIs from any device.</p></div></div>

            {{-- Comms --}}
            <div class="fcard" data-cat="comms"><div class="fcard-icon"><i class="fas fa-sms"></i></div><div><h4>SMS Notifications</h4><p>Order updates, promos, reminders, balance alerts.</p></div></div>
            <div class="fcard" data-cat="comms"><div class="fcard-icon"><i class="fas fa-envelope"></i></div><div><h4>Email Invoices</h4><p>Auto-send invoices and statements from the system.</p></div></div>
            <div class="fcard" data-cat="comms"><div class="fcard-icon"><i class="fab fa-whatsapp"></i></div><div><h4>WhatsApp Receipts</h4><p>One-click share of receipts via WhatsApp.</p></div></div>

            {{-- Compliance --}}
            <div class="fcard" data-cat="compliance"><div class="fcard-icon">🇰🇪</div><div><h4>KRA eTIMS</h4><p>Automatic invoice sync to KRA eTIMS. Compliant out the box.</p></div></div>
            <div class="fcard" data-cat="compliance"><div class="fcard-icon"><i class="fas fa-user-shield"></i></div><div><h4>Role-Based Access</h4><p>Fine-grained permissions for cashier, manager, owner.</p></div></div>
            <div class="fcard" data-cat="compliance"><div class="fcard-icon"><i class="fas fa-shield-alt"></i></div><div><h4>Audit Trail</h4><p>Every change logged with user, time, before/after.</p></div></div>
        </div>
    </section>

    {{-- HOW IT WORKS --}}
    <section class="sec" id="how">
        <div class="sec-head">
            <div class="sec-eyebrow">How it works</div>
            <h2>Live in 4 simple steps.</h2>
            <p>Pick what you need, pay for it, and we handle the rest.</p>
        </div>
        <div class="steps">
            <div class="step">
                <h4>Pick your goals</h4>
                <p>Tell us what you want your system to do — selling, stock, pharmacy, restaurant.</p>
            </div>
            <div class="step">
                <h4>Choose features</h4>
                <p>Mix and match modules. See your monthly total update as you go.</p>
            </div>
            <div class="step">
                <h4>Pay &amp; activate</h4>
                <p>Pay via M-Pesa, card or bank transfer. Account created instantly.</p>
            </div>
            <div class="step">
                <h4>We set you up</h4>
                <p>Our team imports your data, configures printers, trains your staff.</p>
            </div>
        </div>
    </section>

    {{-- PRICING TEASER --}}
    <section class="sec">
        <div class="pricing-teaser">
            <h3>Transparent, modular pricing.</h3>
            <p>Start from as little as you need. Scale features up or down any time.</p>
            <div class="teaser-chips">
                <span class="teaser-chip"><i class="fas fa-check"></i> Pay monthly, quarterly or yearly</span>
                <span class="teaser-chip"><i class="fas fa-check"></i> Save up to 20% yearly</span>
                <span class="teaser-chip"><i class="fas fa-check"></i> Cloud or On-Premise</span>
                <span class="teaser-chip"><i class="fas fa-check"></i> No long-term contract</span>
            </div>
            <a href="{{ route('saas.pricing') }}" class="hero-cta">Get Started Free <i class="fas fa-arrow-right"></i></a>
        </div>
    </section>

    {{-- SOCIAL PROOF --}}
    <section class="proof">
        <div class="proof-label">Trusted by growing businesses across Kenya</div>
        <div class="proof-logos">
            <span>Retail</span><span>Pharmacies</span><span>Restaurants</span><span>Clinics</span><span>Distributors</span>
        </div>
    </section>

    {{-- FAQ --}}
    <section class="sec" id="faq">
        <div class="sec-head">
            <div class="sec-eyebrow">Common questions</div>
            <h2>Everything you wanted to ask.</h2>
        </div>
        <div class="faq-wrap">
            <details class="faq"><summary>How is this priced?</summary><div class="faq-body">You pay only for the features you pick. Each module has its own monthly price — add or remove any time. Choose monthly, quarterly (–10%) or yearly (–20%) billing. Full breakdown on the <a href="{{ route('saas.pricing') }}" style="color:#5eead4;">pricing page</a>.</div></details>
            <details class="faq"><summary>Can I test before paying?</summary><div class="faq-body">Yes. Submit your plan and we schedule a demo on your real data before any payment. You only pay after you've seen it work for your specific business.</div></details>
            <details class="faq"><summary>Is my data safe?</summary><div class="faq-body">Cloud hosting is on encrypted Kenyan-region servers with daily backups. Prefer to keep data on-premise? We install locally on your own server — you own everything.</div></details>
            <details class="faq"><summary>Do you support KRA eTIMS?</summary><div class="faq-body">Yes — the KRA eTIMS module is fully compliant and syncs every invoice automatically. Add it to your plan on the pricing page.</div></details>
            <details class="faq"><summary>What happens if I need more features later?</summary><div class="faq-body">Log into your customer portal and toggle any feature on or off. Billing adjusts automatically on your next cycle. No re-installation needed.</div></details>
            <details class="faq"><summary>Do you help with setup?</summary><div class="faq-body">Every plan includes onboarding — we import your existing products/customers, configure receipt printers, and train your team. Usually live within 24–48 hours.</div></details>
        </div>
    </section>

    {{-- FINAL CTA --}}
    <section class="final-cta">
        <h2>Ready to run your business better?</h2>
        <p>Set up your business in under 2 minutes. Start with a free trial — no card needed.</p>
        <a href="{{ route('saas.pricing') }}" class="hero-cta">Start Free Trial <i class="fas fa-arrow-right"></i></a>
    </section>

    <div class="lp-foot">
        &copy; {{ date('Y') }} {{ config('app.name', 'Apex POS') }} ·
        <a href="{{ route('saas.pricing') }}">Pricing</a> · <a href="{{ route('saas.pricing') }}">Register</a> ·
        <a href="#features">Features</a> ·
        <a href="#faq">FAQ</a> ·
        <a href="{{ action([\App\Http\Controllers\Auth\LoginController::class, 'login']) }}">Sign In</a>
    </div>

</div>

<script>
// Feature category filter
document.querySelectorAll('.cat-pill').forEach(pill => {
    pill.addEventListener('click', () => {
        document.querySelectorAll('.cat-pill').forEach(p => p.classList.remove('active'));
        pill.classList.add('active');
        const cat = pill.dataset.cat;
        document.querySelectorAll('.fcard').forEach(card => {
            card.style.display = (cat === 'all' || card.dataset.cat === cat) ? 'flex' : 'none';
        });
    });
});
</script>
@endsection
