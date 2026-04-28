@php
    $is_mobile = isMobile();
	$tab_class = " tw-rounded-tr-xl tw-rounded-tl-xl";
    /*pos-form-actions its neccesary, pending to see the action on resize*/    
@endphp

<!--Uncomment this line to generate the tailwind styles-->
{{-- <div class=" border-2 border-blue-700 border-black border-purple-700 border-green-700 border-red-700 border-yellow-700 border-orange-700 border-sky-700 "></div>--}}
{{-- 
<div class="text-orange-700 hover:text-white border border-orange-700 hover:bg-orange-500 focus:ring-orange-300 
			dark:border-orange-500 dark:text-orange-500 dark:hover:text-white dark:hover:bg-orange-600 dark:focus:ring-orange-800">
</div>
 --}}

@if ($poscustom_btnstyle == 's1')
    <!--#JCN Original style for buttons actions (s1 rounded hover) -->
    <div class="pos-form-actions {{-- tw-bg-white --}} tw-pt-1 tw-w-full !tw-items-center tw-justify-between {{-- tw-rounded-tr-xl tw-rounded-tl-xl --}} tw-shadow-[rgba(17,_17,_26,_0.1)_0px_0px_16px] tw-border-t-2 {{ $border_pos_class }}">
                    
        {{-- Total Small devices--}}
        <div class="{{-- tw-bg-red-400 --}} hidden  md:!tw-w-none !tw-flex md:!tw-hidden !tw-flex-row !tw-items-center !tw-gap-3">
            <div class="tw-pos-total tw-flex tw-items-center tw-gap-3">
                <div class="text-black tw-font-bold text-sm tw-flex tw-items-center tw-flex-col tw-leading-1 ">
                    <div>@lang('sale.total_payable'):</div>
                    {{-- <div>Payable:</div> --}}
                </div>
                <input type="hidden" name="final_total" id="final_total_input" value="0.00" >
                <span id="total_payable" class="text-green-900 tw-font-bold text-sm number" >0.00</span>
            </div>
        </div>
        
        {{-- Small devices essentials buttons--}} 
        <div class="tw-flex tw-flex-row tw-w-full !tw-items-center tw-justify-between md:!tw-hidden {{-- !tw-gap-3 --}}">
        
            @if (!Gate::check('disable_pay_checkout') || auth()->user()->can('superadmin') || auth()->user()->can('admin'))        
                <button type="button" id="pos-finalize" title="@lang('lang_v1.tooltip_checkout_multi_pay')" 
                    class="
                    text-blue-700 hover:text-white border border-blue-700 hover:bg-blue-500 focus:ring-4 focus:outline-none focus:ring-blue-300 
                    font-medium text-sm tw-p-2 tw-w-full {{-- me-2 mb-2 --}} 
                    dark:border-green-500 dark:text-green-500 dark:hover:text-white dark:hover:bg-green-600 dark:focus:ring-green-800">
                    <p class="text-nowrap"> <i class="fas fa-money-check-alt" aria-hidden="true"> @lang('lang_v1.checkout_multi_pay') </i></p>
                </button>
            @endif     
            
            @if (!Gate::check('disable_express_checkout') || auth()->user()->can('superadmin') || auth()->user()->can('admin'))
                <button type="button" 
                    class="pos-express-finalize 
                        @if (!$is_mobile)  @endif no-print @if ($pos_settings['disable_express_checkout'] != 0 || !array_key_exists('cash', $payment_types)) hide @endif 
                        text-green-700 hover:text-white border border-green-700 hover:bg-green-500 focus:ring-4 focus:outline-none focus:ring-green-300 
                        font-medium text-sm tw-p-2 tw-w-full {{-- me-2 mb-2--}}  
                        dark:border-green-500 dark:text-green-500 dark:hover:text-white dark:hover:bg-green-600 dark:focus:ring-green-800"
                        data-pay_method="cash" title="@lang('tooltip.express_checkout')">
                    <p class="{{-- text-nowrap --}}"> <i class="fas fa-money-bill-alt" aria-hidden="true"> @lang('lang_v1.express_checkout_cash') </i></p>
                </button>
            @endif

            @if (empty($edit))
                    <button type="button"					
                        class="text-red-700 hover:text-white border border-red-700 hover:bg-red-500 focus:ring-4 focus:outline-none focus:ring-red-300 
                            font-medium text-sm tw-p-2 tw-w-full {{-- me-2 mb-2 --}}  dark:border-red-500 dark:text-red-500 dark:hover:text-white dark:hover:bg-red-600 dark:focus:ring-red-800"
                            id="pos-cancel" title="@lang('sale.cancel')">
                        <p class="{{-- text-nowrap --}}"> <i class="fas fa-window-close" aria-hidden="true"> @lang('sale.cancel') </i></p>
                    </button>
            @else
                    <button type="button"					
                        class="text-red-700 hover:text-white border border-red-700 hover:bg-red-500 focus:ring-4 focus:outline-none focus:ring-red-300 
                            font-medium text-sm tw-p-2 tw-w-full  tw-hidden  {{-- me-2 mb-2--}}  dark:border-red-500 dark:text-red-500 dark:hover:text-white dark:hover:bg-red-600 dark:focus:ring-red-800"
                            id="pos-delete" @if (!empty($only_payment)) disabled @endif title="@lang('messages.delete')">
                        <p class="text-nowrap"> <i class="fas fa-trash-alt" aria-hidden="true"> @lang('messages.delete') </i></p>
                    </button>
            @endif
        </div >

        {{-- Large devices essentials buttons--}} 
        
        <div class=" tw-flex tw-flex-row  {{-- tw-p-1 tw-gap-4 --}} tw-justify-betwwen tw-justify-center tw-hidden md:tw-flex tw-overflow-x-auto">
            
            @if (!Gate::check('disable_pay_checkout') || auth()->user()->can('superadmin') || auth()->user()->can('admin'))        
                <button type="button" id="pos-finalize" title="@lang('lang_v1.tooltip_checkout_multi_pay')" 
                    class="text-blue-700 hover:text-white border border-blue-700 hover:bg-blue-500 focus:ring-4 focus:outline-none focus:ring-blue-300 
                    font-medium text-sm tw-p-2 tw-w-full {{-- tw-p-2 me-2 mb-2--}}  
                    dark:border-blue-500 dark:text-blue-500 dark:hover:text-white dark:hover:bg-blue-600 dark:focus:ring-blue-800">
                    <p class="tw-flex-wrap"> <i class="fas fa-money-check-alt" aria-hidden="true"> @lang('lang_v1.checkout_multi_pay') </i></p>
                </button>
            @endif     
            
            @if (!Gate::check('disable_express_checkout') || auth()->user()->can('superadmin') || auth()->user()->can('admin'))
                <button type="button" 
                    class="pos-express-finalize 
                        @if (!$is_mobile)  @endif no-print @if ($pos_settings['disable_express_checkout'] != 0 || !array_key_exists('cash', $payment_types)) hide @endif 
                        text-green-700 hover:text-white border border-green-700 hover:bg-green-500 focus:ring-4 focus:outline-none focus:ring-green-300 
                        font-medium text-sm tw-p-2 tw-w-full{{-- me-2 mb-2--}}  dark:border-green-500 dark:text-green-500 dark:hover:text-white dark:hover:bg-green-600 dark:focus:ring-green-800"
                        data-pay_method="cash" title="@lang('tooltip.express_checkout')">
                    <p class="text-nowrap"> <i class="fas fa-money-bill-alt" aria-hidden="true"> @lang('lang_v1.express_checkout_cash') </i></p>
                </button>
            @endif

            @if (!Gate::check('disable_draft') || auth()->user()->can('superadmin') || auth()->user()->can('admin'))
                <button type="button" 
                    class="@if ($pos_settings['disable_draft'] != 0) hide @endif 
                        text-orange-700 hover:text-white border border-orange-700 hover:bg-orange-500 focus:ring-4 focus:outline-none focus:ring-orange-300 
                        font-medium text-sm tw-p-2 tw-w-full{{-- me-2 mb-2--}}  dark:border-orange-500 dark:text-orange-500 dark:hover:text-white dark:hover:bg-orange-600 dark:focus:ring-orange-800"
                        id="pos-draft" @if (!empty($only_payment)) disabled @endif> 
                    <p class="text-nowrap"> <i class="fas fa-edit " aria-hidden="true"> @lang('sale.draft') </i></p>
                </button>
            @endif
            <!--Button Cancel-->
            @if (empty($edit))
                    <button type="button"					
                        class="text-red-700 hover:text-white border border-red-700 hover:bg-red-500 focus:ring-4 focus:outline-none focus:ring-red-300 
                            font-medium text-sm tw-p-2 tw-w-full{{-- me-2 mb-2--}}  
                            dark:border-red-500 dark:text-red-500 dark:hover:text-white dark:hover:bg-red-600 dark:focus:ring-red-800"
                            id="pos-cancel" title="@lang('sale.cancel')">
                        <p class="text-nowrap"> <i class="fas fa-window-close" aria-hidden="true"> @lang('sale.cancel') </i></p>
                    </button>
            @else
                    <button type="button"					
                        class="text-red-700 hover:text-white border border-red-700 hover:bg-red-500 focus:ring-4 focus:outline-none focus:ring-red-300 
                            font-medium text-sm tw-p-2 tw-w-full{{-- me-2 mb-2--}}  dark:border-red-500 dark:text-red-500 dark:hover:text-white dark:hover:bg-red-600 dark:focus:ring-red-800"
                            id="pos-delete" @if (!empty($only_payment)) disabled @endif title="@lang('messages.delete')">
                        <p class="text-nowrap"> <i class="fas fa-trash-alt" aria-hidden="true"> @lang('messages.delete') </i></p>
                    </button>
            @endif	
        </div>
        
        <div class=" tw-flex tw-flex-row  {{-- tw-gap-4 --}} tw-justify-betwwen tw-justify-center tw-hidden md:tw-flex tw-overflow-x-auto">
            @if (!Gate::check('disable_quotation') || auth()->user()->can('superadmin') || auth()->user()->can('admin'))
                <button type="button"
                    class="text-amber-700 hover:text-white border border-amber-700 
                    hover:bg-amber-500 focus:ring-4 focus:outline-none focus:ring-amber-300 
                    font-medium text-sm tw-p-2 tw-w-full 
                    dark:border-amber-500 dark:text-amber-500 dark:hover:text-white dark:hover:bg-amber-600 dark:focus:ring-amber-900 @if ($is_mobile) col-xs-6 @endif"
                    id="pos-quotation" @if (!empty($only_payment)) disabled @endif>
                    <p class="text-nowrap"><i class="fas fa-edit"></i> @lang('lang_v1.quotation')</p>
                    </button>
            @endif	            
            @if (!Gate::check('disable_suspend_sale') || auth()->user()->can('superadmin') || auth()->user()->can('admin'))
                @if (empty($pos_settings['disable_suspend']))
                    <button type="button"					
                        class="no-print pos-express-finalize
                            text-yellow-700 hover:text-white border border-yellow-700 hover:bg-yellow-500 focus:ring-4 focus:outline-none focus:ring-yellow-300 
                            font-medium text-sm tw-p-2 tw-w-full 
                            dark:border-yellow-500 dark:text-yellow-500 dark:hover:text-white dark:hover:bg-yellow-600 dark:focus:ring-yellow-800"
                                data-pay_method="suspend" title="@lang('lang_v1.tooltip_suspend')"
                            @if (!empty($only_payment)) disabled @endif>
                        <p class="text-nowrap"> <i class="fas fa-pause" aria-hidden="true"> @lang('lang_v1.suspend') </i></p>
                    </button>
                @endif
            @endif

            @if (!Gate::check('disable_credit_sale') || auth()->user()->can('superadmin') || auth()->user()->can('admin'))
                @if (empty($pos_settings['disable_credit_sale_button']))
                    <input type="hidden" name="is_credit_sale" value="0" id="is_credit_sale">					
                    <button type="button"					
                        class="no-print pos-express-finalize @if ($is_mobile) col-xs-6 @endif
                            text-emerald-700 hover:text-white border border-emerald-700 hover:bg-emerald-500 focus:ring-4 focus:outline-none focus:ring-emerald-300 
                            font-medium text-sm tw-p-2 tw-w-full
                            dark:border-emerald-500 dark:text-emerald-500 dark:hover:text-white dark:hover:bg-emerald-600 dark:focus:ring-emerald-800"
                            data-pay_method="credit_sale" title="@lang('lang_v1.tooltip_credit_sale')"
                        @if (!empty($only_payment)) disabled @endif>
                        <p class="text-nowrap"> <i class="fas fa-check" aria-hidden="true"> @lang('lang_v1.credit_sale') </i></p>
                    </button>
                @endif
            @endif

            @if (!Gate::check('disable_card') || auth()->user()->can('superadmin') || auth()->user()->can('admin'))
                <button type="button"					
                    class="no-print @if (!empty($pos_settings['disable_suspend']))  @endif pos-express-finalize @if (!array_key_exists('card', $payment_types)) hide @endif 
                        text-sky-700 hover:text-white border border-sky-700 
                        hover:bg-sky-500 focus:ring-4 focus:outline-none focus:ring-sky-300 
                        font-medium text-sm tw-p-2 tw-w-full  
                        dark:border-sky-500 dark:text-sky-500 dark:hover:text-white dark:hover:bg-sky-600 dark:focus:ring-sky-800"
                        data-pay_method="card" title="@lang('lang_v1.tooltip_express_checkout_card')">
                    <p class="text-nowrap"> <i class="fas fa-credit-card" aria-hidden="true"> @lang('lang_v1.express_checkout_card') </i></p>
                </button>
            @endif

            {{-- recent transactions buttons--}} 
            @if (!isset($pos_settings['hide_recent_trans']) || $pos_settings['hide_recent_trans'] == 0)
                <button type="button"					
                    class="text-white border tw-border-{{ $theme_color }}-700 dark:border-{{ $theme_color }}-500
                    bg-gradient-to-r from-{{ $theme_color }}-500 via-{{ $theme_color }}-600 to-{{ $theme_color }}-700 hover:bg-gradient-to-br 
                    focus:ring-4 focus:outline-none focus:ring-{{ $theme_color }}-300 
                    dark:focus:ring-{{ $theme_color }}-800 shadow-lg shadow-{{ $theme_color }}-500/50 dark:shadow-lg dark:shadow-{{ $theme_color }}-800/80 
                    font-medium text-sm tw-p-2 tw-w-full"
                        data-toggle="modal" data-target="#recent_transactions_modal" id="recent-transactions" title="@lang('lang_v1.recent_transactions')">
                        <p class="text-nowrap"> <i class="fas fa-clock " aria-hidden="true"> @lang('lang_v1.recent_transactions') </i></p>
                </button>
            @endif	

        </div>
    </div>
