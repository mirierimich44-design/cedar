@extends('layouts.app')
@section('title', __('lang_v1.mpesa_settings'))

@section('content')

<!-- Content Header -->
<section class="content-header">
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">
        <i class="fas fa-mobile-alt"></i> @lang('lang_v1.mpesa_settings')
    </h1>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            @component('components.widget', ['class' => 'box-primary'])
                @slot('title')
                    <i class="fas fa-cog"></i> @lang('lang_v1.mpesa_configuration')
                @endslot

                {!! Form::open(['url' => route('mpesa.settings.save'), 'method' => 'post', 'id' => 'mpesa_settings_form']) !!}

                <div class="row">
                    <!-- Environment Toggle -->
                    <div class="col-md-12">
                        <div class="form-group">
                            {!! Form::label('environment', __('lang_v1.mpesa_environment') . ':') !!}
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fas fa-server"></i>
                                </span>
                                {!! Form::select('environment', 
                                    ['sandbox' => __('lang_v1.mpesa_sandbox'), 'production' => __('lang_v1.mpesa_production')],
                                    $settings->environment ?? 'sandbox',
                                    ['class' => 'form-control', 'id' => 'environment']
                                ) !!}
                            </div>
                            <p class="help-block text-muted">
                                <i class="fas fa-info-circle"></i> @lang('lang_v1.mpesa_environment_help')
                            </p>
                        </div>
                    </div>

                    <!-- Consumer Key -->
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('consumer_key', __('lang_v1.mpesa_consumer_key') . ':') !!}
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fas fa-key"></i>
                                </span>
                                {!! Form::password('consumer_key', [
                                    'class' => 'form-control',
                                    'id' => 'consumer_key',
                                    'placeholder' => $settings && $settings->consumer_key ? '********' : __('lang_v1.enter_consumer_key'),
                                    'autocomplete' => 'off'
                                ]) !!}
                            </div>
                        </div>
                    </div>

                    <!-- Consumer Secret -->
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('consumer_secret', __('lang_v1.mpesa_consumer_secret') . ':') !!}
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fas fa-lock"></i>
                                </span>
                                {!! Form::password('consumer_secret', [
                                    'class' => 'form-control',
                                    'id' => 'consumer_secret',
                                    'placeholder' => $settings && $settings->consumer_secret ? '********' : __('lang_v1.enter_consumer_secret'),
                                    'autocomplete' => 'off'
                                ]) !!}
                            </div>
                        </div>
                    </div>

                    <!-- Passkey -->
                    <div class="col-md-12">
                        <div class="form-group">
                            {!! Form::label('passkey', __('lang_v1.mpesa_passkey') . ':') !!}
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fas fa-fingerprint"></i>
                                </span>
                                {!! Form::password('passkey', [
                                    'class' => 'form-control',
                                    'id' => 'passkey',
                                    'placeholder' => $settings && $settings->passkey ? '********' : __('lang_v1.enter_passkey'),
                                    'autocomplete' => 'off'
                                ]) !!}
                            </div>
                            <p class="help-block text-muted">
                                <i class="fas fa-info-circle"></i> @lang('lang_v1.mpesa_passkey_help')
                            </p>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <hr>
                        <h4><i class="fas fa-university"></i> B2C & Balance Query Settings</h4>
                        <p class="help-block text-muted">Required for checking account balance and paying expenses.</p>
                    </div>

                    <!-- Initiator Name -->
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('initiator_name', 'Initiator Name:') !!}
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fas fa-user-tag"></i>
                                </span>
                                {!! Form::text('initiator_name', $settings->initiator_name ?? '', [
                                    'class' => 'form-control',
                                    'id' => 'initiator_name',
                                    'placeholder' => 'Enter Initiator Name'
                                ]) !!}
                            </div>
                        </div>
                    </div>

                    <!-- Security Credential -->
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('security_credential', 'Security Credential:') !!}
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fas fa-shield-alt"></i>
                                </span>
                                {!! Form::password('security_credential', [
                                    'class' => 'form-control',
                                    'id' => 'security_credential',
                                    'placeholder' => $settings && $settings->security_credential ? '********' : 'Enter Security Credential',
                                    'autocomplete' => 'off'
                                ]) !!}
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <hr>
                        <h4><i class="fas fa-building"></i> @lang('lang_v1.mpesa_business_details')</h4>
                    </div>

                    <!-- Shortcode Type -->
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('shortcode_type', __('lang_v1.mpesa_shortcode_type') . ':') !!}
                            {!! Form::select('shortcode_type',
                                ['paybill' => __('lang_v1.mpesa_paybill'), 'till' => __('lang_v1.mpesa_till_number')],
                                $settings->shortcode_type ?? 'paybill',
                                ['class' => 'form-control', 'id' => 'shortcode_type']
                            ) !!}
                        </div>
                    </div>

                    <!-- Business Shortcode -->
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('shortcode', __('lang_v1.mpesa_business_shortcode') . ':') !!}
                            {!! Form::text('shortcode', $settings->shortcode ?? '', [
                                'class' => 'form-control',
                                'id' => 'shortcode',
                                'placeholder' => 'e.g., 174379'
                            ]) !!}
                            <p class="help-block text-muted">@lang('lang_v1.mpesa_shortcode_help')</p>
                        </div>
                    </div>

                    <!-- Till Number (conditional) -->
                    <div class="col-md-4" id="till_number_div" style="{{ ($settings->shortcode_type ?? 'paybill') === 'till' ? '' : 'display:none;' }}">
                        <div class="form-group">
                            {!! Form::label('till_number', __('lang_v1.mpesa_till_number') . ':') !!}
                            {!! Form::text('till_number', $settings->till_number ?? '', [
                                'class' => 'form-control',
                                'id' => 'till_number',
                                'placeholder' => 'e.g., 123456'
                            ]) !!}
                        </div>
                    </div>

                    <!-- Default Account Number -->
                    <div class="col-md-4" id="account_number_div" style="{{ ($settings->shortcode_type ?? 'paybill') === 'paybill' ? '' : 'display:none;' }}">
                        <div class="form-group">
                            {!! Form::label('account_number', __('lang_v1.mpesa_account_number') . ':') !!}
                            {!! Form::text('account_number', $settings->account_number ?? '', [
                                'class' => 'form-control',
                                'id' => 'account_number',
                                'placeholder' => __('lang_v1.mpesa_default_account')
                            ]) !!}
                            <p class="help-block text-muted">@lang('lang_v1.mpesa_account_help')</p>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <hr>
                    </div>

                    <!-- Active Toggle -->
                    <div class="col-md-4">
                        <div class="form-group">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('is_active', 1, $settings->is_active ?? false, ['class' => 'input-icheck', 'id' => 'is_active']) !!}
                                    <strong>@lang('lang_v1.mpesa_enable_payments')</strong>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Last Tested -->
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>@lang('lang_v1.mpesa_last_tested'):</label>
                            <p class="form-control-static" id="last_tested_display">
                                @if($settings && $settings->last_tested_at)
                                    <span class="text-success">
                                        <i class="fas fa-check-circle"></i>
                                        {{ $settings->last_tested_at->format('Y-m-d H:i:s') }}
                                    </span>
                                @else
                                    <span class="text-muted">@lang('lang_v1.mpesa_never_tested')</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <!-- Test Connection Button -->
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <button type="button" id="test_connection_btn" class="btn btn-info btn-block">
                                <i class="fas fa-plug"></i> @lang('lang_v1.mpesa_test_connection')
                            </button>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <hr>
                    </div>

                    <!-- Callback URLs (Read-only) -->
                    <div class="col-md-12">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> <strong>@lang('lang_v1.mpesa_webhook_urls'):</strong>
                            <p class="tw-mt-2 tw-text-sm">@lang('lang_v1.mpesa_webhook_info')</p>
                            <ul class="tw-mt-2 tw-text-sm">
                                <li><strong>Callback URL:</strong> <code>{{ url('/mobile-money/webhook/callback') }}</code></li>
                                <li><strong>Validation URL:</strong> <code>{{ url('/mobile-money/webhook/validation') }}</code></li>
                                <li><strong>Confirmation URL:</strong> <code>{{ url('/mobile-money/webhook/confirmation') }}</code></li>
                            </ul>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="col-md-12">
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary btn-lg" id="save_mpesa_settings">
                                <i class="fas fa-save"></i> @lang('messages.save')
                            </button>

                            <a href="{{ route('mpesa.transactions') }}" class="btn btn-default btn-lg">
                                <i class="fas fa-history"></i> @lang('lang_v1.mpesa_view_transactions')
                            </a>

                            <button type="button" id="register_c2b_urls_btn" class="btn btn-warning btn-lg">
                                <i class="fas fa-link"></i> @lang('lang_v1.mpesa_register_c2b_urls')
                            </button>
                        </div>
                    </div>
                </div>

                {!! Form::close() !!}
            @endcomponent
        </div>
    </div>
