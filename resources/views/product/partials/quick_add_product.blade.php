
<div class="modal-dialog modal-xl" role="document">
  <div class="modal-content" style="border-radius: var(--pos-radius-lg) !important; overflow: hidden !important; border: none !important; box-shadow: var(--pos-shadow-lg) !important;">
    {!! Form::open(['url' => action([\App\Http\Controllers\ProductController::class, 'saveQuickProduct']), 'method' => 'post', 'id' => 'quick_add_product_form' ]) !!}

    <div class="modal-header-material" style="background: linear-gradient(135deg, var(--pos-primary) 0%, #0d47a1 100%) !important; color: white !important; padding: 20px 24px !important;">
      <h4 class="modal-title" style="display: flex; align-items: center; gap: 12px; font-weight: 600 !important; margin: 0 !important; color: white !important;">
        <i class="material-icons" style="font-size: 28px !important;">add_box</i>
        @lang( 'product.add_new_product' )
      </h4>
      <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white !important; opacity: 0.8 !important;">×</button>
    </div>

    <div class="modal-body" style="padding: 32px; background: #f8fafc;">
      <div class="row">
        {{-- Basic Information Section --}}
        <div class="col-md-12 mb-24">
            <div class="material-summary-card" style="background: white !important; padding: 24px !important; border: 1.5px solid var(--pos-border) !important;">
                <div class="row">
                    <div class="col-md-4">
                      <div class="material-input-group">
                        {!! Form::label('name', __('product.product_name') . ':*') !!}
                        <div class="material-input-wrapper">
                            <span class="input-prefix"><i class="fa fa-tag"></i></span>
                            {!! Form::text('name', $product_name, ['required', 'placeholder' => __('product.product_name')]); !!}
                            {!! Form::select('type', ['single' => 'Single', 'variable' => 'Variable'], 'single', ['class' => 'hide', 'id' => 'type']); !!}
                        </div>
                      </div>
                    </div>

                    <div class="col-sm-4">
                      <div class="material-input-group">
                        {!! Form::label('sku', __('product.sku') . ':') !!}
                        <div class="material-input-wrapper">
                            <span class="input-prefix"><i class="fa fa-barcode"></i></span>
                            {!! Form::text('sku', null, ['placeholder' => __('product.sku')]); !!}
                        </div>
                      </div>
                    </div>

                    <div class="col-sm-4">
                      <div class="material-input-group">
                        {!! Form::label('barcode_type', __('product.barcode_type') . ':*') !!}
                        <div class="material-input-wrapper">
                            <span class="input-prefix"><i class="fa fa-align-justify"></i></span>
                            {!! Form::select('barcode_type', $barcode_types, 'C128', ['class' => 'form-control select2', 'required', 'style' => 'width: 100%;']); !!}
                        </div>
                      </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- categorization Section --}}
        <div class="col-md-12">
            <div class="row">
                <div class="col-sm-3">
                  <div class="material-input-group">
                    {!! Form::label('unit_id', __('product.unit') . ':*') !!}
                    <div class="material-input-wrapper">
                        <span class="input-prefix"><i class="fa fa-balance-scale"></i></span>
                        {!! Form::select('unit_id', $units, null, ['class' => 'form-control select2', 'required', 'style' => 'width: 100%;']); !!}
                    </div>
                  </div>
                </div>

                <div class="col-sm-3 @if(!session('business.enable_sub_units')) hide @endif">
                  <div class="material-input-group">
                    {!! Form::label('sub_unit_ids', __('lang_v1.related_sub_units') . ':') !!}
                    <div class="material-input-wrapper">
                        {!! Form::select('sub_unit_ids[]', [], null, ['class' => 'form-control select2', 'multiple', 'id' => 'sub_unit_ids', 'style' => 'width: 100%;']); !!}
                    </div>
                  </div>
                </div>

                <div class="col-sm-3">
                  <div class="material-input-group">
                    {!! Form::label('brand_id', __('product.brand') . ':') !!}
                    <div class="material-input-wrapper">
                        <span class="input-prefix"><i class="fa fa-copyright"></i></span>
                        {!! Form::select('brand_id', $brands, null, ['placeholder' => __('messages.please_select'), 'class' => 'form-control select2', 'style' => 'width: 100%;']); !!}
                    </div>
                  </div>
                </div>
                
                <div class="col-sm-3">
                  <div class="material-input-group">
                    {!! Form::label('category_id', __('product.category') . ':') !!}
                    <div class="material-input-wrapper">
                        <span class="input-prefix"><i class="fa fa-folder-open"></i></span>
                        {!! Form::select('category_id', $categories, null, ['placeholder' => __('messages.please_select'), 'class' => 'form-control select2', 'style' => 'width: 100%;']); !!}
                    </div>
                  </div>
                </div>

                <div class="col-sm-3 @if(!(session('business.enable_category') && session('business.enable_sub_category'))) hide @endif">
                  <div class="material-input-group">
                    {!! Form::label('sub_category_id', __('product.sub_category') . ':') !!}
                    <div class="material-input-wrapper">
                        {!! Form::select('sub_category_id', [], null, ['placeholder' => __('messages.please_select'), 'class' => 'form-control select2', 'style' => 'width: 100%;']); !!}
                    </div>
                  </div>
                </div>

                <div class="col-sm-3">
                  <div class="material-input-group" style="padding-top: 32px;">
                    <label style="cursor: pointer; font-weight: 700; color: var(--pos-text);">
                      {!! Form::checkbox('enable_stock', 1, true, ['class' => 'input-icheck', 'id' => 'enable_stock']); !!} 
                      <span style="margin-left: 8px;">@lang('product.manage_stock')</span>
                    </label>
                  </div>
                </div>

                <div class="col-sm-3" id="alert_quantity_div">
                  <div class="material-input-group">
                    {!! Form::label('alert_quantity', __('product.alert_quantity') . ':') !!}
                    <div class="material-input-wrapper">
                        <span class="input-prefix"><i class="fa fa-bell"></i></span>
                        {!! Form::text('alert_quantity', null, ['class' => 'input_number', 'placeholder' => __('product.alert_quantity'), 'min' => '0']); !!}
                    </div>
                  </div>
                </div>

                @if(!empty($common_settings['enable_product_warranty']))
                <div class="col-sm-3">
                  <div class="material-input-group">
                    {!! Form::label('warranty_id', __('lang_v1.warranty') . ':') !!}
                    <div class="material-input-wrapper">
                        {!! Form::select('warranty_id', $warranties, null, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'style' => 'width: 100%;']); !!}
                    </div>
                  </div>
                </div>
                @endif
                
                @if(session('business.enable_product_expiry'))
                <div class="col-sm-3">
                  <div class="material-input-group">
                    {!! Form::label('expiry_period', __('product.expires_in') . ':') !!}
                    <div style="display: flex; gap: 8px;">
                        <div class="material-input-wrapper" style="flex: 1;">
                            {!! Form::text('expiry_period', null, ['class' => 'input_number', 'placeholder' => 'No.']); !!}
                        </div>
                        <div class="material-input-wrapper" style="flex: 1.5;">
                            {!! Form::select('expiry_period_type', ['months'=>__('product.months'), 'days'=>__('product.days'), '' =>__('product.not_applicable') ], 'months', ['class' => 'form-control select2', 'style' => 'width: 100%;', 'id' => 'expiry_period_type']); !!}
                        </div>
                    </div>
                  </div>
                </div>
                @endif

                <div class="col-sm-3">
                  <div class="material-input-group">
                    {!! Form::label('product_locations', __('business.business_locations') . ':') !!}
                    <div class="material-input-wrapper">
                        <span class="input-prefix"><i class="fa fa-map-marker-alt"></i></span>
                        {!! Form::select('product_locations[]', $business_locations, null, ['class' => 'form-control select2', 'multiple', 'id' => 'product_locations', 'style' => 'width: 100%;']); !!}
                    </div>
                  </div>
                </div>

                <div class="col-sm-3">
                  <div class="material-input-group">
                    {!! Form::label('weight',  __('lang_v1.weight') . ':') !!}
                    <div class="material-input-wrapper">
                        {!! Form::text('weight', null, ['placeholder' => __('lang_v1.weight')]); !!}
                    </div>
                  </div>
                </div>
            </div>
        </div>

        {{-- Tax & Settings Section --}}
        <div class="col-md-12 mt-24">
            <div class="row">
                <div class="col-sm-4">
                  <div class="material-input-group">
                    {!! Form::label('tax', __('product.applicable_tax') . ':') !!}
                    <div class="material-input-wrapper">
                        <span class="input-prefix"><i class="fa fa-percent"></i></span>
                        {!! Form::select('tax', $taxes, null, ['placeholder' => __('messages.please_select'), 'class' => 'form-control select2', 'style' => 'width: 100%;'], $tax_attributes); !!}
                    </div>
                  </div>
                </div>

                <div class="col-sm-4">
                  <div class="material-input-group">
                    {!! Form::label('tax_type', __('product.selling_price_tax_type') . ':*') !!}
                    <div class="material-input-wrapper">
                        {!! Form::select('tax_type', ['inclusive' => __('product.inclusive'), 'exclusive' => __('product.exclusive')], 'exclusive', ['class' => 'form-control select2', 'required', 'style' => 'width: 100%;']); !!}
                    </div>
                  </div>
                </div>

                <div class="col-sm-4" style="padding-top: 32px;">
                  <div class="material-input-group">
                    <label style="cursor: pointer; font-weight: 700; color: var(--pos-text);">
                      {!! Form::checkbox('enable_sr_no', 1, false, ['class' => 'input-icheck']); !!} 
                      <span style="margin-left: 8px;">@lang('lang_v1.enable_imei_or_sr_no')</span>
                    </label>
                  </div>
                </div>

                <div class="col-sm-4" style="padding-top: 10px;">
                  <div class="material-input-group">
                    <label style="cursor: pointer; font-weight: 700; color: var(--pos-text);">
                      {!! Form::checkbox('not_for_selling', 1, false, ['class' => 'input-icheck']); !!} 
                      <span style="margin-left: 8px;">@lang('lang_v1.not_for_selling')</span>
                    </label>
                  </div>
                </div>
            </div>
        </div>

        {{-- Custom Fields Section --}}
        @php
          $custom_labels = json_decode(session('business.custom_labels'), true);
          $product_custom_field1 = !empty($custom_labels['product']['custom_field_1']) ? $custom_labels['product']['custom_field_1'] : __('lang_v1.product_custom_field1');
          $product_custom_field2 = !empty($custom_labels['product']['custom_field_2']) ? $custom_labels['product']['custom_field_2'] : __('lang_v1.product_custom_field2');
          $product_custom_field3 = !empty($custom_labels['product']['custom_field_3']) ? $custom_labels['product']['custom_field_3'] : __('lang_v1.product_custom_field3');
          $product_custom_field4 = !empty($custom_labels['product']['custom_field_4']) ? $custom_labels['product']['custom_field_4'] : __('lang_v1.product_custom_field4');
        @endphp
        <div class="col-md-12 mt-24">
            <div class="row">
                <div class="col-sm-3">
                  <div class="material-input-group">
                    {!! Form::label('product_custom_field1',  $product_custom_field1 . ':') !!}
                    <div class="material-input-wrapper">
                        {!! Form::text('product_custom_field1', null, ['placeholder' => $product_custom_field1]); !!}
                    </div>
                  </div>
                </div>

                <div class="col-sm-3">
                  <div class="material-input-group">
                    {!! Form::label('product_custom_field2',  $product_custom_field2 . ':') !!}
                    <div class="material-input-wrapper">
                        {!! Form::text('product_custom_field2',null, ['placeholder' => $product_custom_field2]); !!}
                    </div>
                  </div>
                </div>

                <div class="col-sm-3">
                  <div class="material-input-group">
                    {!! Form::label('product_custom_field3',  $product_custom_field3 . ':') !!}
                    <div class="material-input-wrapper">
                        {!! Form::text('product_custom_field3', null, ['placeholder' => $product_custom_field3]); !!}
                    </div>
                  </div>
                </div>

                <div class="col-sm-3">
                  <div class="material-input-group">
                    {!! Form::label('product_custom_field4',  $product_custom_field4 . ':') !!}
                    <div class="material-input-wrapper">
                        {!! Form::text('product_custom_field4', null, ['placeholder' => $product_custom_field4]); !!}
                    </div>
                  </div>
                </div>
            </div>
        </div>

        {{-- Module Form Parts --}}
        @if(!empty($module_form_parts))
          <div class="col-md-12">
            @foreach($module_form_parts as $key => $value)
                @if(!empty($value['template_path']))
                  @php
                    $template_data = $value['template_data'] ?: [];
                  @endphp
                  @include($value['template_path'], $template_data)
                @endif
            @endforeach
          </div>
        @endif

        {{-- Pricing Section --}}
        <div class="col-md-12 mt-32">
            <div style="background: white; border-radius: var(--pos-radius-md); border: 1.5px solid var(--pos-border); padding: 24px;">
                <h5 style="margin-top: 0; margin-bottom: 20px; font-weight: 700; color: var(--pos-primary); display: flex; align-items: center; gap: 8px;">
                    <i class="material-icons" style="font-size: 20px;">payments</i>
                    @lang('product.product_pricing')
                </h5>
                @include('product.partials.single_product_form_part', ['profit_percent' => $default_profit_percent, 'quick_add' => true ])
            </div>
        </div>

        @if(!empty($product_for) && $product_for == 'pos')
          <div class="col-md-12 mt-24">
            @include('product.partials.quick_product_opening_stock', ['locations' => $locations])
          </div>
        @endif
      </div>
    </div>

    <div class="modal-footer" style="padding: 24px 32px !important; background: white !important; border-top: 1.5px solid var(--pos-border) !important;">
      <button type="submit" class="btn-material" id="submit_quick_product" style="background: var(--pos-primary) !important; color: white !important; font-weight: 700 !important; padding: 0 32px !important; min-width: 160px !important; height: 48px !important;">
        <i class="fa fa-save"></i> @lang( 'messages.save' )
      </button>
      <button type="button" class="btn-material" data-dismiss="modal" style="background: var(--pos-bg) !important; color: var(--pos-text) !important; border: 1.5px solid var(--pos-border) !important; padding: 0 24px !important; height: 48px !important;">
        @lang( 'messages.close' )
      </button>
    </div>


    {!! Form::close() !!}
  </div>
