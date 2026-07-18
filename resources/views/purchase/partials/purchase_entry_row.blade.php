@foreach( $variations as $variation)
@php
    $currency_precision  = session('business.currency_precision', 2);
    $quantity_precision  = session('business.quantity_precision', 2);
    $check_decimal       = ($product->unit->allow_decimal == 0) ? 'true' : 'false';

    $quantity_value      = !empty($purchase_order_line)       ? $purchase_order_line->quantity       : 1;
    $quantity_value      = !empty($purchase_requisition_line) ? $purchase_requisition_line->quantity - $purchase_requisition_line->po_quantity_purchased : $quantity_value;
    $max_quantity        = !empty($purchase_order_line)       ? $purchase_order_line->quantity - $purchase_order_line->po_quantity_purchased       : 0;
    $max_quantity        = !empty($purchase_requisition_line) ? $purchase_requisition_line->quantity - $purchase_requisition_line->po_quantity_purchased : $max_quantity;
    $quantity_value      = !empty($imported_data)             ? $imported_data['quantity']            : $quantity_value;

    $pp_without_discount = !empty($purchase_order_line)       ? $purchase_order_line->pp_without_discount / $purchase_order->exchange_rate          : $variation->default_purchase_price;
    $discount_percent    = !empty($purchase_order_line)       ? $purchase_order_line->discount_percent                                              : 0;
    $purchase_price      = !empty($purchase_order_line)       ? $purchase_order_line->purchase_price / $purchase_order->exchange_rate                : $variation->default_purchase_price;
    $tax_id              = !empty($purchase_order_line)       ? $purchase_order_line->tax_id                                                        : $product->tax;
    $tax_id              = !empty($imported_data['tax_id'])   ? $imported_data['tax_id']                                                            : $tax_id;
    $pp_without_discount = !empty($imported_data['unit_cost_before_discount']) ? $imported_data['unit_cost_before_discount'] : $pp_without_discount;
    $discount_percent    = !empty($imported_data['discount_percent'])          ? $imported_data['discount_percent']          : $discount_percent;

    $dpp_inc_tax = number_format($variation->dpp_inc_tax, $currency_precision, $currency_details->decimal_separator, $currency_details->thousand_separator);
    if ($hide_tax == 'hide') {
        $dpp_inc_tax = number_format($variation->default_purchase_price, $currency_precision, $currency_details->decimal_separator, $currency_details->thousand_separator);
    }
    $dpp_inc_tax = !empty($purchase_order_line)
        ? number_format($purchase_order_line->purchase_price_inc_tax / $purchase_order->exchange_rate, $currency_precision, $currency_details->decimal_separator, $currency_details->thousand_separator)
        : $dpp_inc_tax;

    $expiry_period_type = !empty($product->expiry_period_type) ? $product->expiry_period_type : 'month';
    $lot_number         = !empty($imported_data['lot_number']) ? $imported_data['lot_number'] : null;
    $mfg_date           = !empty($imported_data['mfg_date'])   ? $imported_data['mfg_date']   : null;
    $exp_date           = !empty($imported_data['exp_date'])   ? $imported_data['exp_date']   : null;
    $hide_mfg           = (session('business.expiry_type') == 'add_manufacturing') ? false : true;

    $cost_fmt     = number_format($pp_without_discount, $currency_precision, $currency_details->decimal_separator, $currency_details->thousand_separator);
    $disc_fmt     = number_format($discount_percent,    $currency_precision, $currency_details->decimal_separator, $currency_details->thousand_separator);
    $price_fmt    = number_format($purchase_price,      $currency_precision, $currency_details->decimal_separator, $currency_details->thousand_separator);

    $sell_fmt = number_format($variation->sell_price_inc_tax, $currency_precision, $currency_details->decimal_separator, $currency_details->thousand_separator);
@endphp

<tr data-row="{{ $row_count }}"
    @if(!empty($purchase_order_line))       data-purchase_order_id="{{ $purchase_order_line->transaction_id }}"            @endif
    @if(!empty($purchase_requisition_line)) data-purchase_requisition_id="{{ $purchase_requisition_line->transaction_id }}" @endif
