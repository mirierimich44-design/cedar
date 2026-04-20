@extends('layouts.app')
@section('title', 'Features — ' . $business->name)

@section('content')
<section class="content-header">
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">Feature Toggles
        <small class="tw-text-sm tw-text-gray-600">{{ $business->name }}</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('saas-admin.index') }}">SaaS Admin</a></li>
        <li class="active">{{ $business->name }}</li>
    </ol>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-8">
            {!! Form::open(['route' => ['saas-admin.update-features', $business->id], 'method' => 'POST']) !!}

            @foreach($all_modules as $category => $modules)
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">{{ $category }}</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-xs btn-default toggle-all-btn" data-category="{{ Str::slug($category) }}">
                            Toggle All
                        </button>
                    </div>
                </div>
                <div class="box-body" id="cat-{{ Str::slug($category) }}">
                    <div class="row">
                        @foreach($modules as $key => $module)
                        <div class="col-sm-6 col-md-4">
                            <div class="tw-flex tw-items-center tw-gap-3 tw-p-3 tw-rounded-xl tw-border tw-mb-2
                                {{ in_array($key, $enabled_modules) ? 'tw-bg-green-50 tw-border-green-300' : 'tw-bg-gray-50 tw-border-gray-200' }}
                                feature-item" data-category="{{ Str::slug($category) }}">
                                <label class="tw-flex tw-items-center tw-gap-2 tw-cursor-pointer tw-m-0 tw-flex-1">
                                    <input type="checkbox"
                                        name="enabled_modules[]"
                                        value="{{ $key }}"
                                        class="feature-checkbox tw-w-5 tw-h-5"
                                        {{ in_array($key, $enabled_modules) ? 'checked' : '' }}>
                                    <span>
                                        <i class="fa {{ $module['icon'] ?? 'fa-puzzle-piece' }} tw-text-blue-600 tw-mr-1"></i>
                                        <strong>{{ $module['name'] }}</strong>
                                    </span>
                                </label>
                                <span class="tw-text-xs {{ in_array($key, $enabled_modules) ? 'tw-text-green-600' : 'tw-text-gray-400' }} status-text">
                                    {{ in_array($key, $enabled_modules) ? 'ON' : 'OFF' }}
                                </span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endforeach

            <div class="box box-default">
                <div class="box-body">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fa fa-save"></i> Save Feature Settings
                    </button>
                    <a href="{{ route('saas-admin.index') }}" class="btn btn-default btn-lg tw-ml-2">
                        <i class="fa fa-arrow-left"></i> Back
                    </a>
                </div>
            </div>

            {!! Form::close() !!}
        </div>

        <!-- Right: Business Summary -->
        <div class="col-md-4">
            <div class="box box-info">
                <div class="box-header with-border"><h3 class="box-title">Business Info</h3></div>
                <div class="box-body">
                    <p><strong>Name:</strong> {{ $business->name }}</p>
                    <p><strong>Type:</strong> {{ ucfirst($business->business_type ?? '—') }}</p>
                    <p><strong>Owner:</strong> {{ optional($business->owner)->first_name }} {{ optional($business->owner)->last_name }}</p>
                    <p><strong>Registered:</strong> {{ $business->created_at ? $business->created_at->format('d M Y') : '—' }}</p>
                    <hr>
                    <p><strong>Currently Enabled:</strong></p>
                    @foreach($enabled_modules as $mod)
                    <span class="label label-success tw-mr-1 tw-mb-1 tw-inline-block">{{ $mod }}</span>
                    @endforeach
                </div>
            </div>

            <!-- Quick presets -->
            <div class="box box-default">
                <div class="box-header with-border"><h3 class="box-title">Quick Presets</h3></div>
                <div class="box-body">
                    <p class="text-muted text-sm">Click a preset to select that business type's default features:</p>
                    @php
                    $presets = [
                        'retail'     => ['label' => '🛒 Retail',    'modules' => ['purchases','add_sale','pos_sale','stock_transfers','stock_adjustment','expenses','account']],
                        'restaurant' => ['label' => '🍽️ Restaurant', 'modules' => ['add_sale','pos_sale','tables','modifiers','kitchen','service_staff','expenses','account']],
                        'courier'    => ['label' => '📦 Courier',   'modules' => ['parcels','expenses','account']],
                        'clinic'     => ['label' => '🏥 Clinic',    'modules' => ['hospital_billing','expenses','account']],
                    ];
                    @endphp
                    @foreach($presets as $pk => $preset)
                    <button type="button" class="btn btn-sm btn-default btn-block tw-mb-1 preset-btn"
                        data-modules='@json($preset["modules"])'>
                        {{ $preset['label'] }}
                    </button>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('javascript')
<script>
$(function() {
    // Live ON/OFF toggle display
    $(document).on('change', '.feature-checkbox', function() {
        var $item    = $(this).closest('.feature-item');
        var checked  = $(this).is(':checked');
        $item.toggleClass('tw-bg-green-50 tw-border-green-300', checked);
        $item.toggleClass('tw-bg-gray-50 tw-border-gray-200', !checked);
        $item.find('.status-text')
            .text(checked ? 'ON' : 'OFF')
            .toggleClass('tw-text-green-600', checked)
            .toggleClass('tw-text-gray-400', !checked);
    });

    // Toggle all in category
    $('.toggle-all-btn').on('click', function() {
        var cat      = $(this).data('category');
        var allBoxes = $('#cat-' + cat).find('.feature-checkbox');
        var anyUnchecked = allBoxes.filter(':not(:checked)').length > 0;
        allBoxes.each(function() {
            $(this).prop('checked', anyUnchecked).trigger('change');
        });
    });

    // Presets
    $('.preset-btn').on('click', function() {
        var modules = $(this).data('modules');
        $('.feature-checkbox').each(function() {
            var shouldCheck = modules.indexOf($(this).val()) !== -1;
            $(this).prop('checked', shouldCheck).trigger('change');
        });
    });
});
</script>
@endsection
