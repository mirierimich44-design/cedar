<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Bill {{ $bill->bill_number }}</title>
<style>
  body { font-family: Arial, sans-serif; font-size: 12px; color: #000; }
  .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 10px; margin-bottom: 15px; }
  .header h2 { margin: 0; font-size: 20px; }
  .header p { margin: 2px 0; }
  .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 4px; margin-bottom: 15px; }
  .info-grid p { margin: 2px 0; }
  table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
  th, td { border: 1px solid #ccc; padding: 5px 8px; }
  th { background: #f5f5f5; font-weight: bold; }
  .text-right { text-align: right; }
  .summary { float: right; width: 300px; }
  .summary table td { border: none; padding: 3px 8px; }
  .total-row { font-weight: bold; border-top: 2px solid #333; }
  .badge-paid { color: green; font-weight: bold; }
  .badge-unpaid { color: red; font-weight: bold; }
  .badge-partial { color: orange; font-weight: bold; }
  .footer { margin-top: 40px; border-top: 1px solid #ccc; padding-top: 8px; font-size: 10px; text-align: center; color: #666; }
  @media print { body { margin: 10mm; } }
</style>
</head>
<body onload="window.print()">

<div class="header">
    <h2>{{ session('business.name') }}</h2>
    <p>{{ session('business.landmark') }}, {{ session('business.city') }}, {{ session('business.country') }}</p>
    <p>Tel: {{ session('business.mobile') }} | Email: {{ session('business.email') }}</p>
    <h3 style="margin-top:8px">PATIENT BILL / RECEIPT</h3>
</div>

<div class="info-grid">
    <div>
        <p><strong>Bill No.:</strong> {{ $bill->bill_number }}</p>
        <p><strong>Patient:</strong> {{ $bill->patient_name }}</p>
        <p><strong>Phone:</strong> {{ $bill->patient_phone ?? '—' }}</p>
        <p><strong>Gender:</strong> {{ ucfirst($bill->gender ?? '—') }}</p>
        <p><strong>NHIF No.:</strong> {{ $bill->nhif_number ?? '—' }}</p>
    </div>
    <div>
        <p><strong>Visit Date:</strong> {{ $bill->visit_date ? $bill->visit_date->format('d M Y') : '—' }}</p>
        <p><strong>Visit Type:</strong> {{ ucfirst($bill->visit_type) }}</p>
        <p><strong>Doctor:</strong> {{ $bill->doctor_name ?? '—' }}</p>
        <p><strong>Diagnosis:</strong> {{ $bill->diagnosis ?? '—' }}</p>
        <p><strong>Payment Status:</strong>
            <span class="badge-{{ $bill->payment_status }}">{{ strtoupper($bill->payment_status) }}</span>
        </p>
    </div>
</div>

<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Service / Description</th>
            <th class="text-right">Qty</th>
            <th class="text-right">Unit Price (KES)</th>
            <th class="text-right">Total (KES)</th>
        </tr>
    </thead>
    <tbody>
        @foreach($bill->bill_items ?? [] as $idx => $item)
        <tr>
            <td>{{ $idx + 1 }}</td>
            <td>{{ $item['name'] ?? '' }}</td>
            <td class="text-right">{{ $item['qty'] ?? 1 }}</td>
            <td class="text-right">{{ number_format($item['unit_price'] ?? 0, 2) }}</td>
            <td class="text-right">{{ number_format($item['total'] ?? 0, 2) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<div class="summary">
    <table>
        <tr><td>Subtotal:</td><td class="text-right">KES {{ number_format($bill->subtotal, 2) }}</td></tr>
        <tr><td>NHIF Deduction:</td><td class="text-right">KES {{ number_format($bill->nhif_amount, 2) }}</td></tr>
        <tr><td>Discount:</td><td class="text-right">KES {{ number_format($bill->discount, 2) }}</td></tr>
        <tr class="total-row"><td>TOTAL:</td><td class="text-right">KES {{ number_format($bill->total_amount, 2) }}</td></tr>
        <tr><td>Amount Paid:</td><td class="text-right">KES {{ number_format($bill->paid_amount, 2) }}</td></tr>
        <tr class="total-row"><td>BALANCE:</td><td class="text-right">KES {{ number_format($bill->balance, 2) }}</td></tr>
    </table>
    <p><strong>Payment Method:</strong> {{ ucfirst($bill->payment_method) }}
        @if($bill->mpesa_code) | Code: {{ $bill->mpesa_code }} @endif
    </p>
</div>

<div style="clear:both; margin-top: 50px;">
    <table style="width:100%; border:none">
        <tr>
            <td style="border:none; width:40%">
                <p>Patient/Guardian Signature: _____________________</p>
            </td>
            <td style="border:none; width:20%"></td>
            <td style="border:none; width:40%">
                <p>Authorised by: _____________________</p>
            </td>
        </tr>
    </table>
</div>

<div class="footer">
    <p>Printed on: {{ now()->format('d M Y H:i') }} | {{ session('business.name') }}</p>
    <p>This is a computer-generated receipt. Thank you for choosing our services.</p>
</div>
</body>
</html>
