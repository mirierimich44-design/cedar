@extends('layouts.app')
@section('title', 'KCB Buni Settings')

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>KCB Buni Settings</h1>
</section>

<!-- Main content -->
<section class="content">
    {!! Form::open(['url' => action([\Modules\KcbBuni\Http\Controllers\KcbBuniGatewayController::class, 'saveSettings']), 'method' => 'post', 'id' => 'kcb_buni_settings_form' ]) !!}
    <div class="box box-solid">
        <div class="box-body">
            <div class="row">
                <div class="col-sm-4">
                    <div class="form-group">
                        {!! Form::label('app_key', 'App Key' . ':') !!}
                        {!! Form::password('app_key', ['class' => 'form-control', 'placeholder' => 'App Key']); !!}
                        <p class="help-block">Enter your KCB Buni Client ID / App Key</p>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        {!! Form::label('app_secret', 'App Secret' . ':') !!}
                        {!! Form::password('app_secret', ['class' => 'form-control', 'placeholder' => 'App Secret']); !!}
                        <p class="help-block">Enter your KCB Buni Client Secret / App Secret</p>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        {!! Form::label('environment', 'Environment' . ':') !!}
                        {!! Form::select('environment', ['sandbox' => 'Sandbox', 'production' => 'Production'], $settings->environment ?? 'sandbox', ['class' => 'form-control']); !!}
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="col-sm-4">
                    <div class="form-group">
                        {!! Form::label('b2b_shortcode', 'B2B Shortcode' . ':') !!}
                        {!! Form::text('b2b_shortcode', $settings->b2b_shortcode ?? null, ['class' => 'form-control', 'placeholder' => 'B2B Shortcode']); !!}
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        {!! Form::label('b2c_shortcode', 'B2C Shortcode' . ':') !!}
                        {!! Form::text('b2c_shortcode', $settings->b2c_shortcode ?? null, ['class' => 'form-control', 'placeholder' => 'B2C Shortcode']); !!}
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <br>
                        <label>
                          {!! Form::checkbox('is_active', 1, $settings->is_active ?? false, ['class' => 'input-icheck']); !!} Enable KCB Buni
                        </label>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-sm-12">
                    <button type="submit" class="btn btn-primary pull-right">Save Settings</button>
                    <button type="button" id="test_kcb_connection" class="btn btn-warning pull-right" style="margin-right: 10px;">Test Connection</button>
                </div>
            </div>
        </div>
    </div>
    {!! Form::close() !!}
</section>
@endsection

@section('javascript')
<script type="text/javascript">
    $(document).ready(function(){
        $(document).on('submit', 'form#kcb_buni_settings_form', function(e){
            e.preventDefault();
            var data = $(this).serialize();
            $.ajax({
                method: "POST",
                url: $(this).attr("action"),
                dataType: "json",
                data: data,
                success: function(result){
                    if(result.success == true){
                        toastr.success(result.message);
                    } else {
                        toastr.error(result.message);
                    }
                }
            });
        });

        $(document).on('click', '#test_kcb_connection', function(){
            var btn = $(this);
            btn.attr('disabled', true).text('Testing...');
            $.ajax({
                method: "POST",
                url: "{{ action([\Modules\KcbBuni\Http\Controllers\KcbBuniGatewayController::class, 'testConnection']) }}",
                dataType: "json",
                success: function(result){
                    btn.attr('disabled', false).text('Test Connection');
                    if(result.success == true){
                        toastr.success(result.message);
                    } else {
                        toastr.error(result.message);
                    }
                }
            });
        });
    });
</script>
@endsection
