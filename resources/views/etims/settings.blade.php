@extends('layouts.app')
@section('title', 'eTIMS Settings')

@section('content')

<section class="content-header">
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">eTIMS Settings</h1>
</section>

<section class="content">
    @if(session('status'))
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            {{ session('status') }}
        </div>
    @endif

    {!! Form::open(['route' => 'etims.settings.save', 'method' => 'post']) !!}
    @component('components.widget', ['class' => 'box-primary'])
        @slot('title')
            <i class="fa fa-cog"></i> Digitax / eTIMS Configuration
        @endslot

        <div class="row">
            <div class="col-sm-12" style="margin-bottom: 15px;">
                <div class="form-group">
                    <label>
                        {!! Form::hidden('etims_enabled', 0) !!}
                        {!! Form::checkbox('etims_enabled', 1, $business->etims_enabled, ['id' => 'etims_enabled', 'class' => 'input-icheck']) !!}
                        <strong>Enable eTIMS Integration</strong>
                    </label>
                    <p class="help-block">When disabled, no sales or purchases will be synced to eTIMS regardless of other settings.</p>
                </div>
            </div>
        </div>

        <div id="etims_fields" style="{{ $business->etims_enabled ? '' : 'display:none;' }}">
        <div class="row">
            <div class="col-sm-6">
                <div class="form-group {{ $errors->has('digitax_api_key') ? 'has-error' : '' }}">
                    {!! Form::label('digitax_api_key', 'Digitax API Key:') !!}
                    {!! Form::text('digitax_api_key', $business->digitax_api_key, ['class' => 'form-control', 'placeholder' => 'Enter your Digitax API Key']) !!}
                    <p class="help-block">Obtain from your <strong>Digitax dashboard</strong> at digitax.tech.</p>
                    @if($errors->has('digitax_api_key'))
                        <span class="help-block text-danger">{{ $errors->first('digitax_api_key') }}</span>
                    @endif
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group {{ $errors->has('etims_tpin') ? 'has-error' : '' }}">
                    {!! Form::label('etims_tpin', 'KRA PIN (eTIMS TPIN):') !!}
                    {!! Form::text('etims_tpin', $business->etims_tpin, ['class' => 'form-control', 'placeholder' => 'e.g. A123456789Z']) !!}
                    <p class="help-block">Your KRA PIN as registered with eTIMS.</p>
                    @if($errors->has('etims_tpin'))
                        <span class="help-block text-danger">{{ $errors->first('etims_tpin') }}</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-6">
                <div class="form-group {{ $errors->has('etims_sync_mode') ? 'has-error' : '' }}">
                    {!! Form::label('etims_sync_mode', 'Invoice Sync Mode:') !!}
                    {!! Form::select('etims_sync_mode', [
                        'realtime'   => 'Real-time (Immediate) — sync on every sale',
                        'background' => 'Background (Recommended) — queue-based sync',
                        'manual'     => 'Manual — sync invoices from the eTIMS Invoices page',
                    ], $business->etims_sync_mode ?? 'background', ['class' => 'form-control select2', 'style' => 'width:100%;']) !!}
                    <p class="help-block">Controls when sales invoices are pushed to eTIMS via Digitax.</p>
                    @if($errors->has('etims_sync_mode'))
                        <span class="help-block text-danger">{{ $errors->first('etims_sync_mode') }}</span>
                    @endif
                </div>
            </div>
        </div>
        </div>{{-- close #etims_fields --}}
    @endcomponent

    @component('components.widget', ['class' => 'box-info'])
        @slot('title')
            <i class="fa fa-info-circle"></i> How It Works
        @endslot
        <ul class="list-unstyled" style="line-height: 2;">
            <li><i class="fa fa-check text-green"></i> <strong>Real-time</strong>: Invoice is pushed to eTIMS immediately when a sale is finalised. Best for small volumes.</li>
            <li><i class="fa fa-check text-green"></i> <strong>Background</strong>: Invoice is queued and synced in the background. Recommended — won't slow down checkout.</li>
            <li><i class="fa fa-check text-green"></i> <strong>Manual</strong>: No automatic sync. Use the <a href="{{ action([\App\Http\Controllers\EtimsReportController::class, 'index']) }}">eTIMS Invoices</a> page to sync invoices individually.</li>
        </ul>
    @endcomponent

    <div class="row">
        <div class="col-sm-12 text-center" style="margin-bottom: 20px;">
            <button class="tw-dw-btn tw-dw-btn-error tw-dw-btn-lg tw-text-white" type="submit">
                <i class="fa fa-save"></i> Save eTIMS Settings
            </button>
            &nbsp;
            <a href="{{ action([\App\Http\Controllers\EtimsReportController::class, 'index']) }}" class="tw-dw-btn tw-dw-btn-lg tw-dw-btn-outline">
                <i class="fa fa-file-text-o"></i> View eTIMS Invoices
            </a>
        </div>
    </div>
    {!! Form::close() !!}
</section>

@stop
@section('javascript')
<script>
    $(document).ready(function () {
        $('.select2').select2();

        $(document).on('ifChecked ifUnchecked', '#etims_enabled', function (event) {
            if (event.type === 'ifChecked') {
                $('#etims_fields').slideDown();
            } else {
                $('#etims_fields').slideUp();
            }
        });
    });
</script>
@endsection
