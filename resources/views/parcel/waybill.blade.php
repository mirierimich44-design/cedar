<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Waybill {{ $parcel->waybill_number }}</title>
<style>
  * { box-sizing: border-box; }
  body { font-family: Arial, sans-serif; font-size: 11px; color: #000; margin: 0; padding: 10mm; }
  .waybill { border: 2px solid #000; width: 100%; max-width: 190mm; margin: 0 auto; }
  .header { display: flex; align-items: center; border-bottom: 2px solid #000; padding: 6px 10px; background: #1a237e; color: white; }
  .header h2 { margin: 0; font-size: 16px; flex: 1; }
  .header .waybill-no { font-size: 20px; font-weight: bold; text-align: right; }
  .section { padding: 6px 10px; border-bottom: 1px solid #999; }
  .section-title { font-size: 9px; font-weight: bold; text-transform: uppercase; color: #555; margin-bottom: 3px; }
  .two-col { display: flex; gap: 0; }
  .two-col .col { flex: 1; padding: 6px 10px; border-bottom: 1px solid #999; }
  .two-col .col:first-child { border-right: 1px solid #999; }
  .field { margin-bottom: 3px; }
  .field label { font-size: 9px; color: #555; display: block; }
  .field span { font-size: 12px; font-weight: bold; }
  .charges-table { width: 100%; font-size: 11px; }
  .charges-table td { padding: 2px 4px; }
  .charges-table .total-row { font-weight: bold; border-top: 1px solid #000; font-size: 13px; }
  .barcode-area { padding: 8px 10px; text-align: center; border-bottom: 1px solid #999; }
  .waybill-big { font-size: 28px; font-weight: bold; letter-spacing: 4px; }
  .route-arrow { font-size: 30px; text-align: center; padding: 8px; font-weight: bold; border-bottom: 1px solid #999; color: #1a237e; }
  .footer-sigs { display: flex; padding: 8px 10px; }
  .footer-sigs .sig { flex: 1; }
  .sig-line { border-bottom: 1px solid #000; margin: 20px 5px 3px 0; }
  .badge-tag { display: inline-block; background: #1a237e; color: white; padding: 2px 6px; border-radius: 3px; font-size: 10px; }
  @media print { body { padding: 5mm; } button { display: none; } }
</style>
</head>
<body onload="window.print()">

<div class="waybill">
    <!-- Header -->
    <div class="header">
        <div>
            <h2>{{ session('business.name') ?? 'COURIER SERVICES' }}</h2>
            <div style="font-size:10px">{{ session('business.city') }}, Kenya | Tel: {{ session('business.mobile') }}</div>
        </div>
        <div class="waybill-no">
            <div style="font-size:10px;font-weight:normal">WAYBILL</div>
            {{ $parcel->waybill_number }}
        </div>
    </div>

    <!-- Barcode / WB number prominent -->
    <div class="barcode-area">
        <div class="waybill-big">{{ $parcel->waybill_number }}</div>
        <div style="font-size:9px;color:#666">Scan or quote this number for tracking</div>
    </div>

    <!-- Route -->
    <div class="route-arrow">
        📍 {{ strtoupper($parcel->from_town) }}  ——→  📍 {{ strtoupper($parcel->to_town) }}
        <span class="badge-tag">{{ strtoupper($parcel->service_type) }}</span>
    </div>

    <!-- Sender / Receiver -->
    <div class="two-col">
        <div class="col">
            <div class="section-title">FROM (Sender)</div>
            <div class="field"><label>Name</label><span>{{ $parcel->sender_name }}</span></div>
            <div class="field"><label>Phone</label><span>{{ $parcel->sender_phone }}</span></div>
            <div class="field"><label>ID No.</label><span>{{ $parcel->sender_id_number ?? '—' }}</span></div>
            <div class="field"><label>Town</label><span>{{ $parcel->from_town }}</span></div>
            <div class="field"><label>Pickup</label><span>{{ $parcel->pickup_type === 'home_pickup' ? 'Home Pickup' : 'Drop Off' }}</span></div>
        </div>
        <div class="col">
            <div class="section-title">TO (Receiver)</div>
            <div class="field"><label>Name</label><span>{{ $parcel->receiver_name }}</span></div>
            <div class="field"><label>Phone</label><span>{{ $parcel->receiver_phone }}</span></div>
            <div class="field"><label>ID No.</label><span>{{ $parcel->receiver_id_number ?? '—' }}</span></div>
            <div class="field"><label>Town</label><span>{{ $parcel->to_town }}</span></div>
            <div class="field"><label>Address</label><span>{{ $parcel->receiver_address ?? '—' }}</span></div>
            <div class="field"><label>Delivery</label><span>{{ $parcel->delivery_type === 'home_delivery' ? 'Home Delivery' : 'Depot Pickup' }}</span></div>
        </div>
    </div>

    <!-- Parcel Info + Charges -->
    <div class="two-col">
        <div class="col">
            <div class="section-title">Parcel Details</div>
            <div class="field"><label>Type</label><span>{{ ucfirst($parcel->parcel_type) }}</span></div>
            <div class="field"><label>Weight</label><span>{{ $parcel->weight_kg }} kg</span></div>
            <div class="field"><label>Pieces</label><span>{{ $parcel->pieces }}</span></div>
            <div class="field"><label>Dimensions</label><span>{{ $parcel->dimensions ?? '—' }}</span></div>
            <div class="field"><label>Description</label><span>{{ $parcel->parcel_description ?? '—' }}</span></div>
            <div class="field"><label>Declared Value</label><span>KES {{ number_format($parcel->declared_value, 2) }}</span></div>
            @if($parcel->special_instructions)
            <div class="field"><label>Special Instr.</label><span style="color:red">{{ $parcel->special_instructions }}</span></div>
            @endif
        </div>
        <div class="col">
            <div class="section-title">Charges</div>
            <table class="charges-table">
                <tr><td>Freight</td><td style="text-align:right">KES {{ number_format($parcel->freight_charge,2) }}</td></tr>
                @if($parcel->insurance_charge > 0)
                <tr><td>Insurance</td><td style="text-align:right">KES {{ number_format($parcel->insurance_charge,2) }}</td></tr>
                @endif
                @if($parcel->pickup_charge > 0)
                <tr><td>Pickup</td><td style="text-align:right">KES {{ number_format($parcel->pickup_charge,2) }}</td></tr>
                @endif
                @if($parcel->delivery_charge > 0)
                <tr><td>Delivery</td><td style="text-align:right">KES {{ number_format($parcel->delivery_charge,2) }}</td></tr>
                @endif
                <tr class="total-row"><td><strong>TOTAL</strong></td><td style="text-align:right"><strong>KES {{ number_format($parcel->total_amount,2) }}</strong></td></tr>
                <tr><td>Paid</td><td style="text-align:right">KES {{ number_format($parcel->paid_amount,2) }}</td></tr>
                <tr style="color:{{ ($parcel->total_amount-$parcel->paid_amount)>0 ? 'red':'green' }}">
                    <td><strong>Balance</strong></td>
                    <td style="text-align:right"><strong>KES {{ number_format($parcel->total_amount-$parcel->paid_amount,2) }}</strong></td>
                </tr>
            </table>
            <div style="margin-top:5px">
                <strong>Pay by:</strong> {{ ucfirst($parcel->payment_by) }} | {{ ucfirst($parcel->payment_method) }}
                @if($parcel->mpesa_code)<br><strong>M-Pesa:</strong> {{ $parcel->mpesa_code }}@endif
            </div>
            @if($parcel->expected_delivery_date)
            <div style="margin-top:5px;padding:4px;background:#e3f2fd;border:1px solid #90caf9;">
                <strong>Expected Delivery:</strong> {{ $parcel->expected_delivery_date->format('D, d M Y') }}
            </div>
            @endif
        </div>
    </div>

    <!-- Signatures -->
    <div class="footer-sigs">
        <div class="sig">
            <div class="section-title">Sender Signature</div>
            <div class="sig-line"></div>
            <div>Date: _______________</div>
        </div>
        <div class="sig">
            <div class="section-title">Received by (Depot Staff)</div>
            <div class="sig-line"></div>
            <div>Date: _______________</div>
        </div>
        <div class="sig">
            <div class="section-title">Delivered to (Recipient)</div>
            <div class="sig-line"></div>
            <div>Date: _______________</div>
        </div>
    </div>

    <div style="padding:5px 10px;font-size:9px;color:#666;text-align:center;border-top:1px solid #ccc">
        Booked: {{ $parcel->created_at->format('d M Y H:i') }} | {{ session('business.name') }} — This waybill is the property of the carrier. Please keep it safe.
    </div>
</div>

</body>
</html>
