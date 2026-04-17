<!-- default value -->
@php
    $go_back_url = action([\App\Http\Controllers\SellPosController::class, 'index']);
    $transaction_sub_type = '';
    $view_suspended_sell_url = action([\App\Http\Controllers\SellController::class, 'index']) . '?suspended=1';
    $pos_redirect_url = action([\App\Http\Controllers\SellPosController::class, 'create']);
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
@php
    $theme_color = session('business.theme_color', 'blue');
    $colors = [
        'blue' => '#3c8dbc',
        'black' => '#222d32',
        'purple' => '#605ca8',
        'green' => '#00a65a',
        'red' => '#dd4b39',
        'yellow' => '#f39c12',
        'blue-light' => '#3c8dbc',
        'black-light' => '#222d32',
        'purple-light' => '#605ca8',
        'green-light' => '#00a65a',
        'red-light' => '#dd4b39',
        'yellow-light' => '#f39c12',
    ];
    $primary_hex = $colors[$theme_color] ?? '#3c8dbc';
@endphp
<style>
    :root {
        --pos-primary: {{ $primary_hex }};
        --pos-primary-dark: {{ $primary_hex }}; /* Simplified for now */
    }
    .pos-header {
        background-color: var(--pos-primary) !important;
        background-image: linear-gradient(to right, var(--pos-primary), var(--pos-primary-dark)) !important;
    }
</style>
<div class="no-print pos-header app-bar" style="height: 64px; display: flex; align-items: center; padding: 0 12px; box-shadow: var(--pos-shadow-lg); color: white; position: sticky; top: 0; z-index: 1000;">
    <input type="hidden" id="pos_redirect_url" value="{{ $pos_redirect_url }}">

    {{-- Left Side: Location --}}
    <div style="display: flex; align-items: center; gap: 10px; flex-shrink: 0;">
        <div class="md-chip" style="background: rgba(255,255,255,0.2);">
            <i class="material-icons" style="font-size: 18px; margin-right: 6px;">place</i>
            @if (empty($transaction->location_id))
                @if (count($business_locations) > 1)
                    {!! Form::select(
                        'select_location_id',
                        $business_locations,
                        $default_location->id ?? null,
                        ['class' => 'form-control input-sm', 'id' => 'select_location_id', 'required', 'autofocus', 'style' => 'background: transparent; border: none; color: white; height: auto; padding: 0; cursor: pointer;'],
                        $bl_attributes,
                    ) !!}
                @else
                    {{ $default_location->name }}
                @endif
            @else
                {{ $transaction->location->name }}
            @endif
        </div>
    </div>

    {{-- Centre: Business Name --}}
    <div style="flex: 1; text-align: center;">
        <span style="font-size: 20px; font-weight: 600; letter-spacing: 0.5px;">{{ Session::get('business.name', 'Point of Sale') }}</span>
        <div style="font-size: 11px; opacity: 0.8; margin-top: -2px;">
            <i class="material-icons" style="font-size: 12px; vertical-align: middle;">schedule</i>
            <span class="curr_datetime">{{ @format_datetime('now') }}</span>
        </div>
    </div>

    {{-- Right Side: Action Buttons --}}
    <div style="display: flex; align-items: center; gap: 6px; flex-shrink: 0;">
        {{-- Custom Module Icons --}}
        @if (!empty($pos_module_data))
            @foreach ($pos_module_data as $key => $value)
                @if (in_array('pos_sale', $enabled_modules) && !empty($transaction_sub_type))
                    @can('sell.create')
                        <a href="{{ action([\App\Http\Controllers\SellPosController::class, 'create']) }}" class="md-icon-btn" title="@lang('sale.pos_sale')">
                            <i class="material-icons">apps</i>
                        </a>
                    @endcan
                @endif
            @endforeach
        @endif

        {{-- Go Back --}}
        <a href="{{ $go_back_url }}" class="md-icon-btn" title="{{ __('lang_v1.go_back') }}">
            <i class="material-icons">arrow_back</i>
        </a>

        {{-- Add Expense --}}
        @can('expense.access')
            <button type="button" id="add_expense" class="md-icon-btn" title="Add Expense">
                <i class="material-icons">receipt_long</i>
            </button>
        @endcan

        {{-- Recent Transactions --}}
        <button type="button" class="md-icon-btn" data-toggle="modal" data-target="#recent_transactions_modal" id="header-recent-transactions" title="{{ __('lang_v1.recent_transactions') }}">
            <i class="material-icons">history</i>
        </button>

        {{-- Register Details --}}
        @can('view_cash_register')
            <button type="button" id="register_details" class="md-icon-btn btn-modal" data-container=".register_details_modal" data-href="{{ action([\App\Http\Controllers\CashRegisterController::class, 'getRegisterDetails']) }}" title="{{ __('cash_register.register_details') }}">
                <i class="material-icons">assignment_ind</i>
            </button>
        @endcan

        {{-- Calculator --}}
        <button type="button" id="btnCalculator" class="md-icon-btn popover-default" data-toggle="popover" data-trigger="click" data-content='@include('layouts.partials.calculator')' data-html="true" data-placement="bottom" title="@lang('lang_v1.calculator')">
            <i class="material-icons">calculate</i>
        </button>

        {{-- Full Screen --}}
        <button type="button" id="full_screen" class="md-icon-btn" title="{{ __('lang_v1.full_screen') }}">
            <i class="material-icons">fullscreen</i>
        </button>

        {{-- Suspend Button --}}
        @if (empty($pos_settings['disable_suspend']))
            <button type="button" id="view_suspended_sales" class="md-icon-btn btn-modal" data-container=".view_modal" data-href="{{ $view_suspended_sell_url }}" title="{{ __('lang_v1.view_suspended_sales') }}">
                <i class="material-icons">pause_circle_outline</i>
            </button>
        @endif

        {{-- Close Register --}}
        @can('close_cash_register')
            <button type="button" id="close_register" class="md-icon-btn btn-modal" data-container=".close_register_modal" data-href="{{ action([\App\Http\Controllers\CashRegisterController::class, 'getCloseRegister']) }}" title="{{ __('cash_register.close_register') }}" style="color: #ff5252;">
                <i class="material-icons">power_settings_new</i>
            </button>
        @endcan
    </div>
</div>

<div class="modal fade" id="service_staff_modal" tabindex="-1" role="dialog"
    aria-labelledby="gridSystemModalLabel">
</div>
