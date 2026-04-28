@extends('layouts.app')
@section('title', __('product.add_new_product'))

@section('css')
<style>
    .section-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,.06);
        border: 1px solid #e5e7eb;
        margin-bottom: 20px;
        overflow: hidden;
    }
    .section-card-header {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 14px 20px;
        border-bottom: 1px solid #f3f4f6;
        background: #fafafa;
    }
    .section-card-header .section-number {
        width: 26px; height: 26px;
        border-radius: 50%;
        background: #4f46e5;
        color: #fff;
        font-size: 12px;
        font-weight: 700;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }
    .section-card-header h4 {
        margin: 0;
        font-size: 14px;
        font-weight: 700;
        color: #111827;
    }
    .section-card-header p {
        margin: 0;
        font-size: 12px;
        color: #6b7280;
    }
    .section-card-body { padding: 20px; }
    .form-group label { font-size: 12px; font-weight: 600; color: #374151; text-transform: uppercase; letter-spacing: .4px; margin-bottom: 5px; }
    .form-group .form-control { border-radius: 8px; border: 1px solid #d1d5db; font-size: 14px; }
    .form-group .form-control:focus { border-color: #4f46e5; box-shadow: 0 0 0 3px rgba(79,70,229,.1); }
    /* Fix Bootstrap input-group border-radius — don't override the internal corners */
    .input-group .form-control:not(:last-child) { border-radius: 8px 0 0 8px !important; }
    .input-group .form-control:not(:first-child) { border-radius: 0 8px 8px 0 !important; }
    .input-group .form-control:only-child         { border-radius: 8px !important; }
    .input-group-btn .btn { border-radius: 0 8px 8px 0 !important; border-left: 0; }
    /* Prevent input-group overflow into adjacent columns */
    .input-group { position: relative; z-index: 2; }
    .sticky-save-bar {
        position: fixed; bottom: 0; left: 0; right: 0;
        background: #fff;
        border-top: 1px solid #e5e7eb;
        padding: 12px 24px;
        z-index: 1000;
        display: flex; align-items: center; justify-content: space-between;
        box-shadow: 0 -4px 12px rgba(0,0,0,.06);
    }
    .content-wrapper { padding-bottom: 80px; }
    .advanced-toggle { cursor: pointer; user-select: none; }
    .advanced-toggle:hover .section-card-header { background: #f0f4ff; }
    .material-input-wrapper { position: relative; z-index: 1; }
    .material-input-wrapper input { position: relative; z-index: 2; background-color: transparent !important; }
    .price-section-table { width: 100%; }
    .price-section-table th { font-size: 11px; text-transform: uppercase; color: #6b7280; font-weight: 700; padding: 10px 12px; background: #f9fafb; border-bottom: 1px solid #e5e7eb; }
    .price-section-table td { padding: 14px 12px; border-bottom: 1px solid #f3f4f6; vertical-align: top; }
</style>
@endsection

@section('content')
@php
    $form_class = empty($duplicate_product) ? 'create' : '';
    $is_image_required = !empty($common_settings['is_product_image_required']);
@endphp

<section class="content-header">
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">@lang('product.add_new_product')</h1>
</section>

<section class="content">
{!! Form::open(['url' => action([\App\Http\Controllers\ProductController::class, 'store']), 'method' => 'post',
    'id' => 'product_add_form', 'class' => 'product_form ' . $form_class, 'files' => true]) !!}

{{-- ═══════════════════════════════════════════
     SECTION 1 — BASIC INFO
════════════════════════════════════════════ --}}
<div class="section-card">
    <div class="section-card-header">
        <div class="section-number">1</div>
        <div>
            <h4>Basic Information</h4>
            <p>Product identity, classification and type</p>
        </div>
    </div>
    <div class="section-card-body">
        <div class="row">
            {{-- Name --}}
            <div class="col-md-6">
                <div class="form-group">
                    {!! Form::label('name', __('product.product_name') . ' *') !!}
                    {!! Form::text('name', !empty($duplicate_product->name) ? $duplicate_product->name : null,
                        ['class' => 'form-control', 'required', 'placeholder' => __('product.product_name')]) !!}
                </div>
            </div>
            {{-- SKU --}}
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('sku', __('product.sku')) !!} @show_tooltip(__('tooltip.sku'))
                    {!! Form::text('sku', null, ['class' => 'form-control', 'placeholder' => __('product.sku')]) !!}
                </div>
            </div>
            {{-- Barcode --}}
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('barcode_type', __('product.barcode_type') . ' *') !!}
                    {!! Form::select('barcode_type', $barcode_types,
                        !empty($duplicate_product->barcode_type) ? $duplicate_product->barcode_type : $barcode_default,
                        ['class' => 'form-control select2', 'required']) !!}
                </div>
            </div>
        </div>
        <div class="row">
            {{-- Brand --}}
            <div class="col-md-3 @if(!session('business.enable_brand')) hide @endif">
                <div class="form-group">
                    {!! Form::label('brand_id', __('product.brand')) !!}
                    <div class="input-group">
                        {!! Form::select('brand_id', $brands,
                            !empty($duplicate_product->brand_id) ? $duplicate_product->brand_id : null,
                            ['placeholder' => __('messages.please_select'), 'class' => 'form-control select2']) !!}
                        <span class="input-group-btn">
                            <button type="button" @if(!auth()->user()->can('brand.create')) disabled @endif
                                class="btn btn-default bg-white btn-flat btn-modal"
                                data-href="{{ action([\App\Http\Controllers\BrandController::class, 'create'], ['quick_add' => true]) }}"
                                data-container=".view_modal">
                                <i class="fa fa-plus-circle text-primary"></i>
                            </button>
                        </span>
                    </div>
                </div>
            </div>
            {{-- Category --}}
            <div class="col-md-3 @if(!session('business.enable_category')) hide @endif">
                <div class="form-group">
                    {!! Form::label('category_id', __('product.category')) !!}
                    {!! Form::select('category_id', $categories,
                        !empty($duplicate_product->category_id) ? $duplicate_product->category_id : null,
                        ['placeholder' => __('messages.please_select'), 'class' => 'form-control select2']) !!}
                </div>
            </div>
            {{-- Sub-category --}}
            <div class="col-md-3 @if(!(session('business.enable_category') && session('business.enable_sub_category'))) hide @endif">
                <div class="form-group">
                    {!! Form::label('sub_category_id', __('product.sub_category')) !!}
                    {!! Form::select('sub_category_id', $sub_categories,
                        !empty($duplicate_product->sub_category_id) ? $duplicate_product->sub_category_id : null,
                        ['placeholder' => __('messages.please_select'), 'class' => 'form-control select2']) !!}
                </div>
            </div>
            {{-- Product Type --}}
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('type', __('product.product_type') . ' *') !!} @show_tooltip(__('tooltip.product_type'))
                    {!! Form::select('type', $product_types,
                        !empty($duplicate_product->type) ? $duplicate_product->type : null,
                        ['class' => 'form-control select2', 'required',
                         'data-action' => !empty($duplicate_product) ? 'duplicate' : 'add',
                         'data-product_id' => !empty($duplicate_product) ? $duplicate_product->id : '0']) !!}
                </div>
            </div>
        </div>
        {{-- Module form parts --}}
        @if(!empty($pos_module_data))
            @foreach($pos_module_data as $key => $value)
                @if(!empty($value['view_path']))
                    @includeIf($value['view_path'], ['view_data' => $value['view_data']])
                @endif
            @endforeach
        @endif
    </div>
</div>

{{-- ═══════════════════════════════════════════
     SECTION 2 — AVAILABILITY & STOCK
════════════════════════════════════════════ --}}
<div class="section-card">
    <div class="section-card-header">
        <div class="section-number">2</div>
        <div>
            <h4>Availability & Stock</h4>
            <p>Which branches carry this product and how stock is tracked</p>
        </div>
    </div>
    <div class="section-card-body">
        <div class="row">
            @php
                $default_location = null;
                if(count($business_locations) == 1){
                    $default_location = array_key_first($business_locations->toArray());
                }
            @endphp
            {{-- Locations --}}
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('product_locations', __('business.business_locations')) !!}
                    @show_tooltip(__('lang_v1.product_location_help'))
                    {!! Form::select('product_locations[]', $business_locations, $default_location,
                        ['class' => 'form-control select2', 'multiple', 'id' => 'product_locations']) !!}
                </div>
            </div>
            {{-- Unit --}}
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('unit_id', __('product.unit') . ' *') !!}
                    <div class="input-group">
                        {!! Form::select('unit_id', $units,
                            !empty($duplicate_product->unit_id) ? $duplicate_product->unit_id : session('business.default_unit'),
                            ['class' => 'form-control select2', 'required']) !!}
                        <span class="input-group-btn">
                            <button type="button" @if(!auth()->user()->can('unit.create')) disabled @endif
                                class="btn btn-default bg-white btn-flat btn-modal"
                                data-href="{{ action([\App\Http\Controllers\UnitController::class, 'create'], ['quick_add' => true]) }}"
                                data-container=".view_modal">
                                <i class="fa fa-plus-circle text-primary"></i>
                            </button>
                        </span>
                    </div>
                </div>
            </div>
            {{-- Sub units --}}
            <div class="col-md-4 @if(!session('business.enable_sub_units')) hide @endif">
                <div class="form-group">
                    {!! Form::label('sub_unit_ids', __('lang_v1.related_sub_units')) !!}
                    @show_tooltip(__('lang_v1.sub_units_tooltip'))
                    {!! Form::select('sub_unit_ids[]', [], !empty($duplicate_product->sub_unit_ids) ? $duplicate_product->sub_unit_ids : null,
                        ['class' => 'form-control select2', 'multiple', 'id' => 'sub_unit_ids']) !!}
                </div>
            </div>
            @if(!empty($common_settings['enable_secondary_unit']))
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('secondary_unit_id', __('lang_v1.secondary_unit')) !!}
                    @show_tooltip(__('lang_v1.secondary_unit_help'))
                    {!! Form::select('secondary_unit_id', $units,
                        !empty($duplicate_product->secondary_unit_id) ? $duplicate_product->secondary_unit_id : null,
                        ['class' => 'form-control select2']) !!}
                </div>
            </div>
            @endif
        </div>
        <div class="row">
            {{-- Manage stock --}}
            <div class="col-md-4">
                <div class="form-group">
                    <label class="tw-flex tw-items-center tw-gap-2 tw-cursor-pointer" style="text-transform:none;">
                        {!! Form::checkbox('enable_stock', 1, !empty($duplicate_product) ? $duplicate_product->enable_stock : true,
                            ['class' => 'input-icheck', 'id' => 'enable_stock']) !!}
                        <span style="font-size:13px;font-weight:600;color:#111;">@lang('product.manage_stock')</span>
                    </label>
                    @show_tooltip(__('tooltip.enable_stock'))
                    <p class="help-block" style="font-size:11px;"><i>@lang('product.enable_stock_help')</i></p>
                </div>
            </div>
            {{-- Alert quantity --}}
            <div class="col-md-4 @if(!empty($duplicate_product) && $duplicate_product->enable_stock == 0) hide @endif" id="alert_quantity_div">
                <div class="form-group">
                    {!! Form::label('alert_quantity', __('product.alert_quantity')) !!}
                    @show_tooltip(__('tooltip.alert_quantity'))
                    {!! Form::text('alert_quantity',
                        !empty($duplicate_product->alert_quantity) ? @format_quantity($duplicate_product->alert_quantity) : null,
                        ['class' => 'form-control input_number', 'placeholder' => __('product.alert_quantity'), 'min' => '0']) !!}
                </div>
            </div>
            {{-- Warranty --}}
            @if(!empty($common_settings['enable_product_warranty']))
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('warranty_id', __('lang_v1.warranty')) !!}
                    {!! Form::select('warranty_id', $warranties, null,
                        ['class' => 'form-control select2', 'placeholder' => __('messages.please_select')]) !!}
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════
     SECTION 3 — PRICING & TAX
