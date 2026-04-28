@extends('layouts.app')
@section('title', 'eTIMS Settings')

@section('content')

<section class="content-header">
    <div style="background:linear-gradient(135deg,#064e3b 0%,#047857 100%);border-radius:14px;
                padding:18px 24px;margin-bottom:20px;box-shadow:0 4px 20px rgba(6,78,59,.2);">
        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;">
            <div style="display:flex;align-items:center;gap:14px;">
                <div style="width:42px;height:42px;background:rgba(255,255,255,.15);border-radius:10px;
                            display:flex;align-items:center;justify-content:center;">
                    <i class="fa fa-cog" style="color:#fff;font-size:18px;"></i>
                </div>
                <div>
                    <h1 style="color:#fff;font-size:1.2rem;font-weight:700;margin:0;">eTIMS Settings</h1>
                    <p style="color:rgba(255,255,255,.7);margin:2px 0 0;font-size:.78rem;">
                        Configure your KRA eTIMS / Digitax integration
                    </p>
                </div>
            </div>
            <a href="{{ route('etims.index') }}"
               style="background:rgba(255,255,255,.15);color:#fff;border:1px solid rgba(255,255,255,.3);
                      border-radius:8px;padding:7px 14px;font-size:.8rem;text-decoration:none;
                      display:inline-flex;align-items:center;gap:6px;">
                <i class="fa fa-arrow-left"></i> Back to Reports
            </a>
        </div>
    </div>
</section>

<section class="content">

@if(session('status'))
<div class="alert alert-success alert-dismissible" style="border-radius:10px;">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    <i class="fa fa-check-circle"></i> {{ session('status') }}
</div>
@endif

