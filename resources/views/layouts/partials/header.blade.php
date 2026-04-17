@inject('request', 'Illuminate\Http\Request')
<!-- Main Header -->
<style>
    .thetop .tw-flex.tw-flex-wrap.tw-items-center.tw-justify-end.tw-gap-2 button,
    .thetop .tw-flex.tw-flex-wrap.tw-items-center.tw-justify-end.tw-gap-2 a,
    .header-action-btn {
        background-color: var(--theme-dark) !important;
        color: white !important;
        border: none !important;
        height: 38px !important;
        padding-top: 0 !important;
        padding-bottom: 0 !important;
        padding-left: 0.875rem !important;
        padding-right: 0.875rem !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        box-sizing: border-box !important;
        font-size: 0.875rem !important;
        font-weight: 600 !important;
        border-radius: 0.375rem !important;
    }
    .thetop .tw-flex.tw-flex-wrap.tw-items-center.tw-justify-end.tw-gap-2 button:hover,
    .thetop .tw-flex.tw-flex-wrap.tw-items-center.tw-justify-end.tw-gap-2 a:hover,
    .header-action-btn:hover {
        background-color: var(--theme-main) !important;
    }
    .thetop .tw-flex.tw-flex-wrap.tw-items-center.tw-justify-end.tw-gap-2 svg,
    .header-action-btn svg {
        color: white !important;
        width: 1.125rem !important;
        height: 1.125rem !important;
    }
    /* Exception for the Date Display span so it matches perfectly */
    .thetop .tw-flex.tw-flex-wrap.tw-items-center.tw-justify-end.tw-gap-2 span.header-action-btn {
        height: 38px !important;
    }
    .header-theme-btn {
        background-color: var(--theme-dark) !important;
        color: white !important;
        border: none !important;
    }
    .header-theme-btn:hover {
        background-color: var(--theme-main) !important;
    }
    .header-theme-btn svg {
        color: white !important;
    }
</style>

