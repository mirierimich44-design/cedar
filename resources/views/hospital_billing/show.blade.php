@extends('layouts.app')
@section('title', 'Bill - ' . $bill->bill_number)

@section('content')
<section class="content-header">
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">
        Bill #{{ $bill->bill_number }}
        @php $colors = \App\HospitalBill::paymentStatusColors(); @endphp
        <span class="label label-{{ $colors[$bill->payment_status] ?? 'default' }} tw-ml-2">{{ ucfirst($bill->payment_status) }}</span>
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('hospital-billing.index') }}">Hospital Billing</a></li>
        <li class="active">{{ $bill->bill_number }}</li>
    </ol>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-8">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Patient Details</h3>
                    <div class="box-tools pull-right">
                        <a href="{{ route('hospital-billing.print', $bill->id) }}" target="_blank" class="btn btn-sm btn-default"><i class="fa fa-print"></i> Print</a>
                        <a href="{{ route('hospital-billing.edit', $bill->id) }}" class="btn btn-sm btn-primary"><i class="fa fa-edit"></i> Edit</a>
                    </div>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <p><strong>Patient:</strong> {{ $bill->patient_name }}</p>
                            <p><strong>Phone:</strong> {{ $bill->patient_phone ?? '—' }}</p>
                            <p><strong>Gender:</strong> {{ ucfirst($bill->gender ?? '—') }}</p>
                            <p><strong>NHIF No.:</strong> {{ $bill->nhif_number ?? '—' }}</p>
                        </div>
                        <div class="col-sm-6">
                            <p><strong>Visit Date:</strong> {{ $bill->visit_date ? $bill->visit_date->format('d M Y') : '—' }}</p>
                            <p><strong>Visit Type:</strong> {{ ucfirst($bill->visit_type) }}</p>
                            <p><strong>Doctor:</strong> {{ $bill->doctor_name ?? '—' }}</p>
                            <p><strong>Diagnosis:</strong> {{ $bill->diagnosis ?? '—' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bill Items -->
            <div class="box box-default">
                <div class="box-header with-border"><h3 class="box-title">Services Rendered</h3></div>
                <div class="box-body no-padding">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>#</th><th>Service</th><th class="text-right">Qty</th>
                                <th class="text-right">Unit Price</th><th class="text-right">Total</th>
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
                </div>
            </div>
        </div>

        <!-- Summary -->
        <div class="col-md-4">
            <div class="box box-success">
                <div class="box-header with-border"><h3 class="box-title">Payment Summary</h3></div>
                <div class="box-body">
                    <table class="table table-condensed">
                        <tr><td>Subtotal:</td><td class="text-right">KES {{ number_format($bill->subtotal, 2) }}</td></tr>
                        <tr><td>NHIF Deduction:</td><td class="text-right">KES {{ number_format($bill->nhif_amount, 2) }}</td></tr>
                        <tr><td>Discount:</td><td class="text-right">KES {{ number_format($bill->discount, 2) }}</td></tr>
                        <tr class="bg-light-blue"><td><strong>Total:</strong></td><td class="text-right"><strong>KES {{ number_format($bill->total_amount, 2) }}</strong></td></tr>
                        <tr><td>Amount Paid:</td><td class="text-right">KES {{ number_format($bill->paid_amount, 2) }}</td></tr>
                        <tr class="{{ $bill->balance > 0 ? 'bg-yellow' : 'bg-green' }}">
                            <td><strong>Balance:</strong></td>
                            <td class="text-right"><strong>KES {{ number_format($bill->balance, 2) }}</strong></td>
                        </tr>
                    </table>
                    <p><strong>Payment Method:</strong> {{ ucfirst($bill->payment_method) }}</p>
                    @if($bill->mpesa_code)
                    <p><strong>M-Pesa Code:</strong> {{ $bill->mpesa_code }}</p>
                    @endif
                    @if($bill->notes)
                    <p><strong>Notes:</strong> {{ $bill->notes }}</p>
                    @endif
                    <p class="text-muted text-sm">Created by {{ optional($bill->creator)->first_name }} on {{ $bill->created_at->format('d M Y H:i') }}</p>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
