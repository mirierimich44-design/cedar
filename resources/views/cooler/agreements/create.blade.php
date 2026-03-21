@extends('layouts.app')
@section('title', 'New Agreement')

@section('content')
<section class="content-header">
    <h1 class="tw-text-xl tw-font-bold tw-text-black">New Cooler Loan Agreement</h1>
</section>

<section class="content">
    <div class="box box-primary">
        <div class="box-header with-border"><h3 class="box-title">Agreement Details</h3></div>
        <div class="box-body">
            {!! Form::open(['route' => 'cooler.agreements.store', 'method' => 'POST']) !!}
            @if($errors->any())
            <div class="alert alert-danger">
                <ul class="tw-list-disc tw-pl-4">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
            @endif
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('dealer_id', 'Dealer / Outlet *') !!}
                        {!! Form::select('dealer_id', $dealers, request('dealer_id'), ['class' => 'form-control select2', 'required', 'placeholder' => '— Select dealer —']) !!}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('cooler_id', 'Cooler Asset *') !!}
                        {!! Form::select('cooler_id', $coolers, null, ['class' => 'form-control select2', 'required', 'placeholder' => '— Select available cooler —']) !!}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('agreement_date', 'Agreement Date *') !!}
                        {!! Form::date('agreement_date', now()->toDateString(), ['class' => 'form-control', 'required']) !!}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('sales_volume_target', 'Monthly Sales Target (KES)') !!}
                        {!! Form::number('sales_volume_target', null, ['class' => 'form-control', 'step' => '1000', 'min' => '0', 'placeholder' => 'e.g. 50000']) !!}
                        <small class="help-block">Clause 8 — dealer-specific sales volume target</small>
                    </div>
                </div>
            </div>
            <div class="alert alert-info">
                <i class="fa fa-info-circle"></i>
                After saving, you will be taken to the signature capture screen. All four signatures are required before the agreement becomes active.
            </div>
            <button type="submit" class="btn btn-primary">Create Agreement &amp; Capture Signatures →</button>
            <a href="{{ route('cooler.agreements.index') }}" class="btn btn-default">Cancel</a>
            {!! Form::close() !!}
        </div>
    </div>
</section>
@endsection
