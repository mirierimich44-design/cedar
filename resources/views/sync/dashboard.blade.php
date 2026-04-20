@extends('layouts.app')

@section('title', __('Cloud Sync'))

@section('content')
<div class="tw-p-4 md:tw-p-6 tw-space-y-6 tw-max-w-6xl tw-mx-auto">

    {{-- ── Header ──────────────────────────────────────────────────────────── --}}
    <div class="tw-flex tw-flex-col sm:tw-flex-row tw-items-start sm:tw-items-center tw-justify-between tw-gap-3">
        <div>
            <h1 class="tw-text-2xl tw-font-bold tw-text-gray-800 tw-flex tw-items-center tw-gap-2">
                <svg class="tw-w-7 tw-h-7 tw-text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Cloud Sync
            </h1>
            <p class="tw-text-sm tw-text-gray-500 tw-mt-1">
                Keep offline devices and your local Laragon copy in sync with
                <strong>{{ config('app.url') }}</strong>
            </p>
        </div>
        <div class="tw-flex tw-items-center tw-gap-3">
            <span id="sync-connection-badge"
                  class="tw-inline-flex tw-items-center tw-gap-1 tw-text-xs tw-font-semibold tw-text-gray-500 tw-bg-gray-100 tw-rounded-full tw-px-3 tw-py-1">
                <span class="tw-w-1.5 tw-h-1.5 tw-rounded-full tw-bg-gray-400"></span> Checking…
            </span>
            @if($conflicts > 0)
            <span class="tw-inline-flex tw-items-center tw-gap-1 tw-text-xs tw-font-semibold tw-text-amber-700 tw-bg-amber-100 tw-rounded-full tw-px-3 tw-py-1">
                ⚠ {{ $conflicts }} Conflict{{ $conflicts > 1 ? 's' : '' }}
            </span>
            @endif
        </div>
    </div>

    {{-- ── Sync Actions ─────────────────────────────────────────────────────── --}}
    <div class="tw-grid tw-grid-cols-1 sm:tw-grid-cols-3 tw-gap-4">

        {{-- Pull --}}
        <div class="tw-bg-white tw-rounded-xl tw-shadow-sm tw-border tw-border-gray-200 tw-p-5">
            <div class="tw-flex tw-items-center tw-gap-3 tw-mb-3">
                <div class="tw-p-2 tw-bg-blue-50 tw-rounded-lg">
                    <svg class="tw-w-6 tw-h-6 tw-text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/>
                    </svg>
                </div>
                <div>
                    <p class="tw-font-semibold tw-text-gray-800">Pull from Cloud</p>
                    <p class="tw-text-xs tw-text-gray-500">Cloud → This browser</p>
                </div>
            </div>
            <p id="sync-last-pull" class="tw-text-xs tw-text-gray-400 tw-mb-4">Never pulled</p>
            <button id="btn-pull" onclick="CloudSync.pull()"
                    class="tw-w-full tw-bg-blue-600 hover:tw-bg-blue-700 tw-text-white tw-text-sm tw-font-medium tw-rounded-lg tw-px-4 tw-py-2 tw-transition">
                ↓ Pull
            </button>
        </div>

        {{-- Push --}}
        <div class="tw-bg-white tw-rounded-xl tw-shadow-sm tw-border tw-border-gray-200 tw-p-5">
            <div class="tw-flex tw-items-center tw-gap-3 tw-mb-3">
                <div class="tw-p-2 tw-bg-green-50 tw-rounded-lg">
                    <svg class="tw-w-6 tw-h-6 tw-text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                    </svg>
                </div>
                <div>
                    <p class="tw-font-semibold tw-text-gray-800">Push to Cloud</p>
                    <p class="tw-text-xs tw-text-gray-500">Browser → Cloud</p>
                </div>
            </div>
            <p id="sync-last-push" class="tw-text-xs tw-text-gray-400 tw-mb-1">Never pushed</p>
            <p class="tw-text-xs tw-text-amber-600 tw-mb-3">
                <span id="sync-pending-count" class="tw-font-bold">0</span> records pending
            </p>
            <button id="btn-push" onclick="CloudSync.push()"
                    class="tw-w-full tw-bg-green-600 hover:tw-bg-green-700 tw-text-white tw-text-sm tw-font-medium tw-rounded-lg tw-px-4 tw-py-2 tw-transition">
                ↑ Push
            </button>
        </div>

        {{-- Full Sync --}}
        <div class="tw-bg-white tw-rounded-xl tw-shadow-sm tw-border tw-border-gray-200 tw-p-5">
            <div class="tw-flex tw-items-center tw-gap-3 tw-mb-3">
                <div class="tw-p-2 tw-bg-purple-50 tw-rounded-lg">
                    <svg class="tw-w-6 tw-h-6 tw-text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                </div>
                <div>
                    <p class="tw-font-semibold tw-text-gray-800">Full Sync</p>
                    <p class="tw-text-xs tw-text-gray-500">Push then Pull (both ways)</p>
                </div>
            </div>
            <p class="tw-text-xs tw-text-gray-400 tw-mb-4">
                Auto-runs every 5 min while online.
            </p>
            <button id="btn-sync" onclick="CloudSync.sync()"
                    class="tw-w-full tw-bg-purple-600 hover:tw-bg-purple-700 tw-text-white tw-text-sm tw-font-medium tw-rounded-lg tw-px-4 tw-py-2 tw-transition">
                ⟳ Sync
            </button>
        </div>
    </div>

    {{-- ── Register New Device ──────────────────────────────────────────────── --}}
    <div class="tw-bg-white tw-rounded-xl tw-shadow-sm tw-border tw-border-gray-200 tw-p-5">
        <h2 class="tw-font-semibold tw-text-gray-800 tw-mb-1 tw-flex tw-items-center tw-gap-2">
            <svg class="tw-w-5 tw-h-5 tw-text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Register a Device
        </h2>
        <p class="tw-text-xs tw-text-gray-500 tw-mb-4">
            Register once per device (browser, Laragon PC, phone). Each gets a unique token.
        </p>
        <div class="tw-flex tw-flex-col sm:tw-flex-row tw-gap-3">
            <input type="text" id="device-name-input"
                   placeholder="e.g. Cashier PC – Branch 1 or Laragon Dev"
                   class="tw-flex-1 tw-rounded-lg tw-border tw-border-gray-300 tw-px-3 tw-py-2 tw-text-sm focus:tw-ring-2 focus:tw-ring-blue-500 focus:tw-outline-none">
            <select id="device-type-input"
                    class="tw-rounded-lg tw-border tw-border-gray-300 tw-px-3 tw-py-2 tw-text-sm">
                <option value="browser">Browser / POS terminal</option>
                <option value="desktop">Laragon / Desktop app</option>
                <option value="mobile">Mobile phone</option>
            </select>
            <button type="button" onclick="registerDevice()"
                    class="tw-bg-gray-900 hover:tw-bg-gray-700 tw-text-white tw-text-sm tw-font-medium tw-rounded-lg tw-px-5 tw-py-2">
                Register &amp; Get Token
            </button>
        </div>

        {{-- Token result box --}}
        <div id="new-token-box" class="tw-hidden tw-mt-4 tw-rounded-xl tw-border tw-border-dashed tw-border-blue-300 tw-bg-blue-50 tw-p-4">
            <p class="tw-text-xs tw-font-semibold tw-text-blue-800 tw-mb-2">✓ Device registered — copy your token now (shown only once)</p>
            <div class="tw-flex tw-items-center tw-gap-2 tw-mb-3">
                <code id="new-token-value"
                      class="tw-flex-1 tw-text-xs tw-font-mono tw-bg-white tw-border tw-border-blue-200 tw-rounded tw-px-3 tw-py-2 tw-break-all tw-select-all"></code>
                <button onclick="copyToken()"
                        class="tw-shrink-0 tw-text-blue-700 hover:tw-text-blue-900 tw-text-xs tw-font-semibold tw-bg-white tw-border tw-border-blue-200 tw-rounded tw-px-2 tw-py-1">
                    Copy
                </button>
            </div>
            <div id="laragon-instructions" class="tw-hidden">
                <p class="tw-text-xs tw-font-semibold tw-text-blue-800 tw-mb-1">Laragon setup — add these to your local <code>.env</code>:</p>
                <pre id="laragon-env-snippet"
                     class="tw-text-xs tw-bg-gray-900 tw-text-green-300 tw-rounded tw-p-3 tw-overflow-x-auto tw-select-all tw-font-mono"></pre>
                <p class="tw-text-xs tw-text-blue-700 tw-mt-2">
                    Then run: <code class="tw-bg-white tw-px-1 tw-rounded">php artisan sync:pull</code>
                    to download all live data into your local database.
                </p>
            </div>
        </div>
    </div>

    {{-- ── Active Devices ───────────────────────────────────────────────────── --}}
    <div class="tw-bg-white tw-rounded-xl tw-shadow-sm tw-border tw-border-gray-200 tw-p-5">
        <h2 class="tw-font-semibold tw-text-gray-800 tw-mb-4">
            Registered Devices
            <span class="tw-text-xs tw-text-gray-400 tw-font-normal">({{ $tokens->count() }} total)</span>
        </h2>
        @if($tokens->isEmpty())
            <p class="tw-text-sm tw-text-gray-400 tw-italic">No devices registered yet. Register one above.</p>
        @else
        <div class="tw-overflow-x-auto -tw-mx-5">
            <table class="tw-w-full tw-text-sm tw-min-w-max">
                <thead>
                    <tr class="tw-border-b tw-border-gray-100 tw-text-xs tw-text-gray-400 tw-uppercase tw-tracking-wide">
                        <th class="tw-px-5 tw-pb-2 tw-text-left">Device</th>
                        <th class="tw-px-3 tw-pb-2 tw-text-left">Type</th>
                        <th class="tw-px-3 tw-pb-2 tw-text-left">User</th>
                        <th class="tw-px-3 tw-pb-2 tw-text-left">Last Pull</th>
                        <th class="tw-px-3 tw-pb-2 tw-text-left">Last Push</th>
                        <th class="tw-px-3 tw-pb-2 tw-text-left">Last Seen</th>
                        <th class="tw-px-5 tw-pb-2 tw-text-left">Status</th>
                    </tr>
                </thead>
                <tbody class="tw-divide-y tw-divide-gray-50">
                    @foreach($tokens as $t)
                    <tr class="{{ $t->is_active ? '' : 'tw-opacity-40' }}">
                        <td class="tw-px-5 tw-py-3">
                            <p class="tw-font-medium tw-text-gray-800">{{ $t->device_name ?? 'Unnamed' }}</p>
                            <p class="tw-text-xs tw-text-gray-400 tw-font-mono">…{{ substr($t->token, -10) }}</p>
                        </td>
                        <td class="tw-px-3 tw-py-3">
                            <span class="tw-capitalize tw-text-xs tw-bg-gray-100 tw-text-gray-600 tw-px-2 tw-py-0.5 tw-rounded-full">
                                {{ $t->device_type }}
                            </span>
                        </td>
                        <td class="tw-px-3 tw-py-3 tw-text-gray-500 tw-text-xs">
                            {{ optional($t->user)->username ?? '—' }}
                        </td>
                        <td class="tw-px-3 tw-py-3 tw-text-gray-500 tw-text-xs tw-whitespace-nowrap">
                            {{ $t->last_pulled_at ? $t->last_pulled_at->diffForHumans() : 'Never' }}
                        </td>
                        <td class="tw-px-3 tw-py-3 tw-text-gray-500 tw-text-xs tw-whitespace-nowrap">
                            {{ $t->last_pushed_at ? $t->last_pushed_at->diffForHumans() : 'Never' }}
                        </td>
                        <td class="tw-px-3 tw-py-3 tw-text-gray-500 tw-text-xs tw-whitespace-nowrap">
                            {{ $t->last_seen_at ? $t->last_seen_at->diffForHumans() : 'Never' }}
                        </td>
                        <td class="tw-px-5 tw-py-3">
                            @if($t->is_active)
                                <span class="tw-text-xs tw-text-green-600 tw-font-medium">Active</span>
                                <button onclick="revokeDevice({{ $t->id }})"
                                        class="tw-ml-3 tw-text-red-500 hover:tw-text-red-700 tw-text-xs">
                                    Revoke
                                </button>
                            @else
                                <span class="tw-text-xs tw-text-gray-400">Revoked</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

    {{-- ── Sync History ─────────────────────────────────────────────────────── --}}
    <div class="tw-bg-white tw-rounded-xl tw-shadow-sm tw-border tw-border-gray-200 tw-p-5">
        <h2 class="tw-font-semibold tw-text-gray-800 tw-mb-4">
            Sync History
            <span class="tw-text-xs tw-text-gray-400 tw-font-normal">(last 50 operations)</span>
        </h2>
        @if($logs->isEmpty())
            <p class="tw-text-sm tw-text-gray-400 tw-italic">No sync history yet.</p>
        @else
        <div class="tw-overflow-x-auto -tw-mx-5">
            <table class="tw-w-full tw-text-sm tw-min-w-max">
                <thead>
                    <tr class="tw-border-b tw-border-gray-100 tw-text-xs tw-text-gray-400 tw-uppercase tw-tracking-wide">
                        <th class="tw-px-5 tw-pb-2 tw-text-left">When</th>
                        <th class="tw-px-3 tw-pb-2 tw-text-left">Direction</th>
                        <th class="tw-px-3 tw-pb-2 tw-text-left">Status</th>
                        <th class="tw-px-3 tw-pb-2 tw-text-left">Records</th>
                        <th class="tw-px-3 tw-pb-2 tw-text-left">Device</th>
                        <th class="tw-px-5 tw-pb-2 tw-text-left">Detail</th>
                    </tr>
                </thead>
                <tbody class="tw-divide-y tw-divide-gray-50">
                    @foreach($logs as $log)
                    <tr>
                        <td class="tw-px-5 tw-py-2.5 tw-text-gray-500 tw-text-xs tw-whitespace-nowrap">
                            {{ $log->synced_at->diffForHumans() }}
                        </td>
                        <td class="tw-px-3 tw-py-2.5">
                            @if($log->direction === 'pull')
                            <span class="tw-text-blue-700 tw-bg-blue-50 tw-px-2 tw-py-0.5 tw-rounded-full tw-text-xs tw-font-medium">↓ Pull</span>
                            @else
                            <span class="tw-text-green-700 tw-bg-green-50 tw-px-2 tw-py-0.5 tw-rounded-full tw-text-xs tw-font-medium">↑ Push</span>
                            @endif
                        </td>
                        <td class="tw-px-3 tw-py-2.5">
                            <span class="tw-text-xs tw-font-semibold
                                {{ $log->status === 'success' ? 'tw-text-green-700' :
                                   ($log->status === 'partial' ? 'tw-text-amber-600' : 'tw-text-red-600') }}">
                                {{ ucfirst($log->status) }}
                            </span>
                        </td>
                        <td class="tw-px-3 tw-py-2.5 tw-text-gray-600 tw-text-xs">
                            {{ $log->records_sent + $log->records_received }}
                        </td>
                        <td class="tw-px-3 tw-py-2.5 tw-text-gray-400 tw-text-xs">
                            {{ optional($log->token)->device_name ?? '—' }}
                        </td>
                        <td class="tw-px-5 tw-py-2.5 tw-text-xs tw-text-gray-400">
                            @if($log->summary)
                                @foreach($log->summary as $k => $v)
                                    @if($v > 0)<span class="tw-mr-2">{{ $k }}: {{ $v }}</span>@endif
                                @endforeach
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

