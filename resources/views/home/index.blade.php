@extends('layouts.app')
@section('title', __('home.home'))

@section('content')
    <div class="dashboard-fortypos">
    <div class="tw-pb-6 tw-bg-gradient-to-r tw-from-@if(!empty(session('business.theme_color'))){{session('business.theme_color')}}@else{{'primary'}}@endif-800 tw-to-@if(!empty(session('business.theme_color'))){{session('business.theme_color')}}@else{{'primary'}}@endif-900 xl:tw-pb-0 ">
        <div class="tw-px-5 tw-pt-3">
            {{-- <div class="sm:tw-flex sm:tw-items-center sm:tw-justify-between sm:tw-gap-12">
                <h1 class="tw-text-2xl tw-font-medium tw-tracking-tight tw-text-white">
                    {{ __('home.welcome_message', ['name' => Session::get('user.first_name')]) }}
                </h1>
            </div> --}}
                    <div class="sm:tw-flex sm:tw-items-center sm:tw-justify-between sm:tw-gap-12">
                        <div class="tw-mt-2 sm:tw-w-1/2 md:tw-w-1/2">
                            <h1
                                class="tw-text-2xl md:tw-text-4xl tw-tracking-tight tw-text-primary-800 tw-font-semibold text-white tw-mb-10 md:tw-mb-0">
                                {{ __('home.welcome_message', ['name' => Session::get('user.first_name')]) }}
                            </h1>
                        </div>
    
                        @if (auth()->user()->can('dashboard.data'))
                            @if ($is_admin)
                                <div class="tw-mt-2 sm:tw-w-1/3 md:tw-w-1/4 ">
                                    @if (count($all_locations) > 1)
                                        {!! Form::select('dashboard_location', $all_locations, null, [
                                            'class' => 'form-control select2',
                                            'placeholder' => __('lang_v1.select_location'),
                                            'id' => 'dashboard_location',
                                        ]) !!}
                                    @endif
                                </div>
            
                                <div class="tw-mt-2 sm:tw-w-1/3 md:tw-w-1/4 tw-text-right">
                                    @if ($is_admin)
                                        <button type="button" id="dashboard_date_filter"
                                            class="tw-inline-flex tw-items-center tw-justify-center tw-w-full tw-gap-1 tw-px-3 tw-py-2 tw-text-sm tw-font-medium tw-text-gray-900 tw-transition-all tw-duration-200 tw-bg-white tw-rounded-lg sm:tw-w-auto hover:tw-bg-primary-50">
                                            <svg aria-hidden="true" class="tw-size-5" xmlns="http://www.w3.org/2000/svg"
                                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none"
                                                stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path
                                                    d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12z" />
                                                <path d="M16 3v4" />
                                                <path d="M8 3v4" />
                                                <path d="M4 11h16" />
                                                <path d="M7 14h.013" />
                                                <path d="M10.01 14h.005" />
                                                <path d="M13.01 14h.005" />
                                                <path d="M16.015 14h.005" />
                                                <path d="M13.015 17h.005" />
                                                <path d="M7.01 17h.005" />
                                                <path d="M10.01 17h.005" />
                                            </svg>
                                            <span>
                                                {{ __('messages.filter_by_date') }}
                                            </span>
                                            <svg aria-hidden="true" class="tw-size-4" xmlns="http://www.w3.org/2000/svg"
                                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                                stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path d="M6 9l6 6l6 -6" />
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                            @endif
                        @endif
                    </div>
                    {{-- ===== QUICK ACTIONS BAR ===== --}}
                    <div class="tw-mt-5 tw-mb-2">
                        <p class="tw-text-xs tw-font-semibold tw-text-white/60 tw-uppercase tw-tracking-wider tw-mb-2">Quick Actions</p>
                        <div class="tw-flex tw-flex-wrap tw-gap-2">
                            @can('direct_sell.access')
                            <a href="{{ action([\App\Http\Controllers\SellPosController::class, 'create']) }}"
                               style="display:inline-flex;align-items:center;gap:0.4rem;padding:0.45rem 0.875rem;border-radius:999px;background:rgba(255,255,255,0.18);border:1px solid rgba(255,255,255,0.3);color:#fff;font-size:0.8rem;font-weight:600;text-decoration:none;transition:background .15s;backdrop-filter:blur(4px);"
                               onmouseover="this.style.background='rgba(255,255,255,0.28)'"
                               onmouseout="this.style.background='rgba(255,255,255,0.18)'">
                                <i class="fas fa-cash-register" style="font-size:0.75rem;"></i> New Sale
                            </a>
                            @endcan
                            @can('purchase.create')
                            <a href="{{ action([\App\Http\Controllers\PurchaseController::class, 'create']) }}"
                               style="display:inline-flex;align-items:center;gap:0.4rem;padding:0.45rem 0.875rem;border-radius:999px;background:rgba(255,255,255,0.18);border:1px solid rgba(255,255,255,0.3);color:#fff;font-size:0.8rem;font-weight:600;text-decoration:none;transition:background .15s;backdrop-filter:blur(4px);"
                               onmouseover="this.style.background='rgba(255,255,255,0.28)'"
                               onmouseout="this.style.background='rgba(255,255,255,0.18)'">
                                <i class="fas fa-truck" style="font-size:0.75rem;"></i> New Purchase
                            </a>
                            @endcan
                            @can('customer.create')
                            <a href="{{ action([\App\Http\Controllers\ContactController::class, 'create'], ['type'=>'customer']) }}"
                               style="display:inline-flex;align-items:center;gap:0.4rem;padding:0.45rem 0.875rem;border-radius:999px;background:rgba(255,255,255,0.18);border:1px solid rgba(255,255,255,0.3);color:#fff;font-size:0.8rem;font-weight:600;text-decoration:none;transition:background .15s;backdrop-filter:blur(4px);"
                               onmouseover="this.style.background='rgba(255,255,255,0.28)'"
                               onmouseout="this.style.background='rgba(255,255,255,0.18)'">
                                <i class="fas fa-user-plus" style="font-size:0.75rem;"></i> Add Customer
                            </a>
                            @endcan
                            @can('product.create')
                            <a href="{{ action([\App\Http\Controllers\ProductController::class, 'create']) }}"
                               style="display:inline-flex;align-items:center;gap:0.4rem;padding:0.45rem 0.875rem;border-radius:999px;background:rgba(255,255,255,0.18);border:1px solid rgba(255,255,255,0.3);color:#fff;font-size:0.8rem;font-weight:600;text-decoration:none;transition:background .15s;backdrop-filter:blur(4px);"
                               onmouseover="this.style.background='rgba(255,255,255,0.28)'"
                               onmouseout="this.style.background='rgba(255,255,255,0.18)'">
                                <i class="fas fa-box" style="font-size:0.75rem;"></i> Add Product
                            </a>
                            @endcan
                            @can('expense.access')
                            <a href="{{ action([\App\Http\Controllers\ExpenseController::class, 'create']) }}"
                               style="display:inline-flex;align-items:center;gap:0.4rem;padding:0.45rem 0.875rem;border-radius:999px;background:rgba(255,255,255,0.18);border:1px solid rgba(255,255,255,0.3);color:#fff;font-size:0.8rem;font-weight:600;text-decoration:none;transition:background .15s;backdrop-filter:blur(4px);"
                               onmouseover="this.style.background='rgba(255,255,255,0.28)'"
                               onmouseout="this.style.background='rgba(255,255,255,0.18)'">
                                <i class="fas fa-receipt" style="font-size:0.75rem;"></i> Add Expense
                            </a>
                            @endcan
                            @can('profit_loss_report.view')
                            <a href="{{ action([\App\Http\Controllers\ReportController::class, 'getProfitLoss']) }}"
                               style="display:inline-flex;align-items:center;gap:0.4rem;padding:0.45rem 0.875rem;border-radius:999px;background:rgba(255,255,255,0.18);border:1px solid rgba(255,255,255,0.3);color:#fff;font-size:0.8rem;font-weight:600;text-decoration:none;transition:background .15s;backdrop-filter:blur(4px);"
                               onmouseover="this.style.background='rgba(255,255,255,0.28)'"
                               onmouseout="this.style.background='rgba(255,255,255,0.18)'">
                                <i class="fas fa-chart-bar" style="font-size:0.75rem;"></i> P&amp;L Report
                            </a>
                            @endcan
                        </div>
                    </div>
                    {{-- ===== END QUICK ACTIONS ===== --}}

                    @if (auth()->user()->can('dashboard.data'))
                        <div class="tw-bg-white/10 tw-border tw-border-white/20 tw-rounded-xl tw-p-3 tw-mt-4 tw-flex tw-items-center tw-justify-between tw-text-white tw-backdrop-blur-sm">
                            <div class="tw-flex tw-items-center tw-gap-4">
                                <div class="tw-animate-pulse tw-w-2 tw-h-2 tw-bg-green-400 tw-rounded-full"></div>
                                <span class="tw-text-sm tw-font-medium">Live Sales Ticker: <span id="ticker_today_sales" class="tw-font-bold">{{ session('currency')['symbol'] }} 0.00</span></span>
                                <span class="tw-text-xs tw-text-white/60">| Transitions Today: <span id="ticker_today_transactions" class="tw-font-bold">0</span></span>
                            </div>
                            <div class="tw-text-right">
                                <span id="ticker_last_sale" class="tw-text-xs tw-text-white/80">Last Sale: Fetching...</span>
                                <span class="tw-text-xs tw-text-white/40 tw-ml-2">Refreshed: <span id="ticker_timestamp">--:--:--</span></span>
                            </div>
                        </div>

                        @if ($is_admin)
                            <div class="tw-grid tw-grid-cols-1 tw-gap-4 tw-mt-6 sm:tw-grid-cols-2 xl:tw-grid-cols-4 sm:tw-gap-5">
                            
                                <div
                                    class="tw-transition-all tw-duration-200 tw-bg-white tw-shadow-sm hover:tw-shadow-md tw-rounded-xl  tw-ring-1 tw-ring-gray-200">
                                    <div class="tw-p-4 sm:tw-p-5">
                                        <div class="tw-flex tw-items-center tw-gap-4">
                                            <div
                                                class="tw-inline-flex tw-items-center tw-justify-center tw-w-10 tw-h-10 tw-rounded-full sm:tw-w-12 sm:tw-h-12 tw-shrink-0 tw-bg-sky-100 tw-text-sky-500">
                                                <svg aria-hidden="true" class="tw-w-6 tw-h-6" xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                                    stroke-linecap="round" stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path d="M6 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                                                    <path d="M17 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                                                    <path d="M17 17h-11v-14h-2" />
                                                    <path d="M6 5l14 1l-1 7h-13" />
                                                </svg>
                                            </div>

                                            <div class="tw-flex-1 tw-min-w-0">
                                                <p
                                                    class="tw-text-sm tw-font-medium tw-text-gray-500 tw-truncate tw-whitespace-nowrap">
                                                    {{ __('home.total_sell') }}
                                                </p>
                                                <p
                                                    class="total_sell tw-mt-0.5 tw-text-gray-900 tw-text-xl tw-truncate tw-font-semibold tw-tracking-tight tw-font-mono">
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div
                                    class="tw-transition-all tw-duration-200 tw-bg-white tw-shadow-sm hover:tw-shadow-md tw-rounded-xl hover:tw--translate-y-0.5 tw-ring-1 tw-ring-gray-200">
                                    <div class="tw-p-4 sm:tw-p-5">
                                        <div class="tw-flex tw-items-center tw-gap-4">
                                            <div
                                                class="tw-inline-flex tw-items-center tw-justify-center tw-w-10 tw-h-10 tw-text-green-500 tw-bg-green-100 tw-rounded-full sm:tw-w-12 sm:tw-h-12 tw-shrink-0">
                                                <svg aria-hidden="true" class="tw-w-6 tw-h-6" xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                                    stroke-linecap="round" stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                    <path
                                                        d="M5 21v-16a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v16l-3 -2l-2 2l-2 -2l-2 2l-2 -2l-3 2">
                                                    </path>
                                                    <path
                                                        d="M14.8 8a2 2 0 0 0 -1.8 -1h-2a2 2 0 1 0 0 4h2a2 2 0 1 1 0 4h-2a2 2 0 0 1 -1.8 -1">
                                                    </path>
                                                    <path d="M12 6v10"></path>
                                                </svg>
                                            </div>

                                            <div class="tw-flex-1 tw-min-w-0">
                                                <p
                                                    class="tw-text-sm tw-font-medium tw-text-gray-500 tw-truncate tw-whitespace-nowrap">
                                                    {{ __('lang_v1.net') }} @show_tooltip(__('lang_v1.net_home_tooltip'))
                                                </p>
                                                <p
                                                    class="net tw-mt-0.5 tw-text-gray-900 tw-text-xl tw-truncate tw-font-semibold tw-tracking-tight tw-font-mono">
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div
                                    class="tw-transition-all tw-duration-200 tw-bg-white tw-shadow-sm hover:tw-shadow-md tw-rounded-xl hover:tw--translate-y-0.5 tw-ring-1 tw-ring-gray-200">
                                    <div class="tw-p-4 sm:tw-p-5">
                                        <div class="tw-flex tw-items-center tw-gap-4">
                                            <div
                                                class="tw-inline-flex tw-items-center tw-justify-center tw-w-10 tw-h-10 tw-text-yellow-500 tw-bg-yellow-100 tw-rounded-full sm:tw-w-12 sm:tw-h-12 shrink-0">
                                                <svg aria-hidden="true" class="tw-w-6 tw-h-6" xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                                    stroke-linecap="round" stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                                                    <path
                                                        d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                                                    <path d="M9 7l1 0" />
                                                    <path d="M9 13l6 0" />
                                                    <path d="M13 17l2 0" />
                                                </svg>
                                            </div>

                                            <div class="tw-flex-1 tw-min-w-0">
                                                <p
                                                    class="tw-text-sm tw-font-medium tw-text-gray-500 tw-truncate tw-whitespace-nowrap">
                                                    {{ __('home.invoice_due') }}
                                                </p>
                                                <p
                                                    class="invoice_due tw-mt-0.5 tw-text-gray-900 tw-text-xl tw-truncate tw-font-semibold tw-tracking-tight tw-font-mono">
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div
                                    class="tw-transition-all tw-duration-200 tw-bg-white tw-shadow-sm hover:tw-shadow-md tw-rounded-xl hover:tw--translate-y-0.5 tw-ring-1 tw-ring-gray-200">
                                    <div class="tw-p-4 sm:tw-p-5">
                                        <div class="tw-flex tw-items-center tw-gap-4">
                                            <div
                                                class="tw-inline-flex tw-items-center tw-justify-center tw-w-10 tw-h-10 tw-text-red-500 tw-bg-red-100 tw-rounded-full sm:tw-w-12 sm:tw-h-12 shrink-0">
                                                <svg aria-hidden="true" class="tw-w-6 tw-h-6" xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                                    stroke-linecap="round" stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path d="M21 7l-18 0" />
                                                    <path d="M18 10l3 -3l-3 -3" />
                                                    <path d="M6 20l-3 -3l3 -3" />
                                                    <path d="M3 17l18 0" />
                                                </svg>
                                            </div>

                                            <div class="tw-flex-1 tw-min-w-0">
                                                <p
                                                    class="tw-text-sm tw-font-medium tw-text-gray-500 tw-truncate tw-whitespace-nowrap">
                                                    {{ __('lang_v1.total_sell_return') }}
                                                    <i class="fa fa-info-circle text-info hover-q no-print" aria-hidden="true" data-container="body"
                                                    data-toggle="popover" data-placement="auto bottom" id="total_srp"
                                                    data-value="{{ __('lang_v1.total_sell_return') }}-{{ __('lang_v1.total_sell_return_paid') }}"
                                                    data-content="" data-html="true" data-trigger="hover"></i>
                                                </p>
                                                <p
                                                    class="total_sell_return tw-mt-0.5 tw-text-gray-900 tw-text-xl tw-truncate tw-font-semibold tw-tracking-tight tw-font-mono">
                                                </p>
                                                {{-- <p class="mb-0 text-muted fs-10 mt-5">{{ __('lang_v1.total_sell_return') }}: <span
                                                        class="total_sr"></span><br>
                                                    {{ __('lang_v1.total_sell_return_paid') }}<span class="total_srp"></span></p> --}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endif
              
        </div>
        @if (auth()->user()->can('dashboard.data'))
            @if ($is_admin)
                <div class="tw-relative">
                    <div class="tw-absolute tw-inset-0 tw-grid" aria-hidden="true">
                        <div class="tw-bg-gradient-to-r tw-from-@if(!empty(session('business.theme_color'))){{session('business.theme_color')}}@else{{'primary'}}@endif-800 tw-to-@if(!empty(session('business.theme_color'))){{session('business.theme_color')}}@else{{'primary'}}@endif-900"></div>
                        <div class="tw-bg-gradient-to-r tw-from-@if(!empty(session('business.theme_color'))){{session('business.theme_color')}}@else{{'primary'}}@endif-800 tw-to-@if(!empty(session('business.theme_color'))){{session('business.theme_color')}}@else{{'primary'}}@endif-900 xl:tw-bg-none xl:tw-bg-gray-100">
                        </div>
                    </div>
                    <div class="tw-px-5 tw-isolate">
                        <div
                            class="tw-grid tw-grid-cols-1 tw-gap-4 tw-mt-4 sm:tw-mt-6 sm:tw-grid-cols-2 xl:tw-grid-cols-4 sm:tw-gap-5">
                            <div
                                class="tw-transition-all tw-duration-200 tw-bg-white tw-shadow-sm tw-rounded-xl hover:tw-shadow-md hover:tw--translate-y-0.5 tw-ring-1 tw-ring-gray-200">
                                <div class="tw-p-4 sm:tw-p-5">
                                    <div class="tw-flex tw-items-center tw-gap-4">
                                        <div
                                            class="tw-inline-flex tw-items-center tw-justify-center tw-w-10 tw-h-10 tw-rounded-full sm:tw-w-12 sm:tw-h-12 shrink-0 bg-sky-100 tw-text-sky-500">
                                            <svg aria-hidden="true" class="tw-w-6 tw-h-6"
                                                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2"
                                                stroke="currentColor" fill="none" stroke-linecap="round"
                                                stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                <path d="M12 3v12"></path>
                                                <path d="M16 11l-4 4l-4 -4"></path>
                                                <path d="M3 12a9 9 0 0 0 18 0"></path>
                                            </svg>
                                        </div>

                                        <div class="tw-flex-1 tw-min-w-0">
                                            <p
                                                class="tw-text-sm tw-font-medium tw-text-gray-500 tw-truncate tw-whitespace-nowrap">
                                                {{ __('home.total_purchase') }}
                                            </p>
                                            <p
                                                class="total_purchase tw-mt-0.5 tw-text-gray-900 tw-text-xl tw-truncate tw-font-semibold tw-tracking-tight tw-font-mono">
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div
                                class="tw-transition-all tw-duration-200 tw-bg-white tw-shadow-sm tw-rounded-xl hover:tw-shadow-md hover:tw--translate-y-0.5 tw-ring-1 tw-ring-gray-200">
                                <div class="tw-p-4 sm:tw-p-5">
                                    <div class="tw-flex tw-items-center tw-gap-4">
                                        <div
                                            class="tw-inline-flex tw-items-center tw-justify-center tw-w-10 tw-h-10 tw-text-yellow-500 tw-bg-yellow-100 tw-rounded-full sm:tw-w-12 sm:tw-h-12 shrink-0">
                                            <svg aria-hidden="true" class="tw-w-6 tw-h-6"
                                                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2"
                                                stroke="currentColor" fill="none" stroke-linecap="round"
                                                stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path d="M12 9v4" />
                                                <path
                                                    d="M10.363 3.591l-8.106 13.534a1.914 1.914 0 0 0 1.636 2.871h16.214a1.914 1.914 0 0 0 1.636 -2.87l-8.106 -13.536a1.914 1.914 0 0 0 -3.274 0z" />
                                                <path d="M12 16h.01" />
                                            </svg>
                                        </div>

                                        <div>
                                            <p class="tw-text-sm tw-font-medium tw-text-gray-500">
                                                {{ __('home.purchase_due') }}
                                            </p>
                                            <p
                                                class="purchase_due tw-mt-0.5 tw-text-gray-900 tw-text-xl tw-truncate tw-font-semibold tw-tracking-tight tw-font-mono">

                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div
                                class="tw-transition-all tw-duration-200 tw-bg-white tw-shadow-sm tw-rounded-xl hover:tw-shadow-md hover:tw--translate-y-0.5 tw-ring-1 tw-ring-gray-200">
                                <div class="tw-p-4 sm:tw-p-5">
                                    <div class="tw-flex tw-items-center tw-gap-4">
                                        <div
                                            class="tw-inline-flex tw-items-center tw-justify-center tw-w-10 tw-h-10 tw-text-red-500 tw-bg-red-100 tw-rounded-full sm:tw-w-12 sm:tw-h-12 shrink-0">
                                            <svg aria-hidden="true" class="tw-w-6 tw-h-6"
                                                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2"
                                                stroke="currentColor" fill="none" stroke-linecap="round"
                                                stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path
                                                    d="M5 21v-16a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v16l-3 -2l-2 2l-2 -2l-2 2l-2 -2l-3 2" />
                                                <path d="M15 14v-2a2 2 0 0 0 -2 -2h-4l2 -2m0 4l-2 -2" />
                                            </svg>
                                        </div>

                                        <div class="tw-flex-1 tw-min-w-0">
                                            <p
                                                class="tw-text-sm tw-font-medium tw-text-gray-500 tw-truncate tw-whitespace-nowrap">
                                                {{ __('lang_v1.total_purchase_return') }}
                                                <i class="fa fa-info-circle text-info hover-q no-print" aria-hidden="true" data-container="body"
                                                data-toggle="popover" data-placement="auto bottom" id="total_prp"
                                                data-value="{{ __('lang_v1.total_purchase_return') }}-{{ __('lang_v1.total_purchase_return_paid') }}"
                                                data-content="" data-html="true" data-trigger="hover"></i>
                                            </p>
                                            <p
                                                class="total_purchase_return tw-mt-0.5 tw-text-gray-900 tw-text-xl tw-truncate tw-font-semibold tw-tracking-tight tw-font-mono">
                                            </p>
                                            {{-- <p class="mb-0 text-muted fs-10 mt-5">
                                                {{ __('lang_v1.total_purchase_return') }}: <span
                                                    class="total_pr"></span><br>
                                                {{ __('lang_v1.total_purchase_return_paid') }}<span
                                                    class="total_prp"></span></p> --}}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div
                                class="tw-transition-all tw-duration-200 tw-bg-white tw-shadow-sm tw-rounded-xl hover:tw-shadow-md hover:tw--translate-y-0.5 tw-ring-1 tw-ring-gray-200">
                                <div class="tw-p-4 sm:tw-p-5">
                                    <div class="tw-flex tw-items-center tw-gap-4">
                                        <div
                                            class="tw-inline-flex tw-items-center tw-justify-center tw-w-10 tw-h-10 tw-text-red-500 tw-bg-red-100 tw-rounded-full sm:tw-w-12 sm:tw-h-12 shrink-0">
                                            <svg aria-hidden="true" class="tw-w-6 tw-h-6"
                                                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2"
                                                stroke="currentColor" fill="none" stroke-linecap="round"
                                                stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                <path
                                                    d="M5 21v-16a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v16l-3 -2l-2 2l-2 -2l-2 2l-2 -2l-3 2">
                                                </path>
                                                <path
                                                    d="M14.8 8a2 2 0 0 0 -1.8 -1h-2a2 2 0 1 0 0 4h2a2 2 0 1 1 0 4h-2a2 2 0 0 1 -1.8 -1">
                                                </path>
                                                <path d="M12 6v10"></path>
                                            </svg>
                                        </div>

                                        <div class="tw-flex-1 tw-min-w-0">
                                            <p
                                                class="tw-text-sm tw-font-medium tw-text-gray-500 tw-truncate tw-whitespace-nowrap">
                                                {{ __('lang_v1.expense') }}
                                            </p>
                                            <p
                                                class="total_expense tw-mt-0.5 tw-text-gray-900 tw-text-xl tw-truncate tw-font-semibold tw-tracking-tight tw-font-mono">

                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- @if (!empty($widgets['after_sale_purchase_totals']))
                    @foreach ($widgets['after_sale_purchase_totals'] as $widget)
                        {!! $widget !!}
                    @endforeach
                @endif --}}
            @endif
        @endif
    </div>
    @if (auth()->user()->can('dashboard.data'))
        <div class="tw-px-5 tw-py-6">
            <div class="tw-grid tw-grid-cols-1 tw-gap-4 sm:tw-gap-5 lg:tw-grid-cols-2">
                @if (auth()->user()->can('sell.view') || auth()->user()->can('direct_sell.view'))
                    @if (!empty($all_locations))
                        <div
                            class="tw-transition-all lg:tw-col-span-1 tw-duration-200 tw-bg-white tw-shadow-sm tw-rounded-xl tw-ring-1 hover:tw-shadow-md hover:tw--translate-y-0.5 tw-ring-gray-200">
                            <div class="tw-p-4 sm:tw-p-5">
                                <div class="tw-flex tw-items-center tw-gap-2.5">
                                    <div
                                        class="tw-border-2 tw-flex tw-items-center tw-justify-center tw-rounded-full tw-w-10 tw-h-10">
                                        <svg aria-hidden="true" class="tw-size-5 tw-text-sky-500 tw-shrink-0"
                                            xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2"
                                            stroke="currentColor" fill="none" stroke-linecap="round"
                                            stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                            <path d="M6 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"></path>
                                            <path d="M17 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"></path>
                                            <path d="M17 17h-11v-14h-2"></path>
                                            <path d="M6 5l14 1l-1 7h-13"></path>
                                        </svg>
                                    </div>

                                    <h3 class="tw-font-bold tw-text-base lg:tw-text-xl">
                                        {{ __('home.sells_last_30_days') }}
                                    </h3>
                                </div>
                                <div class="tw-mt-5">
                                    <div
                                        class="tw-grid tw-w-full tw-h-100 tw-border tw-border-gray-200 tw-border-dashed tw-rounded-xl tw-bg-gray-50 " style="min-height: 250px;">
                                        {!! $sells_chart_1->container() !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- @if (!empty($widgets['after_sales_last_30_days']))
                        @foreach ($widgets['after_sales_last_30_days'] as $widget)
                            {!! $widget !!}
                        @endforeach
                    @endif --}}
                    @if (!empty($all_locations))
                        <div
                            class="tw-transition-all lg:tw-col-span-1 tw-duration-200 tw-bg-white tw-shadow-sm tw-rounded-xl tw-ring-1 hover:tw-shadow-md hover:tw--translate-y-0.5 tw-ring-gray-200">
                            <div class="tw-p-4 sm:tw-p-5">
                                <div class="tw-flex tw-items-center tw-gap-2.5">
                                    <div
                                        class="tw-border-2 tw-flex tw-items-center tw-justify-center tw-rounded-full tw-w-10 tw-h-10">
                                        <svg aria-hidden="true" class="tw-size-5 tw-text-sky-500 tw-shrink-0"
                                            xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2"
                                            stroke="currentColor" fill="none" stroke-linecap="round"
                                            stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                            <path d="M6 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"></path>
                                            <path d="M17 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"></path>
                                            <path d="M17 17h-11v-14h-2"></path>
                                            <path d="M6 5l14 1l-1 7h-13"></path>
                                        </svg>
                                    </div>
                                    <h3 class="tw-font-bold tw-text-base lg:tw-text-xl">
                                        {{ __('home.sells_current_fy') }}
                                    </h3>
                                </div>
                                <div class="tw-mt-5">
                                    <div
                                        class="tw-grid tw-w-full tw-h-100 tw-border tw-border-gray-200 tw-border-dashed tw-rounded-xl tw-bg-gray-50 " style="min-height: 250px;">
                                        {!! $sells_chart_2->container() !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                @endif
                
                {{-- Analytics Charts Phase 5 --}}
                @if (auth()->user()->can('dashboard.data') && $is_admin)
                    <div class="tw-transition-all lg:tw-col-span-1 tw-duration-200 tw-bg-white tw-shadow-sm tw-rounded-xl tw-ring-1 hover:tw-shadow-md hover:tw--translate-y-0.5 tw-ring-gray-200">
                        <div class="tw-p-4 sm:tw-p-5">
                            <div class="tw-flex tw-items-center tw-gap-2.5">
                                <div class="tw-border-2 tw-flex tw-items-center tw-justify-center tw-rounded-full tw-w-10 tw-h-10">
                                    <i class="fa fa-users tw-text-blue-500"></i>
                                </div>
                                <h3 class="tw-font-bold tw-text-base lg:tw-text-xl">
                                    Staff Performance (Last 30 Days)
                                </h3>
                            </div>
                            <div class="tw-mt-5">
                                <div class="tw-grid tw-w-full tw-h-100 tw-border tw-border-gray-200 tw-border-dashed tw-rounded-xl tw-bg-gray-50 " style="min-height: 250px;">
                                    {!! $staff_performance_chart->container() !!}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tw-transition-all lg:tw-col-span-1 tw-duration-200 tw-bg-white tw-shadow-sm tw-rounded-xl tw-ring-1 hover:tw-shadow-md hover:tw--translate-y-0.5 tw-ring-gray-200">
                        <div class="tw-p-4 sm:tw-p-5">
                            <div class="tw-flex tw-items-center tw-gap-2.5">
                                <div class="tw-border-2 tw-flex tw-items-center tw-justify-center tw-rounded-full tw-w-10 tw-h-10">
                                    <i class="fa fa-line-chart tw-text-green-500"></i>
                                </div>
                                <h3 class="tw-font-bold tw-text-base lg:tw-text-xl">
                                    Monthly Gross Profit (Current FY)
                                </h3>
                            </div>
                            <div class="tw-mt-5">
                                <div class="tw-grid tw-w-full tw-h-100 tw-border tw-border-gray-200 tw-border-dashed tw-rounded-xl tw-bg-gray-50 " style="min-height: 250px;">
                                    {!! $profit_margin_chart->container() !!}
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                @can('stock_report.view')
                    <div
                        class="tw-transition-all lg:tw-col-span-1 tw-duration-200 tw-bg-white tw-shadow-sm tw-rounded-xl tw-ring-1 hover:tw-shadow-md hover:tw--translate-y-0.5 tw-ring-gray-200">
                        <div class="tw-p-4 sm:tw-p-5">
                            <div class="tw-flex tw-items-center tw-gap-2.5">
                                <div
                                    class="tw-border-2 tw-flex tw-items-center tw-justify-center tw-rounded-full tw-w-10 tw-h-10">
                                    <svg aria-hidden="true" class="tw-text-yellow-500 tw-size-5 tw-shrink-0"
                                        xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2"
                                        stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                        <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"></path>
                                        <path d="M12 8v4"></path>
                                        <path d="M12 16h.01"></path>
                                    </svg>
                                </div>
                                <div class="tw-flex tw-items-center tw-flex-1 tw-min-w-0 tw-gap-1">
                                    <div class="tw-w-full sm:tw-w-1/2 md:tw-w-1/2">
                                        <h3 class="tw-font-bold tw-text-base lg:tw-text-xl">
                                            {{ __('home.product_stock_alert') }}
                                            @show_tooltip(__('tooltip.product_stock_alert'))
                                        </h3>
                                    </div>
                                    <div class="tw-w-full sm:tw-w-1/2 md:tw-w-1/2">
                                        @if (count($all_locations) > 1)
                                            {!! Form::select('stock_alert_location', $all_locations, null, [
                                                'class' => 'form-control select2',
                                                'placeholder' => __('lang_v1.select_location'),
                                                'id' => 'stock_alert_location',
                                            ]) !!}
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="tw-flow-root tw-mt-5  tw-border-gray-200">
                                <div class="tw--mx-4 tw--my-2 tw-overflow-x-auto sm:tw--mx-5">
                                    <div class="tw-inline-block tw-min-w-full tw-py-2 tw-align-middle sm:tw-px-5">
                                        <table class="table table-bordered table-striped" id="stock_alert_table"
                                            style="width: 100%;">
                                            <thead>
                                                <tr>
                                                    <th>@lang('sale.product')</th>
                                                    <th>@lang('business.location')</th>
                                                    <th>@lang('report.current_stock')</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Overall Reports Pie Chart -->
                    <div class="tw-transition-all lg:tw-col-span-1 tw-duration-200 tw-bg-white tw-shadow-sm tw-rounded-xl tw-ring-1 hover:tw-shadow-md hover:tw--translate-y-0.5 tw-ring-gray-200">
                        <div class="tw-p-4 sm:tw-p-5">
                            <div class="tw-flex tw-items-center tw-justify-between tw-gap-2.5">
                                <div class="tw-flex tw-items-center tw-gap-2.5">
                                    <div class="tw-border-2 tw-flex tw-items-center tw-justify-center tw-rounded-full tw-w-10 tw-h-10">
                                        <svg aria-hidden="true" class="tw-text-blue-500 tw-size-5 tw-shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                            <path d="M10 3.2a9 9 0 1 0 10.8 10.8a1 1 0 0 0 -1 -1h-6.8a2 2 0 0 1 -2 -2v-7a.9 .9 0 0 0 -1 -.8"></path>
                                            <path d="M15 3.5a9 9 0 0 1 5.5 5.5h-4.5a1 1 0 0 1 -1 -1v-4.5"></path>
                                        </svg>
                                    </div>
                                    <h3 class="tw-font-bold tw-text-base lg:tw-text-xl">Overall Reports</h3>
                                </div>
                                <select id="overall_reports_year" class="tw-border tw-border-gray-300 tw-rounded-lg tw-px-3 tw-py-1.5 tw-text-sm">
                                    @for($y = date('Y'); $y >= date('Y') - 2; $y--)
                                        <option value="{{ $y }}">{{ $y }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="tw-mt-5">
                                <div class="tw-flex tw-flex-col lg:tw-flex-row tw-gap-6">
                                    <div class="tw-flex-1" style="min-height: 250px;">
                                        <canvas id="overall_reports_chart"></canvas>
                                    </div>
                                    <div class="tw-flex tw-flex-col tw-justify-center tw-gap-3 lg:tw-w-1/3">
                                        <div class="tw-flex tw-items-center tw-gap-2">
                                            <span class="tw-w-3 tw-h-3 tw-rounded-sm" style="background: #4CAF50;"></span>
                                            <span class="tw-text-sm tw-text-gray-600">Purchase:</span>
                                            <span class="tw-font-semibold tw-text-sm" id="pie_purchase">{{ session('currency')['symbol'] }}0</span>
                                        </div>
                                        <div class="tw-flex tw-items-center tw-gap-2">
                                            <span class="tw-w-3 tw-h-3 tw-rounded-sm" style="background: #2196F3;"></span>
                                            <span class="tw-text-sm tw-text-gray-600">Sales:</span>
                                            <span class="tw-font-semibold tw-text-sm" id="pie_sales">{{ session('currency')['symbol'] }}0</span>
                                        </div>
                                        <div class="tw-flex tw-items-center tw-gap-2">
                                            <span class="tw-w-3 tw-h-3 tw-rounded-sm" style="background: #FF9800;"></span>
                                            <span class="tw-text-sm tw-text-gray-600">Income:</span>
                                            <span class="tw-font-semibold tw-text-sm" id="pie_income">{{ session('currency')['symbol'] }}0</span>
                                        </div>
                                        <div class="tw-flex tw-items-center tw-gap-2">
                                            <span class="tw-w-3 tw-h-3 tw-rounded-sm" style="background: #f44336;"></span>
                                            <span class="tw-text-sm tw-text-gray-600">Expense:</span>
                                            <span class="tw-font-semibold tw-text-sm" id="pie_expense">{{ session('currency')['symbol'] }}0</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if (session('business.enable_product_expiry') == 1)
                        <div
                            class="tw-transition-all lg:tw-col-span-1 tw-duration-200 tw-bg-white tw-shadow-sm tw-rounded-xl tw-ring-1 hover:tw-shadow-md hover:tw--translate-y-0.5 tw-ring-gray-200">
                            <div class="tw-p-4 sm:tw-p-5">
                                <div class="tw-flex tw-items-center tw-gap-2.5">
                                    <div
                                        class="tw-border-2 tw-flex tw-items-center tw-justify-center tw-rounded-full tw-w-10 tw-h-10">
                                        <svg aria-hidden="true" class="tw-text-yellow-500 tw-size-5 tw-shrink-0"
                                            xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2"
                                            stroke="currentColor" fill="none" stroke-linecap="round"
                                            stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                            <path d="M12 9v4"></path>
                                            <path
                                                d="M10.363 3.591l-8.106 13.534a1.914 1.914 0 0 0 1.636 2.871h16.214a1.914 1.914 0 0 0 1.636 -2.87l-8.106 -13.536a1.914 1.914 0 0 0 -3.274 0z">
                                            </path>
                                            <path d="M12 16h.01"></path>
                                        </svg>
                                    </div>
                                    <div class="tw-flex tw-items-center tw-flex-1 tw-min-w-0 tw-gap-1">
                                        <div class="tw-w-full sm:tw-w-1/2 md:tw-w-1/2">
                                            <h3 class="tw-font-bold tw-text-base lg:tw-text-xl">
                                                {{ __('home.stock_expiry_alert') }}
                                                @show_tooltip(
                                                __('tooltip.stock_expiry_alert', [
                                                'days'
                                                =>session('business.stock_expiry_alert_days', 30) ]) )
                                            </h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="tw-flow-root tw-mt-5  tw-border-gray-200">
                                    <div class="tw--mx-4 tw--my-2 tw-overflow-x-auto sm:tw--mx-5">
                                        <div class="tw-inline-block tw-min-w-full tw-py-2 tw-align-middle sm:tw-px-5">
                                            <input type="hidden" id="stock_expiry_alert_days"
                                                value="{{ \Carbon::now()->addDays(session('business.stock_expiry_alert_days', 30))->format('Y-m-d') }}">
                                            <table class="table table-bordered table-striped" id="stock_expiry_alert_table">
                                                <thead>
                                                    <tr>
                                                        <th>@lang('business.product')</th>
                                                        <th>@lang('business.location')</th>
                                                        <th>@lang('report.stock_left')</th>
                                                        <th>@lang('product.expires_in')</th>
                                                    </tr>
                                                </thead>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                @endcan
                {{-- New Widgets Phase 1 --}}
                <div class="tw-transition-all lg:tw-col-span-1 tw-duration-200 tw-bg-white tw-shadow-sm tw-rounded-xl tw-ring-1 hover:tw-shadow-md hover:tw--translate-y-0.5 tw-ring-gray-200">
                    <div class="tw-p-4 sm:tw-p-5">
                        <div class="tw-flex tw-items-center tw-justify-between tw-mb-4">
                            <h3 class="tw-font-bold tw-text-base lg:tw-text-xl tw-flex tw-items-center tw-gap-2">
                                <i class="fa fa-trophy tw-text-yellow-500"></i> Best Sellers (Weekly)
                            </h3>
                            <div class="tw-flex tw-gap-2">
                                <!-- DataTables buttons will appear here -->
                            </div>
                        </div>
                        <div class="tw-flow-root">
                            <table class="table table-bordered table-striped" id="best_sellers_table" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Qty Sold</th>
                                        <th>Total Revenue</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="tw-transition-all lg:tw-col-span-1 tw-duration-200 tw-bg-white tw-shadow-sm tw-rounded-xl tw-ring-1 hover:tw-shadow-md hover:tw--translate-y-0.5 tw-ring-gray-200">
                    <div class="tw-p-4 sm:tw-p-5">
                        <div class="tw-flex tw-items-center tw-justify-between tw-mb-4">
                            <h3 class="tw-font-bold tw-text-base lg:tw-text-xl tw-flex tw-items-center tw-gap-2">
                                <i class="fa fa-hourglass-end tw-text-red-500"></i> Expiring Soon
                            </h3>
                            <div class="tw-flex tw-items-center tw-gap-3">
                                <span class="tw-text-xs tw-text-gray-400">Next 30 days</span>
                                <!-- DataTables buttons will appear here -->
                            </div>
                        </div>
                        <div class="tw-flow-root">
                            <table class="table table-bordered table-striped" id="expiring_soon_table" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Expiry</th>
                                        <th>Stock</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="tw-transition-all lg:tw-col-span-1 tw-duration-200 tw-bg-white tw-shadow-sm tw-rounded-xl tw-ring-1 hover:tw-shadow-md hover:tw--translate-y-0.5 tw-ring-gray-200">
                    <div class="tw-p-4 sm:tw-p-5">
                        <div class="tw-flex tw-items-center tw-justify-between tw-mb-4">
                            <h3 class="tw-font-bold tw-text-base lg:tw-text-xl tw-flex tw-items-center tw-gap-2">
                                <i class="fa fa-shopping-cart tw-text-blue-500"></i> Reorder Suggestions
                            </h3>
                            <span class="tw-text-xs tw-text-gray-400">Based on sales velocity</span>
                        </div>
                        <div class="tw-flow-root">
                            <table class="table table-bordered table-striped" id="reorder_suggestions_table" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>In Stock</th>
                                        <th>Avg Daily</th>
                                        <th>Suggestion</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>

                @if (
                    !empty($common_settings['enable_purchase_requisition']) &&
                        (auth()->user()->can('purchase_requisition.view_all') || auth()->user()->can('purchase_requisition.view_own')))
                    <div
                        class="tw-transition-all lg:tw-col-span-1 tw-duration-200 tw-bg-white tw-shadow-sm tw-rounded-xl tw-ring-1 hover:tw-shadow-md hover:tw--translate-y-0.5 tw-ring-gray-200">
                        <div class="tw-p-4 sm:tw-p-5">
                            <div class="tw-flex tw-items-center tw-gap-2.5">
                                <div
                                    class="tw-border-2 tw-flex tw-items-center tw-justify-center tw-rounded-full tw-w-10 tw-h-10">
                                    <svg aria-hidden="true" class="tw-text-yellow-500 tw-size-5 tw-shrink-0"
                                        xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2"
                                        stroke="currentColor" fill="none" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                        <path d="M10 10v4a1 1 0 0 0 1 1h2a1 1 0 0 0 1 -1v-4"></path>
                                        <path d="M9 6h6"></path>
                                        <path d="M10 6v-2a1 1 0 0 1 1 -1h2a1 1 0 0 1 1 1v2"></path>
                                        <circle cx="12" cy="16" r="2"></circle>
                                        <path d="M5 20h14a2 2 0 0 0 2 -2v-10"></path>
                                        <path d="M15 16v4"></path>
                                        <path d="M9 20v-4"></path>
                                    </svg>
                                </div>
                                <div class="tw-flex tw-items-center tw-flex-1 tw-min-w-0 tw-gap-1">
                                    <div class="tw-w-full sm:tw-w-1/2 md:tw-w-1/2">
                                        <h3 class="tw-font-bold tw-text-base lg:tw-text-xl">
                                            @lang('lang_v1.purchase_requisition')
                                        </h3>
                                    </div>
                                    <div class="tw-w-full sm:tw-w-1/2 md:tw-w-1/2">
                                        @if (count($all_locations) > 1)
                                                {!! Form::select('pr_location', $all_locations, null, [
                                                    'class' => 'form-control select2',
                                                    'placeholder' => __('lang_v1.select_location'),
                                                    'id' => 'pr_location',
                                                ]) !!}
                                            @endif
                                    </div>
                                </div>
                            </div>
                            <div class="tw-flow-root tw-mt-5  tw-border-gray-200">
                                <div class="tw--mx-4 tw--my-2 tw-overflow-x-auto sm:tw--mx-5">
                                    <div class="tw-inline-block tw-min-w-full tw-py-2 tw-align-middle sm:tw-px-5">
                                        <table class="table table-bordered table-striped ajax_view"
                                            id="purchase_requisition_table" style="width: 100%;">
                                            <thead>
                                                <tr>
                                                    <th>@lang('messages.action')</th>
                                                    <th>@lang('messages.date')</th>
                                                    <th>@lang('purchase.ref_no')</th>
                                                    <th>@lang('purchase.location')</th>
                                                    <th>@lang('sale.status')</th>
                                                    <th>@lang('lang_v1.required_by_date')</th>
                                                    <th>@lang('lang_v1.added_by')</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if (
                    !empty($common_settings['enable_purchase_order']) &&
                        (auth()->user()->can('purchase_order.view_all') || auth()->user()->can('purchase_order.view_own')))

                    <div
                        class="tw-transition-all lg:tw-col-span-1 tw-duration-200 tw-bg-white tw-shadow-sm tw-rounded-xl tw-ring-1 hover:tw-shadow-md hover:tw--translate-y-0.5 tw-ring-gray-200">
                        <div class="tw-p-4 sm:tw-p-5">
                            <div class="tw-flex tw-items-center tw-gap-2.5">
                                <div
                                    class="tw-border-2 tw-flex tw-items-center tw-justify-center tw-rounded-full tw-w-10 tw-h-10">
                                    <svg aria-hidden="true" class="tw-text-yellow-500 tw-size-5 tw-shrink-0"
                                        xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2"
                                        stroke="currentColor" fill="none" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <rect x="4" y="4" width="16" height="16" rx="2" />
                                        <line x1="4" y1="10" x2="20" y2="10" />
                                        <line x1="12" y1="4" x2="12" y2="20" />
                                        <line x1="12" y1="10" x2="16" y2="10" />
                                    </svg>
                                </div>
                                <div class="tw-flex tw-items-center tw-flex-1 tw-min-w-0 tw-gap-1">
                                    <div class="tw-w-full sm:tw-w-1/2 md:tw-w-1/2">
                                        <h3 class="tw-font-bold tw-text-base lg:tw-text-xl">
                                            @lang('lang_v1.purchase_order')
                                        </h3>
                                    </div>
                                    <div class="tw-w-full sm:tw-w-1/2 md:tw-w-1/2">
                                        @if (count($all_locations) > 1)
                                            {!! Form::select('po_location', $all_locations, null, [
                                                'class' => 'form-control select2',
                                                'placeholder' => __('lang_v1.select_location'),
                                                'id' => 'po_location',
                                            ]) !!}
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="tw-flow-root tw-mt-5  tw-border-gray-200">
                                <div class="tw--mx-4 tw--my-2 tw-overflow-x-auto sm:tw--mx-5">
                                    <div class="tw-inline-block tw-min-w-full tw-py-2 tw-align-middle sm:tw-px-5">
                                        <table class="table table-bordered table-striped ajax_view"
                                            id="purchase_order_table" style="width: 100%;">
                                            <thead>
                                                <tr>
                                                    <th>@lang('messages.action')</th>
                                                    <th>@lang('messages.date')</th>
                                                    <th>@lang('purchase.ref_no')</th>
                                                    <th>@lang('purchase.location')</th>
                                                    <th>@lang('purchase.supplier')</th>
                                                    <th>@lang('sale.status')</th>
                                                    <th>@lang('lang_v1.quantity_remaining')</th>
                                                    <th>@lang('lang_v1.added_by')</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                @endif
                @if (auth()->user()->can('account.access') && config('constants.show_payments_recovered_today') == true)
                    <div
                        class="tw-transition-all lg:tw-col-span-2 tw-duration-200 tw-bg-white tw-shadow-sm tw-rounded-xl tw-ring-1 hover:tw-shadow-md hover:tw--translate-y-0.5 tw-ring-gray-200">
                        <div class="tw-p-4 sm:tw-p-5">
                            <div class="tw-flex tw-items-center tw-gap-2.5">
                                <div
                                    class="tw-border-2 tw-flex tw-items-center tw-justify-center tw-rounded-full tw-w-10 tw-h-10">
                                    <svg aria-hidden="true" class="tw-text-yellow-500 tw-size-5 tw-shrink-0"
                                        xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2"
                                        stroke="currentColor" fill="none" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                        <path d="M12 9v4"></path>
                                        <path
                                            d="M10.363 3.591l-8.106 13.534a1.914 1.914 0 0 0 1.636 2.871h16.214a1.914 1.914 0 0 0 1.636 -2.87l-8.106 -13.536a1.914 1.914 0 0 0 -3.274 0z">
                                        </path>
                                        <path d="M12 16h.01"></path>
                                    </svg>
                                </div>
                                <div class="tw-flex tw-items-center tw-flex-1 tw-min-w-0 tw-gap-1">
                                    <div class="tw-w-full sm:tw-w-1/2 md:tw-w-1/2">
                                        <h3 class="tw-font-bold tw-text-base lg:tw-text-xl">
                                            @lang('lang_v1.payment_recovered_today')
                                        </h3>
                                    </div>

                                </div>
                            </div>
                            <div class="tw-flow-root tw-mt-5  tw-border-gray-200">
                                <div class="tw--mx-4 tw--my-2 tw-overflow-x-auto sm:tw--mx-5">
                                    <div class="tw-inline-block tw-min-w-full tw-py-2 tw-align-middle sm:tw-px-5">
                                        <table class="table table-bordered table-striped" id="cash_flow_table">
                                            <thead>
                                                <tr>
                                                    <th>@lang('messages.date')</th>
                                                    <th>@lang('account.account')</th>
                                                    <th>@lang('lang_v1.description')</th>
                                                    <th>@lang('lang_v1.payment_method')</th>
                                                    <th>@lang('lang_v1.payment_details')</th>
                                                    <th>@lang('account.credit')</th>
                                                    <th>@lang('lang_v1.account_balance')
                                                        @show_tooltip(__('lang_v1.account_balance_tooltip'))</th>
                                                    <th>@lang('lang_v1.total_balance')
                                                        @show_tooltip(__('lang_v1.total_balance_tooltip'))</th>
                                                </tr>
                                            </thead>
                                            <tfoot>
                                                <tr class="bg-gray font-17 footer-total text-center">
                                                    <td colspan="5"><strong>@lang('sale.total'):</strong></td>
                                                    <td class="footer_total_credit"></td>
                                                    <td colspan="2"></td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                {{-- @if (!empty($widgets['after_dashboard_reports']))
                    @foreach ($widgets['after_dashboard_reports'] as $widget)
                        {!! $widget !!}
                    @endforeach
                @endif --}}

                {{-- ===== RECENT TRANSACTIONS WIDGET ===== --}}
                @can('sell.view')
                <div class="tw-transition-all lg:tw-col-span-2 tw-duration-200 tw-bg-white tw-shadow-sm tw-rounded-xl tw-ring-1 hover:tw-shadow-md tw-ring-gray-200">
                    <div class="tw-p-4 sm:tw-p-5">
                        <div class="tw-flex tw-items-center tw-justify-between tw-mb-4">
                            <div class="tw-flex tw-items-center tw-gap-2.5">
                                <div class="tw-border-2 tw-border-indigo-200 tw-flex tw-items-center tw-justify-center tw-rounded-full tw-w-10 tw-h-10 tw-bg-indigo-50">
                                    <i class="fas fa-clock tw-text-indigo-500" style="font-size:1rem;"></i>
                                </div>
                                <h3 class="tw-font-bold tw-text-base lg:tw-text-xl">Recent Transactions</h3>
                            </div>
                            <a href="{{ action([\App\Http\Controllers\SellController::class, 'index']) }}"
                               class="tw-text-xs tw-font-semibold tw-text-indigo-600 hover:tw-text-indigo-800 tw-no-underline"
                               style="text-decoration:none;">
                                View All &rarr;
                            </a>
                        </div>

                        {{-- Summary strip --}}
                        <div id="recent_tx_summary"
                             style="display:grid;grid-template-columns:repeat(3,1fr);gap:0.75rem;margin-bottom:1rem;">
                            <div style="background:#f0fdf4;border-radius:0.75rem;padding:0.75rem 1rem;text-align:center;">
                                <p style="font-size:0.75rem;color:#6b7280;margin:0 0 0.2rem;">Today's Sales</p>
                                <p id="rt_today_sales" style="font-size:1.1rem;font-weight:700;color:#16a34a;margin:0;">
                                    <span class="tw-animate-pulse">—</span>
                                </p>
                            </div>
                            <div style="background:#eff6ff;border-radius:0.75rem;padding:0.75rem 1rem;text-align:center;">
                                <p style="font-size:0.75rem;color:#6b7280;margin:0 0 0.2rem;">Transactions</p>
                                <p id="rt_today_count" style="font-size:1.1rem;font-weight:700;color:#2563eb;margin:0;">
                                    <span class="tw-animate-pulse">—</span>
                                </p>
                            </div>
                            <div style="background:#fdf4ff;border-radius:0.75rem;padding:0.75rem 1rem;text-align:center;">
                                <p style="font-size:0.75rem;color:#6b7280;margin:0 0 0.2rem;">Avg. Order Value</p>
                                <p id="rt_avg_order" style="font-size:1.1rem;font-weight:700;color:#9333ea;margin:0;">
                                    <span class="tw-animate-pulse">—</span>
                                </p>
                            </div>
                        </div>

                        {{-- Table --}}
                        <div class="tw-overflow-x-auto">
                            <table class="table table-bordered table-striped" id="recent_transactions_table" style="width:100%;">
                                <thead>
                                    <tr>
                                        <th>@lang('messages.date')</th>
                                        <th>@lang('sale.invoice_no')</th>
                                        <th>@lang('contact.customer')</th>
                                        <th>@lang('sale.location')</th>
                                        <th>@lang('sale.payment_status')</th>
                                        <th>@lang('sale.total_amount')</th>
                                        <th>@lang('messages.action')</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
                @endcan
                {{-- ===== END RECENT TRANSACTIONS ===== --}}

            </div>
        </div>
    @endif
    </div>
@endsection


<div class="modal fade payment_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
</div>
<div class="modal fade edit_pso_status_modal" tabindex="-1" role="dialog"></div>
<div class="modal fade edit_payment_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
</div>

@section('css')
    <link rel="stylesheet" href="{{ asset('css/dashboard-fortypos.css?v=' . $asset_v) }}">
    <style>
        .select2-container {
            width: 100% !important;
        }
    </style>
@endsection

@section('javascript')
    <script src="{{ asset('js/home.js?v=' . $asset_v) }}"></script>
    <script src="{{ asset('js/payment.js?v=' . $asset_v) }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    @includeIf('sales_order.common_js')
    @includeIf('purchase_order.common_js')

    <script type="text/javascript">
        var overallReportsChart = null;

        function loadOverallReports() {
            var year = $('#overall_reports_year').val() || new Date().getFullYear();
            var location_id = $('#dashboard_location').length > 0 ? $('#dashboard_location').val() : '';

            $.ajax({
                url: '/home/overall-reports',
                data: { year: year, location_id: location_id },
                success: function(data) {
                    var currencySymbol = '{{ session("currency")["symbol"] }}';
                    $('#pie_purchase').text(currencySymbol + Number(data.purchase || 0).toLocaleString());
                    $('#pie_sales').text(currencySymbol + Number(data.sales || 0).toLocaleString());
                    $('#pie_income').text(currencySymbol + Number(data.income || 0).toLocaleString());
                    $('#pie_expense').text(currencySymbol + Number(data.expense || 0).toLocaleString());

                    // Draw pie chart
                    var ctx = document.getElementById('overall_reports_chart');
                    if (ctx) {
                        if (overallReportsChart) {
                            overallReportsChart.destroy();
                        }

                        overallReportsChart = new Chart(ctx, {
                            type: 'doughnut',
                            data: {
                                labels: ['Purchase', 'Sales', 'Income', 'Expense'],
                                datasets: [{
                                    data: [data.purchase || 0, data.sales || 0, data.income || 0, data.expense || 0],
                                    backgroundColor: ['#4CAF50', '#2196F3', '#FF9800', '#f44336'],
                                    borderWidth: 0
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: {
                                        display: false
                                    }
                                },
                                cutout: '60%'
                            }
                        });
                    }
                }
            });
        }

        $(document).ready(function() {
            // Load overall reports chart
            loadOverallReports();

            // ===== RECENT TRANSACTIONS TABLE =====
            @can('sell.view')
            var recent_transactions_table = $('#recent_transactions_table').DataTable({
                processing: true,
                serverSide: true,
                fixedHeader: false,
                aaSorting: [[0, 'desc']],
                pageLength: 10,
                ajax: {
                    url: '{{ action([\App\Http\Controllers\SellController::class, "index"]) }}',
                    data: function(d) {
                        d.per_page = 10;
                        d.from_dashboard = true;
                    }
                },
                columns: [
                    { data: 'transaction_date', name: 'transaction_date' },
                    { data: 'invoice_no',       name: 'invoice_no' },
                    { data: 'name',             name: 'c.name' },
                    { data: 'location_name',    name: 'bl.name' },
                    { data: 'payment_status',   name: 'payment_status', orderable: false },
                    { data: 'final_total',      name: 'final_total' },
                    { data: 'action',           name: 'action', orderable: false, searchable: false },
                ],
                fnDrawCallback: function(oSettings) {
                    __currency_convert_recursively($('#recent_transactions_table'));

                    // Populate summary strip from totals already fetched
                    if (oSettings.json) {
                        var sym = '{{ session("currency")["symbol"] }}';
                        var count = oSettings.json.recordsFiltered || 0;
                        var total = 0;
                        var rows = oSettings.json.data || [];
                        rows.forEach(function(r) {
                            var raw = $(r.final_total).data('orig-value');
                            if (raw) total += parseFloat(raw);
                        });
                        var avg = rows.length > 0 ? (total / rows.length) : 0;
                        $('#rt_today_count').text(count);
                        $('#rt_today_sales').text(sym + __number_f(total));
                        $('#rt_avg_order').text(sym + __number_f(avg));
                    }
                }
            });
            @endcan
            // ===== END RECENT TRANSACTIONS ====

            $('#overall_reports_year').change(function() {
                loadOverallReports();
            });

            $('#dashboard_location').change(function() {
                loadOverallReports();
            });
            @if (auth()->user()->can('account.access') && config('constants.show_payments_recovered_today') == true)

                // Cash Flow Table
                cash_flow_table = $('#cash_flow_table').DataTable({
                    processing: true,
                    serverSide: true,
                    fixedHeader:false,
                    "ajax": {
                        "url": "{{ action([\App\Http\Controllers\AccountController::class, 'cashFlow']) }}",
                        "data": function(d) {
                            d.type = 'credit';
                            d.only_payment_recovered = true;
                        }
                    },
                    "ordering": false,
                    "searching": false,
                    columns: [{
                            data: 'operation_date',
                            name: 'operation_date'
                        },
                        {
                            data: 'account_name',
                            name: 'account_name'
                        },
                        {
                            data: 'sub_type',
                            name: 'sub_type'
                        },
                        {
                            data: 'method',
                            name: 'TP.method'
                        },
                        {
                            data: 'payment_details',
                            name: 'payment_details',
                            searchable: false
                        },
                        {
                            data: 'credit',
                            name: 'amount'
                        },
                        {
                            data: 'balance',
                            name: 'balance'
                        },
                        {
                            data: 'total_balance',
                            name: 'total_balance'
                        },
                    ],
                    "fnDrawCallback": function(oSettings) {
                        __currency_convert_recursively($('#cash_flow_table'));
                    },
                    "footerCallback": function(row, data, start, end, display) {
                        var footer_total_credit = 0;

                        for (var r in data) {
                            footer_total_credit += $(data[r].credit).data('orig-value') ? parseFloat($(
                                data[r].credit).data('orig-value')) : 0;
                        }
                        $('.footer_total_credit').html(__currency_trans_from_en(footer_total_credit));
                    }
                });
            @endif

            @if (!empty($common_settings['enable_purchase_order']))
                //Purchase table
                purchase_order_table = $('#purchase_order_table').DataTable({
                    processing: true,
                    serverSide: true,
                    fixedHeader:false,
                    aaSorting: [
                        [1, 'desc']
                    ],
                    scrollY: "75vh",
                    scrollX: true,
                    scrollCollapse: true,
                    ajax: {
                        url: '{{ action([\App\Http\Controllers\PurchaseOrderController::class, 'index']) }}',
                        data: function(d) {
                            d.from_dashboard = true;

                            if ($('#po_location').length > 0) {
                                d.location_id = $('#po_location').val();
                            }
                        },
                    },
                    columns: [{
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'transaction_date',
                            name: 'transaction_date'
                        },
                        {
                            data: 'ref_no',
                            name: 'ref_no'
                        },
                        {
                            data: 'location_name',
                            name: 'BS.name'
                        },
                        {
                            data: 'name',
                            name: 'contacts.name'
                        },
                        {
                            data: 'status',
                            name: 'transactions.status'
                        },
                        {
                            data: 'po_qty_remaining',
                            name: 'po_qty_remaining',
                            "searchable": false
                        },
                        {
                            data: 'added_by',
                            name: 'u.first_name'
                        }
                    ]
                })

                $('#po_location').change(function() {
                    purchase_order_table.ajax.reload();
                });
            @endif

            @if (!empty($common_settings['enable_purchase_requisition']))
                //Purchase table
                purchase_requisition_table = $('#purchase_requisition_table').DataTable({
                    processing: true,
                    serverSide: true,
                    fixedHeader:false,
                    aaSorting: [
                        [1, 'desc']
                    ],
                    scrollY: "75vh",
                    scrollX: true,
                    scrollCollapse: true,
                    ajax: {
                        url: '{{ action([\App\Http\Controllers\PurchaseRequisitionController::class, 'index']) }}',
                        data: function(d) {
                            d.from_dashboard = true;

                            if ($('#pr_location').length > 0) {
                                d.location_id = $('#pr_location').val();
                            }
                        },
                    },
                    columns: [{
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'transaction_date',
                            name: 'transaction_date'
                        },
                        {
                            data: 'ref_no',
                            name: 'ref_no'
                        },
                        {
                            data: 'location_name',
                            name: 'BS.name'
                        },
                        {
                            data: 'status',
                            name: 'status'
                        },
                        {
                            data: 'delivery_date',
                            name: 'delivery_date'
                        },
                        {
                            data: 'added_by',
                            name: 'u.first_name'
                        },
                    ]
                })

                $('#pr_location').change(function() {
                    purchase_requisition_table.ajax.reload();
                });

                $(document).on('click', 'a.delete-purchase-requisition', function(e) {
                    e.preventDefault();
                    swal({
                        title: LANG.sure,
                        icon: 'warning',
                        buttons: true,
                        dangerMode: true,
                    }).then(willDelete => {
                        if (willDelete) {
                            var href = $(this).attr('href');
                            $.ajax({
                                method: 'DELETE',
                                url: href,
                                dataType: 'json',
                                success: function(result) {
                                    if (result.success == true) {
                                        toastr.success(result.msg);
                                        purchase_requisition_table.ajax.reload();
                                    } else {
                                        toastr.error(result.msg);
                                    }
                                },
                            });
                        }
                    });
                });
            @endif

            sell_table = $('#shipments_table').DataTable({
                processing: true,
                serverSide: true,
                fixedHeader:false,
                aaSorting: [
                    [1, 'desc']
                ],
                scrollY: "75vh",
                scrollX: true,
                scrollCollapse: true,
                "ajax": {
                    "url": '{{ action([\App\Http\Controllers\SellController::class, 'index']) }}',
                    "data": function(d) {
                        d.only_pending_shipments = true;
                        if ($('#pending_shipments_location').length > 0) {
                            d.location_id = $('#pending_shipments_location').val();
                        }
                    }
                },
                columns: [{
                        data: 'action',
                        name: 'action',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'transaction_date',
                        name: 'transaction_date'
                    },
                    {
                        data: 'invoice_no',
                        name: 'invoice_no'
                    },
                    {
                        data: 'conatct_name',
                        name: 'conatct_name'
                    },
                    {
                        data: 'mobile',
                        name: 'contacts.mobile'
                    },
                    {
                        data: 'business_location',
                        name: 'bl.name'
                    },
                    {
                        data: 'shipping_status',
                        name: 'shipping_status'
                    },
                    @if (!empty($custom_labels['shipping']['custom_field_1']))
                        {
                            data: 'shipping_custom_field_1',
                            name: 'shipping_custom_field_1'
                        },
                    @endif
                    @if (!empty($custom_labels['shipping']['custom_field_2']))
                        {
                            data: 'shipping_custom_field_2',
                            name: 'shipping_custom_field_2'
                        },
                    @endif
                    @if (!empty($custom_labels['shipping']['custom_field_3']))
                        {
                            data: 'shipping_custom_field_3',
                            name: 'shipping_custom_field_3'
                        },
                    @endif
                    @if (!empty($custom_labels['shipping']['custom_field_4']))
                        {
                            data: 'shipping_custom_field_4',
                            name: 'shipping_custom_field_4'
                        },
                    @endif
                    @if (!empty($custom_labels['shipping']['custom_field_5']))
                        {
                            data: 'shipping_custom_field_5',
                            name: 'shipping_custom_field_5'
                        },
                    @endif {
                        data: 'payment_status',
                        name: 'payment_status'
                    },
                    {
                        data: 'waiter',
                        name: 'ss.first_name',
                        @if (empty($is_service_staff_enabled))
                            visible: false
                        @endif
                    }
                ],
                "fnDrawCallback": function(oSettings) {
                    __currency_convert_recursively($('#sell_table'));
                },
                createdRow: function(row, data, dataIndex) {
                    $(row).find('td:eq(4)').attr('class', 'clickable_td');
                }
            });

            $('#pending_shipments_location').change(function() {
                sell_table.ajax.reload();
            });
        });
    </script>
    <script type="text/javascript">
        $(document).ready(function() {
            var quotation_datatable;
            if ($('#quotation_table').length) {
                quotation_datatable = $('#quotation_table').DataTable({
                    processing: true,
                    serverSide: true,
                    fixedHeader: false,
                    aaSorting: [
                        [0, 'desc']
                    ],
                    "ajax": {
                        "url": '/sells/draft-dt?is_quotation=1',
                        "data": function(d) {
                            if ($('#dashboard_location').length > 0) {
                                d.location_id = $('#dashboard_location').val();
                            }
                        }
                    },
                    columnDefs: [{
                        "targets": 4,
                        "orderable": false,
                        "searchable": false
                    }],
                    columns: [{
                            data: 'transaction_date',
                            name: 'transaction_date'
                        },
                        {
                            data: 'invoice_no',
                            name: 'invoice_no'
                        },
                        {
                            data: 'name',
                            name: 'contacts.name'
                        },
                        {
                            data: 'business_location',
                            name: 'bl.name'
                        },
                        {
                            data: 'action',
                            name: 'action'
                        }
                    ]
                });
            }

            function updateLiveStats() {
                var location_id = $('#dashboard_location').val();
                $.ajax({
                    url: '{{ action([\App\Http\Controllers\HomeController::class, "getLiveStats"]) }}',
                    data: { location_id: location_id },
                    success: function(data) {
                        $('#ticker_today_sales').text('{{ session("currency")["symbol"] }} ' + data.today_sales);
                        $('#ticker_today_transactions').text(data.today_transactions);
                        $('#ticker_last_sale').text('Last Sale: {{ session("currency")["symbol"] }} ' + data.last_sale_amount + ' (' + data.last_sale_time + ')');
                        $('#ticker_timestamp').text(data.timestamp);
                    }
                });
            }

            var best_sellers_table;
            function loadBestSellers() {
                if ($.fn.DataTable.isDataTable('#best_sellers_table')) {
                    $('#best_sellers_table').DataTable().ajax.reload();
                    return;
                }
                best_sellers_table = $('#best_sellers_table').DataTable({
                    processing: true,
                    serverSide: true,
                    ordering: false,
                    searching: false,
                    dom: 'Brtip',
                    buttons: ['copy', 'csv', 'excel', 'pdf', 'print', 'colvis'],
                    ajax: {
                        url: '/home/best-sellers',
                        data: function(d) {
                            d.location_id = $('#dashboard_location').val();
                        }
                    },
                    columns: [
                        { 
                            data: 'name', 
                            name: 'p.name',
                            render: function(data, type, row) {
                                var image = row.image ? '/uploads/img/' + row.image : null;
                                var sku = row.sku ? row.sku : '';
                                var name = data ? data : '';
                                var html = '<div class="tw-flex tw-items-center tw-gap-3">' +
                                    '<div class="tw-w-8 tw-h-8 tw-rounded tw-bg-gray-100 tw-flex tw-items-center tw-justify-center tw-overflow-hidden">' +
                                        (image ? '<img src="' + image + '" class="tw-object-cover tw-w-full tw-h-full">' : '<i class="fa fa-image tw-text-gray-300"></i>') +
                                    '</div>' +
                                    '<div>' +
                                        '<p class="tw-font-semibold tw-text-gray-900">' + name + '</p>' +
                                        '<p class="tw-text-xs tw-text-gray-500">' + sku + '</p>' +
                                    '</div>' +
                                '</div>';
                                return html;
                            }
                        },
                        { 
                            data: 'total_qty', 
                            name: 'total_qty',
                            render: function(data) {
                                var val = parseFloat(data || 0);
                                return '<span class="tw-font-bold text-primary">' + val.toFixed(0) + '</span>';
                            }
                        },
                        { 
                            data: 'total_revenue', 
                            name: 'total_revenue',
                            render: function(data) {
                                var val = parseFloat(data || 0);
                                return '<span class="tw-text-green-600">{{ session("currency")["symbol"] }} ' + val.toLocaleString() + '</span>';
                            }
                        }
                    ]
                });
            }

            var expiring_soon_table;
            function loadExpiringSoon() {
                if ($.fn.DataTable.isDataTable('#expiring_soon_table')) {
                    $('#expiring_soon_table').DataTable().ajax.reload();
                    return;
                }
                expiring_soon_table = $('#expiring_soon_table').DataTable({
                    processing: true,
                    serverSide: true,
                    ordering: false,
                    searching: false,
                    dom: 'Brtip',
                    buttons: ['copy', 'csv', 'excel', 'pdf', 'print', 'colvis'],
                    ajax: {
                        url: '/home/expiring-products',
                        data: function(d) {
                            d.location_id = $('#dashboard_location').val();
                        }
                    },
                    columns: [
                        { 
                            data: 'name', 
                            name: 'p.name',
                            render: function(data, type, row) {
                                return '<div class="tw-text-sm">' +
                                    '<p class="tw-font-semibold tw-text-gray-900">' + (data || '') + '</p>' +
                                    '<p class="tw-text-xs tw-text-gray-500">Lot: ' + (row.lot_number || "N/A") + '</p>' +
                                '</div>';
                            }
                        },
                        { 
                            data: 'exp_date', 
                            name: 'pl.exp_date',
                            render: function(data, type, row) {
                                var days = parseInt(row.days_until_expiry || 0);
                                var colorClass = days <= 7 ? 'tw-text-red-600' : 'tw-text-yellow-600';
                                return '<div class="tw-text-sm">' +
                                    '<p class="tw-font-bold ' + colorClass + '">' + (data || '') + '</p>' +
                                    '<p class="tw-text-xs tw-text-gray-500">' + days + ' days left</p>' +
                                '</div>';
                            }
                        },
                        { 
                            data: 'qty_remaining', 
                            name: 'qty_remaining',
                            render: function(data) {
                                var val = parseFloat(data || 0);
                                return '<span class="badge badge-info">' + val.toFixed(0) + '</span>';
                            }
                        }
                    ]
                });
            }

            var reorder_suggestions_table;
            function loadReorderSuggestions() {
                if ($.fn.DataTable.isDataTable('#reorder_suggestions_table')) {
                    $('#reorder_suggestions_table').DataTable().ajax.reload();
                    return;
                }
                reorder_suggestions_table = $('#reorder_suggestions_table').DataTable({
                    processing: true,
                    serverSide: true,
                    ordering: false,
                    searching: false,
                    dom: 'Brtip',
                    buttons: ['copy', 'csv', 'excel', 'pdf', 'print', 'colvis'],
                    ajax: {
                        url: '/home/reorder-suggestions',
                        data: function(d) {
                            d.location_id = $('#dashboard_location').val();
                        }
                    },
                    columns: [
                        { 
                            data: 'name', 
                            name: 'p.name',
                            render: function(data, type, row) {
                                return '<span class="tw-text-sm">' + (data || '') + ' <small>(' + (row.sku || '') + ')</small></span>';
                            }
                        },
                        { 
                            data: 'current_stock', 
                            name: 'vld.qty_available',
                            render: function(data) {
                                var val = parseFloat(data || 0);
                                return '<span class="badge badge-warning">' + val.toFixed(0) + '</span>';
                            }
                        },
                        { 
                            data: 'avg_daily_sales', 
                            name: 'avg_daily_sales',
                            render: function(data) {
                                var val = parseFloat(data || 0);
                                return '<span class="tw-text-sm">' + val.toFixed(2) + '</span>';
                            }
                        },
                        { 
                            data: 'suggested_reorder_qty', 
                            name: 'suggested_reorder_qty',
                            render: function(data) {
                                var val = parseFloat(data || 0);
                                return '<span class="tw-font-bold tw-text-blue-600">Reorder ' + val.toFixed(0) + '</span>';
                            }
                        }
                    ]
                });
            }

            // Initial load
            updateLiveStats();
            loadBestSellers();
            loadExpiringSoon();
            loadReorderSuggestions();

            // Refresh live stats every 30 seconds
            setInterval(updateLiveStats, 30000);

            // Reload widgets when location changes
            $('#dashboard_location').change(function() {
                updateLiveStats();
                loadBestSellers();
                loadExpiringSoon();
                loadReorderSuggestions();
                if (typeof quotation_datatable !== 'undefined') {
                    quotation_datatable.ajax.reload();
                }
            });
        });
    </script>
    @if (!empty($all_locations))
        {!! $sells_chart_1->script() !!}
        {!! $sells_chart_2->script() !!}
        {!! $staff_performance_chart->script() !!}
        {!! $profit_margin_chart->script() !!}
    @endif
@endsection

