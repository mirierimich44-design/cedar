@extends('layouts.auth')
@section('title', 'Get Started')

@section('content')
<div class="register-page tw-min-h-screen tw-bg-gradient-to-br tw-from-blue-900 tw-via-blue-800 tw-to-indigo-900 tw-flex tw-items-center tw-justify-center tw-py-8">
<div class="tw-w-full tw-max-w-5xl tw-mx-auto tw-px-4">

    <!-- Logo / Header -->
    <div class="tw-text-center tw-mb-8">
        <h1 class="tw-text-4xl tw-font-extrabold tw-text-white tw-tracking-tight">{{ config('app.name', 'Reenson') }}</h1>
        <p class="tw-text-blue-200 tw-mt-1">Set up your business in under 2 minutes</p>
    </div>

    <!-- STEP 1: Business Type -->
    <div id="step1" class="step">
        <div class="tw-text-center tw-mb-6">
            <span class="tw-inline-flex tw-items-center tw-justify-center tw-w-8 tw-h-8 tw-bg-blue-500 tw-text-white tw-rounded-full tw-font-bold tw-mr-2">1</span>
            <span class="tw-text-white tw-text-xl tw-font-semibold">What kind of business are you?</span>
        </div>

        <div class="tw-grid tw-grid-cols-2 md:tw-grid-cols-3 tw-gap-4">

            @php
            $business_types = [
                ['key' => 'retail',    'icon' => '🛒', 'label' => 'Retail Shop',        'desc' => 'Supermarket, General store, Hardware'],
                ['key' => 'restaurant','icon' => '🍽️', 'label' => 'Restaurant / Cafe',  'desc' => 'Food court, Hotel, Fast food, Bar'],
                ['key' => 'courier',   'icon' => '📦', 'label' => 'Courier & Logistics', 'desc' => 'Parcel delivery, Cargo, Freight'],
                ['key' => 'clinic',    'icon' => '🏥', 'label' => 'Clinic / Hospital',   'desc' => 'Medical centre, Pharmacy, Dental'],
                ['key' => 'garage',    'icon' => '🔧', 'label' => 'Garage / Workshop',   'desc' => 'Auto repair, Electronics, Repairs'],
                ['key' => 'salon',     'icon' => '💇', 'label' => 'Salon / Spa',         'desc' => 'Hair salon, Beauty, Barbershop'],
                ['key' => 'wholesale', 'icon' => '🏭', 'label' => 'Wholesale / Distributor', 'desc' => 'Bulk goods, Distribution'],
                ['key' => 'services',  'icon' => '💼', 'label' => 'Professional Services','desc' => 'Consulting, Accounting, Legal'],
                ['key' => 'other',     'icon' => '✨',  'label' => 'Other Business',      'desc' => 'Any other type of business'],
            ];
            @endphp

            @foreach($business_types as $bt)
            <div class="business-type-card tw-bg-white tw-bg-opacity-10 tw-border-2 tw-border-transparent tw-rounded-2xl tw-p-5 tw-cursor-pointer tw-transition-all tw-duration-200 hover:tw-bg-opacity-20 hover:tw-border-blue-300 hover:tw-scale-105"
                data-type="{{ $bt['key'] }}" onclick="selectBusinessType('{{ $bt['key'] }}')">
                <div class="tw-text-4xl tw-mb-2">{{ $bt['icon'] }}</div>
                <div class="tw-text-white tw-font-bold tw-text-base">{{ $bt['label'] }}</div>
                <div class="tw-text-blue-200 tw-text-xs tw-mt-1">{{ $bt['desc'] }}</div>
                <div class="checkmark tw-hidden tw-mt-2">
                    <span class="tw-inline-block tw-bg-green-500 tw-text-white tw-rounded-full tw-px-2 tw-py-0.5 tw-text-xs">✓ Selected</span>
                </div>
            </div>
            @endforeach
        </div>

        <div class="tw-text-center tw-mt-6">
            <button id="step1_next" onclick="goToStep2()" disabled
                class="tw-px-8 tw-py-3 tw-bg-blue-500 tw-text-white tw-font-bold tw-rounded-full tw-text-lg disabled:tw-opacity-40 disabled:tw-cursor-not-allowed tw-transition-all">
                Continue →
            </button>
        </div>
    </div>

    <!-- STEP 2: Account Details -->
    <div id="step2" class="step tw-hidden">
        <div class="tw-text-center tw-mb-6">
            <span class="tw-inline-flex tw-items-center tw-justify-center tw-w-8 tw-h-8 tw-bg-blue-500 tw-text-white tw-rounded-full tw-font-bold tw-mr-2">2</span>
            <span class="tw-text-white tw-text-xl tw-font-semibold">Create your account</span>
            <button onclick="goToStep1()" class="tw-ml-3 tw-text-blue-300 tw-text-sm hover:tw-text-white">← Change business type</button>
        </div>

        <div class="tw-bg-white tw-rounded-2xl tw-shadow-2xl tw-p-8 tw-max-w-xl tw-mx-auto">

            {!! Form::open(['url' => route('business.postRegister'), 'method' => 'POST', 'id' => 'register_form']) !!}
            {!! Form::token() !!}

            <!-- Hidden fields (auto-filled) -->
            <input type="hidden" name="business_type" id="hidden_business_type">
            <input type="hidden" name="country" value="Kenya">
            <input type="hidden" name="state" value="Nairobi">
            <input type="hidden" name="city" value="Nairobi">
            <input type="hidden" name="zip_code" value="00100">
            <input type="hidden" name="landmark" value="Kenya">
            <input type="hidden" name="time_zone" value="Africa/Nairobi">
            <input type="hidden" name="fy_start_month" value="1">
            <input type="hidden" name="accounting_method" value="FIFO">
            <input type="hidden" name="start_date" id="hidden_start_date" value="{{ now()->format(config('constants.default_date_format', 'd/m/Y')) }}">
            <input type="hidden" name="currency_id" id="hidden_currency_id">
            <input type="hidden" name="surname" value=".">

            <!-- Business type banner -->
            <div id="selected_type_banner" class="tw-bg-blue-50 tw-border tw-border-blue-200 tw-rounded-xl tw-p-3 tw-mb-5 tw-flex tw-items-center tw-gap-3">
                <span id="selected_type_icon" class="tw-text-3xl">🏢</span>
                <div>
                    <div class="tw-font-bold tw-text-blue-800" id="selected_type_label">Your Business</div>
                    <div class="tw-text-xs tw-text-blue-500" id="selected_type_features"></div>
                </div>
            </div>

            @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="tw-list-disc tw-pl-4 tw-mb-0">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <!-- Business Name -->
            <div class="tw-mb-4">
                <label class="tw-block tw-font-semibold tw-text-gray-700 tw-mb-1">Business Name <span class="tw-text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" required placeholder="e.g. Reenson Stores"
                    class="tw-w-full tw-border tw-border-gray-300 tw-rounded-xl tw-px-4 tw-py-3 tw-text-gray-800 focus:tw-outline-none focus:tw-ring-2 focus:tw-ring-blue-400 focus:tw-border-transparent">
            </div>

            <!-- Owner Name + Email on one row -->
            <div class="tw-grid tw-grid-cols-2 tw-gap-3 tw-mb-4">
                <div>
                    <label class="tw-block tw-font-semibold tw-text-gray-700 tw-mb-1">First Name <span class="tw-text-red-500">*</span></label>
                    <input type="text" name="first_name" value="{{ old('first_name') }}" required placeholder="John"
                        class="tw-w-full tw-border tw-border-gray-300 tw-rounded-xl tw-px-4 tw-py-3 tw-text-gray-800 focus:tw-outline-none focus:tw-ring-2 focus:tw-ring-blue-400">
                </div>
                <div>
                    <label class="tw-block tw-font-semibold tw-text-gray-700 tw-mb-1">Last Name</label>
                    <input type="text" name="last_name" value="{{ old('last_name') }}" placeholder="Doe"
                        class="tw-w-full tw-border tw-border-gray-300 tw-rounded-xl tw-px-4 tw-py-3 tw-text-gray-800 focus:tw-outline-none focus:tw-ring-2 focus:tw-ring-blue-400">
                </div>
            </div>

            <!-- Email -->
            <div class="tw-mb-4">
                <label class="tw-block tw-font-semibold tw-text-gray-700 tw-mb-1">Email <span class="tw-text-red-500">*</span></label>
                <input type="email" name="email" value="{{ old('email') }}" required placeholder="you@business.com"
                    id="reg_email"
                    class="tw-w-full tw-border tw-border-gray-300 tw-rounded-xl tw-px-4 tw-py-3 tw-text-gray-800 focus:tw-outline-none focus:tw-ring-2 focus:tw-ring-blue-400">
            </div>

            <!-- Phone (used for M-Pesa STK push on activation) -->
            <div class="tw-mb-4">
                <label class="tw-block tw-font-semibold tw-text-gray-700 tw-mb-1">Phone Number <span class="tw-text-red-500">*</span>
                    <span class="tw-text-xs tw-font-normal tw-text-gray-500">(for M-Pesa payments)</span>
                </label>
                <input type="text" name="mobile" value="{{ old('mobile') }}" required placeholder="0712 345 678"
                    class="tw-w-full tw-border tw-border-gray-300 tw-rounded-xl tw-px-4 tw-py-3 tw-text-gray-800 focus:tw-outline-none focus:tw-ring-2 focus:tw-ring-blue-400">
            </div>

            <!-- Username (auto-suggested, editable) -->
            <div class="tw-mb-4">
                <label class="tw-block tw-font-semibold tw-text-gray-700 tw-mb-1">Username <span class="tw-text-red-500">*</span>
                    <span class="tw-text-xs tw-font-normal tw-text-gray-500">(used to log in)</span>
                </label>
                <input type="text" name="username" id="reg_username" value="{{ old('username') }}" required placeholder="e.g. admin"
                    class="tw-w-full tw-border tw-border-gray-300 tw-rounded-xl tw-px-4 tw-py-3 tw-text-gray-800 focus:tw-outline-none focus:tw-ring-2 focus:tw-ring-blue-400">
                <p class="tw-text-xs tw-text-gray-400 tw-mt-1">Minimum 4 characters</p>
            </div>

            <!-- Password -->
            <div class="tw-grid tw-grid-cols-2 tw-gap-3 tw-mb-6">
                <div>
                    <label class="tw-block tw-font-semibold tw-text-gray-700 tw-mb-1">Password <span class="tw-text-red-500">*</span></label>
                    <input type="password" name="password" required placeholder="Min 6 characters"
                        class="tw-w-full tw-border tw-border-gray-300 tw-rounded-xl tw-px-4 tw-py-3 tw-text-gray-800 focus:tw-outline-none focus:tw-ring-2 focus:tw-ring-blue-400">
                </div>
                <div>
                    <label class="tw-block tw-font-semibold tw-text-gray-700 tw-mb-1">Confirm Password <span class="tw-text-red-500">*</span></label>
                    <input type="password" name="confirm_password" required placeholder="Repeat password"
                        class="tw-w-full tw-border tw-border-gray-300 tw-rounded-xl tw-px-4 tw-py-3 tw-text-gray-800 focus:tw-outline-none focus:tw-ring-2 focus:tw-ring-blue-400">
                </div>
            </div>

            <!-- Currency: auto KES, hidden -->
            <input type="hidden" name="currency_id" value="{{ $kes_id ?? '' }}">

            <button type="submit" id="submit_btn" class="tw-w-full tw-bg-gradient-to-r tw-from-blue-600 tw-to-indigo-600 tw-text-white tw-font-bold tw-text-lg tw-py-4 tw-rounded-xl tw-shadow-lg hover:tw-shadow-xl tw-transition-all">
                Continue to Activation →
            </button>

            <p class="tw-text-center tw-text-gray-500 tw-text-sm tw-mt-4">
                Already have an account? <a href="{{ url('/login') }}" class="tw-text-blue-600 tw-font-semibold">Sign in</a>
            </p>

            {!! Form::close() !!}
        </div>
    </div>

