{{-- Current Sale Panel Header --}}
<div class="current-sale-header">
    <div class="header-left">
        <i class="fas fa-shopping-cart"></i>
        <h2 class="current-sale-title">Current Sale</h2>
    </div>
    <div class="header-right">
        <span class="item-count-badge" id="total_quantity_badge">0 Items</span>
    </div>
</div>

{{-- Hidden Fields --}}
<div class="hide">
    <input type="hidden" name="pay_term_number" id="pay_term_number" value="{{$walk_in_customer['pay_term_number'] ?? ''}}">
    <input type="hidden" name="pay_term_type" id="pay_term_type" value="{{$walk_in_customer['pay_term_type'] ?? ''}}">

    @if(!empty($commission_agent))
        {!! Form::select('commission_agent', $commission_agent, null, ['class' => 'form-control select2', 'placeholder' => __('lang_v1.commission_agent'), 'id' => 'commission_agent']); !!}
    @endif

    @if(!empty($pos_settings['show_invoice_layout']))
        {!! Form::select('invoice_layout_id', $invoice_layouts, $default_location->invoice_layout_id ?? null, ['class' => 'form-control select2', 'placeholder' => __('lang_v1.select_invoice_layout'), 'id' => 'invoice_layout_id']); !!}
    @endif

    @if(isset($sub_tax) && is_array($sub_tax))
        @foreach ($sub_tax as $key => $value)
            <input type="hidden" name="sub_tax[{{ $key }}]" id="sub_tax_{{ $key }}" value="{{ @num_format($value) }}">
        @endforeach
    @endif
</div>

{{-- Customer Selection Bar --}}
<div class="customer-bar">
    <div class="customer-icon">
        <i class="fas fa-user"></i>
    </div>
    <div class="customer-select-wrapper">
        {!! Form::select('contact_id', [], null, [
            'class' => 'form-control mousetrap select2',
            'id' => 'customer_id',
            'placeholder' => 'Walk-in Customer',
            'required',
        ]) !!}
        <input type="hidden" id="default_customer_id" value="{{ $walk_in_customer['id'] ?? ''}}">
        <input type="hidden" id="default_customer_name" value="{{ $walk_in_customer['name'] ?? ''}}">
    </div>

    <button type="button" class="customer-btn btn-modal"
        data-href="{{action([\App\Http\Controllers\ContactController::class, 'create'], ['type' => 'customer'])}}"
        data-container=".contact_modal" title="Add Customer">
        <i class="fas fa-user-plus"></i>
    </button>
</div>

{{-- Styling for search dropdowns (jQuery UI autocomplete + custom) --}}
<style>
    /* Fix jQuery UI autocomplete dropdown — card padding & appearance */
    .ui-autocomplete.ui-menu {
        padding: 8px 0 !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 10px !important;
        box-shadow: 0 8px 24px rgba(0,0,0,0.15) !important;
        max-height: 320px !important;
        overflow-y: auto !important;
        overflow-x: hidden !important;
        margin-top: 3px !important;
        background: #fff !important;
    }
    .ui-autocomplete.ui-menu .ui-menu-item {
        margin: 0 !important;
        padding: 0 !important;
        border: none !important;
        list-style: none !important;
    }
    .ui-autocomplete.ui-menu .ui-menu-item .pos-search-item {
        padding: 9px 20px !important;
    }
    .ui-autocomplete.ui-menu::-webkit-scrollbar { width: 5px; }
    .ui-autocomplete.ui-menu::-webkit-scrollbar-track { background: #f8fafc; border-radius: 10px; }
    .ui-autocomplete.ui-menu::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }

    /* Custom dropdown scrollbar */
    #pos_search_dropdown::-webkit-scrollbar { width: 5px; }
    #pos_search_dropdown::-webkit-scrollbar-track { background: #f8fafc; border-radius: 10px; }
    #pos_search_dropdown::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
</style>

