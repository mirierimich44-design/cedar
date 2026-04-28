@php
	$is_mobile = isMobile(); 
	$custom_labels = json_decode($business_details['custom_labels']);
	$theme_divider_class = 'theme_' . $business_details->theme_color . '_divider';
	/* Old style 5.40 using css/app.css */
    $theme_pos_class1 = 'theme_' . $business_details->theme_color . '_pos';
    /* New style usin css/tailwind/app.css */
    $theme_color = $business_details->theme_color;
    if ($theme_color == 'primary') /* primary doesnt exist */
        $theme_color = 'blue';

    $theme_pos_class = 'tw-bg-gradient-to-r tw-from-' . $business_details->theme_color . '-800'
    .' tw-to-'.$theme_color.'-500';
    
    /*Blue, Black, Purple, Green, Red, Yellow, Orange, Sky*/
    $border_pos_class = 'tw-border-' . $theme_color . '-700';

    /* Parameters for styles */
    /*Get the style for the frame totals left o right*/
    $poscustom_posi_totals = 'left'; //Default right
    if (!empty($pos_settings['poscustom_position_totals']))
    {
        $poscustom_posi_totals = $pos_settings['poscustom_position_totals'];
    }    

    /*Get the style buttons fixed bottom s0, buttons only left s1*/
    $poscustom_style_totals = 's0'; //Default
    if (!empty($pos_settings['poscustom_style_totals']))
    {
        $poscustom_style_totals = $pos_settings['poscustom_style_totals'];
    }    

    /* Get the size of the left part (pos-totals)*/
    $poscustom_width_totals = '40%';//Default
    if (!empty($pos_settings['poscustom_width_totals']))
    {
        $poscustom_width_totals = $pos_settings['poscustom_width_totals'];
    }

    /* pos_form_actions Style for bottom buttons PosCustom*/
    $poscustom_btnstyle = 's0';
    if (!empty($pos_settings['poscustom_btnstyle'])) //Default style for buttons s0 
    {
            $poscustom_btnstyle = $pos_settings['poscustom_btnstyle']; 
    }

    /* pos_sidebar Style for categories in the POS rtsyle s0 top, s1 middle*/
    $poscustom_style_cate = 's0'; // Default style for category s0
    if (!empty($pos_settings['poscustom_style_cate']))
    {
        if ($pos_settings['poscustom_style_cate'] != 's0')
            $poscustom_style_cate = $pos_settings['poscustom_style_cate'];
    }
@endphp

@extends('PosCustom::layouts.app')

@section('title', __('sale.pos_sale'))

@section('content')

<section class="{{-- content --}} no-print">
	<input type="hidden" id="amount_rounding_method" value="{{$pos_settings['amount_rounding_method'] ?? ''}}">
	@if(!empty($pos_settings['allow_overselling']))
		<input type="hidden" id="is_overselling_allowed">
	@endif
	@if(session('business.enable_rp') == 1)
        <input type="hidden" id="reward_point_enabled">
    @endif
    @php
		$is_discount_enabled = $pos_settings['disable_discount'] != 1 ? true : false;
		$is_rp_enabled = session('business.enable_rp') == 1 ? true : false;

		/*#JCN To use only the left size without the products*/
		if($poscustom_width_totals == 'full')
			$class = 'tw-gap-1 tw-pt-1';
            
            
            //For the position of the products ill use the position of the totals 
            if ($poscustom_posi_totals == 'right' )
            {
                $class = 'grid grid-rows-[1fr,auto] md:tw-grid-cols-[1fr,'.$poscustom_width_totals.'] lg:tw-grid-cols-[1fr,'.$poscustom_width_totals.']  tw-gap-1 ';
                $class = 'grid grid-rows-[auto,1fr] md:tw-grid-cols-['.$poscustom_width_totals.',1fr] lg:tw-grid-cols-['.$poscustom_width_totals.',1fr]  tw-gap-1 ';
}
		else
			$class = 'grid grid-rows-[auto,1fr] md:tw-grid-cols-['.$poscustom_width_totals.',1fr] lg:tw-grid-cols-['.$poscustom_width_totals.',1fr]  tw-gap-1 ';
			
	@endphp
	
	{!! Form::open([
		'url' => action([Modules\PosCustom\Http\Controllers\SellPosController::class, 'update'], [$transaction->id]), 
		'method' => 'post', 
		'id' => 'edit_pos_sell_form',
		'class' => $class 
	]) !!}
	{{ method_field('PUT') }}

	{!! Form::hidden('location_id', $transaction->location_id, [
		'id' => 'location_id', 
		'data-receipt_printer_type' => !empty($location_printer_type) 
		? $location_printer_type 
		: 'browser', 
		'data-default_payment_accounts' => $transaction->location->default_payment_accounts
		]); !!}
	<!-- sub_type -->
	{!! Form::hidden('sub_type', isset($sub_type) ? $sub_type : null) !!}
		<input type="hidden" id="item_addition_method"	
		value="{{ $business_details->item_addition_method }}">
        
        <!--Left Side for totals & rigt for products -->
        <pos-items-totals class = "tw-bg-white grid-cols-1 grid   @if ($poscustom_posi_totals == 'right' ) md:col-start-2  {{-- @else grid-rows-[auto,1fr]  --}} @endif md:row-start-2 md:block tw-overflow-y-auto tw-overflow-hidden tw-rounded-lg" >
            <div class="flex @if ($poscustom_style_totals == 's1') tw-h-dvh-totals-left @else tw-h-dvh-totals-fixed @endif 
                w-full flex-col tw-p-1 {{-- bg-slate-400 --}}">
                <!--#JCN implement header, main, footer pos_form contain header & main-->
                    @include('PosCustom::sale_pos.partials.pos_form_edit') 

                <footer-totals class="flex-none max-h-[20vh] min-h-0 w-full {{-- bg-green-400 --}} overflow-y-auto">
                    <!-- left side totals belove-->
                    {{-- <div class="{{$theme_divider_class}}" ></div> --}}  <!-- Divider--> 
                            @include('PosCustom::sale_pos.partials.pos_form_totals')
                            
                            <!--pos_form_actions_wleft show totals & buttons actions on left side -->
                            @if ($poscustom_style_totals == 's1')
                                @include("PosCustom::sale_pos.partials.pos_form_actions_left")
                            @endif

                    @include('sale_pos.partials.payment_modal')

                    @if (empty($pos_settings['disable_suspend']))
                        @include('sale_pos.partials.suspend_note_modal')
                    @endif

                    @if (empty($pos_settings['disable_recurring_invoice']))
                        @include('sale_pos.partials.recurring_invoice_modal')
                    @endif
                    
                    @if(!empty($only_payment))
                        <div class="overlay"></div>
                    @endif			
                </footer-totals>
            </div>
        </pos-items-totals>
        <pos-products class="@if($poscustom_width_totals == 'full') hidden @endif @if ($poscustom_posi_totals == 'right' ) md:col-start-1 {{-- grid-rows-[1fr,auto] --}} @endif md:row-start-2  {{--bg-white p-2 --}} "> <!--p-2 padding for the  div products -->    
            @if (empty($pos_settings['hide_product_suggestion']) && !isMobile())
                <!--Including diferents style in the right side-->
                    @if ($poscustom_style_cate == 's1') {{-- In the middle --}}
                        @include("PosCustom::sale_pos.partials.pos_sidebar_s1")
                    @else
                        @include("PosCustom::sale_pos.partials.pos_sidebar")
                    @endif
            @endif 
            {{-- <div style="padding: 10px;"></div> --}}                                  
        </pos-products>
            
        <!-- Its no necessary to have the footer here  -->
            {{-- <footer class="bg-red-200 col-span-full row-start-3 md:col-start-1 p-4 "> </footer> --}}
        
        <!--s0 show button bottom fixed -->
        @if ($poscustom_style_totals == 's0')
            @include("PosCustom::sale_pos.partials.pos_form_actions")
        @endif           

			{!! Form::close() !!} 
