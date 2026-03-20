@forelse($products as $product)
	<div class="product_list no-print product_box product-card"
        data-variation_id="{{$product->id}}"
        data-enable_stock="{{$product->enable_stock}}"
        data-qty_available="{{$product->qty_available}}"
        data-name="{{$product->name}}"
        data-unit="{{$product->unit}}"
        data-type="{{$product->type}}"
        title="{{$product->name}} @if($product->type == 'variable')- {{$product->variation}} @endif"
        style="aspect-ratio: unset !important; padding: 0 !important;">

		{{-- Favorite Toggle --}}
		<div class="favorite-toggle" data-variation_id="{{$product->id}}" style="position: absolute; top: 4px; right: 4px; z-index: 10; cursor: pointer;">
			<i class="fa {{ in_array($product->id, $featured_products ?? []) ? 'fa-star tw-text-yellow-500' : 'fa-star-o tw-text-gray-300' }}" style="font-size: 14px;"></i>
		</div>

		{{-- Product Name --}}
		<div style="padding: 8px 10px; background: #f8fafc; border-bottom: 1px solid #e2e8f0;">
			<div style="font-weight: 700; font-size: 11px; color: #1e293b; line-height: 1.3; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
				{{$product->name}}@if($product->type == 'variable') - {{$product->variation}}@endif
			</div>
			<div style="font-size: 9px; color: #64748b; margin-top: 2px;">{{$product->sub_sku}}</div>
		</div>

		{{-- Cost Price --}}
		@can('access_cost_price')
		<div style="padding: 6px 10px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e2e8f0;">
			<span style="font-size: 9px; color: #94a3b8; font-weight: 600;">COST</span>
			<span style="font-size: 11px; font-weight: 700; color: #64748b;">@format_currency($product->cost_price ?? 0)</span>
		</div>
		@endcan

		{{-- Selling Price --}}
		<div style="padding: 6px 10px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e2e8f0; background: #f0fdf4;">
			<span style="font-size: 9px; color: #22c55e; font-weight: 600;">SELL</span>
			<span style="font-size: 12px; font-weight: 800; color: #16a34a;">@format_currency($product->selling_price)</span>
		</div>

		{{-- Quantity --}}
		@if($product->enable_stock)
		<div style="padding: 6px 10px; display: flex; justify-content: space-between; align-items: center;">
			<span style="font-size: 9px; color: #94a3b8; font-weight: 600;">QTY</span>
			<span style="font-size: 11px; font-weight: 700; color: {{ $product->qty_available > 0 ? '#3b82f6' : '#ef4444' }};">
				{{@num_format($product->qty_available)}} {{$product->unit}}
			</span>
		</div>
		@else
		<div style="padding: 6px 10px; text-align: center;">
			<span style="font-size: 9px; color: #94a3b8;">Service Item</span>
		</div>
		@endif
	</div>
@empty
	<input type="hidden" id="no_products_found">
	<div style="grid-column: span 4; padding: 32px 16px; text-align: center;">
		<i class="material-icons" style="font-size: 48px; color: #e2e8f0; margin-bottom: 12px; display: block;">search_off</i>
		<p style="color: #64748b; font-size: 14px; margin: 0;">
			@lang('lang_v1.no_products_to_display')
		</p>
	</div>
@endforelse
