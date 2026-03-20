<div style="display: flex; flex-direction: column; gap: 4px;">
    {{-- Item & Subtotal Row --}}
    <div class="summary-row">
        <div style="display: flex; align-items: center; gap: 8px;">
            <span style="font-size: 12px; color: var(--pos-text-secondary);">Items:</span>
            <span class="total_quantity">0</span>
        </div>
        <div style="display: flex; align-items: center; gap: 8px;">
            <span style="font-size: 12px; color: var(--pos-text-secondary);">Subtotal:</span>
            <span class="price_total" style="font-size: 14px; font-weight: 600; color: var(--pos-text);">0.00</span>
        </div>
    </div>

    {{-- Product Discounts Row --}}
    <div class="summary-row" id="product_discounts_row" style="display: none;">
        <div style="display: flex; align-items: center; gap: 8px;">
            <span style="font-size: 12px; color: var(--pos-text-secondary);">
                <i class="fas fa-tags"></i> Product Discounts:
            </span>
        </div>
        <span id="product_discounts_amount" style="font-size: 13px; font-weight: 500; color: var(--pos-text-secondary);">0.00</span>
    </div>

    {{-- Discount Row (Clickable) --}}
    @if($is_discount_enabled)
    <div class="summary-row pos-discount-row" id="discount_row" data-toggle="modal" data-target="#posEditDiscountModal" style="cursor: pointer; padding: 6px 8px; margin: 2px -8px; border-radius: 6px; transition: background 0.2s;">
        <div style="display: flex; align-items: center; gap: 8px;">
            <span style="font-size: 12px; color: #dc2626;">
                <i class="fas fa-tag"></i> Total Discount:
            </span>
            <span id="discount_type_display" style="font-size: 11px; color: #94a3b8;"></span>
        </div>
        <div style="display: flex; align-items: center; gap: 6px;">
            <span id="discount_amount_display" style="font-size: 14px; font-weight: 600; color: #dc2626;">0.00</span>
            <i class="fas fa-pencil-alt" style="font-size: 10px; color: #94a3b8;"></i>
        </div>
    </div>
    <style>
        .pos-discount-row:hover { background: rgba(220, 38, 38, 0.08) !important; }
    </style>
    @endif

    {{-- Tax Row (Clickable) --}}
    <div class="summary-row pos-tax-row" id="tax_row" data-toggle="modal" data-target="#posEditOrderTaxModal" style="cursor: pointer; padding: 6px 8px; margin: 2px -8px; border-radius: 6px; transition: background 0.2s; display: none;">
        <div style="display: flex; align-items: center; gap: 8px;">
            <span style="font-size: 12px; color: var(--pos-text-secondary);">
                <i class="fas fa-percent"></i> Tax:
            </span>
            <span id="tax_percent_display" style="font-size: 11px; color: #94a3b8;"></span>
        </div>
        <div style="display: flex; align-items: center; gap: 6px;">
            <span id="tax_amount_display" style="font-size: 14px; font-weight: 500; color: var(--pos-text-secondary);">0.00</span>
            <i class="fas fa-pencil-alt" style="font-size: 10px; color: #94a3b8;"></i>
        </div>
    </div>
    <style>
        .pos-tax-row:hover { background: rgba(100, 116, 139, 0.08) !important; }
    </style>

    {{-- Round Off (Conditional) --}}
    @if(!empty($pos_settings['amount_rounding_method']) && $pos_settings['amount_rounding_method'] > 0)
    <div class="summary-row">
        <span style="font-size: 12px; color: var(--pos-text-secondary);">@lang('lang_v1.round_off'):</span>
        <span id="round_off_text" style="font-size: 12px; font-weight: 500; color: var(--pos-text-secondary);">0</span>
        <input type="hidden" name="round_off_amount" id="round_off_amount" value=0>
    </div>
    @endif

    {{-- Final Total Row --}}
    <div class="total-row final">
        <span>Total Payable</span>
        <span id="total_payable">0.00</span>
    </div>
</div>

{{-- Hidden Inputs & Tooltips --}}
<div class="hide">
    @if($is_discount_enabled)
        <input type="hidden" name="discount_type" id="discount_type" value="@if(empty($edit)){{'percentage'}}@else{{$transaction->discount_type}}@endif" data-default="percentage">
        <input type="hidden" name="discount_amount" id="discount_amount" value="@if(empty($edit)) {{@num_format($business_details->default_sales_discount)}} @else {{@num_format($transaction->discount_amount)}} @endif" data-default="{{$business_details->default_sales_discount}}">
        <span id="total_discount">0</span>
    @endif
    <input type="hidden" name="tax_rate_id" id="tax_rate_id" value="@if(empty($edit)) {{$business_details->default_sales_tax}} @else {{$transaction->tax_id}} @endif" data-default="{{$business_details->default_sales_tax}}">
    <input type="hidden" name="tax_calculation_amount" id="tax_calculation_amount" value="@if(empty($edit)) {{@num_format($business_details->tax_calculation_amount)}} @else {{@num_format($transaction->tax?->amount)}} @endif" data-default="{{$business_details->tax_calculation_amount}}">
    <span id="order_tax">0</span>
    <input type="hidden" name="shipping_charges" id="shipping_charges" value="@if(empty($edit)){{@num_format(0.00)}} @else{{@num_format($transaction->shipping_charges)}} @endif" data-default="0.00">
    <input type="hidden" name="final_total" id="final_total_input" value=0>
</div>