@elseif ($poscustom_btnstyle == 's1t')
    <!--#JCN Original style for buttons actions (s1t tab hover) -->
    <div class="pos-form-actions {{ $border_pos_class }} tw-pt-0.5 {{-- tw-bg-white tw-p-1 --}}   tw-w-full !tw-items-center tw-justify-between {{-- tw-rounded-tr-xl tw-rounded-tl-xl --}} tw-shadow-[rgba(17,_17,_26,_0.1)_0px_0px_16px] tw-border-t-2 ">
                    
        {{-- Total Small devices--}}
        <div class="{{-- tw-bg-red-400 --}} hidden  md:!tw-w-none !tw-flex md:!tw-hidden !tw-flex-row !tw-items-center !tw-gap-3">
            <div class="tw-pos-total tw-flex tw-items-center tw-gap-3">
                <div class="text-black tw-font-bold text-sm tw-flex tw-items-center tw-flex-col tw-leading-1 ">
                    <div>@lang('sale.total_payable'):</div>
                    {{-- <div>Payable:</div> --}}
                </div>
                <input type="hidden" name="final_total" id="final_total_input" value="0.00" >
                <span id="total_payable" class="text-green-900 tw-font-bold text-sm number" >0.00</span>
            </div>
        </div>
        
        {{-- Small devices essentials buttons--}} 
        <div class="tw-flex tw-flex-row tw-w-full !tw-items-center tw-justify-between md:!tw-hidden {{-- !tw-gap-3 --}}">
        
            @if (!Gate::check('disable_pay_checkout') || auth()->user()->can('superadmin') || auth()->user()->can('admin'))        
                <button type="button" id="pos-finalize" title="@lang('lang_v1.tooltip_checkout_multi_pay')" 
                    class="
                    text-blue-700 hover:text-white border border-blue-700 hover:bg-blue-500 focus:ring-4 focus:outline-none focus:ring-blue-300 
                    font-medium text-sm tw-p-2 tw-w-full {{ $tab_class }} {{-- me-2 mb-2 --}} 
                    dark:border-green-500 dark:text-green-500 dark:hover:text-white dark:hover:bg-green-600 dark:focus:ring-green-800">
                    <p class="text-nowrap"> <i class="fas fa-money-check-alt" aria-hidden="true"> @lang('lang_v1.checkout_multi_pay') </i></p>
                </button>
            @endif     
            
            @if (!Gate::check('disable_express_checkout') || auth()->user()->can('superadmin') || auth()->user()->can('admin'))
                <button type="button" 
                    class="pos-express-finalize 
                        @if (!$is_mobile)  @endif no-print @if ($pos_settings['disable_express_checkout'] != 0 || !array_key_exists('cash', $payment_types)) hide @endif 
                        text-green-700 hover:text-white border border-green-700 hover:bg-green-500 focus:ring-4 focus:outline-none focus:ring-green-300 
                        font-medium text-sm tw-p-2 tw-w-full {{ $tab_class }} {{-- me-2 mb-2--}}  
                        dark:border-green-500 dark:text-green-500 dark:hover:text-white dark:hover:bg-green-600 dark:focus:ring-green-800"
                        data-pay_method="cash" title="@lang('tooltip.express_checkout')">
                    <p class="{{-- text-nowrap --}}"> <i class="fas fa-money-bill-alt" aria-hidden="true"> @lang('lang_v1.express_checkout_cash') </i></p>
                </button>
            @endif

            @if (empty($edit))
                    <button type="button"					
                        class="text-red-700 hover:text-white border border-red-700 hover:bg-red-500 focus:ring-4 focus:outline-none focus:ring-red-300 
                            font-medium text-sm tw-p-2 tw-w-full {{ $tab_class }} {{-- me-2 mb-2 --}}  dark:border-red-500 dark:text-red-500 dark:hover:text-white dark:hover:bg-red-600 dark:focus:ring-red-800"
                            id="pos-cancel" title="@lang('sale.cancel')">
                        <p class="{{-- text-nowrap --}}"> <i class="fas fa-window-close" aria-hidden="true"> @lang('sale.cancel') </i></p>
                    </button>
            @else
                    <button type="button"					
                        class="text-red-700 hover:text-white border border-red-700 hover:bg-red-500 focus:ring-4 focus:outline-none focus:ring-red-300 
                            font-medium text-sm tw-p-2 tw-w-full {{ $tab_class }} {{-- me-2 mb-2--}}  dark:border-red-500 dark:text-red-500 dark:hover:text-white dark:hover:bg-red-600 dark:focus:ring-red-800"
                            id="pos-delete" @if (!empty($only_payment)) disabled @endif title="@lang('messages.delete')">
                        <p class="text-nowrap"> <i class="fas fa-trash-alt" aria-hidden="true"> @lang('messages.delete') </i></p>
                    </button>
            @endif
        </div >

        {{-- Large devices essentials buttons--}} 
        <div class=" tw-flex tw-flex-row  {{-- tw-p-1 tw-gap-4 --}} tw-justify-betwwen tw-justify-center tw-hidden md:tw-flex tw-overflow-x-auto">
            
            @if (!Gate::check('disable_pay_checkout') || auth()->user()->can('superadmin') || auth()->user()->can('admin'))        
                <button type="button" id="pos-finalize" title="@lang('lang_v1.tooltip_checkout_multi_pay')" 
                    class="text-blue-700 hover:text-white border border-blue-700 hover:bg-blue-500 focus:ring-4 focus:outline-none focus:ring-blue-300 
                    font-medium text-sm tw-p-2 tw-w-full {{ $tab_class }} {{-- tw-p-2 me-2 mb-2--}}  
                    dark:border-blue-500 dark:text-blue-500 dark:hover:text-white dark:hover:bg-blue-600 dark:focus:ring-blue-800">
                    <p class="tw-flex-wrap"> <i class="fas fa-money-check-alt" aria-hidden="true"> @lang('lang_v1.checkout_multi_pay') </i></p>
                </button>
            @endif     
            
            @if (!Gate::check('disable_express_checkout') || auth()->user()->can('superadmin') || auth()->user()->can('admin'))
                <button type="button" 
                    class="pos-express-finalize 
                        @if (!$is_mobile)  @endif no-print @if ($pos_settings['disable_express_checkout'] != 0 || !array_key_exists('cash', $payment_types)) hide @endif 
                        text-green-700 hover:text-white border border-green-700 hover:bg-green-500 focus:ring-4 focus:outline-none focus:ring-green-300 
                        font-medium text-sm tw-p-2 tw-w-full {{ $tab_class }} {{-- me-2 mb-2--}}  dark:border-green-500 dark:text-green-500 dark:hover:text-white dark:hover:bg-green-600 dark:focus:ring-green-800"
                        data-pay_method="cash" title="@lang('tooltip.express_checkout')">
                    <p class="text-nowrap"> <i class="fas fa-money-bill-alt" aria-hidden="true"> @lang('lang_v1.express_checkout_cash') </i></p>
                </button>
            @endif

            @if (!Gate::check('disable_draft') || auth()->user()->can('superadmin') || auth()->user()->can('admin'))
                <button type="button" 
                    class="@if ($pos_settings['disable_draft'] != 0) hide @endif 
                        text-orange-700 hover:text-white border border-orange-700 hover:bg-orange-500 focus:ring-4 focus:outline-none focus:ring-orange-300 
                        font-medium text-sm tw-p-2 tw-w-full {{ $tab_class }} {{-- me-2 mb-2--}}  dark:border-orange-500 dark:text-orange-500 dark:hover:text-white dark:hover:bg-orange-600 dark:focus:ring-orange-800"
                        id="pos-draft" @if (!empty($only_payment)) disabled @endif> 
                    <p class="text-nowrap"> <i class="fas fa-edit " aria-hidden="true"> @lang('sale.draft') </i></p>
                </button>
            @endif
            <!--Button Cancel-->
            @if (empty($edit))
                    <button type="button"					
                        class="text-red-700 hover:text-white border border-red-700 hover:bg-red-500 focus:ring-4 focus:outline-none focus:ring-red-300 
                            font-medium text-sm tw-p-2 tw-w-full {{ $tab_class }} {{-- me-2 mb-2--}}  
                            dark:border-red-500 dark:text-red-500 dark:hover:text-white dark:hover:bg-red-600 dark:focus:ring-red-800"
                            id="pos-cancel" title="@lang('sale.cancel')">
                        <p class="text-nowrap"> <i class="fas fa-window-close" aria-hidden="true"> @lang('sale.cancel') </i></p>
                    </button>
            @else
                    <button type="button"					
                        class="text-red-700 hover:text-white border border-red-700 hover:bg-red-500 focus:ring-4 focus:outline-none focus:ring-red-300 
                            font-medium text-sm tw-p-2 tw-w-full {{ $tab_class }} {{-- me-2 mb-2--}}  dark:border-red-500 dark:text-red-500 dark:hover:text-white dark:hover:bg-red-600 dark:focus:ring-red-800"
                            id="pos-delete" @if (!empty($only_payment)) disabled @endif title="@lang('messages.delete')">
                        <p class="text-nowrap"> <i class="fas fa-trash-alt" aria-hidden="true"> @lang('messages.delete') </i></p>
                    </button>
            @endif	
        </div>
        <div class=" tw-flex tw-flex-row  {{-- tw-gap-4 --}} tw-justify-betwwen tw-justify-center tw-hidden md:tw-flex tw-overflow-x-auto">
            @if (!Gate::check('disable_quotation') || auth()->user()->can('superadmin') || auth()->user()->can('admin'))
                <button type="button"
                    class="text-amber-700 hover:text-white border border-amber-700 
                    hover:bg-amber-500 focus:ring-4 focus:outline-none focus:ring-amber-300 
                    font-medium text-sm tw-p-2 tw-w-full {{ $tab_class }}
                    dark:border-amber-500 dark:text-amber-500 dark:hover:text-white dark:hover:bg-amber-600 dark:focus:ring-amber-900 @if ($is_mobile) col-xs-6 @endif"
                    id="pos-quotation" @if (!empty($only_payment)) disabled @endif>
                    <p class="text-nowrap"><i class="fas fa-edit"></i> @lang('lang_v1.quotation')</p>
                    </button>
            @endif	            
            @if (!Gate::check('disable_suspend_sale') || auth()->user()->can('superadmin') || auth()->user()->can('admin'))
                @if (empty($pos_settings['disable_suspend']))
                    <button type="button"					
                        class="no-print pos-express-finalize
                            text-yellow-700 hover:text-white border border-yellow-700 hover:bg-yellow-500 focus:ring-4 focus:outline-none focus:ring-yellow-300 
                            font-medium text-sm tw-p-2 tw-w-full {{ $tab_class }}
                            dark:border-yellow-500 dark:text-yellow-500 dark:hover:text-white dark:hover:bg-yellow-600 dark:focus:ring-yellow-800"
                                data-pay_method="suspend" title="@lang('lang_v1.tooltip_suspend')"
                            @if (!empty($only_payment)) disabled @endif>
                        <p class="text-nowrap"> <i class="fas fa-pause" aria-hidden="true"> @lang('lang_v1.suspend') </i></p>
                    </button>
                @endif
            @endif

            @if (!Gate::check('disable_credit_sale') || auth()->user()->can('superadmin') || auth()->user()->can('admin'))
                @if (empty($pos_settings['disable_credit_sale_button']))
                    <input type="hidden" name="is_credit_sale" value="0" id="is_credit_sale">					
                    <button type="button"					
                        class="no-print pos-express-finalize @if ($is_mobile) col-xs-6 @endif
                            text-emerald-700 hover:text-white border border-emerald-700 hover:bg-emerald-500 focus:ring-4 focus:outline-none focus:ring-emerald-300 
                            font-medium text-sm tw-p-2 tw-w-full {{ $tab_class }}
                            dark:border-emerald-500 dark:text-emerald-500 dark:hover:text-white dark:hover:bg-emerald-600 dark:focus:ring-emerald-800"
                            data-pay_method="credit_sale" title="@lang('lang_v1.tooltip_credit_sale')"
                        @if (!empty($only_payment)) disabled @endif>
                        <p class="text-nowrap"> <i class="fas fa-check" aria-hidden="true"> @lang('lang_v1.credit_sale') </i></p>
                    </button>
                @endif
            @endif

            @if (!Gate::check('disable_card') || auth()->user()->can('superadmin') || auth()->user()->can('admin'))
                <button type="button"					
                    class="no-print @if (!empty($pos_settings['disable_suspend']))  @endif pos-express-finalize @if (!array_key_exists('card', $payment_types)) hide @endif 
                        text-sky-700 hover:text-white border border-sky-700 
                        hover:bg-sky-500 focus:ring-4 focus:outline-none focus:ring-sky-300 
                        font-medium text-sm tw-p-2 tw-w-full  {{ $tab_class }}
                        dark:border-sky-500 dark:text-sky-500 dark:hover:text-white dark:hover:bg-sky-600 dark:focus:ring-sky-800"
                        data-pay_method="card" title="@lang('lang_v1.tooltip_express_checkout_card')">
                    <p class="text-nowrap"> <i class="fas fa-credit-card" aria-hidden="true"> @lang('lang_v1.express_checkout_card') </i></p>
                </button>
            @endif
            {{-- recent transactions buttons--}} 
            @if (!isset($pos_settings['hide_recent_trans']) || $pos_settings['hide_recent_trans'] == 0)
                <button type="button"					
                    class="text-white border tw-border-{{ $theme_color }}-700 dark:border-{{ $theme_color }}-500
                    bg-gradient-to-r from-{{ $theme_color }}-500 via-{{ $theme_color }}-600 to-{{ $theme_color }}-700 hover:bg-gradient-to-br 
                    focus:ring-4 focus:outline-none focus:ring-{{ $theme_color }}-300 
                    dark:focus:ring-{{ $theme_color }}-800 shadow-lg shadow-{{ $theme_color }}-500/50 dark:shadow-lg dark:shadow-{{ $theme_color }}-800/80 
                    font-medium text-sm tw-p-2 tw-w-full {{ $tab_class }}"
                        data-toggle="modal" data-target="#recent_transactions_modal" id="recent-transactions" title="@lang('lang_v1.recent_transactions')">
                        <p class="text-nowrap"> <i class="fas fa-clock " aria-hidden="true"> @lang('lang_v1.recent_transactions') </i></p>
                </button>
            @endif	

        </div>
    </div>