</div>
</div>
@endsection

@section('javascript')
<script>
var selectedType = null;

var typeConfig = {
    retail:    { icon: '🛒', label: 'Retail Shop',        features: 'POS, Sales, Inventory, Purchases, Expenses',          modules: ['purchases','add_sale','pos_sale','stock_transfers','stock_adjustment','expenses','account'] },
    restaurant:{ icon: '🍽️', label: 'Restaurant / Cafe',  features: 'POS, Tables, Kitchen, Modifiers, Bookings',            modules: ['add_sale','pos_sale','tables','modifiers','kitchen','service_staff','expenses','account'] },
    courier:   { icon: '📦', label: 'Courier & Logistics', features: 'Parcel booking, Waybills, Routes, Tracking, Manifest', modules: ['parcels','expenses','account'] },
    clinic:    { icon: '🏥', label: 'Clinic / Hospital',   features: 'Patient billing, NHIF, OPD/IPD, Pharmacy, Reports',   modules: ['hospital_billing','expenses','account'] },
    garage:    { icon: '🔧', label: 'Garage / Workshop',   features: 'Job cards, Checklists, Parts inventory, Invoices',     modules: ['purchases','add_sale','pos_sale','stock_adjustment','expenses','account'] },
    salon:     { icon: '💇', label: 'Salon / Spa',         features: 'Appointments, Services, POS, Customer tracking',      modules: ['add_sale','pos_sale','booking','types_of_service','expenses','account'] },
    wholesale: { icon: '🏭', label: 'Wholesale / Distributor', features: 'Bulk sales, Purchases, Stock, Accounts',          modules: ['purchases','add_sale','stock_transfers','stock_adjustment','expenses','account'] },
    services:  { icon: '💼', label: 'Professional Services',   features: 'Invoicing, Expenses, Clients, Reports',           modules: ['add_sale','expenses','account','subscription'] },
    other:     { icon: '✨',  label: 'Other Business',          features: 'Sales, Purchases, Inventory, Expenses',           modules: ['purchases','add_sale','pos_sale','stock_adjustment','expenses'] },
};

