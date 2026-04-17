@extends('layouts.auth2')
@section('title', config('app.name', 'Reenson Pharmacy'))
@inject('request', 'Illuminate\Http\Request')
@section('content')

<div class="col-md-12 col-sm-12 col-xs-12" style="padding-top: 60px; padding-bottom: 40px;">

    {{-- Hero Section --}}
    <div class="tw-flex tw-flex-col tw-items-center tw-text-center tw-mb-10 tw-px-4">

        {{-- Pharmacy Cross Icon --}}
        <div style="
            width: 80px; height: 80px;
            background: rgba(255,255,255,0.15);
            border: 2px solid rgba(255,255,255,0.3);
            border-radius: 20px;
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 20px;
            backdrop-filter: blur(4px);
        ">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="white" width="48" height="48">
                <path d="M19 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V5a2 2 0 0 0-2-2zm-6 14h-2v-4H7v-2h4V7h2v4h4v2h-4v4z"/>
            </svg>
        </div>

        {{-- App Name --}}
        <h1 style="
            font-size: 2.8rem;
            font-weight: 800;
            color: #ffffff;
            margin: 0 0 8px;
            letter-spacing: -0.5px;
            text-shadow: 0 2px 8px rgba(0,0,0,0.2);
        ">
            {{ config('app.name', 'Reenson Pharmacy') }}
        </h1>

        {{-- Tagline --}}
        <p style="
            font-size: 1.1rem;
            color: rgba(255,255,255,0.85);
            margin: 0 0 6px;
            font-weight: 500;
        ">
            {{ env('APP_TITLE', 'Kenya PPB Compliant Pharmacy Management System') }}
        </p>

        <p style="font-size: 0.85rem; color: rgba(255,255,255,0.6); margin: 0 0 32px;">
            Pharmacy &amp; Poisons Act Cap. 244 &bull; Dangerous Drugs Act Compliance
        </p>

        {{-- CTA Buttons --}}
        <div style="display: flex; gap: 12px; flex-wrap: wrap; justify-content: center;">
            <a href="{{ action([\App\Http\Controllers\Auth\LoginController::class, 'login']) }}"
               style="
                   display: inline-flex; align-items: center; gap: 8px;
                   background: #ffffff;
                   color: #065f46;
                   padding: 12px 32px;
                   border-radius: 50px;
                   font-weight: 700;
                   font-size: 1rem;
                   text-decoration: none;
                   box-shadow: 0 4px 14px rgba(0,0,0,0.2);
                   transition: all 0.2s;
               "
               onmouseover="this.style.background='#f0fdf4'; this.style.boxShadow='0 6px 20px rgba(0,0,0,0.25)'"
               onmouseout="this.style.background='#ffffff'; this.style.boxShadow='0 4px 14px rgba(0,0,0,0.2)'">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/>
                </svg>
                Sign In
            </a>
        </div>
    </div>

    {{-- Feature Cards --}}
    <div class="row" style="max-width: 960px; margin: 0 auto; padding: 0 16px;">

        @php
        $features = [
            [
                'icon' => '<path d="M9 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V5a2 2 0 0 0-2-2h-4"/><path d="M9 3v10l3-3 3 3V3"/>',
                'title' => 'DDA Register',
                'desc'  => 'Kenya PPB controlled substances register — dispense logs, batch tracking &amp; destruction records.',
            ],
            [
                'icon' => '<path d="M16.7 8a3 3 0 0 0-2.7-2h-4a3 3 0 0 0 0 6h4a3 3 0 0 1 0 6h-4a3 3 0 0 1-2.7-2"/><path d="M12 3v3m0 12v3"/>',
                'title' => 'M-Pesa Payments',
                'desc'  => 'STK Push &amp; C2B auto-detection. Real-time payment matching at the POS counter.',
            ],
            [
                'icon' => '<path d="M12 3l8 4.5v9l-8 4.5-8-4.5v-9l8-4.5"/><path d="M12 12l8-4.5"/><path d="M12 12v9"/><path d="M12 12L4 7.5"/>',
                'title' => 'Live Inventory',
                'desc'  => 'Real-time stock balances, batch &amp; expiry tracking, low-stock alerts and dead-stock reports.',
            ],
            [
                'icon' => '<path d="M8 5H6a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-1"/><rect x="8" y="3" width="8" height="4" rx="1"/><path d="M18 14v4h4"/><circle cx="18" cy="18" r="4"/>',
                'title' => 'eTIMS &amp; Reports',
                'desc'  => 'KRA eTIMS integration, profit &amp; loss, daily reconciliation and full financial reporting.',
            ],
        ];
        @endphp

        @foreach($features as $f)
        <div class="col-md-3 col-sm-6 col-xs-12" style="margin-bottom: 16px;">
            <div style="
                background: rgba(255,255,255,0.1);
                border: 1px solid rgba(255,255,255,0.2);
                border-radius: 16px;
                padding: 20px 16px;
                text-align: center;
                backdrop-filter: blur(6px);
                height: 100%;
                transition: background 0.2s;
            "
            onmouseover="this.style.background='rgba(255,255,255,0.18)'"
            onmouseout="this.style.background='rgba(255,255,255,0.1)'">
                <div style="
                    width: 48px; height: 48px;
                    background: rgba(255,255,255,0.2);
                    border-radius: 12px;
                    display: flex; align-items: center; justify-content: center;
                    margin: 0 auto 12px;
                ">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                         fill="none" stroke="white" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        {!! $f['icon'] !!}
                    </svg>
                </div>
                <div style="font-size: 0.95rem; font-weight: 700; color: #ffffff; margin-bottom: 6px;">
                    {{ $f['title'] }}
                </div>
                <div style="font-size: 0.8rem; color: rgba(255,255,255,0.72); line-height: 1.4;">
                    {!! $f['desc'] !!}
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Footer note --}}
    <div class="tw-text-center" style="margin-top: 28px;">
        <p style="font-size: 0.75rem; color: rgba(255,255,255,0.45);">
            &copy; {{ date('Y') }} {{ config('app.name', 'Reenson Pharmacy') }} &bull; Powered by ApexPOS
        </p>
    </div>

</div>

@endsection
