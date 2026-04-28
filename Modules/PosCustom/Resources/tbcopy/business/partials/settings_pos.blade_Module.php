<div class="pos-tab-content">
    <h4>@lang('business.add_keyboard_shortcuts'):</h4>
    <p class="help-block">@lang('lang_v1.shortcut_help'); @lang('lang_v1.example'): <b>ctrl+shift+b</b>, <b>ctrl+h</b></p>
    <p class="help-block">
        <b>@lang('lang_v1.available_key_names_are'):</b>
        <br> shift, ctrl, alt, backspace, tab, enter, return, capslock, esc, escape, space, pageup, pagedown, end, home, <br>left, up, right, down, ins, del, and plus
    </p>
    <div class="row">
        <div class="col-sm-6">
            <table class="table table-striped">
                <tr>
                    <th>@lang('business.operations')</th>
                    <th>@lang('business.keyboard_shortcut')</th> 
                </tr>
                <tr>
                    <td>{!! __('sale.express_finalize') !!}:</td>
                    <td>
                        {!! Form::text('shortcuts[pos][express_checkout]', 
                        !empty($shortcuts["pos"]["express_checkout"]) ? $shortcuts["pos"]["express_checkout"] : null, ['class' => 'form-control']); !!}
                    </td>
                </tr>
                <tr>
                    <td>@lang('sale.finalize'):</td>
                    <td>
                        {!! Form::text('shortcuts[pos][pay_n_ckeckout]', !empty($shortcuts["pos"]["pay_n_ckeckout"]) ? $shortcuts["pos"]["pay_n_ckeckout"] : null, ['class' => 'form-control']); !!}
                    </td>
                </tr>
                <tr>
                    <td>@lang('sale.draft'):</td>
                    <td>
                        {!! Form::text('shortcuts[pos][draft]', !empty($shortcuts["pos"]["draft"]) ? $shortcuts["pos"]["draft"] : null, ['class' => 'form-control']); !!}
                    </td>
                </tr>
                <tr>
                    <td>@lang('messages.cancel'):</td>
                    <td>
                        {!! Form::text('shortcuts[pos][cancel]', !empty($shortcuts["pos"]["cancel"]) ? $shortcuts["pos"]["cancel"] : null, ['class' => 'form-control']); !!}
                    </td>
                </tr>
                <tr>
                    <td>@lang('lang_v1.recent_product_quantity'):</td>
                    <td>
                        {!! Form::text('shortcuts[pos][recent_product_quantity]', !empty($shortcuts["pos"]["recent_product_quantity"]) ? $shortcuts["pos"]["recent_product_quantity"] : null, ['class' => 'form-control']); !!}
                    </td>
                </tr>
                <tr>
                    <td>@lang('lang_v1.weighing_scale'):</td>
                    <td>
                        {!! Form::text('shortcuts[pos][weighing_scale]', !empty($shortcuts["pos"]["weighing_scale"]) ? $shortcuts["pos"]["weighing_scale"] : null, ['class' => 'form-control']); !!}
                    </td>
                </tr>
            </table>
        </div>
        <div class="col-sm-6">
            <table class="table table-striped">
                <tr>
                    <th>@lang('business.operations')</th>
                    <th>@lang('business.keyboard_shortcut')</th>
                </tr>
                <tr>
                    <td>@lang('sale.edit_discount'):</td>
                    <td>
                        {!! Form::text('shortcuts[pos][edit_discount]', !empty($shortcuts["pos"]["edit_discount"]) ? $shortcuts["pos"]["edit_discount"] : null, ['class' => 'form-control']); !!}
                    </td>
                </tr>
                <tr>
                    <td>@lang('sale.edit_order_tax'):</td>
                    <td>
                        {!! Form::text('shortcuts[pos][edit_order_tax]', !empty($shortcuts["pos"]["edit_order_tax"]) ? $shortcuts["pos"]["edit_order_tax"] : null, ['class' => 'form-control']); !!}
                    </td>
                </tr>
                <tr>
                    <td>@lang('sale.add_payment_row'):</td>
                    <td>
                        {!! Form::text('shortcuts[pos][add_payment_row]', !empty($shortcuts["pos"]["add_payment_row"]) ? $shortcuts["pos"]["add_payment_row"] : null, ['class' => 'form-control']); !!}
                    </td>
                </tr>
                <tr>
                    <td>@lang('sale.finalize_payment'):</td>
                    <td>
                        {!! Form::text('shortcuts[pos][finalize_payment]', !empty($shortcuts["pos"]["finalize_payment"]) ? $shortcuts["pos"]["finalize_payment"] : null, ['class' => 'form-control']); !!}
                    </td>
                </tr>
                <tr>
                    <td>@lang('lang_v1.add_new_product'):</td>
                    <td>
                        {!! Form::text('shortcuts[pos][add_new_product]', !empty($shortcuts["pos"]["add_new_product"]) ? $shortcuts["pos"]["add_new_product"] : null, ['class' => 'form-control']); !!}
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <h4>@lang('lang_v1.pos_settings'):</h4>
        </div>

        <div class="col-sm-4">
            <div class="form-group">
                <div class="checkbox">
                <br>
                  <label>
                    {!! Form::checkbox('pos_settings[disable_pay_checkout]', 1,  
                        $pos_settings['disable_pay_checkout'] , 
                    [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.disable_pay_checkout' ) }}
                  </label>
                </div>
            </div>
        </div>

        <div class="col-sm-4">
            <div class="form-group">
                <div class="checkbox">
                <br>
                  <label>
                    {!! Form::checkbox('pos_settings[disable_draft]', 1,  
                        $pos_settings['disable_draft'] , 
                    [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.disable_draft' ) }}
                  </label>
                </div>
            </div>
        </div>

        <div class="col-sm-4">
            <div class="form-group">
                <div class="checkbox">
                <br>
                  <label>
                    {!! Form::checkbox('pos_settings[disable_express_checkout]', 1,  
                        $pos_settings['disable_express_checkout'] , 
                    [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.disable_express_checkout' ) }}
                  </label>
                </div>
            </div>
        </div>

        <div class="col-sm-4">
            <div class="form-group">
                <div class="checkbox">
                <br>
                  <label>
                    {!! Form::checkbox('pos_settings[hide_product_suggestion]', 1,  $pos_settings['hide_product_suggestion'] , 
                    [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.hide_product_suggestion' ) }}
                  </label>
                </div>
            </div>
        </div>

        <div class="col-sm-4">
            <div class="form-group">
                <div class="checkbox">
                <br>
                  <label>
                    {!! Form::checkbox('pos_settings[hide_recent_trans]', 1,  $pos_settings['hide_recent_trans'] , 
                    [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.hide_recent_trans' ) }}
                  </label>
                </div>
            </div>
        </div>

        <div class="col-sm-4">
            <div class="form-group">
                <div class="checkbox">
                <br>
                  <label>
                    {!! Form::checkbox('pos_settings[disable_discount]', 1,  $pos_settings['disable_discount'] , 
                    [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.disable_discount' ) }}
                  </label>
                </div>
            </div>
        </div>

        <div class="col-sm-4">
            <div class="form-group">
                <div class="checkbox">
                <br>
                  <label>
                    {!! Form::checkbox('pos_settings[disable_order_tax]', 1,  $pos_settings['disable_order_tax'] , 
                    [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.disable_order_tax' ) }}
                  </label>
                </div>
            </div>
        </div>

        <div class="col-sm-4">
            <div class="form-group">
                <div class="checkbox">
                <br>
                  <label>
                    {!! Form::checkbox('pos_settings[is_pos_subtotal_editable]', 1,  
                    empty($pos_settings['is_pos_subtotal_editable']) ? 0 : 1 , 
                    [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.subtotal_editable' ) }}
                  </label>
                  @show_tooltip(__('lang_v1.subtotal_editable_help_text'))
                </div>
            </div>
        </div>

        <div class="col-sm-4">
            <div class="form-group">
                <div class="checkbox">
                <br>
                  <label>
                    {!! Form::checkbox('pos_settings[disable_suspend]', 1,  
                    empty($pos_settings['disable_suspend']) ? 0 : 1 , 
                    [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.disable_suspend_sale' ) }}
                  </label>
                </div>
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="col-sm-6">
            <div class="form-group">
                <div class="checkbox">
                <br>
                  <label>
                    {!! Form::checkbox('pos_settings[enable_transaction_date]', 1,  
                    empty($pos_settings['enable_transaction_date']) ? 0 : 1 , 
                    [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.enable_pos_transaction_date' ) }}
                  </label>
                </div>
            </div>
        </div>

        <div class="col-sm-6">
            <div class="form-group">
                <div class="checkbox">
                <br>
                  <label>
                    {!! Form::checkbox('pos_settings[inline_service_staff]', 1,  
                    !empty($pos_settings['inline_service_staff']) ? true : false , 
                    [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.enable_service_staff_in_product_line' ) }}
                  </label>
                  @show_tooltip(__('lang_v1.inline_service_staff_tooltip'))
                </div>
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="col-sm-4">
            <div class="form-group">
                <div class="checkbox">
                <br>
                  <label>
                    {!! Form::checkbox('pos_settings[is_service_staff_required]', 1,  
                    empty($pos_settings['is_service_staff_required']) ? 0 : 1 , 
                    [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.is_service_staff_required' ) }}
                  </label>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                <div class="checkbox">
                <br>
                  <label>
                    {!! Form::checkbox('pos_settings[disable_credit_sale_button]', 1,  
                    empty($pos_settings['disable_credit_sale_button']) ? 0 : 1 , 
                    [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.disable_credit_sale_button' ) }}
                  </label>
                  @show_tooltip(__('lang_v1.show_credit_sale_btn_help'))
                </div>
            </div>
        </div>

        <div class="col-sm-4">
            <div class="form-group">
                <div class="checkbox">
                <br>
                  <label>
                    {!! Form::checkbox('pos_settings[enable_weighing_scale]', 1,  
                    empty($pos_settings['enable_weighing_scale']) ? 0 : 1 , 
                    [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.enable_weighing_scale' ) }}
                  </label>
                </div>
            </div>
        </div>

        <div class="col-sm-4">
            <div class="form-group">
                <div class="checkbox">
                <br>
                  <label>
                    {!! Form::checkbox('pos_settings[show_invoice_scheme]', 1,  
                       empty($pos_settings['show_invoice_scheme']) ? 0 : 1 , 
                    [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.show_invoice_scheme' ) }}
                  </label>
                </div>
            </div>
        </div>

        <div class="col-sm-4">
            <div class="form-group">
                <div class="checkbox">
                <br>
                  <label>
                    {!! Form::checkbox('pos_settings[show_invoice_layout]', 1,  
                        !empty($pos_settings['show_invoice_layout']) , 
                    [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.show_invoice_layout' ) }}
                  </label>
                </div>
            </div>
        </div>

        <div class="col-sm-4">
            <div class="form-group">
                <div class="checkbox">
                <br>
                  <label>
                    {!! Form::checkbox('pos_settings[print_on_suspend]', 1,  
                        !empty($pos_settings['print_on_suspend']) , 
                    [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.print_on_suspend' ) }}
                  </label>
                </div>
            </div>
        </div>

        <div class="col-sm-4">
            <div class="form-group">
                <div class="checkbox">
                <br>
                  <label>
                    {!! Form::checkbox('pos_settings[show_pricing_on_product_sugesstion]', 1,  
                        !empty($pos_settings['show_pricing_on_product_sugesstion']) , 
                    [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.show_pricing_on_product_sugesstion' ) }}
                  </label>
                </div>
            </div>
        </div>
    </div>
    <hr>
    {{--#JCN 2025 Adding parameters for POSCustom & POS Display--}}
    <div class="row">
            <div class="col-sm-12">
                <h4>@lang('poscustom::lang.poscustom_settings'):</h4>
            </div>
            <!--Button category-->
            <div class="col-sm-4">
                <div class="form-group">
                    <div class="checkbox">
                    <br>
                        <label>
                            {!! Form::checkbox('pos_settings[poscustom_btncate_show]', 1,  
                            !empty($pos_settings['poscustom_btncate_show']) , 
                            [ 'class' => 'input-icheck']); !!} {{ __( 'poscustom::lang.poscustom_btncate_show' ) }}
                        </label>
                    </div>
                </div>
            </div>              
            <!--Quick access category-->            
            <div class="col-sm-4">
                <div class="form-group">
                    <div class="checkbox">
                    <br>
                        <label>
                            {!! Form::checkbox('pos_settings[poscustom_bar_show]', 1,  
                            !empty($pos_settings['poscustom_bar_show']) , 
                            [ 'class' => 'input-icheck']); !!} {{ __( 'poscustom::lang.poscustom_bar_show' ) }}
                        </label>
                    </div>
                </div>
            </div>
          
            <div class="col-sm-4">
                <div class="form-group">
                    <div class="checkbox">
                    <br>
                      <label>
                        {!! Form::checkbox('pos_settings[poscustom_show_units]', 1,  
                        !empty($pos_settings['poscustom_show_units']) , 
                        [ 'class' => 'input-icheck']); !!} {{ __( 'poscustom::lang.poscustom_show_units' ) }}
                      </label>
                    </div>
                </div>
            </div>

            <div class="col-sm-4">
                <div class="form-group">
                    <div class="checkbox">
                    <br>
                      <label>
                        {!! Form::checkbox('pos_settings[poscustom_show_imgproduct]', 1,  
                        !empty($pos_settings['poscustom_show_imgproduct']) , 
                        [ 'class' => 'input-icheck']); !!} {{ __( 'poscustom::lang.poscustom_show_imgproduct' ) }}
                      </label>
                    </div>
                </div>
            </div>

            {{-- Second Screen & Advertisement --}}            
            <div class="col-sm-4">
                <div class="form-group">
                    <div class="checkbox">
                    <br>
                      <label>
                        {!! Form::checkbox('pos_settings[poscustom_secondscreen_show]', 1,  
                        !empty($pos_settings['poscustom_secondscreen_show']) , 
                        [ 'class' => 'input-icheck']); !!} {{ __( 'poscustom::lang.poscustom_secondscreen_show' ) }}
                      </label>
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    <div class="checkbox">
                    <br>
                      <label>
                        {!! Form::checkbox('pos_settings[show_advertisement_s1]', 1,  
                        !empty($pos_settings['show_advertisement_s1']) , 
                        [ 'class' => 'input-icheck']); !!} {{ __( 'poscustom::lang.show_advertisement_s1' ) }}
                      </label>
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    <div class="checkbox">
                    <br>
                      <label>
                        {!! Form::checkbox('pos_settings[show_advertisement_s2]', 1,  
                        !empty($pos_settings['show_advertisement_s2']) , 
                        [ 'class' => 'input-icheck']); !!} {{ __( 'poscustom::lang.show_advertisement_s2' ) }}
                      </label>
                    </div>
                </div>
            </div>
            <!--New Style left & right only -->
            <div class="col-sm-4">
                <div class="form-group">
                    <div class="checkbox">
                    <br>
                      <label>
                        {!! Form::checkbox('pos_settings[show_advertisement_s2]', 1,  
                        !empty($pos_settings['show_advertisement_s2']) , 
                        [ 'class' => 'input-icheck']); !!} {{ __( 'poscustom::lang.show_advertisement_s2' ) }}
                      </label>
                    </div>
                </div>
            </div>
            {{--This section to have parameters string/number--}}
            <div class="col-sm-12">
                <div class="col-sm-4">
                    <div class="form-group">
                        <span  class="tw-inline-flex tw-items-center tw-justify-center tw-border-2 tw-rounded-tr-xl tw-rounded-bl-xl tw-w-8 tw-h-6 tw-text-xs tw-font-semibold text-white tw-bg-green-600">
                            999
                        </span>
                        {!! Form::label('poscustom_width_badge_show', __('poscustom::lang.poscustom_width_badge_show') . ':') !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-circle"></i>
                            </span>
                            {!! Form::text('pos_settings[poscustom_width_badge_show]', isset($pos_settings['poscustom_width_badge_show']) ? $pos_settings['poscustom_width_badge_show'] : null, 
                            ['class' => 'form-control' ]); !!}
                            <span class="input-group-addon">%</span>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    @php
                    $products_size = ['105' => __('105 Beta'), 
                    /* '110' => __('110'),
                        '120' => __('120'),
                        '140' => __('140')  */]; 
                    @endphp
                    <div class="form-group">
                        @show_tooltip(__('poscustom::lang.poscustom_width_product_help'))
                        {!! Form::label('poscustom_width_product', __('poscustom::lang.poscustom_width_product') . ':*') !!}
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-clone"></i></span>
                            {!! Form::select('pos_settings[poscustom_width_product]', $products_size, isset($pos_settings['poscustom_width_product']) ? $pos_settings['poscustom_width_product'] : null , 
                            ['class' => 'form-control select2', 'required', 'style' => 'width: 100%;']); !!}
                            <span class="input-group-addon">px</span>
                        </div>
                    </div>
                    

                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        @show_tooltip(__('poscustom::lang.poscustom_default_category_help'))
                        {!! Form::label('poscustom_default_category', __('poscustom::lang.poscustom_default_category') . ':') !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-list"></i>
                            </span>
                            {!! Form::select('pos_settings[poscustom_default_category]', $categories_dropdown, isset($pos_settings['poscustom_default_category']) ? $pos_settings['poscustom_default_category'] : null, 
                            ['class' => 'form-control select2', 'style' => 'width: 100%;' ]); !!}
                        </div>
                    </div>
                </div>
            </div>       
            {{--Style Settings--}}
            <div class="col-sm-12">
                <h4>@lang('poscustom::lang.poscustom_styles'):</h4>
            </div>            
            <div class="col-sm-12">
                @php
                    $totals_lr_side = ['left' => __('poscustom::lang.plrstyle0'), 
                                    'right' => __('poscustom::lang.plrstyle1')];  
                    $total_styles = ['s0' => __('poscustom::lang.totalstyle0'), 
                                    's1' => __('poscustom::lang.totalstyle1')];                 
                    $cate_styles = ['s0' => __('poscustom::lang.catestyle0'), 
                                    's1' => __('poscustom::lang.catestyle1'), 
                                    /* 's3' => __('poscustom::lang.style3') */];                
                    $btn_styles = ['s0' => __('poscustom::lang.bstyle0'), 
                                    's1' => __('poscustom::lang.bstyle1'), 
                                    's1t' => __('poscustom::lang.bstyle1t'),
                                    's2' => __('poscustom::lang.bstyle2'),
                                    's2t' => __('poscustom::lang.bstyle2t')
                                    /* 's3' => __('poscustom::lang.style3') */];
                    $leftsize = ['40%' => __('Left size 40%'), 
                                    '45%' => __('Left size 45%'), 
                                    '50%' => __('Left size 50%'),
                                    '55%' => __('Left size 55%'),
                                    '60%' => __('Left size 60%'),
/*                                     '65%' => __('Left size 65%'), 
                                    '70%' => __('Left size 70%'), 
                                    '75%' => __('Left size 75%'),
                                    '80%' => __('Left size 80%'), */
                                    'full' => __('Left "Full Size"')];                                  
                @endphp   
            <!--New Style totals position only -->
                <div class="col-sm-4">
                    <div class="form-group">
                        @show_tooltip(__('poscustom::lang.poscustom_ppositionlr_ttip'))
                        {!! Form::label('poscustom_position_totals', __('poscustom::lang.poscustom_ppositionlr_lang') . ':*') !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="{{-- fa fa-calendar --}}">📱</i>
                            </span>
                            {!! Form::select('pos_settings[poscustom_position_totals]', $totals_lr_side, isset($pos_settings['poscustom_position_totals']) ? $pos_settings['poscustom_position_totals'] : null , 
                            ['class' => 'form-control select2', 'required', 'style' => 'width: 100%;']); !!}
                        </div>
                    </div>
                </div>                 
            <!--New Style for totals side -->
                <div class="col-sm-4">
                    <div class="form-group">
                        @show_tooltip(__('poscustom::lang.poscustom_totalstyle_ttip'))
                        {!! Form::label('poscustom_style_totals', __('poscustom::lang.poscustom_totalstyle_lang') . ':*') !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="{{-- fa fa-calendar --}}">🛒</i>
                            </span>
                            {!! Form::select('pos_settings[poscustom_style_totals]', $total_styles, isset($pos_settings['poscustom_style_totals']) ? $pos_settings['poscustom_style_totals'] : null , 
                            ['class' => 'form-control select2', 'required', 'style' => 'width: 100%;']); !!}  
                        </div>
                    </div>
                </div>            
                <!--Left Size POS--> 
                <div class="col-sm-4">
                    <div class="form-group">
                        @show_tooltip(__('poscustom::lang.poscustom_width_totals_ttip'))
                        {!! Form::label('poscustom_width_totals', __('poscustom::lang.poscustom_width_totals') . ':*') !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i >🗔</i>
                            </span>
                            {!! Form::select('pos_settings[poscustom_width_totals]', $leftsize, isset($pos_settings['poscustom_width_totals']) ? $pos_settings['poscustom_width_totals'] : null , 
                            ['class' => 'form-control select2', 'required', 'style' => 'width: 100%;']); !!}
                            <span class="input-group-addon">%</span>
                            
                        </div>
                    </div>
                </div>                 
                <!--Styles rigt side POS-->                
                <div class="col-sm-4">
                    <div class="form-group">
                        @show_tooltip(__('poscustom::lang.poscustom_catestyle_ttip'))
                        {!! Form::label('poscustom_style_cate', __('poscustom::lang.poscustom_catestyle_lang') . ':*') !!}
                        <div class="input-group">
                            <span class="input-group-addon"> <i class="tw-text-red-500 " aria-hidden="true">📋</i></span>                            
                            {!! Form::select('pos_settings[poscustom_style_cate]', $cate_styles, isset($pos_settings['poscustom_style_cate']) ? $pos_settings['poscustom_style_cate'] : null , 
                            ['class' => 'form-control select2', 'required', 'style' => 'width: 100%;']); !!}
                        </div>
                    </div>
                </div> 
            
                <!--Styles buttons POS-->
                <div class="col-sm-4">
                    <div class="form-group">
                        @show_tooltip(__('poscustom::lang.poscustom_btnstyle_ttip'))
                        {!! Form::label('poscustom_btnstyle', __('poscustom::lang.poscustom_btnstyle_lang') . ':*') !!}
                        <div class="input-group">
                            <span class="input-group-addon"> <i class="tw-text-red-500 " aria-hidden="true">🌈</i></span>                            
                            {!! Form::select('pos_settings[poscustom_btnstyle]', $btn_styles, isset($pos_settings['poscustom_btnstyle']) ? $pos_settings['poscustom_btnstyle'] : null , 
                            ['class' => 'form-control select2', 'required', 'style' => 'width: 100%;']); !!}
                        </div> 
                    </div>
                </div> 
                 
            </div>               
            {{--END parameters--}}
    </div>    
    <hr>
    @include('business.partials.settings_weighing_scale')
</div>