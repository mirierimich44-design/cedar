@extends('layouts.app')
@section('title', 'Book Parcel')

@section('content')
<section class="content-header">
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">Book New Parcel</h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('parcels.index') }}">Parcels</a></li>
        <li class="active">Book Parcel</li>
    </ol>
</section>

<section class="content">
{!! Form::open(['route' => 'parcels.store', 'method' => 'POST', 'id' => 'parcel_form']) !!}

<div class="row">
    <!-- LEFT: Sender & Receiver -->
    <div class="col-md-8">

        <!-- Route Selection -->
        <div class="box box-primary">
            <div class="box-header with-border"><h3 class="box-title"><i class="fa fa-route"></i> Route & Service</h3></div>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>From Town <span class="text-danger">*</span></label>
                            {!! Form::select('from_town', array_combine($kenyan_towns, $kenyan_towns), null,
                                ['class' => 'form-control select2', 'required', 'placeholder' => 'Origin town', 'id' => 'from_town']) !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>To Town <span class="text-danger">*</span></label>
                            {!! Form::select('to_town', array_combine($kenyan_towns, $kenyan_towns), null,
                                ['class' => 'form-control select2', 'required', 'placeholder' => 'Destination', 'id' => 'to_town']) !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Saved Route (optional)</label>
                            {!! Form::select('route_id', $routes, null, ['class' => 'form-control select2', 'placeholder' => 'Select route', 'id' => 'route_id']) !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Service Type</label>
                            {!! Form::select('service_type', ['standard' => 'Standard', 'express' => 'Express', 'overnight' => 'Overnight'], null,
                                ['class' => 'form-control select2', 'id' => 'service_type']) !!}
                        </div>
                    </div>
                </div>
                <div id="route_info" class="alert alert-info tw-hidden tw-text-sm"></div>
            </div>
        </div>

        <div class="row">
            <!-- Sender -->
            <div class="col-md-6">
                <div class="box box-default">
                    <div class="box-header with-border"><h3 class="box-title"><i class="fa fa-user"></i> Sender</h3></div>
                    <div class="box-body">
                        <div class="form-group">
                            <label>Full Name <span class="text-danger">*</span></label>
                            {!! Form::text('sender_name', null, ['class' => 'form-control', 'required', 'placeholder' => 'Sender full name']) !!}
                        </div>
                        <div class="form-group">
                            <label>Phone <span class="text-danger">*</span></label>
                            {!! Form::text('sender_phone', null, ['class' => 'form-control', 'required', 'placeholder' => '07xxxxxxxx']) !!}
                        </div>
                        <div class="form-group">
                            <label>ID Number</label>
                            {!! Form::text('sender_id_number', null, ['class' => 'form-control', 'placeholder' => 'National ID / Passport']) !!}
                        </div>
                        <div class="form-group">
                            <label>Pickup Type</label>
                            {!! Form::select('pickup_type', ['drop_off' => 'Drop Off at Depot', 'home_pickup' => 'Home/Office Pickup (+KES 200)'], null,
                                ['class' => 'form-control select2', 'id' => 'pickup_type']) !!}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Receiver -->
            <div class="col-md-6">
                <div class="box box-default">
                    <div class="box-header with-border"><h3 class="box-title"><i class="fa fa-user-check"></i> Receiver</h3></div>
                    <div class="box-body">
                        <div class="form-group">
                            <label>Full Name <span class="text-danger">*</span></label>
                            {!! Form::text('receiver_name', null, ['class' => 'form-control', 'required', 'placeholder' => 'Receiver full name']) !!}
                        </div>
                        <div class="form-group">
                            <label>Phone <span class="text-danger">*</span></label>
                            {!! Form::text('receiver_phone', null, ['class' => 'form-control', 'required', 'placeholder' => '07xxxxxxxx']) !!}
                        </div>
                        <div class="form-group">
                            <label>ID Number</label>
                            {!! Form::text('receiver_id_number', null, ['class' => 'form-control', 'placeholder' => 'National ID / Passport']) !!}
                        </div>
                        <div class="form-group">
                            <label>Delivery Type</label>
                            {!! Form::select('delivery_type', ['depot_pickup' => 'Pickup at Depot', 'home_delivery' => 'Home/Office Delivery (+KES 200)'], null,
                                ['class' => 'form-control select2', 'id' => 'delivery_type']) !!}
                        </div>
                        <div class="form-group">
                            <label>Delivery Address</label>
                            {!! Form::text('receiver_address', null, ['class' => 'form-control', 'placeholder' => 'Street / Estate / Building']) !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Parcel Details -->
        <div class="box box-default">
            <div class="box-header with-border"><h3 class="box-title"><i class="fa fa-box-open"></i> Parcel Details</h3></div>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Parcel Type</label>
                            {!! Form::select('parcel_type', $parcel_types, null, ['class' => 'form-control select2']) !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Weight (kg) <span class="text-danger">*</span></label>
                            {!! Form::number('weight_kg', null, ['class' => 'form-control', 'required', 'min' => '0.001', 'step' => '0.001', 'id' => 'weight_kg', 'placeholder' => '0.000']) !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>No. of Pieces</label>
                            {!! Form::number('pieces', 1, ['class' => 'form-control', 'min' => '1']) !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Dimensions (cm)</label>
                            {!! Form::text('dimensions', null, ['class' => 'form-control', 'placeholder' => 'L x W x H']) !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Description</label>
                            {!! Form::text('parcel_description', null, ['class' => 'form-control', 'placeholder' => 'Brief content description']) !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Declared Value (KES)</label>
                            {!! Form::number('declared_value', 0, ['class' => 'form-control', 'min' => '0', 'id' => 'declared_value']) !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Expected Delivery</label>
                            {!! Form::text('expected_delivery_date', null, ['class' => 'form-control start-date-picker', 'readonly', 'placeholder' => 'Date']) !!}
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Special Instructions</label>
                            {!! Form::textarea('special_instructions', null, ['class' => 'form-control', 'rows' => 2, 'placeholder' => 'Handle with care, fragile, etc.']) !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- RIGHT: Pricing & Payment -->
    <div class="col-md-4">
        <div class="box box-success">
            <div class="box-header with-border"><h3 class="box-title"><i class="fa fa-money-bill"></i> Pricing & Payment</h3></div>
            <div class="box-body">
                <table class="table table-condensed">
                    <tr>
                        <td>Freight Charge (KES):</td>
                        <td>{!! Form::number('freight_charge', 0, ['class' => 'form-control input-sm text-right', 'id' => 'freight_charge', 'min' => '0', 'step' => '0.01']) !!}</td>
                    </tr>
                    <tr>
                        <td>Insurance (1% of value):</td>
                        <td><span id="insurance_display">0.00</span><input type="hidden" name="insurance_charge" id="insurance_charge" value="0"></td>
                    </tr>
                    <tr id="pickup_charge_row" style="display:none">
                        <td>Pickup Charge:</td>
                        <td class="text-right">KES 200.00</td>
                    </tr>
                    <tr id="delivery_charge_row" style="display:none">
                        <td>Delivery Charge:</td>
                        <td class="text-right">KES 200.00</td>
                    </tr>
                    <tr class="bg-light-blue">
                        <td><strong>Total:</strong></td>
                        <td class="text-right"><strong id="total_display">0.00</strong></td>
                    </tr>
                </table>

                <div class="form-group">
                    <label>Payment By</label>
                    {!! Form::select('payment_by', ['sender' => 'Sender Pays', 'receiver' => 'Receiver Pays', 'third_party' => 'Third Party'], null,
                        ['class' => 'form-control select2']) !!}
                </div>
                <div class="form-group">
                    <label>Payment Method</label>
                    {!! Form::select('payment_method', ['cash' => 'Cash', 'mpesa' => 'M-Pesa', 'credit' => 'Credit / Account', 'card' => 'Card'], null,
                        ['class' => 'form-control select2', 'id' => 'payment_method_field']) !!}
                </div>
                <div class="form-group" id="mpesa_code_group" style="display:none">
                    <label>M-Pesa Code</label>
                    {!! Form::text('mpesa_code', null, ['class' => 'form-control', 'placeholder' => 'e.g. QAM1234567']) !!}
                </div>
                <div class="form-group">
                    <label>Amount Paid (KES)</label>
                    {!! Form::number('paid_amount', 0, ['class' => 'form-control', 'id' => 'paid_amount', 'min' => '0', 'step' => '0.01']) !!}
                </div>
                <div class="form-group">
                    <label>Location / Branch</label>
                    {!! Form::select('location_id', $locations, null, ['class' => 'form-control select2', 'placeholder' => 'Select branch']) !!}
                </div>
                <div class="form-group">
                    <label>Notes</label>
                    {!! Form::textarea('notes', null, ['class' => 'form-control', 'rows' => 2]) !!}
                </div>
                <button type="submit" class="btn btn-primary btn-block btn-lg">
                    <i class="fa fa-save"></i> Book & Print Waybill
                </button>
                <a href="{{ route('parcels.index') }}" class="btn btn-default btn-block">Cancel</a>
            </div>
        </div>
    </div>
</div>

{!! Form::close() !!}
</section>
@endsection

@section('javascript')
<script>
function recalcTotal() {
    var freight  = parseFloat($('#freight_charge').val()) || 0;
    var insur    = parseFloat($('#insurance_charge').val()) || 0;
    var pickup   = $('#pickup_type').val() === 'home_pickup' ? 200 : 0;
    var delivery = $('#delivery_type').val() === 'home_delivery' ? 200 : 0;
    var total    = freight + insur + pickup + delivery;
    $('#total_display').text(total.toFixed(2));
    $('#pickup_charge_row').toggle(pickup > 0);
    $('#delivery_charge_row').toggle(delivery > 0);
}

$(function() {
    // Route price auto-fill
    function fetchRoutePrice() {
        var routeId = $('#route_id').val();
        var weight  = parseFloat($('#weight_kg').val()) || 0;
        var service = $('#service_type').val();
        if (routeId && weight > 0) {
            $.get('{{ route("parcel-routes.calculate-price") }}', { route_id: routeId, weight_kg: weight, service_type: service }, function(r) {
                $('#freight_charge').val(r.price.toFixed(2));
                if (r.transit_days) {
                    $('#route_info').removeClass('tw-hidden').text('Estimated transit: ' + r.transit_days + ' | Min charge: KES ' + r.min_price);
                }
                recalcTotal();
            });
        }
    }

    $('#route_id, #weight_kg, #service_type').on('change keyup', fetchRoutePrice);

    // Insurance (1% of declared value)
    $('#declared_value').on('keyup change', function() {
        var insur = (parseFloat($(this).val()) || 0) * 0.01;
        $('#insurance_charge').val(insur.toFixed(2));
        $('#insurance_display').text(insur.toFixed(2));
        recalcTotal();
    });

    $('#freight_charge').on('keyup change', recalcTotal);
    $('#pickup_type, #delivery_type').on('change', recalcTotal);

    $('#payment_method_field').on('change', function() {
        $('#mpesa_code_group').toggle($(this).val() === 'mpesa');
    });
});
</script>
@endsection