@elseif ($poscustom_btnstyle == 's2')
    <!--#JCN Original style for buttons actions (s2 rounded degrade) -->
    <div class="pos-form-actions {{-- tw-bg-white --}} tw-pt-0.5 tw-w-full !tw-items-center tw-justify-between {{-- tw-rounded-tr-xl tw-rounded-tl-xl --}} tw-shadow-[rgba(17,_17,_26,_0.1)_0px_0px_16px] tw-border-t-2 {{ $border_pos_class }}">
        
        {{-- Total Small devices--}}
        <div class="{{-- tw-bg-red-400 --}} hidden md:!tw-w-none !tw-flex md:!tw-hidden !tw-flex-row !tw-items-center !tw-gap-3">
            <div class="tw-pos-total tw-flex tw-items-center tw-gap-3">
                <div class="text-black tw-font-bold text-sm tw-flex tw-items-center tw-flex-col tw-leading-1 ">
                    <div>@lang('sale.total_payable'):</div>
                    {{-- <div>Payable:</div> --}}
                </div>
                <input type="hidden" name="final_total" id="final_total_input" value="0.00" >
                <span id="total_payable" class="text-green-900 tw-font-bold text-sm number" >0.00</span>
            </div>
        </div>

        {{-- Small devices essentials buttons--}} 
        <div class="tw-flex tw-flex-row tw-w-full {{-- tw-gap-1 --}} !tw-items-center tw-justify-between md:!tw-hidden">
        
            @if (!Gate::check('disable_pay_checkout') || auth()->user()->can('superadmin') || auth()->user()->can('admin'))        
                <button type="button" id="pos-finalize" title="@lang('lang_v1.tooltip_checkout_multi_pay')" 
                    class="border tw-border-white
                    text-white bg-gradient-to-r from-blue-500 via-blue-600 to-blue-700 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 shadow-lg shadow-blue-500/50 dark:shadow-lg dark:shadow-blue-800/80 
                    font-medium text-sm tw-p-2 tw-w-full {{-- me-2 mb-2 --}}">
                    <p class="text-nowrap"> <i class="fas fa-money-check-alt" aria-hidden="true"> @lang('lang_v1.checkout_multi_pay') </i></p>
                </button>
            @endif     
            
            @if (!Gate::check('disable_express_checkout') || auth()->user()->can('superadmin') || auth()->user()->can('admin'))
                <button type="button" 
                    class="pos-express-finalize1 
                        @if (!$is_mobile)  @endif no-print @if ($pos_settings['disable_express_checkout'] != 0 || !array_key_exists('cash', $payment_types)) hide @endif 
                        border tw-border-white text-white bg-gradient-to-r from-green-500 via-green-600 to-green-700 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-green-300 dark:focus:ring-green-800 shadow-lg shadow-green-500/50 dark:shadow-lg dark:shadow-green-800/80 
                        font-medium text-sm tw-p-2 tw-w-full {{-- me-2 mb-2 --}}"
                        data-pay_method="cash" title="@lang('tooltip.express_checkout')">
                    <p class="{{-- text-nowrap --}}"> <i class="fas fa-money-bill-alt" aria-hidden="true"> @lang('lang_v1.express_checkout_cash') </i></p>
                </button>
            @endif

            @if (empty($edit))
                    <button type="button"					
                        class="border tw-border-white text-white bg-gradient-to-r from-red-500 via-red-600 to-red-700 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-red-300 dark:focus:ring-red-800 shadow-lg shadow-red-500/50 dark:shadow-lg dark:shadow-red-800/80 
                            font-medium text-sm tw-p-2 tw-w-full {{-- me-2 mb-2 --}}"
                            id="pos-cancel" title="@lang('sale.cancel')">
                        <p class="{{-- text-nowrap --}}"> <i class="fas fa-window-close" aria-hidden="true"> @lang('sale.cancel') </i></p>
                    </button>
            @else
                    <button type="button"					
                        class="border tw-border-white text-white bg-gradient-to-r from-red-500 via-red-600 to-red-700 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-red-300 dark:focus:ring-red-800 shadow-lg shadow-red-500/50 dark:shadow-lg dark:shadow-red-800/80 
                            font-medium text-sm tw-p-2 tw-w-full {{-- me-2 mb-2 --}}"
                            id="pos-delete" @if (!empty($only_payment)) disabled @endif title="@lang('messages.delete')">
                        <p class="text-nowrap"> <i class="fas fa-trash-alt" aria-hidden="true"> @lang('messages.delete') </i></p>
                    </button>
            @endif
        </div >

        {{-- Large devices essentials buttons--}} 
        <div class=" tw-flex tw-flex-row  {{-- tw-gap-4 --}} tw-justify-betwwen tw-justify-center tw-hidden md:tw-flex tw-overflow-x-auto">

            @if (!Gate::check('disable_pay_checkout') || auth()->user()->can('superadmin') || auth()->user()->can('admin'))        
                <button type="button" id="pos-finalize" title="@lang('lang_v1.tooltip_checkout_multi_pay')" 
                    class="border tw-border-white text-white bg-gradient-to-r from-blue-500 via-blue-600 to-blue-700 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 shadow-lg shadow-blue-500/50 dark:shadow-lg dark:shadow-blue-800/80 
                    font-medium text-sm tw-p-2 tw-w-full {{-- me-2 mb-2 --}}">
                    <p class="text-nowrap"> <i class="fas fa-money-check-alt" aria-hidden="true"> @lang('lang_v1.checkout_multi_pay') </i></p>
                </button>
            @endif     
            
            @if (!Gate::check('disable_express_checkout') || auth()->user()->can('superadmin') || auth()->user()->can('admin'))
                <button type="button" 
                    class="pos-express-finalize 
                        @if (!$is_mobile)  @endif no-print @if ($pos_settings['disable_express_checkout'] != 0 || !array_key_exists('cash', $payment_types)) hide @endif 
                        border tw-border-white text-white bg-gradient-to-r from-green-500 via-green-600 to-green-700 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-green-300 dark:focus:ring-green-800 shadow-lg shadow-green-500/50 dark:shadow-lg dark:shadow-green-800/80 font-medium text-sm tw-p-2 tw-w-full {{-- me-2 mb-2 --}}"
                        data-pay_method="cash" title="@lang('tooltip.express_checkout')">
                    <p class="text-nowrap"> <i class="fas fa-money-bill-alt" aria-hidden="true"> @lang('lang_v1.express_checkout_cash') </i></p>
                </button>
            @endif
            