{{-- Product Search Bar --}}
<div class="search-bar" style="position:relative;">
    <div class="search-icon">
        <i class="fas fa-search"></i>
    </div>
    {!! Form::text('search_product', null, [
        'class' => 'form-control search-input',
        'id' => 'search_product',
        'placeholder' => __('lang_v1.search_product_placeholder'),
        'disabled' => is_null($default_location)? true : false,
        'autofocus' => is_null($default_location)? false : true,
        'autocomplete' => 'off',
    ]) !!}
    <div id="pos_search_dropdown" style="display:none;position:absolute;top:100%;left:0;right:0;z-index:100000;background:#fff;border:1px solid #e2e8f0;border-radius:10px;box-shadow:0 8px 24px rgba(0,0,0,0.15);max-height:320px;overflow-y:auto;overflow-x:hidden;margin-top:3px;padding:8px 12px;"></div>
</div>

{{-- Cart Items List --}}
<div class="cart-items-container">
    <input type="hidden" name="sell_price_tax" id="sell_price_tax" value="{{$business_details->sell_price_tax}}">
    <input type="hidden" id="product_row_count" value="0">
    @php
        $hide_tax = '';
        if( session()->get('business.enable_inline_tax') == 0){
            $hide_tax = 'hide';
        }
        $is_service_staff_enabled = !empty($pos_settings['inline_service_staff']);
        $is_warranty_enabled = !empty($common_settings['enable_product_warranty']);
    @endphp

    <div class="pos_product_div">
        <table class="table" id="pos_table">
            <thead>
                <tr>
                    <th class="col-product">Product</th>
                    <th class="col-qty">Qty</th>
                    @if($is_service_staff_enabled)
                        <th class="col-staff">Staff</th>
                    @endif
                    @if(session()->get('business.enable_inline_tax') == 1)
                        <th class="col-price">Price</th>
                    @endif
                    @if($is_warranty_enabled)
                        <th class="col-warranty">Warranty</th>
                    @endif
                    <th class="col-discount">Disc</th>
                    <th class="col-total">Subtotal</th>
                    <th class="col-action"><i class="fas fa-trash-alt"></i></th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

{{-- Hidden Inputs for Form Submission --}}
<div class="hide">
    @php
        $is_discount_enabled = $pos_settings['disable_discount'] != 1 ? true : false;
    @endphp

    @if($is_discount_enabled)
        <input type="hidden" name="discount_type" id="discount_type" value="@if(empty($edit)){{'percentage'}}@else{{$transaction->discount_type ?? 'percentage'}}@endif" data-default="percentage">
        <input type="hidden" name="discount_amount" id="discount_amount" value="@if(empty($edit)) {{@num_format($business_details->default_sales_discount)}} @else {{@num_format($transaction->discount_amount ?? 0)}} @endif" data-default="{{$business_details->default_sales_discount}}">
    @endif

    <input type="hidden" name="tax_rate_id" id="tax_rate_id" value="@if(empty($edit)) {{$business_details->default_sales_tax}} @else {{$transaction->tax_id ?? ''}} @endif" data-default="{{$business_details->default_sales_tax}}">
    <input type="hidden" name="tax_calculation_amount" id="tax_calculation_amount" value="@if(empty($edit)) {{@num_format($business_details->tax_calculation_amount ?? 0)}} @else {{@num_format($transaction->tax->amount ?? 0)}} @endif" data-default="{{$business_details->tax_calculation_amount ?? 0}}">

    <input type="hidden" name="shipping_charges" id="shipping_charges" value="@if(empty($edit)){{@num_format(0.00)}} @else{{@num_format($transaction->shipping_charges ?? 0)}} @endif" data-default="0.00">
    <input type="hidden" name="round_off_amount" id="round_off_amount" value="0">
    <input type="hidden" name="final_total" id="final_total_input" value="0">

    {{-- Additional spans for totals display used by pos.js --}}
    <span class="total_quantity">0</span>
    <span id="shipping_charges_amount">0</span>
</div>
