<div class="pos-tab-content">
    <div class="row">
        <div class="col-sm-12">
            <div class="form-group">
                <label>
                    {!! Form::hidden('etims_enabled', 0) !!}
                    {!! Form::checkbox('etims_enabled', 1, $business->etims_enabled, ['id' => 'etims_enabled_biz', 'class' => 'input-icheck']) !!}
                    <strong>Enable eTIMS Integration</strong>
                </label>
                <p class="help-block">When disabled, no sales or purchases will be synced to eTIMS.</p>
            </div>
        </div>
    </div>
    <div id="etims_biz_fields" style="{{ $business->etims_enabled ? '' : 'display:none;' }}">
        <div class="row">
            <div class="col-sm-4">
                <div class="form-group">
                    {!! Form::label('digitax_api_key', 'eTIMS API Key' . ':') !!}
                    {!! Form::text('digitax_api_key', $business->digitax_api_key, ['class' => 'form-control', 'placeholder' => 'eTIMS API Key']); !!}
                    <p class="help-block">Your eTIMS integration API key.</p>
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
</div>
<script>
    $(document).on('ifChecked ifUnchecked', '#etims_enabled_biz', function (event) {
        if (event.type === 'ifChecked') {
            $('#etims_biz_fields').slideDown();
        } else {
            $('#etims_biz_fields').slideUp();
        }
    });
</script>
