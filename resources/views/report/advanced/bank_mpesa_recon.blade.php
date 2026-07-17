@extends('layouts.app')
@section('title', 'Bank / M-Pesa reconciliation')

@section('css')
@include('layouts.partials.page_modern_css')
<style>
.rc-kpi{display:grid;grid-template-columns:repeat(2,1fr);gap:10px;margin-bottom:14px}
@media(min-width:1000px){.rc-kpi{grid-template-columns:repeat(5,1fr)}}
.rc-card{border-radius:12px;padding:12px 14px;color:#fff}.rc-card .v{font-size:17px;font-weight:800}.rc-card .l{font-size:10px;opacity:.9;font-weight:700;text-transform:uppercase;margin-top:4px}
.rc-a{background:linear-gradient(135deg,#2563eb,#1d4ed8)}.rc-b{background:linear-gradient(135deg,#0d9488,#0f766e)}
.rc-c{background:linear-gradient(135deg,#7c3aed,#6d28d9)}.rc-d{background:linear-gradient(135deg,#d97706,#b45309)}
.rc-e{background:linear-gradient(135deg,#dc2626,#b91c1c)}.rc-e.ok{background:linear-gradient(135deg,#059669,#047857)}
.rc-sec{background:#fff;border:1px solid #e2e8f0;border-radius:12px;margin-bottom:14px;overflow:hidden}
.rc-sec h3{margin:0;padding:12px 16px;font-size:13px;font-weight:800;background:#f8fafc;border-bottom:1px solid #e2e8f0}
.rc-sec .body{padding:12px 16px;overflow-x:auto}
table.rc{width:100%;font-size:12px;border-collapse:collapse}table.rc th,table.rc td{padding:7px 8px;border-bottom:1px solid #f1f5f9;text-align:left}
table.rc th{font-size:10px;text-transform:uppercase;color:#64748b}
.badge-m{display:inline-block;padding:2px 7px;border-radius:999px;font-size:10px;font-weight:700;background:#e0e7ff;color:#3730a3}
.rc-note{font-size:12px;color:#64748b;margin-bottom:10px}
</style>
@endsection

@section('content')
@php
    $matchedCount = count($matched ?? []);
    $posU = $posUnmatched ?? collect();
    $mpU = $mpesaUnmatched ?? collect();
    $gapOk = abs((float)($mpesaGap ?? 0)) < 1;
    $varOk = $variance === null || abs((float)$variance) < 1;
@endphp
<div class="page-modern">
    <section class="content-header"></section>
    <div class="pg-banner">
        <div class="pg-banner-inner">
            <div class="pg-banner-title">
                <div class="pg-banner-icon"><i class="fas fa-university"></i></div>
                <div>
                    <h1>Bank / M-Pesa reconciliation</h1>
                    <p class="pg-subtitle">Daily totals · auto-match · unmatched · statement paste · {{ session('business.name') }}</p>
                </div>
            </div>
            <div class="pg-banner-actions no-print">
                <a href="{{ route('reports.hub') }}" class="pg-add-btn" style="background:rgba(255,255,255,.2)!important;color:#fff!important"><i class="fa fa-th"></i> All reports</a>
            </div>
        </div>
    </div>
    <section class="content">
        <form method="get" class="no-print" style="display:flex;flex-wrap:wrap;gap:10px;margin-bottom:14px;align-items:flex-end">
            <div>
                <label style="font-size:12px;font-weight:700;display:block">From</label>
                <input type="date" name="start_date" value="{{ $start }}" class="form-control">
            </div>
            <div>
                <label style="font-size:12px;font-weight:700;display:block">To</label>
                <input type="date" name="end_date" value="{{ $end }}" class="form-control">
            </div>
            <div>
                <label style="font-size:12px;font-weight:700;display:block">Location</label>
                <select name="location_id" class="form-control select2" style="min-width:180px">
                    <option value="">All</option>
                    @foreach($business_locations as $id => $name)
                        @if((string)$id !== '')
                        <option value="{{ $id }}" @if((string)$location_id === (string)$id) selected @endif>{{ $name }}</option>
                        @endif
                    @endforeach
                </select>
            </div>
            <div>
                <label style="font-size:12px;font-weight:700;display:block">POS method</label>
                <select name="method" class="form-control" style="min-width:130px">
                    @foreach(['all'=>'All','cash'=>'Cash','card'=>'Card','cheque'=>'Cheque','bank_transfer'=>'Bank','mpesa'=>'M-Pesa / mobile','custom_pay_1'=>'Custom 1','custom_pay_2'=>'Custom 2','custom_pay_3'=>'Custom 3'] as $k=>$lab)
                        <option value="{{ $k }}" @if(($method ?? 'all') === $k) selected @endif>{{ $lab }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label style="font-size:12px;font-weight:700;display:block">Statement total (optional)</label>
                <input type="text" name="statement_total" value="{{ $statementTotal }}" class="form-control" placeholder="e.g. 125000" style="min-width:120px">
            </div>
            <div style="flex:1;min-width:220px">
                <label style="font-size:12px;font-weight:700;display:block">Paste statement lines (optional)</label>
                <textarea name="statement_lines" class="form-control" rows="2" placeholder="One bank/M-Pesa line per row — amounts are auto-detected">{{ $statementLinesRaw ?? '' }}</textarea>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fa fa-refresh"></i> Reconcile</button>
            <button type="button" class="btn btn-default" onclick="window.print()"><i class="fa fa-print"></i> Print</button>
        </form>
        @includeIf('report.partials.export_toolbar', ['table' => '#recon_export_table', 'title' => 'Payment recon'])

        <div class="rc-kpi">
            <div class="rc-card rc-a"><div class="v">@format_currency($posTotal)</div><div class="l">POS payments</div></div>
            <div class="rc-card rc-b"><div class="v">@format_currency((float)$posMpesaLikeTotal)</div><div class="l">POS mobile-like</div></div>
            <div class="rc-card rc-c"><div class="v">@format_currency($mpesaTotal + $c2bTotal)</div><div class="l">M-Pesa module (+C2B)</div></div>
            <div class="rc-card rc-d"><div class="v">{{ $matchedCount }}</div><div class="l">Auto-matched pairs</div></div>
            <div class="rc-card rc-e {{ ($gapOk && $varOk) ? 'ok' : '' }}">
                <div class="v">
                    @if($variance !== null)
                        @format_currency($variance)
                    @else
                        @format_currency((float)$mpesaGap)
                    @endif
                </div>
                <div class="l">{{ $variance !== null ? 'Statement − POS' : 'M-Pesa − POS mobile' }}</div>
            </div>
        </div>
        <p class="rc-note">
            Match rules: (1) M-Pesa receipt = POS transaction no · (2) amount + same day.
            Unmatched rows need manual check. Paste Safaricom statement lines to sum automatically.
            @if(($statementLinesTotal ?? 0) > 0)
                · Pasted lines total: <strong>@format_currency((float)$statementLinesTotal)</strong> ({{ count($statementLines) }} lines)
            @endif
        </p>

        <div class="rc-sec">
            <h3>Daily POS summary</h3>
            <div class="body">
                <table class="rc">
                    <thead>
                        <tr>
                            <th>Date</th><th>Count</th><th>Cash</th><th>Mobile-like</th><th>Card</th><th>Other</th><th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse(($dailyPos ?? []) as $d)
                        <tr>
                            <td>{{ $d->day }}</td>
                            <td>{{ $d->count }}</td>
                            <td>@format_currency((float)$d->cash)</td>
                            <td>@format_currency((float)$d->mpesa_like)</td>
                            <td>@format_currency((float)$d->card)</td>
                            <td>@format_currency((float)$d->other)</td>
                            <td><strong>@format_currency((float)$d->total)</strong></td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-muted">No daily data</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="rc-sec">
                    <h3>Auto-matched ({{ $matchedCount }})</h3>
                    <div class="body">
                        <table class="rc">
                            <thead><tr><th>How</th><th>POS amount</th><th>M-Pesa</th><th>Receipt / invoice</th></tr></thead>
                            <tbody>
                            @forelse($matched as $m)
                                <tr>
                                    <td><span class="badge-m">{{ $m->how }}</span></td>
                                    <td>@format_currency((float)$m->amount_pos)</td>
                                    <td>@format_currency((float)$m->amount_mpesa)</td>
                                    <td>
                                        {{ $m->mpesa->mpesa_receipt_number ?? '—' }}
                                        · {{ $m->pos->invoice_no ?? $m->pos->transaction_no ?? '—' }}
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-muted">No automatic matches (or no M-Pesa module rows)</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="rc-sec">
                    <h3>M-Pesa module unmatched ({{ $mpU->count() }})</h3>
                    <div class="body">
                        <table class="rc">
                            <thead><tr><th>Receipt</th><th>Amount</th><th>Phone</th><th>When</th></tr></thead>
                            <tbody>
                            @forelse($mpU->take(100) as $m)
                                <tr>
                                    <td>{{ $m->mpesa_receipt_number ?: '—' }}</td>
                                    <td>@format_currency((float)($m->amount ?? 0))</td>
                                    <td>{{ $m->phone ?? '—' }}</td>
                                    <td>{{ $m->updated_at ?? $m->created_at }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-muted">All M-Pesa rows matched or none paid</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="rc-sec">
            <h3>POS payments by method</h3>
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

        @if(($c2bRows ?? collect())->count())
        <div class="rc-sec">
            <h3>M-Pesa C2B ({{ $c2bRows->count() }}) · @format_currency($c2bTotal)</h3>
            <div class="body">
                <table class="rc">
                    <thead><tr><th>Ref</th><th>Amount</th><th>Phone</th><th>When</th></tr></thead>
                    <tbody>
                    @foreach($c2bRows->take(100) as $c)
                        <tr>
                            <td>{{ $c->trans_id ?? $c->BillRefNumber ?? $c->id }}</td>
                            <td>@format_currency((float)($c->amount ?? 0))</td>
                            <td>{{ $c->msisdn ?? '—' }}</td>
                            <td>{{ $c->trans_time ?? $c->created_at ?? '—' }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        @if(($statementLines ?? []) && count($statementLines))
        <div class="rc-sec">
            <h3>Parsed statement lines ({{ count($statementLines) }})</h3>
            <div class="body">
                <table class="rc">
                    <thead><tr><th>#</th><th>Text</th><th>Amount</th></tr></thead>
                    <tbody>
                    @foreach($statementLines as $sl)
                        <tr>
                            <td>{{ $sl['line'] }}</td>
                            <td>{{ $sl['text'] }}</td>
                            <td>@format_currency((float)$sl['amount'])</td>
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot>
                        <tr><td colspan="2"><strong>Total</strong></td><td><strong>@format_currency((float)$statementLinesTotal)</strong></td></tr>
                    </tfoot>
                </table>
            </div>
        </div>
        @endif

        @if(($accountLedger ?? collect())->count())
        <div class="rc-sec">
            <h3>Account ledger movements</h3>
            <div class="body">
                <table class="rc">
                    <thead><tr><th>When</th><th>Account</th><th>Type</th><th>Amount</th><th>Note</th></tr></thead>
                    <tbody>
                    @foreach($accountLedger->take(200) as $a)
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
