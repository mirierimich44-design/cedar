{{-- Owner control strip on home dashboard --}}
@if(!empty($owner_ops))
@php $o = $owner_ops; @endphp
<div class="no-print" style="padding:12px 16px 0">
    <div style="background:#fff;border:1px solid #e2e8f0;border-radius:14px;padding:14px 16px;box-shadow:0 1px 6px rgba(0,0,0,.05)">
        <div style="display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:10px;margin-bottom:12px">
            <div>
                <strong style="font-size:14px">Owner control</strong>
                <span style="font-size:12px;color:#64748b;margin-left:8px">{{ $o['today'] }}</span>
            </div>
            <div style="display:flex;flex-wrap:wrap;gap:6px">
                <a href="{{ $o['links']['day_close'] }}" class="btn btn-xs btn-primary">Day close</a>
                <a href="{{ $o['links']['month_end'] }}" class="btn btn-xs btn-default">Month-end</a>
                <a href="{{ $o['links']['recon'] }}" class="btn btn-xs btn-default">M-Pesa recon</a>
                <a href="{{ $o['links']['ritual'] }}" class="btn btn-xs btn-default">Ritual</a>
            </div>
        </div>
        <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:10px">
            <div style="background:linear-gradient(135deg,#2563eb,#1d4ed8);color:#fff;border-radius:10px;padding:12px">
                <div style="font-size:11px;opacity:.9;font-weight:700;text-transform:uppercase">Sales today</div>
                <div style="font-size:20px;font-weight:800">@format_currency($o['sales_today'])</div>
                <div style="font-size:11px;opacity:.85">{{ $o['invoices_today'] }} invoices</div>
            </div>
            <div style="background:linear-gradient(135deg,#dc2626,#b91c1c);color:#fff;border-radius:10px;padding:12px">
                <div style="font-size:11px;opacity:.9;font-weight:700;text-transform:uppercase">Credit due</div>
                <div style="font-size:20px;font-weight:800">@format_currency($o['credit_due'])</div>
                <div style="font-size:11px;opacity:.85"><a href="{{ $o['links']['credit'] }}" style="color:#fff;text-decoration:underline">View credit</a></div>
            </div>
            <div style="background:linear-gradient(135deg,#d97706,#b45309);color:#fff;border-radius:10px;padding:12px">
                <div style="font-size:11px;opacity:.9;font-weight:700;text-transform:uppercase">Low / zero stock</div>
                <div style="font-size:20px;font-weight:800">{{ $o['low_stock'] }}</div>
                <div style="font-size:11px;opacity:.85"><a href="{{ $o['links']['reorder'] }}" style="color:#fff;text-decoration:underline">Reorder list</a></div>
            </div>
            <div style="background:linear-gradient(135deg,#059669,#047857);color:#fff;border-radius:10px;padding:12px">
                <div style="font-size:11px;opacity:.9;font-weight:700;text-transform:uppercase">Open tills · Expiry 90d</div>
                <div style="font-size:20px;font-weight:800">{{ $o['open_tills'] }} · {{ $o['expiring_90d'] }}</div>
                <div style="font-size:11px;opacity:.85">Register &amp; batch watch</div>
            </div>
        </div>
    </div>
</div>
@if(request()->is('home') || request()->is('/'))
<style>
@media(min-width:800px){
  .owner-ops-grid-fix{grid-template-columns:repeat(4,1fr)!important}
}
</style>
@endif
@endif