════════════════════════════════════════════ --}}
<div class="section-card">
    <div class="section-card-header">
        <div class="section-number">3</div>
        <div>
            <h4>Pricing & Tax</h4>
            <p>Purchase cost, selling price and applicable taxes</p>
        </div>
    </div>
    <div class="section-card-body">
        <div class="row">
            {{-- Tax rate --}}
            <div class="col-md-3 @if(!session('business.enable_price_tax')) hide @endif">
                <div class="form-group">
                    {!! Form::label('tax', __('product.applicable_tax')) !!}
                    {!! Form::select('tax', $taxes,
                        !empty($duplicate_product->tax) ? $duplicate_product->tax : null,
                        ['placeholder' => __('messages.please_select'), 'class' => 'form-control select2'], $tax_attributes) !!}
                </div>
            </div>
            {{-- Tax type --}}
            <div class="col-md-3 @if(!session('business.enable_price_tax')) hide @endif">
                <div class="form-group">
                    {!! Form::label('tax_type', __('product.selling_price_tax_type') . ' *') !!}
                    {!! Form::select('tax_type',
                        ['inclusive' => __('product.inclusive'), 'exclusive' => __('product.exclusive')],
                        !empty($duplicate_product->tax_type) ? $duplicate_product->tax_type : 'exclusive',
                        ['class' => 'form-control select2', 'required']) !!}
                </div>
            </div>
            {{-- eTIMS Tax Category --}}
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('etims_tax_category', 'eTIMS Tax Category *') !!}
                    {!! Form::select('etims_tax_category',
                        ['A' => '16% (Category A)', 'B' => '8% (Category B)', 'C' => '0% (Category C)', 'D' => 'Non-Taxable (D)', 'E' => 'Exempt (E)'],
                        'A', ['class' => 'form-control select2', 'required']) !!}
                </div>
            </div>
            {{-- eTIMS UOM --}}
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('etims_uom', 'eTIMS Unit of Measure *') !!}
                    {!! Form::select('etims_uom',
                        ['U' => 'Units (U)', 'KG' => 'Kilograms (KG)', 'M' => 'Metres (M)', 'L' => 'Litres (L)'],
                        'U', ['class' => 'form-control select2', 'required']) !!}
                </div>
            </div>
        </div>

        {{-- Price table --}}
        <div class="form-group" id="product_form_part">
            @include('product.partials.single_product_form_part', ['profit_percent' => $default_profit_percent])
        </div>

        <input type="hidden" id="variation_counter" value="1">
        <input type="hidden" id="default_profit_percent" value="{{ $default_profit_percent }}">
    </div>
