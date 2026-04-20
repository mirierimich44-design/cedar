@extends('layouts.app')

@section('title', __('Cloud Sync'))

@section('content')
<div class="tw-p-4 md:tw-p-6 tw-space-y-6">

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
                Keep offline devices in sync with your cloud data.
            </p>
        </div>

        <div class="tw-flex tw-items-center tw-gap-3">
            <span id="sync-connection-badge"
                  class="tw-inline-flex tw-items-center tw-gap-1 tw-text-xs tw-font-semibold tw-text-gray-500 tw-bg-gray-100 tw-rounded-full tw-px-3 tw-py-1">
                <span class="tw-w-1.5 tw-h-1.5 tw-rounded-full tw-bg-gray-400"></span> Checking…
            </span>
            @if($conflicts > 0)
            <a href="#conflicts-section"
               class="tw-inline-flex tw-items-center tw-gap-1 tw-text-xs tw-font-semibold tw-text-amber-700 tw-bg-amber-100 tw-rounded-full tw-px-3 tw-py-1">
                ⚠ {{ $conflicts }} Conflict{{ $conflicts > 1 ? 's' : '' }} Pending
            </a>
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
                    <p class="tw-text-xs tw-text-gray-500">Server → This device</p>
                </div>
            </div>
            <p id="sync-last-pull" class="tw-text-xs tw-text-gray-400 tw-mb-4">Never pulled</p>
            <button onclick="CloudSync.pull()" id="btn-pull"
                    class="tw-w-full tw-bg-blue-600 hover:tw-bg-blue-700 tw-text-white tw-text-sm tw-font-medium tw-rounded-lg tw-px-4 tw-py-2 tw-transition">
                Pull Now
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
                    <p class="tw-text-xs tw-text-gray-500">This device → Server</p>
                </div>
            </div>
            <p id="sync-last-push" class="tw-text-xs tw-text-gray-400 tw-mb-2">Never pushed</p>
            <p class="tw-text-xs tw-text-amber-600 tw-mb-4">
                <span id="sync-pending-count" class="tw-font-bold">0</span> records pending upload
            </p>
            <button onclick="CloudSync.push()" id="btn-push"
                    class="tw-w-full tw-bg-green-600 hover:tw-bg-green-700 tw-text-white tw-text-sm tw-font-medium tw-rounded-lg tw-px-4 tw-py-2 tw-transition">
                Push Now
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
                Auto-syncs every 5 minutes when online.
            </p>
            <button onclick="CloudSync.sync()" id="btn-sync"
                    class="tw-w-full tw-bg-purple-600 hover:tw-bg-purple-700 tw-text-white tw-text-sm tw-font-medium tw-rounded-lg tw-px-4 tw-py-2 tw-transition">
                Sync Now
            </button>
        </div>
    </div>

    {{-- ── Register New Device ──────────────────────────────────────────────── --}}
    <div class="tw-bg-white tw-rounded-xl tw-shadow-sm tw-border tw-border-gray-200 tw-p-5">
        <h2 class="tw-text-base tw-font-semibold tw-text-gray-800 tw-mb-4 tw-flex tw-items-center tw-gap-2">
            <svg class="tw-w-5 tw-h-5 tw-text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 4v16m8-8H4"/>
            </svg>
            Register Offline Device
        </h2>
        <form id="register-device-form" class="tw-flex tw-flex-col sm:tw-flex-row tw-gap-3">
            @csrf
            <input type="text" id="device-name-input" placeholder="Device name (e.g. Cashier PC – Branch 1)"
                   class="tw-flex-1 tw-rounded-lg tw-border tw-border-gray-300 tw-px-3 tw-py-2 tw-text-sm focus:tw-ring-2 focus:tw-ring-blue-500 focus:tw-outline-none">
            <select id="device-type-input"
                    class="tw-rounded-lg tw-border tw-border-gray-300 tw-px-3 tw-py-2 tw-text-sm focus:tw-ring-2 focus:tw-ring-blue-500">
                <option value="browser">Browser</option>
                <option value="desktop">Desktop App</option>
                <option value="mobile">Mobile</option>
            </select>
            <button type="button" onclick="registerDevice()"
                    class="tw-bg-gray-800 hover:tw-bg-gray-900 tw-text-white tw-text-sm tw-font-medium tw-rounded-lg tw-px-5 tw-py-2 tw-transition">
                Register &amp; Get Token
            </button>
        </form>
        <div id="new-token-box" class="tw-hidden tw-mt-4 tw-p-4 tw-bg-gray-50 tw-rounded-lg tw-border tw-border-dashed tw-border-gray-300">
            <p class="tw-text-xs tw-text-gray-500 tw-mb-1">Copy this token to your offline device. It won't be shown again.</p>
            <div class="tw-flex tw-items-center tw-gap-2">
                <code id="new-token-value" class="tw-flex-1 tw-text-sm tw-font-mono tw-bg-white tw-border tw-border-gray-200 tw-rounded tw-px-3 tw-py-2 tw-break-all"></code>
                <button onclick="copyToken()" class="tw-text-blue-600 hover:tw-text-blue-800 tw-text-xs tw-font-medium">Copy</button>
            </div>
        </div>
    </div>

    {{-- ── Active Devices ───────────────────────────────────────────────────── --}}
    <div class="tw-bg-white tw-rounded-xl tw-shadow-sm tw-border tw-border-gray-200 tw-p-5">
        <h2 class="tw-text-base tw-font-semibold tw-text-gray-800 tw-mb-4">
            Active Devices ({{ $tokens->where('is_active', true)->count() }})
        </h2>
        @if($tokens->isEmpty())
            <p class="tw-text-sm tw-text-gray-400 tw-italic">No devices registered yet.</p>
        @else
        <div class="tw-overflow-x-auto">
            <table class="tw-w-full tw-text-sm">
                <thead>
                    <tr class="tw-border-b tw-border-gray-100 tw-text-left tw-text-xs tw-text-gray-500 tw-uppercase tw-tracking-wide">
                        <th class="tw-pb-2 tw-pr-4">Device</th>
                        <th class="tw-pb-2 tw-pr-4">Type</th>
                        <th class="tw-pb-2 tw-pr-4">User</th>
                        <th class="tw-pb-2 tw-pr-4">Last Pull</th>
                        <th class="tw-pb-2 tw-pr-4">Last Push</th>
                        <th class="tw-pb-2 tw-pr-4">Last Seen</th>
                        <th class="tw-pb-2">Action</th>
                    </tr>
                </thead>
                <tbody class="tw-divide-y tw-divide-gray-50">
                    @foreach($tokens as $t)
                    <tr class="{{ $t->is_active ? '' : 'tw-opacity-40' }}">
                        <td class="tw-py-3 tw-pr-4">
                            <p class="tw-font-medium tw-text-gray-800">{{ $t->device_name ?? 'Unnamed' }}</p>
                            <p class="tw-text-xs tw-text-gray-400 tw-font-mono">…{{ substr($t->token, -8) }}</p>
                        </td>
                        <td class="tw-py-3 tw-pr-4">
                            <span class="tw-capitalize tw-text-xs tw-bg-gray-100 tw-text-gray-600 tw-px-2 tw-py-0.5 tw-rounded-full">
                                {{ $t->device_type }}
                            </span>
                        </td>
                        <td class="tw-py-3 tw-pr-4 tw-text-gray-600">
                            {{ optional($t->user)->username ?? '—' }}
                        </td>
                        <td class="tw-py-3 tw-pr-4 tw-text-gray-500 tw-text-xs">
                            {{ $t->last_pulled_at ? $t->last_pulled_at->diffForHumans() : 'Never' }}
                        </td>
                        <td class="tw-py-3 tw-pr-4 tw-text-gray-500 tw-text-xs">
                            {{ $t->last_pushed_at ? $t->last_pushed_at->diffForHumans() : 'Never' }}
                        </td>
                        <td class="tw-py-3 tw-pr-4 tw-text-gray-500 tw-text-xs">
                            {{ $t->last_seen_at ? $t->last_seen_at->diffForHumans() : 'Never' }}
                        </td>
                        <td class="tw-py-3">
                            @if($t->is_active)
                            <button onclick="revokeDevice({{ $t->id }})"
                                    class="tw-text-red-600 hover:tw-text-red-800 tw-text-xs tw-font-medium">
                                Revoke
                            </button>
                            @else
                            <span class="tw-text-gray-400 tw-text-xs">Revoked</span>
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
        <h2 class="tw-text-base tw-font-semibold tw-text-gray-800 tw-mb-4">
            Sync History <span class="tw-text-xs tw-text-gray-400 tw-font-normal">(last 50)</span>
        </h2>
        @if($logs->isEmpty())
            <p class="tw-text-sm tw-text-gray-400 tw-italic">No sync history yet.</p>
        @else
        <div class="tw-overflow-x-auto">
            <table class="tw-w-full tw-text-sm">
                <thead>
                    <tr class="tw-border-b tw-border-gray-100 tw-text-left tw-text-xs tw-text-gray-500 tw-uppercase tw-tracking-wide">
                        <th class="tw-pb-2 tw-pr-4">When</th>
                        <th class="tw-pb-2 tw-pr-4">Direction</th>
                        <th class="tw-pb-2 tw-pr-4">Status</th>
                        <th class="tw-pb-2 tw-pr-4">Records</th>
                        <th class="tw-pb-2 tw-pr-4">Device</th>
                        <th class="tw-pb-2">Summary</th>
                    </tr>
                </thead>
                <tbody class="tw-divide-y tw-divide-gray-50">
                    @foreach($logs as $log)
                    <tr>
                        <td class="tw-py-2.5 tw-pr-4 tw-text-gray-500 tw-text-xs">
                            {{ $log->synced_at->diffForHumans() }}
                        </td>
                        <td class="tw-py-2.5 tw-pr-4">
                            @if($log->direction === 'pull')
                            <span class="tw-text-blue-700 tw-bg-blue-50 tw-px-2 tw-py-0.5 tw-rounded-full tw-text-xs tw-font-medium">
                                ↓ Pull
                            </span>
                            @else
                            <span class="tw-text-green-700 tw-bg-green-50 tw-px-2 tw-py-0.5 tw-rounded-full tw-text-xs tw-font-medium">
                                ↑ Push
                            </span>
                            @endif
                        </td>
                        <td class="tw-py-2.5 tw-pr-4">
                            <span class="tw-text-xs tw-font-medium
                                {{ $log->status === 'success' ? 'tw-text-green-700' :
                                   ($log->status === 'partial' ? 'tw-text-amber-600' : 'tw-text-red-600') }}">
                                {{ ucfirst($log->status) }}
                            </span>
                        </td>
                        <td class="tw-py-2.5 tw-pr-4 tw-text-gray-600">
                            {{ $log->records_sent + $log->records_received }}
                        </td>
                        <td class="tw-py-2.5 tw-pr-4 tw-text-gray-500 tw-text-xs">
                            {{ optional(optional($log->token))->device_name ?? '—' }}
                        </td>
                        <td class="tw-py-2.5 tw-text-xs tw-text-gray-400">
                            @if($log->summary)
                                @foreach($log->summary as $k => $v)
                                    @if($v > 0)
                                    <span class="tw-mr-2">{{ $k }}: {{ $v }}</span>
                                    @endif
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
    const SYNC_BUSINESS_ID = {{ $businessId }};
    const SYNC_TOKEN       = localStorage.getItem('apexpos_sync_token_' + SYNC_BUSINESS_ID) || '';

    if (SYNC_TOKEN) {
        CloudSync.init({
            businessId : SYNC_BUSINESS_ID,
            syncToken  : SYNC_TOKEN,
            autoSync   : true,
        });
    }

    // ── Register device ────────────────────────────────────────────────────
    function registerDevice() {
        const name = document.getElementById('device-name-input').value.trim();
        const type = document.getElementById('device-type-input').value;

        fetch('/sync/register', {
            method : 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept'      : 'application/json',
            },
            body: JSON.stringify({ business_id: SYNC_BUSINESS_ID, device_name: name, device_type: type }),
        })
        .then(r => r.json())
        .then(data => {
            if (data.error) { alert(data.error); return; }
            // Save the token in localStorage for this browser session
            localStorage.setItem('apexpos_sync_token_' + SYNC_BUSINESS_ID, data.sync_token);
            document.getElementById('new-token-value').textContent = data.sync_token;
            document.getElementById('new-token-box').classList.remove('tw-hidden');
            location.reload();
        })
        .catch(err => alert('Registration failed: ' + err.message));
    }

    function copyToken() {
        const val = document.getElementById('new-token-value').textContent;
        navigator.clipboard.writeText(val).then(() => alert('Token copied!'));
    }

    // ── Revoke device ──────────────────────────────────────────────────────
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

    // ── Button feedback ────────────────────────────────────────────────────
    ['pull', 'push', 'sync'].forEach(action => {
        window.addEventListener('cloud-sync:' + action + ':start', () => {
            const btn = document.getElementById('btn-' + action);
            if (btn) { btn.disabled = true; btn.textContent = 'Syncing…'; }
        });
        window.addEventListener('cloud-sync:' + action + ':success', () => {
            const btn = document.getElementById('btn-' + action);
            if (btn) { btn.disabled = false; btn.textContent = action.charAt(0).toUpperCase() + action.slice(1) + ' Now'; }
        });
        window.addEventListener('cloud-sync:' + action + ':error', e => {
            const btn = document.getElementById('btn-' + action);
            if (btn) { btn.disabled = false; btn.textContent = 'Retry'; }
            toastr.error(e.detail?.error || 'Sync failed');
        });
        window.addEventListener('cloud-sync:' + action + ':complete', () => {
            const btn = document.getElementById('btn-' + action);
            if (btn) { btn.disabled = false; btn.textContent = action.charAt(0).toUpperCase() + action.slice(1) + ' Now'; }
        });
    });
</script>
@endpush
@endsection