</section>
@endsection

@section('javascript')
<script type="text/javascript">
$(document).ready(function() {
    // Toggle till/account number fields
    $('#shortcode_type').change(function() {
        if ($(this).val() === 'till') {
            $('#till_number_div').show();
            $('#account_number_div').hide();
        } else {
            $('#till_number_div').hide();
            $('#account_number_div').show();
        }
    });

    // Save settings
    $('#mpesa_settings_form').submit(function(e) {
        e.preventDefault();
        
        var btn = $('#save_mpesa_settings');
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');

        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                } else {
                    toastr.error(response.message);
                }
            },
            error: function(xhr) {
                toastr.error('An error occurred. Please try again.');
            },
            complete: function() {
                btn.prop('disabled', false).html('<i class="fas fa-save"></i> @lang("messages.save")');
            }
        });
    });

    // Test connection
    $('#test_connection_btn').click(function() {
        var btn = $(this);
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Testing...');

        $.ajax({
            url: '{{ route("mpesa.test") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    $('#last_tested_display').html(
                        '<span class="text-success"><i class="fas fa-check-circle"></i> ' + 
                        response.tested_at + '</span>'
                    );
                } else {
                    toastr.error(response.message);
                }
            },
            error: function(xhr) {
                toastr.error('Connection test failed. Please check your credentials.');
            },
            complete: function() {
                btn.prop('disabled', false).html('<i class="fas fa-plug"></i> @lang("lang_v1.mpesa_test_connection")');
            }
        });
    });

    // Register C2B URLs
    $('#register_c2b_urls_btn').click(function() {
        var btn = $(this);
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Registering...');

        $.ajax({
            url: '{{ route("mpesa.register-urls") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                } else {
                    toastr.error(response.message);
                }
            },
            error: function(xhr) {
                toastr.error('URL registration failed.');
            },
            complete: function() {
                btn.prop('disabled', false).html('<i class="fas fa-link"></i> @lang("lang_v1.mpesa_register_c2b_urls")');
            }
        });
    });
});
</script>
@endsection
