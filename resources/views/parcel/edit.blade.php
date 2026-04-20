@extends('layouts.app')
@section('title', 'Edit Parcel - ' . $parcel->waybill_number)

@section('content')
<section class="content-header">
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">Edit Parcel: {{ $parcel->waybill_number }}</h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('parcels.index') }}">Parcels</a></li>
        <li><a href="{{ route('parcels.show', $parcel->id) }}">{{ $parcel->waybill_number }}</a></li>
        <li class="active">Edit</li>
    </ol>
</section>

<section class="content">
{!! Form::model($parcel, ['route' => ['parcels.update', $parcel->id], 'method' => 'PUT', 'id' => 'parcel_form']) !!}

<div class="row">
    <div class="col-md-8">
        <div class="box box-primary">
            <div class="box-header with-border"><h3 class="box-title">Route</h3></div>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>From Town</label>
                            {!! Form::select('from_town', array_combine($kenyan_towns, $kenyan_towns), $parcel->from_town, ['class' => 'form-control select2']) !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>To Town</label>
                            {!! Form::select('to_town', array_combine($kenyan_towns, $kenyan_towns), $parcel->to_town, ['class' => 'form-control select2']) !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Service Type</label>
                            {!! Form::select('service_type', ['standard' => 'Standard', 'express' => 'Express', 'overnight' => 'Overnight'], $parcel->service_type, ['class' => 'form-control select2']) !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Status</label>
                            {!! Form::select('status', \App\Parcel::statusList(), $parcel->status, ['class' => 'form-control select2']) !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="box box-default">
                    <div class="box-header with-border"><h3 class="box-title">Sender</h3></div>
                    <div class="box-body">
                        <div class="form-group"><label>Name</label>{!! Form::text('sender_name', $parcel->sender_name, ['class' => 'form-control', 'required']) !!}</div>
                        <div class="form-group"><label>Phone</label>{!! Form::text('sender_phone', $parcel->sender_phone, ['class' => 'form-control', 'required']) !!}</div>
                        <div class="form-group"><label>ID No.</label>{!! Form::text('sender_id_number', $parcel->sender_id_number, ['class' => 'form-control']) !!}</div>
                        <div class="form-group"><label>Pickup</label>
                            {!! Form::select('pickup_type', ['drop_off' => 'Drop Off', 'home_pickup' => 'Home Pickup (+200)'], $parcel->pickup_type, ['class' => 'form-control select2']) !!}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="box box-default">
                    <div class="box-header with-border"><h3 class="box-title">Receiver</h3></div>
                    <div class="box-body">
                        <div class="form-group"><label>Name</label>{!! Form::text('receiver_name', $parcel->receiver_name, ['class' => 'form-control', 'required']) !!}</div>
                        <div class="form-group"><label>Phone</label>{!! Form::text('receiver_phone', $parcel->receiver_phone, ['class' => 'form-control', 'required']) !!}</div>
                        <div class="form-group"><label>ID No.</label>{!! Form::text('receiver_id_number', $parcel->receiver_id_number, ['class' => 'form-control']) !!}</div>
                        <div class="form-group"><label>Address</label>{!! Form::text('receiver_address', $parcel->receiver_address, ['class' => 'form-control']) !!}</div>
                        <div class="form-group"><label>Delivery</label>
                            {!! Form::select('delivery_type', ['depot_pickup' => 'Depot Pickup', 'home_delivery' => 'Home Delivery (+200)'], $parcel->delivery_type, ['class' => 'form-control select2']) !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="box box-default">
            <div class="box-header with-border"><h3 class="box-title">Parcel Details</h3></div>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group"><label>Type</label>
                            {!! Form::select('parcel_type', $parcel_types, $parcel->parcel_type, ['class' => 'form-control select2']) !!}
                        </div>
                    </div>
                    <div class="col-md-3"><div class="form-group"><label>Weight (kg)</label>{!! Form::number('weight_kg', $parcel->weight_kg, ['class' => 'form-control', 'step' => '0.001']) !!}</div></div>
                    <div class="col-md-3"><div class="form-group"><label>Pieces</label>{!! Form::number('pieces', $parcel->pieces, ['class' => 'form-control']) !!}</div></div>
                    <div class="col-md-3"><div class="form-group"><label>Declared Value</label>{!! Form::number('declared_value', $parcel->declared_value, ['class' => 'form-control']) !!}</div></div>
                    <div class="col-md-6"><div class="form-group"><label>Description</label>{!! Form::text('parcel_description', $parcel->parcel_description, ['class' => 'form-control']) !!}</div></div>
                    <div class="col-md-6"><div class="form-group"><label>Dimensions</label>{!! Form::text('dimensions', $parcel->dimensions, ['class' => 'form-control']) !!}</div></div>
                    <div class="col-md-12"><div class="form-group"><label>Special Instructions</label>{!! Form::textarea('special_instructions', $parcel->special_instructions, ['class' => 'form-control', 'rows' => 2]) !!}</div></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="box box-success">
            <div class="box-header with-border"><h3 class="box-title">Charges & Payment</h3></div>
            <div class="box-body">
                <div class="form-group"><label>Freight (KES)</label>{!! Form::number('freight_charge', $parcel->freight_charge, ['class' => 'form-control', 'step' => '0.01']) !!}</div>
                <div class="form-group"><label>Insurance (KES)</label>{!! Form::number('insurance_charge', $parcel->insurance_charge, ['class' => 'form-control', 'step' => '0.01']) !!}</div>
                <div class="form-group"><label>Pickup Charge</label>{!! Form::number('pickup_charge', $parcel->pickup_charge, ['class' => 'form-control', 'step' => '0.01']) !!}</div>
                <div class="form-group"><label>Delivery Charge</label>{!! Form::number('delivery_charge', $parcel->delivery_charge, ['class' => 'form-control', 'step' => '0.01']) !!}</div>
                <div class="form-group"><label>Payment Method</label>
                    {!! Form::select('payment_method', ['cash' => 'Cash', 'mpesa' => 'M-Pesa', 'credit' => 'Credit', 'card' => 'Card'], $parcel->payment_method, ['class' => 'form-control select2']) !!}
                </div>
                <div class="form-group"><label>M-Pesa Code</label>{!! Form::text('mpesa_code', $parcel->mpesa_code, ['class' => 'form-control']) !!}</div>
                <div class="form-group"><label>Amount Paid (KES)</label>{!! Form::number('paid_amount', $parcel->paid_amount, ['class' => 'form-control', 'step' => '0.01']) !!}</div>
                <div class="form-group"><label>Driver/Rider</label>{!! Form::text('driver_id', $parcel->driver_id, ['class' => 'form-control']) !!}</div>
                <div class="form-group"><label>Vehicle Reg</label>{!! Form::text('vehicle_reg', $parcel->vehicle_reg, ['class' => 'form-control']) !!}</div>
                <div class="form-group"><label>Notes</label>{!! Form::textarea('notes', $parcel->notes, ['class' => 'form-control', 'rows' => 2]) !!}</div>
                <button type="submit" class="btn btn-primary btn-block btn-lg"><i class="fa fa-save"></i> Update Parcel</button>
                <a href="{{ route('parcels.show', $parcel->id) }}" class="btn btn-default btn-block">Cancel</a>
            </div>
        </div>
    </div>
</div>
{!! Form::close() !!}
</section>
@endsection
