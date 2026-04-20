@extends('layouts.auth')
@section('title', 'Activate Your Account')

@section('content')
<div class="tw-min-h-screen tw-bg-gradient-to-br tw-from-blue-900 tw-via-blue-800 tw-to-indigo-900 tw-flex tw-items-center tw-justify-center tw-py-10 tw-px-4">
<div class="tw-w-full tw-max-w-3xl tw-mx-auto">

    <!-- Header -->
    <div class="tw-text-center tw-mb-8">
        <div class="tw-text-5xl tw-mb-3">🎉</div>
        <h1 class="tw-text-3xl tw-font-extrabold tw-text-white">Welcome, {{ $user->first_name }}!</h1>
        <p class="tw-text-blue-200 tw-mt-2 tw-text-lg">
            <strong class="tw-text-white">{{ $business->name }}</strong> is ready.
            Choose how to activate your account.
        </p>
    </div>

    @if(session('error'))
    <div class="alert alert-danger tw-mb-4">{{ session('error') }}</div>
    @endif

    <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-gap-6">

        {{-- ── Trial Card ───────────────────────────────────────────── --}}
        @if($trialEnabled && $trialDays > 0)
        <div class="tw-bg-white tw-rounded-2xl tw-shadow-2xl tw-p-8 tw-flex tw-flex-col">
            <div class="tw-text-4xl tw-mb-3">⚡</div>
            <h2 class="tw-text-2xl tw-font-bold tw-text-gray-800">Start Free Trial</h2>
            <p class="tw-text-gray-500 tw-mt-2 tw-mb-4 tw-flex-1">
                Get <strong>{{ $trialDays }} days free</strong> — no payment needed.
                Explore everything: sales, inventory, reports and more.
            </p>
            <ul class="tw-text-sm tw-text-gray-600 tw-space-y-1 tw-mb-6">
                <li>✅ Full access to all features</li>
                <li>✅ No credit card required</li>
                <li>✅ Cancel or pay anytime</li>
                <li>✅ Reminder 1 day before trial ends</li>
            </ul>
            <form method="POST" action="{{ route('onboarding.trial') }}">
                @csrf
                <button type="submit"
                    class="tw-w-full tw-bg-green-500 hover:tw-bg-green-600 tw-text-white tw-font-bold tw-text-lg tw-py-4 tw-rounded-xl tw-transition-all tw-shadow-lg">
                    Start {{ $trialDays }}-Day Free Trial →
                </button>
            </form>
        </div>
        @endif

        {{-- ── M-Pesa Card ──────────────────────────────────────────── --}}
        @if($mpesaEnabled)
        <div class="tw-bg-white tw-rounded-2xl tw-shadow-2xl tw-p-8 tw-flex tw-flex-col">
            <div class="tw-text-4xl tw-mb-3">📱</div>
            <h2 class="tw-text-2xl tw-font-bold tw-text-gray-800">Pay with M-Pesa</h2>
            <p class="tw-text-gray-500 tw-mt-2 tw-mb-4">
                Activate immediately with M-Pesa.
                <strong class="tw-text-gray-700">KES {{ number_format($monthlyPrice) }}/month</strong>.
            </p>
            <ul class="tw-text-sm tw-text-gray-600 tw-space-y-1 tw-mb-6 tw-flex-1">
                <li>✅ Instant activation on payment</li>
                <li>✅ Full 30 days access</li>
                <li>✅ Receipt sent to your email</li>
                <li>✅ Renew monthly or annually</li>
            </ul>

            {{-- Phone input + STK --}}
            <div id="mpesa_form_area">
                <div class="tw-mb-3">
                    <label class="tw-block tw-text-sm tw-font-semibold tw-text-gray-700 tw-mb-1">Your M-Pesa Phone Number</label>
                    <input type="text" id="mpesa_phone" placeholder="0712 345 678"
                        class="tw-w-full tw-border tw-border-gray-300 tw-rounded-xl tw-px-4 tw-py-3 tw-text-gray-800 focus:tw-outline-none focus:tw-ring-2 focus:tw-ring-green-400">
                    <p class="tw-text-xs tw-text-gray-400 tw-mt-1">The STK push will be sent to this number.</p>
                </div>
                <button onclick="initiateMpesaPayment()"
                    class="tw-w-full tw-bg-gradient-to-r tw-from-green-500 tw-to-emerald-600 tw-text-white tw-font-bold tw-text-lg tw-py-4 tw-rounded-xl tw-transition-all tw-shadow-lg hover:tw-shadow-xl"
                    id="mpesa_pay_btn">
                    Pay KES {{ number_format($monthlyPrice) }} via M-Pesa →
                </button>
            </div>

            {{-- Waiting for PIN --}}
            <div id="mpesa_waiting" class="tw-hidden tw-text-center tw-py-4">
                <div class="tw-animate-spin tw-inline-block tw-w-10 tw-h-10 tw-border-4 tw-border-green-500 tw-border-t-transparent tw-rounded-full tw-mb-3"></div>
                <p class="tw-font-semibold tw-text-gray-700">Waiting for M-Pesa PIN...</p>
                <p class="tw-text-sm tw-text-gray-500 tw-mt-1">Check your phone and enter your PIN to complete payment.</p>
            </div>

            {{-- Manual paybill fallback --}}
            <div id="mpesa_manual" class="tw-hidden tw-bg-green-50 tw-border tw-border-green-200 tw-rounded-xl tw-p-4 tw-mt-2">
                <p class="tw-font-bold tw-text-green-800 tw-mb-2">Pay manually via Lipa Na M-Pesa:</p>
                <table class="tw-text-sm tw-text-gray-700 tw-w-full">
                    <tr><td class="tw-py-1 tw-font-medium">Paybill:</td><td class="tw-font-bold" id="manual_paybill">{{ $mpesaPaybill }}</td></tr>
                    <tr><td class="tw-py-1 tw-font-medium">Account:</td><td class="tw-font-bold" id="manual_account">APEX-{{ $business->id }}</td></tr>
                    <tr><td class="tw-py-1 tw-font-medium">Amount:</td><td class="tw-font-bold">KES {{ number_format($monthlyPrice) }}</td></tr>
                </table>
                <p class="tw-text-xs tw-text-gray-500 tw-mt-2">Your account will be activated within minutes of payment.</p>
            </div>
        </div>
        @endif

    </div>

    {{-- If neither trial nor mpesa are configured, show a contact note --}}
    @if(!$trialEnabled && !$mpesaEnabled)
    <div class="tw-bg-white tw-rounded-2xl tw-p-8 tw-text-center tw-shadow-xl">
        <div class="tw-text-5xl tw-mb-4">📞</div>
        <h2 class="tw-text-xl tw-font-bold tw-text-gray-800">Contact Us to Activate</h2>
        <p class="tw-text-gray-500 tw-mt-2">Please contact support to activate your account.</p>
        <p class="tw-text-lg tw-font-bold tw-text-blue-600 tw-mt-3">support@apexpos.co.ke</p>
    </div>
    @endif

    <p class="tw-text-center tw-text-blue-300 tw-text-sm tw-mt-6">
        Signed in as <strong class="tw-text-white">{{ $user->username }}</strong> —
        <a href="{{ url('/logout') }}" class="tw-text-blue-200 hover:tw-text-white tw-underline"
           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Sign out</a>
    </p>
    <form id="logout-form" action="{{ url('/logout') }}" method="POST" class="tw-hidden">@csrf</form>

