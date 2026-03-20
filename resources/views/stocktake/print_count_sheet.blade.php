<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Stock Count Sheet - {{ $stocktake->ref_no }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 11px; }
        .page { page-break-after: always; padding: 15px; }
        .page:last-child { page-break-after: avoid; }
        .header { text-align: center; margin-bottom: 15px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .header h1 { font-size: 18px; margin-bottom: 5px; }
        .header h2 { font-size: 14px; font-weight: normal; }
        .meta-info { display: flex; justify-content: space-between; margin-bottom: 15px; padding: 10px; background: #f5f5f5; border: 1px solid #ddd; }
        .meta-left, .meta-right { width: 48%; }
        .meta-row { display: flex; margin-bottom: 8px; }
        .meta-label { font-weight: bold; width: 120px; }
        .meta-value { flex: 1; border-bottom: 1px solid #333; min-width: 150px; }
        .counter-info { margin-bottom: 15px; padding: 10px; border: 2px solid #333; background: #fffef0; }
        .counter-info h3 { margin-bottom: 10px; font-size: 12px; }
        .counter-row { display: flex; gap: 20px; margin-bottom: 8px; }
        .counter-field { flex: 1; display: flex; }
        .counter-field label { font-weight: bold; margin-right: 8px; white-space: nowrap; }
        .counter-field .line { flex: 1; border-bottom: 1px solid #333; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th, td { border: 1px solid #333; padding: 8px 10px; text-align: left; }
        th { background: #333; color: #fff; font-size: 10px; text-transform: uppercase; }
        .num { text-align: center; width: 35px; }
        .product-name { width: auto; }
        .sku { width: 100px; }
        .qty-col { width: 80px; text-align: center; }
        .lot-col { width: 90px; }
        .expiry-col { width: 90px; }
        .input-cell { background: #fffef0; min-height: 28px; }
        .footer { text-align: center; font-size: 10px; padding-top: 10px; border-top: 1px solid #ddd; }
        .sheet-number { font-size: 16px; font-weight: bold; background: #333; color: #fff; padding: 5px 15px; display: inline-block; }
        .instructions { background: #e8f4f8; padding: 8px; margin-bottom: 10px; font-size: 10px; border: 1px solid #4a90a4; }
        .signature-area { margin-top: 20px; display: flex; justify-content: space-between; }
        .signature-box { width: 30%; text-align: center; }
        .signature-line { border-top: 1px solid #333; margin-top: 40px; padding-top: 5px; }
        .total-products { background: #d4edda; border: 1px solid #28a745; padding: 8px; margin-bottom: 10px; font-size: 12px; }
        @media print {
            .no-print { display: none; }
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="padding: 15px; background: #f0f0f0; text-align: center;">
        {{-- Print Type Selector --}}
        <div style="margin-bottom: 15px; background: #fff; display: inline-block; padding: 12px 20px; border-radius: 8px; border: 1px solid #ddd;">
            <strong style="margin-right: 15px;">Print Type:</strong>
            <a href="{{ route('stocktake.printCountSheet', ['id' => $stocktake->id, 'print_type' => 'blank', 'per_sheet' => $rowsPerSheet]) }}"
               style="margin-right: 6px; padding: 6px 16px; border-radius: 4px; text-decoration: none; font-size: 13px;
                      {{ $print_type === 'blank' ? 'background:#333;color:#fff;font-weight:bold;' : 'background:#eee;color:#333;border:1px solid #ccc;' }}">
               Blank Sheet
            </a>
            <a href="{{ route('stocktake.printCountSheet', ['id' => $stocktake->id, 'print_type' => 'with_products', 'per_sheet' => $rowsPerSheet]) }}"
               style="margin-right: 6px; padding: 6px 16px; border-radius: 4px; text-decoration: none; font-size: 13px;
                      {{ $print_type === 'with_products' ? 'background:#333;color:#fff;font-weight:bold;' : 'background:#eee;color:#333;border:1px solid #ccc;' }}">
               All Products (No Qty)
            </a>
            <a href="{{ route('stocktake.printCountSheet', ['id' => $stocktake->id, 'print_type' => 'with_stock_only', 'per_sheet' => $rowsPerSheet]) }}"
               style="padding: 6px 16px; border-radius: 4px; text-decoration: none; font-size: 13px;
                      {{ $print_type === 'with_stock_only' ? 'background:#1a6b3a;color:#fff;font-weight:bold;' : 'background:#eee;color:#333;border:1px solid #ccc;' }}">
               With Stock Only
            </a>
        </div>
        <br>
        <button onclick="window.print()" style="padding: 10px 30px; font-size: 14px; cursor: pointer; background: #4CAF50; color: white; border: none; border-radius: 5px;">
            <strong>Print Count Sheets</strong>
        </button>
        <a href="{{ action([\App\Http\Controllers\StocktakeController::class, 'edit'], $stocktake->id) }}" style="margin-left: 20px; padding: 10px 30px; font-size: 14px; cursor: pointer; background: #2196F3; color: white; border: none; border-radius: 5px; text-decoration: none;">
            Back to Stocktake
        </a>
        <p style="margin-top: 15px; color: #666;">
            Total Products in Stock: <strong>{{ $totalProducts }}</strong> |
            Sheets to Print: <strong>{{ count($sheets) }}</strong> |
            Rows Per Sheet: <strong>{{ $rowsPerSheet }}</strong> |
            Type: <strong>
                @if($print_type === 'with_products') All Products (No Qty)
                @elseif($print_type === 'with_stock_only') With Stock Only (No Qty)
                @else Blank Sheet
                @endif
            </strong>
        </p>
    </div>

    @if($print_type === 'with_products' || $print_type === 'with_stock_only')
        {{-- PRODUCTS MODE (all products OR stock-only) — pre-filled names, qty blank --}}
        @foreach($sheets as $index => $sheetProducts)
        <div class="page">
            <div class="header">
                <h1>{{ $business->name ?? 'Company Name' }}</h1>
                <h2>STOCK COUNT SHEET &mdash; {{ $print_type === 'with_stock_only' ? 'ITEMS WITH STOCK (NO QUANTITIES)' : 'ALL PRODUCTS (NO QUANTITIES)' }}</h2>
            </div>

            <div class="meta-info">
                <div class="meta-left">
                    <div class="meta-row">
                        <span class="meta-label">Reference No:</span>
                        <span class="meta-value">{{ $stocktake->ref_no }}</span>
                    </div>
                    <div class="meta-row">
                        <span class="meta-label">Location:</span>
                        <span class="meta-value">{{ $stocktake->location->name ?? '-' }}</span>
                    </div>
                </div>
                <div class="meta-right">
                    <div class="meta-row">
                        <span class="meta-label">Date:</span>
                        <span class="meta-value">{{ \Carbon::parse($stocktake->transaction_date)->format('d/m/Y') }}</span>
                    </div>
                    <div class="meta-row">
                        <span class="meta-label">Sheet Number:</span>
                        <span class="sheet-number">{{ $index + 1 }} of {{ count($sheets) }}</span>
                    </div>
                </div>
            </div>

            <div class="counter-info">
                <h3>COUNTER INFORMATION (To be filled by person doing stock count)</h3>
                <div class="counter-row">
                    <div class="counter-field">
                        <label>Counter Name:</label>
                        <span class="line"></span>
                    </div>
                    <div class="counter-field">
                        <label>Date/Time:</label>
                        <span class="line"></span>
                    </div>
                </div>
                <div class="counter-row">
                    <div class="counter-field">
                        <label>Signature:</label>
                        <span class="line"></span>
                    </div>
                    <div class="counter-field">
                        <label>Area/Section:</label>
                        <span class="line"></span>
                    </div>
                </div>
            </div>

            <div class="instructions">
                <strong>INSTRUCTIONS:</strong> Products are pre-listed. Count the physical stock and record the quantity in the Counted Qty column.
                Fill in Lot Number and Expiry Date if applicable.
            </div>

            <table>
                <thead>
                    <tr>
                        <th class="num">#</th>
                        <th class="product-name">Product Name</th>
                        <th class="sku">SKU/Barcode</th>
                        <th class="qty-col">Counted Qty</th>
                        <th class="lot-col">Lot No.</th>
                        <th class="expiry-col">Expiry Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sheetProducts as $i => $product)
                    <tr>
                        <td class="num">{{ ($index * $rowsPerSheet) + $i + 1 }}</td>
                        <td class="product-name">
                            {{ $product->product_name }}
                            @if($product->variation_name && $product->variation_name !== 'DUMMY')
                                <small> &mdash; {{ $product->variation_name }}</small>
                            @endif
                        </td>
                        <td class="sku">{{ $product->sub_sku ?: $product->sku }}</td>
                        <td class="qty-col input-cell"></td>
                        <td class="lot-col input-cell"></td>
                        <td class="expiry-col input-cell"></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="signature-area">
                <div class="signature-box">
                    <div class="signature-line">Counter Signature</div>
                </div>
                <div class="signature-box">
                    <div class="signature-line">Supervisor Signature</div>
                </div>
                <div class="signature-box">
                    <div class="signature-line">Verified By</div>
                </div>
            </div>

            <div class="footer">
                Page {{ $index + 1 }} of {{ count($sheets) }} | Ref: {{ $stocktake->ref_no }} | Printed: {{ now()->format('d/m/Y H:i') }}
            </div>
        </div>
        @endforeach

    @else
        {{-- BLANK MODE --}}
        @foreach($sheets as $index => $rowCount)
        <div class="page">
            <div class="header">
                <h1>{{ $business->name ?? 'Company Name' }}</h1>
                <h2>STOCK COUNT SHEET (BLANK)</h2>
            </div>

            <div class="meta-info">
                <div class="meta-left">
                    <div class="meta-row">
                        <span class="meta-label">Reference No:</span>
                        <span class="meta-value">{{ $stocktake->ref_no }}</span>
                    </div>
                    <div class="meta-row">
                        <span class="meta-label">Location:</span>
                        <span class="meta-value">{{ $stocktake->location->name ?? '-' }}</span>
                    </div>
                </div>
                <div class="meta-right">
                    <div class="meta-row">
                        <span class="meta-label">Date:</span>
                        <span class="meta-value">{{ \Carbon::parse($stocktake->transaction_date)->format('d/m/Y') }}</span>
                    </div>
                    <div class="meta-row">
                        <span class="meta-label">Sheet Number:</span>
                        <span class="sheet-number">{{ $index + 1 }} of {{ count($sheets) }}</span>
                    </div>
                </div>
            </div>

            <div class="counter-info">
                <h3>COUNTER INFORMATION (To be filled by person doing stock count)</h3>
                <div class="counter-row">
                    <div class="counter-field">
                        <label>Counter Name:</label>
                        <span class="line"></span>
                    </div>
                    <div class="counter-field">
                        <label>Date/Time:</label>
                        <span class="line"></span>
                    </div>
                </div>
                <div class="counter-row">
                    <div class="counter-field">
                        <label>Signature:</label>
                        <span class="line"></span>
                    </div>
                    <div class="counter-field">
                        <label>Area/Section:</label>
                        <span class="line"></span>
                    </div>
                </div>
            </div>

            <div class="instructions">
                <strong>INSTRUCTIONS:</strong> Write the Product Name/SKU, then count the physical stock and record the quantity.
                Fill in Lot Number and Expiry Date if applicable. Leave blank if not applicable.
            </div>

            <table>
                <thead>
                    <tr>
                        <th class="num">#</th>
                        <th class="product-name">Product Name</th>
                        <th class="sku">SKU/Barcode</th>
                        <th class="qty-col">Counted Qty</th>
                        <th class="lot-col">Lot No.</th>
                        <th class="expiry-col">Expiry Date</th>
                    </tr>
                </thead>
                <tbody>
                    @for($i = 0; $i < $rowCount; $i++)
                    <tr>
                        <td class="num">{{ ($index * $rowsPerSheet) + $i + 1 }}</td>
                        <td class="product-name input-cell"></td>
                        <td class="sku input-cell"></td>
                        <td class="qty-col input-cell"></td>
                        <td class="lot-col input-cell"></td>
                        <td class="expiry-col input-cell"></td>
                    </tr>
                    @endfor
                </tbody>
            </table>

            <div class="signature-area">
                <div class="signature-box">
                    <div class="signature-line">Counter Signature</div>
                </div>
                <div class="signature-box">
                    <div class="signature-line">Supervisor Signature</div>
                </div>
                <div class="signature-box">
                    <div class="signature-line">Verified By</div>
                </div>
            </div>

            <div class="footer">
                Page {{ $index + 1 }} of {{ count($sheets) }} | Ref: {{ $stocktake->ref_no }} | Printed: {{ now()->format('d/m/Y H:i') }}
            </div>
        </div>
        @endforeach
    @endif

</body>
</html>
