@extends('layouts.app')
@section('title', __('purchase.add_purchase'))

@section('content')

@php
	$custom_labels = json_decode(session('business.custom_labels'), true);
@endphp
@include('purchase.partials.purchase_slim_styles')
<!-- Content Header (Page header) -->
<section class="content-header">
    <div class="tw-flex tw-justify-between tw-items-center">
        <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black use_ai_btn">@lang('purchase.add_purchase') <i class="fa fa-keyboard hover-q text-muted" aria-hidden="true" data-container="body" data-toggle="popover" data-placement="bottom" data-content="@include('purchase.partials.keyboard_shortcuts_details')" data-html="true" data-trigger="hover" data-original-title="" title=""></i></h1>
    </div>
</section>

<!-- Main content -->
<section class="content">

	<!-- Page level currency setting -->
	<input type="hidden" id="p_code" value="{{$currency_details->code}}">
	<input type="hidden" id="p_symbol" value="{{$currency_details->symbol}}">
	<input type="hidden" id="p_thousand" value="{{$currency_details->thousand_separator}}">
	<input type="hidden" id="p_decimal" value="{{$currency_details->decimal_separator}}">

	@include('layouts.partials.error')

	{{-- AI Invoice Scanner trigger --}}
	<div style="margin-bottom:16px;">
		<button type="button" class="btn btn-info" id="btn_scan_invoice" style="border-radius:8px;font-weight:600;padding:8px 18px;">
			<i class="fas fa-magic"></i> &nbsp;Scan Invoice with AI
		</button>
		<small class="text-muted" style="margin-left:10px;">Upload an invoice image and Kimi AI will extract and match all products automatically.</small>
	</div>

	{!! Form::open(['url' => action([\App\Http\Controllers\PurchaseController::class, 'store']), 'method' => 'post', 'id' => 'add_purchase_form', 'files' => true ]) !!}
	@component('components.widget', ['class' => 'box-primary purchase-meta-card'])
		<div class="row">
			<div class="@if(!empty($default_purchase_status)) col-sm-4 @else col-sm-3 @endif">
				<div class="form-group">
					{!! Form::label('supplier_id', __('purchase.supplier') . ':*') !!}
					<div class="input-group">
						<span class="input-group-addon">
							<i class="fa fa-user"></i>
						</span>
						{!! Form::select('contact_id', [], null, ['class' => 'form-control', 'placeholder' => __('messages.please_select'), 'required', 'id' => 'supplier_id']); !!}
						<span class="input-group-btn">
							<button type="button" class="btn btn-default bg-white btn-flat add_new_supplier" data-name=""><i class="fa fa-plus-circle text-primary fa-lg"></i></button>
						</span>
					</div>
				</div>
				<span class="purchase-addr-label">@lang('business.address')</span>
				<div id="supplier_address_div"></div>
			</div>
			<div class="@if(!empty($default_purchase_status)) col-sm-4 @else col-sm-3 @endif">
				<div class="form-group">
					{!! Form::label('ref_no', __('purchase.ref_no').':') !!}
					@show_tooltip(__('lang_v1.leave_empty_to_autogenerate'))
					{!! Form::text('ref_no', null, ['class' => 'form-control']); !!}
				</div>
			</div>
			<div class="@if(!empty($default_purchase_status)) col-sm-4 @else col-sm-3 @endif">
				<div class="form-group">
					{!! Form::label('transaction_date', __('purchase.purchase_date') . ':*') !!}
					<div class="input-group">
						<span class="input-group-addon">
							<i class="fa fa-calendar"></i>
						</span>
						{!! Form::text('transaction_date', @format_datetime('now'), ['class' => 'form-control', 'readonly', 'required']); !!}
					</div>
				</div>
			</div>
			<div class="col-sm-3 @if(!empty($default_purchase_status)) hide @endif">
				<div class="form-group">
					{!! Form::label('status', __('purchase.purchase_status') . ':*') !!} @show_tooltip(__('tooltip.order_status'))
					{!! Form::select('status', $orderStatuses, $default_purchase_status, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'required']); !!}
				</div>
			</div>			
			@if(count($business_locations) == 1)
				@php 
					$default_location = current(array_keys($business_locations->toArray()));
					$search_disable = false; 
				@endphp
			@else
				@php $default_location = null;
				$search_disable = true;
				@endphp
			@endif
			<div class="col-sm-3">
				<div class="form-group">
					{!! Form::label('location_id', __('purchase.business_location').':*') !!}
					@show_tooltip(__('tooltip.purchase_location'))
					{!! Form::select('location_id', $business_locations, $default_location, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'required'], $bl_attributes); !!}
				</div>
			</div>

			<!-- Currency Exchange Rate -->
			<div class="col-sm-3 @if(!$currency_details->purchase_in_diff_currency) hide @endif">
				<div class="form-group">
					{!! Form::label('exchange_rate', __('purchase.p_exchange_rate') . ':*') !!}
					@show_tooltip(__('tooltip.currency_exchange_factor'))
					<div class="input-group">
						<span class="input-group-addon">
							<i class="fa fa-info"></i>
						</span>
						{!! Form::number('exchange_rate', $currency_details->p_exchange_rate, ['class' => 'form-control', 'required', 'step' => 0.001]); !!}
					</div>
					<span class="help-block text-danger">
						@lang('purchase.diff_purchase_currency_help', ['currency' => $currency_details->name])
					</span>
				</div>
			</div>

			<div class="col-md-3">
		          <div class="form-group">
		            <div class="multi-input">
		              {!! Form::label('pay_term_number', __('contact.pay_term') . ':') !!} @show_tooltip(__('tooltip.pay_term'))
		              <br/>
		              {!! Form::number('pay_term_number', null, ['class' => 'form-control width-40 pull-left', 'min' => 0, 'placeholder' => __('contact.pay_term')]); !!}

		              {!! Form::select('pay_term_type', 
		              	['months' => __('lang_v1.months'), 
		              		'days' => __('lang_v1.days')], 
		              		null, 
		              	['class' => 'form-control width-60 pull-left','placeholder' => __('messages.please_select'), 'id' => 'pay_term_type']); !!}
		            </div>
		        </div>
		    </div>

