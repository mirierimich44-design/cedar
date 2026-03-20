@if(!session('business.enable_price_tax')) 
  @php
    $default = 0;
  @endphp
@else
  @php
    $default = null;
  @endphp
@endif

<div class="table-responsive">
    <table class="table table-bordered add-product-price-table material-pricing-table" style="background: white !important; border-radius: var(--pos-radius-md) !important; overflow: hidden !important; border: 1.5px solid var(--pos-border) !important;">
        <thead>
            <tr style="background: #F8FAFC !important; color: var(--pos-text) !important;">
              <th class="text-center" style="padding: 12px !important; border-bottom: 2px solid var(--pos-border) !important; font-weight: 700 !important;">@lang('product.default_purchase_price')</th>
              <th class="text-center" style="padding: 12px !important; border-bottom: 2px solid var(--pos-border) !important; font-weight: 700 !important;">@lang('product.profit_percent')</th>
              <th class="text-center" style="padding: 12px !important; border-bottom: 2px solid var(--pos-border) !important; font-weight: 700 !important;">@lang('product.default_selling_price')</th>
              @if(empty($quick_add))
                <th class="text-center" style="padding: 12px !important; border-bottom: 2px solid var(--pos-border) !important; font-weight: 700 !important;">@lang('lang_v1.product_image')</th>
              @endif
            </tr>
        </thead>
        <tbody>
            <tr>
              <td style="padding: 16px !important;">
                <div class="row">
                    <div class="col-md-6">
                      <label style="font-size: 11px; text-transform: uppercase; color: var(--pos-text-secondary); display: block; margin-bottom: 5px;">@lang('product.exc_of_tax'):*</label>
                      <div class="material-input-wrapper">
                        {!! Form::text('single_dpp', $default, ['class' => 'form-control dpp input_number', 'placeholder' => __('product.exc_of_tax'), 'required', 'id' => 'single_dpp', 'style' => 'text-align: center !important;']); !!}
                      </div>
                    </div>
                    <div class="col-md-6">
                      <label style="font-size: 11px; text-transform: uppercase; color: var(--pos-text-secondary); display: block; margin-bottom: 5px;">@lang('product.inc_of_tax'):*</label>
                      <div class="material-input-wrapper">
                        {!! Form::text('single_dpp_inc_tax', $default, ['class' => 'form-control dpp_inc_tax input_number', 'placeholder' => __('product.inc_of_tax'), 'required', 'id' => 'single_dpp_inc_tax', 'style' => 'text-align: center !important;']); !!}
                      </div>
                    </div>
                </div>
              </td>

              <td style="padding: 16px !important; vertical-align: middle !important;">
                <label style="font-size: 11px; text-transform: uppercase; color: var(--pos-text-secondary); display: block; margin-bottom: 5px;">Margin %</label>
                <div class="material-input-wrapper">
                    {!! Form::text('profit_percent', @num_format($profit_percent), ['class' => 'form-control input_number', 'id' => 'profit_percent', 'required', 'style' => 'text-align: center !important;']); !!}
                </div>
              </td>

              <td style="padding: 16px !important;">
                <div class="row">
                    <div class="col-md-6">
                      <label style="font-size: 11px; text-transform: uppercase; color: var(--pos-text-secondary); display: block; margin-bottom: 5px;">@lang('product.exc_of_tax'):*</label>
                      <div class="material-input-wrapper">
                        {!! Form::text('single_dsp', $default, ['class' => 'form-control dsp input_number', 'placeholder' => __('product.exc_of_tax'), 'id' => 'single_dsp', 'required', 'style' => 'text-align: center !important;']); !!}
                      </div>
                    </div>
                    <div class="col-md-6">
                      <label style="font-size: 11px; text-transform: uppercase; color: var(--pos-text-secondary); display: block; margin-bottom: 5px;">@lang('product.inc_of_tax'):*</label>
                      <div class="material-input-wrapper">
                        {!! Form::text('single_dsp_inc_tax', $default, ['class' => 'form-control dsp_inc_tax input_number', 'placeholder' => __('product.inc_of_tax'), 'id' => 'single_dsp_inc_tax', 'required', 'style' => 'text-align: center !important;']); !!}
                      </div>
                    </div>
                </div>
              </td>
              @if(empty($quick_add))
              <td style="padding: 16px !important; vertical-align: middle !important;">
                  <div class="form-group" style="margin-bottom: 0;">
                    {!! Form::file('variation_images[]', ['class' => 'variation_images', 'accept' => 'image/*', 'multiple']); !!}
                  </div>
              </td>
              @endif
            </tr>
        </tbody>
    </table>
</div>