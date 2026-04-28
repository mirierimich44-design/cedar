{{--  {{dump($featured_products)}}   --}}
{{-- {{ dump($business_details) }} --}}

@php
	if (!empty($business_details)) {
		$price_pos_class = 'tw-bg-' . $business_details->theme_color . '-800';
		$divider_class = 'theme_' . $business_details->theme_color . '_divider';
		$colorfocus = $business_details->theme_color;
		//Product active
		$style = "
				<style>
					.prd-active:hover,
					.prd-active:focus
					{
						cursor: pointer;
						box-shadow: $colorfocus 0 1px 2px 3px !important;
						font-weight: 700;
					}
					
				</style>
			";
	}
	else {
		$price_pos_class = 'tw-bg-blue-800';
	}

@endphp 

@foreach($featured_products as $variation)
<div  class="tw-inline-block tw-px-3 tw-w-28 tw-mt-0.1 tw-ml-1 {{--  pos_product_list--}}  no-print " > {{-- #JCN change col-xs-4 col-sm-3 col-md-2 for css pos_product_list --}}
<div class="product_box prd-active hover:tw-animate-pulse tw-text-center tw-font-semibold tw-mt-0.5 tw-w-100% tw-bg-white tw-rounded-lg   " data-toggle="tooltip" data-placement="bottom" data-variation_id="{{$variation->id}}" title="{{$variation->full_name}}"> 

		<div class="image-container" 
			style="background-image: url(
					@if(count($variation->media) > 0)
						{{$variation->media->first()->display_url}}
					@elseif(!empty($variation->product->image_url))
						{{$variation->product->image_url}}
					@else
						{{asset('/img/default.png')}}
					@endif
				);
			background-repeat: no-repeat; background-position: center;
			background-size: contain;">
			
		</div>

		{{-- 		<div class="text_div">
			<small class="text text-muted">
				{{$variation->product->name}} 
				@if($variation->product->type == 'variable')
					- {{$variation->name}}
				@endif
			</small>

			<small class="text-muted">
				({{$variation->sub_sku}})
				- {{$variation->quantity}}
			</small>
			<small class="text-muted" >
				<div class= "tw-rounded-lg {{$price_pos_class}} text-white" >  @format_currency($variation->default_sell_price) 	</div> 
			</small>
		</div> --}}

		<div class="text_div">
			<small class="text first-letter:text-muted"> {{$variation->product->name}} </small>
			@if ( $variation->product->type == "variable" )
					<small class="text-muted" style="color: green">
						@if ($variation->product->type == "variable") 
							{{$variation->name}} {{--Variable--}}
						@endif
					</small>

					<!--
						<span class=" tw-inline-flex tw-items-center tw-justify-center tw-w-6 tw-h-4 tw-ms-2 tw-text-xs tw-font-semibold text-white tw-bg-white {{-- tw-bg-green-600 --}} tw-rounded-full">
							{{--number_format($variation->quantity)--}}  
						</span>	
					-->
			@else
				<small class="text-muted" >
					<i class="fas fa-grip-vertical" ></i>
					{{-- ({{$product->sub_sku}}) --}}
				</small>
			@endif
		
			{{--#JCN to show the selling price--}}
			<small class="text-muted" >
				<div class= "tw-rounded-lg {{$price_pos_class}} text-white" >  @format_currency($variation->default_sell_price) 	</div> 
			</small>
		</div>
			
		</div>
	</div>
@endforeach