{{-- <div class="col-sm-3">
    <div class="form-group">
        {!! Form::label('document', __('purchase.attach_document') . ':') !!}
        {!! Form::file('document', ['id' => 'upload_document', 'accept' => implode(',', array_keys(config('constants.document_upload_mimes_types')))]); !!}
        <p class="help-block">
            @lang('purchase.max_file_size', ['size' => (config('constants.document_size_limit') / 1000000)])
            @includeIf('components.document_help_text')
        </p>
    </div>
</div> --}}
		</div>
		<div class="row">
			@php
		    $custom_field_1_label = !empty($custom_labels['purchase']['custom_field_1']) ? $custom_labels['purchase']['custom_field_1'] : '';

		    $is_custom_field_1_required = !empty($custom_labels['purchase']['is_custom_field_1_required']) && $custom_labels['purchase']['is_custom_field_1_required'] == 1 ? true : false;

		    $custom_field_2_label = !empty($custom_labels['purchase']['custom_field_2']) ? $custom_labels['purchase']['custom_field_2'] : '';

		    $is_custom_field_2_required = !empty($custom_labels['purchase']['is_custom_field_2_required']) && $custom_labels['purchase']['is_custom_field_2_required'] == 1 ? true : false;

		    $custom_field_3_label = !empty($custom_labels['purchase']['custom_field_3']) ? $custom_labels['purchase']['custom_field_3'] : '';

		    $is_custom_field_3_required = !empty($custom_labels['purchase']['is_custom_field_3_required']) && $custom_labels['purchase']['is_custom_field_3_required'] == 1 ? true : false;

		    $custom_field_4_label = !empty($custom_labels['purchase']['custom_field_4']) ? $custom_labels['purchase']['custom_field_4'] : '';

		    $is_custom_field_4_required = !empty($custom_labels['purchase']['is_custom_field_4_required']) && $custom_labels['purchase']['is_custom_field_4_required'] == 1 ? true : false;
		@endphp
		@if(!empty($custom_field_1_label))
			@php
				$label_1 = $custom_field_1_label . ':';
				if($is_custom_field_1_required) {
					$label_1 .= '*';
				}
			@endphp

			<div class="col-md-4">
		        <div class="form-group">
		            {!! Form::label('custom_field_1', $label_1 ) !!}
		            {!! Form::text('custom_field_1', null, ['class' => 'form-control','placeholder' => $custom_field_1_label, 'required' => $is_custom_field_1_required]); !!}
		        </div>
		    </div>
		@endif
		@if(!empty($custom_field_2_label))
			@php
				$label_2 = $custom_field_2_label . ':';
				if($is_custom_field_2_required) {
					$label_2 .= '*';
				}
			@endphp

			<div class="col-md-4">
		        <div class="form-group">
		            {!! Form::label('custom_field_2', $label_2 ) !!}
		            {!! Form::text('custom_field_2', null, ['class' => 'form-control','placeholder' => $custom_field_2_label, 'required' => $is_custom_field_2_required]); !!}
		        </div>
		    </div>
		@endif
		@if(!empty($custom_field_3_label))
			@php
				$label_3 = $custom_field_3_label . ':';
				if($is_custom_field_3_required) {
					$label_3 .= '*';
				}
			@endphp

			<div class="col-md-4">
		        <div class="form-group">
		            {!! Form::label('custom_field_3', $label_3 ) !!}
		            {!! Form::text('custom_field_3', null, ['class' => 'form-control','placeholder' => $custom_field_3_label, 'required' => $is_custom_field_3_required]); !!}
		        </div>
		    </div>
		@endif
		@if(!empty($custom_field_4_label))
			@php
				$label_4 = $custom_field_4_label . ':';
				if($is_custom_field_4_required) {
					$label_4 .= '*';
				}
			@endphp

			<div class="col-md-4">
		        <div class="form-group">
		            {!! Form::label('custom_field_4', $label_4 ) !!}
		            {!! Form::text('custom_field_4', null, ['class' => 'form-control','placeholder' => $custom_field_4_label, 'required' => $is_custom_field_4_required]); !!}
		        </div>
		    </div>
		@endif
		</div>
		@if(!empty($common_settings['enable_purchase_order']))
		<div class="row">
			<div class="col-sm-3">
				<div class="form-group">
					{!! Form::label('purchase_order_ids', __('lang_v1.purchase_order').':') !!}
					{!! Form::select('purchase_order_ids[]', [], null, ['class' => 'form-control select2', 'multiple', 'id' => 'purchase_order_ids']); !!}
				</div>
			</div>
		</div>
		@endif
	@endcomponent

	@component('components.widget', ['class' => 'box-primary'])
		{{-- Draft restore banner (shown when returning from Drafts page) --}}
		<div id="purchase-draft-banner" style="display:none;background:var(--theme-subtle,#eef2ff);border:1px solid var(--theme-border,#c7d2fe);border-radius:8px;padding:9px 16px;margin-bottom:10px;align-items:center;gap:10px;font-size:13px;">
			<i class="fa fa-clock-o" style="color:var(--theme-main);"></i>
			<span id="draft-banner-text" style="flex:1;color:#374151;"></span>
			<a href="#" id="draft-restore-btn" style="color:var(--theme-main);font-weight:600;text-decoration:none;margin-left:8px;">Restore it</a>
			<span style="color:#9ca3af;margin:0 6px;">|</span>
			<a href="{{ route('purchases.drafts') }}" id="draft-discard-btn" style="color:#9ca3af;font-weight:500;text-decoration:none;">View Drafts</a>
		</div>

