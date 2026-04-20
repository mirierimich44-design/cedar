@extends('layouts.app')
@section('title', 'Parcel - ' . $parcel->waybill_number)

@section('content')
<section class="content-header">
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">
        Parcel: {{ $parcel->waybill_number }}
        {!! $parcel->status_badge !!}
        @php $pay_colors = ['unpaid' => 'danger', 'partial' => 'warning', 'paid' => 'success']; @endphp
        <span class="label label-{{ $pay_colors[$parcel->payment_status] ?? 'default' }}">{{ ucfirst($parcel->payment_status) }}</span>
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('parcels.index') }}">Parcels</a></li>
        <li class="active">{{ $parcel->waybill_number }}</li>
    </ol>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-8">
            <!-- Sender / Receiver -->
            <div class="row">
                <div class="col-md-6">
                    <div class="box box-default">
                        <div class="box-header with-border bg-light-blue"><h3 class="box-title"><i class="fa fa-user"></i> Sender</h3></div>
                        <div class="box-body">
                            <p><strong>Name:</strong> {{ $parcel->sender_name }}</p>
                            <p><strong>Phone:</strong> <a href="tel:{{ $parcel->sender_phone }}">{{ $parcel->sender_phone }}</a></p>
                            <p><strong>ID:</strong> {{ $parcel->sender_id_number ?? '—' }}</p>
                            <p><strong>Town:</strong> {{ $parcel->from_town }}</p>
                            <p><strong>Pickup:</strong> {{ $parcel->pickup_type === 'home_pickup' ? 'Home Pickup' : 'Drop Off' }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="box box-default">
                        <div class="box-header with-border bg-green"><h3 class="box-title"><i class="fa fa-user-check"></i> Receiver</h3></div>
                        <div class="box-body">
                            <p><strong>Name:</strong> {{ $parcel->receiver_name }}</p>
                            <p><strong>Phone:</strong> <a href="tel:{{ $parcel->receiver_phone }}">{{ $parcel->receiver_phone }}</a></p>
                            <p><strong>ID:</strong> {{ $parcel->receiver_id_number ?? '—' }}</p>
                            <p><strong>Town:</strong> {{ $parcel->to_town }}</p>
                            <p><strong>Address:</strong> {{ $parcel->receiver_address ?? '—' }}</p>
                            <p><strong>Delivery:</strong> {{ $parcel->delivery_type === 'home_delivery' ? 'Home Delivery' : 'Depot Pickup' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Parcel Details -->
            <div class="box box-default">
                <div class="box-header with-border"><h3 class="box-title"><i class="fa fa-box-open"></i> Parcel Details</h3></div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-sm-4"><p><strong>Type:</strong> {{ ucfirst($parcel->parcel_type) }}</p></div>
                        <div class="col-sm-4"><p><strong>Weight:</strong> {{ $parcel->weight_kg }} kg</p></div>
                        <div class="col-sm-4"><p><strong>Pieces:</strong> {{ $parcel->pieces }}</p></div>
                        <div class="col-sm-4"><p><strong>Dimensions:</strong> {{ $parcel->dimensions ?? '—' }}</p></div>
                        <div class="col-sm-4"><p><strong>Service:</strong> {{ ucfirst($parcel->service_type) }}</p></div>
                        <div class="col-sm-4"><p><strong>Declared Value:</strong> KES {{ number_format($parcel->declared_value, 2) }}</p></div>
                        <div class="col-sm-12"><p><strong>Description:</strong> {{ $parcel->parcel_description ?? '—' }}</p></div>
                        @if($parcel->special_instructions)
                        <div class="col-sm-12"><p><strong>Special Instructions:</strong> {{ $parcel->special_instructions }}</p></div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Tracking Timeline -->
            <div class="box box-primary">
                <div class="box-header with-border"><h3 class="box-title"><i class="fa fa-map-marked-alt"></i> Tracking History</h3></div>
                <div class="box-body">
                    @if($parcel->checkpoints->count())
                    <ul class="timeline">
                        @foreach($parcel->checkpoints as $cp)
                        <li>
                            <i class="fa fa-map-marker bg-blue"></i>
                            <div class="timeline-item">
                                <span class="time"><i class="fa fa-clock"></i> {{ $cp->created_at->format('d M Y H:i') }}</span>
                                <h3 class="timeline-header">{{ \App\ParcelCheckpoint::typeLabels()[$cp->checkpoint_type] ?? $cp->checkpoint_type }}
                                    <small>@ {{ $cp->location }}</small>
                                </h3>
                                @if($cp->status_note)
                                <div class="timeline-body">{{ $cp->status_note }}</div>
                                @endif
                                @if($cp->scannedByUser)
                                <div class="timeline-footer"><small>By {{ $cp->scannedByUser->first_name }}</small></div>
                                @endif
                            </div>
                        </li>
                        @endforeach
                    </ul>
                    @else
                    <p class="text-muted">No tracking events yet.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- RIGHT: Summary + Actions -->
        <div class="col-md-4">
            <div class="box box-success">
                <div class="box-header with-border"><h3 class="box-title">Charges</h3></div>
                <div class="box-body">
                    <table class="table table-condensed">
                        <tr><td>Freight:</td><td class="text-right">KES {{ number_format($parcel->freight_charge, 2) }}</td></tr>
                        <tr><td>Insurance:</td><td class="text-right">KES {{ number_format($parcel->insurance_charge, 2) }}</td></tr>
                        @if($parcel->pickup_charge > 0)
                        <tr><td>Pickup:</td><td class="text-right">KES {{ number_format($parcel->pickup_charge, 2) }}</td></tr>
                        @endif
                        @if($parcel->delivery_charge > 0)
                        <tr><td>Delivery:</td><td class="text-right">KES {{ number_format($parcel->delivery_charge, 2) }}</td></tr>
                        @endif
                        <tr class="bg-light-blue"><td><strong>Total:</strong></td><td class="text-right"><strong>KES {{ number_format($parcel->total_amount, 2) }}</strong></td></tr>
                        <tr><td>Paid:</td><td class="text-right">KES {{ number_format($parcel->paid_amount, 2) }}</td></tr>
                        <tr class="{{ ($parcel->total_amount - $parcel->paid_amount) > 0 ? 'bg-yellow' : 'bg-green' }}">
                            <td><strong>Balance:</strong></td>
                            <td class="text-right"><strong>KES {{ number_format($parcel->total_amount - $parcel->paid_amount, 2) }}</strong></td>
                        </tr>
                    </table>
                    <p><strong>Payment Method:</strong> {{ ucfirst($parcel->payment_method) }} <small>({{ ucfirst($parcel->payment_by) }})</small></p>
                    @if($parcel->mpesa_code)<p><strong>M-Pesa:</strong> {{ $parcel->mpesa_code }}</p>@endif
                </div>
            </div>

            <div class="box box-default">
                <div class="box-header with-border"><h3 class="box-title">Route Info</h3></div>
                <div class="box-body">
                    <p><i class="fa fa-map-marker-alt text-danger"></i> <strong>{{ $parcel->from_town }}</strong></p>
                    <p class="text-center"><i class="fa fa-arrow-down"></i></p>
                    <p><i class="fa fa-map-marker-alt text-success"></i> <strong>{{ $parcel->to_town }}</strong></p>
                    @if($parcel->expected_delivery_date)
                    <p><strong>Expected Delivery:</strong> {{ $parcel->expected_delivery_date->format('d M Y') }}</p>
                    @endif
                    @if($parcel->actual_delivery_date)
                    <p><strong>Delivered On:</strong> {{ $parcel->actual_delivery_date->format('d M Y H:i') }}</p>
                    <p><strong>Received By:</strong> {{ $parcel->delivered_to }}</p>
                    @endif
                    @if($parcel->vehicle_reg)
                    <p><strong>Vehicle:</strong> {{ $parcel->vehicle_reg }}</p>
                    @endif
                    <p class="text-muted text-sm">Booked by {{ optional($parcel->creator)->first_name }} on {{ $parcel->created_at->format('d M Y H:i') }}</p>
                </div>
            </div>

            <div class="box box-default">
                <div class="box-body tw-flex tw-flex-col tw-gap-2">
                    <a href="{{ route('parcels.waybill', $parcel->id) }}" target="_blank" class="btn btn-default btn-block"><i class="fa fa-print"></i> Print Waybill</a>
                    <a href="{{ route('parcels.edit', $parcel->id) }}" class="btn btn-primary btn-block"><i class="fa fa-edit"></i> Edit Parcel</a>
                    <a href="{{ route('parcels.index') }}" class="btn btn-default btn-block"><i class="fa fa-arrow-left"></i> Back to List</a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
