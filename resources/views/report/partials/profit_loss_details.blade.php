{{-- Redesigned Profit & Loss summary (AJAX fragment)
     Loaded into #pl_data_div. Keep #opening_stock_by_sp / #closing_stock_by_sp for stock-by-SP AJAX.
--}}
@php
    $d = $data ?? [];
    $opening = (float) ($d['opening_stock'] ?? 0);
    $closing = (float) ($d['closing_stock'] ?? 0);
    $totalPurchase = (float) ($d['total_purchase'] ?? 0);
    $totalSell = (float) ($d['total_sell'] ?? 0);
    $gross = (float) ($d['gross_profit'] ?? 0);
    $net = (float) ($d['net_profit'] ?? 0);
    $cogs = ($opening + $totalPurchase) - $closing;
    $netPositive = $net >= 0;
    $grossPositive = $gross >= 0;

    $leftModules = $d['left_side_module_data'] ?? [];
    $rightModules = $d['right_side_module_data'] ?? [];
    $grossLabels = $d['gross_profit_label'] ?? [];
    $sellBySubtype = $d['total_sell_by_subtype'] ?? [];
@endphp

<style>
.pl-wrap{padding:4px 0 8px}
.pl-kpi{display:grid;grid-template-columns:repeat(2,1fr);gap:12px;margin-bottom:16px}
@media(min-width:900px){.pl-kpi{grid-template-columns:repeat(4,1fr)}}
.pl-kpi-card{border-radius:14px;padding:16px 18px;color:#fff;position:relative;overflow:hidden;box-shadow:0 4px 14px rgba(0,0,0,.12)}
.pl-kpi-card::after{content:'';position:absolute;top:-24px;right:-24px;width:90px;height:90px;border-radius:50%;background:rgba(255,255,255,.08)}
.pl-kpi-card .lbl{font-size:11px;font-weight:700;letter-spacing:.04em;text-transform:uppercase;opacity:.9;margin:0}
.pl-kpi-card .val{font-size:22px;font-weight:800;margin:6px 0 0;line-height:1.15;font-variant-numeric:tabular-nums}
.pl-kpi-card .hint{font-size:11px;opacity:.85;margin-top:6px}
.pl-kpi-sales{background:linear-gradient(135deg,#2563eb,#1d4ed8)}
.pl-kpi-cogs{background:linear-gradient(135deg,#d97706,#b45309)}
.pl-kpi-gross{background:linear-gradient(135deg,#0d9488,#0f766e)}
.pl-kpi-net-pos{background:linear-gradient(135deg,#059669,#047857)}
.pl-kpi-net-neg{background:linear-gradient(135deg,#dc2626,#b91c1c)}
.pl-stock{display:grid;grid-template-columns:1fr;gap:10px;margin-bottom:16px}
@media(min-width:768px){.pl-stock{grid-template-columns:repeat(3,1fr)}}
.pl-stock-card{background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:14px 16px;box-shadow:0 1px 4px rgba(0,0,0,.04)}
.pl-stock-card h4{margin:0 0 10px;font-size:12px;font-weight:800;text-transform:uppercase;letter-spacing:.04em;color:#64748b}
.pl-stock-row{display:flex;justify-content:space-between;align-items:baseline;gap:8px;padding:5px 0;border-bottom:1px dashed #f1f5f9;font-size:13px}
.pl-stock-row:last-child{border-bottom:0}
.pl-stock-row .k{color:#475569}
.pl-stock-row .v{font-weight:700;color:#0f172a;font-variant-numeric:tabular-nums}
.pl-cols{display:grid;grid-template-columns:1fr;gap:14px;margin-bottom:16px}
@media(min-width:900px){.pl-cols{grid-template-columns:1fr 1fr}}
.pl-panel{background:#fff;border:1px solid #e2e8f0;border-radius:14px;overflow:hidden;box-shadow:0 1px 6px rgba(0,0,0,.05)}
.pl-panel-h{padding:12px 16px;display:flex;align-items:center;gap:10px;color:#fff}
.pl-panel-h.income{background:linear-gradient(135deg,#059669,#047857)}
.pl-panel-h.costs{background:linear-gradient(135deg,#dc2626,#b91c1c)}
.pl-panel-h .ico{width:28px;height:28px;border-radius:8px;background:rgba(255,255,255,.18);display:flex;align-items:center;justify-content:center}
.pl-panel-h h3{margin:0;font-size:14px;font-weight:800}
.pl-panel-b{padding:4px 0}
.pl-line{display:flex;justify-content:space-between;align-items:flex-start;gap:12px;padding:10px 16px;border-bottom:1px solid #f1f5f9;font-size:13px}
.pl-line:last-child{border-bottom:0}
.pl-line .k{color:#334155;flex:1}
.pl-line .k small{display:block;color:#94a3b8;font-size:11px;margin-top:2px}
.pl-line .v{font-weight:700;color:#0f172a;white-space:nowrap;font-variant-numeric:tabular-nums}
.pl-line.total{background:#f8fafc;font-weight:700}
.pl-line.total .k,.pl-line.total .v{color:#0f172a;font-size:14px}
.pl-formula{background:#fff;border:1px solid #e2e8f0;border-radius:14px;padding:16px 18px;margin-bottom:12px;box-shadow:0 1px 4px rgba(0,0,0,.04)}
.pl-formula h3{margin:0 0 10px;font-size:14px;font-weight:800;color:#0f172a}
.pl-formula .box{background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;padding:12px 14px;margin-bottom:10px}
.pl-formula .box:last-child{margin-bottom:0}
.pl-formula .box .t{font-size:12px;font-weight:800;text-transform:uppercase;letter-spacing:.03em;color:#64748b;margin-bottom:4px}
.pl-formula .box .f{font-size:13px;color:#334155;line-height:1.45}
.pl-formula .box .r{margin-top:6px;font-size:14px;font-weight:800;color:#0f172a}
.pl-formula .box .r.pos{color:#047857}
.pl-formula .box .r.neg{color:#b91c1c}
@media print{
  .pl-kpi-card,.pl-panel-h{-webkit-print-color-adjust:exact;print-color-adjust:exact}
}
</style>

<div class="pl-wrap col-xs-12">
    {{-- 1. KPI row --}}
    <div class="pl-kpi">
        <div class="pl-kpi-card pl-kpi-sales">
            <p class="lbl"><i class="fa fa-shopping-cart"></i> Sales</p>
            <div class="val"><span class="display_currency" data-currency_symbol="true">{{ $totalSell }}</span></div>
            <div class="hint">Total sold (exc. tax &amp; discount)</div>
        </div>
        <div class="pl-kpi-card pl-kpi-cogs">
            <p class="lbl"><i class="fa fa-boxes"></i> COGS</p>
            <div class="val"><span class="display_currency" data-currency_symbol="true">{{ $cogs }}</span></div>
            <div class="hint">Opening + purchases − closing</div>
        </div>
        <div class="pl-kpi-card pl-kpi-gross">
            <p class="lbl"><i class="fa fa-chart-area"></i> Gross profit</p>
            <div class="val"><span class="display_currency" data-currency_symbol="true">{{ $gross }}</span></div>
            <div class="hint">Sell price − purchase cost of items sold</div>
        </div>
        <div class="pl-kpi-card {{ $netPositive ? 'pl-kpi-net-pos' : 'pl-kpi-net-neg' }}">
            <p class="lbl"><i class="fa fa-balance-scale"></i> Net profit</p>
            <div class="val"><span class="display_currency" data-currency_symbol="true">{{ $net }}</span></div>
            <div class="hint">{{ $netPositive ? 'Profit after expenses & adjustments' : 'Loss after expenses & adjustments' }}</div>
        </div>
    </div>

    {{-- 3. Stock strip --}}
    <div class="pl-stock">
        <div class="pl-stock-card">
            <h4><i class="fa fa-door-open"></i> Opening stock</h4>
            <div class="pl-stock-row">
                <span class="k">By purchase price</span>
                <span class="v"><span class="display_currency" data-currency_symbol="true">{{ $opening }}</span></span>
            </div>
            <div class="pl-stock-row">
                <span class="k">By sell price</span>
                <span class="v"><span id="opening_stock_by_sp"><i class="fa fa-sync fa-spin fa-fw"></i></span></span>
            </div>
        </div>
        <div class="pl-stock-card">
            <h4><i class="fa fa-truck"></i> Purchases (period)</h4>
            <div class="pl-stock-row">
                <span class="k">Purchases (exc. tax)</span>
                <span class="v"><span class="display_currency" data-currency_symbol="true">{{ $totalPurchase }}</span></span>
            </div>
            <div class="pl-stock-row">
                <span class="k">Purchase returns</span>
                <span class="v"><span class="display_currency" data-currency_symbol="true">{{ $d['total_purchase_return'] ?? 0 }}</span></span>
            </div>
            <div class="pl-stock-row">
                <span class="k">Purchase discount</span>
                <span class="v"><span class="display_currency" data-currency_symbol="true">{{ $d['total_purchase_discount'] ?? 0 }}</span></span>
            </div>
        </div>
        <div class="pl-stock-card">
            <h4><i class="fa fa-door-closed"></i> Closing stock</h4>
            <div class="pl-stock-row">
                <span class="k">By purchase price</span>
                <span class="v"><span class="display_currency" data-currency_symbol="true">{{ $closing }}</span></span>
            </div>
            <div class="pl-stock-row">
                <span class="k">By sell price</span>
                <span class="v"><span id="closing_stock_by_sp"><i class="fa fa-sync fa-spin fa-fw"></i></span></span>
            </div>
        </div>
    </div>

    {{-- 2. Income vs costs --}}
    <div class="pl-cols">
        <div class="pl-panel">
            <div class="pl-panel-h income">
                <span class="ico"><i class="fa fa-plus"></i></span>
                <h3>Income &amp; credits</h3>
            </div>
            <div class="pl-panel-b">
                <div class="pl-line">
                    <div class="k">
                        Total sales
                        <small>Excluding tax &amp; sale discounts</small>
                        @if(is_countable($sellBySubtype) && count($sellBySubtype) > 1)
                            <ul style="margin:6px 0 0;padding-left:16px;font-size:11px;color:#64748b">
                                @foreach($sellBySubtype as $sell)
                                    <li>
                                        <span class="display_currency" data-currency_symbol="true">{{ $sell->total_before_tax ?? 0 }}</span>
                                        @if(!empty($sell->sub_type))
                                            ({{ ucfirst($sell->sub_type) }})
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                    <div class="v"><span class="display_currency" data-currency_symbol="true">{{ $totalSell }}</span></div>
                </div>
                <div class="pl-line">
                    <div class="k">Sell shipping charges<small>Delivery fees charged to customers</small></div>
                    <div class="v"><span class="display_currency" data-currency_symbol="true">{{ $d['total_sell_shipping_charge'] ?? 0 }}</span></div>
                </div>
                <div class="pl-line">
                    <div class="k">Sell additional expense (income side)<small>Extra charges on sales</small></div>
                    <div class="v"><span class="display_currency" data-currency_symbol="true">{{ $d['total_sell_additional_expense'] ?? 0 }}</span></div>
                </div>
                <div class="pl-line">
                    <div class="k">Stock recovered<small>Value recovered from adjustments</small></div>
                    <div class="v"><span class="display_currency" data-currency_symbol="true">{{ $d['total_recovered'] ?? 0 }}</span></div>
                </div>
                <div class="pl-line">
                    <div class="k">Purchase returns</div>
                    <div class="v"><span class="display_currency" data-currency_symbol="true">{{ $d['total_purchase_return'] ?? 0 }}</span></div>
                </div>
                <div class="pl-line">
                    <div class="k">Purchase discounts</div>
                    <div class="v"><span class="display_currency" data-currency_symbol="true">{{ $d['total_purchase_discount'] ?? 0 }}</span></div>
                </div>
                <div class="pl-line">
                    <div class="k">Sell round-off</div>
                    <div class="v"><span class="display_currency" data-currency_symbol="true">{{ $d['total_sell_round_off'] ?? 0 }}</span></div>
                </div>
                @foreach($rightModules as $module_data)
                    <div class="pl-line">
                        <div class="k">{{ $module_data['label'] ?? 'Module' }}</div>
                        <div class="v"><span class="display_currency" data-currency_symbol="true">{{ $module_data['value'] ?? 0 }}</span></div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="pl-panel">
            <div class="pl-panel-h costs">
                <span class="ico"><i class="fa fa-minus"></i></span>
                <h3>Costs &amp; deductions</h3>
            </div>
            <div class="pl-panel-b">
                <div class="pl-line">
                    <div class="k">Purchases<small>Excluding tax &amp; purchase discounts</small></div>
                    <div class="v"><span class="display_currency" data-currency_symbol="true">{{ $totalPurchase }}</span></div>
                </div>
                <div class="pl-line">
                    <div class="k">Stock adjustments<small>Shrinkage / write-offs</small></div>
                    <div class="v"><span class="display_currency" data-currency_symbol="true">{{ $d['total_adjustment'] ?? 0 }}</span></div>
                </div>
                <div class="pl-line">
                    <div class="k">Expenses<small>Operating expenses in this period</small></div>
                    <div class="v"><span class="display_currency" data-currency_symbol="true">{{ $d['total_expense'] ?? 0 }}</span></div>
                </div>
                <div class="pl-line">
                    <div class="k">Purchase shipping</div>
                    <div class="v"><span class="display_currency" data-currency_symbol="true">{{ $d['total_purchase_shipping_charge'] ?? 0 }}</span></div>
                </div>
                <div class="pl-line">
                    <div class="k">Purchase additional expense</div>
                    <div class="v"><span class="display_currency" data-currency_symbol="true">{{ $d['total_purchase_additional_expense'] ?? 0 }}</span></div>
                </div>
                <div class="pl-line">
                    <div class="k">Transfer shipping</div>
                    <div class="v"><span class="display_currency" data-currency_symbol="true">{{ $d['total_transfer_shipping_charges'] ?? 0 }}</span></div>
                </div>
                <div class="pl-line">
                    <div class="k">Sell discounts</div>
                    <div class="v"><span class="display_currency" data-currency_symbol="true">{{ $d['total_sell_discount'] ?? 0 }}</span></div>
                </div>
                <div class="pl-line">
                    <div class="k">Reward points amount</div>
                    <div class="v"><span class="display_currency" data-currency_symbol="true">{{ $d['total_reward_amount'] ?? 0 }}</span></div>
                </div>
                <div class="pl-line">
                    <div class="k">Sell returns</div>
                    <div class="v"><span class="display_currency" data-currency_symbol="true">{{ $d['total_sell_return'] ?? 0 }}</span></div>
                </div>
                @foreach($leftModules as $module_data)
                    <div class="pl-line">
                        <div class="k">{{ $module_data['label'] ?? 'Module' }}</div>
                        <div class="v"><span class="display_currency" data-currency_symbol="true">{{ $module_data['value'] ?? 0 }}</span></div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- 4. Plain-language formulas --}}
    <div class="pl-formula">
        <h3><i class="fa fa-info-circle"></i> How these numbers are calculated</h3>
        <div class="box">
            <div class="t">COGS (cost of goods sold)</div>
            <div class="f">Opening stock (purchase price) + purchases in period − closing stock (purchase price)</div>
            <div class="r">
                Result:
                <span class="display_currency" data-currency_symbol="true">{{ $cogs }}</span>
            </div>
        </div>
        <div class="box">
            <div class="t">Gross profit</div>
            <div class="f">
                Profit on items sold: sell price − purchase cost of those items
                @if(!empty($grossLabels))
                    @foreach($grossLabels as $val)
                        + {{ $val }}
                    @endforeach
                @endif
            </div>
            <div class="r {{ $grossPositive ? 'pos' : 'neg' }}">
                Result:
                <span class="display_currency" data-currency_symbol="true">{{ $gross }}</span>
            </div>
        </div>
        <div class="box">
            <div class="t">Net profit</div>
            <div class="f">
                Gross profit
                + sell shipping + sell extra charges + stock recovered + purchase discounts + round-off
                @foreach($rightModules as $module_data)
                    @if(!empty($module_data['add_to_net_profit']))
                        + {{ $module_data['label'] }}
                    @endif
                @endforeach
                − stock adjustments − expenses − purchase shipping − transfer shipping − purchase extra − sell discounts − rewards
                @foreach($leftModules as $module_data)
                    @if(!empty($module_data['add_to_net_profit']))
                        − {{ $module_data['label'] }}
                    @endif
                @endforeach
            </div>
            <div class="r {{ $netPositive ? 'pos' : 'neg' }}">
                Result:
                <span class="display_currency" data-currency_symbol="true">{{ $net }}</span>
            </div>
        </div>
    </div>

    {{-- Hidden export table for Print / Excel / PDF toolbar --}}
    <table id="pl_export_table" class="table table-bordered" style="display:none" aria-hidden="true">
        <thead>
            <tr>
                <th>Metric</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            <tr><td>Sales (exc. tax)</td><td>{{ number_format($totalSell, 2, '.', '') }}</td></tr>
            <tr><td>COGS</td><td>{{ number_format($cogs, 2, '.', '') }}</td></tr>
            <tr><td>Gross profit</td><td>{{ number_format($gross, 2, '.', '') }}</td></tr>
            <tr><td>Net profit</td><td>{{ number_format($net, 2, '.', '') }}</td></tr>
            <tr><td>Opening stock (purchase price)</td><td>{{ number_format($opening, 2, '.', '') }}</td></tr>
            <tr><td>Purchases (exc. tax)</td><td>{{ number_format($totalPurchase, 2, '.', '') }}</td></tr>
            <tr><td>Closing stock (purchase price)</td><td>{{ number_format($closing, 2, '.', '') }}</td></tr>
            <tr><td>Total expense</td><td>{{ number_format((float)($d['total_expense'] ?? 0), 2, '.', '') }}</td></tr>
            <tr><td>Stock adjustments</td><td>{{ number_format((float)($d['total_adjustment'] ?? 0), 2, '.', '') }}</td></tr>
            <tr><td>Sell returns</td><td>{{ number_format((float)($d['total_sell_return'] ?? 0), 2, '.', '') }}</td></tr>
            <tr><td>Sell discounts</td><td>{{ number_format((float)($d['total_sell_discount'] ?? 0), 2, '.', '') }}</td></tr>
            <tr><td>Purchase discounts</td><td>{{ number_format((float)($d['total_purchase_discount'] ?? 0), 2, '.', '') }}</td></tr>
        </tbody>
    </table>
</div>