<div class="row tw-sticky !tw-sticky tw-top-0 tw-z-[99] purchase-search-strip">
			<div class="col-sm-12 missing-product-warning">
			</div>
			<div class="col-sm-2 text-center">
				<button type="button" class="tw-dw-btn tw-dw-btn-primary tw-text-white tw-dw-btn-sm" data-toggle="modal" data-target="#import_purchase_products_modal">@lang('product.import_products')</button>
			</div>
			<div class="col-sm-8">
				<div class="form-group">
					<div class="input-group">
						<span class="input-group-addon">
							<i class="fa fa-search"></i>
						</span>
						{!! Form::text('search_product', null, ['class' => 'form-control mousetrap', 'id' => 'search_product', 'placeholder' => __('lang_v1.search_product_placeholder'), 'disabled' => $search_disable]); !!}
					</div>
				</div>
			</div>
			<div class="col-sm-2">
				<div class="form-group">
					<button tabindex="-1" type="button" class="btn btn-link btn-modal"data-href="{{action([\App\Http\Controllers\ProductController::class, 'quickAdd'])}}" 
            	data-container=".quick_add_product_modal"><i class="fa fa-plus"></i> @lang( 'product.add_new_product' ) </button>
				</div>
			</div>
		</div>
		@php
			$hide_tax = '';
			if( session()->get('business.enable_inline_tax') == 0){
				$hide_tax = 'hide';
			}
		@endphp
		<div class="row">
			<div class="col-sm-12">
			<div class="table-responsive">
				<table class="table table-condensed table-bordered table-th-green text-center table-striped purchase-slim" id="purchase_entry_table">
					<thead>
						<tr>
							<th style="width:40px;">#</th>
							<th class="text-left">@lang('product.product_name')</th>
							<th style="width:90px;">Qty</th>
							<th style="width:120px;">Unit Cost</th>
							<th class="hide">Disc %</th>
							<th class="hide">Sub Total</th>
							<th class="hide">Tax %</th>
							<th style="width:110px;">Line Total</th>
							<th class="hide">@lang('lang_v1.unit_cost_before_discount')</th>
							<th class="hide">@lang('lang_v1.discount_percent')</th>
							<th class="hide">@lang('purchase.net_cost')</th>
							<th class="hide">Margin %</th>
							<th class="hide">Sell Price</th>
							@if(session('business.enable_lot_number'))
								<th class="hide">@lang('lang_v1.lot_number')</th>
							@endif
							@if(true || session('business.enable_product_expiry'))
								<th class="hide">MFG / EXP</th>
							@endif
							<th style="width:100px;">Details</th>
							<th style="width:40px;"><i class="fa fa-trash" aria-hidden="true"></i></th>
						</tr>
					</thead>
					<tbody></tbody>
				</table>
			</div>

				{{-- Hidden totals still required by purchase.js --}}
				<div class="hide">
					<span id="total_st_before_tax" class="display_currency"></span>
					<input type="hidden" id="st_before_tax_input" value=0>
					<span id="total_subtotal" class="display_currency"></span>
					<input type="hidden" id="total_subtotal_input" value=0 name="total_before_tax">
				</div>

				<input type="hidden" id="row_count" value="0">
			</div>
		</div>
	@endcomponent

	@component('components.widget', ['class' => 'box-primary hide'])
		<div class="row">
			<div class="col-sm-12">
			<table class="table">
				<tr class="hide">
					<td class="col-md-3">
						<div class="form-group">
							{!! Form::label('discount_type', __( 'purchase.discount_type' ) . ':') !!}
							{!! Form::select('discount_type', [ '' => __('lang_v1.none'), 'fixed' => __( 'lang_v1.fixed' ), 'percentage' => __( 'lang_v1.percentage' )], '', ['class' => 'form-control select2']); !!}
						</div>
					</td>
					<td class="col-md-3">
						<div class="form-group">
						{!! Form::label('discount_amount', __( 'purchase.discount_amount' ) . ':') !!}
						{!! Form::text('discount_amount', 0, ['class' => 'form-control input_number', 'required']); !!}
						</div>
					</td>
					<td class="col-md-3">
						&nbsp;
					</td>
					<td class="col-md-3">
						<b>@lang( 'purchase.discount' ):</b>(-) 
						<span id="discount_calculated_amount" class="display_currency">0</span>
					</td>
				</tr>
				<tr class="hide">
					<td>
						<div class="form-group">
						{!! Form::label('tax_id', __('purchase.purchase_tax') . ':') !!}
						<select name="tax_id" id="tax_id" class="form-control select2" placeholder="'Please Select'">
							<option value="" data-tax_amount="0" data-tax_type="fixed" selected>@lang('lang_v1.none')</option>
							@foreach($taxes as $tax)
								<option value="{{ $tax->id }}" data-tax_amount="{{ $tax->amount }}" data-tax_type="{{ $tax->calculation_type }}">{{ $tax->name }}</option>
							@endforeach
						</select>
						{!! Form::hidden('tax_amount', 0, ['id' => 'tax_amount']); !!}
						</div>
					</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>
						<b>@lang( 'purchase.purchase_tax' ):</b>(+) 
						<span id="tax_calculated_amount" class="display_currency">0</span>
					</td>
				</tr>
{{-- <tr>
    <td colspan="4">
        <div class="form-group">
            {!! Form::label('additional_notes',__('purchase.additional_notes')) !!}
            {!! Form::textarea('additional_notes', null, ['class' => 'form-control', 'rows' => 3]); !!}
        </div>
    </td>
</tr> --}}

			{!! Form::hidden('additional_notes', null); !!}
			</table>
			</div>
		</div>
	@endcomponent
	@component('components.widget', ['class' => 'box-primary hide'])
	<div class="row">
{!! Form::hidden('shipping_details', null); !!}
{!! Form::hidden('shipping_charges', 0); !!}
	</div>
	<div class="row">
			@php
			    $shipping_custom_label_1 = !empty($custom_labels['purchase_shipping']['custom_field_1']) ? $custom_labels['purchase_shipping']['custom_field_1'] : '';

			    $is_shipping_custom_field_1_required = !empty($custom_labels['purchase_shipping']['is_custom_field_1_required']) && $custom_labels['purchase_shipping']['is_custom_field_1_required'] == 1 ? true : false;

			    $shipping_custom_label_2 = !empty($custom_labels['purchase_shipping']['custom_field_2']) ? $custom_labels['purchase_shipping']['custom_field_2'] : '';

			    $is_shipping_custom_field_2_required = !empty($custom_labels['purchase_shipping']['is_custom_field_2_required']) && $custom_labels['purchase_shipping']['is_custom_field_2_required'] == 1 ? true : false;

			    $shipping_custom_label_3 = !empty($custom_labels['purchase_shipping']['custom_field_3']) ? $custom_labels['purchase_shipping']['custom_field_3'] : '';
			    
			    $is_shipping_custom_field_3_required = !empty($custom_labels['purchase_shipping']['is_custom_field_3_required']) && $custom_labels['purchase_shipping']['is_custom_field_3_required'] == 1 ? true : false;

			    $shipping_custom_label_4 = !empty($custom_labels['purchase_shipping']['custom_field_4']) ? $custom_labels['purchase_shipping']['custom_field_4'] : '';
			    
			    $is_shipping_custom_field_4_required = !empty($custom_labels['purchase_shipping']['is_custom_field_4_required']) && $custom_labels['purchase_shipping']['is_custom_field_4_required'] == 1 ? true : false;

			    $shipping_custom_label_5 = !empty($custom_labels['purchase_shipping']['custom_field_5']) ? $custom_labels['purchase_shipping']['custom_field_5'] : '';
			    
			    $is_shipping_custom_field_5_required = !empty($custom_labels['purchase_shipping']['is_custom_field_5_required']) && $custom_labels['purchase_shipping']['is_custom_field_5_required'] == 1 ? true : false;
			@endphp

			@if(!empty($shipping_custom_label_1))
				@php
					$label_1 = $shipping_custom_label_1 . ':';
					if($is_shipping_custom_field_1_required) {
						$label_1 .= '*';
					}
				@endphp

				<div class="col-md-4">
			        <div class="form-group">
			            {!! Form::label('shipping_custom_field_1', $label_1 ) !!}
			            {!! Form::text('shipping_custom_field_1', null, ['class' => 'form-control','placeholder' => $shipping_custom_label_1, 'required' => $is_shipping_custom_field_1_required]); !!}
			        </div>
			    </div>
			@endif
			@if(!empty($shipping_custom_label_2))
				@php
					$label_2 = $shipping_custom_label_2 . ':';
					if($is_shipping_custom_field_2_required) {
						$label_2 .= '*';
					}
				@endphp

				<div class="col-md-4">
			        <div class="form-group">
			            {!! Form::label('shipping_custom_field_2', $label_2 ) !!}
			            {!! Form::text('shipping_custom_field_2', null, ['class' => 'form-control','placeholder' => $shipping_custom_label_2, 'required' => $is_shipping_custom_field_2_required]); !!}
			        </div>
			    </div>
			@endif
			@if(!empty($shipping_custom_label_3))
				@php
					$label_3 = $shipping_custom_label_3 . ':';
					if($is_shipping_custom_field_3_required) {
						$label_3 .= '*';
					}
				@endphp

				<div class="col-md-4">
			        <div class="form-group">
			            {!! Form::label('shipping_custom_field_3', $label_3 ) !!}
			            {!! Form::text('shipping_custom_field_3', null, ['class' => 'form-control','placeholder' => $shipping_custom_label_3, 'required' => $is_shipping_custom_field_3_required]); !!}
			        </div>
			    </div>
			@endif
			@if(!empty($shipping_custom_label_4))
				@php
					$label_4 = $shipping_custom_label_4 . ':';
					if($is_shipping_custom_field_4_required) {
						$label_4 .= '*';
					}
				@endphp

				<div class="col-md-4">
			        <div class="form-group">
			            {!! Form::label('shipping_custom_field_4', $label_4 ) !!}
			            {!! Form::text('shipping_custom_field_4', null, ['class' => 'form-control','placeholder' => $shipping_custom_label_4, 'required' => $is_shipping_custom_field_4_required]); !!}
			        </div>
			    </div>
			@endif
			@if(!empty($shipping_custom_label_5))
				@php
					$label_5 = $shipping_custom_label_5 . ':';
					if($is_shipping_custom_field_5_required) {
						$label_5 .= '*';
					}
				@endphp

				<div class="col-md-4">
			        <div class="form-group">
			            {!! Form::label('shipping_custom_field_5', $label_5 ) !!}
			            {!! Form::text('shipping_custom_field_5', null, ['class' => 'form-control','placeholder' => $shipping_custom_label_5, 'required' => $is_shipping_custom_field_5_required]); !!}
			        </div>
			    </div>
			@endif
		</div>
		<div class="row">
			<div class="col-md-12">
				<h4 class="tw-font-bold tw-text-lg tw-mb-4">@lang('lang_v1.purchase_additional_expense'):</h4>
			</div>
			<div class="col-md-8 col-md-offset-4" id="additional_expenses_div">
				<table class="table table-condensed">
					<thead>
						<tr>
							<th>@lang('lang_v1.additional_expense_name')</th>
							<th>@lang('sale.amount')</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>
								{!! Form::text('additional_expense_key_1', null, ['class' => 'form-control', 'id' => 'additional_expense_key_1']); !!}
							</td>
							<td>
								{!! Form::text('additional_expense_value_1', null, ['class' => 'form-control input_number', 'id' => 'additional_expense_value_1']); !!}
							</td>
						</tr>
						<tr>
							<td>
								{!! Form::text('additional_expense_key_2', null, ['class' => 'form-control', 'id' => 'additional_expense_key_2']); !!}
							</td>
							<td>
								{!! Form::text('additional_expense_value_2', null, ['class' => 'form-control input_number', 'id' => 'additional_expense_value_2']); !!}
							</td>
						</tr>
						<tr>
							<td>
								{!! Form::text('additional_expense_key_3', null, ['class' => 'form-control', 'id' => 'additional_expense_key_3']); !!}
							</td>
							<td>
								{!! Form::text('additional_expense_value_3', null, ['class' => 'form-control input_number', 'id' => 'additional_expense_value_3']); !!}
							</td>
						</tr>
						<tr>
							<td>
								{!! Form::text('additional_expense_key_4', null, ['class' => 'form-control', 'id' => 'additional_expense_key_4']); !!}
							</td>
							<td>
								{!! Form::text('additional_expense_value_4', null, ['class' => 'form-control input_number', 'id' => 'additional_expense_value_4']); !!}
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
		{!! Form::hidden('final_total', 0 , ['id' => 'grand_total_hidden']); !!}
	@endcomponent
	@component('components.widget', ['class' => 'box-primary', 'title' => __('purchase.add_payment')])
		<div class="box-body payment_row">
			<div class="row">
				<div class="col-md-12">
					<strong>@lang('lang_v1.advance_balance'):</strong> <span id="advance_balance_text">0</span>
					{!! Form::hidden('advance_balance', null, ['id' => 'advance_balance', 'data-error-msg' => __('lang_v1.required_advance_balance_not_available')]); !!}
				</div>
			</div>
			@include('sale_pos.partials.payment_row_form', ['row_index' => 0, 'show_date' => true, 'show_denomination' => true])
		</div>
	@endcomponent

	<div class="purchase-sticky-footer">
		<div class="psf-stats">
			<span>@lang('lang_v1.total_items'): <strong><span id="total_quantity" class="display_currency" data-currency_symbol="false">0</span></strong></span>
			<span>@lang('purchase.payment_due'): <strong><span id="payment_due">0.00</span></strong></span>
		</div>
		<div class="psf-total">
			<small>@lang('purchase.purchase_total')</small>
			<span id="grand_total" class="display_currency" data-currency_symbol='true'>0</span>
		</div>
		<div class="psf-actions">
			<button type="button" id="submit_purchase_form" class="tw-dw-btn tw-dw-btn-primary tw-dw-btn-lg tw-text-white">
				<i class="fa fa-check"></i> @lang('messages.save')
			</button>
		</div>
	</div>

