@php
	$common_settings = session()->get('business.common_settings');
	$multiplier = 1;
	$action = !empty($action) ? $action : '';
@endphp

@foreach($sub_units as $key => $value)
	@if(!empty($product->sub_unit_id) && $product->sub_unit_id == $key)
		@php $multiplier = $value['multiplier']; @endphp
	@endif
@endforeach

<tr class="product_row" style="vertical-align: middle !important;" data-row_index="{{$row_count}}" data-is_dda="{{!empty($product->is_dda) ? 1 : 0}}" data-dda_drug_id="{{$product->dda_drug_id ?? ''}}" @if(!empty($so_line)) data-so_id="{{$so_line->transaction_id}}" @endif>
	@if(!empty($is_serial_no))
		<td class="serial_no"></td>
	@endif
    
    {{-- Product Column --}}
	<td style="padding: 4px 8px !important;">
		@if(!empty($so_line))
			<input type="hidden" name="products[{{$row_count}}][so_line_id]" value="{{$so_line->id}}">
		@endif
		@php
			$product_name = e($product->product_name) . ' (' . $product->sub_sku . ')';
			if(!empty($product->brand)){ $product_name .= ' ' . $product->brand ;}
		@endphp

        <div style="display: flex; align-items: center; gap: 4px;">
            <div style="min-width: 0; flex: 1;">
                @if( ($edit_price || $edit_discount) && empty($is_direct_sell) )
                    <span class="text-link" data-toggle="modal" data-target="#row_edit_product_price_modal_{{$row_count}}" style="font-size: 11px !important; font-weight: 700 !important; display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; color: var(--pos-text) !important;">
                        {!! $product_name !!}
                    </span>
                @else
                    <span style="font-size: 11px !important; font-weight: 700 !important; color: var(--pos-text) !important; display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{!! $product_name !!}</span>
                @endif
                
                @if($product->enable_stock)
                    <div style="font-size: 9px; color: var(--pos-text-muted); margin-top: 0px;">{{ @num_format($product->qty_available) }} in stock</div>
                @endif

                {{-- Batch / Lot Tracking for Pharmaceuticals --}}
                @if(!empty($product->lot_numbers) && (session()->get('business.enable_lot_number') == 1 || session()->get('business.enable_product_expiry') == 1))
                    <div style="margin-top: 2px;">
                        <select name="products[{{$row_count}}][lot_no_line_id]" class="form-control input-sm lot_number" style="width: 100%; height: 20px; font-size: 9px; padding: 0 4px; border-radius: 4px; border: 1px solid #ddd; background: #fff;">
                            <option value="">Lot Number</option>
                            @foreach($product->lot_numbers as $lot_number)
                                @php
                                    $selected = "";
                                    if(!empty($product->lot_no_line_id) && $product->lot_no_line_id == $lot_number->purchase_line_id){
                                        $selected = "selected";
                                    }
                                    $expiry = "";
                                    if(!empty($lot_number->exp_date)){
                                        $expiry = ' (Exp: ' . \Carbon::createFromTimestamp(strtotime($lot_number->exp_date))->format(session('business.date_format')) . ')';
                                    }
                                @endphp
                                <option value="{{$lot_number->purchase_line_id}}" {{$selected}} data-qty_available="{{$lot_number->qty_available}}" data-msg-max-value="@lang('lang_v1.quantity_error_msg_in_lot', ['qty' => $lot_number->qty_formated, 'unit' => $product->unit])">
                                    {{$lot_number->lot_number}} {{$expiry}}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif
            </div>
        </div>

		<input type="hidden" class="enable_sr_no" value="{{$product->enable_sr_no}}">
		<input type="hidden" class="product_type" name="products[{{$row_count}}][product_type]" value="{{$product->product_type}}">

		@php
			$hide_tax = 'hide';
	        if(session()->get('business.enable_inline_tax') == 1){ $hide_tax = ''; }
			$tax_id = $product->tax_id;
			$item_tax = !empty($product->item_tax) ? $product->item_tax : 0;
			$unit_price_inc_tax = $product->sell_price_inc_tax;
			if($hide_tax == 'hide'){ $tax_id = null; $unit_price_inc_tax = $product->default_sell_price; }

			$discount_type = !empty($product->line_discount_type) ? $product->line_discount_type : 'fixed';
			$discount_amount = !empty($product->line_discount_amount) ? $product->line_discount_amount : 0;
            if($discount_type == 'fixed') { $discount_amount = $discount_amount * $multiplier; }

			$sell_line_note = '';
			if(!empty($product->sell_line_note)){
				$sell_line_note = $product->sell_line_note;
			}
  		@endphp

        {{-- Hidden fields for standard functional logic --}}
        @if(!empty($discount))
			{!! Form::hidden("products[$row_count][discount_id]", $discount->id); !!}
		@endif
        @php
			$warranty_id = !empty($action) && $action == 'edit' && !empty($product->warranties->first())  ? $product->warranties->first()->id : $product->warranty_id;
		@endphp

		@if(empty($is_direct_sell))
            <div class="modal fade row_edit_product_price_model" id="row_edit_product_price_modal_{{$row_count}}" tabindex="-1" role="dialog">
                @include('sale_pos.partials.row_edit_product_price_modal')
            </div> 
		@endif
        
        @if(in_array('modifiers' , $enabled_modules))
			<div class="modifiers_html">
				@if(!empty($product->product_ms))
					@include('restaurant.product_modifier_set.modifier_for_product', array('edit_modifiers' => true, 'row_count' => $row_count, 'product_ms' => $product->product_ms ))
				@endif
			</div>
		@endif
	</td>

    {{-- Qty Column --}}
	<td class="col-qty" style="padding: 4px !important; vertical-align: middle !important;">
		@if(!empty($product->transaction_sell_lines_id))
			<input type="hidden" name="products[{{$row_count}}][transaction_sell_lines_id]" value="{{$product->transaction_sell_lines_id}}">
		@endif
		<input type="hidden" name="products[{{$row_count}}][product_id]" class="product_id" value="{{$product->product_id}}">
		<input type="hidden" value="{{$product->variation_id}}" name="products[{{$row_count}}][variation_id]" class="row_variation_id">
		<input type="hidden" value="{{$product->enable_stock}}" name="products[{{$row_count}}][enable_stock]">
		
		@php
			if(empty($product->quantity_ordered)){ $product->quantity_ordered = 1; }
			$allow_decimal = $product->unit_allow_decimal == 1;
            
            $max_quantity = $product->qty_available;
            $formatted_max_quantity = $product->formatted_qty_available;
            
            if(!empty($action) && $action == 'edit') {
				if(!empty($so_line)) {
					$qty_available = $so_line->quantity - $so_line->so_quantity_invoiced + $product->quantity_ordered;
					$max_quantity = $qty_available;
					$formatted_max_quantity = number_format($qty_available, session('business.quantity_precision', 2), session('currency')['decimal_separator'], session('currency')['thousand_separator']);
				}
			} else {
				if(!empty($so_line) && $so_line->qty_available <= $max_quantity) {
					$max_quantity = $so_line->qty_available;
					$formatted_max_quantity = $so_line->formatted_qty_available;
				}
			}
            
            $max_qty_rule = $max_quantity;
            $max_qty_msg = __('validation.custom-messages.quantity_not_available', ['qty'=> $formatted_max_quantity, 'unit' => $product->unit  ]);
		@endphp
        
        @foreach($sub_units as $key => $value)
        	@if(!empty($product->sub_unit_id) && $product->sub_unit_id == $key)
        		@php
        			$max_qty_rule = $max_qty_rule / $multiplier;
        			$unit_name = $value['name'];
        			$max_qty_msg = __('validation.custom-messages.quantity_not_available', ['qty'=> $max_qty_rule, 'unit' => $unit_name  ]);
        			if($value['allow_decimal']) { $allow_decimal = true; }
        		@endphp
        	@endif
        @endforeach

        <div class="quantity-controls input-number">
            <button type="button" class="quantity-down">−</button>
            <input type="text" class="pos_quantity input_number mousetrap input_quantity" 
                value="{{@format_quantity($product->quantity_ordered)}}" name="products[{{$row_count}}][quantity]" 
                data-decimal="{{$allow_decimal ? 1 : 0}}"
                data-rule-required="true" 
                data-allow-overselling="@if(empty($pos_settings['allow_overselling'])){{'false'}}@else{{'true'}}@endif"
                @if($product->enable_stock && empty($pos_settings['allow_overselling']))
                    data-rule-max-value="{{$max_qty_rule}}" 
                    data-qty_available="{{$product->qty_available}}" 
                    data-msg-max-value="{{$max_qty_msg}}" 
                    data-msg_max_default="@lang('validation.custom-messages.quantity_not_available', ['qty'=> $product->formatted_qty_available, 'unit' => $product->unit  ])"
                @endif 
            >
            <button type="button" class="quantity-up">+</button>
        </div>
        <input type="hidden" name="products[{{$row_count}}][product_unit_id]" value="{{$product->unit_id}}">
        <input type="hidden" class="base_unit_multiplier" name="products[{{$row_count}}][base_unit_multiplier]" value="{{$multiplier}}">
        
        {{-- Hidden fields for combo products --}}
		@if($product->product_type == 'combo'&& !empty($product->combo_products))
			@foreach($product->combo_products as $k => $combo_product)
				@php
					$combo_qty = $combo_product['qty_required'];
					if(isset($action) && $action == 'edit') {
						$combo_qty = $combo_product['quantity'];
					}
				@endphp
				<input type="hidden" name="products[{{$row_count}}][combo][{{$k}}][product_id]" value="{{$combo_product['product_id']}}">
				<input type="hidden" name="products[{{$row_count}}][combo][{{$k}}][variation_id]" value="{{$combo_product['variation_id']}}">
				<input type="hidden" class="combo_product_qty" name="products[{{$row_count}}][combo][{{$k}}][quantity]" data-unit_quantity="{{$combo_product['qty_required']}}" value="{{$combo_qty}}">
				@if(isset($action) && $action == 'edit')
					<input type="hidden" name="products[{{$row_count}}][combo][{{$k}}][transaction_sell_lines_id]" value="{{$combo_product['id']}}">
				@endif
			@endforeach
		@endif
	</td>

    {{-- Service Staff Column --}}
    @if(!empty($pos_settings['inline_service_staff']))
        <td class="col-staff" style="padding: 4px !important;">
            {!! Form::select("products[" . $row_count . "][res_service_staff_id]", $waiters, !empty($product->res_service_staff_id) ? $product->res_service_staff_id : null, ['class' => 'form-control select2', 'style' => 'width: 100%; height: 24px; font-size: 10px; padding: 0 4px; border-radius: 6px;']); !!}
        </td>
    @endif

    {{-- Price Column --}}
    @if(session()->get('business.enable_inline_tax') == 1)
        <td class="col-price" style="padding: 4px !important; text-align: right; vertical-align: middle !important;">
            <input type="text" name="products[{{$row_count}}][unit_price_inc_tax]" class="form-control pos_unit_price_inc_tax input_number" value="{{@num_format($unit_price_inc_tax)}}" readonly style="width: 100%; height: 24px; font-size: 11px; text-align: right; border: none; background: transparent; color: var(--pos-text); font-weight: 700;">
        </td>
    @else
        <input type="hidden" name="products[{{$row_count}}][unit_price_inc_tax]" class="pos_unit_price_inc_tax input_number" value="{{@num_format($unit_price_inc_tax)}}">
    @endif

    {{-- Warranty Column --}}
	@if(!empty($common_settings['enable_product_warranty']))
		<td class="col-warranty" style="padding: 4px !important;">
			{!! Form::select("products[$row_count][warranty_id]", $warranties, $warranty_id, ['placeholder' => 'No', 'class' => 'form-control', 'style' => 'width: 100%; height: 24px; font-size: 10px; padding: 0 4px; border-radius: 6px;']); !!}
		</td>
	@endif

    {{-- Discount Column --}}
    <td class="col-discount" style="padding: 4px 6px !important; text-align: center; vertical-align: middle !important; width: 140px; min-width: 140px;">
        @if( ($edit_price || $edit_discount) && empty($is_direct_sell) )
            <div style="display: flex; align-items: center; justify-content: center; gap: 4px; flex-wrap: nowrap;">
                <select name="products[{{$row_count}}][line_discount_type]" class="form-control row_discount_type" style="width: 65px; min-width: 60px; height: 26px; font-size: 10px; padding: 0 2px; border-radius: 6px; border: 1px solid #ddd; background: #fff; flex-shrink: 0;">
                    <option value="fixed" @if($discount_type == 'fixed') selected @endif>Fixed</option>
                    <option value="percentage" @if($discount_type == 'percentage') selected @endif>%</option>
                </select>
                <input type="text" name="products[{{$row_count}}][line_discount_amount]" class="form-control input_number row_discount_amount" value="{{@num_format($discount_amount)}}" style="width: 65px; min-width: 55px; height: 26px; font-size: 11px; text-align: right; padding: 0 6px; border-radius: 6px; border: 1px solid #ddd; background: #fff; flex: 1;">
            </div>
        @else
            <input type="hidden" name="products[{{$row_count}}][line_discount_type]" class="row_discount_type" value="{{$discount_type}}">
            <input type="hidden" name="products[{{$row_count}}][line_discount_amount]" class="input_number row_discount_amount" value="{{@num_format($discount_amount)}}">
            <span style="font-size: 10px; color: var(--pos-text-muted);">
                @if($discount_amount > 0)
                    {{ @num_format($discount_amount) }}{{ $discount_type == 'percentage' ? '%' : '' }}
                @else
                    —
                @endif
            </span>
        @endif
    </td>

    {{-- Total Column --}}
	<td class="col-total" style="padding: 4px !important; vertical-align: middle !important;">
		<input type="hidden" class="pos_line_total" value="{{$product->quantity_ordered*$unit_price_inc_tax}}">
		<span class="pos_line_total_text" style="font-size: 12px; font-weight: 700; color: var(--pos-text);">{{$product->quantity_ordered*$unit_price_inc_tax}}</span>
	</td>

    {{-- Remove Column --}}
	<td class="col-action" style="padding: 4px !important; text-align: center; vertical-align: middle !important;">
		<i class="material-icons pos_remove_row" style="font-size: 18px !important;">close</i>
	</td>
</tr>