<!-- default value -->
@php
    $go_back_url = action([\App\Http\Controllers\SellPosController::class, 'index']);
    $transaction_sub_type = '';
    //#JCN to redirect in the module
    $view_suspended_sell_url = action([\Modules\PosCustom\Http\Controllers\SellController::class, 'index']).'?suspended=1';
    //#JCN to redirect in the module
    $pos_redirect_url = action([\Modules\PosCustom\Http\Controllers\SellPosController::class, 'create']); 
    $theme_divider_class = 'theme_' . $business_details->theme_color . '_divider';
    $theme_pos_class = 'tw-bg-gradient-to-r tw-from-' . $business_details->theme_color . '-800'
    .' tw-to-'.$business_details->theme_color.'-500';
@endphp

@if (!empty($pos_module_data))
    @foreach ($pos_module_data as $key => $value)
        @php
            if (!empty($value['go_back_url'])) {
                $go_back_url = $value['go_back_url'];
            }

            if (!empty($value['transaction_sub_type'])) {
                $transaction_sub_type = $value['transaction_sub_type'];
                $view_suspended_sell_url .= '&transaction_sub_type=' . $transaction_sub_type;
                $pos_redirect_url .= '?sub_type=' . $transaction_sub_type;
            }
        @endphp
    @endforeach 
