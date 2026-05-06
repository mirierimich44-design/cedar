@extends('layouts.app')
@section('title', 'Business Feature Management')


@section('css')
@parent
@include('layouts.partials.page_modern_css')
@endsection

@section('content')

<div class="page-modern">

    <section class="content-header"></section>

    <div class="pg-banner">
        <div class="pg-banner-inner">
            <div class="pg-banner-title">
                <div class="pg-banner-icon">
                    <i class="fas fa-toggle-on"></i>
                </div>
                <div>
                    <h1>Business Feature Management</h1>
                    <p class="pg-subtitle">{{ session('business.name') }}</p>
                </div>
            </div>
            <div class="pg-banner-actions"></div>
        </div>
    </div>

<section class="content">
<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">
            <i class="fa fa-toggle-on"></i> Feature &amp; Module Control
        </h3>
        <div class="box-tools pull-right" style="display:flex;gap:8px;align-items:center">

            {{-- Business selector --}}
            <form method="GET" action="{{ route('business-features.index') }}" style="margin:0">
                <div class="input-group input-group-sm" style="width:220px">
                    <select name="business_id" class="form-control select2" onchange="this.form.submit()">
                        @foreach($businesses as $biz)
                            <option value="{{ $biz->id }}" {{ $biz->id == $selectedId ? 'selected' : '' }}>
                                {{ $biz->name }}
                            </option>
                        @endforeach
                    </select>
                    <span class="input-group-btn">
                        <button type="submit" class="btn btn-default btn-sm"><i class="fa fa-refresh"></i></button>
                    </span>
                </div>
            </form>

            @if($selectedId)
            <button class="btn btn-success btn-sm" onclick="setAll(true)">
                <i class="fa fa-check-circle"></i> Enable All
            </button>
            <button class="btn btn-danger btn-sm" onclick="setAll(false)">
                <i class="fa fa-times-circle"></i> Disable All
            </button>
            @endif

        </div>
    </div>

    <div class="box-body">

        @if(!$selectedId)
            <div class="callout callout-info">
                <p>Select a business above to manage its features.</p>
            </div>
        @else

        {{-- Status banner --}}
        <div id="status-banner" style="display:none;margin-bottom:12px"></div>

        <p class="text-muted" style="margin-bottom:16px">
            <strong>{{ $selectedBiz->name ?? '' }}</strong> — toggle any feature below.
            Changes take effect immediately (cache cleared on save).
            Features default to <span class="label label-success">Enabled</span> if never explicitly set.
        </p>

        @foreach($grouped as $category => $features)
        <div class="panel panel-default" style="margin-bottom:16px">
            <div class="panel-heading" style="background:#f7f7f7;font-weight:600;font-size:13px">
                {{ $category }}
            </div>
            <div class="panel-body" style="padding:0">
                <table class="table table-hover" style="margin:0">
                    <thead>
                        <tr>
                            <th style="width:40px"></th>
                            <th>Feature</th>
                            <th>Description</th>
                            <th style="width:120px;text-align:center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($features as $key => $meta)
                        <tr id="row-{{ $key }}">
                            <td style="text-align:center;font-size:20px;line-height:1">
                                {{ $meta['icon'] }}
                            </td>
                            <td>
                                <strong>{{ $meta['label'] }}</strong>
                                <br><small class="text-muted" style="font-family:monospace;font-size:11px">{{ $key }}</small>
                            </td>
                            <td class="text-muted" style="font-size:13px">{{ $meta['description'] }}</td>
                            <td style="text-align:center">
                                <label class="toggle-switch" title="{{ $meta['enabled'] ? 'Click to disable' : 'Click to enable' }}">
                                    <input type="checkbox"
                                           class="feature-toggle"
                                           data-key="{{ $key }}"
                                           data-business="{{ $selectedId }}"
                                           {{ $meta['enabled'] ? 'checked' : '' }}>
                                    <span class="slider"></span>
                                </label>
                                <br>
                                <small id="badge-{{ $key }}" class="label {{ $meta['enabled'] ? 'label-success' : 'label-default' }}">
                                    {{ $meta['enabled'] ? 'Enabled' : 'Disabled' }}
                                </small>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endforeach

        @endif
    </div>{{-- box-body --}}
</div>
</section>
@endsection

@section('javascript')
<script>
$(function () {

    // Individual toggle
    $(document).on('change', '.feature-toggle', function () {
        var $cb      = $(this);
        var key      = $cb.data('key');
        var bizId    = $cb.data('business');
        var enabled  = $cb.is(':checked') ? 1 : 0;

        $cb.prop('disabled', true);

        $.ajax({
            url:  '{{ route("business-features.toggle") }}',
            type: 'POST',
            data: {
                _token:      '{{ csrf_token() }}',
                business_id: bizId,
                feature_key: key,
                is_enabled:  enabled
            },
            success: function (resp) {
                if (resp.success) {
                    var $badge = $('#badge-' + key);
                    if (enabled) {
                        $badge.removeClass('label-default').addClass('label-success').text('Enabled');
                    } else {
                        $badge.removeClass('label-success').addClass('label-default').text('Disabled');
                    }
                    showBanner('Feature <strong>' + key + '</strong> ' + (enabled ? 'enabled' : 'disabled') + '.', 'success');
                }
            },
            error: function (xhr) {
                $cb.prop('checked', !$cb.is(':checked')); // revert
                showBanner('Error: ' + (xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Could not save change.'), 'danger');
            },
            complete: function () {
                $cb.prop('disabled', false);
            }
        });
    });

});

// Enable / Disable all
function setAll(enable) {
    var bizId = {{ $selectedId ?: 0 }};
    if (!bizId) return;

    var url    = enable ? '{{ route("business-features.enable-all") }}' : '{{ route("business-features.disable-all") }}';
    var label  = enable ? 'Enable All' : 'Disable All';

    if (!confirm(label + ' features for this business?')) return;

    $.ajax({
        url:  url,
        type: 'POST',
        data: { _token: '{{ csrf_token() }}', business_id: bizId },
        success: function (resp) {
            if (resp.success) {
                location.reload();
            }
        },
        error: function (xhr) {
            alert('Error: ' + (xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Request failed.'));
        }
    });
}

function showBanner(msg, type) {
    var $b = $('#status-banner');
    $b.html('<div class="alert alert-' + type + ' alert-dismissible" style="padding:8px 14px;margin:0"><button type="button" class="close" data-dismiss="alert">&times;</button>' + msg + '</div>');
    $b.show();
    if (type === 'success') {
        setTimeout(function () { $b.fadeOut(400, function(){ $b.hide(); }); }, 3000);
    }
}
</script>

<style>
/* Toggle switch CSS */
.toggle-switch {
    position: relative;
    display: inline-block;
    width: 44px;
    height: 24px;
    margin: 0;
    cursor: pointer;
}
.toggle-switch input { opacity: 0; width: 0; height: 0; }
.slider {
    position: absolute;
    top: 0; left: 0; right: 0; bottom: 0;
    background: #ccc;
    border-radius: 24px;
    transition: .25s;
}
.slider:before {
    position: absolute;
    content: "";
    height: 18px; width: 18px;
    left: 3px; bottom: 3px;
    background: white;
    border-radius: 50%;
    transition: .25s;
}
input:checked + .slider { background: #27ae60; }
input:checked + .slider:before { transform: translateX(20px); }
input:disabled + .slider { opacity: .5; }
</style>

</div>{{-- .page-modern --}}
@endsection