
<div class="modal-dialog modal-xl" role="document">
  <div class="modal-content">
    @php
      $form_id = 'contact_add_form';
      if(isset($quick_add)){
        $form_id = 'quick_add_contact';
      }

      if(isset($store_action)) {
        $url = $store_action;
        $type = 'lead';
        $customer_groups = [];
      } else {
        $url = action([\App\Http\Controllers\ContactController::class, 'store']);
        $type = isset($selected_type) ? $selected_type : '';
        $sources = [];
        $life_stages = [];
      }
    @endphp
    {!! Form::open(['url' => $url, 'method' => 'post', 'id' => $form_id ]) !!}

    <div class="modal-header-material" style="background: linear-gradient(135deg, var(--pos-primary) 0%, #0d47a1 100%) !important; color: white !important;">
      <h4 class="modal-title">
        <i class="material-icons">person_add</i>
        @lang('contact.add_contact')
      </h4>
      <button type="button" class="close" data-dismiss="modal" aria-label="Close">×</button>
    </div>

    <div class="modal-body" style="padding: 32px; background: #f8fafc;">
        <div class="row">            
            {{-- Contact Type & Basic Info Section --}}
            <div class="col-md-12 mb-24">
                <div class="material-summary-card" style="background: white !important; padding: 24px !important; border: 1.5px solid var(--pos-border) !important;">
                    <div class="row">
                        <div class="col-md-4 contact_type_div">
                            <div class="material-input-group">
                                {!! Form::label('type', __('contact.contact_type') . ':*' ) !!}
                                <div class="material-input-wrapper">
                                    <span class="input-prefix"><i class="fa fa-user"></i></span>
                                    {!! Form::select('type', $types, $type , ['class' => 'form-control select2', 'id' => 'contact_type', 'required', 'style' => 'width: 100%;']); !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="material-input-group" style="padding-top: 32px;">
                                <label class="radio-inline" style="font-weight: 700; color: var(--pos-text);">
                                    <input type="radio" name="contact_type_radio" id="inlineRadio1" value="individual" checked>
                                    @lang('lang_v1.individual')
                                </label>
                                <label class="radio-inline" style="font-weight: 700; color: var(--pos-text); margin-left: 20px;">
                                    <input type="radio" name="contact_type_radio" id="inlineRadio2" value="business">
                                    @lang('business.business')
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="material-input-group">
                                {!! Form::label('contact_id', __('lang_v1.contact_id') . ':') !!}
                                <div class="material-input-wrapper">
                                    <span class="input-prefix"><i class="fa fa-id-badge"></i></span>
                                    {!! Form::text('contact_id', null, ['placeholder' => __('lang_v1.contact_id')]); !!}
                                </div>
                                <small class="text-muted">@lang('lang_v1.leave_empty_to_autogenerate')</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Main Details Section --}}
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-3 business" style="display: none;">
                        <div class="material-input-group">
                            {!! Form::label('supplier_business_name', __('business.business_name') . ':*') !!}
                            <div class="material-input-wrapper">
                                <span class="input-prefix"><i class="fa fa-briefcase"></i></span>
                                {!! Form::text('supplier_business_name', null, ['placeholder' => __('business.business_name')]); !!}
                            </div>
                        </div>
                    </div>

                    <div class="col-md-2 individual">
                        <div class="material-input-group">
                            {!! Form::label('prefix', __( 'business.prefix' ) . ':') !!}
                            <div class="material-input-wrapper">
                                {!! Form::text('prefix', null, ['placeholder' => __( 'business.prefix_placeholder' ) ]); !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 individual">
                        <div class="material-input-group">
                            {!! Form::label('first_name', __( 'business.first_name' ) . ':*') !!}
                            <div class="material-input-wrapper">
                                <span class="input-prefix"><i class="fa fa-user"></i></span>
                                {!! Form::text('first_name', null, ['required', 'placeholder' => __( 'business.first_name' ) ]); !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 individual">
                        <div class="material-input-group">
                            {!! Form::label('middle_name', __( 'lang_v1.middle_name' ) . ':') !!}
                            <div class="material-input-wrapper">
                                {!! Form::text('middle_name', null, ['placeholder' => __( 'lang_v1.middle_name' ) ]); !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 individual">
                        <div class="material-input-group">
                            {!! Form::label('last_name', __( 'business.last_name' ) . ':') !!}
                            <div class="material-input-wrapper">
                                {!! Form::text('last_name', null, ['placeholder' => __( 'business.last_name' ) ]); !!}
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="material-input-group">
                            {!! Form::label('mobile', __('contact.mobile') . ':*') !!}
                            <div class="material-input-wrapper">
                                <span class="input-prefix"><i class="fa fa-mobile-alt"></i></span>
                                {!! Form::text('mobile', null, ['required', 'placeholder' => __('contact.mobile')]); !!}
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="material-input-group">
                            {!! Form::label('email', __('business.email') . ':') !!}
                            <div class="material-input-wrapper">
                                <span class="input-prefix"><i class="fa fa-envelope"></i></span>
                                {!! Form::email('email', null, ['placeholder' => __('business.email')]); !!}
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 customer_fields">
                        <div class="material-input-group">
                            {!! Form::label('customer_group_id', __('lang_v1.customer_group') . ':') !!}
                            <div class="material-input-wrapper">
                                <span class="input-prefix"><i class="fa fa-users"></i></span>
                                {!! Form::select('customer_group_id', $customer_groups, '', ['class' => 'form-control select2', 'style' => 'width: 100%;']); !!}
                            </div>
                        </div>
                    </div>

                    @if(config('constants.enable_contact_assign') && $type !== 'lead')
                        <div class="col-md-3">
                            <div class="material-input-group">
                                {!! Form::label('assigned_to_users', __('lang_v1.assigned_to') . ':' ) !!}
                                <div class="material-input-wrapper">
                                    <span class="input-prefix"><i class="fa fa-user-tag"></i></span>
                                    {!! Form::select('assigned_to_users[]', $users ?? [], null , ['class' => 'form-control select2', 'id' => 'assigned_to_users', 'multiple', 'style' => 'width: 100%;']); !!}
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- More Info Section --}}
            <div class="col-md-12 mt-24">
                <div style="border-top: 1.5px dashed var(--pos-border); padding-top: 24px; margin-bottom: 16px;">
                    <button type="button" class="btn-material more_btn" data-target="#more_div" style="background: var(--pos-bg) !important; color: var(--pos-primary) !important; font-weight: 700 !important; width: 100% !important; height: 48px !important; border: 1.5px solid var(--pos-border) !important;">
                        <i class="fa fa-chevron-down"></i> @lang('lang_v1.more_info')
                    </button>
                </div>

                <div id="more_div" class="hide">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="material-input-group">
                                {!! Form::label('tax_number', __('contact.tax_no') . ':') !!}
                                <div class="material-input-wrapper">
                                    <span class="input-prefix"><i class="fa fa-info-circle"></i></span>
                                    {!! Form::text('tax_number', null, ['placeholder' => __('contact.tax_no')]); !!}
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 opening_balance">
                            <div class="material-input-group">
                                {!! Form::label('opening_balance', __('lang_v1.opening_balance') . ':') !!}
                                <div class="material-input-wrapper">
                                    <span class="input-prefix">KES</span>
                                    {!! Form::text('opening_balance', 0, ['class' => 'input_number']); !!}
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 pay_term">
                            <div class="material-input-group">
                                {!! Form::label('pay_term_number', __('contact.pay_term') . ':') !!}
                                <div style="display: flex; gap: 8px;">
                                    <div class="material-input-wrapper" style="flex: 1;">
                                        {!! Form::number('pay_term_number', null, ['placeholder' => 'No.']); !!}
                                    </div>
                                    <div class="material-input-wrapper" style="flex: 1.5;">
                                        {!! Form::select('pay_term_type', ['months' => __('lang_v1.months'), 'days' => __('lang_v1.days')], '', ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'style' => 'width: 100%;']); !!}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12"><hr style="border-top: 1px solid var(--pos-border); margin: 24px 0;"></div>

                        <div class="col-md-6">
                            <div class="material-input-group">
                                {!! Form::label('address_line_1', __('lang_v1.address_line_1') . ':') !!}
                                <div class="material-input-wrapper" style="height: auto !important;">
                                    {!! Form::text('address_line_1', null, ['style' => 'padding: 12px 16px !important;', 'placeholder' => __('lang_v1.address_line_1')]); !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="material-input-group">
                                {!! Form::label('address_line_2', __('lang_v1.address_line_2') . ':') !!}
                                <div class="material-input-wrapper" style="height: auto !important;">
                                    {!! Form::text('address_line_2', null, ['style' => 'padding: 12px 16px !important;', 'placeholder' => __('lang_v1.address_line_2')]); !!}
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="material-input-group">
                                {!! Form::label('city', __('business.city') . ':') !!}
                                <div class="material-input-wrapper">
                                    <span class="input-prefix"><i class="fa fa-map-marker-alt"></i></span>
                                    {!! Form::text('city', null, ['placeholder' => __('business.city')]); !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="material-input-group">
                                {!! Form::label('state', __('business.state') . ':') !!}
                                <div class="material-input-wrapper">
                                    {!! Form::text('state', null, ['placeholder' => __('business.state')]); !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="material-input-group">
                                {!! Form::label('zip_code', __('business.zip_code') . ':') !!}
                                <div class="material-input-wrapper">
                                    {!! Form::text('zip_code', null, ['placeholder' => __('business.zip_code_placeholder')]); !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="material-input-group">
                                {!! Form::label('country', __('business.country') . ':') !!}
                                <div class="material-input-wrapper">
                                    {!! Form::text('country', null, ['placeholder' => __('business.country')]); !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('layouts.partials.module_form_part')
    </div>
    
    <div class="modal-footer" style="padding: 24px 32px !important; background: white !important; border-top: 1.5px solid var(--pos-border) !important;">
      <button type="submit" class="btn-material" style="background: var(--pos-primary) !important; color: white !important; font-weight: 700 !important; padding: 0 32px !important; min-width: 160px !important; height: 48px !important;">
        <i class="fa fa-save"></i> @lang( 'messages.save' )
      </button>
      <button type="button" class="btn-material" data-dismiss="modal" style="background: var(--pos-bg) !important; color: var(--pos-text) !important; border: 1.5px solid var(--pos-border) !important; padding: 0 24px !important; height: 48px !important;">
        @lang( 'messages.close' )
      </button>
    </div>

    {!! Form::close() !!}
  
  </div>
</div>