@endif
<input type="hidden" name="transaction_sub_type" id="transaction_sub_type" value="{{ $transaction_sub_type }}">
@inject('request', 'Illuminate\Http\Request')
<div class="{{-- col-md-12 no-print pos-header --}} tw-right-0 tw-top-0  tw-w-full">
    <input type="hidden" id="pos_redirect_url" value="{{ $pos_redirect_url }}">
    <div
        class="{{$theme_pos_class}} tw-flex tw-flex-col md:tw-flex-row tw-w-full tw-items-center tw-justify-between tw-shadow-[rgba(17,_17,_26,_0.1)_0px_0px_16px] tw-bg-white tw-rounded-xl  {{-- tw-mx-0 tw-mt-1  tw-mb-0 md:tw-mb-0 tw-p-2--}} ">
        <div class="tw-w-full md:tw-w-1/3">
            <div class="tw-flex tw-items-center tw-gap-2">
                <p style="display: flex; color: #fff" ><strong> &nbsp; @lang('sale.location'): &nbsp;</strong></p>
                <div style="width: 40%">
                    @if (empty($transaction->location_id))
                        @if (count($business_locations) > 1)
                            {!! Form::select(
                                'select_location_id',
                                $business_locations,
                                $default_location->id ?? null,
                                ['class' => 'form-control input-sm', 'id' => 'select_location_id', 'required', 'autofocus'],
                                $bl_attributes,
                            ) !!}
                        @else
                            <p style="color: #fff" ><strong>{{ $default_location->name }}</strong></p>
                        @endif
                    @endif
                    {{--#JCN to show in edit mode --}}
                    @if (!empty($transaction->location_id))
                        <div class="m-6 mt-5" style="display: flex; color: #fff">
                        {{ $transaction->location->name }}
                        </div>
                    @endif
                </div>
                <div
                    class="tw-hidden md:tw-block {{$theme_pos_class}} hover:tw-to-blue-600 tw-py-1.5 tw-px-2 tw-rounded-md">
            {{-- #JCN Put above  @if (!empty($transaction->location_id))
                        {{ $transaction->location->name }}
                    @endif &nbsp;  --}}<span
                        class="curr_datetime text-white tw-font-semibold">{{ @format_datetime('now') }}</span>
                    <i class="fa fa-keyboard hover-q text-white" aria-hidden="true" data-container="body"
                        data-toggle="popover" data-placement="bottom" data-content="@include('sale_pos.partials.keyboard_shortcuts_details')"
                        data-html="true" data-trigger="hover" data-original-title="" title=""></i>
                </div>

                @if (empty($pos_settings['hide_product_suggestion']))
                    <button type="button" title="{{ __('lang_v1.view_products') }}" data-placement="bottom"
                        class="tw-shadow-[rgba(17,_17,_26,_0.1)_0px_0px_16px] tw-bg-white hover:tw-bg-white/60 tw-cursor-pointer tw-border-2 tw-flex tw-items-center tw-justify-center tw-rounded-md tw-w-8 tw-h-8 tw-text-gray-600 btn-modal pull-right tw-block md:tw-hidden"
                        data-toggle="modal" data-target="#mobile_product_suggestion_modal">
                        <strong><i class="fa fa-cubes fa-lg tw-text-[#00935F] !tw-text-sm"></i></strong>
                    </button>
                @endif

                <span class="tw-block md:tw-hidden">
                    <i class="fas hamburger fa-bars tw-mx-5"
                        onclick="document.getElementById('pos_header_more_options').classList.toggle('tw-hidden')"></i>
                </span>

            </div>
        </div>

        <div class="tw-w-full md:tw-w-2/3 !tw-p-0 tw-flex tw-items-center tw-justify-between tw-gap-4 tw-flex-col md:tw-flex-row tw-hidden md:tw-flex"
            id="pos_header_more_options">
            <a href="{{ $go_back_url }}" title="{{ __('lang_v1.go_back') }}"
                class="tw-shadow-[rgba(17,_17,_26,_0.1)_0px_0px_16px] tw-bg-white hover:tw-bg-white/60 tw-cursor-pointer tw-border-2 tw-flex tw-items-center tw-justify-center tw-rounded-md md:tw-w-8 tw-w-auto tw-h-8 tw-text-gray-600 pull-right">
                <strong class="!tw-m-3">
                    <i class="fa fa-backward fa-lg fa fa-backward tw-fa-lg tw-text-[#009EE4] !tw-text-sm"></i>
                    <span class="tw-inline md:tw-hidden">{{ __('lang_v1.go_back') }}</span>
                </strong>
            </a>

            {{-- <a href="{{ $go_back_url }}" title="{{ __('lang_v1.go_back') }}"
              class="md:tw-hidden tw-shadow-[rgba(17,_17,_26,_0.1)_0px_0px_16px] tw-bg-white hover:tw-bg-white/60 tw-cursor-pointer tw-border-2 tw-flex tw-items-center tw-justify-center tw-rounded-md md:tw-w-8 tw-w-auto tw-h-8 tw-text-gray-600 pull-right">
            <strong class="!tw-m-3">
                <i class="fa fa-backward fa-lg fa fa-backward tw-fa-lg tw-text-[#009EE4] !tw-text-sm"></i>
                <span class="tw-inline md:tw-hidden">{{ __('lang_v1.go_back') }}</span>
            </strong>
          </a> --}}

            @if (!isset($pos_settings['hide_recent_trans']) || $pos_settings['hide_recent_trans'] == 0)
                <button type="button"
                    class="md:tw-hidden tw-shadow-[rgba(17,_17,_26,_0.1)_0px_0px_16px] tw-bg-white hover:tw-bg-white/60 tw-cursor-pointer tw-border-2 tw-flex tw-items-center tw-justify-center tw-rounded-md md:tw-w-8 tw-w-auto tw-h-8 tw-text-gray-600 pull-right"
                    data-toggle="modal" data-target="#recent_transactions_modal" id="recent-transactions">
                        <strong class="!tw-m-3">
                            <i class="fa fa-clock fa-lg tw-text-[#646EE4] !tw-text-sm"></i>
                            <span class="tw-inline md:tw-hidden">{{ __('lang_v1.recent_transactions') }}</span>
                        </strong>
                </button>
            @endif

            @if (!empty($pos_settings['inline_service_staff']))
                <button type="button" id="show_service_staff_availability"
                    title="{{ __('lang_v1.service_staff_availability') }}"
                    class="tw-shadow-[rgba(17,_17,_26,_0.1)_0px_0px_16px] tw-bg-white hover:tw-bg-white/60 tw-cursor-pointer tw-border-2 tw-flex tw-items-center tw-justify-center tw-rounded-md md:tw-w-8 tw-w-auto tw-h-8 tw-text-gray-600 pull-right"
                    data-container=".view_modal"
                    data-href="{{ action([\App\Http\Controllers\SellPosController::class, 'showServiceStaffAvailibility']) }}">
                    <strong class="!tw-m-3">
                        <i class="fa fa-users fa-lg tw-text-[#646EE4] !tw-text-sm"></i>
                        <span class="tw-inline md:tw-hidden">{{ __('lang_v1.service_staff_availability') }}</span>
                    </strong>
                </button>
            @endif

            @can('close_cash_register')
                <button type="button" id="close_register" title="{{ __('cash_register.close_register') }}"
                    class="tw-shadow-[rgba(17,_17,_26,_0.1)_0px_0px_16px] tw-bg-white hover:tw-bg-white/60 tw-cursor-pointer tw-border-2 tw-flex tw-items-center tw-justify-center tw-rounded-md md:tw-w-8 tw-w-auto tw-h-8 tw-text-gray-600 btn-modal pull-right"
                    data-container=".close_register_modal"
                    data-href="{{ action([\App\Http\Controllers\CashRegisterController::class, 'getCloseRegister']) }}">
                    <strong class="!tw-m-3">
                        <i class="fa fa-window-close fa-lg tw-text-[#EF4B53] !tw-text-sm"></i>
                        <span class="tw-inline md:tw-hidden">{{ __('cash_register.close_register') }}</span>
                    </strong>
                </button>
            @endcan

            @if (
                !empty($pos_settings['inline_service_staff']) ||
                    (in_array('tables', $enabled_modules) || in_array('service_staff', $enabled_modules)))
                <button type="button"
                    class="tw-shadow-[rgba(17,_17,_26,_0.1)_0px_0px_16px] tw-bg-white hover:tw-bg-white/60 tw-cursor-pointer tw-border-2 tw-flex tw-items-center tw-justify-center tw-rounded-md md:tw-w-8 tw-w-auto tw-h-8 tw-text-gray-600 pull-right popover-default"
                    id="service_staff_replacement" title="{{ __('restaurant.service_staff_replacement') }}"
                    data-toggle="popover" data-trigger="click"
                    data-content='<div class="m-8"><input type="text" class="form-control" placeholder="@lang('sale.invoice_no')" id="send_for_sell_service_staff_invoice_no"></div><div class="w-100 text-center"><button type="button" class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline tw-dw-btn-error" id="send_for_sercice_staff_replacement">@lang('lang_v1.send')</button></div>'
                    data-html="true" data-placement="bottom">

                    <strong class="!tw-m-3">
                        <i class="fa fa-user-plus fa-lg tw-text-[#646EE4] !tw-text-sm"></i>
                        <span class="tw-inline md:tw-hidden">{{ __('restaurant.service_staff_replacement') }}</span>
                    </strong>
                </button>
            @endif

            @can('view_cash_register')
                <button type="button" id="register_details" title="{{ __('cash_register.register_details') }}"
                    class="tw-shadow-[rgba(17,_17,_26,_0.1)_0px_0px_16px] tw-bg-white hover:tw-bg-white/60 tw-cursor-pointer tw-border-2 tw-flex tw-items-center tw-justify-center tw-rounded-md md:tw-w-8 tw-w-auto tw-h-8 tw-text-gray-600 btn-modal pull-right"
                    data-container=".register_details_modal"
                    data-href="{{ action([\App\Http\Controllers\CashRegisterController::class, 'getRegisterDetails']) }}">

                    <strong class="!tw-m-3">
                        <i class="fa fa-briefcase tw-fa-lg tw-text-[#00935F] !tw-text-sm" aria-hidden="true"></i>
                        <span class="tw-inline md:tw-hidden">{{ __('cash_register.register_details') }}</span>
                    </strong>
                </button>
            @endcan

            <button title="@lang('lang_v1.calculator')" id="btnCalculator" type="button"
                class="tw-shadow-[rgba(17,_17,_26,_0.1)_0px_0px_16px] tw-bg-white hover:tw-bg-white/60 tw-cursor-pointer tw-border-2 tw-flex tw-items-center tw-justify-center tw-rounded-md md:tw-w-8 tw-w-auto tw-h-8 tw-text-gray-600 pull-right popover-default"
                data-toggle="popover" data-trigger="click" data-content='@include('layouts.partials.calculator')' data-html="true"
                data-placement="bottom">


                <strong class="!tw-m-3">
                    <i class="fa fa-calculator fa-lg tw-text-[#00935F] !tw-text-sm" aria-hidden="true"></i>
                    <span class="tw-inline md:tw-hidden">{{ __('lang_v1.calculator') }}</span>
                </strong>
            </button>

            <button type="button"
                class="tw-shadow-[rgba(17,_17,_26,_0.1)_0px_0px_16px] tw-bg-white hover:tw-bg-white/60 tw-cursor-pointer tw-border-2 tw-flex tw-items-center tw-justify-center tw-rounded-md md:tw-w-8 tw-w-auto tw-h-8 tw-text-gray-600 pull-right pull-right popover-default"
                id="return_sale" title="@lang('lang_v1.sell_return')" data-toggle="popover" data-trigger="click"
                data-content='<div class="m-8"><input type="text" class="form-control" placeholder="@lang('sale.invoice_no')" id="send_for_sell_return_invoice_no"></div><div class="w-100 text-center"><button type="button" class="tw-dw-btn tw-dw-btn-error tw-text-white tw-dw-btn-sm" id="send_for_sell_return">@lang('lang_v1.send')</button></div>'
                data-html="true" data-placement="bottom">
                <strong class="!tw-m-3">
                    <i class="fas fa-undo fa-lg tw-text-[#EF4B53] !tw-text-sm"></i>
                    <span class="tw-inline md:tw-hidden">{{ __('lang_v1.sell_return') }}</span>
                </strong>
            </button>


            <button type="button" title="{{ __('lang_v1.full_screen') }}"
                class="tw-shadow-[rgba(17,_17,_26,_0.1)_0px_0px_16px] tw-bg-white hover:tw-bg-white/60 tw-cursor-pointer tw-border-2 tw-flex tw-items-center tw-justify-center tw-rounded-md md:tw-w-8 tw-w-auto tw-h-8 tw-text-gray-600 pull-right"
                id="full_screen">
                <strong class="!tw-m-3">
                    <i class="fa fa-window-maximize fa-lg tw-text-[#646EE4] !tw-text-sm"></i>
                    <span class="tw-inline md:tw-hidden">Full Screen</span>
                </strong>
            </button>

            <button type="button" id="view_suspended_sales" title="{{ __('lang_v1.view_suspended_sales') }}"
                class="tw-shadow-[rgba(17,_17,_26,_0.1)_0px_0px_16px] tw-bg-white hover:tw-bg-white/60 tw-cursor-pointer tw-border-2 tw-flex tw-items-center tw-justify-center tw-rounded-md md:tw-w-8 tw-w-auto tw-h-8 tw-text-gray-600 btn-modal pull-right"
                data-container=".view_modal" data-href="{{ $view_suspended_sell_url }}" >
                <strong class="!tw-m-3">
                    <i class="fa fa-pause-circle fa-lg {{-- tw-text-[#A5ADBB] --}} !tw-text-sm" style="color: #ffa500"></i>
                    <span class="tw-inline md:tw-hidden">{{ __('lang_v1.view_suspended_sales') }}</span>
                </strong>
            </button>
            @if (!empty($pos_settings['customer_display_screen']))
                <a href="{{route('pos_display')}}" id="customer_display_screen"  onclick="window.open(this.href, 'customer_display', 'width='+screen.width+',height='+screen.height+',top=0,left=0'); return false;"   title="{{ __('lang_v1.customer_display_screen') }}"
                    class="tw-shadow-[rgba(17,_17,_26,_0.1)_0px_0px_16px] tw-bg-white hover:tw-bg-white/60 tw-cursor-pointer tw-border-2 tw-flex tw-items-center tw-justify-center tw-rounded-md md:tw-w-8 tw-w-auto tw-h-8 tw-text-gray-600 pull-right">
                    <strong class="!tw-m-3">
                        <i class="fa fa-tv fa-lg tw-text-[#646EE4] !tw-text-sm"></i>
                        <span class="tw-inline md:tw-hidden">{{ __('lang_v1.customer_display_screen') }}</span>
                    </strong>
                </a>
            @endif
            
            @if (!empty($pos_settings['poscustom_secondscreen_show']))  
            <a href="{{ route('posDisplay_pc') }}" {{-- target="_blank" --}} onclick="window.open(this.href, 'customer_display', 'width='+screen.width+',height='+screen.height+',top=0,left=0'); return false;" >
                <button type="button"  title="{{ __('poscustom::lang.pos_display') }}" id="btnCDisplay"  
                class="tw-shadow-[rgba(17,_17,_26,_0.1)_0px_0px_16px] tw-bg-white hover:tw-bg-white/60 tw-cursor-pointer tw-border-2 tw-flex tw-items-center tw-justify-center tw-rounded-md md:tw-w-8 tw-w-auto tw-h-8 tw-text-gray-600 pull-right popover-default"
                {{-- onclick="window.location='{{ route('pos_display') }}','_blank'" --}}
                data-placement="bottom">
                    {{--
                    <strong class="!tw-m-3">
                        <i class="fa fa-desktop tw-fa-lg tw-text-[#646EE4] !tw-text-sm" aria-hidden="true"></i>
                        <span class="tw-inline md:tw-hidden">{{ __('poscustom::lang.pos_display') }}</span>
                    </strong>
                    --}}
                    <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" 
                        width="24" height="24"  viewBox="0 0 122.88 94.35" >
                        <path class="st0" d="M90.37,26.48h25.48c1.94,0,3.71,0.79,4.97,2.06c1.28,1.28,2.06,3.04,2.06,4.97v53.82 c0,1.94-0.79,3.71-2.06,4.97c-1.28,1.28-3.04,2.06-4.97,2.06H90.37c-1.94,0-3.71-0.79-4.97-2.06c-1.28-1.28-2.06-3.04-2.06-4.97 V33.5c0-1.94,0.79-3.71,2.06-4.97C86.68,27.25,88.43,26.48,90.37,26.48L90.37,26.48z M3.05,0h106.12c1.68,0,3.05,1.37,3.05,3.05 v18.44h-6.48V8.44c0-1.48-1.21-2.7-2.7-2.7H9.17v0c-1.48,0-2.7,1.21-2.7,2.7v52.53c0,1.48,1.21,2.7,2.7,2.7H76.7V76.4H3.05 C1.37,76.4,0,75.03,0,73.35V3.05C0,1.37,1.37,0,3.05,0L3.05,0L3.05,0z M42.27,80.61h27.67c0.07,4.79,2.04,9.07,7.39,12.45H34.89 C39.16,89.96,42.29,86.19,42.27,80.61L42.27,80.61L42.27,80.61z M56.11,66.12c2.16,0,3.92,1.75,3.92,3.92 c0,2.16-1.76,3.92-3.92,3.92c-2.16,0-3.92-1.75-3.92-3.92C52.19,67.88,53.94,66.12,56.11,66.12L56.11,66.12z M103.1,85.72 c1.59,0,2.89,1.28,2.89,2.89c0,1.59-1.28,2.89-2.89,2.89c-1.59,0-2.89-1.28-2.89-2.89C100.21,87.02,101.49,85.72,103.1,85.72 L103.1,85.72z M86.3,83.52h33.61V37.37H86.3V83.52L86.3,83.52z"/>
                    </svg>
                </button> 
            </a>
            @endif


            @if (Module::has('Repair') && $transaction_sub_type != 'repair')
                @include('repair::layouts.partials.pos_header')
            @endif

            @if (in_array('pos_sale', $enabled_modules) && !empty($transaction_sub_type))
                @can('sell.create')
                    <a href="{{ action([\App\Http\Controllers\SellPosController::class, 'create']) }}"
                        title="@lang('sale.pos_sale')"
                        class="tw-shadow-[rgba(17,_17,_26,_0.1)_0px_0px_16px] tw-bg-white hover:tw-bg-white/60 tw-cursor-pointer tw-border-2 tw-w-auto tw-h-auto tw-py-1 tw-px-4 tw-rounded-md pull-right">
                        <strong><i class="fa fa-th-large tw-text-[#00935F] !tw-text-sm"></i> &nbsp;
                            @lang('sale.pos_sale')</strong>
                    </a>
                @endcan
            @endif
            @can('expense.add')
                <button type="button" title="{{ __('expense.add_expense') }}" data-placement="bottom"
                    class="tw-shadow-[rgba(17,_17,_26,_0.1)_0px_0px_16px] tw-bg-white hover:tw-bg-white/60 tw-cursor-pointer tw-border-2 tw-w-auto tw-h-auto tw-py-1 tw-px-4 tw-rounded-md btn-modal pull-right"
                    id="add_expense">
                    <strong><i class="fa fas fa-minus-circle"></i> @lang('expense.add_expense')</strong>
                </button>
            @endcan

        <!-- To show the name of the cashier -->
        <div class="tw-p-2 mt-5" style="display: flex; color: #fff">
            <!-- The user image in the navbar-->
            @php
              $profile_photo = auth()->user()->media;
              /* var_dump($profile_photo); */
            @endphp
            @if(!empty($profile_photo))
                @if(file_exists(public_path('uploads/media/'.$profile_photo->file_name)))
                    <img src="{{$profile_photo->display_url}}" width="25px" height="25px" alt="User Image">
                @endif
            @endif
            <p class="tw-ml-2"> 
              <span style="color: #fff;">{{ Auth::User()->first_name }} {{ Auth::User()->last_name }}</span>
            </p>
          </div>
        </div>
    </div>
</div>

<div class="modal fade" id="service_staff_modal" tabindex="-1" role="dialog"
    aria-labelledby="gridSystemModalLabel">
</div>
