@extends('layouts.app')
@section('title', __('New Stocktake'))

@section('content')
<section class="content-header">
    <h1>@lang('New Stocktake')</h1>
</section>

<section class="content">
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">@lang('Start Physical Inventory Count')</h3>
        </div>
        {!! Form::open(['route' => 'stocktake.store', 'method' => 'post']) !!}
        <div class="box-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('location_id', __('business.location') . ':*') !!}
                        {!! Form::select('location_id', $locations, null, [
                            'class' => 'form-control select2',
                            'required',
                            'placeholder' => __('messages.please_select')
                        ]) !!}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('notes', __('Notes') . ':') !!}
                        {!! Form::textarea('notes', null, [
                            'class' => 'form-control',
                            'rows' => 3,
                            'placeholder' => __('Optional notes for this stocktake')
                        ]) !!}
                    </div>
                </div>
            </div>
        </div>
        <div class="box-footer">
            <a href="{{ action([\App\Http\Controllers\StocktakeController::class, 'index']) }}" class="btn btn-default">
                @lang('messages.cancel')
            </a>
            <button type="submit" class="btn btn-primary pull-right">
                <i class="fa fa-play"></i> @lang('Start Counting')
            </button>
        </div>
        {!! Form::close() !!}
    </div>
</section>
@endsection
