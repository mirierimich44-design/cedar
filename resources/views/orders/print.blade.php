<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Shop Order - {{ $order->ref_no }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            line-height: 1.4;
            padding: 10px;
            max-width: 300px;
            margin: 0 auto;
        }
        .header {
            text-align: center;
            border-bottom: 1px dashed #000;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }
        .header h1 {
            font-size: 16px;
            margin-bottom: 5px;
        }
        .header p {
            font-size: 10px;
        }
        .order-info {
            margin-bottom: 10px;
        }
        .order-info table {
            width: 100%;
        }
        .order-info td {
            padding: 2px 0;
        }
        .items {
            border-top: 1px dashed #000;
            border-bottom: 1px dashed #000;
            padding: 10px 0;
            margin-bottom: 10px;
        }
        .items table {
            width: 100%;
        }
        .items th {
            text-align: left;
            border-bottom: 1px solid #000;
            padding-bottom: 5px;
            font-size: 11px;
        }
        .items td {
            padding: 5px 0;
            vertical-align: top;
        }
        .items .qty {
            text-align: center;
            width: 40px;
        }
        .items .price {
            text-align: right;
            width: 60px;
        }
        .notes {
            margin-bottom: 10px;
            padding: 5px;
            background: #f5f5f5;
        }
        .notes h4 {
            font-size: 11px;
            margin-bottom: 3px;
        }
        .footer {
            text-align: center;
            border-top: 1px dashed #000;
            padding-top: 10px;
            font-size: 10px;
        }
        .status-badge {
            display: inline-block;
            padding: 2px 8px;
            font-size: 10px;
            font-weight: bold;
            border-radius: 3px;
            text-transform: uppercase;
        }
        .status-pending { background: #ffc107; color: #000; }
        .status-processing { background: #17a2b8; color: #fff; }
        .status-completed { background: #28a745; color: #fff; }
        .status-cancelled { background: #dc3545; color: #fff; }
        @media print {
            body {
                padding: 0;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $business->name ?? 'Shop Order' }}</h1>
        @if($order->location)
            <p>{{ $order->location->name }}</p>
            @if($order->location->mobile)
                <p>Tel: {{ $order->location->mobile }}</p>
            @endif
        @endif
    </div>

    <div class="order-info">
        <table>
            <tr>
                <td><strong>Order #:</strong></td>
                <td style="text-align: right;">{{ $order->ref_no }}</td>
            </tr>
            <tr>
                <td><strong>Date:</strong></td>
                <td style="text-align: right;">{{ $order->created_at->format('d/m/Y H:i') }}</td>
            </tr>
            <tr>
                <td><strong>Status:</strong></td>
                <td style="text-align: right;">
                    <span class="status-badge status-{{ $order->status }}">{{ ucfirst($order->status) }}</span>
                </td>
            </tr>
            @if($order->createdBy)
            <tr>
                <td><strong>Created By:</strong></td>
                <td style="text-align: right;">{{ $order->createdBy->first_name }}</td>
            </tr>
            @endif
        </table>
    </div>

    <div class="items">
        <table>
            <thead>
                <tr>
                    <th>Item</th>
                    <th class="qty" style="text-align: right;">Qty</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->orderLines as $line)
                <tr>
                    <td>
                        @if($line->product)
                            {{ $line->product->name }}
                            @if($line->variation && $line->variation->name !== 'DUMMY')
                                <br><small>({{ $line->variation->name }})</small>
                            @endif
                        @elseif($line->custom_product_name)
                            {{ $line->custom_product_name }}
                            <br><small>(Custom)</small>
                        @else
                            -
                        @endif
                    </td>
                    <td class="qty" style="text-align: right;">{{ number_format($line->quantity) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if($order->notes)
    <div class="notes">
        <h4>Notes:</h4>
        <p>{{ $order->notes }}</p>
    </div>
    @endif

    <div class="footer">
        <p>--- SHOP ORDER ---</p>
        <p>Printed: {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <div class="no-print" style="text-align: center; margin-top: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; font-size: 14px; cursor: pointer;">
            Print Order
        </button>
        <button onclick="window.close()" style="padding: 10px 20px; font-size: 14px; cursor: pointer; margin-left: 10px;">
            Close
        </button>
    </div>

    <script>
        // Auto print when page loads
        window.onload = function() {
            // Small delay to ensure styles are loaded
            setTimeout(function() {
                window.print();
            }, 500);
        };
    </script>
</body>
</html>
