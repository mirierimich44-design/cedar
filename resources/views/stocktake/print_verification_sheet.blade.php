<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>@lang('Verification Sheet') - {{ $stocktake->ref_no }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 11px; padding: 15px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 3px solid #333; padding-bottom: 15px; }
        .header h1 { font-size: 20px; margin-bottom: 5px; }
        .header h2 { font-size: 16px; font-weight: normal; color: #666; }
        .header h3 { font-size: 14px; background: #28a745; color: #fff; display: inline-block; padding: 5px 20px; margin-top: 10px; }
        .alert-box { background: #fff3cd; border: 2px solid #ffc107; padding: 10px; margin-bottom: 15px; text-align: center; }
        .alert-box strong { color: #856404; }
        .meta-info { display: flex; justify-content: space-between; margin-bottom: 20px; }
        .meta-box { width: 48%; padding: 10px; background: #f5f5f5; border: 1px solid #ddd; }
        .meta-row { margin-bottom: 8px; }
        .meta-row strong { display: inline-block; width: 130px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #333; padding: 6px 8px; text-align: left; }
        th { background: #333; color: #fff; font-size: 10px; text-transform: uppercase; }
        .num { text-align: center; width: 30px; }
        .sku { width: 80px; font-size: 10px; }
        .product { width: auto; }
        .unit { width: 50px; text-align: center; }
        .qty { width: 65px; text-align: right; }
        .variance { width: 65px; text-align: right; }
        .positive { color: #28a745; font-weight: bold; }
        .negative { color: #dc3545; font-weight: bold; }
        .zero { color: #6c757d; }
        .lot { width: 70px; font-size: 10px; }
        .expiry { width: 70px; font-size: 10px; }
        .counter { width: 80px; font-size: 10px; }
        .check-col { width: 40px; text-align: center; }
        .check-box { width: 20px; height: 20px; border: 2px solid #333; display: inline-block; }
        .summary { margin-bottom: 20px; }
        .summary-row { display: flex; gap: 20px; margin-bottom: 10px; }
        .summary-box { flex: 1; padding: 15px; text-align: center; border: 2px solid #ddd; }
        .summary-box.items { border-color: #2196F3; background: #e3f2fd; }
        .summary-box.positive-var { border-color: #4CAF50; background: #e8f5e9; }
        .summary-box.negative-var { border-color: #f44336; background: #ffebee; }
        .summary-box.zero-var { border-color: #9e9e9e; background: #f5f5f5; }
        .summary-box h4 { font-size: 24px; margin-bottom: 5px; }
        .summary-box p { font-size: 11px; color: #666; }
        .verification-section { margin-top: 30px; padding: 20px; background: #f9f9f9; border: 2px solid #333; }
        .verification-section h3 { margin-bottom: 15px; text-align: center; text-transform: uppercase; }
        .signature-grid { display: flex; justify-content: space-between; margin-top: 20px; }
        .signature-box { width: 30%; text-align: center; }
        .signature-line { border-top: 1px solid #333; margin-top: 50px; padding-top: 5px; }
        .footer { margin-top: 20px; text-align: center; font-size: 10px; padding-top: 10px; border-top: 1px solid #ddd; }
        @media print {
            .no-print { display: none; }
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="padding: 15px; background: #f0f0f0; text-align: center; margin-bottom: 20px;">
        <button onclick="window.print()" style="padding: 10px 30px; font-size: 14px; cursor: pointer; background: #4CAF50; color: white; border: none; border-radius: 5px;">
            <strong>🖨️ Print Verification Sheet</strong>
        </button>
        <a href="{{ action([\App\Http\Controllers\StocktakeController::class, 'edit'], $stocktake->id) }}" style="margin-left: 20px; padding: 10px 30px; font-size: 14px; cursor: pointer; background: #2196F3; color: white; border: none; border-radius: 5px; text-decoration: none;">
            ← Back to Stocktake
        </a>
    </div>

    <div class="header">
        <h1>{{ $business->name ?? 'Company Name' }}</h1>
        <h2>STOCK COUNT VERIFICATION SHEET</h2>
        <h3>FOR DATA ENTRY REVIEW</h3>
    </div>

    <div class="alert-box">
        <strong>⚠️ IMPORTANT:</strong> This sheet contains data entered into the system. 
        Please verify each entry against the original count sheets and check the box to confirm accuracy.
    </div>

    <div class="meta-info">
        <div class="meta-box">
            <div class="meta-row"><strong>Reference No:</strong> {{ $stocktake->ref_no }}</div>
            <div class="meta-row"><strong>Location:</strong> {{ $stocktake->location->name ?? '-' }}</div>
            <div class="meta-row"><strong>Status:</strong> {{ ucfirst($stocktake->status) }}</div>
        </div>
        <div class="meta-box">
            <div class="meta-row"><strong>Count Date:</strong> {{ \Carbon::parse($stocktake->transaction_date)->format('d/m/Y H:i') }}</div>
            <div class="meta-row"><strong>Print Date:</strong> {{ now()->format('d/m/Y H:i') }}</div>
            <div class="meta-row"><strong>Total Items:</strong> {{ count($lines) }}</div>
        </div>
    </div>

    @php
        $positiveVar = $lines->where('variance', '>', 0)->count();
        $negativeVar = $lines->where('variance', '<', 0)->count();
        $zeroVar = $lines->where('variance', 0)->count();
    @endphp

    <div class="summary">
        <div class="summary-row">
            <div class="summary-box items">
                <h4>{{ count($lines) }}</h4>
                <p>Total Items Counted</p>
            </div>
            <div class="summary-box positive-var">
                <h4>{{ $positiveVar }}</h4>
                <p>Positive Variance (Gain)</p>
            </div>
            <div class="summary-box negative-var">
                <h4>{{ $negativeVar }}</h4>
                <p>Negative Variance (Loss)</p>
            </div>
            <div class="summary-box zero-var">
                <h4>{{ $zeroVar }}</h4>
                <p>No Variance</p>
            </div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th class="num">#</th>
                <th class="sku">SKU</th>
                <th class="product">Product Name</th>
                <th class="unit">Unit</th>
                <th class="qty">System Qty</th>
                <th class="qty">Counted</th>
                <th class="variance">Variance</th>
                <th class="lot">Lot No.</th>
                <th class="expiry">Expiry</th>
                <th class="counter">Counted By</th>
                <th class="check-col">✓</th>
            </tr>
        </thead>
        <tbody>
            @foreach($lines as $index => $line)
            <tr>
                <td class="num">{{ $index + 1 }}</td>
                <td class="sku">{{ $line->sku ?: $line->sub_sku }}</td>
                <td class="product">
                    {{ $line->product_name }}
                    @if($line->variation_name && $line->variation_name != 'DUMMY')
                        - {{ $line->variation_name }}
                    @endif
                </td>
                <td class="unit">{{ $line->unit_name ?? '-' }}</td>
                <td class="qty">{{ number_format($line->system_qty, 2) }}</td>
                <td class="qty">{{ number_format($line->counted_qty, 2) }}</td>
                <td class="variance @if($line->variance > 0) positive @elseif($line->variance < 0) negative @else zero @endif">
                    @if($line->variance > 0)+@endif{{ number_format($line->variance, 2) }}
                </td>
                <td class="lot">{{ $line->lot_number ?? '-' }}</td>
                <td class="expiry">{{ $line->expiry_date ? \Carbon::parse($line->expiry_date)->format('d/m/Y') : '-' }}</td>
                <td class="counter">{{ $line->counted_by_name ?? '-' }}</td>
                <td class="check-col"><span class="check-box"></span></td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="verification-section">
        <h3>Verification Sign-Off</h3>
        <p style="text-align: center; margin-bottom: 15px;">
            I have reviewed all entries above against the original count sheets and confirm they are accurate.
        </p>
        
        <div class="signature-grid">
            <div class="signature-box">
                <div class="signature-line">
                    Data Entry Clerk<br>
                    <small>Name & Signature</small>
                </div>
            </div>
            <div class="signature-box">
                <div class="signature-line">
                    Verified By<br>
                    <small>Name & Signature</small>
                </div>
            </div>
            <div class="signature-box">
                <div class="signature-line">
                    Approved By<br>
                    <small>Manager Signature</small>
                </div>
            </div>
        </div>

        <div style="margin-top: 20px; display: flex; gap: 20px;">
            <div style="flex: 1;">
                <strong>Discrepancies Found:</strong>
                <div style="border: 1px solid #333; min-height: 60px; margin-top: 5px; padding: 5px;"></div>
            </div>
            <div style="flex: 1;">
                <strong>Corrections Made:</strong>
                <div style="border: 1px solid #333; min-height: 60px; margin-top: 5px; padding: 5px;"></div>
            </div>
        </div>
    </div>

    <div class="footer">
        Stock Count Verification Sheet | Ref: {{ $stocktake->ref_no }} | Printed: {{ now()->format('d/m/Y H:i') }} | Page 1 of 1
    </div>
</body>
</html>
