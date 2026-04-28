<style type="text/css">

/*   .acc-header {
    display: flex;
    cursor: pointer;
    align-items: center;
    justify-content: space-between;
    border-top-width: 1px;
    border-bottom-width: 1px;
    --tw-border-opacity: 1;
    border-color: rgb(8 145 178 / var(--tw-border-opacity, 1));
    --tw-bg-opacity: 1;
    background-color: rgb(8 145 178 / var(--tw-bg-opacity, 1));
    padding-left: 1.25rem;
    padding-right: 1.25rem;
    --tw-text-opacity: 1; 
    color: rgb(255 255 255 / var(--tw-text-opacity, 1));
  } */

   .acc-header1:hover {
      --tw-bg-opacity: 1;
      background-color: rgb(229 231 235 / var(--tw-bg-opacity, 1));
  } 

  .acc-header1 span {
    transition-duration: 300ms;
  }

/*   .acc-header p {
    font-weight: 600;
  } */

/*   .acc-body {
    overflow: hidden;
    --tw-bg-opacity: 1;
    background-color: rgb(245 245 244 / var(--tw-bg-opacity, 1));
    padding-left: 1.25rem;
    padding-right: 1.25rem;
    font-size: 0.875rem;
    line-height: 1.25rem;
    transition-duration: 300ms;
    transition-timing-function: linear;
  } */

  .acc-item.accclose .acc-body1 {
    max-height: 0px;
    opacity: 0;
  }

  .acc-item.accopen .acc-header1 span {
    --tw-rotate: 180deg;
    transform: translate(var(--tw-translate-x), var(--tw-translate-y)) rotate(var(--tw-rotate)) skewX(var(--tw-skew-x)) skewY(var(--tw-skew-y)) scaleX(var(--tw-scale-x)) scaleY(var(--tw-scale-y));
  }

  .acc-item.accopen .acc-body1 {
    max-height: -moz-fit-content;
    max-height: fit-content;
    padding-top: 0.75rem;
    padding-bottom: 0.75rem;
    /* opacity: 1; */
    

  }

  /*Category active*/
  .active-cate:hover,
  .active-cate:focus
  {
    cursor: pointer;
    box-shadow: $colorfocus 0 1px 2px 2px !important; 
    font-weight: 800;
    background-color: #E5E5E5FF;
  }

    .cardpanel {
        border-radius: ppx !important;
        border-top-left-radius: 20px;
        border-top-right-radius: 20px;
        border-bottom-left-radius: 0px;
        border-bottom-right-radius: 0px;
        border: 3px solid #ddd !important;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1) !important;
        transition: box-shadow 0.3s ease !important;
        /* font-size: 10px !important;
    	font-family: 'Times New Roman' !important; */
        /* background-color: #f0f0f0; */
    }
    .all-badge{
      position: absolute;
      top: 100px;
      
      /* left: calc(100% - 10px); */
      text-wrap: nowrap;
      padding: 4px 6px;
      border-radius: 30px;
      

    }
    
</style>
@php
    $theme_color = $business_details->theme_color;
    if ($theme_color == 'primary') /* primary doesnt exist */
        $theme_color = 'blue';

    $theme_pos_class = 'tw-bg-gradient-to-r tw-from-' . $business_details->theme_color . '-800';
    $btn_accordion = 'color: '.$theme_color;
/*Testing toggle if its possible*/

echo $style;

@endphp
{{--#JCN change the clase close (It makes opacity by default) to accclose --}}
{{-- 
<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="m9 12.75 3 3m0 0 3-3m-3 3v-7.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
</svg>

--}}

<div class="flex justify-center items-center">
  
    <div id="accordion" class="tw-w-full  ">
      <div class="acc-item accclose tw-w-full  ">
          <div class="acc-header1 active-all tw-rounded-lg 
          flex justify-between items-center tw-p-3 main-category  
          tw-border-t-1 border-gray-300 tw-bg-white cursor-pointer"
              data-value="all" data-parent="0"  >
            <div  class="tw-inline-flex tw-items-center " >
              <span class="tw--ms-0.5 ">📱</span> <!-- -ms-0.5 to the left -->
              <p class="text-nowrap hover:text-balance tw-text-xs tw-text-left">@lang('lang_v1.all_category')</p>
            </div>
          </div>
      </div>
      @foreach ($categories as $category)
      <!--#JCN data-overlay="1" 1=Yes, it works with click in the button to show categories in the POS
          0=No it works with catgegory left      to avoid overlay-category-->
      <div class="acc-item accclose tw-w-full" >
          <div class="acc-header1 tw-rounded-lg 
                  flex justify-between items-center tw-p-3 {{-- px-5 py-3 --}} 
                  active-{{$category['id']}} border border-gray-300 tw-bg-white cursor-pointer 
                  @if (empty($category['sub_categories'])) main-category   
                  @else main-category-div    
                  @endif no-print" 
                  data-overlay="0" data-value="{{ $category['id'] }}" data-name="{{ $category['name'] }}" data-parent="0" >
          @if (!empty($category['sub_categories'])) {{--If there isnt empty has categories--}}
            <span {{-- style="{{ $btn_accordion }}" --}} id="icon-{{$category['id']}}" class="tw-text-xs">▲</span> {{-- ↳&nbsp; ○ ▲ ▸ △ ▲ ┕ ┣ --}}
          @endif 
          <p class="text-nowrap  hover:text-balance tw-text-xs">&nbsp;{{ $category['name'] }}</p>
          </div>
          @if (!empty($category['sub_categories'])) {{--Isnt a parent category--}}
            @foreach ($category['sub_categories'] as $sc)
            <div class="tw-pl-2 tw-w-full">
              <div class="acc-body1  tw-rounded-lg tw-border-t-1 border-gray-300 tw-bg-white  {{-- px-5  --}}text-sm overflow-hidden duration-300 ease-linear active-{{$sc['id']}} product_subcategory no-print" 
                    data-maincategory="{{$category['id']}}" 
                    data-overlay="0" data-value="{{ $sc['id'] }}" data-name="{{$sc['name']}}" data-parent="1" value="{{$sc['id']}}">
                  <p class="tw-pl-2 text-nowrap hover:text-balance tw-text-xs tw-text-left text-slate-800" >
                    <span style="{{ $btn_accordion }}">●</span> {{ $sc['name'] }} 
                  </p>
              </div>            
            </div>
            @endforeach
          @endif
      </div>  
      @endforeach
    </div>
</div>

<!--#JCNCommon Script to translate to pos_js_custom.blade.php-->
<!--const accItems = document.querySelectorAll(".acc-item"); Toggle class for subcategories--> 