</div>

@push('js')
<script src="{{ asset('js/cloud-sync.js') }}"></script>
<script>
    const BUSINESS_ID = @json($businessId);
    const APP_URL     = @json(config('app.url'));

    // Get saved token for this business
    const savedToken = localStorage.getItem('apexpos_sync_token_' + BUSINESS_ID);

    if (savedToken) {
        CloudSync.init({ businessId: BUSINESS_ID, syncToken: savedToken, autoSync: true });
    }

    // ── Register device ──────────────────────────────────────────────────────
    function registerDevice() {
        const name = document.getElementById('device-name-input').value.trim() || 'Unnamed Device';
        const type = document.getElementById('device-type-input').value;

        fetch('/sync/register', {
            method : 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept'      : 'application/json',
            },
            body: JSON.stringify({ business_id: BUSINESS_ID, device_name: name, device_type: type }),
        })
        .then(r => r.json())
        .then(data => {
            if (data.error) { alert(data.error); return; }

            const token = data.sync_token;
            localStorage.setItem('apexpos_sync_token_' + BUSINESS_ID, token);

            document.getElementById('new-token-value').textContent = token;
            document.getElementById('new-token-box').classList.remove('tw-hidden');

            // Show Laragon-specific instructions for desktop type
            if (type === 'desktop') {
                const snippet = `SYNC_REMOTE_URL=${APP_URL}\nSYNC_TOKEN=${token}\nSYNC_BUSINESS_ID=${BUSINESS_ID}`;
                document.getElementById('laragon-env-snippet').textContent = snippet;
                document.getElementById('laragon-instructions').classList.remove('tw-hidden');
            }

            // Reinitialise sync with new token
            CloudSync.init({ businessId: BUSINESS_ID, syncToken: token, autoSync: true });

            setTimeout(() => location.reload(), 2500);
        })
        .catch(err => alert('Registration failed: ' + err.message));
    }

    function copyToken() {
        const val = document.getElementById('new-token-value').textContent;
        navigator.clipboard.writeText(val)
            .then(() => { document.querySelector('[onclick="copyToken()"]').textContent = '✓ Copied!'; })
            .catch(() => alert('Copy failed — select the text manually.'));
    }

    // ── Revoke device ────────────────────────────────────────────────────────
    function revokeDevice(id) {
        if (!confirm('Revoke this device? It will no longer be able to sync.')) return;
        fetch('/sync/token/' + id, {
            method : 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept'      : 'application/json',
            },
        })
        .then(r => r.json())
        .then(() => location.reload())
        .catch(err => alert('Revoke failed: ' + err.message));
    }

    // ── Button state listeners ───────────────────────────────────────────────
    ['pull', 'push', 'sync'].forEach(action => {
        window.addEventListener('cs:' + action + ':error', e => {
            if (typeof toastr !== 'undefined')
                toastr.error(e.detail?.error || 'Sync failed');
        });
    });
</script>
@endpush
@endsection