</section>

<!-- This will be printed -->
<section class="invoice print_section" id="receipt_section">
</section>
<div class="modal fade contact_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
	@include('contact.create', ['quick_add' => true])
</div>
    @if (empty($pos_settings['hide_product_suggestion']) && isMobile())
        @include('PosCustom::sale_pos.partials.mobile_product_suggestions')
@endif
<!-- /.content -->
<div class="modal fade register_details_modal" tabindex="-1" role="dialog" 
	aria-labelledby="gridSystemModalLabel">
</div>
<div class="modal fade close_register_modal" tabindex="-1" role="dialog" 
	aria-labelledby="gridSystemModalLabel">
</div>
<!-- quick product modal -->
<div class="modal fade quick_add_product_modal" tabindex="-1" role="dialog" aria-labelledby="modalTitle"></div>

@include('sale_pos.partials.configure_search_modal')

@include('sale_pos.partials.recent_transactions_modal')

@include('sale_pos.partials.weighing_scale_modal')

@stop

@section('javascript')
{{-- #JCN--}}
{{-- INI JAVA 
	<script src="{{ asset('js/pos.js?v=' . $asset_v) }}"></script>
	I move from public/js/pos.js to PosCustom/layouts/partials/pos_js_custom.blade.php	
	This because when we are using a module the link to Module/PosCustom/Resource/assets/js doesnt work
--}}
	{{-- @include('PosCustom::layouts.partials.js_custom.pos_js_custom') --}}
	{{-- <script src="{{ Module::asset('poscustom:js/pos_js_custom.js') }}"></script> --}}
    <script src="{{ Module::asset('poscustom:js/pos_js_custom.js?v=' . $asset_v) }}"></script>

    
{{-- #JCN--}}
	<script src="{{ asset('js/printer.js?v=' . $asset_v) }}"></script>
	<script src="{{ asset('js/product.js?v=' . $asset_v) }}"></script>
	<script src="{{ asset('js/opening_stock.js?v=' . $asset_v) }}"></script>
	@include('sale_pos.partials.keyboard_shortcuts')

	<!-- Call restaurant module if defined -->
    @if(in_array('tables' ,$enabled_modules) || in_array('modifiers' ,$enabled_modules) || in_array('service_staff' ,$enabled_modules))
    	<script src="{{ asset('js/restaurant.js?v=' . $asset_v) }}"></script>
    @endif

    <!-- include module js -->
    @if(!empty($pos_module_data))
	    @foreach($pos_module_data as $key => $value)
            @if(!empty($value['module_js_path']))
                @includeIf($value['module_js_path'], ['view_data' => $value['view_data']])
            @endif
	    @endforeach
	@endif
	
@endsection

@section('css')
	<style type="text/css">
		/*CSS to print receipts*/
		.print_section{
		    display: none;
		}
		@media print{
		    .print_section{
		        display: block !important;
		    }
		}
		@page {
		    size: 3.1in auto;/* width height */
		    height: auto !important;
		    margin-top: 0mm;
		    margin-bottom: 0mm;
		}
		.overlay {
			background: rgba(255,255,255,0) !important;
			cursor: not-allowed;
		}
	</style>
	<!-- include module css -->
    @if(!empty($pos_module_data))
        @foreach($pos_module_data as $key => $value)
            @if(!empty($value['module_css_path']))
                @includeIf($value['module_css_path'])
            @endif
        @endforeach
    @endif
@endsection