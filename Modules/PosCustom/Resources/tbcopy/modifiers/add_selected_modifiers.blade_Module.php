@php
//#JCN Add to work with second screen
$count_modifier = 0;
@endphp

@if(empty($edit_modifiers))
<small>
	@foreach($modifiers as $modifier)
		<div class="product_modifier">
		{{$modifier->name}}(<span class="modifier_price
		_text">{{@num_format($modifier->sell_price_inc_tax)}}</span> X <span class="modifier_qty_text">{{@num_format($quantity)}}</span>)
		<input type="hidden" name="products[{{$index}}][modifier][{{$count_modifier}}]" 
			value="{{$modifier->id}}">
		<input type="hidden" class="modifiers_price" 
			name="products[{{$index}}][modifier_price][{{$count_modifier}}]" 
			value="{{@num_format($modifier->sell_price_inc_tax)}}">
		{{--#JCN Add the name of the modifier por second screen--}}
		<input type="hidden" class="modifiers_name" 
			name="products[{{$index}}][modifier_name][{{$count_modifier}}]" 
			value="{{$modifier->name}}">
		{{--#END Modifiers add--}}
		<input type="hidden" class="modifiers_quantity" 

			name="products[{{$index}}][modifier_quantity][{{$count_modifier}}]" 
			value="{{$quantity}}">
		<input type="hidden" 
			name="products[{{$index}}][modifier_set_id][{{$count_modifier}}]" 
			value="{{$modifier->product_id}}">
		</div>
		@php
			$count_modifier = $count_modifier + 1;
		@endphp
	@endforeach
</small>
@else

	@foreach($modifiers as $modifier)
		<div class="product_modifier">
		{{$modifier->variations->name ?? ''}}(<span cla
		ss="modifier_price_text">{{@num_format($modifier->unit_price_inc_tax)}}</span> X <span class="modifier_qty_text">{{@num_format($modifier->quantity)}}</span>)
		<input type="hidden" name="products[{{$index}}][modifier][{{$count_modifier}}]" 
			value="{{$modifier->variation_id}}">
		<input type="hidden" class="modifiers_price" 
			name="products[{{$index}}][modifie
			r_price][{{$count_modifier}}]" 
			value="{{@num_format($modifier->unit_price_inc_tax)}}">
		{{--#JCN Add the name of the modifier por second screen--}}
		<input type="hidden" class="modifiers_name" 
			name="products[{{$index}}][modifier_name][{{$count_modifier}}]" 
			value="{{@num_format($modifier->variations->name)}}">
		{{--#END Modifiers add--}}
		<input type="hidden" class="modifiers_quantity" 
			name="products[{{$index}}][modifier_quantity][{{$count_modifier}}]" 
			value="{{$modifier->quantity}}">
		<input type="hidden" 

			name="products[{{$index}}][modifier_set_id][{{$count_modifier}}]" 
			value="{{$modifier->product_id}}">
		<input type="hidden" 
			name="products[{{$index}}][modifier_sell_line_id][{{$count_modifier}}]" 
			value="{{$modifier->id}}">
		</div>
		@php
			$count_modifier = $count_modifier + 1;
		@endphp
	@endforeach
@endif