{!! Form::close() !!}
</section>
<!-- quick product modal -->
<div class="modal fade quick_add_product_modal" tabindex="-1" role="dialog" aria-labelledby="modalTitle"></div>
<div class="modal fade contact_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
	@include('contact.create', ['quick_add' => true])
</div>

@include('purchase.partials.import_purchase_products_modal')

{{-- ── Kimi AI Invoice Scanner Modal ──────────────────────────────────── --}}
<div class="modal fade" id="scan_invoice_modal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header" style="background:linear-gradient(135deg,#4f46e5,#7c3aed);color:#fff;">
        <button type="button" class="close" data-dismiss="modal" style="color:#fff;opacity:1;">&times;</button>
        <h4 class="modal-title"><i class="fas fa-magic"></i> &nbsp;Kimi AI — Invoice Scanner</h4>
      </div>
      <div class="modal-body" id="scan_modal_body">

        {{-- Step 1: Upload --}}
        <div id="scan_step_upload">
          {{-- label wraps the input so clicking anywhere on the zone opens file picker natively --}}
          <label for="scan_file_input" id="scan_dropzone" style="border:2px dashed #c7d2fe;border-radius:12px;padding:40px;text-align:center;cursor:pointer;background:#f5f3ff;transition:background .2s;display:block;margin:0;">
            <i class="fas fa-cloud-upload-alt" style="font-size:40px;color:#6366f1;"></i>
            <p style="margin:12px 0 4px;font-size:16px;font-weight:600;color:#312e81;">Drop invoice image here</p>
            <p style="color:#94a3b8;font-size:13px;">or click to browse &mdash; JPG, PNG, WEBP, PDF &mdash; max 10 MB</p>
            <input type="file" id="scan_file_input" accept=".jpg,.jpeg,.png,.webp,.pdf" style="display:none;">
          </label>
          <div id="scan_file_preview" style="display:none;margin-top:12px;padding:10px 14px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;">
            <div style="display:flex;align-items:center;gap:10px;">
              <i class="fas fa-file-image" style="color:#6366f1;font-size:20px;"></i>
              <span id="scan_file_name" style="font-size:13px;font-weight:500;color:#1e293b;flex:1;"></span>
              <button type="button" id="scan_clear_file" class="btn btn-xs btn-default"><i class="fas fa-times"></i></button>
            </div>
          </div>
          <div style="margin-top:16px;text-align:right;">
            <button type="button" class="btn btn-primary" id="btn_extract_invoice" disabled style="border-radius:8px;padding:8px 22px;font-weight:600;">
              <i class="fas fa-magic"></i> Extract with Kimi
            </button>
          </div>
        </div>

        {{-- Step 2: Extracting --}}
        <div id="scan_step_extracting" style="display:none;text-align:center;padding:50px 20px;">
          <i class="fas fa-robot" style="font-size:48px;color:#6366f1;animation:pulse 1.5s infinite;"></i>
          <p style="margin-top:16px;font-size:16px;font-weight:600;color:#312e81;">Kimi is reading your invoice…</p>
          <p style="color:#94a3b8;font-size:13px;">Extracting products, prices, supplier &mdash; usually takes 5–15 seconds</p>
          <div class="progress" style="margin-top:20px;height:6px;border-radius:3px;">
            <div class="progress-bar progress-bar-striped active" style="width:100%;background:#6366f1;"></div>
          </div>
        </div>

        {{-- Step 3: Review --}}
        <div id="scan_step_review" style="display:none;">
          <div id="scan_review_content"></div>
          <div style="margin-top:16px;display:flex;justify-content:space-between;align-items:center;">
            <button type="button" class="btn btn-default" id="btn_rescan"><i class="fas fa-redo"></i> Scan Again</button>
            <button type="button" class="btn btn-success" id="btn_apply_invoice" style="border-radius:8px;padding:8px 22px;font-weight:600;">
              <i class="fas fa-check"></i> Apply to Purchase Form
            </button>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>