{{--             @if (!Gate::check('disable_express_checkout') || auth()->user()->can('superadmin') || auth()->user()->can('admin'))
                    <button type="button"
                        class="tw-font-bold tw-text-white tw-cursor-pointer tw-text-xs md:tw-text-sm tw-bg-[rgb(40,183,123)] tw-p-2 tw-rounded-md tw-w-[8.5rem] tw-hidden md:tw-flex lg:tw-flex lg:tw-flex-row lg:tw-items-center lg:tw-justify-center lg:tw-gap-1 @if (!$is_mobile)  @endif no-print @if ($pos_settings['disable_express_checkout'] != 0 || !array_key_exists('cash', $payment_types)) hide @endif pos-express-finalize"
                        data-pay_method="cash" title="@lang('tooltip.express_checkout')"> <i class="fas fa-money-bill-alt"
                            aria-hidden="true"></i> @lang('lang_v1.express_checkout_cash')</button>
                @endif --}}
            @if (empty($edit))
                    <button type="button"					
                        class="border tw-border-white text-white bg-gradient-to-r from-red-500 via-red-600 to-red-700 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-red-300 dark:focus:ring-red-800 shadow-lg shadow-red-500/50 dark:shadow-lg dark:shadow-red-800/80 font-medium text-sm tw-p-2 tw-w-full {{-- me-2 mb-2 --}}"
                            id="pos-cancel" title="@lang('sale.cancel')">
                        <p class="text-nowrap"> <i class="fas fa-window-close" aria-hidden="true"> @lang('sale.cancel') </i></p>
                    </button>
            @else
                    <button type="button"					
                        class="border tw-border-white text-white bg-gradient-to-r from-red-500 via-red-600 to-red-700 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-red-300 dark:focus:ring-red-800 shadow-lg shadow-red-500/50 dark:shadow-lg dark:shadow-red-800/80 font-medium text-sm tw-p-2 tw-w-full {{-- me-2 mb-2 --}}"
                            id="pos-delete" @if (!empty($only_payment)) disabled @endif title="@lang('messages.delete')">
                        <p class="text-nowrap"> <i class="fas fa-trash-alt" aria-hidden="true"> @lang('messages.delete') </i></p>
                    </button>
            @endif            

            @if (!Gate::check('disable_draft') || auth()->user()->can('superadmin') || auth()->user()->can('admin'))
                <button type="button" 
                    class="@if ($pos_settings['disable_draft'] != 0) hide @endif 
                        border tw-border-white text-white bg-gradient-to-r from-orange-500 via-orange-600 to-orange-700 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-orange-300 dark:focus:ring-orange-800 shadow-lg shadow-orange-500/50 dark:shadow-lg dark:shadow-orange-800/80 font-medium text-sm tw-p-2 tw-w-full {{-- me-2 mb-2 --}}"
                        id="pos-draft" @if (!empty($only_payment)) disabled @endif> 
                    <p class="text-nowrap"> <i class="fas fa-edit " aria-hidden="true"> @lang('sale.draft') </i></p>
                </button>
            @endif

        </div>
        <div class=" tw-flex tw-flex-row  {{-- tw-gap-4 --}} tw-justify-betwwen tw-justify-center tw-hidden md:tw-flex tw-overflow-x-auto">
            @if (!Gate::check('disable_quotation') || auth()->user()->can('superadmin') || auth()->user()->can('admin'))
                <button type="button"
                    class="@if ($is_mobile) col-xs-6 @endif
                        border tw-border-white text-white bg-gradient-to-r from-amber-500 via-amber-600 to-amber-700 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-amber-300 dark:focus:ring-amber-800 shadow-lg shadow-amber-500/50 dark:shadow-lg dark:shadow-amber-800/80 font-medium text-sm tw-p-2 tw-w-full {{-- me-2 mb-2 --}} "
                    id="pos-quotation" @if (!empty($only_payment)) disabled @endif>
                    <p class="text-nowrap"><i class="fas fa-edit"></i> @lang('lang_v1.quotation')</p>
                    </button>
            @endif	
            @if (!Gate::check('disable_suspend_sale') || auth()->user()->can('superadmin') || auth()->user()->can('admin'))
                @if (empty($pos_settings['disable_suspend']))
                    <button type="button"					
                        class="no-print pos-express-finalize
                            border tw-border-white text-white bg-gradient-to-r from-yellow-500 via-yellow-600 to-yellow-700 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-yellow-300 dark:focus:ring-yellow-800 shadow-lg shadow-yellow-500/50 dark:shadow-lg dark:shadow-yellow-800/80 font-medium text-sm tw-p-2 tw-w-full {{-- me-2 mb-2 --}}"
                                data-pay_method="suspend" title="@lang('lang_v1.tooltip_suspend')"
                            @if (!empty($only_payment)) disabled @endif>
                        <p class="text-nowrap"> <i class="fas fa-pause" aria-hidden="true"> @lang('lang_v1.suspend') </i></p>
                    </button>
                @endif
            @endif

            @if (!Gate::check('disable_credit_sale') || auth()->user()->can('superadmin') || auth()->user()->can('admin'))
                @if (empty($pos_settings['disable_credit_sale_button']))
                    <input type="hidden" name="is_credit_sale" value="0" id="is_credit_sale">					
                    <button type="button"					
                        class="no-print pos-express-finalize @if ($is_mobile) col-xs-6 @endif
                            border tw-border-white text-white bg-gradient-to-r from-teal-500 via-teal-600 to-teal-700 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-teal-300 dark:focus:ring-teal-800 shadow-lg shadow-teal-500/50 dark:shadow-lg dark:shadow-teal-800/80 font-medium text-sm tw-p-2 tw-w-full {{-- me-2 mb-2 --}}"
                            data-pay_method="credit_sale" title="@lang('lang_v1.tooltip_credit_sale')"
                        @if (!empty($only_payment)) disabled @endif>
                        <p class="text-nowrap"> <i class="fas fa-check" aria-hidden="true"> @lang('lang_v1.credit_sale') </i></p>
                    </button>
                @endif
            @endif

            @if (!Gate::check('disable_card') || auth()->user()->can('superadmin') || auth()->user()->can('admin'))
                    <button type="button"					
                        class="no-print @if (!empty($pos_settings['disable_suspend']))  @endif pos-express-finalize @if (!array_key_exists('card', $payment_types)) hide @endif 
                            border tw-border-white text-white bg-gradient-to-r from-sky-500 via-sky-600 to-sky-700 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-sky-300 dark:focus:ring-sky-800 shadow-lg shadow-sky-500/50 dark:shadow-lg dark:shadow-sky-800/80 
                            font-medium text-sm tw-p-2 tw-w-full {{-- me-2 mb-2 --}}"
                            data-pay_method="card" title="@lang('lang_v1.tooltip_express_checkout_card')">
                        <p class="text-nowrap"> <i class="fas fa-credit-card" aria-hidden="true"> @lang('lang_v1.express_checkout_card') </i></p>
                    </button>
            @endif
            {{-- recent transactions buttons--}} 
            @if (!isset($pos_settings['hide_recent_trans']) || $pos_settings['hide_recent_trans'] == 0)
                <button type="button"					
                    class="border tw-border-white text-white
                    bg-gradient-to-r from-{{ $theme_color }}-500 via-{{ $theme_color }}-600 to-{{ $theme_color }}-700 hover:bg-gradient-to-br 
                    focus:ring-4 focus:outline-none focus:ring-{{ $theme_color }}-300 
                    dark:focus:ring-{{ $theme_color }}-800 shadow-lg shadow-{{ $theme_color }}-500/50 dark:shadow-lg dark:shadow-{{ $theme_color }}-800/80 
                    font-medium text-sm tw-p-2 tw-w-full"
                        data-toggle="modal" data-target="#recent_transactions_modal" id="recent-transactions" title="@lang('lang_v1.recent_transactions')">
                        <p class="text-nowrap"> <i class="fas fa-clock " aria-hidden="true"> @lang('lang_v1.recent_transactions') </i></p>
                </button>
            @endif	            		
        </div>
    </div>
