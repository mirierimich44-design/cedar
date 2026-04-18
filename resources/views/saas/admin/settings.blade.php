@extends('layouts.app')
@section('title', 'SaaS Settings')
@section('content')
<section class="content-header">
    <h1>SaaS Control Panel
        <small>Trial, campaigns, payment & support</small>
    </h1>
</section>
<section class="content">

@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
@if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif

<form method="POST" action="{{ route('saas.admin.settings.update') }}">
    @csrf @method('PUT')

    {{-- TRIAL PERIOD CONTROL --}}
    @php $trial = $settings['trial'] ?? collect(); @endphp
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title"><i class="fas fa-gift"></i> Free Trial Control</h3>
            <span class="pull-right text-muted" style="font-size:12px;">Campaign-ready — flip trial length anytime.</span>
        </div>
        <div class="box-body">
            <div class="row">
                @foreach($trial as $s)
                    <div class="col-md-4">
                        <div class="form-group">
                            <label><strong>{{ $s->label }}</strong></label>
                            @if($s->type === 'bool')
                                <div class="checkbox" style="padding-top:6px;">
                                    <label>
                                        <input type="hidden" name="{{ $s->key }}" value="0">
                                        <input type="checkbox" name="{{ $s->key }}" value="1" {{ $s->value == '1' ? 'checked' : '' }}>
                                        <span>Enable</span>
                                    </label>
                                </div>
                            @elseif($s->key === 'trial_days')
                                <select name="{{ $s->key }}" class="form-control">
                                    @foreach([0 => 'No trial (force payment)', 1 => '1 day (aggressive)', 3 => '3 days (default)', 7 => '7 days (standard)', 14 => '14 days (campaign)', 30 => '30 days (enterprise)'] as $v => $lbl)
                                        <option value="{{ $v }}" {{ (int)$s->value === $v ? 'selected' : '' }}>{{ $lbl }}</option>
                                    @endforeach
                                </select>
                            @else
                                <input type="number" min="0" max="30" name="{{ $s->key }}" value="{{ $s->value }}" class="form-control">
                            @endif
                            @if($s->description)<p class="help-block" style="font-size:12px;">{{ $s->description }}</p>@endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- CAMPAIGN CONTROL --}}
    @php $camp = $settings['campaign'] ?? collect(); @endphp
    <div class="box box-warning">
        <div class="box-header with-border">
            <h3 class="box-title"><i class="fas fa-bullhorn"></i> Marketing Campaign</h3>
            <span class="pull-right text-muted" style="font-size:12px;">Banner + sitewide discount.</span>
        </div>
        <div class="box-body">
            @foreach($camp as $s)
                <div class="form-group">
                    <label><strong>{{ $s->label }}</strong></label>
                    @if($s->type === 'bool')
                        <div class="checkbox" style="padding-top:6px;">
                            <label>
                                <input type="hidden" name="{{ $s->key }}" value="0">
                                <input type="checkbox" name="{{ $s->key }}" value="1" {{ $s->value == '1' ? 'checked' : '' }}>
                                <span>Show banner + apply discount on pricing page</span>
                            </label>
                        </div>
                    @elseif($s->key === 'campaign_discount_percent')
                        <input type="number" min="0" max="80" name="{{ $s->key }}" value="{{ $s->value }}" class="form-control" style="max-width:200px;">
                    @else
                        <input type="text" name="{{ $s->key }}" value="{{ $s->value }}" class="form-control">
                    @endif
                    @if($s->description)<p class="help-block" style="font-size:12px;">{{ $s->description }}</p>@endif
                </div>
            @endforeach
        </div>
    </div>

    {{-- M-PESA PAYMENT --}}
    @php $pay = $settings['payment'] ?? collect(); @endphp
    <div class="box box-success">
        <div class="box-header with-border">
            <h3 class="box-title"><i class="fas fa-mobile-alt"></i> M-Pesa Payment Settings</h3>
            <span class="pull-right text-muted" style="font-size:12px;">Daraja STK Push + manual Paybill.</span>
        </div>
        <div class="box-body">
            <div class="row">
                @foreach($pay as $s)
                    @php
                        $isSecret = in_array($s->key, ['mpesa_consumer_secret','mpesa_passkey']);
                        $colClass = in_array($s->key, ['mpesa_enabled','mpesa_mode','mpesa_env']) ? 'col-md-4' : 'col-md-6';
                    @endphp
                    <div class="{{ $colClass }}">
                        <div class="form-group">
                            <label><strong>{{ $s->label }}</strong></label>
                            @if($s->type === 'bool')
                                <div class="checkbox" style="padding-top:6px;">
                                    <label>
                                        <input type="hidden" name="{{ $s->key }}" value="0">
                                        <input type="checkbox" name="{{ $s->key }}" value="1" {{ $s->value == '1' ? 'checked' : '' }}>
                                        <span>Enabled</span>
                                    </label>
                                </div>
                            @elseif($s->key === 'mpesa_mode')
                                <select name="{{ $s->key }}" class="form-control">
                                    <option value="manual" {{ $s->value === 'manual' ? 'selected' : '' }}>Manual — show Paybill instructions (no API)</option>
                                    <option value="stk"    {{ $s->value === 'stk'    ? 'selected' : '' }}>STK Push — auto-prompt phone via Daraja API</option>
                                </select>
                            @elseif($s->key === 'mpesa_env')
                                <select name="{{ $s->key }}" class="form-control">
                                    <option value="sandbox"    {{ $s->value === 'sandbox'    ? 'selected' : '' }}>Sandbox (testing)</option>
                                    <option value="production" {{ $s->value === 'production' ? 'selected' : '' }}>Production (live)</option>
                                </select>
                            @elseif($isSecret)
                                <input type="password" name="{{ $s->key }}" value="{{ $s->value }}" class="form-control" autocomplete="new-password" placeholder="••••••••">
                            @else
                                <input type="text" name="{{ $s->key }}" value="{{ $s->value }}" class="form-control">
                            @endif
                            @if($s->description)<p class="help-block" style="font-size:12px;">{{ $s->description }}</p>@endif
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="alert alert-info" style="margin-top:10px;">
                <i class="fas fa-info-circle"></i> Get credentials at
                <a href="https://developer.safaricom.co.ke" target="_blank">developer.safaricom.co.ke</a>.
                Sandbox works immediately for testing; production requires Safaricom approval.
            </div>
        </div>
    </div>

    {{-- GENERAL --}}
    @php $gen = $settings['general'] ?? collect(); @endphp
    <div class="box box-default">
        <div class="box-header with-border">
            <h3 class="box-title"><i class="fas fa-cog"></i> Support & Contact</h3>
        </div>
        <div class="box-body">
            <div class="row">
                @foreach($gen as $s)
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><strong>{{ $s->label }}</strong></label>
                            <input type="text" name="{{ $s->key }}" value="{{ $s->value }}" class="form-control">
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div style="margin-bottom:40px;">
        <button type="submit" class="btn btn-primary btn-lg">
            <i class="fas fa-save"></i> Save All Settings
        </button>
        <a href="{{ route('saas.admin.dashboard') }}" class="btn btn-default btn-lg">Cancel</a>
        <span class="pull-right text-muted" style="line-height:42px;">
            <i class="fas fa-info-circle"></i> Changes apply to new signups immediately.
        </span>
    </div>
</form>
</section>
@endsection
