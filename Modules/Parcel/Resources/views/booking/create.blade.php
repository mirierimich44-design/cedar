@extends('layouts.app')

@section('title', 'Book Parcel')

@section('content')
<section class="content-header">
    <h1>Book New Parcel</h1>
</section>

<section class="content">
    <div class="box box-primary">
        {!! Form::open(['url' => action([\Modules\Parcel\Http\Controllers\BookingController::class, 'store']), 'method' => 'post', 'id' => 'parcel_booking_form']) !!}
        <div class="box-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('sender_name', 'Sender Name:*') !!}
                        {!! Form::text('sender_name', null, ['class' => 'form-control', 'required', 'placeholder' => 'Enter sender name']) !!}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('sender_phone', 'Sender Phone (M-Pesa):*') !!}
                        {!! Form::text('sender_phone', null, ['class' => 'form-control', 'required', 'placeholder' => 'e.g. 0712345678']) !!}
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('recipient_name', 'Recipient Name:*') !!}
                        {!! Form::text('recipient_name', null, ['class' => 'form-control', 'required', 'placeholder' => 'Enter recipient name']) !!}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('recipient_phone', 'Recipient Phone:*') !!}
                        {!! Form::text('recipient_phone', null, ['class' => 'form-control', 'required', 'placeholder' => 'e.g. 0712345678']) !!}
                    </div>
                </div>
            </div>

            <hr>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('origin_station_id', 'Origin Station:*') !!}
                        {!! Form::select('origin_station_id', $stations, null, ['class' => 'form-control select2', 'required', 'placeholder' => 'Select origin']) !!}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('destination_station_id', 'Destination Station:*') !!}
                        {!! Form::select('destination_station_id', $stations, null, ['class' => 'form-control select2', 'required', 'placeholder' => 'Select destination']) !!}
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('weight_kg', 'Weight (kg):*') !!}
                        {!! Form::number('weight_kg', null, ['class' => 'form-control', 'required', 'step' => '0.1', 'min' => '0.1']) !!}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('payment_method', 'Payment Method:*') !!}
                        {!! Form::select('payment_method', ['cash' => 'Cash', 'mpesa' => 'M-Pesa STK Push', 'cod' => 'Cash on Delivery (COD)'], 'cash', ['class' => 'form-control', 'required']) !!}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('declared_value', 'Declared Value (KES):') !!}
                        {!! Form::number('declared_value', 0, ['class' => 'form-control', 'step' => '0.01']) !!}
                    </div>
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('description', 'Parcel Description:') !!}
                {!! Form::textarea('description', null, ['class' => 'form-control', 'rows' => 3]) !!}
            </div>
        </div>

        <div class="box-footer">
            <button type="submit" class="btn btn-primary pull-right">Book Parcel</button>
        </div>
        {!! Form::close() !!}
    </div>
</section>
@endsection