<div
    class="tw-transition-all tw-duration-5000 tw-border-b tw-shrink-0 lg:tw-h-15 no-print"
    style="background: linear-gradient(to right, var(--theme-dark), var(--theme-main)); border-bottom-color: rgba(255,255,255,0.15);">
    <div class="tw-px-5 tw-py-3">
        <div class="tw-flex tw-items-start tw-justify-between tw-gap-6 lg:tw-items-center">
            <div class="tw-flex tw-items-center tw-gap-3">
                <button type="button"
                    class="small-view-button xl:tw-w-20 lg:tw-hidden tw-inline-flex tw-items-center tw-justify-center tw-text-sm tw-font-medium tw-text-white tw-transition-all tw-duration-200 header-theme-btn tw-p-1.5 tw-rounded-lg tw-ring-1 tw-ring-white/10">
                    <span class="tw-sr-only">
                        Sidebar Menu
                    </span>
                    <svg aria-hidden="true" class="tw-size-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                        stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M4 6l16 0" />
                        <path d="M4 12l16 0" />
                        <path d="M4 18l16 0" />
                    </svg>
                </button>

                <button type="button"
                    class="side-bar-collapse tw-hidden lg:tw-inline-flex tw-items-center tw-justify-center tw-text-sm tw-font-medium tw-text-white tw-transition-all tw-duration-200 header-theme-btn tw-p-1.5 tw-rounded-lg tw-ring-1 tw-ring-white/10">
                    <span class="tw-sr-only">
                        Collapse Sidebar
                    </span>
                    <svg aria-hidden="true" class="tw-size-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                        stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M4 4m0 2a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2z" />
                        <path d="M15 4v16" />
                        <path d="M10 10l-2 2l2 2" />
                    </svg>
                </button>
            </div>


            {{-- Showing active package for SaaS Superadmin --}}
            @if(Module::has('Superadmin'))
                @includeIf('superadmin::layouts.partials.active_subscription')
            @endif

            {{-- When using superadmin, this button is used to switch users --}}
            @if(!empty(session('previous_user_id')) && !empty(session('previous_username')))
                <a href="{{route('sign-in-as-user', session('previous_user_id'))}}" class="btn btn-flat btn-danger m-8 btn-sm mt-10"><i class="fas fa-undo"></i> @lang('lang_v1.back_to_username', ['username' => session('previous_username')] )</a>
            @endif


            @php
                $__hdrBtn = 'tw-inline-flex tw-items-center tw-justify-center tw-gap-1.5 tw-text-white tw-transition-all tw-duration-200 tw-ring-1 tw-ring-white/20 hover:tw-ring-white/40 header-action-btn shadow-sm';
            @endphp
            <div class="tw-flex tw-flex-wrap tw-items-center tw-justify-end tw-gap-2">
                @if (Module::has('Essentials'))
                    @includeIf('essentials::layouts.partials.header_part')
                @endif

                {{-- Date Display --}}
                <span class="{{ $__hdrBtn }} tw-hidden sm:tw-inline-flex tw-font-mono tw-opacity-80" style="cursor:default;">
                    <svg aria-hidden="true" class="tw-size-4 !tw-text-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0"/><path d="M12 7v5l3 3"/>
                    </svg>
                    {{ @format_date('now') }}
                </span>

                {{-- Calendar --}}
                <a href="{{ route('calendar') }}" class="{{ $__hdrBtn }}">
                    <svg aria-hidden="true" class="tw-size-4 !tw-text-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12z"/><path d="M16 3v4"/><path d="M8 3v4"/><path d="M4 11h16"/><path d="M7 14h.013"/><path d="M10.01 14h.005"/><path d="M13.01 14h.005"/>
                    </svg>
                    @lang('lang_v1.calendar')
                </a>

                {{-- Calculator --}}
                <button id="btnCalculator" title="@lang('lang_v1.calculator')" data-content='@include('layouts.partials.calculator')'
                    type="button" data-trigger="click" data-html="true" data-placement="bottom"
                    class="{{ $__hdrBtn }}">
                    <svg aria-hidden="true" class="tw-size-4 !tw-text-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 3m0 2a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v14a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2z"/><path d="M8 7m0 1a1 1 0 0 1 1 -1h6a1 1 0 0 1 1 1v1a1 1 0 0 1 -1 1h-6a1 1 0 0 1 -1 -1z"/><path d="M8 14l0 .01"/><path d="M12 14l0 .01"/><path d="M16 14l0 .01"/><path d="M8 17l0 .01"/><path d="M12 17l0 .01"/><path d="M16 17l0 .01"/>
                    </svg>
                    @lang('lang_v1.calculator')
                </button>

                {{-- Today's Profit --}}
                @can('profit_loss_report.view')
                    <button type="button" id="view_todays_profit" title="{{ __('home.todays_profit') }}" data-toggle="tooltip" data-placement="bottom"
                        class="{{ $__hdrBtn }}">
                        <svg aria-hidden="true" class="tw-size-4 !tw-text-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0"/><path d="M3 6m0 2a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v8a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2z"/><path d="M18 12l.01 0"/><path d="M6 12l.01 0"/>
                        </svg>
                        {{ __('home.todays_profit') }}
                    </button>
                @endcan

                {{-- Add Expense --}}
                @can('expense.access')
                    <a href="{{ action([\App\Http\Controllers\ExpenseController::class, 'create']) }}" class="{{ $__hdrBtn }}">
                        <svg aria-hidden="true" class="tw-size-4 !tw-text-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 9m0 2a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2v6a2 2 0 0 1 -2 2h-6a2 2 0 0 1 -2 -2z"/><path d="M14 3v4h-6"/><path d="M3 14h4v-6"/><path d="M12 11v4"/><path d="M10 13h4"/>
                        </svg>
                        Expense
                    </a>
                @endcan

                {{-- Add Purchase --}}
                @can('purchase.create')
                    <a href="{{ action([\App\Http\Controllers\PurchaseController::class, 'create']) }}" class="{{ $__hdrBtn }}">
                        <svg aria-hidden="true" class="tw-size-4 !tw-text-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M6 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"/><path d="M17 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"/><path d="M17 17h-11v-14h-2"/><path d="M6 5l14 1l-1 7h-13"/>
                        </svg>
                        Purchase
                    </a>
                @endcan

                {{-- POS Sale --}}
                @if (in_array('pos_sale', $enabled_modules))
                    @can('sell.create')
                        <a href="{{ action([\App\Http\Controllers\SellPosController::class, 'create']) }}" class="{{ $__hdrBtn }}">
                            <svg aria-hidden="true" class="tw-size-4 !tw-text-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 4m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z"/><path d="M14 4m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z"/><path d="M4 14m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z"/><path d="M14 14m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z"/>
                            </svg>
                            @lang('sale.pos_sale')
                        </a>
                    @endcan
                @endif

                @if (Module::has('Repair'))
                    @includeIf('repair::layouts.partials.header')
                @endif

                @include('layouts.partials.header-notifications')



                <details class="tw-dw-dropdown tw-relative tw-inline-block tw-text-left">
                    <summary data-toggle="popover"
                        class="tw-dw-m-1 tw-inline-flex tw-transition-all tw-ring-1 tw-ring-white/20 tw-cursor-pointer tw-duration-200 header-action-btn tw-items-center tw-justify-center tw-text-white hover:tw-text-white tw-gap-1 shadow-sm">
                        <span class="tw-hidden md:tw-block">{{ Auth::User()->first_name }} {{ Auth::User()->last_name }}</span>

                        <svg  xmlns="http://www.w3.org/2000/svg"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="tw-size-4"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M12 10m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" /><path d="M6.168 18.849a4 4 0 0 1 3.832 -2.849h4a4 4 0 0 1 3.834 2.855" /></svg>

                        
                        
                    </summary>

                    <ul class="tw-p-2 tw-w-48 tw-absolute tw-right-0 tw-z-10 tw-mt-2 tw-origin-top-right tw-bg-white tw-rounded-lg tw-shadow-lg tw-ring-1 tw-ring-gray-200 focus:tw-outline-none"
                        role="menu" tabindex="-1">
                        <div class="tw-px-4 tw-pt-3 tw-pb-1" role="none">
                            <p class="tw-text-sm" role="none">
                                @lang('lang_v1.signed_in_as')
                            </p>
                            <p class="tw-text-sm tw-font-medium tw-text-gray-900 tw-truncate" role="none">
                                {{ Auth::User()->first_name }} {{ Auth::User()->last_name }}
                            </p>
                        </div>

                        <li>
                            <a href="{{ action([\App\Http\Controllers\UserController::class, 'getProfile']) }}"
                                class="tw-flex tw-items-center tw-gap-2 tw-px-3 tw-py-2 tw-text-sm tw-font-medium tw-text-gray-600 tw-transition-all tw-duration-200 tw-rounded-lg hover:tw-text-gray-900 hover:tw-bg-gray-100"
                                role="menuitem" tabindex="-1">
                                <svg aria-hidden="true" class="tw-w-5 tw-h-5" xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" fill="none"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                                    <path d="M12 10m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" />
                                    <path d="M6.168 18.849a4 4 0 0 1 3.832 -2.849h4a4 4 0 0 1 3.834 2.855" />
                                </svg>
                                @lang('lang_v1.profile')
                            </a>
                        </li>
                        <li>
                            <a href="{{ action([\App\Http\Controllers\Auth\LoginController::class, 'logout']) }}"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                                class="tw-flex tw-items-center tw-gap-2 tw-px-3 tw-py-2 tw-text-sm tw-font-medium tw-text-gray-600 tw-transition-all tw-duration-200 tw-rounded-lg hover:tw-text-gray-900 hover:tw-bg-gray-100"
                                role="menuitem" tabindex="-1">
                                <svg aria-hidden="true" class="tw-size-5" xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" fill="none"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path
                                        d="M14 8v-2a2 2 0 0 0 -2 -2h-7a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h7a2 2 0 0 0 2 -2v-2" />
                                    <path d="M9 12h12l-3 -3" />
                                    <path d="M18 15l3 -3" />
                                </svg>
                                @lang('lang_v1.sign_out')
                            </a>
                            <form id="logout-form" action="{{ action([\App\Http\Controllers\Auth\LoginController::class, 'logout']) }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </li>
                    </ul>
                </details>
            </div>
        </div>
    </div>
</div>
