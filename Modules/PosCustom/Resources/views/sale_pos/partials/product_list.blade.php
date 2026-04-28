{{-- dump($products->lastPage()) --}}

{{-- dump($products) --}}
{{-- dump($poscustom_size_badge) --}}
@php
	$price_pos_class = 'tw-bg-' . $business_details->theme_color . '-800' . '';
	$divider_class = 'theme_' . $business_details->theme_color . '_divider';
	$colorfocus = $business_details->theme_color;
	
	//Size stock badge default
	$size_bage = 50;
	if (isset($poscustom_size_badge)){	
		$size_bage = $poscustom_size_badge;
	} 

/* 	$size_products = 'tw-w-24';
	if (isset($poscustom_width_product)){
		//w-24 = 96px so 96/4 = w-24  160/4 = w-40	
		$size_products = 'tw-w-'.$poscustom_width_product / 4;
		if ($size_products == 'tw-w-24') 
			$size_text = 'tw-text-xs';
		else 
			$size_text = 'tw-text-sm';

	}  */

	/*#JCN 2025-12-2-7 Still testing...*/
	if (isset($poscustom_width_product)){
		//w-24 = 96px so 96/4 = w-24  160/4 = w-40	
		$size_products = 'width: '.$poscustom_width_product.'px';
		if ($poscustom_width_product == '105') 
			$size_text = 'tw-text-sm';
		else 
			$size_text = 'tw-text-xs';
	} 

/* 	$size_products = 'width: 109px';
	$size_text = 'tw-text-sm'; */

	//Active Product 
	$style = "
			<style>
				/*Categories*/
				.prd-active-cate:hover,
				.prd-active-cate:focus
				{
					cursor: pointer;
					box-shadow: $colorfocus 0 1px 2px 2px !important; 
					font-weight: 800;
					background-color: #E5E5E5FF;
				}			
				/* Products */
				.prd-active:hover,
				.prd-active:focus
				{
					cursor: pointer;
					box-shadow: $colorfocus 0 1px 2px 2px !important;
					font-weight: 700;
				}
			</style>
		";

@endphp

<?php 
echo $style; 
?>

{{-- $size_bage = 
{{ $size_bage  }}
{{ $size_products }}
{{ $poscustom_width_product }} 
 style="{{ $size_products }}"
 <div style="width: 110px"  id="product_block"  class="tw-inline-block  tw-justify-betwwen tw-justify-center  no-print " > 

 --}}

