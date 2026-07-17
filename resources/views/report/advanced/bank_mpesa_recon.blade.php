@extends('layouts.app')
@section('title', 'Bank / M-Pesa reconciliation')

@section('css')
@include('layouts.partials.page_modern_css')
<style>
.rc-kpi{display:grid;grid-template-columns:repeat(2,1fr);gap:12px;margin-bottom:14px}
@media(min-width:800px){.rc-kpi{grid-template-columns:repeat(4,1fr)}}
.rc-card{border-radius:12px;padding:14px;color:#fff}.rc-card .v{font-size:18px;font-weight:800}.rc-card .l{font-size:11px;opacity:.9;font-weight:700;text-transform:uppercase;margin-top:4px}
.rc-a{background:linear-gradient(135deg,#2563eb,#1d4ed8)}.rc-b{background:linear-gradient(135deg,#0d9488,#0f766e)}
.rc-c{background:linear-gradient(135deg,#7c3aed,#6d28d9)}.rc-d{background:linear-gradient(135deg,#dc2626,#b91c1c)}
.rc-d.ok{background:linear-gradient(135deg,#059669,#047857)}
.rc-sec{background:#fff;border:1px solid #e2e8f0;border-radius:12px;margin-bottom:14px;overflow:hidden}
.rc-sec h3{margin:0;padding:12px 16px;font-size:14px;font-weight:800;background:#f8fafc;border-bottom:1px solid #e2e8f0}
.rc-sec .body{padding:12px 16px;overflow-x:auto}
table.rc{width:100%;font-size:12px;border-collapse:collapse}table.rc th,table.rc td{padding:7px 8px;border-bottom:1px solid #f1f5f9;text-align:left}
table.rc th{font-size:11px;text-transform:uppercase;color:#64748b}
</style>
@endsection

@section('content')
<div class="page-modern">
    <section class="content-header"></section>
    <div class="pg-banner">
        <div class="pg-banner-inner">
            <div class="pg-banner-title">
                <div class="pg-banner-icon"><i class="fas fa-university"></i></div>
                <div>
                    <h1>Bank / M-Pesa reconciliation</h1>
                    <p class="pg-subtitle">POS payments vs M-Pesa logs vs account ledger · {{ session('business.name') }}</p>
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
            'showMethod' => true,
            'extra' => '<div><label style="font-size:12px;font-weight:700;display:block">Bank/M-Pesa statement total</label><input type="text" name="statement_total" value="'.e($statementTotal ?? '').'" class="form-control" placeholder="Optional" style="min-width:140px"></div>'
        ])
        @includeIf('report.partials.export_toolbar', ['table' => '#recon_export_table', 'title' => 'Payment recon'])

        <div class="rc-kpi">
            <div class="rc-card rc-a"><div class="v">@format_currency($posTotal)</div><div class="l">POS payments</div></div>
            <div class="rc-card rc-b"><div class="v">@format_currency($mpesaTotal)</div><div class="l">M-Pesa module</div></div>
            <div class="rc-card rc-c"><div class="v">{{ $posPayments->count() }}</div><div class="l">Payment rows</div></div>
            <div class="rc-card rc-d {{ ($variance === null || abs($variance) < 0.01) ? 'ok' : '' }}">
                <div class="v">
                    @if($variance === null)
                        —
                    @else
                        @format_currency($variance)
                    @endif
                </div>
                <div class="l">Statement − POS</div>
            </div>
        </div>

        <div class="rc-sec">
            <h3>POS totals by method</h3>
            <div class="body">
                <table class="rc">
                    <thead><tr><th>Method</th><th>Count</th><th>Total</th></tr></thead>
                    <tbody>
                    @forelse($posByMethod as $m)
                        <tr>
                            <td>{{ ucfirst(str_replace('_',' ', $m->method ?? 'other')) }}</td>
                            <td>{{ $m->count }}</td>
                            <td>@format_currency((float)$m->total)</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="text-muted">No payments</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="rc-sec">
            <h3>POS payment ledger</h3>
            <div class="body">
                <table class="rc" id="recon_export_table">
                    <thead>
                        <tr>
                            <th>When</th><th>Method</th><th>Amount</th><th>Invoice / ref</th><th>Type</th><th>Txn no</th><th>Cashier</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($posPayments as $p)
                        <tr>
                            <td>{{ $p->paid_on ?: $p->created_at }}</td>
                            <td>{{ $p->method }}</td>
                            <td>@format_currency((float)$p->amount)</td>
                            <td>{{ $p->invoice_no ?: $p->ref_no ?: '—' }}</td>
                            <td>{{ $p->transaction_type ?: '—' }}</td>
                            <td>{{ $p->transaction_no ?: $p->card_transaction_number ?: $p->cheque_number ?: '—' }}</td>
                            <td>{{ $p->cashier ?: '—' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-muted">No rows</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($mpesaRows->count())
        <div class="rc-sec">
            <h3>M-Pesa STK / module log ({{ $mpesaRows->count() }})</h3>
            <div class="body">
                <table class="rc">
                    <thead><tr><th>ID</th><th>Amount</th><th>Status</th><th>Phone / ref</th><th>When</th></tr></thead>
                    <tbody>
                    @foreach($mpesaRows->take(200) as $m)
                        <tr>
                            <td>{{ $m->id }}</td>
                            <td>@format_currency((float)($m->amount ?? $m->trans_amount ?? 0))</td>
                            <td>{{ $m->status ?? '—' }}</td>
                            <td>{{ $m->phone ?? $m->msisdn ?? $m->CheckoutRequestID ?? $m->MerchantRequestID ?? '—' }}</td>
                            <td>{{ $m->transaction_date ?? $m->created_at ?? '—' }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        @if($accountLedger->count())
        <div class="rc-sec">
            <h3>Account ledger movements</h3>
            <div class="body">
                <table class="rc">
                    <thead><tr><th>When</th><th>Account</th><th>Type</th><th>Amount</th><th>Note</th></tr></thead>
                    <tbody>
                    @foreach($accountLedger->take(300) as $a)
                        <tr>
                            <td>{{ $a->operation_date }}</td>
                            <td>{{ $a->account_name }}</td>
                            <td>{{ $a->type }}</td>
                            <td>@format_currency((float)$a->amount)</td>
                            <td>{{ $a->note ?: $a->sub_type }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </section>
</div>
@endsection