<!-- /.content -->

@include('purchase.partials.purchase_line_details_modal')
@endsection


@section('javascript')
	<script src="{{ asset('js/purchase.js?v=' . $asset_v) }}"></script>
	<script src="{{ asset('js/product.js?v=' . $asset_v) }}"></script>
	<script type="text/javascript">
		$(document).ready( function(){
      		__page_leave_confirmation('#add_purchase_form');
      		$('.paid_on').datetimepicker({
                format: moment_date_format + ' ' + moment_time_format,
                ignoreReadonly: true,
            });

			if($('.payment_types_dropdown').length){
				$('.payment_types_dropdown').change();
			}
			set_payment_type_dropdown();
			$('select#location_id').change(function() {
				set_payment_type_dropdown();
				// Enable product search once a location is selected
				if ($(this).val()) {
					$('#search_product').prop('disabled', false).focus();
				} else {
					$('#search_product').prop('disabled', true);
				}
			});
			// If location is already pre-selected (single location), ensure search is enabled
			if ($('select#location_id').val()) {
				$('#search_product').prop('disabled', false);
			}
    	});
    	$(document).on('change', '.payment_types_dropdown, #location_id', function(e) {
		    var default_accounts = $('select#location_id').length ? 
		                $('select#location_id')
		                .find(':selected')
		                .data('default_payment_accounts') : [];
		    var payment_types_dropdown = $('.payment_types_dropdown');
		    var payment_type = payment_types_dropdown.val();
		    var payment_row = payment_types_dropdown.closest('.payment_row');
	        var row_index = payment_row.find('.payment_row_index').val();

	        var account_dropdown = payment_row.find('select#account_' + row_index);
		    if (payment_type && payment_type != 'advance') {
		        var default_account = default_accounts && default_accounts[payment_type]['account'] ? 
		            default_accounts[payment_type]['account'] : '';
		        if (account_dropdown.length && default_accounts) {
		            account_dropdown.val(default_account);
		            account_dropdown.change();
		        }
		    }

		    if (payment_type == 'advance') {
		        if (account_dropdown) {
		            account_dropdown.prop('disabled', true);
		            account_dropdown.closest('.form-group').addClass('hide');
		        }
		    } else {
		        if (account_dropdown) {
		            account_dropdown.prop('disabled', false); 
		            account_dropdown.closest('.form-group').removeClass('hide');
		        }    
		    }
		});

		function set_payment_type_dropdown() {
			var payment_settings = $('#location_id').find(':selected').data('default_payment_accounts');
			payment_settings = payment_settings ? payment_settings : [];
			enabled_payment_types = [];
			for (var key in payment_settings) {
				if (payment_settings[key] && payment_settings[key]['is_enabled']) {
					enabled_payment_types.push(key);
				}
			}
			if (enabled_payment_types.length) {
				$(".payment_types_dropdown > option").each(function() {
					//skip if advance
					if ($(this).val() && $(this).val() != 'advance') {
						if (enabled_payment_types.indexOf($(this).val()) != -1) {
							$(this).removeClass('hide');
						} else {
							$(this).addClass('hide');
						}
					}
				});
			}
		}
	</script>
	@include('purchase.partials.keyboard_shortcuts')
	@include('purchase.partials.purchase_line_details_js')

	{{-- ── Purchase Invoice Autosave ──────────────────────────────────────────── --}}
	<script type="text/javascript">
	(function() {
		var DRAFT_KEY = 'purchase_draft_{{ session("user.business_id") }}';
		var saveTimer = null;
		var draftRestored = false;

		// ── Collect form state ──────────────────────────────────────────────────
		function collectDraft() {
			var rows = [];
			$('#purchase_entry_table tbody tr').each(function() {
				var row = {};
				$(this).find('input,select,textarea').each(function() {
					var name = $(this).attr('name') || $(this).attr('id');
					if (name) row[name] = $(this).val();
				});
				rows.push(row);
			});

			return {
				supplier_id:       $('#supplier_id').val(),
				ref_no:            $('input[name="ref_no"]').val(),
				transaction_date:  $('input[name="transaction_date"]').val(),
				status:            $('select[name="status"]').val(),
				location_id:       $('select[name="location_id"]').val(),
				additional_notes:  $('textarea[name="additional_notes"]').val(),
				discount_type:     $('select[name="discount_type"]').val(),
				discount_amount:   $('input[name="discount_amount"]').val(),
				shipping_details:  $('input[name="shipping_details"]').val(),
				shipping_charges:  $('input[name="shipping_charges"]').val(),
				final_total:       $('input[name="final_total"]').val(),
				rows:              rows,
				saved_at:          new Date().toLocaleString(),
			};
		}

		// ── Save draft ──────────────────────────────────────────────────────────
		function saveDraft() {
			// Don't overwrite if user already submitted
			if ($('#add_purchase_form').data('submitted')) return;
			try {
				localStorage.setItem(DRAFT_KEY, JSON.stringify(collectDraft()));
			} catch(e) {}
		}

		// ── Clear draft on successful submit ────────────────────────────────────
		$('#add_purchase_form').on('submit', function() {
			$(this).data('submitted', true);
			localStorage.removeItem(DRAFT_KEY);
		});

		// ── Auto-save every 30 seconds + on change ──────────────────────────────
		function startAutosave() {
			clearInterval(saveTimer);
			saveTimer = setInterval(saveDraft, 30000);
		}

		$(document).on('change keyup', '#add_purchase_form input, #add_purchase_form select, #add_purchase_form textarea', function() {
			clearTimeout(window._purchaseDebounce);
			window._purchaseDebounce = setTimeout(saveDraft, 1500);
		});

		// ── Show restore banner if draft exists ─────────────────────────────────
		function checkForDraft() {
			try {
				var raw = localStorage.getItem(DRAFT_KEY);
				if (!raw) return;
				var draft = JSON.parse(raw);
				if (!draft || !draft.saved_at) return;

				var $banner = $('#purchase-draft-banner');
				$('#draft-banner-text').html(
					'<strong>Unsaved draft</strong> found from <em>' + draft.saved_at + '</em>.'
					+ (draft.rows && draft.rows.length ? ' (' + draft.rows.length + ' product' + (draft.rows.length > 1 ? 's' : '') + ')' : '')
				);
				$banner.css('display', 'flex');

				$('#draft-restore-btn').off('click').on('click', function(e) {
					e.preventDefault();
					restoreDraft(draft);
					$banner.hide();
				});

				$('#draft-discard-btn').off('click').on('click', function(e) {
					e.preventDefault();
					localStorage.removeItem(DRAFT_KEY);
					$banner.hide();
				});
			} catch(e) {}
		}

		// ── Restore rows sequentially (each waits for AJAX to finish) ────────────
		function restoreRowsSequentially(rows, index) {
			if (index >= rows.length) {
				toastr.success('Draft fully restored!', '', {timeOut: 3000});
				return;
			}
			var rowData = rows[index];
			var product_id = null, variation_id = null;
			$.each(rowData, function(key) {
				if (key.indexOf('[product_id]') !== -1)   product_id   = rowData[key];
				if (key.indexOf('[variation_id]') !== -1) variation_id = rowData[key];
			});

			if (!product_id || !variation_id) {
				restoreRowsSequentially(rows, index + 1);
				return;
			}

			var prevCount = $('#purchase_entry_table tbody tr').length;
			get_purchase_entry_row(product_id, variation_id);

			// Poll until the new row appears (AJAX appends it)
			var attempts = 0;
			var poll = setInterval(function() {
				attempts++;
				var newCount = $('#purchase_entry_table tbody tr').length;
				if (newCount > prevCount || attempts > 60) {
					clearInterval(poll);
					if (newCount > prevCount) {
						var $row = $('#purchase_entry_table tbody tr').last();
						// Set all saved field values onto the new row
						$.each(rowData, function(key, val) {
							var m = key.match(/\[([^\]]+)\]$/);
							if (!m) return;
							var field = m[1];
							var $el = $row.find('[name$="[' + field + ']"]');
							if ($el.length) $el.val(val);
						});
						// Trigger recalculation chain
						$row.find('.purchase_unit_cost_without_discount').trigger('change');
					}
					setTimeout(function() { restoreRowsSequentially(rows, index + 1); }, 150);
				}
			}, 100);
		}

		// ── Restore draft values ─────────────────────────────────────────────────
		function restoreDraft(draft) {
			try {
				// Header fields
				if (draft.location_id) {
					$('select[name="location_id"]').val(draft.location_id).trigger('change');
				}
				if (draft.supplier_id) {
					var opt = new Option('(Saved supplier)', draft.supplier_id, true, true);
					$('#supplier_id').append(opt).trigger('change');
				}
				if (draft.ref_no)           $('input[name="ref_no"]').val(draft.ref_no);
				if (draft.status)           $('select[name="status"]').val(draft.status).trigger('change');
				if (draft.discount_type)    $('select[name="discount_type"]').val(draft.discount_type).trigger('change');
				if (draft.discount_amount)  $('input[name="discount_amount"]').val(draft.discount_amount);
				if (draft.shipping_details) $('input[name="shipping_details"]').val(draft.shipping_details);
				if (draft.shipping_charges) $('input[name="shipping_charges"]').val(draft.shipping_charges);
				if (draft.additional_notes) $('textarea[name="additional_notes"]').val(draft.additional_notes);

				// Product rows — restore one by one
				if (draft.rows && draft.rows.length) {
					toastr.info('Restoring ' + draft.rows.length + ' product row(s)…', '', {timeOut: 3000});
					setTimeout(function() { restoreRowsSequentially(draft.rows, 0); }, 400);
				} else {
					toastr.success('Draft restored!', '', {timeOut: 3000});
				}
			} catch(e) {
				toastr.error('Could not restore draft.');
			}
		}

		// ── Init ──────────────────────────────────────────────────────────────────
		$(document).ready(function() {
			checkForDraft();
			startAutosave();
		});
	})();
	</script>

	{{-- ── Kimi Invoice Scanner JS ──────────────────────────────────────────── --}}
	<script>
	$(document).ready(function() {
	(function() {
		var scannedData  = null;
		var selectedFile = null; // holds the File object whether dropped or picked

		// Prevent browser from opening dropped files on the page
		$(document).on('dragover drop', function(e) { e.preventDefault(); });

		// Open modal
		$('#btn_scan_invoice').on('click', function() {
			resetScanModal();
			$('#scan_invoice_modal').modal('show');
		});

		// Dropzone drag-and-drop (click handled natively by <label for="scan_file_input">)
		$('#scan_dropzone').on('dragenter dragover', function(e) {
			e.preventDefault();
			e.stopPropagation();
			$(this).css('background', '#ede9fe').css('border-color', '#6366f1');
		}).on('dragleave', function(e) {
			e.preventDefault();
			e.stopPropagation();
			$(this).css('background', '#f5f3ff').css('border-color', '#c7d2fe');
		}).on('drop', function(e) {
			e.preventDefault();
			e.stopPropagation();
			$(this).css('background', '#f5f3ff').css('border-color', '#c7d2fe');
			var dt   = e.originalEvent.dataTransfer || e.dataTransfer;
			var file = dt && dt.files && dt.files[0];
			if (file) setFile(file);
		});

		$('#scan_file_input').on('change', function() {
			if (this.files[0]) setFile(this.files[0]);
			// also sync selectedFile when picked via dialog
		});

		$('#scan_clear_file').on('click', function(e) {
			e.preventDefault();
			selectedFile = null;
			$('#scan_file_input').val('');
			$('#scan_file_preview').hide();
			$('#scan_dropzone').show();
			$('#btn_extract_invoice').prop('disabled', true);
		});

		$('#btn_rescan').on('click', function() { resetScanModal(); });

		function setFile(file) {
			selectedFile = file;
			$('#scan_file_name').text(file.name + ' (' + (file.size / 1024).toFixed(0) + ' KB)');
			$('#scan_file_preview').show();
			$('#scan_dropzone').hide();
			$('#btn_extract_invoice').prop('disabled', false);
		}

		function resetScanModal() {
			selectedFile = null;
			$('#scan_file_input').val('');
			$('#scan_file_preview').hide();
			$('#scan_dropzone').show().css({'background': '#f5f3ff', 'border-color': '#c7d2fe'});
			$('#btn_extract_invoice').prop('disabled', true);
			$('#scan_step_upload').show();
			$('#scan_step_extracting').hide();
			$('#scan_step_review').hide();
			$('#scan_review_content').html('');
			scannedData = null;
		}

		// Extract
		$('#btn_extract_invoice').on('click', function() {
			var file = selectedFile;
			if (!file) { toastr.warning('Please select or drop an invoice file first.'); return; }

			$('#scan_step_upload').hide();
			$('#scan_step_extracting').show();

			var fd = new FormData();
			fd.append('invoice_image', file);
			fd.append('_token', '{{ csrf_token() }}');

			$.ajax({
				url: '/purchases/scan-invoice',
				method: 'POST',
				data: fd,
				processData: false,
				contentType: false,
				timeout: 90000,
				success: function(res) {
					$('#scan_step_extracting').hide();
					if (res.success) {
						scannedData = res.data;
						renderReview(res.data);
						$('#scan_step_review').show();
					} else {
						toastr.error('Extraction failed: ' + res.msg);
						$('#scan_step_upload').show();
					}
				},
				error: function(xhr) {
					$('#scan_step_extracting').hide();
					$('#scan_step_upload').show();
					var msg = xhr.responseJSON ? xhr.responseJSON.msg : 'Server error. Check KIMI_API_KEY.';
					toastr.error(msg);
				}
			});
		});

		function renderReview(data) {
			var html = '';

			// ── Header info (editable) ────────────────────────────────────────────
			html += '<div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:10px;margin-bottom:16px;">';
			html += headerField('Supplier',   'si_supplier',    data.supplier_name  || '');
			html += headerField('Invoice #',  'si_ref_no',      data.invoice_number || '');
			html += headerField('Date',       'si_inv_date',    data.invoice_date   || '');
			html += '</div>';

			// ── Items table ───────────────────────────────────────────────────────
			html += '<p style="font-size:12px;color:#64748b;margin-bottom:6px;">'
				+ '<i class="fas fa-info-circle"></i> '
				+ 'Search and select the matching system product for each row. Leave blank to skip that row.'
				+ '</p>';
			html += '<div style="overflow-x:auto;">';
			html += '<table class="table table-bordered table-sm" id="si_items_table" style="font-size:12px;">';
			html += '<thead><tr style="background:#f8fafc;">'
				+ '<th style="width:28px;">#</th>'
				+ '<th>Extracted from Invoice</th>'
				+ '<th style="min-width:180px;">Match System Product</th>'
				+ '<th style="width:65px;">Qty</th>'
				+ '<th style="width:85px;">Unit Price</th>'
				+ '<th style="width:60px;">Disc%</th>'
				+ '<th style="width:30px;"></th>'
				+ '</tr></thead><tbody>';

			if (data.items && data.items.length) {
				data.items.forEach(function(item, i) {
					html += '<tr data-index="' + i + '">';
					html += '<td style="color:#94a3b8;text-align:center;">' + (i+1) + '</td>';
					// Extracted name + pack size
					html += '<td>'
						+ '<span style="font-weight:600;color:#1e293b;">' + esc(item.product_name||'') + '</span>'
						+ (item.pack_size ? '<br><small style="color:#94a3b8;">' + esc(item.pack_size) + '</small>' : '')
						+ '</td>';
					// Product search autocomplete
					html += '<td>'
						+ '<input type="text" class="form-control input-sm si-product-search" placeholder="Type to search…" autocomplete="off" style="width:100%;">'
						+ '<input type="hidden" class="si-product-id">'
						+ '<input type="hidden" class="si-variation-id">'
						+ '</td>';
					// Qty / Price / Disc
					html += '<td><input type="number" class="form-control input-sm si-qty" value="' + (item.quantity||1) + '" min="0.01" step="any"></td>';
					html += '<td><input type="number" class="form-control input-sm si-price" value="' + (item.unit_price||0) + '" min="0" step="any"></td>';
					html += '<td><input type="number" class="form-control input-sm si-disc" value="' + (item.discount_percent||0) + '" min="0" max="100" step="any"></td>';
					html += '<td style="text-align:center;"><button type="button" class="btn btn-xs btn-default si-remove-row" title="Remove row"><i class="fas fa-times text-danger"></i></button></td>';
					html += '</tr>';
				});
			} else {
				html += '<tr><td colspan="7" style="text-align:center;color:#94a3b8;padding:20px;">No line items extracted</td></tr>';
			}

			html += '</tbody></table></div>';

			// Grand total summary
			if (data.grand_total) {
				html += '<div style="text-align:right;font-size:13px;color:#475569;margin-top:6px;">'
					+ 'Subtotal: <strong>' + (data.subtotal||0) + '</strong>'
					+ ' &nbsp;|&nbsp; Tax: <strong>' + (data.tax_amount||0) + '</strong>'
					+ ' &nbsp;|&nbsp; Grand Total: <strong style="font-size:15px;color:#1e293b;">' + data.grand_total + '</strong>'
					+ '</div>';
			}

			$('#scan_review_content').html(html);

			// Wire up product autocomplete on each search input
			$('#si_items_table').find('.si-product-search').each(function() {
				wireProductSearch($(this));
			});

			// Remove row button
			$('#si_items_table').on('click', '.si-remove-row', function() {
				$(this).closest('tr').remove();
			});
		}

		function headerField(label, id, val) {
			return '<div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;padding:10px;">'
				+ '<p style="font-size:11px;color:#94a3b8;margin:0 0 4px;text-transform:uppercase;font-weight:600;">' + label + '</p>'
				+ '<input type="text" id="' + id + '" class="form-control input-sm" value="' + esc(val) + '">'
				+ '</div>';
		}

		function wireProductSearch($input) {
			$input.autocomplete({
				source: function(req, resp) {
					$.getJSON('/purchases/get_products', {
						term:        req.term,
						location_id: $('#location_id').val(),
					}, function(data) {
						resp($.map(data, function(d) {
							return { label: d.text, value: d.text, product_id: d.product_id, variation_id: d.variation_id };
						}));
					});
				},
				minLength: 2,
				select: function(e, ui) {
					var $row = $(this).closest('tr');
					$row.find('.si-product-id').val(ui.item.product_id);
					$row.find('.si-variation-id').val(ui.item.variation_id);
					$row.css('background', '#f0fdf4');
				}
			});
		}

		function esc(str) {
			return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
		}

		// Apply to form
		$('#btn_apply_invoice').on('click', function() {
			if (!scannedData) return;

			// Set supplier from editable header field
			var supplierName = $('#si_supplier').val();
			if (supplierName) {
				// Try to find existing option, else set as text in the ref field
				$.getJSON('/purchases/get_suppliers', { q: supplierName }, function(results) {
					if (results && results.length) {
						var s = results[0];
						var opt = new Option(s.text, s.id, true, true);
						$('#supplier_id').append(opt).trigger('change');
					}
				});
			}

			// Set ref_no
			var refNo = $('#si_ref_no').val();
			if (refNo) $('input[name="ref_no"]').val(refNo);

			// Set date
			var invDate = $('#si_inv_date').val();
			if (invDate) {
				var d = moment(invDate);
				if (d.isValid()) $('#transaction_date').val(d.format(moment_date_format + ' ' + moment_time_format));
			}

			// Collect rows that have a product matched
			var rows = [];
			$('#si_items_table tbody tr').each(function() {
				var pid = $(this).find('.si-product-id').val();
				var vid = $(this).find('.si-variation-id').val();
				if (!pid || !vid) return; // skip rows without a match
				rows.push({
					product_id:   parseInt(pid),
					variation_id: parseInt(vid),
					quantity:     parseFloat($(this).find('.si-qty').val()) || 1,
					unit_price:   parseFloat($(this).find('.si-price').val()) || 0,
					discount:     parseFloat($(this).find('.si-disc').val()) || 0,
				});
			});

			$('#scan_invoice_modal').modal('hide');

			if (rows.length === 0) {
				toastr.warning('No products matched. Search and select system products before applying.');
				return;
			}

			toastr.info('Applying ' + rows.length + ' items…', '', {timeOut: 3000});
			applyRowsSequentially(rows, 0);
		});

		function applyRowsSequentially(rows, idx) {
			if (idx >= rows.length) {
				toastr.success('Invoice applied! Review and submit.');
				return;
			}
			var item = rows[idx];
			var rowCountBefore = parseInt($('#row_count').val()) || 0;

			$.ajax({
				method: 'POST',
				url: '/purchases/get_purchase_entry_row',
				dataType: 'html',
				data: {
					product_id:   item.product_id,
					variation_id: item.variation_id,
					row_count:    rowCountBefore,
					location_id:  $('#location_id').val(),
					supplier_id:  $('#supplier_id').val(),
				},
				success: function(result) {
					append_purchase_lines(result, rowCountBefore);

					// Give DOM a tick to settle then fill in values
					setTimeout(function() {
						var $row = $('#purchase_entry_table tbody tr').last();
						if ($row.length) {
							// Quantity
							if (item.quantity && $row.find('.purchase_quantity').length) {
								$row.find('.purchase_quantity').val(
									__number_f(item.quantity)
								).trigger('change');
							}
							// Unit cost without discount
							if (item.unit_price && $row.find('.purchase_unit_cost_without_discount').length) {
								$row.find('.purchase_unit_cost_without_discount').val(
									__number_f(item.unit_price)
								).trigger('change');
							}
							// Discount
							if (item.discount && $row.find('.purchase_discount').length) {
								$row.find('.purchase_discount').val(item.discount).trigger('change');
							}
						}
						applyRowsSequentially(rows, idx + 1);
					}, 300);
				},
				error: function() {
					applyRowsSequentially(rows, idx + 1);
				}
			});
		}
	})();
	}); // end document.ready
	</script>
	<style>
	@keyframes pulse {
		0%,100%{opacity:1;transform:scale(1);}
		50%{opacity:.7;transform:scale(1.08);}
	}
	</style>
@endsection
