@extends('layouts.app')
@section('title', 'Pesapal Settings')

@section('content')
<section class="content-header">
    <h1>Pesapal Settings <small>Card & mobile-money gateway (v3 API)</small></h1>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-8">
            <div class="box box-solid">
                <div class="box-header with-border"><h3 class="box-title">Credentials</h3></div>
                <div class="box-body">
                    <div id="pesapal-alert" class="alert" style="display:none;"></div>

                    <form id="pesapal-settings-form">
                        @csrf
                        <div class="form-group">
                            <label>Environment</label>
                            <select name="environment" class="form-control" required>
                                <option value="sandbox"    @if(optional($settings)->environment === 'sandbox')    selected @endif>Sandbox (testing)</option>
                                <option value="production" @if(optional($settings)->environment === 'production') selected @endif>Production (live)</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Consumer Key</label>
                            <input type="text" name="consumer_key" class="form-control"
                                   placeholder="{{ optional($settings)->consumer_key ? '•••••• (leave blank to keep existing)' : 'Enter your Pesapal consumer key' }}">
                        </div>
                        <div class="form-group">
                            <label>Consumer Secret</label>
                            <input type="text" name="consumer_secret" class="form-control"
                                   placeholder="{{ optional($settings)->consumer_secret ? '•••••• (leave blank to keep existing)' : 'Enter your Pesapal consumer secret' }}">
                        </div>
                        <div class="form-group">
                            <label>Currency</label>
                            <input type="text" name="currency" class="form-control" value="{{ optional($settings)->currency ?? 'KES' }}" maxlength="10">
                        </div>
                        <div class="form-group">
                            <label>
                                <input type="checkbox" name="is_active" value="1" @if(optional($settings)->is_active) checked @endif>
                                Enable Pesapal gateway
                            </label>
                        </div>
                        <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Save Settings</button>
                        <button type="button" id="pesapal-test-btn" class="btn btn-info"><i class="fa fa-plug"></i> Test Connection</button>
                        <button type="button" id="pesapal-register-ipn-btn" class="btn btn-warning"><i class="fa fa-bell"></i> Register IPN URL</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="box box-solid">
                <div class="box-header with-border"><h3 class="box-title">Status</h3></div>
                <div class="box-body">
                    <p><strong>Configured:</strong>
                        @if(optional($settings)->isConfigured())
                            <span class="label label-success">Yes</span>
                        @else
                            <span class="label label-danger">No — save keys and register IPN</span>
                        @endif
                    </p>
                    <p><strong>IPN ID:</strong> {{ optional($settings)->ipn_id ?? '—' }}</p>
                    <p><strong>IPN URL:</strong> <code>{{ url('/pesapal/ipn') }}</code></p>
                    <p><strong>Callback URL:</strong> <code>{{ url('/pesapal/callback') }}</code></p>
                    <p><strong>Last tested:</strong> {{ optional(optional($settings)->last_tested_at)->format('Y-m-d H:i') ?? 'Never' }}</p>
                    <hr>
                    <p class="text-muted small">Steps: (1) Save credentials, (2) Test connection, (3) Register IPN URL with Pesapal.</p>
                </div>
            </div>
        </div>
    </div>
</section>

@section('javascript')
<script>
$(function(){
    function alertBox(ok, msg) {
        $('#pesapal-alert').removeClass('alert-success alert-danger')
            .addClass(ok ? 'alert-success' : 'alert-danger')
            .html(msg).show();
    }

    $('#pesapal-settings-form').on('submit', function(e){
        e.preventDefault();
        $.post("{{ route('pesapal.settings.save') }}", $(this).serialize(), function(res){
            alertBox(res.success, res.message);
        }, 'json').fail(function(x){ alertBox(false, x.responseJSON?.message || 'Request failed'); });
    });

    $('#pesapal-test-btn').on('click', function(){
        $.post("{{ route('pesapal.test') }}", {_token: "{{ csrf_token() }}"}, function(res){
            alertBox(res.success, res.message);
        }, 'json');
    });

    $('#pesapal-register-ipn-btn').on('click', function(){
        $.post("{{ route('pesapal.register-ipn') }}", {_token: "{{ csrf_token() }}"}, function(res){
            alertBox(res.success, (res.message || '') + (res.ipn_id ? ' (IPN ID: ' + res.ipn_id + ')' : ''));
            if (res.success) setTimeout(() => location.reload(), 1200);
        }, 'json');
    });
});
</script>
@endsection
@endsection