</div>

{{-- ═══════════════════════════════════════════
     SECTION 4 — ADVANCED (collapsed)
════════════════════════════════════════════ --}}
<div class="section-card advanced-toggle">
    <div class="section-card-header" data-toggle="collapse" data-target="#advanced-section" style="cursor:pointer;">
        <div class="section-number" style="background:#9ca3af;">4</div>
        <div style="flex:1;">
            <h4>Advanced Settings <span style="font-size:11px;font-weight:400;color:#9ca3af;">(optional)</span></h4>
            <p>Expiry, serial numbers, rack location, custom fields</p>
        </div>
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
             stroke="#9ca3af" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="6 9 12 15 18 9"/>
        </svg>
    </div>
    <div id="advanced-section" class="collapse">
        <div class="section-card-body">
            <div class="row">
                {{-- Expiry --}}
                @if(session('business.enable_product_expiry'))
                    @php
                        $expiry_period = session('business.expiry_type') == 'add_expiry' ? 12 : null;
                        $hide_expiry   = session('business.expiry_type') == 'add_expiry';
                    @endphp
                    <div class="col-md-3 @if($hide_expiry) hide @endif">
                        <div class="form-group">
                            {!! Form::label('expiry_period', __('product.expires_in')) !!}
                            <div class="input-group">
                                {!! Form::text('expiry_period',
                                    !empty($duplicate_product->expiry_period) ? @num_format($duplicate_product->expiry_period) : $expiry_period,
                                    ['class' => 'form-control input_number', 'placeholder' => __('product.expiry_period')]) !!}
                                {!! Form::select('expiry_period_type',
                                    ['months' => __('product.months'), 'days' => __('product.days'), '' => __('product.not_applicable')],
                                    !empty($duplicate_product->expiry_period_type) ? $duplicate_product->expiry_period_type : 'months',
                                    ['class' => 'form-control select2', 'id' => 'expiry_period_type', 'style' => 'border-radius:0 8px 8px 0;']) !!}
                            </div>
                        </div>
                    </div>
                @endif

                {{-- IMEI / SR No --}}
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="tw-flex tw-items-center tw-gap-2 tw-cursor-pointer" style="text-transform:none;">
                            {!! Form::checkbox('enable_sr_no', 1,
                                !(empty($duplicate_product)) ? $duplicate_product->enable_sr_no : false,
                                ['class' => 'input-icheck']) !!}
                            <span style="font-size:13px;font-weight:600;color:#111;">@lang('lang_v1.enable_imei_or_sr_no')</span>
                        </label>
                        @show_tooltip(__('lang_v1.tooltip_sr_no'))
                    </div>
                </div>

                {{-- Not for selling --}}
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="tw-flex tw-items-center tw-gap-2 tw-cursor-pointer" style="text-transform:none;">
                            {!! Form::checkbox('not_for_selling', 1,
                                !(empty($duplicate_product)) ? $duplicate_product->not_for_selling : false,
                                ['class' => 'input-icheck']) !!}
                            <span style="font-size:13px;font-weight:600;color:#111;">@lang('lang_v1.not_for_selling')</span>
                        </label>
                        @show_tooltip(__('lang_v1.tooltip_not_for_selling'))
                    </div>
                </div>

                {{-- Weight --}}
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('weight', __('lang_v1.weight')) !!}
                        {!! Form::text('weight',
                            !empty($duplicate_product->weight) ? $duplicate_product->weight : null,
                            ['class' => 'form-control', 'placeholder' => __('lang_v1.weight')]) !!}
                    </div>
                </div>

                {{-- Prep time --}}
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('preparation_time_in_minutes', __('lang_v1.preparation_time_in_minutes')) !!}
                        {!! Form::number('preparation_time_in_minutes',
                            !empty($duplicate_product->preparation_time_in_minutes) ? $duplicate_product->preparation_time_in_minutes : null,
                            ['class' => 'form-control', 'placeholder' => __('lang_v1.preparation_time_in_minutes')]) !!}
                    </div>
                </div>
            </div>

            {{-- Rack details --}}
            @if(session('business.enable_racks') || session('business.enable_row') || session('business.enable_position'))
                <hr style="border-color:#f3f4f6;margin:10px 0 16px;">
                <p style="font-size:12px;font-weight:700;text-transform:uppercase;color:#6b7280;margin-bottom:12px;">
                    @lang('lang_v1.rack_details') @show_tooltip(__('lang_v1.tooltip_rack_details'))
                </p>
                <div class="row">
                    @foreach($business_locations as $id => $location)
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('rack_' . $id, $location . ':') !!}
                                @if(session('business.enable_racks'))
                                    {!! Form::text('product_racks[' . $id . '][rack]',
                                        !empty($rack_details[$id]['rack']) ? $rack_details[$id]['rack'] : null,
                                        ['class' => 'form-control', 'placeholder' => __('lang_v1.rack')]) !!}
                                @endif
                                @if(session('business.enable_row'))
                                    {!! Form::text('product_racks[' . $id . '][row]',
                                        !empty($rack_details[$id]['row']) ? $rack_details[$id]['row'] : null,
                                        ['class' => 'form-control', 'placeholder' => __('lang_v1.row')]) !!}
                                @endif
                                @if(session('business.enable_position'))
                                    {!! Form::text('product_racks[' . $id . '][position]',
                                        !empty($rack_details[$id]['position']) ? $rack_details[$id]['position'] : null,
                                        ['class' => 'form-control', 'placeholder' => __('lang_v1.position')]) !!}
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- Custom fields --}}
            @php
                $custom_labels = json_decode(session('business.custom_labels'), true);
                $product_custom_fields = !empty($custom_labels['product']) ? $custom_labels['product'] : [];
                $product_cf_details = !empty($custom_labels['product_cf_details']) ? $custom_labels['product_cf_details'] : [];
            @endphp
            @if(array_filter($product_custom_fields))
                <hr style="border-color:#f3f4f6;margin:10px 0 16px;">
                <p style="font-size:12px;font-weight:700;text-transform:uppercase;color:#6b7280;margin-bottom:12px;">Custom Fields</p>
                <div class="row">
                    @foreach($product_custom_fields as $index => $cf)
                        @if(!empty($cf))
                            @php
                                $db_field_name = 'product_custom_field' . $loop->iteration;
                                $cf_type = !empty($product_cf_details[$loop->iteration]['type']) ? $product_cf_details[$loop->iteration]['type'] : 'text';
                                $dropdown = !empty($product_cf_details[$loop->iteration]['dropdown_options']) ? explode(PHP_EOL, $product_cf_details[$loop->iteration]['dropdown_options']) : [];
                            @endphp
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label($db_field_name, $cf) !!}
                                    @if(in_array($cf_type, ['text', 'date']))
                                        <input type="{{ $cf_type }}" name="{{ $db_field_name }}" id="{{ $db_field_name }}"
                                               value="{{ !empty($duplicate_product->$db_field_name) ? $duplicate_product->$db_field_name : null }}"
                                               class="form-control" placeholder="{{ $cf }}">
                                    @elseif($cf_type == 'dropdown')
                                        <select name="{{ $db_field_name }}" id="{{ $db_field_name }}" class="form-control select2">
                                            <option value="">{{ $cf }}</option>
                                            @foreach($dropdown as $option)
                                                <option value="{{ $option }}" @if(!empty($duplicate_product->$db_field_name) && $option == $duplicate_product->$db_field_name) selected @endif>{{ $option }}</option>
                                            @endforeach
                                        </select>
                                    @endif
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            @endif

            @include('layouts.partials.module_form_part')
        </div>
    </div>
