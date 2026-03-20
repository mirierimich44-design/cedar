<!-- Order Receipt for Printing in Same Window -->
<div class="ticket" style="font-family: 'Courier New', Courier, monospace; font-size: 12px; line-height: 1.4; max-width: 300px; margin: 0 auto; padding: 10px;">

    <!-- Header -->
    <div style="text-align: center; border-bottom: 1px dashed #000; padding-bottom: 10px; margin-bottom: 10px;">
        <h2 style="font-size: 16px; margin: 0 0 5px 0;">{{ $business->name ?? 'Shop' }}</h2>
        @if($order->location)
            <p style="margin: 0; font-size: 11px;">{{ $order->location->name }}</p>
            @if($order->location->mobile)
                <p style="margin: 0; font-size: 11px;">Tel: {{ $order->location->mobile }}</p>
            @endif
        @endif
        <p style="margin: 10px 0 0 0; font-weight: bold; font-size: 14px;">SHOP ORDER</p>
    </div>

    <!-- Order Info -->
    <div style="margin-bottom: 10px;">
        <table style="width: 100%; font-size: 11px;">
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
                <td style="text-align: right; font-weight: bold;">{{ strtoupper($order->status) }}</td>
            </tr>
            @if($order->createdBy)
            <tr>
                <td><strong>By:</strong></td>
                <td style="text-align: right;">{{ $order->createdBy->first_name }}</td>
            </tr>
            @endif
        </table>
    </div>

    <!-- Items -->
    <div style="border-top: 1px dashed #000; border-bottom: 1px dashed #000; padding: 10px 0; margin-bottom: 10px;">
        <table style="width: 100%; font-size: 11px;">
            <thead>
                <tr style="border-bottom: 1px solid #000;">
                    <th style="text-align: left; padding-bottom: 5px;">Item</th>
                    <th style="text-align: right; width: 60px; padding-bottom: 5px;">Qty</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->orderLines as $line)
                <tr>
                    <td style="padding: 3px 0; vertical-align: top;">
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
                    <td style="text-align: right; padding: 3px 0;">{{ number_format($line->quantity) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if($order->notes)
    <!-- Notes -->
    <div style="margin-bottom: 10px; padding: 5px; background: #f5f5f5;">
        <strong style="font-size: 10px;">Notes:</strong>
        <p style="margin: 3px 0 0 0; font-size: 11px;">{{ $order->notes }}</p>
    </div>
    @endif

    <!-- Footer -->
    <div style="text-align: center; border-top: 1px dashed #000; padding-top: 10px; font-size: 10px;">
        <p style="margin: 0;">--- SHOP ORDER ---</p>
        <p style="margin: 5px 0 0 0;">Printed: {{ now()->format('d/m/Y H:i') }}</p>
    </div>
</div>