function selectBusinessType(type) {
    selectedType = type;
    $('.business-type-card').each(function() {
        var isThis = $(this).data('type') === type;
        $(this).toggleClass('tw-border-blue-300 tw-bg-opacity-20', isThis);
        $(this).toggleClass('tw-border-transparent', !isThis);
        $(this).find('.checkmark').toggleClass('tw-hidden', !isThis);
    });
    $('#step1_next').prop('disabled', false);
}

function goToStep2() {
    if (!selectedType) return;
    var cfg = typeConfig[selectedType];
    $('#hidden_business_type').val(selectedType);
    // Set enabled modules as hidden inputs
    $('.enabled_module_input').remove();
    cfg.modules.forEach(function(m) {
        $('<input>').attr({type:'hidden', name:'enabled_modules[]', class:'enabled_module_input', value: m}).appendTo('#register_form');
    });
    $('#selected_type_icon').text(cfg.icon);
    $('#selected_type_label').text(cfg.label);
    $('#selected_type_features').text('Auto-enabled: ' + cfg.features);
    $('#step1').addClass('tw-hidden');
    $('#step2').removeClass('tw-hidden');
    $('html, body').animate({ scrollTop: 0 }, 300);
}

function goToStep1() {
    $('#step2').addClass('tw-hidden');
    $('#step1').removeClass('tw-hidden');
}

// Auto-suggest username from email
$('#reg_email').on('blur', function() {
    var email = $(this).val();
    if (email && !$('#reg_username').val()) {
        var suggestion = email.split('@')[0].toLowerCase().replace(/[^a-z0-9_]/g, '');
        $('#reg_username').val(suggestion);
    }
});

// If returning with errors (validation failed), go straight to step 2
@if($errors->any() || old('business_type'))
$(function() {
    var bt = '{{ old("business_type") }}';
    if (bt && typeConfig[bt]) {
        selectBusinessType(bt);
        goToStep2();
    }
});
@endif
</script>
@endsection

@section('css')
<style>
.business-type-card.selected {
    border-color: #60a5fa !important;
    background: rgba(255,255,255,0.2) !important;
}
</style>
@endsection
