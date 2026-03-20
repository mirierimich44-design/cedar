<!-- Follow-up Receipt for Printing in Same Window -->
<div class="ticket" style="font-family: 'Courier New', Courier, monospace; font-size: 12px; line-height: 1.4; max-width: 300px; margin: 0 auto; padding: 10px;">

    <!-- Header -->
    <div style="text-align: center; border-bottom: 1px dashed #000; padding-bottom: 10px; margin-bottom: 10px;">
        <h2 style="font-size: 16px; margin: 0 0 5px 0;">{{ $business->name ?? 'Shop' }}</h2>
        @if($followup->location)
            <p style="margin: 0; font-size: 11px;">{{ $followup->location->name }}</p>
            @if($followup->location->mobile)
                <p style="margin: 0; font-size: 11px;">Tel: {{ $followup->location->mobile }}</p>
            @endif
        @endif
        <p style="margin: 10px 0 0 0; font-weight: bold; font-size: 14px;">PRODUCT FOLLOW-UP</p>
    </div>

    <!-- Customer Info -->
    <div style="margin-bottom: 10px; padding: 5px; background: #f9f9f9;">
        <table style="width: 100%; font-size: 11px;">
            <tr>
                <td><strong>Customer:</strong></td>
                <td style="text-align: right;">{{ $followup->customer_name ?: 'N/A' }}</td>
            </tr>
            <tr>
                <td><strong>Phone:</strong></td>
                <td style="text-align: right; font-weight: bold;">{{ $followup->customer_phone }}</td>
            </tr>
        </table>
    </div>

    <!-- Follow-up Info -->
    <div style="margin-bottom: 10px;">
        <table style="width: 100%; font-size: 11px;">
            <tr>
                <td><strong>Date:</strong></td>
                <td style="text-align: right;">{{ $followup->created_at->format('d/m/Y H:i') }}</td>
            </tr>
            <tr>
                <td><strong>Status:</strong></td>
                <td style="text-align: right; font-weight: bold;">{{ strtoupper($followup->status) }}</td>
            </tr>
            @if($followup->createdBy)
            <tr>
                <td><strong>By:</strong></td>
                <td style="text-align: right;">{{ $followup->createdBy->first_name }}</td>
            </tr>
            @endif
        </table>
    </div>

    <!-- Product -->
    <div style="border-top: 1px dashed #000; border-bottom: 1px dashed #000; padding: 10px 0; margin-bottom: 10px;">
        <p style="margin: 0 0 5px 0; font-weight: bold; font-size: 11px;">REQUESTED PRODUCT:</p>
        <table style="width: 100%; font-size: 11px;">
            <tr>
                <td style="padding: 3px 0; vertical-align: top;">
                    @if($followup->product)
                        <strong>{{ $followup->product->name }}</strong>
                        @if($followup->variation && $followup->variation->name !== 'DUMMY')
                            <br><small>({{ $followup->variation->name }})</small>
                        @endif
                    @elseif($followup->custom_product_name)
                        <strong>{{ $followup->custom_product_name }}</strong>
                        <br><small>(Custom Request)</small>
                    @else
                        -
                    @endif
                </td>
                <td style="text-align: right; padding: 3px 0; vertical-align: top;">
                    <strong>Qty: {{ number_format($followup->quantity) }}</strong>
                </td>
            </tr>
        </table>
    </div>

    @if($followup->comment)
    <!-- Comment -->
    <div style="margin-bottom: 10px; padding: 5px; background: #f5f5f5;">
        <strong style="font-size: 10px;">Comment:</strong>
        <p style="margin: 3px 0 0 0; font-size: 11px;">{{ $followup->comment }}</p>
    </div>
    @endif

    <!-- Action Reminder -->
    <div style="margin-bottom: 10px; padding: 5px; border: 1px solid #333; text-align: center;">
        <p style="margin: 0; font-size: 10px; font-weight: bold;">PLEASE CONTACT CUSTOMER WHEN PRODUCT IS AVAILABLE</p>
    </div>

    <!-- Footer -->
    <div style="text-align: center; border-top: 1px dashed #000; padding-top: 10px; font-size: 10px;">
        <p style="margin: 0;">--- FOLLOW-UP REMINDER ---</p>
        <p style="margin: 5px 0 0 0;">Printed: {{ now()->format('d/m/Y H:i') }}</p>
    </div>
</div>
