<style type="text/css">

.container-totals {

	display:flex;
	flex-direction: row;
	justify-content: space-between;
	align-items: center;

}

</style>
@php
$is_mobile = isMobile();

@endphp

<!-- Block Totals & Items-->
<div class="container-totals {{-- tw-rounded-tr-xl tw-rounded-tl-xl --}} {{ $theme_pos_class }}  !tw-flex-row !tw-items-center tw-justify-between tw-text-white">
	<div style="width: 50%" >
		<b class="text-nowrap md:tw-text-xl tw-font-bold">@lang('sale.item'):</b>&nbsp;
		<span class="total_quantity text-nowrap md:tw-text-xl tw-font-semibold">0</span>
	</div>
	<div style="width: 50%" class="tw-rounde-lg" >
		{{-- Mod mobile --}}
		<div style="background-color: #ff0000" class="{{-- tw-bg-red-400 --}} md:!tw-w-none !tw-flex md:!tw-hidden !tw-flex-row !tw-items-center !tw-gap-3">
			<div class="tw-pos-total tw-flex tw-items-center tw-gap-3">
				<div class="{{-- tw-text-black --}} tw-font-bold tw-text-sm tw-flex tw-items-center tw-flex-col tw-leading-1 ">
					<div>@lang('sale.total_payable'):</div>
				</div>
				<input type="hidden" name="final_total" id="final_total_input" value="0.00" >
				<span id="total_payable" class="{{-- tw-text-green-900 --}} tw-font-bold tw-text-sm number" >0.00</span>
			</div>
		</div>
		<div  class="tw-bg-[#001F3E] ">
			@if (!$is_mobile)
				<div class="md:tw-flex md:tw-items-center tw-rounded-md md:tw-gap-6 tw-hidden">
					<div 
						class="tw-text-white tw-font-bold text-nowrap md:tw-text-sm tw-flex tw-items-center {{-- tw-flex-col --}}">
						<div>&nbsp;@lang('sale.total_payable') :&nbsp; </div>
					</div>
					<input type="hidden" name="final_total" id="final_total_input" value="0.00" >
					<span id="total_payable" 
						 class="  tw-text-red-500  tw-font-bold text-nowrap md:tw-text-3xl number">0.00</span>
				</div>            
			@endif                 
		</div>
	</div>
</div>

<!-- Block plain Buttons use to generat the tailwind file-->
<div class="hidden outline-red-500 outline-blue-500 outline-orange-500 outline-yellow-500 outline-sky-500 outline-green-500"></div>
<div class="hidden border-red-500 border-blue-500 border-orange-500 border-yellow-500 border-sky-500 border-green-500"></div>


