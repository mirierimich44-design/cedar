
<div class="modal-dialog modal-lg" role="document">
  <div class="modal-content" style="border-radius:14px; overflow:hidden; box-shadow:0 20px 60px rgba(0,0,0,0.25); border:none;">
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

    {{-- Scoped styles for this modal's inputs --}}
    <style>
      #contact_add_form input[type="text"],
      #contact_add_form input[type="email"],
      #contact_add_form input[type="number"],
      #contact_add_form textarea,
      #quick_add_contact input[type="text"],
      #quick_add_contact input[type="email"],
      #quick_add_contact input[type="number"],
      #quick_add_contact textarea {
        width: 100%;
        height: 32px;
        border-radius: 6px;
        border: 1.5px solid #e2e8f0;
        font-size: 13px;
        padding: 5px 10px;
        box-sizing: border-box;
        background: #fff;
        color: #1e293b;
        transition: border-color 0.15s;
      }
      #contact_add_form input[type="text"]:focus,
      #contact_add_form input[type="email"]:focus,
      #contact_add_form input[type="number"]:focus,
      #quick_add_contact input[type="text"]:focus,
      #quick_add_contact input[type="email"]:focus,
      #quick_add_contact input[type="number"]:focus {
        border-color: var(--theme-main,#4f46e5);
        outline: none;
        box-shadow: 0 0 0 3px rgba(var(--theme-main,79,70,229),0.12);
      }
      /* shift input text right when there's a prefix icon inside wrapper */
      .contact-form-field .has-prefix input,
      .contact-form-field .has-prefix select {
        padding-left: 32px !important;
      }
      /* select2 container height fix */
      #contact_add_form .select2-container .select2-selection--single,
      #quick_add_contact .select2-container .select2-selection--single {
        height: 32px !important;
        border-radius: 6px !important;
        border: 1.5px solid #e2e8f0 !important;
        display: flex;
        align-items: center;
      }
      #contact_add_form .select2-container .select2-selection--single .select2-selection__rendered,
      #quick_add_contact .select2-container .select2-selection--single .select2-selection__rendered {
        line-height: 30px !important;
        padding-left: 10px !important;
        font-size: 13px;
        color: #1e293b;
      }
      #contact_add_form .select2-container .select2-selection--single .select2-selection__arrow,
      #quick_add_contact .select2-container .select2-selection--single .select2-selection__arrow {
        height: 30px !important;
      }
    </style>

    {{-- Header --}}
    <div style="background: linear-gradient(135deg, var(--theme-dark,#3730a3) 0%, var(--theme-main,#4f46e5) 100%); padding: 12px 16px; display:flex; align-items:center; justify-content:space-between;">
      <h4 style="margin:0; color:white; font-weight:600; font-size:14px; display:flex; align-items:center; gap:8px;">
        <i class="fas fa-user-plus" style="font-size:18px;"></i>
        @lang('contact.add_contact')
      </h4>
      <button type="button" class="close" data-dismiss="modal" style="color:white; opacity:1; font-size:24px; text-shadow:none; margin:0; padding:0; line-height:1;">&times;</button>
    </div>

    <div class="modal-body" style="padding:12px 14px 8px; background:#f8fafc;">

      {{-- Section 1: Contact Type --}}
      <div style="background:white; border-radius:8px; padding:10px 14px; margin-bottom:8px; border:1px solid #e2e8f0;">
        <div class="row" style="align-items:flex-end;">

          {{-- Contact Type dropdown --}}
          <div class="col-md-4 contact_type_div">
            <label style="font-size:11px; font-weight:600; color:#64748b; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:4px; display:block;">
              @lang('contact.contact_type') <span style="color:#e53e3e;">*</span>
            </label>
            <div style="position:relative; display:flex; align-items:center;">
              <span style="position:absolute; left:10px; z-index:1; color:#94a3b8;"><i class="fa fa-user"></i></span>
              {!! Form::select('type', $types, $type , ['class' => 'form-control select2', 'id' => 'contact_type', 'required', 'style' => 'width:100%; padding-left:32px; border-radius:8px; border:1.5px solid #e2e8f0; height:32px; font-size:13px;']); !!}
            </div>
          </div>

          {{-- Individual / Business toggle --}}
          <div class="col-md-4">
            <label style="font-size:12px; font-weight:600; color:#64748b; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:8px; display:block;">
              Contact Subtype
            </label>
            <div style="display:inline-flex; background:#f1f5f9; border-radius:8px; padding:3px; gap:3px;">
              <label style="margin:0; cursor:pointer;">
                <input type="radio" name="contact_type_radio" id="inlineRadio1" value="individual" checked style="display:none;">
                <span id="lbl_individual" style="display:block; padding:4px 12px; border-radius:5px; font-size:12px; font-weight:600; background:var(--theme-main,#4f46e5); color:white; transition:all 0.2s; user-select:none;">
                  <i class="fa fa-user" style="margin-right:5px;"></i>@lang('lang_v1.individual')
                </span>
              </label>
              <label style="margin:0; cursor:pointer;">
                <input type="radio" name="contact_type_radio" id="inlineRadio2" value="business" style="display:none;">
                <span id="lbl_business" style="display:block; padding:4px 12px; border-radius:5px; font-size:12px; font-weight:600; background:transparent; color:#64748b; transition:all 0.2s; user-select:none;">
                  <i class="fa fa-building" style="margin-right:5px;"></i>@lang('business.business')
                </span>
              </label>
            </div>
          </div>

          {{-- Contact ID --}}
          <div class="col-md-4">
            <label style="font-size:11px; font-weight:600; color:#64748b; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:4px; display:block;">
              @lang('lang_v1.contact_id')
            </label>
            <div style="position:relative; display:flex; align-items:center;">
              <span style="position:absolute; left:10px; z-index:1; color:#94a3b8;"><i class="fa fa-id-badge"></i></span>
              {!! Form::text('contact_id', null, ['placeholder' => __('lang_v1.contact_id'), 'style' => 'width:100%; padding:9px 12px 9px 32px; border-radius:8px; border:1.5px solid #e2e8f0; height:32px; font-size:13px; outline:none;']); !!}
            </div>
            <small style="color:#94a3b8; font-size:11px;">@lang('lang_v1.leave_empty_to_autogenerate')</small>
          </div>

        </div>
      </div>

      {{-- Section 2: Name & Contact Details --}}
      <div style="background:white; border-radius:8px; padding:10px 14px; margin-bottom:8px; border:1px solid #e2e8f0;">
        <p style="font-size:11px; font-weight:700; color:#94a3b8; text-transform:uppercase; letter-spacing:0.8px; margin:0 0 8px 0;">
          <i class="fa fa-address-card" style="margin-right:6px; color:var(--theme-main,#4f46e5);"></i>Personal Information
        </p>
        <div class="row">

          {{-- Business name (shown only for Business type) --}}
          <div class="col-md-6 business" style="display: none;">
            @include('contact.partials._field', ['icon' => 'fa-briefcase', 'label' => __('business.business_name').':*', 'field' => Form::text('supplier_business_name', null, ['placeholder' => __('business.business_name')])])
          </div>

          <div class="col-md-2 individual">
            @include('contact.partials._field', ['label' => __('business.prefix').':','field' => Form::text('prefix', null, ['placeholder' => __('business.prefix_placeholder')])])
          </div>
          <div class="col-md-4 individual">
            @include('contact.partials._field', ['icon' => 'fa-user', 'label' => __('business.first_name').':*', 'field' => Form::text('first_name', null, ['required', 'placeholder' => __('business.first_name')])])
          </div>
          <div class="col-md-3 individual">
            @include('contact.partials._field', ['label' => __('lang_v1.middle_name').':', 'field' => Form::text('middle_name', null, ['placeholder' => __('lang_v1.middle_name')])])
          </div>
          <div class="col-md-3 individual">
            @include('contact.partials._field', ['label' => __('business.last_name').':', 'field' => Form::text('last_name', null, ['placeholder' => __('business.last_name')])])
          </div>
        </div>

        <div style="border-top:1px solid #f1f5f9; margin:8px 0;"></div>
        <p style="font-size:11px; font-weight:700; color:#94a3b8; text-transform:uppercase; letter-spacing:0.8px; margin:0 0 8px 0;">
          <i class="fa fa-phone" style="margin-right:6px; color:#0369a1;"></i>Contact Details
        </p>

        <div class="row">
          <div class="col-md-4">
            @include('contact.partials._field', ['icon' => 'fa-mobile-alt', 'label' => __('contact.mobile').':*', 'field' => Form::text('mobile', null, ['required', 'placeholder' => __('contact.mobile')])])
          </div>
          <div class="col-md-4">
            @include('contact.partials._field', ['icon' => 'fa-envelope', 'label' => __('business.email').':', 'field' => Form::email('email', null, ['placeholder' => __('business.email')])])
          </div>
          <div class="col-md-2 customer_fields">
            @include('contact.partials._field', ['icon' => 'fa-users', 'label' => __('lang_v1.customer_group').':', 'field' => Form::select('customer_group_id', $customer_groups, '', ['class' => 'form-control select2', 'style' => 'width:100%;'])])
          </div>
          @if(config('constants.enable_contact_assign') && $type !== 'lead')
          <div class="col-md-2">
            @php $usersArr = ($users ?? collect())->toArray(); $firstUserId = array_key_first($usersArr); $firstUserName = $usersArr[$firstUserId] ?? __('lang_v1.assigned_to'); @endphp
            @include('contact.partials._field', ['icon' => 'fa-user-tag', 'label' => __('lang_v1.assigned_to').':', 'field' => Form::select('assigned_to_users[]', $users ?? [], $firstUserId, ['class' => 'form-control select2', 'id' => 'assigned_to_users', 'data-placeholder' => $firstUserName, 'style' => 'width:100%;'])])
          </div>
          @endif
        </div>
      </div>

      {{-- More Info Section --}}
      <div style="margin-bottom:6px;">
        <button type="button" class="more_btn" data-target="#more_div"
          style="width:100%; background:#f1f5f9; border:1.5px dashed #cbd5e1; border-radius:8px; padding:8px 14px; color:#475569; font-weight:600; font-size:12px; display:flex; align-items:center; justify-content:center; gap:8px; cursor:pointer;">
          <i class="fa fa-chevron-down more_btn_icon" style="font-size:11px; transition:transform 0.2s;"></i>
          @lang('lang_v1.more_info')
        </button>

        <div id="more_div" class="hide" style="margin-top:10px;">
          <div style="background:white; border-radius:8px; padding:10px 14px; border:1px solid #e2e8f0;">
            <div class="row">
              <div class="col-md-4">
                @include('contact.partials._field', ['icon' => 'fa-info-circle', 'label' => __('contact.tax_no').':', 'field' => Form::text('tax_number', null, ['placeholder' => __('contact.tax_no')])])
              </div>
              <div class="col-md-4 opening_balance">
                @include('contact.partials._field', ['prefix_text' => 'KES', 'label' => __('lang_v1.opening_balance').':', 'field' => Form::text('opening_balance', 0, ['class' => 'input_number'])])
              </div>
              <div class="col-md-4 pay_term">
                <label style="font-size:12px; font-weight:600; color:#64748b; margin-bottom:6px; display:block;">@lang('contact.pay_term'):</label>
                <div style="display:flex; gap:8px;">
                  <div style="flex:1; position:relative;">
                    {!! Form::number('pay_term_number', null, ['placeholder' => 'No.', 'style' => 'width:100%; padding:9px 12px; border-radius:8px; border:1.5px solid #e2e8f0; height:32px; font-size:13px;']) !!}
                  </div>
                  <div style="flex:1.5;">
                    {!! Form::select('pay_term_type', ['months' => __('lang_v1.months'), 'days' => __('lang_v1.days')], '', ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'style' => 'width:100%;']) !!}
                  </div>
                </div>
              </div>

              <div class="col-md-12"><hr style="border-top:1px solid #f1f5f9; margin:16px 0;"></div>

              <div class="col-md-6">
                @include('contact.partials._field', ['label' => __('lang_v1.address_line_1').':', 'field' => Form::text('address_line_1', null, ['placeholder' => __('lang_v1.address_line_1')])])
              </div>
              <div class="col-md-6">
                @include('contact.partials._field', ['label' => __('lang_v1.address_line_2').':', 'field' => Form::text('address_line_2', null, ['placeholder' => __('lang_v1.address_line_2')])])
              </div>
              <div class="col-md-3">
                @include('contact.partials._field', ['icon' => 'fa-map-marker-alt', 'label' => __('business.city').':', 'field' => Form::text('city', null, ['placeholder' => __('business.city')])])
              </div>
              <div class="col-md-3">
                @include('contact.partials._field', ['label' => __('business.state').':', 'field' => Form::text('state', null, ['placeholder' => __('business.state')])])
              </div>
              <div class="col-md-3">
                @include('contact.partials._field', ['label' => __('business.zip_code').':', 'field' => Form::text('zip_code', null, ['placeholder' => __('business.zip_code_placeholder')])])
              </div>
              <div class="col-md-3">
                @include('contact.partials._field', ['label' => __('business.country').':', 'field' => Form::text('country', null, ['placeholder' => __('business.country')])])
              </div>
            </div>
          </div>
        </div>
      </div>

      @include('layouts.partials.module_form_part')
    </div>

    {{-- Footer --}}
    <div style="padding:10px 16px; background:white; border-top:1px solid #e2e8f0; display:flex; align-items:center; justify-content:flex-end; gap:8px;">
      <button type="button" data-dismiss="modal"
        style="padding:6px 16px; border-radius:6px; border:1.5px solid #e2e8f0; background:white; color:#475569; font-weight:600; font-size:13px; cursor:pointer;">
        @lang('messages.close')
      </button>
      <button type="submit"
        style="padding:6px 20px; border-radius:6px; border:none; background:linear-gradient(135deg,var(--theme-dark,#3730a3),var(--theme-main,#4f46e5)); color:white; font-weight:700; font-size:13px; cursor:pointer; display:flex; align-items:center; gap:6px;">
        <i class="fa fa-save"></i> @lang('messages.save')
      </button>
    </div>

    {!! Form::close() !!}

  </div>