>

    {{-- # --}}
    <td class="text-center ps-col-num">
        <span class="sr_number"></span>
    </td>

    {{-- Product + badges --}}
    <td class="ps-col-product text-left">
        <span class="purchase-product-name">{{ $product->name }}</span>
        @if($product->type == 'variable')
            <span class="purchase-product-sku">(<b>{{ $variation->product_variation->name }}</b>: {{ $variation->name }})</span>
        @else
            <span class="purchase-product-sku">{{ $variation->sub_sku }}</span>
        @endif
        @if($product->enable_stock == 1)
            <div class="purchase-product-meta">
                @lang('report.current_stock'):
                @if(!empty($variation->variation_location_details->first()))
                    {{ @num_format($variation->variation_location_details->first()->qty_available) }}
                @else 0 @endif
                {{ $product->unit->short_name }}
            </div>
        @endif
        @if(!empty($last_purchase_line))
            <div class="purchase-product-meta">
                <i class="fa fa-history"></i>
                Last: @format_currency($last_purchase_line->pp_without_discount)
                @if($last_purchase_line->discount_percent > 0)
                    | Disc: {{ @num_format($last_purchase_line->discount_percent) }}%
                @endif
            </div>
        @endif
        <div class="details-summary-badge" style="margin-top:4px;">
            <small class="label label-info details-sell-badge" style="@if(empty($variation->sell_price_inc_tax))display:none;@endif">
                @if(!empty($variation->sell_price_inc_tax))Sell: {{ $sell_fmt }}@endif
            </small>
            <small class="label label-warning details-lot-badge" style="@if(empty($lot_number))display:none;@endif">
                @if(!empty($lot_number))Lot: {{ $lot_number }}@endif
            </small>
            <small class="label label-default details-exp-badge" style="@if(empty($exp_date))display:none;@endif">
                @if(!empty($exp_date))Exp: {{ $exp_date }}@endif
            </small>
        </div>
    </td>

    {{-- Qty + identity hiddens --}}
    <td class="ps-col-qty" data-label="Qty">
        {!! Form::hidden('purchases[' . $row_count . '][product_id]',   $product->id) !!}
        {!! Form::hidden('purchases[' . $row_count . '][variation_id]', $variation->id, ['class' => 'hidden_variation_id']) !!}
        @if(!empty($purchase_order_line))
            {!! Form::hidden('purchases[' . $row_count . '][purchase_order_line_id]', $purchase_order_line->id) !!}
        @endif
        @if(!empty($purchase_requisition_line))
            {!! Form::hidden('purchases[' . $row_count . '][purchase_requisition_line_id]', $purchase_requisition_line->id) !!}
        @endif
        <input type="hidden" name="purchases[{{ $row_count }}][product_unit_id]" value="{{ $product->unit->id }}">
        <input type="hidden" class="base_unit_cost"          value="{{ $variation->default_purchase_price }}">
        <input type="hidden" class="base_unit_selling_price" value="{{ $variation->sell_price_inc_tax }}">

        <input type="text"
            name="purchases[{{ $row_count }}][quantity]"
            value="{{ @format_quantity($quantity_value) }}"
            class="form-control purchase_quantity input_number mousetrap"
            required
            data-rule-abs_digit="{{ $check_decimal }}"
            data-msg-abs_digit="{{ __('lang_v1.decimal_value_not_allowed') }}"
            @if(!empty($max_quantity))
                data-rule-max-value="{{ $max_quantity }}"
                data-msg-max-value="{{ __('lang_v1.max_quantity_quantity_allowed', ['quantity' => $max_quantity]) }}"
            @endif
        >

        @if(!empty($sub_units))
            <select name="purchases[{{ $row_count }}][sub_unit_id]" class="form-control input-sm sub_unit hide">
                @foreach($sub_units as $key => $value)
                    <option value="{{ $key }}" data-multiplier="{{ $value['multiplier'] }}">{{ $value['name'] }}</option>
                @endforeach
            </select>
        @else
            <span class="hide">{{ $product->unit->short_name }}</span>
        @endif

        @if(!empty($product->second_unit))
            @php $secondary_unit_quantity = !empty($purchase_requisition_line) ? $purchase_requisition_line->secondary_unit_quantity : ""; @endphp
            <br><small>@lang('lang_v1.quantity_in_second_unit', ['unit' => $product->second_unit->short_name])*</small>
            <input type="text"
                name="purchases[{{ $row_count }}][secondary_unit_quantity]"
                @if($secondary_unit_quantity !== '') value="{{ @format_quantity($secondary_unit_quantity) }}" @endif
                class="form-control input-sm input_number"
                required style="margin-top:3px;">
        @endif
    </td>

    {{-- Unit cost (before disc) — main money-in field --}}
    <td class="ps-col-cost" data-label="Unit Cost">
        <input type="text"
            name="purchases[{{ $row_count }}][pp_without_discount]"
            value="{{ $cost_fmt }}"
            class="form-control purchase_unit_cost_without_discount input_number"
            placeholder="0.00"
            required
        >
        {!! Form::hidden('purchases[' . $row_count . '][purchase_price]', $price_fmt, ['class' => 'purchase_unit_cost input_number']) !!}
    </td>

    {{-- Hidden: Disc % (kept for JS) --}}
    <td class="hide">
        <input type="text"
            name="purchases[{{ $row_count }}][discount_percent]"
            value="{{ $disc_fmt }}"
            class="form-control inline_discounts input_number"
            required
        >
    </td>

    {{-- Hidden: Sub total before tax --}}
    <td class="hide text-right">
        <span class="row_subtotal_before_tax display_currency">0</span>
        <input type="hidden" class="row_subtotal_before_tax_hidden" value="0">
    </td>

    {{-- Hidden: Tax % --}}
    <td class="hide">
        {!! Form::text('purchases[' . $row_count . '][item_tax_percent]', 0, [
            'class'       => 'form-control row_tax_percent input_number',
            'placeholder' => '0',
        ]) !!}
        {!! Form::text('purchases[' . $row_count . '][item_tax]', 0, [
            'class' => 'form-control row_tax_amount input_number purchase_product_unit_tax hide',
        ]) !!}
        <select name="purchases[{{ $row_count }}][purchase_line_tax_id]" class="hide purchase_line_tax_id">
            <option value="" data-tax_amount="0" selected>@lang('lang_v1.none')</option>
        </select>
    </td>

    {{-- Line total --}}
    <td class="text-right ps-col-total" data-label="Total">
        <span class="row_subtotal_after_tax display_currency">0</span>
        <input type="hidden" class="row_subtotal_after_tax_hidden" value="0">
    </td>

    {{-- Compatibility empty cols (create table legacy) --}}
    <td class="hide"></td>
    <td class="hide"></td>

    {{-- Net cost / after tax unit --}}
    <td class="hide">
        {!! Form::text('purchases[' . $row_count . '][purchase_price_inc_tax]', $dpp_inc_tax, [
            'class'    => 'form-control input-sm purchase_unit_cost_after_tax input_number',
            'required' => true,
        ]) !!}
    </td>

    {{-- Margin % --}}
    <td class="hide">
        {!! Form::text('purchases[' . $row_count . '][profit_percent]',
            number_format($variation->profit_percent, $currency_precision, $currency_details->decimal_separator, $currency_details->thousand_separator),
            ['class' => 'form-control input-sm input_number profit_percent', 'required']
        ) !!}
    </td>

    {{-- Sell price --}}
    @if(empty($is_purchase_order))
    <td class="hide">
        @if(session('business.enable_editing_product_from_purchase'))
            {!! Form::text('purchases[' . $row_count . '][default_sell_price]',
                $sell_fmt,
                ['class' => 'form-control input-sm input_number default_sell_price', 'required']
            ) !!}
        @else
            <input type="hidden"
                name="purchases[{{ $row_count }}][default_sell_price]"
                value="{{ $sell_fmt }}"
                class="default_sell_price">
        @endif
    </td>
    @endif

    {{-- Lot --}}
    @if(session('business.enable_lot_number'))
    <td class="hide">
        {!! Form::text('purchases[' . $row_count . '][lot_number]', $lot_number, ['class' => 'form-control input-sm lot_number_input']) !!}
    </td>
    @endif

    {{-- MFG / EXP --}}
    @if(true || session('business.enable_product_expiry'))
    <td class="hide">
        @if(!empty($expiry_period_type))
            <input type="hidden" class="row_product_expiry"      value="{{ $product->expiry_period }}">
            <input type="hidden" class="row_product_expiry_type" value="{{ $expiry_period_type }}">
            <input type="text" name="purchases[{{ $row_count }}][mfg_date]" value="{{ $mfg_date }}"
                class="form-control input-sm expiry_datepicker mfg_date @if($hide_mfg) hide @endif" readonly>
            <input type="text" name="purchases[{{ $row_count }}][exp_date]"  value="{{ $exp_date }}"
                class="form-control input-sm expiry_datepicker exp_date" readonly>
        @else
            <input type="hidden" name="purchases[{{ $row_count }}][exp_date]" value="" class="exp_date">
        @endif
    </td>
    @endif

    {{-- Details --}}
    <td class="text-center ps-col-details">
        <button type="button"
            class="btn btn-sm btn-primary btn-purchase-details"
            data-row="{{ $row_count }}"
            data-product="{{ addslashes($product->name) }}"
            data-variation="{{ ($variation->name !== 'DUMMY') ? addslashes($variation->name) : '' }}"
            title="Edit sell price, margin, lot & expiry"
        >
            <i class="fa fa-pencil"></i> Details
        </button>
    </td>

    {{-- Remove --}}
    <td class="text-center ps-col-remove">
        <i class="fa fa-times remove_purchase_entry_row text-danger"
           title="Remove" style="cursor:pointer;"></i>
    </td>

</tr>
<?php $row_count++; ?>
@endforeach

<input type="hidden" id="row_count" value="{{ $row_count }}">
