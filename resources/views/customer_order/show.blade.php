<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Order from {{ $business->name ?? 'Our Store' }}</title>
    <meta name="theme-color" content="#3c3185">

    {{-- LOCAL Bootstrap — same vendor.css the main app uses (passes CSP 'self') --}}
    <link rel="stylesheet" href="{{ asset('css/vendor.css') }}">

    {{-- Font Awesome via jsdelivr.net (allowed in the app's CSP) --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5.15.4/css/all.min.css">

    <style>
        /* ── Tokens ───────────────────────────────────────────────────────── */
        :root {
            --pri:    #3c3185;
            --pri-dk: #2a2160;
            --acc:    #6a0dad;
            --grad:   linear-gradient(135deg, #3c3185 0%, #6a0dad 100%);
            --green:  #27ae60;
            --red:    #e74c3c;
            --orange: #e67e22;
            --txt:    #2c2c3e;
            --muted:  #7f8c9a;
            --bord:   #e1e4ed;
            --card:   #fff;
            --bg:     #f0f2f8;
        }
        *, *::before, *::after { box-sizing: border-box; }
        body { background: var(--bg); font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; }

        /* ── Header ──────────────────────────────────────────────────────── */
        .co-hdr {
            background: var(--grad); color: #fff;
            position: sticky; top: 0; z-index: 200;
            box-shadow: 0 2px 12px rgba(60,49,133,.35);
        }
        .co-hdr-inner {
            display: flex; align-items: center; gap: 12px;
            padding: 11px 16px; max-width: 1140px; margin: 0 auto;
        }
        .co-logo {
            width: 38px; height: 38px; border-radius: 10px;
            background: rgba(255,255,255,.18);
            display: flex; align-items: center; justify-content: center;
            font-size: 1.15rem; flex-shrink: 0;
        }
        .co-hdr-txt { flex: 1; min-width: 0; }
        .co-store    { font-weight: 700; font-size: .94rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .co-sub      { font-size: .72rem; opacity: .82; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .co-cart-btn {
            background: rgba(255,255,255,.18); border: none; border-radius: 9px;
            color: #fff; padding: 8px 13px; font-size: .85rem; font-weight: 600;
            cursor: pointer; flex-shrink: 0; position: relative; transition: background .15s;
        }
        .co-cart-btn:hover { background: rgba(255,255,255,.3); }
        .cart-hdr-badge {
            position: absolute; top: -5px; right: -5px;
            background: var(--red); color: #fff; border-radius: 50%;
            width: 18px; height: 18px; font-size: .62rem; font-weight: 700;
            display: none; align-items: center; justify-content: center;
        }

        /* ── Step bar (mobile only) ───────────────────────────────────────── */
        .step-bar {
            background: #fff; border-bottom: 1px solid var(--bord);
            display: flex;
        }
        .step-tab {
            flex: 1; padding: 9px 4px; text-align: center;
            font-size: .7rem; font-weight: 600; color: var(--muted);
            border-bottom: 3px solid transparent; transition: all .2s;
        }
        .step-tab i { display: block; font-size: .9rem; margin-bottom: 2px; }
        .step-tab.active { color: var(--pri); border-bottom-color: var(--pri); }
        .step-tab.done   { color: var(--green); }

        /* ── Layout ───────────────────────────────────────────────────────── */
        .co-wrap {
            max-width: 1140px; margin: 0 auto;
            padding: 16px 14px 100px;
        }
        /* Desktop: two columns */
        @media (min-width: 900px) {
            .co-wrap { display: flex; gap: 22px; align-items: flex-start; padding-bottom: 30px; }
            .col-left  { flex: 1.4; min-width: 0; }
            .col-right { width: 370px; flex-shrink: 0; }
            .step-bar  { display: none; }
            .mob-only  { display: none !important; }
        }
        /* Mobile: step sections */
        @media (max-width: 899px) {
            .col-right  { display: none; }
            .desk-only  { display: none !important; }
        }

        /* ── Search ───────────────────────────────────────────────────────── */
        .search-wrap-outer { position: relative; margin-bottom: 12px; }
        .search-row {
            display: flex; align-items: center; gap: 0;
            background: #fff; border: 2px solid var(--bord);
            border-radius: 11px; padding: 0 13px;
            transition: border-color .15s;
        }
        .search-row.open { border-bottom-left-radius: 0; border-bottom-right-radius: 0; border-color: var(--pri); }
        .search-row:focus-within { border-color: var(--pri); box-shadow: 0 0 0 3px rgba(60,49,133,.1); }
        .search-row.open:focus-within { box-shadow: none; }
        .search-row i.fa-search { color: var(--muted); margin-right: 8px; flex-shrink: 0; }
        #prod_search {
            flex: 1; border: none; outline: none; background: transparent;
            padding: 11px 0; font-size: .93rem; color: var(--txt);
        }
        .clr-btn { color: var(--muted); cursor: pointer; display: none; flex-shrink: 0; }
        .clr-btn:hover { color: var(--red); }
        .no-match { display: none; text-align: center; padding: 26px 0; color: var(--muted); font-size: .88rem; }

        /* ── Autocomplete dropdown ────────────────────────────────────────── */
        .search-dropdown {
            display: none;
            position: absolute; left: 0; right: 0; top: 100%;
            background: #fff;
            border: 2px solid var(--pri); border-top: none;
            border-bottom-left-radius: 11px; border-bottom-right-radius: 11px;
            box-shadow: 0 8px 24px rgba(60,49,133,.15);
            z-index: 300;
            max-height: 340px; overflow-y: auto;
        }
        .search-dropdown.open { display: block; }
        .dd-item {
            display: flex; align-items: center; gap: 11px;
            padding: 10px 14px; cursor: pointer;
            border-bottom: 1px solid #f5f5f5;
            transition: background .12s;
        }
        .dd-item:last-child { border-bottom: none; }
        .dd-item:hover, .dd-item.focused { background: #f4f1ff; }
        .dd-item:active { background: #ede9ff; }
        .dd-thumb {
            width: 40px; height: 40px; border-radius: 8px;
            object-fit: cover; flex-shrink: 0; background: #eee;
        }
        .dd-thumb-ph {
            width: 40px; height: 40px; border-radius: 8px; flex-shrink: 0;
            background: linear-gradient(135deg,#ede9ff,#ddd6fe);
            display: flex; align-items: center; justify-content: center;
            color: var(--pri); font-size: 1rem;
        }
        .dd-info  { flex: 1; min-width: 0; }
        .dd-name  { font-weight: 600; font-size: .88rem; color: var(--txt); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .dd-var   { font-size: .72rem; color: var(--muted); margin-top: 1px; }
        .dd-price { font-weight: 700; color: var(--pri); font-size: .95rem; flex-shrink: 0; white-space: nowrap; }
        .dd-add-btn {
            background: var(--grad); color: #fff; border: none;
            border-radius: 7px; width: 28px; height: 28px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1rem; font-weight: 700; flex-shrink: 0;
            box-shadow: 0 2px 5px rgba(60,49,133,.25);
        }
        .dd-empty { padding: 18px; text-align: center; color: var(--muted); font-size: .85rem; }
        .dd-header { padding: 7px 14px 4px; font-size: .68rem; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; color: var(--muted); border-bottom: 1px solid #f0f0f0; }

        /* ── Product cards ────────────────────────────────────────────────── */
        .prod-card {
            background: var(--card); border-radius: 11px;
            box-shadow: 0 1px 5px rgba(0,0,0,.07);
            padding: 11px 13px; margin-bottom: 8px;
            display: flex; align-items: center; gap: 11px;
            cursor: pointer; transition: box-shadow .15s;
            border-left: 3px solid transparent;
        }
        .prod-card:hover { box-shadow: 0 4px 14px rgba(60,49,133,.13); }
        .prod-card.in-cart { border-left-color: var(--pri); }
        .prod-img {
            width: 50px; height: 50px; border-radius: 9px;
            object-fit: cover; flex-shrink: 0; background: #eee;
        }
        .prod-ph {
            width: 50px; height: 50px; border-radius: 9px; flex-shrink: 0;
            background: linear-gradient(135deg,#ede9ff,#ddd6fe);
            display: flex; align-items: center; justify-content: center;
            color: var(--pri); font-size: 1.2rem;
        }
        .prod-info  { flex: 1; min-width: 0; }
        .prod-name  { font-weight: 600; font-size: .88rem; color: var(--txt); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .prod-var   { font-size: .72rem; color: var(--muted); margin-top: 1px; }
        .prod-price { font-weight: 700; color: var(--pri); font-size: .94rem; white-space: nowrap; flex-shrink: 0; }
        .in-cart-badge {
            background: var(--pri); color: #fff; border-radius: 50%;
            width: 21px; height: 21px; font-size: .65rem; font-weight: 700;
            display: none; align-items: center; justify-content: center; flex-shrink: 0;
        }
        .prod-card.in-cart .in-cart-badge { display: flex; }
        .btn-add {
            background: var(--grad); color: #fff; border: none;
            border-radius: 8px; width: 31px; height: 31px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.1rem; font-weight: 700; cursor: pointer; flex-shrink: 0;
            box-shadow: 0 2px 6px rgba(60,49,133,.28); transition: opacity .15s;
        }
        .btn-add:hover { opacity: .85; }
        .no-prods { text-align: center; padding: 46px 0; color: var(--muted); }
        .no-prods i { font-size: 2rem; opacity: .25; display: block; margin-bottom: 9px; }

        /* ── Card wrapper ─────────────────────────────────────────────────── */
        .ui-card { background: var(--card); border-radius: 11px; box-shadow: 0 1px 5px rgba(0,0,0,.07); padding: 16px; margin-bottom: 13px; }
        .card-ttl { font-weight: 700; font-size: .8rem; text-transform: uppercase; letter-spacing: .5px; color: var(--muted); margin-bottom: 11px; display: flex; align-items: center; justify-content: space-between; }
        .card-ttl .badge-cnt { background: var(--pri); color: #fff; border-radius: 20px; padding: 1px 9px; font-size: .7rem; font-weight: 700; }

        /* ── Cart rows ────────────────────────────────────────────────────── */
        .ci-row { display: flex; align-items: center; gap: 9px; padding: 8px 0; border-bottom: 1px solid #f5f5f5; }
        .ci-row:last-of-type { border-bottom: none; }
        .ci-info  { flex: 1; min-width: 0; }
        .ci-name  { font-weight: 600; font-size: .84rem; color: var(--txt); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .ci-var   { font-size: .7rem; color: var(--muted); }
        .ci-sub   { font-size: .78rem; color: var(--pri); font-weight: 600; }
        .qty-ctrl { display: flex; align-items: center; gap: 3px; flex-shrink: 0; }
        .qty-btn  { width: 27px; height: 27px; border-radius: 7px; border: 1.5px solid var(--bord); background: #f8f8ff; color: var(--pri); font-weight: 700; font-size: .95rem; cursor: pointer; display: flex; align-items: center; justify-content: center; }
        .qty-btn:hover { background: #ede9ff; }
        .qty-num  { width: 28px; text-align: center; font-weight: 700; font-size: .9rem; }
        .ci-del   { background: none; border: none; color: #ccc; cursor: pointer; font-size: .9rem; padding: 0 3px; }
        .ci-del:hover { color: var(--red); }
        .cart-empty-msg { text-align: center; padding: 28px 0; color: var(--muted); font-size: .88rem; }
        .cart-empty-msg i { font-size: 1.8rem; opacity: .22; display: block; margin-bottom: 7px; }
        .total-row { display: flex; justify-content: space-between; align-items: center; padding: 11px 0 4px; font-weight: 700; font-size: 1rem; border-top: 2px solid var(--bord); margin-top: 7px; }
        .total-amt { color: var(--pri); font-size: 1.15rem; }

        /* ── Summary lines ───────────────────────────────────────────────── */
        .sum-line { display: flex; justify-content: space-between; font-size: .82rem; padding: 5px 0; border-bottom: 1px solid #f5f5f5; color: var(--muted); }
        .sum-line .sn { color: var(--txt); font-weight: 500; }
        .sum-total { display: flex; justify-content: space-between; align-items: center; padding: 10px 0 2px; font-weight: 700; font-size: 1rem; }
        .sum-total-amt { color: var(--pri); font-size: 1.2rem; }

        /* ── M-Pesa form ─────────────────────────────────────────────────── */
        .mpesa-badge { display: inline-flex; align-items: center; gap: 6px; background: linear-gradient(135deg,#009A44,#00B33C); color: #fff; border-radius: 7px; padding: 5px 13px; font-weight: 700; font-size: .82rem; margin-bottom: 12px; }
        .f-label { font-size: .8rem; font-weight: 600; color: var(--txt); margin-bottom: 5px; }
        .phone-wrap { display: flex; border: 2px solid var(--bord); border-radius: 9px; overflow: hidden; transition: border-color .15s; }
        .phone-wrap:focus-within { border-color: var(--pri); box-shadow: 0 0 0 3px rgba(60,49,133,.1); }
        .phone-pfx { background: #f0f2f8; padding: 11px 12px; border-right: 1.5px solid var(--bord); font-weight: 700; font-size: .88rem; color: var(--txt); }
        .phone-inp { flex: 1; border: none; outline: none; padding: 11px 12px; font-size: .95rem; color: var(--txt); background: transparent; }
        .phone-hint { font-size: .72rem; color: var(--muted); margin-top: 4px; }
        .notes-inp { width: 100%; border: 2px solid var(--bord); border-radius: 9px; padding: 9px 12px; font-size: .85rem; resize: none; outline: none; color: var(--txt); transition: border-color .15s; }
        .notes-inp:focus { border-color: var(--pri); box-shadow: 0 0 0 3px rgba(60,49,133,.1); }

        .btn-pay {
            width: 100%; background: linear-gradient(135deg,#009A44,#00B33C);
            color: #fff; border: none; border-radius: 11px; padding: 14px;
            font-weight: 700; font-size: .96rem; cursor: pointer; margin-top: 8px;
            display: flex; align-items: center; justify-content: center; gap: 8px;
            box-shadow: 0 4px 14px rgba(0,154,68,.3); transition: opacity .15s;
        }
        .btn-pay:hover    { opacity: .87; }
        .btn-pay:disabled { opacity: .5; cursor: not-allowed; }

        /* ── Mobile section back row ─────────────────────────────────────── */
        .back-row { display: flex; align-items: center; gap: 9px; margin-bottom: 12px; }
        .btn-back { background: #fff; border: 1.5px solid var(--bord); border-radius: 8px; padding: 6px 13px; font-size: .82rem; font-weight: 600; color: var(--pri); cursor: pointer; }
        .sec-ttl  { font-weight: 700; font-size: .97rem; color: var(--txt); }

        /* ── Mobile sections ─────────────────────────────────────────────── */
        @media (max-width: 899px) {
            .msec { display: none; }
            .msec.active { display: block; }
        }

        /* ── Status view ─────────────────────────────────────────────────── */
        .status-wrap { min-height: 55vh; display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; padding: 28px 20px; }
        .spinner-ring { width: 62px; height: 62px; border: 5px solid #ede9ff; border-top-color: var(--pri); border-radius: 50%; animation: spin .85s linear infinite; margin: 0 auto 17px; }
        @keyframes spin { to { transform: rotate(360deg); } }
        .st-circle { width: 84px; height: 84px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2.3rem; margin: 0 auto 17px; }
        .st-circle.paid   { background: #e8f8ef; color: var(--green); border: 3px solid #a3e6c1; }
        .st-circle.failed { background: #fdf0ef; color: var(--red);   border: 3px solid #f5b4b0; }
        .st-ttl  { font-size: 1.3rem; font-weight: 700; color: var(--txt); margin-bottom: 7px; }
        .st-msg  { font-size: .88rem; color: var(--muted); line-height: 1.55; max-width: 310px; }
        .st-rcpt { margin-top: 10px; background: #e8f8ef; color: var(--green); border-radius: 8px; padding: 8px 18px; font-weight: 700; font-size: .86rem; }
        .btn-new { margin-top: 20px; background: var(--grad); color: #fff; border: none; border-radius: 10px; padding: 11px 28px; font-weight: 700; font-size: .9rem; cursor: pointer; box-shadow: 0 3px 12px rgba(60,49,133,.26); }

        /* ── Sticky bottom bar (mobile) ──────────────────────────────────── */
        .bot-bar { position: fixed; bottom: 0; left: 0; right: 0; background: #fff; border-top: 1px solid var(--bord); padding: 10px 16px; display: flex; align-items: center; gap: 10px; z-index: 150; box-shadow: 0 -4px 20px rgba(0,0,0,.09); }
        .bb-tot   { flex: 1; }
        .bb-lbl   { font-size: .7rem; color: var(--muted); }
        .bb-amt   { font-size: 1.04rem; font-weight: 700; color: var(--pri); }
        .btn-bar  { background: var(--grad); color: #fff; border: none; border-radius: 10px; padding: 11px 18px; font-weight: 700; font-size: .88rem; cursor: pointer; white-space: nowrap; box-shadow: 0 3px 12px rgba(60,49,133,.3); transition: opacity .15s; }
        .btn-bar:hover    { opacity: .88; }
        .btn-bar:disabled { opacity: .5; cursor: not-allowed; }

        /* ── Toast ───────────────────────────────────────────────────────── */
        #toast_box { position: fixed; top: 68px; left: 50%; transform: translateX(-50%); z-index: 9999; width: 92%; max-width: 360px; }
        .t-msg { color: #fff; border-radius: 9px; padding: 11px 14px; margin-bottom: 7px; font-size: .84rem; box-shadow: 0 4px 14px rgba(0,0,0,.17); display: flex; align-items: center; gap: 9px; animation: tIn .22s ease; }
        .t-msg.success { background: var(--green); }
        .t-msg.danger  { background: var(--red); }
        .t-msg.warning { background: var(--orange); }
        @keyframes tIn { from{opacity:0;transform:translateY(-8px)} to{opacity:1;transform:translateY(0)} }

        /* ── Desktop sticky sidebar ───────────────────────────────────────── */
        @media (min-width: 900px) {
            .col-right { position: sticky; top: 72px; }
        }
    </style>
</head>
<body>

{{-- ── Top header ──────────────────────────────────────────────────────────── --}}
<div class="co-hdr">
    <div class="co-hdr-inner">
        <div class="co-logo"><i class="fas fa-store"></i></div>
        <div class="co-hdr-txt">
            <div class="co-store">{{ $business->name ?? 'Our Store' }}</div>
            <div class="co-sub">Hi {{ $contact->name }} — place your order below</div>
        </div>
        <button class="co-cart-btn" id="hdr_cart_btn" onclick="mobileGoTo('cart')">
            <i class="fas fa-shopping-cart"></i>
            <span class="cart-hdr-badge" id="hdr_badge">0</span>
        </button>
    </div>
</div>

{{-- ── Mobile step bar ─────────────────────────────────────────────────────── --}}
<div class="step-bar">
    <div class="step-tab active" id="stab_browse"><i class="fas fa-th-large"></i>Browse</div>
    <div class="step-tab"        id="stab_cart">  <i class="fas fa-cart-plus"></i>Cart</div>
    <div class="step-tab"        id="stab_pay">   <i class="fas fa-mobile-alt"></i>Pay</div>
    <div class="step-tab"        id="stab_done">  <i class="fas fa-check-circle"></i>Done</div>
</div>

<div id="toast_box"></div>

{{-- ════════════════════════════════════════════════════════════════════════
     MAIN LAYOUT
════════════════════════════════════════════════════════════════════════ --}}
<div class="co-wrap">

    {{-- ── LEFT: products + mobile steps ─────────────────────────────────── --}}
    <div class="col-left">

        {{-- ── STEP: Browse ──────────────────────────────────────────────── --}}
        <div class="msec active" id="msec_browse">

            <div class="search-wrap-outer" id="search_outer">
                <div class="search-row" id="search_row">
                    <i class="fas fa-search"></i>
                    <input type="text" id="prod_search"
                           placeholder="Search products and add to cart…"
                           autocomplete="off" autocorrect="off" spellcheck="false">
                    <i class="fas fa-times-circle clr-btn" id="search_clr" onclick="clearSearch()"></i>
                </div>
                {{-- Autocomplete dropdown --}}
                <div class="search-dropdown" id="search_dropdown"></div>
            </div>
            <div class="no-match" id="no_match" style="display:none;"></div>

            <div id="product_list">
                @forelse($products as $product)
                    <div class="prod-card"
                         id="pcard_{{ $product->variation_id }}"
                         data-vid="{{ $product->variation_id }}"
                         data-name="{{ $product->product_name }}"
                         data-var="{{ $product->variation_name !== 'DUMMY' ? $product->variation_name : '' }}"
                         data-price="{{ $product->price }}"
                         data-stock="{{ $product->stock }}"
                         data-search="{{ strtolower($product->product_name . ' ' . ($product->variation_name !== 'DUMMY' ? $product->variation_name : '')) }}"
                         onclick="addToCart(this)">

                        @if($product->product_image)
                            <img class="prod-img"
                                 src="{{ asset('storage/' . $product->product_image) }}"
                                 alt="{{ $product->product_name }}" loading="lazy"
                                 onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                            <div class="prod-ph" style="display:none"><i class="fas fa-box-open"></i></div>
                        @else
                            <div class="prod-ph"><i class="fas fa-box-open"></i></div>
                        @endif

                        <div class="prod-info">
                            <div class="prod-name">{{ $product->product_name }}</div>
                            @if($product->variation_name && $product->variation_name !== 'DUMMY')
                                <div class="prod-var">{{ $product->variation_name }}</div>
                            @endif
                        </div>

                        <div class="in-cart-badge" id="iqty_{{ $product->variation_id }}">0</div>
                        <div class="prod-price">KES&nbsp;{{ number_format($product->price, 2) }}</div>
                        <button class="btn-add"
                                onclick="event.stopPropagation();addToCart(document.getElementById('pcard_{{ $product->variation_id }}'))"
                                title="Add">+</button>
                    </div>
                @empty
                    <div class="no-prods">
                        <i class="fas fa-box-open"></i>
                        No products available at this time.
                    </div>
                @endforelse
            </div>
        </div>{{-- /msec_browse --}}

        {{-- ── STEP: Cart (mobile) ────────────────────────────────────────── --}}
        <div class="msec" id="msec_cart">
            <div class="back-row">
                <button class="btn-back" onclick="mobileGoTo('browse')"><i class="fas fa-arrow-left"></i> Browse</button>
                <span class="sec-ttl">Your Order</span>
            </div>
            <div class="ui-card">
                <div id="m_cart_items">
                    <div class="cart-empty-msg" id="m_cart_empty"><i class="fas fa-shopping-cart"></i>Cart is empty — go back and add items.</div>
                </div>
                <div id="m_cart_total_row" style="display:none">
                    <div class="total-row">
                        <span>Total</span>
                        <span class="total-amt" id="m_cart_total_amt">KES 0.00</span>
                    </div>
                </div>
            </div>
        </div>{{-- /msec_cart --}}

        {{-- ── STEP: Pay (mobile) ─────────────────────────────────────────── --}}
        <div class="msec" id="msec_pay">
            <div class="back-row">
                <button class="btn-back" onclick="mobileGoTo('cart')"><i class="fas fa-arrow-left"></i> Cart</button>
                <span class="sec-ttl">Checkout</span>
            </div>

            {{-- Summary --}}
            <div class="ui-card">
                <div class="card-ttl" style="text-transform:none; font-size:.88rem; color:var(--txt);">
                    <span><i class="fas fa-receipt" style="color:var(--pri);margin-right:6px;"></i>Order Summary</span>
                </div>
                <div id="m_sum_lines"></div>
                <div class="sum-total" id="m_sum_total_row">
                    <span>Total to Pay</span>
                    <span class="sum-total-amt" id="m_sum_total">KES 0.00</span>
                </div>
            </div>

            {{-- M-Pesa form (mobile) --}}
            <div class="ui-card">
                <div style="font-weight:700;font-size:.9rem;color:var(--txt);margin-bottom:11px;display:flex;align-items:center;gap:7px;">
                    <i class="fas fa-mobile-alt" style="color:#009A44;"></i> Pay via M-Pesa
                </div>
                <div class="mpesa-badge"><i class="fas fa-check-circle"></i> M-Pesa STK Push</div>
                <div class="f-label">M-Pesa Phone Number</div>
                <div class="phone-wrap">
                    <div class="phone-pfx">+254</div>
                    <input type="tel" class="phone-inp" id="m_mpesa_phone" placeholder="7XX XXX XXX" maxlength="10" inputmode="numeric">
                </div>
                <div class="phone-hint"><i class="fas fa-info-circle"></i> Number registered with M-Pesa (e.g. 0712345678)</div>
                <div style="margin-top:12px;">
                    <div class="f-label">Notes <span style="font-weight:400;color:var(--muted)">(optional)</span></div>
                    <textarea class="notes-inp" id="m_order_notes" rows="2" placeholder="Delivery instructions, special requests…"></textarea>
                </div>
                <button id="m_btn_pay" class="btn-pay" onclick="placeOrder('m_')">
                    <i class="fas fa-lock"></i>
                    <span id="m_btn_pay_lbl">Pay KES 0.00 via M-Pesa</span>
                </button>
            </div>
        </div>{{-- /msec_pay --}}

        {{-- ── STEP: Status (mobile) ──────────────────────────────────────── --}}
        <div class="msec" id="msec_done">
            <div class="status-wrap">
                <div id="m_sv_spinner">
                    <div class="spinner-ring"></div>
                    <div class="st-ttl">Waiting for Payment…</div>
                    <div class="st-msg">Check your phone for an M-Pesa prompt and enter your PIN.</div>
                </div>
                <div id="m_sv_paid" style="display:none;flex-direction:column;align-items:center;width:100%;">
                    <div class="st-circle paid"><i class="fas fa-check"></i></div>
                    <div class="st-ttl">Payment Confirmed!</div>
                    <div class="st-msg">Your M-Pesa payment was received. Your order is being processed.</div>
                    <div class="st-rcpt" id="m_sv_receipt" style="display:none;"></div>
                    <button class="btn-new" onclick="location.reload()"><i class="fas fa-redo"></i> Place Another Order</button>
                </div>
                <div id="m_sv_failed" style="display:none;flex-direction:column;align-items:center;width:100%;">
                    <div class="st-circle failed"><i class="fas fa-times"></i></div>
                    <div class="st-ttl">Payment Not Completed</div>
                    <div class="st-msg" id="m_sv_fail_msg">The payment was not completed. Please try again.</div>
                    <button class="btn-new" onclick="location.reload()" style="margin-top:18px;"><i class="fas fa-redo"></i> Try Again</button>
                </div>
            </div>
        </div>{{-- /msec_done --}}

    </div>{{-- /col-left --}}

    {{-- ── RIGHT: Desktop sidebar ──────────────────────────────────────────── --}}
    <div class="col-right">

        {{-- Desktop cart --}}
        <div class="ui-card">
            <div class="card-ttl">
                <span><i class="fas fa-shopping-cart" style="color:var(--pri);margin-right:5px;"></i> Your Order</span>
                <span class="badge-cnt" id="d_cart_count">0</span>
            </div>
            <div id="d_cart_items">
                <div class="cart-empty-msg" id="d_cart_empty"><i class="fas fa-shopping-cart"></i>Cart is empty</div>
            </div>
            <div id="d_cart_total_row" style="display:none">
                <div class="total-row">
                    <span>Total</span>
                    <span class="total-amt" id="d_cart_total_amt">KES 0.00</span>
                </div>
            </div>
        </div>

        {{-- Desktop checkout (hidden until cart has items) --}}
        <div id="d_checkout" style="display:none;">

            {{-- Summary --}}
            <div class="ui-card">
                <div class="card-ttl" style="text-transform:none;font-size:.88rem;color:var(--txt);">
                    <span><i class="fas fa-receipt" style="color:var(--pri);margin-right:6px;"></i>Order Summary</span>
                </div>
                <div id="d_sum_lines"></div>
                <div class="sum-total">
                    <span>Total to Pay</span>
                    <span class="sum-total-amt" id="d_sum_total">KES 0.00</span>
                </div>
            </div>

            {{-- M-Pesa form (desktop) --}}
            <div class="ui-card">
                <div style="font-weight:700;font-size:.9rem;color:var(--txt);margin-bottom:11px;display:flex;align-items:center;gap:7px;">
                    <i class="fas fa-mobile-alt" style="color:#009A44;"></i> Pay via M-Pesa
                </div>
                <div class="mpesa-badge"><i class="fas fa-check-circle"></i> M-Pesa STK Push</div>
                <div class="f-label">M-Pesa Phone Number</div>
                <div class="phone-wrap">
                    <div class="phone-pfx">+254</div>
                    <input type="tel" class="phone-inp" id="d_mpesa_phone" placeholder="7XX XXX XXX" maxlength="10" inputmode="numeric">
                </div>
                <div class="phone-hint"><i class="fas fa-info-circle"></i> Number registered with M-Pesa (e.g. 0712345678)</div>
                <div style="margin-top:12px;">
                    <div class="f-label">Notes <span style="font-weight:400;color:var(--muted)">(optional)</span></div>
                    <textarea class="notes-inp" id="d_order_notes" rows="2" placeholder="Delivery instructions, special requests…"></textarea>
                </div>
                <button id="d_btn_pay" class="btn-pay" onclick="placeOrder('d_')">
                    <i class="fas fa-lock"></i>
                    <span id="d_btn_pay_lbl">Pay KES 0.00 via M-Pesa</span>
                </button>
            </div>
        </div>{{-- /d_checkout --}}

        {{-- Desktop status (replaces checkout after order) --}}
        <div id="d_status" style="display:none;">
            <div class="ui-card">
                <div class="status-wrap" style="min-height:0;padding:22px 10px;">
                    <div id="d_sv_spinner">
                        <div class="spinner-ring"></div>
                        <div class="st-ttl">Waiting for Payment…</div>
                        <div class="st-msg">Check your phone for an M-Pesa prompt and enter your PIN.</div>
                    </div>
                    <div id="d_sv_paid" style="display:none;flex-direction:column;align-items:center;width:100%;">
                        <div class="st-circle paid"><i class="fas fa-check"></i></div>
                        <div class="st-ttl">Payment Confirmed!</div>
                        <div class="st-msg">Your M-Pesa payment was received.</div>
                        <div class="st-rcpt" id="d_sv_receipt" style="display:none;"></div>
                        <button class="btn-new" onclick="location.reload()"><i class="fas fa-redo"></i> New Order</button>
                    </div>
                    <div id="d_sv_failed" style="display:none;flex-direction:column;align-items:center;width:100%;">
                        <div class="st-circle failed"><i class="fas fa-times"></i></div>
                        <div class="st-ttl">Not Completed</div>
                        <div class="st-msg" id="d_sv_fail_msg">Payment was not completed.</div>
                        <button class="btn-new" onclick="location.reload()" style="margin-top:14px;"><i class="fas fa-redo"></i> Try Again</button>
                    </div>
                </div>
            </div>
        </div>{{-- /d_status --}}

    </div>{{-- /col-right --}}

</div>{{-- /co-wrap --}}

{{-- ── Mobile sticky bottom bar ─────────────────────────────────────────────── --}}
<div class="bot-bar mob-only" id="bot_bar">
    <div class="bb-tot">
        <div class="bb-lbl" id="bb_lbl">Cart is empty</div>
        <div class="bb-amt" id="bb_amt">KES 0.00</div>
    </div>
    <button class="btn-bar" id="bb_btn" onclick="handleBarBtn()" disabled>
        Add Items <i class="fas fa-arrow-right"></i>
    </button>
</div>

{{-- LOCAL jQuery + Bootstrap JS (passes CSP 'self') --}}
<script src="{{ asset('js/vendor.js') }}"></script>

<script>
// ════════════════════════════════════════════════════════════════════════════
var cart = {};           // vid → { name, var, price, qty, stock }
var grandTotal = 0;
var mStep = 'browse';    // browse | cart | pay | done
var pollTimer = null;

// Is desktop?
function isDesk() { return window.innerWidth >= 900; }

// ════════════════════════════════════════════════════════════════════════════
// CART
// ════════════════════════════════════════════════════════════════════════════
function addToCart(el) {
    var $c  = $(el);
    var vid = String($c.data('vid'));
    var name  = $c.data('name');
    var varN  = $c.data('var') || '';
    var price = parseFloat($c.data('price'));
    var stock = parseFloat($c.data('stock'));

    if (cart[vid]) {
        if (cart[vid].qty >= stock) { toast('warning', 'Max stock reached for this item.'); return; }
        cart[vid].qty++;
    } else {
        cart[vid] = { name: name, v: varN, price: price, qty: 1, stock: stock };
    }
    updateBadge(vid);
    recalc();
    renderBoth();
    toast('success', name + ' added to cart.');
}

function changeQty(vid, delta) {
    vid = String(vid);
    if (!cart[vid]) return;
    cart[vid].qty += delta;
    if (cart[vid].qty <= 0)           { delete cart[vid]; updateBadge(vid); }
    else if (cart[vid].qty > cart[vid].stock) { cart[vid].qty = cart[vid].stock; updateBadge(vid); }
    else                               { updateBadge(vid); }
    recalc(); renderBoth();
}

function removeItem(vid) {
    vid = String(vid);
    delete cart[vid]; updateBadge(vid);
    recalc(); renderBoth();
}

function updateBadge(vid) {
    var $card = $('#pcard_' + vid);
    var $b    = $('#iqty_' + vid);
    if (cart[vid] && cart[vid].qty > 0) { $b.text(cart[vid].qty); $card.addClass('in-cart'); }
    else                                 { $card.removeClass('in-cart'); }
}

function recalc() {
    grandTotal = 0; var count = 0;
    $.each(cart, function(v, i) { grandTotal += i.price * i.qty; count += i.qty; });

    // Header badge
    if (count > 0) { $('#hdr_badge').text(count).css('display','flex'); }
    else           { $('#hdr_badge').hide(); }

    // Desktop sidebar
    $('#d_cart_count').text(count);
    if (isDesk()) {
        if (count > 0) { buildSummary('d_'); $('#d_checkout').show(); }
        else           { $('#d_checkout').hide(); }
    }

    // Mobile bottom bar
    updateBotBar();
}

function renderBoth() {
    renderCartInto('m_cart_items', 'm_cart_empty', 'm_cart_total_row', 'm_cart_total_amt');
    renderCartInto('d_cart_items', 'd_cart_empty', 'd_cart_total_row', 'd_cart_total_amt');
}

function renderCartInto(listId, emptyId, totRowId, totAmtId) {
    var $list = $('#' + listId);
    $list.find('.ci-row').remove();
    var keys = Object.keys(cart);
    if (keys.length === 0) { $('#' + emptyId).show(); $('#' + totRowId).hide(); return; }
    $('#' + emptyId).hide();
    keys.forEach(function(vid) {
        var it = cart[vid];
        var vHtml = it.v ? '<div class="ci-var">' + it.v + '</div>' : '';
        $list.append(
            '<div class="ci-row">' +
            '<div class="ci-info"><div class="ci-name">' + it.name + '</div>' + vHtml +
            '<div class="ci-sub">KES ' + fmt(it.price * it.qty) + '</div></div>' +
            '<div class="qty-ctrl">' +
            '<button class="qty-btn" onclick="changeQty(\'' + vid + '\',-1)">−</button>' +
            '<span class="qty-num">' + it.qty + '</span>' +
            '<button class="qty-btn" onclick="changeQty(\'' + vid + '\',1)">+</button>' +
            '</div>' +
            '<button class="ci-del" onclick="removeItem(\'' + vid + '\')"><i class="fas fa-trash-alt"></i></button>' +
            '</div>'
        );
    });
    $('#' + totAmtId).text('KES ' + fmt(grandTotal));
    $('#' + totRowId).show();
}

function buildSummary(px) {
    var $lines = $('#' + px + 'sum_lines');
    $lines.empty();
    $.each(cart, function(vid, it) {
        var vH = it.v ? ' <span style="color:var(--muted);font-size:.7rem;">(' + it.v + ')</span>' : '';
        $lines.append('<div class="sum-line"><span class="sn">' + it.name + vH + ' × ' + it.qty + '</span><span>KES ' + fmt(it.price * it.qty) + '</span></div>');
    });
    $('#' + px + 'sum_total').text('KES ' + fmt(grandTotal));
    $('#' + px + 'btn_pay_lbl').text('Pay KES ' + fmt(grandTotal) + ' via M-Pesa');
}

// ════════════════════════════════════════════════════════════════════════════
// MOBILE BOTTOM BAR
// ════════════════════════════════════════════════════════════════════════════
function updateBotBar() {
    var count = 0; $.each(cart, function(v, i){ count += i.qty; });
    if (mStep === 'browse') {
        if (count > 0) {
            $('#bb_lbl').text(count + ' item' + (count !== 1 ? 's' : '') + ' in cart');
            $('#bb_amt').text('KES ' + fmt(grandTotal));
            $('#bb_btn').prop('disabled', false).html('View Cart <i class="fas fa-arrow-right"></i>');
        } else {
            $('#bb_lbl').text('Cart is empty'); $('#bb_amt').text('KES 0.00');
            $('#bb_btn').prop('disabled', true).html('Add Items <i class="fas fa-arrow-right"></i>');
        }
        $('#bot_bar').show();
    } else if (mStep === 'cart') {
        if (count > 0) {
            $('#bb_lbl').text('Total'); $('#bb_amt').text('KES ' + fmt(grandTotal));
            $('#bb_btn').prop('disabled', false).html('Proceed to Pay <i class="fas fa-arrow-right"></i>');
        } else {
            $('#bb_lbl').text('Cart is empty'); $('#bb_amt').text('KES 0.00');
            $('#bb_btn').prop('disabled', true).html('Add Items First');
        }
        $('#bot_bar').show();
    } else {
        $('#bot_bar').hide();
    }
}

function handleBarBtn() {
    var count = 0; $.each(cart, function(v, i){ count += i.qty; });
    if (mStep === 'browse' && count > 0) { mobileGoTo('cart'); }
    else if (mStep === 'cart' && count > 0) { buildSummary('m_'); mobileGoTo('pay'); }
}

// ════════════════════════════════════════════════════════════════════════════
// MOBILE NAV
// ════════════════════════════════════════════════════════════════════════════
function mobileGoTo(step) {
    if (isDesk()) return;
    mStep = step;
    $('.msec').removeClass('active');
    $('#msec_' + step).addClass('active');
    var steps = ['browse','cart','pay','done'];
    var idx = steps.indexOf(step);
    steps.forEach(function(s, i) {
        var $t = $('#stab_' + s);
        $t.removeClass('active done');
        if (i < idx) $t.addClass('done');
        else if (i === idx) $t.addClass('active');
    });
    updateBotBar();
    window.scrollTo(0, 0);
}

// ════════════════════════════════════════════════════════════════════════════
// SEARCH — AJAX autocomplete dropdown
// ════════════════════════════════════════════════════════════════════════════
var ddFocusIdx  = -1;
var searchTimer = null;
var SEARCH_URL  = '{{ url("/customer-order/" . $token . "/search") }}';

$('#prod_search').on('input', function() {
    var q = $(this).val().trim();
    $('#search_clr').toggle(q.length > 0);
    ddFocusIdx = -1;

    if (q.length < 1) {
        closeDropdown();
        $('.prod-card').show();
        return;
    }

    // Filter product list in real-time (instant, client-side)
    var ql = q.toLowerCase();
    $('.prod-card').each(function() {
        $(this).toggle(String($(this).data('search')).indexOf(ql) !== -1);
    });

    // Debounce AJAX search for dropdown (wait 250ms after user stops typing)
    clearTimeout(searchTimer);
    showDropdownLoading(q);
    searchTimer = setTimeout(function() { doSearch(q); }, 250);
});

function doSearch(q) {
    $.getJSON(SEARCH_URL, { q: q })
        .done(function(products) { buildDropdown(products, q); })
        .fail(function()         { closeDropdown(); });
}

// Keyboard navigation
$('#prod_search').on('keydown', function(e) {
    var $items = $('#search_dropdown .dd-item');
    if (!$items.length) return;
    if (e.key === 'ArrowDown')  { e.preventDefault(); ddFocusIdx = Math.min(ddFocusIdx+1, $items.length-1); $items.removeClass('focused').eq(ddFocusIdx).addClass('focused'); }
    else if (e.key === 'ArrowUp')   { e.preventDefault(); ddFocusIdx = Math.max(ddFocusIdx-1, 0); $items.removeClass('focused').eq(ddFocusIdx).addClass('focused'); }
    else if (e.key === 'Enter') { e.preventDefault(); if (ddFocusIdx >= 0) $items.eq(ddFocusIdx).trigger('click'); else if ($items.length===1) $items.first().trigger('click'); }
    else if (e.key === 'Escape') { closeDropdown(); $(this).blur(); }
});

// Close on outside click
$(document).on('click', function(e) {
    if (!$(e.target).closest('#search_outer').length) closeDropdown();
});

function showDropdownLoading(q) {
    $('#search_dropdown').html(
        '<div class="dd-empty"><i class="fas fa-spinner fa-spin" style="margin-right:6px;color:var(--pri);"></i>Searching for "<strong>' + $('<span>').text(q).html() + '</strong>"…</div>'
    );
    openDropdown();
}

function buildDropdown(products, q) {
    var $dd = $('#search_dropdown');
    $dd.empty();

    if (!products || products.length === 0) {
        $dd.html('<div class="dd-empty"><i class="fas fa-box-open" style="margin-right:6px;"></i>No products found for "<strong>' + $('<span>').text(q).html() + '</strong>"</div>');
        openDropdown(); return;
    }

    $dd.append('<div class="dd-header"><i class="fas fa-shopping-cart" style="margin-right:4px;color:var(--pri);"></i>' +
        products.length + ' result' + (products.length !== 1 ? 's' : '') + ' — tap to add to cart</div>');

    products.forEach(function(p) {
        var vid   = p.variation_id;
        var name  = p.product_name  || '';
        var varN  = (p.variation_name && p.variation_name !== 'DUMMY') ? p.variation_name : '';
        var price = parseFloat(p.price) || 0;
        var img   = p.product_image ? '{{ asset("storage/") }}/' + p.product_image : null;

        // Highlight matched text in name
        var safe = q.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        var hl   = name.replace(new RegExp('(' + safe + ')', 'gi'),
                       '<mark style="background:#e9d5ff;border-radius:3px;padding:0 2px;">$1</mark>');

        var thumbHtml = img
            ? '<img class="dd-thumb" src="' + img + '" loading="lazy" onerror="this.style.display=\'none\';this.nextElementSibling.style.display=\'flex\'">' +
              '<div class="dd-thumb-ph" style="display:none"><i class="fas fa-box-open"></i></div>'
            : '<div class="dd-thumb-ph"><i class="fas fa-box-open"></i></div>';

        var varHtml = varN ? '<div class="dd-var">' + varN + '</div>' : '';

        var $item = $('<div class="dd-item">' +
            thumbHtml +
            '<div class="dd-info"><div class="dd-name">' + hl + '</div>' + varHtml + '</div>' +
            '<div class="dd-price">KES ' + fmt(price) + '</div>' +
            '<div class="dd-add-btn"><i class="fas fa-plus"></i></div>' +
        '</div>');

        // Clicking adds to cart — works whether product card exists in DOM or not
        $item.on('click', function(e) {
            e.stopPropagation();
            var $existing = $('#pcard_' + vid);
            if ($existing.length) {
                // Product card is in DOM — use existing addToCart
                addToCart($existing[0]);
            } else {
                // Product card not in DOM (AJAX-only result) — add directly to cart state
                addToCartDirect(vid, name, varN, price);
            }
            closeDropdown();
            $('#prod_search').val('').trigger('input');
        });

        $dd.append($item);
    });

    openDropdown();
}

// Add to cart from AJAX data (no DOM card required)
function addToCartDirect(vid, name, varN, price) {
    vid = String(vid);
    if (cart[vid]) {
        cart[vid].qty++;
    } else {
        cart[vid] = { name: name, v: varN, price: price, qty: 1, stock: 9999 };
    }
    recalc();
    renderBoth();
    toast('success', name + ' added to cart.');
}

function openDropdown() {
    $('#search_dropdown').addClass('open');
    $('#search_row').addClass('open');
}
function closeDropdown() {
    clearTimeout(searchTimer);
    $('#search_dropdown').removeClass('open');
    $('#search_row').removeClass('open');
    ddFocusIdx = -1;
}
function clearSearch() {
    $('#prod_search').val('');
    closeDropdown();
    $('.prod-card').show();
    $('#search_clr').hide();
    $('#prod_search').focus();
}

// ════════════════════════════════════════════════════════════════════════════
// PLACE ORDER
// ════════════════════════════════════════════════════════════════════════════
function placeOrder(px) {
    if (Object.keys(cart).length === 0) { toast('danger', 'Cart is empty.'); return; }
    var raw = ($('#' + px + 'mpesa_phone').val() || '').replace(/\s/g,'');
    if (raw.replace(/\D/g,'').length < 9) {
        toast('danger', 'Please enter a valid M-Pesa phone number.');
        $('#' + px + 'mpesa_phone').focus(); return;
    }
    var phone = normPhone(raw);
    var items = [];
    $.each(cart, function(vid, it) { items.push({ variation_id: parseInt(vid), qty: it.qty }); });
    var payload = {
        items: items, payment_method: 'mpesa',
        notes: $('#' + px + 'order_notes').val(),
        mpesa_phone: phone,
        _token: '{{ csrf_token() }}'
    };
    var $btn = $('#' + px + 'btn_pay');
    $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Sending prompt…');

    $.ajax({
        url: '{{ url("/customer-order/" . $token) }}',
        method: 'POST', contentType: 'application/json',
        data: JSON.stringify(payload),
        success: function(resp) {
            if (!resp.success) {
                toast('danger', resp.message || 'Failed to place order.');
                $btn.prop('disabled', false).html('<i class="fas fa-lock"></i> <span id="' + px + 'btn_pay_lbl">Pay KES ' + fmt(grandTotal) + ' via M-Pesa</span>');
                return;
            }
            // Show status view
            if (isDesk()) { $('#d_checkout').hide(); $('#d_status').show(); }
            else          { mobileGoTo('done'); }

            if (resp.mpesa_initiated) startPolling(resp.order_id, resp.mpesa_transaction_id, px);
            else showFailed('Order placed but M-Pesa prompt failed. Contact the store to confirm payment.', px);
        },
        error: function(xhr) {
            var msg = 'Server error, please try again.';
            try { msg = JSON.parse(xhr.responseText).message || msg; } catch(e){}
            toast('danger', msg);
            $btn.prop('disabled', false).html('<i class="fas fa-lock"></i> Pay KES ' + fmt(grandTotal) + ' via M-Pesa');
        }
    });
}

function normPhone(p) {
    p = p.replace(/\D/g,'');
    if (p.startsWith('0') && p.length === 10)  return '254' + p.substring(1);
    if (p.startsWith('254') && p.length === 12) return p;
    if (p.length === 9)                          return '254' + p;
    return p;
}

// ════════════════════════════════════════════════════════════════════════════
// POLLING
// ════════════════════════════════════════════════════════════════════════════
function startPolling(orderId, txId, px) {
    var att = 0;
    pollTimer = setInterval(function() {
        if (++att > 36) { clearInterval(pollTimer); showFailed('Payment timed out. Contact the store if you completed payment.', px); return; }
        $.post('{{ url("/customer-order/check-payment") }}', { mpesa_transaction_id: txId, order_id: orderId, _token: '{{ csrf_token() }}' },
            function(r) {
                if (!r.success) return;
                if (r.is_paid)                                    { clearInterval(pollTimer); showPaid(r.receipt_number, px); }
                else if (r.status==='failed'||r.status==='cancelled') { clearInterval(pollTimer); showFailed('Payment was not completed. Reload to try again.', px); }
            }
        ).fail(function(){});
    }, 5000);
}

function svPfx() { return isDesk() ? 'd_' : 'm_'; }

function showPaid(receipt, px) {
    var p = px ? (isDesk() ? 'd_' : 'm_') : svPfx();
    $('#' + p + 'sv_spinner').hide(); $('#' + p + 'sv_failed').hide();
    $('#' + p + 'sv_paid').css('display','flex');
    if (receipt) $('#' + p + 'sv_receipt').text('Receipt: ' + receipt).show();
    toast('success', 'Payment confirmed! ✓');
}

function showFailed(msg, px) {
    var p = px ? (isDesk() ? 'd_' : 'm_') : svPfx();
    $('#' + p + 'sv_spinner').hide(); $('#' + p + 'sv_paid').hide();
    if (msg) $('#' + p + 'sv_fail_msg').text(msg);
    $('#' + p + 'sv_failed').css('display','flex');
}

// ════════════════════════════════════════════════════════════════════════════
// TOAST
// ════════════════════════════════════════════════════════════════════════════
function toast(type, msg) {
    var ic = { success:'check-circle', danger:'exclamation-circle', warning:'exclamation-triangle' };
    var id = 't' + Date.now();
    $('#toast_box').append('<div id="' + id + '" class="t-msg ' + type + '"><i class="fas fa-' + (ic[type]||'info') + '"></i>' + msg + '</div>');
    setTimeout(function(){ $('#' + id).fadeOut(300, function(){ $(this).remove(); }); }, 3000);
}

function fmt(n) { return parseFloat(n).toLocaleString('en-KE',{minimumFractionDigits:2,maximumFractionDigits:2}); }
</script>
</body>
</html>