</div>

<script type="text/javascript">
  $(document).ready(function(){
    $("form#quick_add_product_form").validate({
      rules: {
          sku: {
              remote: {
                  url: "/products/check_product_sku",
                  type: "post",
                  data: {
                      sku: function() {
                          return $( "#sku" ).val();
                      },
                      product_id: function() {
                          if($('#product_id').length > 0 ){
                              return $('#product_id').val();
                          } else {
                              return '';
                          }
                      },
                  }
              }
          },
          expiry_period:{
              required: {
                  depends: function(element) {
                      return ($('#expiry_period_type').val().trim() != '');
                  }
              }
          }
      },
      messages: {
          sku: {
              remote: LANG.sku_already_exists
          }
      },
      submitHandler: function (form) {
        
        var form = $("form#quick_add_product_form");
        var url = form.attr('action');
        form.find('button[type="submit"]').attr('disabled', true);
        $.ajax({
            method: "POST",
            url: url,
            dataType: 'json',
            data: $(form).serialize(),
            success: function(data){
                $('.quick_add_product_modal').modal('hide');
                if( data.success){
                    toastr.success(data.msg);
                    if (typeof get_purchase_entry_row !== 'undefined') {
                      var selected_location = $('#location_id').val();
                      var location_check = true;
                      if (data.locations && selected_location && data.locations.indexOf(selected_location) == -1) {
                        location_check = false;
                      }
                      if (location_check) {
                        get_purchase_entry_row( data.product.id, 0 );
                      }
                      
                    }
                    $(document).trigger({type: "quickProductAdded", 'product': data.product, 'variation': data.variation });
                } else {
                    toastr.error(data.msg);
                }
            }
        });
        return false;
      }
    });
  });
</script>