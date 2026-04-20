<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Waybill {{ $parcel->waybill_number }}</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family: Arial, sans-serif; font-size: 12px; color: #111; }
        .receipt { width: 80mm; margin: 0 auto; padding: 8px; }
        .header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 8px; margin-bottom: 8px; }
        .header h1 { font-size: 18px; }
        .header h2 { font-size: 14px; color: #444; }
        .waybill-box { text-align: center; background: #111; color: #fff; padding: 6px; margin: 8px 0; border-radius: 4px; }
        .waybill-box .number { font-size: 22px; font-weight: bold; letter-spacing: 2px; }
        table.info { width: 100%; border-collapse: collapse; margin: 6px 0; }
        table.info td { padding: 3px 4px; vertical-align: top; }
        table.info td:first-child { font-weight: bold; width: 40%; color: #555; }
        .section-title { background: #eee; padding: 3px 6px; font-weight: bold; font-size: 11px; margin: 6px 0 2px; text-transform: uppercase; }
        .divider { border-top: 1px dashed #999; margin: 6px 0; }
        .footer { text-align: center; font-size: 10px; color: #777; margin-top: 8px; }
        .charge-box { background: #f0f8e8; border: 1px solid #5cb85c; padding: 6px; text-align: center; margin: 6px 0; border-radius: 3px; }
        .charge-box .amount { font-size: 20px; font-weight: bold; color: #2d7a2d; }
        .payment-badge { display: inline-block; padding: 2px 8px; border-radius: 3px; font-size: 11px; font-weight: bold; }
        .paid { background: #dff0d8; color: #2d7a2d; }
        .pending { background: #fcf8e3; color: #a07800; }
        .cod { background: #d9edf7; color: #1a5276; }
        @media print { body { margin: 0; } }
    </style>
</head>
<body>
<div class="receipt">
    {{-- Header --}}
    <div class="header">
        @if($business->logo)
            <img src="{{ asset('storage/'.config('constants.business_logo_path').'/'.$business->logo) }}" style="max-height:40px; max-width:120px;" alt="logo">
        @endif
        <h1>{{ $business->name }}</h1>
        <h2>PARCEL WAYBILL</h2>
    </div>

    {{-- Waybill Number --}}
    <div class="waybill-box">
        <div style="font-size:10px; letter-spacing:1px;">WAYBILL NUMBER</div>
        <div class="number">{{ $parcel->waybill_number }}</div>
    </div>

    {{-- Charge --}}
    <div class="charge-box">
        <div style="font-size:10px;">TOTAL CHARGE</div>
        <div class="amount">KES {{ number_format($parcel->charge_amount, 2) }}</div>
        <span class="payment-badge {{ $parcel->payment_status === 'paid' ? 'paid' : ($parcel->payment_method === 'cod' ? 'cod' : 'pending') }}">
            {{ $parcel->payment_method === 'cod' ? 'CASH ON DELIVERY' : strtoupper($parcel->payment_status) }}
        </span>
    </div>

    {{-- Sender --}}
    <div class="section-title">Sender</div>
    <table class="info">
        <tr><td>Name:</td><td>{{ $parcel->sender_name }}</td></tr>
        <tr><td>Phone:</td><td>{{ $parcel->sender_phone }}</td></tr>
        @if($parcel->sender_id_number)
        <tr><td>ID No:</td><td>{{ $parcel->sender_id_number }}</td></tr>
        @endif
        <tr><td>From:</td><td>{{ $parcel->originStation?->name }} ({{ $parcel->originStation?->town }})</td></tr>
    </table>

    {{-- Recipient --}}
    <div class="section-title">Recipient</div>
    <table class="info">
        <tr><td>Name:</td><td>{{ $parcel->recipient_name }}</td></tr>
        <tr><td>Phone:</td><td>{{ $parcel->recipient_phone }}</td></tr>
        @if($parcel->recipient_id_number)
        <tr><td>ID No:</td><td>{{ $parcel->recipient_id_number }}</td></tr>
        @endif
        <tr><td>To:</td><td>{{ $parcel->destinationStation?->name }} ({{ $parcel->destinationStation?->town }})</td></tr>
    </table>

    {{-- Parcel Details --}}
    <div class="section-title">Parcel Info</div>
    <table class="info">
        <tr><td>Weight:</td><td>{{ $parcel->weight_kg }} kg</td></tr>
        @if($parcel->description)
        <tr><td>Contents:</td><td>{{ $parcel->description }}</td></tr>
        @endif
        @if($parcel->declared_value > 0)
        <tr><td>Value:</td><td>KES {{ number_format($parcel->declared_value, 2) }}</td></tr>
        @endif
        <tr><td>Booked:</td><td>{{ $parcel->created_at->format('d/m/Y H:i') }}</td></tr>
        <tr><td>Status:</td><td><strong>{{ ucfirst($parcel->status) }}</strong></td></tr>
    </table>

    <div class="divider"></div>

    {{-- Terms --}}
    <div style="font-size:9px; color:#666; margin:4px 0;">
        <strong>Terms:</strong> Items are transported at owner's risk. Fragile/perishable items must be declared. Max liability: KES 10,000 unless declared value stated. Collection within 7 days or storage charges apply.
    </div>

    {{-- Tracking --}}
    <div class="divider"></div>
    <div style="text-align:center; font-size:10px;">
        Track online: <strong>{{ url('/track/'.$parcel->waybill_number) }}</strong>
    </div>

    <div class="footer">
        <div>{{ $business->name }} &mdash; {{ $business->mobile ?? '' }}</div>
        <div>Printed: {{ now()->format('d/m/Y H:i') }}</div>
    </div>
</div>
<script>window.onload = function() { window.print(); }</script>
</body>
</html>
