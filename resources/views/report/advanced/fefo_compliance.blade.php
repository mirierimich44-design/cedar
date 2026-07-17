@extends('layouts.app')
@section('title', 'FEFO / batch compliance')

@section('css')
@include('layouts.partials.page_modern_css')
<style>
.ff-sec{background:#fff;border:1px solid #e2e8f0;border-radius:12px;overflow:hidden;margin-bottom:14px}
.ff-sec h3{margin:0;padding:12px 16px;font-size:14px;font-weight:800;background:#f8fafc;border-bottom:1px solid #e2e8f0}
.ff-sec .body{padding:12px 16px;overflow-x:auto}
table.ff{width:100%;font-size:12px;border-collapse:collapse}table.ff th,table.ff td{padding:7px 8px;border-bottom:1px solid #f1f5f9}
table.ff th{font-size:11px;text-transform:uppercase;color:#64748b;text-align:left}
.ff-bad{background:#fef2f2}.ff-badge{display:inline-block;padding:2px 8px;border-radius:999px;font-size:11px;font-weight:700}
.ff-badge.bad{background:#fee2e2;color:#991b1b}.ff-badge.ok{background:#d1fae5;color:#065f46}
.ff-card{border-radius:12px;padding:14px;color:#fff;margin-bottom:14px;display:inline-block;min-width:200px}
.ff-card.bad{background:linear-gradient(135deg,#dc2626,#b91c1c)}.ff-card.ok{background:linear-gradient(135deg,#059669,#047857)}
.ff-card .v{font-size:22px;font-weight:800}
</style>
@endsection

@section('content')
<div class="page-modern">
    <section class="content-header"></section>
    <div class="pg-banner">
        <div class="pg-banner-inner">
            <div class="pg-banner-title">
                <div class="pg-banner-icon"><i class="fas fa-pills"></i></div>
                <div>
                    <h1>FEFO / batch compliance</h1>
                    <p class="pg-subtitle">Flag sales where an older-expiry batch still had free stock · {{ session('business.name') }}</p>
                </div>
            </div>
            <div class="pg-banner-actions no-print">
                <a href="{{ route('reports.hub') }}" class="pg-add-btn" style="background:rgba(255,255,255,.2)!important;color:#fff!important"><i class="fa fa-th"></i> All reports</a>
            </div>
        </div>
    </div>
    <section class="content">
        @include('report.advanced._filters', [
            'allow_all_locations' => true,
            'extra' => '<div><label style="font-size:12px;font-weight:700;display:block">Show</label><select name="only_flagged" class="form-control"><option value="1" '.(($only_flagged??true)?'selected':'').'>Breaches only</option><option value="0" '.(!($only_flagged??true)?'selected':'').'>All sold batches</option></select></div>'
        ])
        @includeIf('report.partials.export_toolbar', ['table' => '#ff_export_table', 'title' => 'FEFO log'])

        <div class="ff-card {{ $breachCount > 0 ? 'bad' : 'ok' }}">
            <div class="v">{{ $breachCount }}</div>
            <div>FEFO breach lines in this list</div>
        </div>

        <div class="ff-sec">
            <h3>Batch sales log</h3>
            <div class="body">
                <table class="ff" id="ff_export_table">
                    <thead>
                        <tr>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Invoice</th>
                            <th>Product</th>
                            <th>Qty</th>
                            <th>Sold lot</th>
                            <th>Sold exp</th>
                            <th>Older free lot</th>
                            <th>Older exp</th>
                            <th>Older free qty</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($rows as $r)
                        @php $bad = !empty($r->fefo_breach); @endphp
                        <tr class="{{ $bad ? 'ff-bad' : '' }}">
                            <td><span class="ff-badge {{ $bad ? 'bad' : 'ok' }}">{{ $bad ? 'BREACH' : 'OK' }}</span></td>
                            <td>{{ $r->transaction_date }}</td>
                            <td>{{ $r->invoice_no }}</td>
                            <td>{{ $r->product_name }} <small class="text-muted">{{ $r->sku ?? '' }}</small></td>
                            <td>{{ $r->sold_qty }}</td>
                            <td>{{ $r->lot_number ?: '—' }}</td>
                            <td>{{ $r->sold_batch_exp ?: '—' }}</td>
                            <td>{{ $r->older_lot ?: '—' }}</td>
                            <td>{{ $r->older_exp ?: '—' }}</td>
                            <td>{{ $r->older_free_qty ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="10" class="text-muted">No batch sales / no breaches for this filter</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <p class="text-muted" style="font-size:12px">Breach = a batch with an earlier expiry still had free quantity at the sale location when a later-expiry batch was sold. Depends on purchase-line mapping (lot tracking).</p>
    </section>
</div>
@endsection
