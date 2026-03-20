<div class="pos-tab-content">
    <div class="row">
        <div class="col-sm-4">
            <div class="form-group">
                {!! Form::label('digitax_api_key', 'Digitax API Key' . ':') !!}
                {!! Form::text('digitax_api_key', $business->digitax_api_key, ['class' => 'form-control', 'placeholder' => 'Digitax API Key']); !!}
                <p class="help-block">Obtain from Digitax dashboard.</p>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                {!! Form::label('etims_tpin', 'KRA PIN' . ':') !!}
                {!! Form::text('etims_tpin', $business->etims_tpin, ['class' => 'form-control', 'placeholder' => 'KRA PIN']); !!}
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                {!! Form::label('etims_sync_mode', 'eTIMS Sync Mode' . ':') !!}
                {!! Form::select('etims_sync_mode', ['realtime' => 'Real-time (Immediate)', 'background' => 'Background (Recommended)', 'manual' => 'Manual Sync'], $business->etims_sync_mode, ['class' => 'form-control select2', 'style' => 'width: 100%;']); !!}
            </div>
        </div>
    </div>
</div>