@elseif ($poscustom_btnstyle == 's2t')
    <!--#JCN Original style for buttons actions (s2t tab degrade) -->
    <div class="pos-form-actions{{-- tw-bg-white --}} tw-pt-0.5 tw-w-full !tw-items-center tw-justify-between {{-- tw-rounded-tr-xl tw-rounded-tl-xl --}} tw-shadow-[rgba(17,_17,_26,_0.1)_0px_0px_16px] tw-border-t-2 {{ $border_pos_class }}">
        
        {{-- Total Small devices--}}
        <div class="{{-- tw-bg-red-400 --}} hidden md:!tw-w-none !tw-flex md:!tw-hidden !tw-flex-row !tw-items-center !tw-gap-3">
            <div class="tw-pos-total tw-flex tw-items-center tw-gap-3">
                <div class="text-black tw-font-bold text-sm tw-flex tw-items-center tw-flex-col tw-leading-1 ">
                    <div>@lang('sale.total_payable'):</div>
                    {{-- <div>Payable:</div> --}}
                </div>
                <input type="hidden" name="final_total" id="final_total_input" value="0.00" >
                <span id="total_payable" class="text-green-900 tw-font-bold text-sm number" >0.00</span>
            </div>
        </div>

        {{-- Small devices essentials buttons--}} 
        <div class="tw-flex tw-flex-row tw-w-full {{-- tw-gap-1 --}} !tw-items-center tw-justify-between md:!tw-hidden">
        
            @if (!Gate::check('disable_pay_checkout') || auth()->user()->can('superadmin') || auth()->user()->can('admin'))        
                <button type="button" id="pos-finalize" title="@lang('lang_v1.tooltip_checkout_multi_pay')" 
                    class="border tw-border-white
                    text-white bg-gradient-to-r from-blue-500 via-blue-600 to-blue-700 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 shadow-lg shadow-blue-500/50 dark:shadow-lg dark:shadow-blue-800/80 
                    font-medium text-sm tw-p-2 tw-w-full {{ $tab_class }} {{-- me-2 mb-2 --}}">
                    <p class="text-nowrap"> <i class="fas fa-money-check-alt" aria-hidden="true"> @lang('lang_v1.checkout_multi_pay') </i></p>
                </button>
            @endif     
            
            @if (!Gate::check('disable_express_checkout') || auth()->user()->can('superadmin') || auth()->user()->can('admin'))
                <button type="button" 
                    class="pos-express-finalize 
                        @if (!$is_mobile)  @endif no-print @if ($pos_settings['disable_express_checkout'] != 0 || !array_key_exists('cash', $payment_types)) hide @endif 
                        border tw-border-white text-white bg-gradient-to-r from-green-500 via-green-600 to-green-700 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-green-300 dark:focus:ring-green-800 shadow-lg shadow-green-500/50 dark:shadow-lg dark:shadow-green-800/80 
                        font-medium text-sm tw-p-2 tw-w-full {{ $tab_class }} {{-- me-2 mb-2 --}}"
                        data-pay_method="cash" title="@lang('tooltip.express_checkout')">
                    <p class="{{-- text-nowrap --}}"> <i class="fas fa-money-bill-alt" aria-hidden="true"> @lang('lang_v1.express_checkout_cash') </i></p>
                </button>
            @endif

            @if (empty($edit))
                    <button type="button"					
                        class="border tw-border-white text-white bg-gradient-to-r from-red-500 via-red-600 to-red-700 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-red-300 dark:focus:ring-red-800 shadow-lg shadow-red-500/50 dark:shadow-lg dark:shadow-red-800/80 
                            font-medium text-sm tw-p-2 tw-w-full {{ $tab_class }} {{-- me-2 mb-2 --}}"
                            id="pos-cancel" title="@lang('sale.cancel')">
                        <p class="{{-- text-nowrap --}}"> <i class="fas fa-window-close" aria-hidden="true"> @lang('sale.cancel') </i></p>
                    </button>
            @else
                    <button type="button"					
                        class="border tw-border-white text-white bg-gradient-to-r from-red-500 via-red-600 to-red-700 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-red-300 dark:focus:ring-red-800 shadow-lg shadow-red-500/50 dark:shadow-lg dark:shadow-red-800/80 
                            font-medium text-sm tw-p-2 tw-w-full {{ $tab_class }} {{-- me-2 mb-2 --}}"
                            id="pos-delete" @if (!empty($only_payment)) disabled @endif title="@lang('messages.delete')">
                        <p class="text-nowrap"> <i class="fas fa-trash-alt" aria-hidden="true"> @lang('messages.delete') </i></p>
                    </button>
            @endif
        </div >

        {{-- Large devices essentials buttons--}} 
        <div class=" tw-flex tw-flex-row  {{-- tw-gap-4 --}} tw-justify-betwwen tw-justify-center tw-hidden md:tw-flex tw-overflow-x-auto">

            @if (!Gate::check('disable_pay_checkout') || auth()->user()->can('superadmin') || auth()->user()->can('admin'))        
                <button type="button" id="pos-finalize" title="@lang('lang_v1.tooltip_checkout_multi_pay')" 
                    class="border tw-border-white text-white bg-gradient-to-r from-blue-500 via-blue-600 to-blue-700 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 shadow-lg shadow-blue-500/50 dark:shadow-lg dark:shadow-blue-800/80 
                    font-medium text-sm tw-p-2 tw-w-full {{ $tab_class }} {{-- me-2 mb-2 --}}">
                    <p class="text-nowrap"> <i class="fas fa-money-check-alt" aria-hidden="true"> @lang('lang_v1.checkout_multi_pay') </i></p>
                </button>
            @endif     
            
            @if (!Gate::check('disable_express_checkout') || auth()->user()->can('superadmin') || auth()->user()->can('admin'))
                <button type="button" 
                    class="pos-express-finalize 
                        @if (!$is_mobile)  @endif no-print @if ($pos_settings['disable_express_checkout'] != 0 || !array_key_exists('cash', $payment_types)) hide @endif 
                        border tw-border-white text-white bg-gradient-to-r from-green-500 via-green-600 to-green-700 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-green-300 dark:focus:ring-green-800 shadow-lg shadow-green-500/50 dark:shadow-lg dark:shadow-green-800/80 
                        font-medium text-sm tw-p-2 tw-w-full {{ $tab_class }} {{-- me-2 mb-2 --}}"
                        data-pay_method="cash" title="@lang('tooltip.express_checkout')">
                    <p class="text-nowrap"> <i class="fas fa-money-bill-alt" aria-hidden="true"> @lang('lang_v1.express_checkout_cash') </i></p>
                </button>
            @endif
            @if (empty($edit))
                    <button type="button"					
                        class="border tw-border-white text-white bg-gradient-to-r from-red-500 via-red-600 to-red-700 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-red-300 dark:focus:ring-red-800 shadow-lg shadow-red-500/50 dark:shadow-lg dark:shadow-red-800/80 font-medium text-sm tw-p-2 tw-w-full {{ $tab_class }} {{-- me-2 mb-2 --}}"
                            id="pos-cancel" title="@lang('sale.cancel')">
                        <p class="text-nowrap"> <i class="fas fa-window-close" aria-hidden="true"> @lang('sale.cancel') </i></p>
                    </button>
            @else
                    <button type="button"					
                        class="border tw-border-white text-white bg-gradient-to-r from-red-500 via-red-600 to-red-700 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-red-300 dark:focus:ring-red-800 shadow-lg shadow-red-500/50 dark:shadow-lg dark:shadow-red-800/80 font-medium text-sm tw-p-2 tw-w-full {{ $tab_class }} {{-- me-2 mb-2 --}}"
                            id="pos-delete" @if (!empty($only_payment)) disabled @endif title="@lang('messages.delete')">
                        <p class="text-nowrap"> <i class="fas fa-trash-alt" aria-hidden="true"> @lang('messages.delete') </i></p>
                    </button>
            @endif            

            @if (!Gate::check('disable_draft') || auth()->user()->can('superadmin') || auth()->user()->can('admin'))
                <button type="button" 
                    class="@if ($pos_settings['disable_draft'] != 0) hide @endif 
                        border tw-border-white text-white bg-gradient-to-r from-orange-500 via-orange-600 to-orange-700 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-orange-300 dark:focus:ring-orange-800 shadow-lg shadow-orange-500/50 dark:shadow-lg dark:shadow-orange-800/80 font-medium text-sm tw-p-2 tw-w-full {{ $tab_class }} {{-- me-2 mb-2 --}}"
                        id="pos-draft" @if (!empty($only_payment)) disabled @endif> 
                    <p class="text-nowrap"> <i class="fas fa-edit " aria-hidden="true"> @lang('sale.draft') </i></p>
                </button>
            @endif

        </div>
        <div class=" tw-flex tw-flex-row  {{-- tw-gap-4 --}} tw-justify-betwwen tw-justify-center tw-hidden md:tw-flex tw-overflow-x-auto">
            @if (!Gate::check('disable_quotation') || auth()->user()->can('superadmin') || auth()->user()->can('admin'))
                <button type="button"
                    class="@if ($is_mobile) col-xs-6 @endif
                        border tw-border-white text-white bg-gradient-to-r from-amber-500 via-amber-600 to-amber-700 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-amber-300 dark:focus:ring-amber-800 shadow-lg shadow-amber-500/50 dark:shadow-lg dark:shadow-amber-800/80 font-medium text-sm tw-p-2 tw-w-full {{ $tab_class }} {{-- me-2 mb-2 --}} "
                    id="pos-quotation" @if (!empty($only_payment)) disabled @endif>
                    <p class="text-nowrap"><i class="fas fa-edit"></i> @lang('lang_v1.quotation')</p>
                    </button>
            @endif	
            @if (!Gate::check('disable_suspend_sale') || auth()->user()->can('superadmin') || auth()->user()->can('admin'))
                @if (empty($pos_settings['disable_suspend']))
                    <button type="button"					
                        class="no-print pos-express-finalize
                            border tw-border-white text-white bg-gradient-to-r from-yellow-500 via-yellow-600 to-yellow-700 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-yellow-300 dark:focus:ring-yellow-800 shadow-lg shadow-yellow-500/50 dark:shadow-lg dark:shadow-yellow-800/80 font-medium text-sm tw-p-2 tw-w-full {{ $tab_class }} {{-- me-2 mb-2 --}}"
                                data-pay_method="suspend" title="@lang('lang_v1.tooltip_suspend')"
                            @if (!empty($only_payment)) disabled @endif>
                        <p class="text-nowrap"> <i class="fas fa-pause" aria-hidden="true"> @lang('lang_v1.suspend') </i></p>
                    </button>
                @endif
            @endif

            @if (!Gate::check('disable_credit_sale') || auth()->user()->can('superadmin') || auth()->user()->can('admin'))
                @if (empty($pos_settings['disable_credit_sale_button']))
                    <input type="hidden" name="is_credit_sale" value="0" id="is_credit_sale">					
                    <button type="button"					
                        class="no-print pos-express-finalize @if ($is_mobile) col-xs-6 @endif
                            border tw-border-white text-white bg-gradient-to-r from-teal-500 via-teal-600 to-teal-700 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-teal-300 dark:focus:ring-teal-800 shadow-lg shadow-teal-500/50 dark:shadow-lg dark:shadow-teal-800/80 font-medium text-sm tw-p-2 tw-w-full {{ $tab_class }} {{-- me-2 mb-2 --}}"
                            data-pay_method="credit_sale" title="@lang('lang_v1.tooltip_credit_sale')"
                        @if (!empty($only_payment)) disabled @endif>
                        <p class="text-nowrap"> <i class="fas fa-check" aria-hidden="true"> @lang('lang_v1.credit_sale') </i></p>
                    </button>
                @endif
            @endif

            @if (!Gate::check('disable_card') || auth()->user()->can('superadmin') || auth()->user()->can('admin'))
                    <button type="button"					
                        class="no-print @if (!empty($pos_settings['disable_suspend']))  @endif pos-express-finalize @if (!array_key_exists('card', $payment_types)) hide @endif 
                            border tw-border-white text-white bg-gradient-to-r from-sky-500 via-sky-600 to-sky-700 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-sky-300 dark:focus:ring-sky-800 shadow-lg shadow-sky-500/50 dark:shadow-lg dark:shadow-sky-800/80 
                            font-medium text-sm tw-p-2 tw-w-full {{ $tab_class }} {{-- me-2 mb-2 --}}"
                            data-pay_method="card" title="@lang('lang_v1.tooltip_express_checkout_card')">
                        <p class="text-nowrap"> <i class="fas fa-credit-card" aria-hidden="true"> @lang('lang_v1.express_checkout_card') </i></p>
                    </button>
            @endif

            {{-- recent transactions buttons--}} 
            @if (!isset($pos_settings['hide_recent_trans']) || $pos_settings['hide_recent_trans'] == 0)
                <button type="button"					
                    class="border tw-border-white text-white
                    bg-gradient-to-r from-{{ $theme_color }}-500 via-{{ $theme_color }}-600 to-{{ $theme_color }}-700 hover:bg-gradient-to-br 
                    focus:ring-4 focus:outline-none focus:ring-{{ $theme_color }}-300 
                    dark:focus:ring-{{ $theme_color }}-800 shadow-lg shadow-{{ $theme_color }}-500/50 dark:shadow-lg dark:shadow-{{ $theme_color }}-800/80 
                    font-medium text-sm tw-p-2 tw-w-full {{ $tab_class }}"
                        data-toggle="modal" data-target="#recent_transactions_modal" id="recent-transactions" title="@lang('lang_v1.recent_transactions')">
                        <p class="text-nowrap"> <i class="fas fa-clock " aria-hidden="true"> @lang('lang_v1.recent_transactions') </i></p>
                </button>
            @endif	            		

        </div>
    </div>
