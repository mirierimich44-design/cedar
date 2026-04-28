@if(!session('business.enable_price_tax'))
  @php $default = 0; @endphp
@else
  @php $default = null; @endphp
@endif

<style>
.price-flow { display: flex; align-items: stretch; gap: 0; margin-bottom: 4px; }
.price-flow-card {
    flex: 1;
    background: #fff;
    border: 1.5px solid #e5e7eb;
    border-radius: 10px;
    padding: 16px 18px;
}
.price-flow-card .pf-title {
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .6px;
    color: #6b7280;
    margin-bottom: 12px;
    display: flex;
    align-items: center;
    gap: 6px;
}
.price-flow-card .pf-title .pf-dot {
    width: 8px; height: 8px;
    border-radius: 50%;
    flex-shrink: 0;
}
.price-flow-card .pf-fields { display: flex; gap: 10px; }
.price-flow-card .pf-field { flex: 1; }
.price-flow-card .pf-field label {
    display: block;
    font-size: 10px;
    font-weight: 600;
    color: #9ca3af;
    text-transform: uppercase;
    letter-spacing: .4px;
    margin-bottom: 4px;
}
.price-flow-card .pf-field .form-control {
    font-size: 14px;
    font-weight: 600;
    text-align: center;
    border-radius: 7px;
    border: 1px solid #d1d5db;
    padding: 8px 10px;
    color: #111827;
}
.price-flow-card.purchase { border-top: 3px solid #f59e0b; }
.price-flow-card.margin   { border-top: 3px solid #8b5cf6; flex: 0 0 160px; }
.price-flow-card.selling  { border-top: 3px solid #10b981; }
.price-flow-arrow {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0 10px;
    color: #d1d5db;
    font-size: 20px;
    flex-shrink: 0;
    align-self: center;
}
.price-flow-card.margin .pf-fields { justify-content: center; }
.price-flow-card.margin .pf-field  { flex: 0 0 100%; max-width: 120px; }
</style>

<div class="price-flow">

    {{-- ── Purchase Price ── --}}
    <div class="price-flow-card purchase">
        <div class="pf-title">
            <span class="pf-dot" style="background:#f59e0b;"></span>
            @lang('product.default_purchase_price')
        </div>
        <div class="pf-fields">
            <div class="pf-field">
                <label>@lang('product.exc_of_tax') *</label>
                {!! Form::text('single_dpp', $default, [
                    'class'       => 'form-control dpp input_number',
                    'placeholder' => '0.00',
                    'required',
                    'id'          => 'single_dpp',
                ]) !!}
            </div>
            <div class="pf-field">
                <label>@lang('product.inc_of_tax') *</label>
                {!! Form::text('single_dpp_inc_tax', $default, [
                    'class'       => 'form-control dpp_inc_tax input_number',
                    'placeholder' => '0.00',
                    'required',
                    'id'          => 'single_dpp_inc_tax',
                ]) !!}
            </div>
        </div>
    </div>

    <div class="price-flow-arrow">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="9 18 15 12 9 6"/>
        </svg>
    </div>

    {{-- ── Margin % ── --}}
    <div class="price-flow-card margin">
        <div class="pf-title">
            <span class="pf-dot" style="background:#8b5cf6;"></span>
            @lang('product.profit_percent')
        </div>
        <div class="pf-fields">
            <div class="pf-field">
                <label>%</label>
                {!! Form::text('profit_percent', @num_format($profit_percent), [
                    'class'    => 'form-control input_number',
                    'id'       => 'profit_percent',
                    'required',
                ]) !!}
            </div>
        </div>
    </div>

    <div class="price-flow-arrow">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="9 18 15 12 9 6"/>
        </svg>
    </div>

    {{-- ── Selling Price ── --}}
    <div class="price-flow-card selling">
        <div class="pf-title">
            <span class="pf-dot" style="background:#10b981;"></span>
            @lang('product.default_selling_price')
        </div>
        <div class="pf-fields">
            <div class="pf-field">
                <label>@lang('product.exc_of_tax') *</label>
                {!! Form::text('single_dsp', $default, [
                    'class'       => 'form-control dsp input_number',
                    'placeholder' => '0.00',
                    'id'          => 'single_dsp',
                    'required',
                ]) !!}
            </div>
            <div class="pf-field">
                <label>@lang('product.inc_of_tax') *</label>
                {!! Form::text('single_dsp_inc_tax', $default, [
                    'class'       => 'form-control dsp_inc_tax input_number',
                    'placeholder' => '0.00',
                    'id'          => 'single_dsp_inc_tax',
                    'required',
                ]) !!}
            </div>
        </div>
    </div>

    {{-- ── Product Image (full-width variant or inline) ── --}}
    @if(empty($quick_add))
    <div class="price-flow-arrow" style="color:transparent;">|</div>
    <div class="price-flow-card" style="border-top:3px solid #3b82f6; flex:0 0 180px;">
        <div class="pf-title">
            <span class="pf-dot" style="background:#3b82f6;"></span>
            @lang('lang_v1.product_image')
        </div>
        <div class="pf-fields">
            <div class="pf-field" style="flex:1;">
                {!! Form::file('variation_images[]', ['class' => 'variation_images', 'accept' => 'image/*', 'multiple']) !!}
            </div>
        </div>
    </div>
    @endif

</div>
