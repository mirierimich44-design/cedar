@extends('layouts.app')
@section('title', 'Automated SMS')

@section('content')
<section class="content-header">
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">Automated SMS</h1>
</section>

<section class="content">
    @component('components.widget', ['class' => 'box-primary', 'title' => 'Automated SMS Triggers'])
        <p class="text-muted">Automated SMS messages are triggered by events in the system. Manage the message templates and enable/disable them below.</p>

        <div class="row" style="margin-bottom:16px">
            <div class="col-md-12">
                <a href="{{ action([\App\Http\Controllers\NotificationTemplateController::class, 'index']) }}" class="btn btn-primary">
                    <i class="fa fa-bell"></i> Manage Notification Templates (SMS + Email + WhatsApp)
                </a>
            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading"><strong>How Automated SMS Works</strong></div>
            <div class="panel-body">
                <ul>
                    <li><strong>Sale confirmation</strong> — sent automatically when a new sale is recorded</li>
                    <li><strong>Payment received</strong> — sent when a payment is received from a customer</li>
                    <li><strong>Payment reminder</strong> — scheduled daily to remind customers of outstanding dues</li>
                    <li><strong>Purchase order</strong> — sent to supplier when a purchase order is created</li>
                    <li><strong>Stock alert</strong> — sent when a product goes below the low-stock threshold</li>
                </ul>
                <p class="text-muted" style="margin-top:8px">
                    To enable/disable each trigger or edit the message template, go to
                    <strong>Notification Templates</strong> and toggle the <em>Auto Send SMS</em> option.
                </p>
            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading"><strong>SMS Provider</strong></div>
            <div class="panel-body">
                <p>
                    Your SMS provider is configured in
                    <a href="{{ action([\App\Http\Controllers\BusinessController::class, 'getBusinessSettings']) }}#sms_settings">Business Settings → SMS Settings</a>.
                </p>
                @php
                    $sms_settings = session('business.sms_settings');
                    $provider = $sms_settings['sms_service'] ?? 'other';
                @endphp
                <span class="label label-info" style="font-size:14px">Current provider: {{ ucfirst($provider) }}</span>
            </div>
        </div>
    @endcomponent
</section>
@endsection