@else
<!--#JCN Original style for buttons actions (s0) -->
<div class="row">
    <div
        class=" pos-form-actions {{-- tw-fixed --}} tw-right-0 tw-bottom-0 tw-p-1 tw-w-full  tw-bg-white tw-cursor-pointer
        tw-rounded-tr-xl tw-rounded-tl-xl tw-shadow-[rgba(17,_17,_26,_0.1)_0px_0px_16px] tw-border-t-2 {{ $border_pos_class }}">
        <div
            class="tw-flex tw-items-center tw-justify-between tw-flex-col sm:tw-flex-row md:tw-flex-row lg:tw-flex-row xl:tw-flex-row tw-gap-2 tw-px-5 tw-py-0 tw-overflow-x-auto tw-w-full">

            <div class="md:!tw-w-none !tw-flex md:!tw-hidden !tw-flex-row !tw-items-center !tw-gap-3">
                <div class="tw-pos-total tw-flex tw-items-center tw-gap-3">
                    <div class="tw-text-black tw-font-bold tw-text-sm tw-flex tw-items-center tw-flex-col tw-leading-1">
                        <div>@lang('sale.total_payable'):</div>
                        {{-- <div>Payable:</div> --}}
                    </div>
                    <input type="hidden" name="final_total" id="final_total_input" value="0.00">
                    <span id="total_payable" class="tw-text-green-900 tw-font-bold tw-text-sm number">0.00</span>
                </div>
            </div>

            <div class="!tw-w-full md:!tw-w-none !tw-flex md:!tw-hidden !tw-flex-row !tw-items-center !tw-gap-3">
                @if (!Gate::check('disable_pay_checkout') || auth()->user()->can('superadmin') || auth()->user()->can('admin'))
                    <button type="button"
                        class=" tw-flex tw-flex-row tw-items-center tw-justify-center tw-gap-1 tw-font-bold tw-text-white tw-cursor-pointer 
                        tw-text-xs md:tw-text-sm tw-bg-[#001F3E] tw-rounded-md tw-p-2 tw-w-[8.5rem] @if (!$is_mobile)  @endif no-print @if ($pos_settings['disable_pay_checkout'] != 0) hide @endif"
                        id="pos-finalize" title="@lang('lang_v1.tooltip_checkout_multi_pay')"><i class="fas fa-money-check-alt"
                            aria-hidden="true"></i> @lang('lang_v1.checkout_multi_pay') </button>
                @endif

                @if (!Gate::check('disable_express_checkout') || auth()->user()->can('superadmin') || auth()->user()->can('admin'))
                    <button type="button"
                        class="tw-font-bold tw-text-white tw-cursor-pointer tw-text-xs md:tw-text-sm tw-bg-[rgb(40,183,123)] tw-p-2 tw-rounded-md tw-w-[5.5rem] tw-flex tw-flex-row tw-items-center tw-justify-center tw-gap-1 @if (!$is_mobile)  @endif no-print @if ($pos_settings['disable_express_checkout'] != 0 || !array_key_exists('cash', $payment_types)) hide @endif pos-express-finalize @if ($is_mobile) col-xs-6 @endif"
                        data-pay_method="cash" title="@lang('tooltip.express_checkout')"> <i class="fas fa-money-bill-alt"
                            aria-hidden="true"></i> @lang('lang_v1.express_checkout_cash')</button>
                @endif
                @if (empty($edit))
                    <button type="button" class="tw-font-bold tw-text-white tw-cursor-pointer tw-text-xs md:tw-text-sm tw-bg-red-600 tw-p-2 tw-rounded-md tw-w-[5.5rem] tw-flex tw-flex-row tw-items-center tw-justify-center tw-gap-1" id="pos-cancel"> <i
                            class="fas fa-window-close"></i> @lang('sale.cancel')</button>
                @else
                    <button type="button" class="btn-danger tw-dw-btn hide tw-dw-btn-xs" id="pos-delete"
                        @if (!empty($only_payment)) disabled @endif> <i class="fas fa-trash-alt"></i>
                        @lang('messages.delete')</button>
                @endif
            </div>
            <div class="tw-flex tw-items-center tw-gap-4 tw-flex-row tw-overflow-x-auto">

                @if (!Gate::check('disable_draft') || auth()->user()->can('superadmin') || auth()->user()->can('admin'))
                    <button type="button"
                        class="tw-font-bold tw-text-gray-700 tw-text-xs md:tw-text-sm tw-flex tw-flex-col tw-items-center tw-justify-center tw-gap-1 @if ($pos_settings['disable_draft'] != 0) hide @endif"
                        id="pos-draft" @if (!empty($only_payment)) disabled @endif><i
                            class="fas fa-edit tw-text-[#009ce4]"></i> @lang('sale.draft')</button>
                @endif

                @if (!Gate::check('disable_quotation') || auth()->user()->can('superadmin') || auth()->user()->can('admin'))
                    <button type="button"
                        class="tw-font-bold tw-text-gray-700 tw-cursor-pointer tw-text-xs md:tw-text-sm tw-flex tw-flex-col tw-items-center tw-justify-center tw-gap-1 @if ($is_mobile) col-xs-6 @endif"
                        id="pos-quotation" @if (!empty($only_payment)) disabled @endif><i
                            class="fas fa-edit tw-text-[#E7A500]"></i> @lang('lang_v1.quotation')</button>
                @endif

                @if (!Gate::check('disable_suspend_sale') || auth()->user()->can('superadmin') || auth()->user()->can('admin'))
                    @if (empty($pos_settings['disable_suspend']))
                        <button type="button"
                            class="tw-font-bold tw-text-gray-700 tw-cursor-pointer tw-text-xs md:tw-text-sm tw-flex tw-flex-col tw-items-center tw-justify-center tw-gap-1  no-print pos-express-finalize"
                            data-pay_method="suspend" title="@lang('lang_v1.tooltip_suspend')"
                            @if (!empty($only_payment)) disabled @endif>
                            <i class="fas fa-pause tw-text-[#EF4B51]" aria-hidden="true"></i>
                            @lang('lang_v1.suspend')
                        </button>
                    @endif
                @endif

                @if (!Gate::check('disable_credit_sale') || auth()->user()->can('superadmin') || auth()->user()->can('admin'))
                    @if (empty($pos_settings['disable_credit_sale_button']))
                        <input type="hidden" name="is_credit_sale" value="0" id="is_credit_sale">
                        <button type="button"
                            class=" tw-font-bold tw-text-gray-700 tw-cursor-pointer tw-text-xs md:tw-text-sm tw-flex tw-flex-col tw-items-center tw-justify-center tw-gap-1 no-print pos-express-finalize @if ($is_mobile) col-xs-6 @endif"
                            data-pay_method="credit_sale" title="@lang('lang_v1.tooltip_credit_sale')"
                            @if (!empty($only_payment)) disabled @endif>
                            <i class="fas fa-check tw-text-[#5E5CA8]" aria-hidden="true"></i> @lang('lang_v1.credit_sale')
                        </button>
                    @endif
                @endif
                @if (!Gate::check('disable_card') || auth()->user()->can('superadmin') || auth()->user()->can('admin'))
                    <button type="button"
                        class="tw-font-bold tw-text-gray-700 tw-cursor-pointer tw-text-xs md:tw-text-sm tw-flex tw-flex-col tw-items-center tw-justify-center tw-gap-1  no-print @if (!empty($pos_settings['disable_suspend']))  @endif pos-express-finalize @if (!array_key_exists('card', $payment_types)) hide @endif "
                        data-pay_method="card" title="@lang('lang_v1.tooltip_express_checkout_card')">
                        <i class="fas fa-credit-card tw-text-[#D61B60]" aria-hidden="true"></i> @lang('lang_v1.express_checkout_card')
                    </button>
                @endif

                @if (!Gate::check('disable_pay_checkout') || auth()->user()->can('superadmin') || auth()->user()->can('admin'))
                    <button type="button"
                        class="tw-hidden md:tw-flex md:tw-flex-row md:tw-items-center md:tw-justify-center md:tw-gap-1 tw-font-bold tw-text-white tw-cursor-pointer tw-text-xs md:tw-text-sm tw-bg-[#001F3E] tw-rounded-md tw-p-2 tw-w-[8.5rem] @if (!$is_mobile)  @endif no-print @if ($pos_settings['disable_pay_checkout'] != 0) hide @endif"
                        id="pos-finalize" title="@lang('lang_v1.tooltip_checkout_multi_pay')"><i class="fas fa-money-check-alt"
                            aria-hidden="true"></i> @lang('lang_v1.checkout_multi_pay') </button>
                @endif

                @if (!Gate::check('disable_express_checkout') || auth()->user()->can('superadmin') || auth()->user()->can('admin'))
                    <button type="button"
                        class="tw-font-bold tw-text-white tw-cursor-pointer tw-text-xs md:tw-text-sm tw-bg-[rgb(40,183,123)] tw-p-2 tw-rounded-md tw-w-[8.5rem] tw-hidden md:tw-flex lg:tw-flex lg:tw-flex-row lg:tw-items-center lg:tw-justify-center lg:tw-gap-1 @if (!$is_mobile)  @endif no-print @if ($pos_settings['disable_express_checkout'] != 0 || !array_key_exists('cash', $payment_types)) hide @endif pos-express-finalize"
                        data-pay_method="cash" title="@lang('tooltip.express_checkout')"> <i class="fas fa-money-bill-alt"
                            aria-hidden="true"></i> @lang('lang_v1.express_checkout_cash')</button>
                @endif


                @if (empty($edit))
                    <button type="button"
                        class="tw-font-bold tw-text-white tw-cursor-pointer tw-text-xs md:tw-text-sm tw-bg-red-600 tw-p-2 tw-rounded-md tw-w-[8.5rem] tw-hidden md:tw-flex lg:tw-flex lg:tw-flex-row lg:tw-items-center lg:tw-justify-center lg:tw-gap-1"
                        id="pos-cancel"> <i class="fas fa-window-close"></i> @lang('sale.cancel')</button>
                @else
                    <button type="button"
                        class="tw-font-bold tw-text-white tw-cursor-pointer tw-text-xs md:tw-text-sm tw-bg-red-600 tw-p-2 tw-rounded-md tw-w-[8.5rem] tw-hidden md:tw-flex lg:tw-flex lg:tw-flex-row lg:tw-items-center lg:tw-justify-center lg:tw-gap-1 hide"
                        id="pos-delete" @if (!empty($only_payment)) disabled @endif> <i
                            class="fas fa-trash-alt"></i> @lang('messages.delete')</button>
                @endif

                @if (!$is_mobile)
                    <div class="pos-total tw-flex md:tw-flex md:tw-items-center md:tw-gap-3 tw-hidden">
                        <div
                            class="tw-text-black tw-font-bold tw-text-base md:tw-text-2xl tw-flex tw-items-center {{-- tw-flex-col --}}">
                            <div>@lang('sale.total')</div>
                            <div>@lang('lang_v1.payable'):</div>
                        </div>
                        <input type="hidden" name="final_total" id="final_total_input" value="0.00">
                        <span id="total_payable"
                            class="tw-text-green-900 tw-font-bold tw-text-base md:tw-text-2xl number">0.00</span>
                    </div>
                @endif
                

            </div>

            <div class="tw-w-full md:tw-w-fit tw-flex tw-flex-col tw-items-end {{-- tw-gap-3 --}} tw-hidden md:tw-block">
                @if (!isset($pos_settings['hide_recent_trans']) || $pos_settings['hide_recent_trans'] == 0)
                    <button type="button"
                        class="tw-font-bold tw-bg-[#646EE4] hover:tw-bg-[#414aac] tw-rounded-full tw-text-white tw-w-full md:tw-w-fit 
                        tw-px-5 py-2 {{-- tw-h-11 --}} tw-cursor-pointer tw-text-xs md:tw-text-sm"
                        data-toggle="modal" data-target="#recent_transactions_modal" id="recent-transactions"> <i
                            class="fas fa-clock"></i> @lang('lang_v1.recent_transactions')</button>
                @endif
            </div>
        </div>
    </div>
</div>
@endif

@if (isset($transaction))
    @include('sale_pos.partials.edit_discount_modal', [
        'sales_discount' => $transaction->discount_amount,
        'discount_type' => $transaction->discount_type,
        'rp_redeemed' => $transaction->rp_redeemed,
        'rp_redeemed_amount' => $transaction->rp_redeemed_amount,
        'max_available' => !empty($redeem_details['points']) ? $redeem_details['points'] : 0,
    ])
@else
    @include('sale_pos.partials.edit_discount_modal', [
        'sales_discount' => $business_details->default_sales_discount,
        'discount_type' => 'percentage',
        'rp_redeemed' => 0,
        'rp_redeemed_amount' => 0,
        'max_available' => 0,
    ])
@endif

@if (isset($transaction))
    @include('sale_pos.partials.edit_order_tax_modal', ['selected_tax' => $transaction->tax_id])
@else
    @include('sale_pos.partials.edit_order_tax_modal', [
        'selected_tax' => $business_details->default_sales_tax,
    ])
@endif

@include('sale_pos.partials.edit_shipping_modal') 