</div>

{{-- spacer so sticky bar doesn't cover last section --}}
<div style="height:16px;"></div>

{!! Form::close() !!}
</section>

{{-- ═══════════════════════════════════════════
     STICKY SAVE BAR
════════════════════════════════════════════ --}}
<div class="sticky-save-bar no-print">
    <input type="hidden" name="submit_type" id="submit_type" form="product_add_form">
    <span class="tw-text-sm tw-text-gray-500 tw-hidden md:tw-inline">
        <i class="fa fa-info-circle"></i>&nbsp; All required fields must be filled before saving.
    </span>
    <div class="tw-flex tw-items-center tw-gap-2 tw-ml-auto">
        @if($selling_price_group_count)
            <button type="submit" form="product_add_form" name="submit_type" value="submit_n_add_selling_prices"
                class="tw-dw-btn tw-dw-btn-sm submit_product_form"
                style="background:#f59e0b;color:#fff;border:none;border-radius:8px;">
                <i class="fa fa-list-alt"></i>
                <span class="tw-hidden sm:tw-inline">@lang('lang_v1.save_n_add_selling_price_group_prices')</span>
            </button>
        @endif
        @can('product.opening_stock')
            <button id="opening_stock_button"
                @if(!empty($duplicate_product) && $duplicate_product->enable_stock == 0) disabled @endif
                type="submit" form="product_add_form" name="submit_type" value="submit_n_add_opening_stock"
                class="tw-dw-btn tw-dw-btn-sm submit_product_form"
                style="background:#8b5cf6;color:#fff;border:none;border-radius:8px;">
                <i class="fa fa-database"></i>
                <span class="tw-hidden sm:tw-inline">@lang('lang_v1.save_n_add_opening_stock')</span>
            </button>
        @endcan
        <div class="btn-group">
            <button type="submit" form="product_add_form" name="submit_type" value="submit"
                class="tw-dw-btn tw-dw-btn-sm submit_product_form"
                style="background:#4f46e5;color:#fff;border:none;border-radius:8px 0 0 8px;">
                <i class="fa fa-check-circle"></i> @lang('messages.save')
            </button>
            <button type="button" class="tw-dw-btn tw-dw-btn-sm dropdown-toggle"
                style="background:#4f46e5;color:#fff;border:none;border-left:1px solid rgba(255,255,255,.3);border-radius:0 8px 8px 0;"
                data-toggle="dropdown">
                <span class="caret"></span>
            </button>
            <ul class="dropdown-menu dropdown-menu-right">
                <li>
                    <a href="#" class="submit_product_form" onclick="$('#submit_type').val('save_n_add_another');$('#product_add_form').submit();return false;">
                        <i class="fa fa-plus-circle"></i> @lang('lang_v1.save_n_add_another')
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>

@endsection

@section('javascript')
<script src="{{ asset('js/product.js?v=' . $asset_v) }}"></script>
<script type="text/javascript">
    $(document).ready(function() {
        __page_leave_confirmation('#product_add_form');
        onScan.attachTo(document, {
            suffixKeyCodes: [13],
            reactToPaste: true,
            onScan: function(sCode) { $('input#sku').val(sCode); },
            minLength: 2,
            ignoreIfFocusOn: ['input', '.form-control']
        });
    });
</script>
@endsection
