@extends('layouts.app')
@section('title', 'Cloud Sync')

@section('content')
<div class="tw-pb-10 tw-bg-gradient-to-r tw-from-primary-800 tw-to-primary-900">
    <div class="tw-px-5 tw-pt-5">
        <h1 class="tw-text-3xl tw-font-bold tw-text-white tw-mb-2">Cloud Sync</h1>
        <p class="tw-text-primary-100">Synchronize your local offline data with the hosting server.</p>
    </div>
</div>

<div class="tw-px-5 tw--mt-8">
    <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-3 tw-gap-6 tw-mb-8">
        <!-- Unsynced Transactions -->
        <div class="tw-bg-white tw-p-6 tw-rounded-xl tw-shadow-sm tw-ring-1 tw-ring-gray-200">
            <div class="tw-flex tw-items-center tw-gap-4">
                <div class="tw-p-3 tw-bg-sky-100 tw-text-sky-600 tw-rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" class="tw-size-6" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none">
                        <path d="M6 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M17 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M17 17h-11v-14h-2" /><path d="M6 5l14 1l-1 7h-13" />
                    </svg>
                </div>
                <div>
                    <p class="tw-text-sm tw-text-gray-500 tw-font-medium">Unsynced Transactions</p>
                    <p class="tw-text-2xl tw-font-bold tw-text-gray-900">{{ $unsynced_transactions }}</p>
                </div>
            </div>
        </div>

        <!-- Unsynced Payments -->
        <div class="tw-bg-white tw-p-6 tw-rounded-xl tw-shadow-sm tw-ring-1 tw-ring-gray-200">
            <div class="tw-flex tw-items-center tw-gap-4">
                <div class="tw-p-3 tw-bg-green-100 tw-text-green-600 tw-rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" class="tw-size-6" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none">
                        <path d="M17 8v-3a1 1 0 0 0 -1 -1h-10a2 2 0 0 0 0 4h12a1 1 0 0 1 1 1v3m0 4v3a1 1 0 0 1 -1 1h-12a2 2 0 0 1 -2 -2v-12" /><path d="M20 12v4h-4a2 2 0 0 1 0 -4h4" />
                    </svg>
                </div>
                <div>
                    <p class="tw-text-sm tw-text-gray-500 tw-font-medium">Unsynced Payments</p>
                    <p class="tw-text-2xl tw-font-bold tw-text-gray-900">{{ $unsynced_payments }}</p>
                </div>
            </div>
        </div>

        <!-- Unsynced Customers -->
        <div class="tw-bg-white tw-p-6 tw-rounded-xl tw-shadow-sm tw-ring-1 tw-ring-gray-200">
            <div class="tw-flex tw-items-center tw-gap-4">
                <div class="tw-p-3 tw-bg-purple-100 tw-text-purple-600 tw-rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" class="tw-size-6" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none">
                        <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" /><path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                    </svg>
                </div>
                <div>
                    <p class="tw-text-sm tw-text-gray-500 tw-font-medium">Unsynced Customers</p>
                    <p class="tw-text-2xl tw-font-bold tw-text-gray-900">{{ $unsynced_contacts }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="tw-bg-white tw-rounded-xl tw-shadow-sm tw-ring-1 tw-ring-gray-200 tw-overflow-hidden">
        <div class="tw-p-8 tw-text-center">
            <div class="tw-max-w-md tw-mx-auto">
                <div class="tw-mb-6">
                    <div class="tw-inline-flex tw-items-center tw-justify-center tw-w-20 tw-h-20 tw-bg-primary-100 tw-text-primary-600 tw-rounded-full tw-mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="tw-size-10" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none">
                            <path d="M7 18a4.6 4.4 0 0 1 0 -9a5 4.5 0 0 1 11 2h1a3.5 3.5 0 0 1 0 7h-1" /><path d="M9 15l3 -3l3 3" /><path d="M12 12l0 9" />
                        </svg>
                    </div>
                    <h2 class="tw-text-2xl tw-font-bold tw-text-gray-900">Ready to Sync?</h2>
                    <p class="tw-text-gray-500 tw-mt-2">Make sure you have an active internet connection before starting the synchronization process.</p>
                </div>

                <div id="sync_status" class="tw-hidden tw-mb-6 tw-p-4 tw-rounded-lg"></div>

                <button type="button" id="start_sync" class="tw-inline-flex tw-items-center tw-justify-center tw-px-8 tw-py-4 tw-text-lg tw-font-bold tw-text-white tw-bg-primary-800 tw-rounded-xl hover:tw-bg-primary-700 tw-transition-all tw-duration-200 tw-shadow-lg tw-shadow-primary-800/20 active:tw-scale-95 disabled:tw-opacity-50 disabled:tw-cursor-not-allowed">
                    <span id="btn_text">Sync Data Now</span>
                    <svg id="sync_spinner" class="tw-hidden tw-animate-spin tw-ml-3 tw-size-5 tw-text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="tw-opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="tw-opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </button>
            </div>
        </div>
        
        <div class="tw-bg-gray-50 tw-px-8 tw-py-6 tw-border-t tw-border-gray-200">
            <h3 class="tw-text-sm tw-font-bold tw-text-gray-900 tw-uppercase tw-tracking-wider tw-mb-4">How it works</h3>
            <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-gap-6">
                <div class="tw-flex tw-gap-3">
                    <span class="tw-flex tw-items-center tw-justify-center tw-size-6 tw-bg-primary-800 tw-text-white tw-text-xs tw-font-bold tw-rounded-full tw-shrink-0">1</span>
                    <p class="tw-text-sm tw-text-gray-600">Local transactions are collected and encrypted for security.</p>
                </div>
                <div class="tw-flex tw-gap-3">
                    <span class="tw-flex tw-items-center tw-justify-center tw-size-6 tw-bg-primary-800 tw-text-white tw-text-xs tw-font-bold tw-rounded-full tw-shrink-0">2</span>
                    <p class="tw-text-sm tw-text-gray-600">Data is sent to your cloud hosting via a secure API tunnel.</p>
                </div>
                <div class="tw-flex tw-gap-3">
                    <span class="tw-flex tw-items-center tw-justify-center tw-size-6 tw-bg-primary-800 tw-text-white tw-text-xs tw-font-bold tw-rounded-full tw-shrink-0">3</span>
                    <p class="tw-text-sm tw-text-gray-600">The cloud server validates and updates your remote database.</p>
                </div>
                <div class="tw-flex tw-gap-3">
                    <span class="tw-flex tw-items-center tw-justify-center tw-size-6 tw-bg-primary-800 tw-text-white tw-text-xs tw-font-bold tw-rounded-full tw-shrink-0">4</span>
                    <p class="tw-text-sm tw-text-gray-600">Local records are marked as "Synced" upon success response.</p>
                </div>
            </div>
        </div>
    </div>
    <div class="tw-bg-white tw-rounded-xl tw-shadow-sm tw-ring-1 tw-ring-gray-200 tw-overflow-hidden tw-mt-8">
        <div class="tw-p-8">
            <h2 class="tw-text-xl tw-font-bold tw-text-gray-900 tw-mb-4">Cloud Setup & Settings</h2>
            <p class="tw-text-gray-600 tw-mb-6">Configure the connection to your live cloud server here. The Token must match exactly on both systems.</p>
            
            @if(session('status'))
                <div class="tw-p-4 tw-mb-6 tw-rounded-lg {{ session('status')['success'] ? 'tw-bg-green-100 tw-text-green-800' : 'tw-bg-red-100 tw-text-red-800' }}">
                    {{ session('status')['msg'] }}
                </div>
            @endif

            <form action="{{ route('sync.settings') }}" method="POST">
                @csrf
                <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-gap-6 tw-mb-6">
                    <div>
                        <label class="tw-block tw-text-sm tw-font-medium tw-text-gray-700 tw-mb-2">Cloud Server URL</label>
                        <input type="url" name="cloud_sync_url" value="{{ $cloud_url ?? '' }}" placeholder="https://reenson.cedarpharmacare.co.ke" class="tw-w-full tw-px-4 tw-py-2 tw-border tw-border-gray-300 tw-rounded-lg focus:tw-ring-2 focus:tw-ring-primary-500 focus:tw-border-primary-500">
                        <p class="tw-mt-1 tw-text-xs tw-text-gray-500">The full URL of your live cloud hosting.</p>
                    </div>
                    <div>
                        <label class="tw-block tw-text-sm tw-font-medium tw-text-gray-700 tw-mb-2">Access Token</label>
                        <div class="tw-flex tw-gap-2">
                            <input type="text" id="cloud_sync_token" name="cloud_sync_token" value="{{ $api_token ?? '' }}" placeholder="Generated security token" class="tw-w-full tw-px-4 tw-py-2 tw-border tw-border-gray-300 tw-rounded-lg focus:tw-ring-2 focus:tw-ring-primary-500 focus:tw-border-primary-500">
                            <button type="button" id="generate_token" class="tw-px-4 tw-py-2 tw-bg-gray-100 tw-text-gray-700 tw-border tw-border-gray-300 tw-rounded-lg hover:tw-bg-gray-200 tw-whitespace-nowrap">Generate</button>
                        </div>
                        <p class="tw-mt-1 tw-text-xs tw-text-gray-500">Click Generate to create a new secure token.</p>
                    </div>
                </div>
                <button type="submit" class="tw-px-6 tw-py-2 tw-bg-gray-900 tw-text-white tw-font-bold tw-rounded-lg hover:tw-bg-gray-800">Save Settings</button>
            </form>
        </div>
    </div>
</div>
@endsection

@section('javascript')
<script type="text/javascript">
    $(document).ready(function() {
        // Token Generator
        $('#generate_token').click(function() {
            var token = [...Array(40)].map(() => Math.floor(Math.random() * 16).toString(16)).join('');
            $('#cloud_sync_token').val(token);
        });

        $('#start_sync').click(function() {
            var btn = $(this);
            var btnText = $('#btn_text');
            var spinner = $('#sync_spinner');
            var statusDiv = $('#sync_status');

            btn.prop('disabled', true);
            btnText.text('Syncing...');
            spinner.removeClass('tw-hidden');
            statusDiv.addClass('tw-hidden').removeClass('tw-bg-green-100 tw-text-green-800 tw-bg-red-100 tw-text-red-800');

            $.ajax({
                method: 'POST',
                url: "{{ route('sync.push') }}",
                data: {
                    _token: "{{ csrf_token() }}"
                },
                dataType: 'json',
                success: function(result) {
                    btn.prop('disabled', false);
                    btnText.text('Sync Data Now');
                    spinner.addClass('tw-hidden');
                    
                    statusDiv.removeClass('tw-hidden');
                    if (result.success) {
                        statusDiv.addClass('tw-bg-green-100 tw-text-green-800').text(result.msg);
                        setTimeout(function() {
                            location.reload();
                        }, 2000);
                    } else {
                        statusDiv.addClass('tw-bg-red-100 tw-text-red-800').text(result.msg);
                    }
                },
                error: function(xhr) {
                    btn.prop('disabled', false);
                    btnText.text('Sync Data Now');
                    spinner.addClass('tw-hidden');
                    
                    statusDiv.removeClass('tw-hidden').addClass('tw-bg-red-100 tw-text-red-800').text('Error: ' + xhr.responseText);
                }
            });
        });
    });
</script>
@endsection