</div>
</div>
@endsection

@section('javascript')
<script>
var pollTimer = null;

function initiateMpesaPayment() {
    var phone = $('#mpesa_phone').val().trim();
    if (!phone) {
        alert('Please enter your M-Pesa phone number.');
        return;
    }

    $('#mpesa_pay_btn').prop('disabled', true).text('Sending STK Push...');
    $('#mpesa_waiting').removeClass('tw-hidden');
    $('#mpesa_manual').addClass('tw-hidden');

    $.ajax({
        url: '{{ route("onboarding.stk_push") }}',
        method: 'POST',
        data: { phone: phone, _token: '{{ csrf_token() }}' },
        success: function(resp) {
            if (resp.success) {
                // Poll for confirmation
                startPolling();
            } else if (resp.manual) {
                // Show manual payment details
                $('#mpesa_waiting').addClass('tw-hidden');
                $('#mpesa_pay_btn').prop('disabled', false).text('Retry STK Push');
                $('#manual_paybill').text(resp.paybill || '{{ $mpesaPaybill }}');
                $('#manual_account').text(resp.account || 'APEX-{{ $business->id }}');
                $('#mpesa_manual').removeClass('tw-hidden');
                // Still poll in case they pay manually
                startPolling();
            } else {
                $('#mpesa_waiting').addClass('tw-hidden');
                $('#mpesa_pay_btn').prop('disabled', false).text('Pay KES {{ number_format($monthlyPrice) }} via M-Pesa →');
                alert(resp.message || 'Payment failed. Please try again.');
            }
        },
        error: function() {
            $('#mpesa_waiting').addClass('tw-hidden');
            $('#mpesa_pay_btn').prop('disabled', false).text('Pay KES {{ number_format($monthlyPrice) }} via M-Pesa →');
            alert('Network error. Please try again.');
        }
    });
}

function startPolling() {
    if (pollTimer) clearInterval(pollTimer);
    var attempts = 0;
    pollTimer = setInterval(function() {
        attempts++;
        $.get('{{ route("onboarding.check_payment") }}', function(resp) {
            if (resp.paid) {
                clearInterval(pollTimer);
                window.location.href = resp.redirect || '/home';
            }
        });
        // Stop polling after 3 minutes
        if (attempts >= 36) {
            clearInterval(pollTimer);
            $('#mpesa_waiting').addClass('tw-hidden');
            $('#mpesa_manual').removeClass('tw-hidden');
        }
    }, 5000); // every 5 seconds
}
</script>
@endsection
