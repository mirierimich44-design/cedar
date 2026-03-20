@php
    $whatsapp_enabled = isset($whatsapp_settings['enabled']) ? $whatsapp_settings['enabled'] : 0;
    $api_provider = isset($whatsapp_settings['api_provider']) ? $whatsapp_settings['api_provider'] : 'meta';
@endphp
<div class="pos-tab-content">
    <div class="row">
        <div class="col-xs-12">
            <h4 class="tw-font-bold tw-text-lg">@lang('lang_v1.whatsapp_api_settings')</h4>
            <p class="text-muted">@lang('lang_v1.whatsapp_api_description')</p>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-3">
            <div class="form-group">
                {!! Form::label('whatsapp_enabled', __('lang_v1.enable_whatsapp') . ':') !!}
                {!! Form::select('whatsapp_settings[enabled]', [0 => __('lang_v1.no'), 1 => __('lang_v1.yes')], $whatsapp_enabled, ['class' => 'form-control', 'id' => 'whatsapp_enabled']); !!}
            </div>
        </div>
        <div class="col-xs-3">
            <div class="form-group">
                {!! Form::label('whatsapp_api_provider', __('lang_v1.api_provider') . ':') !!}
                {!! Form::select('whatsapp_settings[api_provider]', ['meta' => 'Meta (Facebook)', 'twilio' => 'Twilio', 'other' => __('lang_v1.other')], $api_provider, ['class' => 'form-control', 'id' => 'whatsapp_api_provider']); !!}
            </div>
        </div>
    </div>

    <!-- Meta WhatsApp Business API Settings -->
    <div class="whatsapp_provider_settings @if($api_provider != 'meta') hide @endif" data-provider="meta">
        <div class="row">
            <div class="col-xs-12">
                <hr>
                <h5 class="tw-font-semibold">Meta WhatsApp Business API</h5>
                <p class="text-muted small">Get these values from your <a href="https://developers.facebook.com/apps" target="_blank">Meta Developer Console</a></p>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-4">
                <div class="form-group">
                    {!! Form::label('whatsapp_access_token', __('lang_v1.access_token') . ':') !!}
                    {!! Form::text('whatsapp_settings[access_token]', !empty($whatsapp_settings['access_token']) ? $whatsapp_settings['access_token'] : null, ['class' => 'form-control', 'placeholder' => __('lang_v1.access_token'), 'id' => 'whatsapp_access_token']); !!}
                </div>
            </div>
            <div class="col-xs-4">
                <div class="form-group">
                    {!! Form::label('whatsapp_phone_number_id', __('lang_v1.phone_number_id') . ':') !!}
                    {!! Form::text('whatsapp_settings[phone_number_id]', !empty($whatsapp_settings['phone_number_id']) ? $whatsapp_settings['phone_number_id'] : null, ['class' => 'form-control', 'placeholder' => __('lang_v1.phone_number_id'), 'id' => 'whatsapp_phone_number_id']); !!}
                </div>
            </div>
            <div class="col-xs-4">
                <div class="form-group">
                    {!! Form::label('whatsapp_business_account_id', __('lang_v1.business_account_id') . ':') !!}
                    {!! Form::text('whatsapp_settings[business_account_id]', !empty($whatsapp_settings['business_account_id']) ? $whatsapp_settings['business_account_id'] : null, ['class' => 'form-control', 'placeholder' => __('lang_v1.business_account_id'), 'id' => 'whatsapp_business_account_id']); !!}
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-4">
                <div class="form-group">
                    {!! Form::label('whatsapp_verify_token', __('lang_v1.verify_token') . ':') !!}
                    @show_tooltip(__('lang_v1.verify_token_tooltip'))
                    {!! Form::text('whatsapp_settings[verify_token]', !empty($whatsapp_settings['verify_token']) ? $whatsapp_settings['verify_token'] : null, ['class' => 'form-control', 'placeholder' => __('lang_v1.verify_token'), 'id' => 'whatsapp_verify_token']); !!}
                </div>
            </div>
            <div class="col-xs-4">
                <div class="form-group">
                    {!! Form::label('whatsapp_api_url', __('lang_v1.api_url') . ':') !!}
                    {!! Form::text('whatsapp_settings[api_url]', !empty($whatsapp_settings['api_url']) ? $whatsapp_settings['api_url'] : 'https://graph.facebook.com/v17.0', ['class' => 'form-control', 'placeholder' => 'https://graph.facebook.com/v17.0', 'id' => 'whatsapp_api_url']); !!}
                </div>
            </div>
        </div>
    </div>

    <!-- Twilio WhatsApp Settings -->
    <div class="whatsapp_provider_settings @if($api_provider != 'twilio') hide @endif" data-provider="twilio">
        <div class="row">
            <div class="col-xs-12">
                <hr>
                <h5 class="tw-font-semibold">Twilio WhatsApp API</h5>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-4">
                <div class="form-group">
                    {!! Form::label('whatsapp_twilio_sid', __('lang_v1.twilio_sid') . ':') !!}
                    {!! Form::text('whatsapp_settings[twilio_sid]', !empty($whatsapp_settings['twilio_sid']) ? $whatsapp_settings['twilio_sid'] : null, ['class' => 'form-control', 'placeholder' => __('lang_v1.twilio_sid'), 'id' => 'whatsapp_twilio_sid']); !!}
                </div>
            </div>
            <div class="col-xs-4">
                <div class="form-group">
                    {!! Form::label('whatsapp_twilio_token', __('lang_v1.twilio_token') . ':') !!}
                    {!! Form::text('whatsapp_settings[twilio_token]', !empty($whatsapp_settings['twilio_token']) ? $whatsapp_settings['twilio_token'] : null, ['class' => 'form-control', 'placeholder' => __('lang_v1.twilio_token'), 'id' => 'whatsapp_twilio_token']); !!}
                </div>
            </div>
            <div class="col-xs-4">
                <div class="form-group">
                    {!! Form::label('whatsapp_twilio_from', __('lang_v1.whatsapp_number') . ':') !!}
                    {!! Form::text('whatsapp_settings[twilio_from]', !empty($whatsapp_settings['twilio_from']) ? $whatsapp_settings['twilio_from'] : null, ['class' => 'form-control', 'placeholder' => 'whatsapp:+1234567890', 'id' => 'whatsapp_twilio_from']); !!}
                </div>
            </div>
        </div>
    </div>

    <!-- Other API Settings -->
    <div class="whatsapp_provider_settings @if($api_provider != 'other') hide @endif" data-provider="other">
        <div class="row">
            <div class="col-xs-12">
                <hr>
                <h5 class="tw-font-semibold">@lang('lang_v1.custom_api_settings')</h5>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-6">
                <div class="form-group">
                    {!! Form::label('whatsapp_custom_url', __('lang_v1.api_url') . ':') !!}
                    {!! Form::text('whatsapp_settings[custom_url]', !empty($whatsapp_settings['custom_url']) ? $whatsapp_settings['custom_url'] : null, ['class' => 'form-control', 'placeholder' => 'https://api.example.com/whatsapp/send', 'id' => 'whatsapp_custom_url']); !!}
                </div>
            </div>
            <div class="col-xs-3">
                <div class="form-group">
                    {!! Form::label('whatsapp_custom_api_key', __('lang_v1.api_key') . ':') !!}
                    {!! Form::text('whatsapp_settings[custom_api_key]', !empty($whatsapp_settings['custom_api_key']) ? $whatsapp_settings['custom_api_key'] : null, ['class' => 'form-control', 'placeholder' => __('lang_v1.api_key'), 'id' => 'whatsapp_custom_api_key']); !!}
                </div>
            </div>
            <div class="col-xs-3">
                <div class="form-group">
                    {!! Form::label('whatsapp_custom_sender', __('lang_v1.sender_number') . ':') !!}
                    {!! Form::text('whatsapp_settings[custom_sender]', !empty($whatsapp_settings['custom_sender']) ? $whatsapp_settings['custom_sender'] : null, ['class' => 'form-control', 'placeholder' => '+254712345678', 'id' => 'whatsapp_custom_sender']); !!}
                </div>
            </div>
        </div>
    </div>

    <!-- Message Templates -->
    <div class="row">
        <div class="col-xs-12">
            <hr>
            <h5 class="tw-font-semibold">@lang('lang_v1.message_templates')</h5>
        </div>
    </div>

    <!-- Sale Notification -->
    <div class="row">
        <div class="col-xs-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="row">
                        <div class="col-xs-6">
                            <strong>@lang('lang_v1.sale_notification')</strong>
                        </div>
                        <div class="col-xs-6 text-right">
                            {!! Form::checkbox('whatsapp_settings[sale_notification_enabled]', 1, !empty($whatsapp_settings['sale_notification_enabled']), ['class' => 'input-icheck', 'id' => 'sale_notification_enabled']); !!}
                            <label for="sale_notification_enabled">@lang('lang_v1.enable')</label>
                        </div>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        {!! Form::label('sale_message_template', __('lang_v1.message_template') . ':') !!}
                        @show_tooltip(__('lang_v1.available_tags') . ': {customer_name}, {business_name}, {invoice_no}, {total}, {date}, {items}')
                        {!! Form::textarea('whatsapp_settings[sale_message_template]', !empty($whatsapp_settings['sale_message_template']) ? $whatsapp_settings['sale_message_template'] : "Hello {customer_name},\n\nThank you for your purchase at {business_name}!\n\nInvoice: {invoice_no}\nTotal: {total}\nDate: {date}\n\nWe appreciate your business!", ['class' => 'form-control', 'rows' => 4, 'id' => 'sale_message_template']); !!}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Reminder -->
    <div class="row">
        <div class="col-xs-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="row">
                        <div class="col-xs-6">
                            <strong>@lang('lang_v1.payment_reminder')</strong>
                        </div>
                        <div class="col-xs-6 text-right">
                            {!! Form::checkbox('whatsapp_settings[payment_reminder_enabled]', 1, !empty($whatsapp_settings['payment_reminder_enabled']), ['class' => 'input-icheck', 'id' => 'payment_reminder_enabled']); !!}
                            <label for="payment_reminder_enabled">@lang('lang_v1.enable')</label>
                        </div>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        {!! Form::label('payment_reminder_template', __('lang_v1.message_template') . ':') !!}
                        @show_tooltip(__('lang_v1.available_tags') . ': {customer_name}, {invoice_no}, {amount_due}, {due_date}, {business_name}')
                        {!! Form::textarea('whatsapp_settings[payment_reminder_template]', !empty($whatsapp_settings['payment_reminder_template']) ? $whatsapp_settings['payment_reminder_template'] : "Hello {customer_name},\n\nThis is a friendly reminder about your pending payment.\n\nInvoice: {invoice_no}\nAmount Due: {amount_due}\nDue Date: {due_date}\n\nPlease contact us if you have any questions.", ['class' => 'form-control', 'rows' => 4, 'id' => 'payment_reminder_template']); !!}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Follow-up Notifications -->
    <div class="row">
        <div class="col-xs-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="row">
                        <div class="col-xs-6">
                            <strong>@lang('lang_v1.followup_notifications')</strong>
                        </div>
                        <div class="col-xs-6 text-right">
                            {!! Form::checkbox('whatsapp_settings[followup_notification_enabled]', 1, !empty($whatsapp_settings['followup_notification_enabled']), ['class' => 'input-icheck', 'id' => 'followup_notification_enabled']); !!}
                            <label for="followup_notification_enabled">@lang('lang_v1.enable')</label>
                        </div>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-xs-8">
                            <div class="form-group">
                                {!! Form::label('followup_notification_time', __('lang_v1.notification_time') . ':') !!}
                                @show_tooltip(__('lang_v1.notification_time_help'))
                                {!! Form::text('whatsapp_settings[followup_notification_time]', !empty($whatsapp_settings['followup_notification_time']) ? $whatsapp_settings['followup_notification_time'] : '09:00', ['class' => 'form-control', 'id' => 'followup_notification_time', 'placeholder' => 'e.g. 09:00, 14:00, 18:00']); !!}
                            </div>
                        </div>
                        <div class="col-xs-4">
                            <div class="form-group">
                                {!! Form::label('followup_notification_frequency', __('lang_v1.notification_frequency') . ':') !!}
                                {!! Form::select('whatsapp_settings[followup_notification_frequency]', ['daily' => __('lang_v1.daily'), 'weekly' => __('lang_v1.weekly')], !empty($whatsapp_settings['followup_notification_frequency']) ? $whatsapp_settings['followup_notification_frequency'] : 'daily', ['class' => 'form-control', 'id' => 'followup_notification_frequency']); !!}
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('followup_notification_template', __('lang_v1.message_template') . ':') !!}
                        @show_tooltip(__('lang_v1.available_tags') . ': {customer_name}, {product_name}, {business_name}, {followup_date}')
                        {!! Form::textarea('whatsapp_settings[followup_notification_template]', !empty($whatsapp_settings['followup_notification_template']) ? $whatsapp_settings['followup_notification_template'] : "Internal Reminder: Follow up with {customer_name} regarding their inquiry for {product_name}. Planned Date: {followup_date}", ['class' => 'form-control', 'rows' => 4, 'id' => 'followup_notification_template']); !!}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scheduling -->
    <div class="row">
        <div class="col-xs-12">
            <hr>
            <h5 class="tw-font-semibold">@lang('lang_v1.message_scheduling')</h5>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-3">
            <div class="form-group">
                {!! Form::label('schedule_enabled', __('lang_v1.enable_scheduling') . ':') !!}
                {!! Form::select('whatsapp_settings[schedule_enabled]', [0 => __('lang_v1.no'), 1 => __('lang_v1.yes')], !empty($whatsapp_settings['schedule_enabled']) ? $whatsapp_settings['schedule_enabled'] : 0, ['class' => 'form-control', 'id' => 'schedule_enabled']); !!}
            </div>
        </div>
        <div class="col-xs-3">
            <div class="form-group">
                {!! Form::label('schedule_time', __('lang_v1.send_time') . ':') !!}
                {!! Form::time('whatsapp_settings[schedule_time]', !empty($whatsapp_settings['schedule_time']) ? $whatsapp_settings['schedule_time'] : '09:00', ['class' => 'form-control', 'id' => 'schedule_time']); !!}
            </div>
        </div>
        <div class="col-xs-6">
            <div class="form-group">
                {!! Form::label('schedule_days', __('lang_v1.send_on_days') . ':') !!}
                <div class="checkbox-group">
                    @php
                        $days = ['monday' => 'Mon', 'tuesday' => 'Tue', 'wednesday' => 'Wed', 'thursday' => 'Thu', 'friday' => 'Fri', 'saturday' => 'Sat', 'sunday' => 'Sun'];
                        $selected_days = !empty($whatsapp_settings['schedule_days']) ? $whatsapp_settings['schedule_days'] : ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];
                    @endphp
                    @foreach($days as $value => $label)
                        <label class="checkbox-inline">
                            {!! Form::checkbox('whatsapp_settings[schedule_days][]', $value, in_array($value, $selected_days), ['class' => 'input-icheck']); !!}
                            {{ $label }}
                        </label>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Test WhatsApp -->
    <div class="row">
        <div class="col-xs-12">
            <hr>
            <h5 class="tw-font-semibold">@lang('lang_v1.test_whatsapp')</h5>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 col-xs-12">
            <div class="form-group">
                <div class="input-group">
                    {!! Form::text('whatsapp_test_number', null, ['class' => 'form-control', 'placeholder' => '+254712345678', 'id' => 'whatsapp_test_number']); !!}
                    <span class="input-group-btn">
                        <button type="button" class="btn btn-success" id="test_whatsapp_btn">
                            <i class="fa fa-whatsapp"></i> @lang('lang_v1.send_test_message')
                        </button>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Toggle provider settings
    $('#whatsapp_api_provider').change(function() {
        var provider = $(this).val();
        $('.whatsapp_provider_settings').addClass('hide');
        $('.whatsapp_provider_settings[data-provider="' + provider + '"]').removeClass('hide');
    });

    // Test WhatsApp
    $('#test_whatsapp_btn').click(function() {
        var test_number = $('#whatsapp_test_number').val();
        if (test_number.trim() == '') {
            toastr.error('Please enter a phone number');
            $('#whatsapp_test_number').focus();
            return false;
        }

        var data = {
            provider: $('#whatsapp_api_provider').val(),
            access_token: $('#whatsapp_access_token').val(),
            phone_number_id: $('#whatsapp_phone_number_id').val(),
            api_url: $('#whatsapp_api_url').val(),
            twilio_sid: $('#whatsapp_twilio_sid').val(),
            twilio_token: $('#whatsapp_twilio_token').val(),
            twilio_from: $('#whatsapp_twilio_from').val(),
            custom_url: $('#whatsapp_custom_url').val(),
            custom_api_key: $('#whatsapp_custom_api_key').val(),
            custom_sender: $('#whatsapp_custom_sender').val(),
            test_number: test_number,
        };

        $.ajax({
            method: 'post',
            data: data,
            url: "{{ action([\App\Http\Controllers\BusinessController::class, 'testWhatsAppConfiguration']) }}",
            dataType: 'json',
            beforeSend: function() {
                $('#test_whatsapp_btn').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Sending...');
            },
            success: function(result) {
                if (result.success == true) {
                    swal({
                        text: result.msg,
                        icon: 'success'
                    });
                } else {
                    swal({
                        text: result.msg,
                        icon: 'error'
                    });
                }
            },
            error: function(xhr) {
                swal({
                    text: 'An error occurred while testing the WhatsApp configuration.',
                    icon: 'error'
                });
            },
            complete: function() {
                $('#test_whatsapp_btn').prop('disabled', false).html('<i class="fa fa-whatsapp"></i> @lang("lang_v1.send_test_message")');
            }
        });
    });
});
</script>
