@forelse($categories as $category) 

		<button id="btn_product_category" name="bt{{$category['id']}}" 
		{{-- class="btn btn-prni btn-default_ff" change class to the active style 2025 --}} 
		{{--#JCN btn-prni hast the parameters high with image or without image
			#JCN active-{{$category['id']}} is used to identified the button/tab to change the backgroundcolor with JS
			#JCN in $('button#btn_product_category').on('click', function(e) app_js_custom --}}
		value="{{$category['id']}}"  tabindex="-1" title="{{$category['name']}}"
		class="active-{{$category['id']}} title_muted {{-- btn --}} btn-prni  prd-active-cate  hover:tw-animate-pulse tw-text-center tw-font-semibold tw-w-100% {{-- tw-mt-0.5  tw-bg-white--}}  " 
		style="color: black" type="button">
		@if (!empty($category['sub_categories'])) {{--If there isnt empty has categories--}}
          <span id="icon-{{$category['id']}}" class="text-slate-800 transition-transform duration-300">
            ▷
          </span>        
        @endif
			{{--#JCN 2025 Remove img--}}
			{{-- <img src="{{asset('/img/' . $category['id'] .'.jpg')}}" class="img-tumbnail">  --}}		    
			<small>{{$category['name']}}</small>
		</button>	
@empty
	<input type="hidden" id="no_products_found">
	<div class="col-md-12">
		<h4 class="text-center">
			@lang('lang_v1.no_products_to_display')
		</h4>
	</div>
@endforelse