<div class="tw-flex tw-flex-wrap tw-gap-1  tw-pt-1 tw-pb-1  {{-- tw-p-1 tw-gap-4 --}} tw-justify-betwwen tw-justify-center  {{-- tw-hidden --}} ">

	<div class=" {{-- tw-flex tw-w-full --}}  border border-{{ $theme_color }}-500 rounded-lg  {{-- text-nowrap --}} tw-justify-betwwen tw-justify-center">
		@if(!Gate::check('disable_discount') || auth()->user()->can('superadmin') || auth()->user()->can('admin'))
		
			<b class=" md:tw-text-sm tw-font-semibold">
				@if($is_discount_enabled)
					@if($is_rp_enabled)
						@show_tooltip(__('tooltip.sale_discount'))@lang('sale.discount')-{{session('business.rp_name')}}
					@else
						@show_tooltip(__('tooltip.sale_discount'))@lang('sale.discount')
					@endif
				@endif

				@if($is_discount_enabled)
					(-):
					@if($edit_discount)
					<i class="fas fa-edit cursor-pointer" id="pos-edit-discount" title="@lang('sale.edit1_discount')" aria-hidden="true" data-toggle="modal" data-target="#posEditDiscountModal"></i>
					@endif
					<span class=" tw-text-red-500  md:tw-text-sm tw-font-semibold" id="total_discount">0</span>
				@endif
					<input type="hidden" name="discount_type" id="discount_type" value="@if(empty($edit)){{'percentage'}}@else{{$transaction->discount_type}}@endif" data-default="percentage">

					<input type="hidden" name="discount_amount" id="discount_amount" value="@if(empty($edit)) {{@num_format($business_details->default_sales_discount)}} @else {{@num_format($transaction->discount_amount)}} @endif" data-default="{{$business_details->default_sales_discount}}">

					<input type="hidden" name="rp_redeemed" id="rp_redeemed" value="@if(empty($edit)){{'0'}}@else{{$transaction->rp_redeemed}}@endif">

					<input type="hidden" name="rp_redeemed_amount" id="rp_redeemed_amount" value="@if(empty($edit)){{'0'}}@else {{$transaction->rp_redeemed_amount}} @endif">

					</span> &nbsp;
			</b> 
		@endif
	</div>
	<div class="{{-- tw-flex tw-w-full --}} border border-{{ $theme_color }}-500 rounded-lg {{-- text-nowrap --}} tw-justify-betwwen tw-justify-center @if($pos_settings['disable_order_tax'] != 0) hide @endif">
		<span class="  md:tw-text-sm tw-font-semibold">
			<b class=" md:tw-text-sm tw-font-semibold">@show_tooltip(__('tooltip.sale_tax'))@lang('sale.order_tax')(+): </b>
			<i class="fas fa-edit cursor-pointer" title="@lang('sale.edit_order_tax')" aria-hidden="true" data-toggle="modal" data-target="#posEditOrderTaxModal" id="pos-edit-tax" ></i> 
			<span class=" md:tw-text-sm tw-font-semibold" id="order_tax">
				@if(empty($edit))
					0
				@else
					{{$transaction->tax_amount}}
				@endif
			</span>

			<input type="hidden" name="tax_rate_id" 
				id="tax_rate_id" 
				value="@if(empty($edit)) {{$business_details->default_sales_tax}} @else {{$transaction->tax_id}} @endif" 
				data-default="{{$business_details->default_sales_tax}}">

			<input type="hidden" name="tax_calculation_amount" id="tax_calculation_amount" 
				value="@if(empty($edit)) {{@num_format($business_details->tax_calculation_amount)}} @else {{@num_format($transaction->tax?->amount)}} @endif" data-default="{{$business_details->tax_calculation_amount}}">
		&nbsp;</span>
	</div>
	<div class="{{-- tw-flex tw-w-full --}} border border-{{ $theme_color }}-500 rounded-lg {{-- text-nowrap --}} tw-justify-betwwen tw-justify-center">
		<span class=" md:tw-text-sm tw-font-semibold ">
			<b class=" md:tw-text-sm tw-font-semibold"> @show_tooltip(__('tooltip.shipping'))@lang('sale.shipping')(+):</b> 
			<i class="fas fa-edit cursor-pointer"  title="@lang('sale.shipping')" aria-hidden="true" data-toggle="modal" data-target="#posShippingModal"></i>
			<span id="shipping_charges_amount">0</span>
			<input type="hidden" name="shipping_details" id="shipping_details" value="@if(empty($edit)){{''}}@else{{$transaction->shipping_details}}@endif" data-default="">

			<input type="hidden" name="shipping_address" id="shipping_address" value="@if(empty($edit)){{''}}@else{{$transaction->shipping_address}}@endif">

			<input type="hidden" name="shipping_status" id="shipping_status" value="@if(empty($edit)){{''}}@else{{$transaction->shipping_status}}@endif">

			<input type="hidden" name="delivered_to" id="delivered_to" value="@if(empty($edit)){{''}}@else{{$transaction->delivered_to}}@endif">

			<input type="hidden" name="delivery_person" id="delivery_person" value="@if(empty($edit)){{''}}@else{{$transaction->delivery_person}}@endif">

			<input type="hidden" name="shipping_charges" id="shipping_charges" value="@if(empty($edit)){{@num_format(0.00)}} @else{{@num_format($transaction->shipping_charges)}} @endif" data-default="0.00">
		&nbsp;</span>	
	</div>
	@if(in_array('types_of_service', $enabled_modules))
		<div class="{{-- tw-flex  tw-w-full --}} border border-{{ $theme_color }}-500 rounded-lg {{-- text-nowrap --}} tw-justify-betwwen tw-justify-center md:tw-text-sm tw-font-semibold">
			<b class=" md:tw-text-sm tw-font-semibold">@lang('lang_v1.packing_charge')(+):</b>
			<i class="fas fa-edit cursor-pointer service_modal_btn"></i> 
			<span   id="packing_charge_text">
				0
			</span>&nbsp;
		</div>			
	@endif

	@if(!empty($pos_settings['amount_rounding_method']) && $pos_settings['amount_rounding_method'] > 0)
		<div class="{{-- tw-flex tw-w-full --}} border border-{{ $theme_color }}-500 rounded-lg{{-- text-nowrap --}} tw-justify-betwwen tw-justify-center md:tw-flex  {{-- md:tw-hidden --}} {{--  --}}">
			&nbsp;<span class=" md:tw-text-sm tw-font-semibold ">
				<b class=" md:tw-text-sm tw-font-semibold" id="round_off">@lang('lang_v1.round_off'):</b> 
				<span class="" id="round_off_text">0</span>								
				<input type="hidden" name="round_off_amount" id="round_off_amount" value=0>
			</span>&nbsp;
		</div>
	@endif

</div> 