<div class="row">
    <div class="col-md-7">
        <div style="background:#fff;border-radius:12px;box-shadow:0 1px 6px rgba(0,0,0,.07);
                    border:1px solid #e2e8f0;overflow:hidden;">
            <div style="padding:16px 20px;border-bottom:1px solid #f1f5f9;background:#f8fafc;">
                <h4 style="margin:0;font-size:.9rem;font-weight:600;color:#374151;">
                    <i class="fa fa-plug" style="color:#059669;margin-right:6px;"></i>
                    Digitax API Configuration
                </h4>
            </div>
            <div style="padding:24px;">
                {!! Form::open(['route' => 'etims.settings.save', 'method' => 'POST']) !!}

                {{-- Enable Toggle --}}
                <div style="display:flex;align-items:center;justify-content:space-between;
                            padding:14px 16px;background:#f0fdf4;border-radius:10px;
                            border:1px solid #bbf7d0;margin-bottom:20px;">
                    <div>
                        <div style="font-size:.85rem;font-weight:600;color:#15803d;">eTIMS Integration</div>
                        <div style="font-size:.72rem;color:#16a34a;">Enable automatic invoice syncing to KRA</div>
                    </div>
                    <label style="position:relative;display:inline-block;width:46px;height:26px;cursor:pointer;margin:0;">
                        <input type="hidden" name="etims_enabled" value="0">
                        <input type="checkbox" name="etims_enabled" value="1"
                               id="etims_enabled_chk"
                               {{ $business->etims_enabled ? 'checked' : '' }}
                               style="opacity:0;width:0;height:0;position:absolute;">
                        <span id="etims_toggle_knob"
                              style="position:absolute;top:0;left:0;right:0;bottom:0;
                                     background:{{ $business->etims_enabled ? '#22c55e' : '#cbd5e1' }};
                                     border-radius:26px;transition:.3s;">
                            <span id="etims_toggle_dot"
                                  style="position:absolute;height:20px;width:20px;left:3px;bottom:3px;
                                         background:#fff;border-radius:50%;transition:.3s;display:block;
                                         transform:{{ $business->etims_enabled ? 'translateX(20px)' : 'translateX(0)' }};"></span>
                        </span>
                    </label>
                </div>

                {{-- API Key --}}
                <div class="form-group">
                    <label style="font-size:.8rem;font-weight:600;color:#475569;">
                        Digitax API Key
                        <span style="color:#94a3b8;font-weight:400;font-size:.72rem;">(required for sync)</span>
                    </label>
                    <div class="input-group">
                        {!! Form::password('digitax_api_key', ['class' => 'form-control', 'id' => 'api_key_field',
                            'placeholder' => 'Enter your Digitax API key']) !!}
                        <span class="input-group-btn">
                            <button type="button" id="toggle_api_key" class="btn btn-default" style="border-left:0;">
                                <i class="fa fa-eye"></i>
                            </button>
                        </span>
                    </div>
                    @if($business->digitax_api_key)
                        <p style="font-size:.72rem;color:#16a34a;margin-top:4px;">
                            <i class="fa fa-check-circle"></i> API key configured
                            &middot; ends in <code>...{{ substr($business->digitax_api_key, -6) }}</code>
                        </p>
                    @else
                        <p style="font-size:.72rem;color:#94a3b8;margin-top:4px;">
                            Get your key from the <a href="https://digitax.co.ke" target="_blank">Digitax portal</a>.
                        </p>
                    @endif
                </div>

                {{-- TPIN --}}
                <div class="form-group">
                    <label style="font-size:.8rem;font-weight:600;color:#475569;">
                        KRA TPIN / PIN
                        <span style="color:#94a3b8;font-weight:400;font-size:.72rem;">(your tax PIN)</span>
                    </label>
                    {!! Form::text('etims_tpin', $business->etims_tpin ?? '', [
                        'class' => 'form-control', 'placeholder' => 'e.g. P000000000X'
                    ]) !!}
                </div>

                {{-- Sync Mode --}}
                <div class="form-group">
                    <label style="font-size:.8rem;font-weight:600;color:#475569;">Sync Mode</label>
                    @php
                        $syncModes = [
                            'realtime'   => ['Real-time',  'Send immediately on sale/purchase', '#4f46e5', 'fa-bolt'],
                            'background' => ['Background', 'Queue and batch-send periodically',  '#0891b2', 'fa-clock-o'],
                            'manual'     => ['Manual',     'Only sync when you click the button', '#64748b', 'fa-hand-pointer-o'],
                        ];
                        $currentMode = $business->etims_sync_mode ?? 'manual';
                    @endphp
                    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:10px;margin-top:6px;">
                        @foreach($syncModes as $val => [$modeLabel, $modeDesc, $modeColor, $modeIcon])
                        <label style="display:block;cursor:pointer;margin:0;">
                            <input type="radio" name="etims_sync_mode" value="{{ $val }}"
                                   {{ $currentMode === $val ? 'checked' : '' }}
                                   class="sync-mode-radio" style="display:none;">
                            <div class="sync-mode-card"
                                 style="border:2px solid {{ $currentMode === $val ? $modeColor : '#e2e8f0' }};
                                        border-radius:10px;padding:12px 10px;text-align:center;transition:.2s;
                                        background:{{ $currentMode === $val ? '#f8fafc' : '#fff' }};">
                                <i class="fa {{ $modeIcon }}"
                                   style="font-size:1.2rem;color:{{ $modeColor }};margin-bottom:6px;display:block;"></i>
                                <div style="font-size:.78rem;font-weight:600;color:#374151;">{{ $modeLabel }}</div>
                                <div style="font-size:.65rem;color:#94a3b8;margin-top:3px;line-height:1.3;">{{ $modeDesc }}</div>
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>

                <div style="padding-top:16px;border-top:1px solid #f1f5f9;display:flex;gap:10px;margin-top:4px;">
                    {!! Form::submit('Save Settings', ['class' => 'btn btn-success', 'style' => 'font-weight:600;']) !!}
                    <a href="{{ route('etims.index') }}" class="btn btn-default">Cancel</a>
                </div>

                {!! Form::close() !!}
            </div>
        </div>
    </div>

    {{-- Right info panel --}}
    <div class="col-md-5">
        <div style="background:#fff;border-radius:12px;box-shadow:0 1px 6px rgba(0,0,0,.07);
                    border:1px solid #e2e8f0;padding:20px;margin-bottom:14px;">
            <h4 style="font-size:.85rem;font-weight:600;color:#374151;margin:0 0 14px;
                        display:flex;align-items:center;gap:6px;">
                <i class="fa fa-info-circle" style="color:#3b82f6;"></i> About eTIMS / Digitax
            </h4>
            <div style="font-size:.8rem;color:#475569;line-height:1.7;">
                <p>The <strong>Electronic Tax Invoice Management System (eTIMS)</strong> is mandated by the
                Kenya Revenue Authority (KRA) for all VAT-registered businesses.</p>
                <p>This system uses <strong>Digitax</strong> as the middleware to push sales and purchase
                invoices to KRA in real time.</p>
                <ul style="padding-left:18px;margin:0;color:#64748b;">
                    <li>All invoices are signed and assigned a KRA invoice number.</li>
                    <li>Compliance deadline: January 2026 for all income/expenses.</li>
                    <li>Failed syncs are retried from the Reports page.</li>
                </ul>
            </div>
            <div style="margin-top:14px;padding:12px;background:#fef3c7;border-radius:8px;
                        border:1px solid #fde68a;font-size:.76rem;color:#92400e;">
                <i class="fa fa-exclamation-triangle" style="margin-right:4px;"></i>
                <strong>Important:</strong> Verify your TPIN and API Key before enabling real-time mode
                to avoid failed invoice submissions to KRA.
            </div>
        </div>

        {{-- Status card --}}
        <div style="background:#fff;border-radius:12px;box-shadow:0 1px 6px rgba(0,0,0,.07);
                    border:1px solid #e2e8f0;padding:20px;">
            <h4 style="font-size:.85rem;font-weight:600;color:#374151;margin:0 0 14px;">
                <i class="fa fa-heartbeat" style="color:#ef4444;margin-right:5px;"></i> Current Status
            </h4>
            <div style="display:flex;flex-direction:column;gap:10px;font-size:.8rem;">
                @php
                    $statusItems = [
                        ['eTIMS Enabled',   $business->etims_enabled,    'Active',      'Disabled'],
                        ['API Key',         $business->digitax_api_key,  'Configured',  'Missing'],
                        ['TPIN / PIN',      $business->etims_tpin,       'Set',         'Not set'],
                    ];
                @endphp
                @foreach($statusItems as [$lbl, $val, $yes, $no])
                <div style="display:flex;justify-content:space-between;align-items:center;
                            padding-bottom:8px;border-bottom:1px solid #f1f5f9;">
                    <span style="color:#64748b;">{{ $lbl }}</span>
                    @if($val)
                        <span style="background:#dcfce7;color:#15803d;border-radius:20px;padding:2px 10px;font-size:.72rem;font-weight:600;">
                            <i class="fa fa-check"></i> {{ $yes }}
                        </span>
                    @else
                        <span style="background:#fef2f2;color:#dc2626;border-radius:20px;padding:2px 10px;font-size:.72rem;">
                            <i class="fa fa-times"></i> {{ $no }}
                        </span>
                    @endif
                </div>
                @endforeach
                <div style="display:flex;justify-content:space-between;align-items:center;">
                    <span style="color:#64748b;">Sync Mode</span>
                    <span style="background:#eff6ff;color:#3b82f6;border-radius:20px;padding:2px 10px;font-size:.72rem;font-weight:600;">
                        {{ ucfirst($business->etims_sync_mode ?? 'manual') }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

</section>
@endsection

@section('javascript')
<script>
$(function () {
    // Toggle API key visibility
    $('#toggle_api_key').on('click', function () {
        var f = $('#api_key_field');
        var isPass = f.attr('type') === 'password';
        f.attr('type', isPass ? 'text' : 'password');
        $(this).find('i').toggleClass('fa-eye', !isPass).toggleClass('fa-eye-slash', isPass);
    });

    // Sync mode card selection
    var modeColors = { realtime: '#4f46e5', background: '#0891b2', manual: '#64748b' };
    $('.sync-mode-radio').on('change', function () {
        $('.sync-mode-card').css({ 'border-color': '#e2e8f0', 'background': '#fff' });
        var val = $(this).val();
        $(this).closest('label').find('.sync-mode-card')
            .css({ 'border-color': modeColors[val] || '#4f46e5', 'background': '#f8fafc' });
    });
    // Also handle click on the card div itself
    $('.sync-mode-card').on('click', function () {
        $(this).closest('label').find('.sync-mode-radio').prop('checked', true).trigger('change');
    });

    // Toggle knob animation
    $('#etims_enabled_chk').on('change', function () {
        var on = $(this).is(':checked');
        $('#etims_toggle_knob').css('background', on ? '#22c55e' : '#cbd5e1');
        $('#etims_toggle_dot').css('transform', on ? 'translateX(20px)' : 'translateX(0)');
    });
    $('#etims_toggle_knob').on('click', function () {
        $('#etims_enabled_chk').prop('checked', !$('#etims_enabled_chk').is(':checked')).trigger('change');
    });
});
</script>
@endsection