@forelse($products as $product) 
	<div style="{{ $size_products }} "  id="product_block"  class="tw-inline-block  tw-ml-1 {{-- tw-mt-1 --}} tw-justify-betwwen tw-justify-center no-print " > 
			<div class=" posBadgeStock product_box prd-active hover:tw-animate-pulse tw-text-center tw-font-semibold tw-mt-0.5 tw-w-100% tw-bg-white tw-rounded-lg " 
				data-variation_id="{{$product->id}}" 
				title="{{$product->name}} 
				@if($product->type == 'variable')- {{$product->variation}} @endif 
				{{ '(' . $product->sub_sku . ')'}} 
				@if(!empty($show_prices)) @lang('lang_v1.default') - @format_currency($product->selling_price) 
					@foreach($product->group_prices as $group_price) 
						@if(array_key_exists($group_price->price_group_id, $allowed_group_prices)) {{$allowed_group_prices[$group_price->price_group_id]}} - @format_currency($group_price->price_inc_tax) @endif 
					@endforeach 
				@endif">
				{{-- 	<div class="tw-bg-white tw-w-24 tw-h-28 product_box btn-default_ff" data-variation_id="{{$product->id}}" title="{{$product->name}} @if($product->type == 'variable')- {{$product->variation}} @endif {{ '(' . $product->sub_sku . ')'}} @if(!empty($show_prices)) @lang('lang_v1.default') - @format_currency($product->selling_price) @foreach($product->group_prices as $group_price) @if(array_key_exists($group_price->price_group_id, $allowed_group_prices)) {{$allowed_group_prices[$group_price->price_group_id]}} - @format_currency($group_price->price_inc_tax) @endif @endforeach @endif"> --}}		<div class="image-container img" 
				style="background-image: url(
						@if(count($product->media) > 0)
							{{$product->media->first()->display_url}}
						@elseif(!empty($product->product_image))
							{{asset('/uploads/img/' . rawurlencode($product->product_image))}}
						@else
							{{asset('/img/default.png')}}
						@endif
					);
				background-repeat: no-repeat; background-position: center;
				background-size: contain;">
				{{--#JCN To put the stock in the image ... testing--}}
			
			</div>
			<div class="text_div "> 
				<small class="text {{ $size_text }} letter:text-muted"> {{$product->name}} </small>
				@if ($product->type == "single" || $product->type == "variable" )
		
					@if ($product->qty_available > 5 ) {{-- there is available stock greeen --}}
	
						<span style="width: {{$size_bage}}%" class="tw-inline-flex tw-items-center tw-justify-center tw-border-2 tw-rounded-tr-xl tw-rounded-bl-xl {{--tw-w-8--}} tw-h-6 tw-ms-2 tw-text-xs tw-font-semibold text-white tw-bg-green-600 {{-- tw-rounded-full --}}">
							{{number_format($product->qty_available)}}
						</span>
						<small class="text first-letter:text-muted" style="color: green">
							@if ($product->type == "variable") 
								{{$product->variation}} {{--Variable--}}
							@else
								<i class="fas fa-caret-down" ></i>
							@endif
						</small>	
					@elseif ($product->qty_available > 0 ) {{-- there is available stock low orange --}}
						<span  style="width: {{$size_bage}}%" class=" tw-inline-flex tw-items-center tw-justify-center tw-border-2 tw-rounded-tr-xl tw-rounded-bl-xl {{--tw-w-8--}} tw-h-6 tw-ms-2 tw-text-xs tw-font-semibold text-white tw-bg-orange-500 {{-- tw-rounded-full --}}">
							{{number_format($product->qty_available)}}
						</span>	
						<small class="text first-letter:text-muted" style="color: orange">

							@if ($product->type == "variable") 
								{{$product->variation}} {{--Variable--}}
								@else
								<i class="fas fa-caret-down" ></i>
							@endif
						</small>

					@else {{-- there isnt available stock CERO --}}
						<span style="width: {{$size_bage}}%"  class=" tw-inline-flex tw-items-center tw-justify-center tw-border-2 tw-rounded-tr-xl tw-rounded-bl-xl {{--tw-w-8--}} tw-h-6 tw-ms-2 tw-text-xs tw-font-semibold text-white tw-bg-red-600 {{-- tw-rounded-full --}}">
							{{number_format($product->qty_available)}}
						</span>	
						<small class="text first-letter:text-muted" style="color:red">
							@if ($product->type == "variable") 
								{{$product->variation}} {{--Variable--}}
								@else
								<i class="fas fa-caret-down" ></i>
							@endif
						</small>
					@endif
				@else
					<small class="text first-letter:text-muted" >
						<i class="fas fa-grip-vertical" ></i>
						{{-- ({{$product->sub_sku}}) --}}
					</small>
				@endif
			
				{{--#JCN to show the selling price--}}
				<div class="{{ $size_text }} text-muted" >
					<div class= "tw-rounded-lg {{$price_pos_class}} text-white" > @format_currency($product->selling_price)	</div> 
				</div>
				
			</div>
		</div>
	</div>
@empty
	<input type="hidden" id="no_products_found">
	<div class="col-md-12">
		<h4 class="text-center">
			@lang('lang_v1.no_products_to_display')
		</h4>
	</div>
	<script type="text/javascript">

	//#JCN If there isn't products to display $products->total() = 0
	var totalProducts = {{ $products->total() }}
	//#JCN Disable the button?s
	if (totalProducts == 0)
		{
			$('#previous').attr('disabled','disabled');
			$('#next').attr('disabled','disabled');
			$('#pagePN').attr('value', '0/0' );
		}

	</script>
	
@endforelse

<script type="text/javascript">
	var lastPage = {{ $products->lastPage() }};
	var currentPage = {{ $products->currentPage() }};
	var totalPages =  currentPage + '/' + lastPage ;

	$('#pagePN').attr('value', totalPages );
	
	if (currentPage == lastPage)
		$('#next').attr('disabled','disabled');
    else
        $('#next').removeAttr('disabled');

	if (currentPage > 1)
		$('#previous').removeAttr('disabled');
	else
		$('#previous').attr('disabled','disabled');


</script>