</div>

{{-- Radio pill + More Info toggle JS --}}
<script>
(function() {
    // Chevron rotation for More Info button
    $(document).on('click', '.more_btn', function() {
        var icon = $(this).find('.more_btn_icon');
        var expanded = $(this).data('expanded');
        if (expanded) {
            icon.css('transform', 'rotate(0deg)');
            $(this).data('expanded', false);
        } else {
            icon.css('transform', 'rotate(180deg)');
            $(this).data('expanded', true);
        }
    });

    function updatePillStyle() {
        var themeColor = getComputedStyle(document.documentElement).getPropertyValue('--theme-main').trim() || '#4f46e5';
        var isIndividual = document.getElementById('inlineRadio1').checked;
        var lblInd = document.getElementById('lbl_individual');
        var lblBiz = document.getElementById('lbl_business');
        if (!lblInd || !lblBiz) return;
        if (isIndividual) {
            lblInd.style.background = themeColor;
            lblInd.style.color = 'white';
            lblBiz.style.background = 'transparent';
            lblBiz.style.color = '#64748b';
        } else {
            lblBiz.style.background = themeColor;
            lblBiz.style.color = 'white';
            lblInd.style.background = 'transparent';
            lblInd.style.color = '#64748b';
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        var r1 = document.getElementById('inlineRadio1');
        var r2 = document.getElementById('inlineRadio2');
        if (r1) r1.addEventListener('change', updatePillStyle);
        if (r2) r2.addEventListener('change', updatePillStyle);
        updatePillStyle();
    });

    // Also handle when modal is shown (for Ajax-loaded modals)
    $(document).on('shown.bs.modal', function() { updatePillStyle(); });
})();
</script>
