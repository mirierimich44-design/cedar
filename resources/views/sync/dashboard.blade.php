@extends('layouts.app')
@section('title', __('Cloud Sync'))

@section('content')
<section class="content-header">
    <h1 class="tw-text-2xl tw-font-bold">
        Cloud Sync
    </h1>
</section>

<section class="content">
<div class="tw-p-4 md:tw-p-6 tw-space-y-5 tw-max-w-5xl">

    {{-- ── Status bar ──────────────────────────────────────────────────────── --}}
    <div class="tw-flex tw-flex-wrap tw-items-center tw-gap-3">
        <span id="sync-connection-badge"
              class="tw-inline-flex tw-items-center tw-gap-1.5 tw-text-xs tw-font-semibold tw-text-gray-500 tw-bg-gray-100 tw-rounded-full tw-px-3 tw-py-1.5">
            <svg width="8" height="8" viewBox="0 0 8 8"><circle cx="4" cy="4" r="4" fill="#9ca3af"/></svg>
            Checking connection…
        </span>
        @if($conflicts > 0)
        <span class="tw-inline-flex tw-items-center tw-gap-1 tw-text-xs tw-font-semibold tw-text-amber-700 tw-bg-amber-100 tw-rounded-full tw-px-3 tw-py-1.5">
            ⚠ {{ $conflicts }} unresolved conflict{{ $conflicts > 1 ? 's' : '' }}
        </span>
        @endif
        <span class="tw-text-xs tw-text-gray-400">Connected to: <strong>{{ config('app.url') }}</strong></span>
    </div>

    {{-- ── How it works ─────────────────────────────────────────────────────── --}}
    <div class="tw-bg-blue-50 tw-border tw-border-blue-200 tw-rounded-xl tw-p-5">
        <h2 class="tw-font-semibold tw-text-blue-900 tw-mb-3" style="font-size:14px">
            📋 How Cloud Sync Works
        </h2>
        <div class="tw-grid tw-grid-cols-1 sm:tw-grid-cols-3 tw-gap-4 tw-text-sm tw-text-blue-800">
            <div class="tw-flex tw-gap-2">
                <span class="tw-shrink-0 tw-font-bold tw-bg-blue-200 tw-rounded-full tw-w-6 tw-h-6 tw-flex tw-items-center tw-justify-center" style="min-width:24px;min-height:24px">1</span>
                <div>
                    <p class="tw-font-semibold">Pull from Cloud</p>
                    <p class="tw-text-xs tw-text-blue-700">Downloads the latest products, contacts, stock and settings from the live server into <strong>this browser's offline storage</strong> (IndexedDB). Use before going offline.</p>
                </div>
            </div>
            <div class="tw-flex tw-gap-2">
                <span class="tw-shrink-0 tw-font-bold tw-bg-blue-200 tw-rounded-full tw-w-6 tw-h-6 tw-flex tw-items-center tw-justify-center" style="min-width:24px;min-height:24px">2</span>
                <div>
                    <p class="tw-font-semibold">Work Offline</p>
                    <p class="tw-text-xs tw-text-blue-700">Create sales, record transactions and add contacts while offline. They are queued automatically in IndexedDB until you come back online.</p>
                </div>
            </div>
            <div class="tw-flex tw-gap-2">
                <span class="tw-shrink-0 tw-font-bold tw-bg-blue-200 tw-rounded-full tw-w-6 tw-h-6 tw-flex tw-items-center tw-justify-center" style="min-width:24px;min-height:24px">3</span>
                <div>
                    <p class="tw-font-semibold">Push to Cloud</p>
                    <p class="tw-text-xs tw-text-blue-700">When back online, click <strong>Push</strong> to upload all queued offline transactions to the live server. Or use <strong>Full Sync</strong> to do both at once.</p>
                </div>
            </div>
        </div>
        <hr class="tw-border-blue-200 tw-my-3">
        <p class="tw-text-xs tw-text-blue-700">
            <strong>Laragon / Desktop setup:</strong> Register a device below, copy the token, add it to your local <code>.env</code> as
            <code>SYNC_TOKEN=…</code>, then run <code>php artisan sync:pull</code> to mirror the live database locally.
        </p>
    </div>

    {{-- ── Action cards ──────────────────────────────────────────────────────── --}}
    <div class="tw-grid tw-grid-cols-1 sm:tw-grid-cols-3 tw-gap-4">

        {{-- Pull --}}
        <div class="tw-bg-white tw-rounded-xl tw-border tw-border-gray-200 tw-shadow-sm tw-p-5">
            <div class="tw-flex tw-items-center tw-gap-3 tw-mb-2">
                <div class="tw-p-2 tw-bg-blue-50 tw-rounded-lg">
                    <svg width="22" height="22" fill="none" stroke="#2563eb" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/>
                    </svg>
                </div>
                <div>
                    <p class="tw-font-semibold tw-text-gray-800" style="font-size:14px">Pull from Cloud</p>
                    <p class="tw-text-xs tw-text-gray-500">Cloud → This browser</p>
                </div>
            </div>
            <p id="sync-last-pull" class="tw-text-xs tw-text-gray-400 tw-mb-4">Never pulled</p>
            <button id="btn-pull" onclick="doPull()"
                    class="tw-w-full tw-text-white tw-text-sm tw-font-medium tw-rounded-lg tw-px-4 tw-py-2 tw-transition"
                    style="background:#2563eb">
                ↓ Pull
            </button>
        </div>

        {{-- Push --}}
        <div class="tw-bg-white tw-rounded-xl tw-border tw-border-gray-200 tw-shadow-sm tw-p-5">
            <div class="tw-flex tw-items-center tw-gap-3 tw-mb-2">
                <div class="tw-p-2 tw-bg-green-50 tw-rounded-lg">
                    <svg width="22" height="22" fill="none" stroke="#16a34a" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                    </svg>
                </div>
                <div>
                    <p class="tw-font-semibold tw-text-gray-800" style="font-size:14px">Push to Cloud</p>
                    <p class="tw-text-xs tw-text-gray-500">Browser → Cloud</p>
                </div>
            </div>
            <p id="sync-last-push" class="tw-text-xs tw-text-gray-400 tw-mb-1">Never pushed</p>
            <p class="tw-text-xs tw-text-amber-600 tw-mb-3">
                <span id="sync-pending-count" class="tw-font-bold">0</span> records pending
            </p>
            <button id="btn-push" onclick="doPush()"
                    class="tw-w-full tw-text-white tw-text-sm tw-font-medium tw-rounded-lg tw-px-4 tw-py-2 tw-transition"
                    style="background:#16a34a">
                ↑ Push
            </button>
        </div>

        {{-- Full Sync --}}
        <div class="tw-bg-white tw-rounded-xl tw-border tw-border-gray-200 tw-shadow-sm tw-p-5">
            <div class="tw-flex tw-items-center tw-gap-3 tw-mb-2">
                <div class="tw-p-2 tw-bg-purple-50 tw-rounded-lg">
                    <svg width="22" height="22" fill="none" stroke="#9333ea" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                </div>
                <div>
                    <p class="tw-font-semibold tw-text-gray-800" style="font-size:14px">Full Sync</p>
                    <p class="tw-text-xs tw-text-gray-500">Push then Pull (both ways)</p>
                </div>
            </div>
            <p class="tw-text-xs tw-text-gray-400 tw-mb-4">Auto-runs every 5 min while online.</p>
            <button id="btn-sync" onclick="doSync()"
                    class="tw-w-full tw-text-white tw-text-sm tw-font-medium tw-rounded-lg tw-px-4 tw-py-2 tw-transition"
                    style="background:#9333ea">
                ⟳ Sync Now
            </button>
        </div>
    </div>

    {{-- ── Progress / result toast ──────────────────────────────────────────── --}}
    <div id="sync-result" class="tw-hidden tw-rounded-lg tw-px-4 tw-py-3 tw-text-sm tw-font-medium"></div>

    {{-- ── Register New Device ──────────────────────────────────────────────── --}}
    <div class="tw-bg-white tw-rounded-xl tw-border tw-border-gray-200 tw-shadow-sm tw-p-5">
        <h2 class="tw-font-semibold tw-text-gray-800 tw-mb-1" style="font-size:14px">
            ＋ Register a Device / Get Token
        </h2>
        <p class="tw-text-xs tw-text-gray-500 tw-mb-4">
            Register once per device. The token lets that device sync securely.
            Browser POS terminals register automatically — use <em>Desktop</em> for Laragon.
        </p>
        <div class="tw-flex tw-flex-col sm:tw-flex-row tw-gap-3">
            <input type="text" id="device-name-input"
                   placeholder="e.g. Cashier PC – Branch 1 or Laragon Dev"
                   class="tw-flex-1 tw-rounded-lg tw-border tw-border-gray-300 tw-px-3 tw-py-2 tw-text-sm">
            <select id="device-type-input"
                    class="tw-rounded-lg tw-border tw-border-gray-300 tw-px-3 tw-py-2 tw-text-sm">
                <option value="browser">Browser / POS terminal</option>
                <option value="desktop">Laragon / Desktop app</option>
                <option value="mobile">Mobile phone</option>
            </select>
            <button type="button" onclick="registerDevice()"
                    class="tw-text-white tw-text-sm tw-font-medium tw-rounded-lg tw-px-5 tw-py-2"
                    style="background:#111827">
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
                        class="tw-text-blue-700 tw-text-xs tw-font-semibold tw-bg-white tw-border tw-border-blue-200 tw-rounded tw-px-2 tw-py-1">
                    Copy
                </button>
            </div>
            <div id="laragon-instructions" class="tw-hidden">
                <p class="tw-text-xs tw-font-semibold tw-text-blue-800 tw-mb-1">Add to your local Laragon <code>.env</code>:</p>
                <pre id="laragon-env-snippet"
                     class="tw-text-xs tw-rounded tw-p-3 tw-overflow-x-auto tw-select-all tw-font-mono"
                     style="background:#111827;color:#86efac"></pre>
                <p class="tw-text-xs tw-text-blue-700 tw-mt-2">
                    Then run: <code class="tw-bg-white tw-px-1 tw-rounded">php artisan sync:pull</code>
                    to download all live data into your local database.
                </p>
            </div>
        </div>
    </div>

    {{-- ── Registered Devices ───────────────────────────────────────────────── --}}
    <div class="tw-bg-white tw-rounded-xl tw-border tw-border-gray-200 tw-shadow-sm tw-p-5">
        <h2 class="tw-font-semibold tw-text-gray-800 tw-mb-4" style="font-size:14px">
            Registered Devices
            <span class="tw-text-xs tw-text-gray-400 tw-font-normal">({{ $tokens->count() }} total)</span>
        </h2>
        @if($tokens->isEmpty())
            <p class="tw-text-sm tw-text-gray-400">No devices registered yet.</p>
        @else
        <div class="tw-overflow-x-auto" style="margin:0 -20px">
            <table class="tw-w-full tw-text-sm" style="min-width:560px">
                <thead>
                    <tr class="tw-border-b tw-border-gray-100 tw-text-xs tw-text-gray-400">
                        <th class="tw-px-5 tw-pb-2 tw-text-left">Device</th>
                        <th class="tw-px-3 tw-pb-2 tw-text-left">Type</th>
                        <th class="tw-px-3 tw-pb-2 tw-text-left">Last Pull</th>
                        <th class="tw-px-3 tw-pb-2 tw-text-left">Last Push</th>
                        <th class="tw-px-3 tw-pb-2 tw-text-left">Last Seen</th>
                        <th class="tw-px-5 tw-pb-2 tw-text-left">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tokens as $t)
                    <tr class="tw-border-b tw-border-gray-50 {{ $t->is_active ? '' : 'tw-opacity-40' }}">
                        <td class="tw-px-5 tw-py-3">
                            <p class="tw-font-medium tw-text-gray-800">{{ $t->device_name ?? 'Unnamed' }}</p>
                            <p class="tw-text-xs tw-text-gray-400" style="font-family:monospace">…{{ substr($t->token, -10) }}</p>
                        </td>
                        <td class="tw-px-3 tw-py-3">
                            <span class="tw-capitalize tw-text-xs tw-bg-gray-100 tw-text-gray-600 tw-px-2 tw-rounded-full">{{ $t->device_type }}</span>
                        </td>
                        <td class="tw-px-3 tw-py-3 tw-text-xs tw-text-gray-500">{{ $t->last_pulled_at ? $t->last_pulled_at->diffForHumans() : 'Never' }}</td>
                        <td class="tw-px-3 tw-py-3 tw-text-xs tw-text-gray-500">{{ $t->last_pushed_at ? $t->last_pushed_at->diffForHumans() : 'Never' }}</td>
                        <td class="tw-px-3 tw-py-3 tw-text-xs tw-text-gray-500">{{ $t->last_seen_at ? $t->last_seen_at->diffForHumans() : 'Never' }}</td>
                        <td class="tw-px-5 tw-py-3">
                            @if($t->is_active)
                                <span class="tw-text-xs tw-text-green-600 tw-font-medium">Active</span>
                                <button onclick="revokeDevice({{ $t->id }})"
                                        class="tw-ml-3 tw-text-red-500 tw-text-xs">Revoke</button>
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
    <div class="tw-bg-white tw-rounded-xl tw-border tw-border-gray-200 tw-shadow-sm tw-p-5">
        <h2 class="tw-font-semibold tw-text-gray-800 tw-mb-4" style="font-size:14px">
            Sync History
            <span class="tw-text-xs tw-text-gray-400 tw-font-normal">(last 50 operations)</span>
        </h2>
        @if($logs->isEmpty())
            <p class="tw-text-sm tw-text-gray-400">No sync history yet.</p>
        @else
        <div class="tw-overflow-x-auto" style="margin:0 -20px">
            <table class="tw-w-full tw-text-sm" style="min-width:480px">
                <thead>
                    <tr class="tw-border-b tw-border-gray-100 tw-text-xs tw-text-gray-400">
                        <th class="tw-px-5 tw-pb-2 tw-text-left">When</th>
                        <th class="tw-px-3 tw-pb-2 tw-text-left">Direction</th>
                        <th class="tw-px-3 tw-pb-2 tw-text-left">Status</th>
                        <th class="tw-px-3 tw-pb-2 tw-text-left">Records</th>
                        <th class="tw-px-5 tw-pb-2 tw-text-left">Device</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($logs as $log)
                    <tr class="tw-border-b tw-border-gray-50">
                        <td class="tw-px-5 tw-py-2 tw-text-xs tw-text-gray-500">{{ $log->synced_at->diffForHumans() }}</td>
                        <td class="tw-px-3 tw-py-2">
                            @if($log->direction === 'pull')
                            <span class="tw-text-blue-700 tw-bg-blue-50 tw-px-2 tw-rounded-full tw-text-xs">↓ Pull</span>
                            @else
                            <span class="tw-text-green-700 tw-bg-green-50 tw-px-2 tw-rounded-full tw-text-xs">↑ Push</span>
                            @endif
                        </td>
                        <td class="tw-px-3 tw-py-2 tw-text-xs tw-font-semibold
                            {{ $log->status === 'success' ? 'tw-text-green-700' : ($log->status === 'partial' ? 'tw-text-amber-600' : 'tw-text-red-600') }}">
                            {{ ucfirst($log->status) }}
                        </td>
                        <td class="tw-px-3 tw-py-2 tw-text-xs tw-text-gray-600">{{ $log->records_sent + $log->records_received }}</td>
                        <td class="tw-px-5 tw-py-2 tw-text-xs tw-text-gray-400">{{ optional($log->token)->device_name ?? '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

</div>
</section>
@endsection

@push('js')
<script src="{{ asset('js/cloud-sync.js') }}"></script>
<script>
const BUSINESS_ID = @json($businessId);
const APP_URL     = @json(config('app.url'));
const CSRF        = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

// ── Helper: show result message ──────────────────────────────────────────────
function showResult(msg, type) {
    const el = document.getElementById('sync-result');
    el.textContent = msg;
    el.className = 'tw-rounded-lg tw-px-4 tw-py-3 tw-text-sm tw-font-medium '
        + (type === 'success' ? 'tw-bg-green-100 tw-text-green-800'
         : type === 'error'   ? 'tw-bg-red-100 tw-text-red-800'
         :                      'tw-bg-blue-100 tw-text-blue-800');
    el.classList.remove('tw-hidden');
    setTimeout(() => el.classList.add('tw-hidden'), 6000);
}

function setBtnLoading(id, loading, label) {
    const btn = document.getElementById(id);
    if (!btn) return;
    btn.disabled = loading;
    if (loading) { btn.dataset.orig = btn.textContent; btn.textContent = '⟳ Working…'; }
    else         { btn.textContent = label || btn.dataset.orig || btn.textContent; }
}

// ── Core sync calls (direct fetch, no cloud-sync.js dependency) ──────────────
async function doPull() {
    setBtnLoading('btn-pull', true);
    try {
        const res  = await fetch('/sync/pull', {
            method : 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body   : JSON.stringify({ business_id: BUSINESS_ID }),
        });
        const data = await res.json();
        if (data.error) throw new Error(data.error);
        const total = Object.values(data).reduce((s,v) => s + (Array.isArray(v) ? v.length : 0), 0);
        showResult('✓ Pull complete — ' + total + ' records downloaded', 'success');
        document.getElementById('sync-last-pull').textContent = 'Just now';
    } catch (e) {
        showResult('✗ Pull failed: ' + e.message, 'error');
    } finally {
        setBtnLoading('btn-pull', false, '↓ Pull');
    }
}

async function doPush() {
    setBtnLoading('btn-push', true);
    try {
        const res  = await fetch('/sync/push', {
            method : 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body   : JSON.stringify({ business_id: BUSINESS_ID }),
        });
        const data = await res.json();
        if (data.error) throw new Error(data.error);
        showResult('✓ Push complete — ' + (data.upserted ?? 0) + ' records uploaded', 'success');
        document.getElementById('sync-last-push').textContent = 'Just now';
    } catch (e) {
        showResult('✗ Push failed: ' + e.message, 'error');
    } finally {
        setBtnLoading('btn-push', false, '↑ Push');
    }
}

async function doSync() {
    setBtnLoading('btn-sync', true);
    showResult('⟳ Running full sync…', 'info');
    await doPush();
    await doPull();
    setBtnLoading('btn-sync', false, '⟳ Sync Now');
}

// ── Connection badge ─────────────────────────────────────────────────────────
(function checkConnection() {
    const badge = document.getElementById('sync-connection-badge');
    fetch('/sync/status', { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF } })
        .then(r => r.json())
        .then(d => {
            badge.innerHTML = '<svg width="8" height="8" viewBox="0 0 8 8"><circle cx="4" cy="4" r="4" fill="#16a34a"/></svg> Online';
            badge.className = badge.className.replace('tw-text-gray-500 tw-bg-gray-100', 'tw-text-green-700 tw-bg-green-100');
        })
        .catch(() => {
            badge.innerHTML = '<svg width="8" height="8" viewBox="0 0 8 8"><circle cx="4" cy="4" r="4" fill="#dc2626"/></svg> Offline';
            badge.className = badge.className.replace('tw-text-gray-500 tw-bg-gray-100', 'tw-text-red-700 tw-bg-red-100');
        });
})();

// ── Register device ──────────────────────────────────────────────────────────
function registerDevice() {
    const name = document.getElementById('device-name-input').value.trim() || 'Unnamed Device';
    const type = document.getElementById('device-type-input').value;

    fetch('/sync/register', {
        method : 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
        body   : JSON.stringify({ business_id: BUSINESS_ID, device_name: name, device_type: type }),
    })
    .then(r => r.json())
    .then(data => {
        if (data.error) { alert(data.error); return; }

        const token = data.sync_token;
        localStorage.setItem('apexpos_sync_token_' + BUSINESS_ID, token);

        document.getElementById('new-token-value').textContent = token;
        document.getElementById('new-token-box').classList.remove('tw-hidden');

        if (type === 'desktop') {
            const snippet = 'SYNC_REMOTE_URL=' + APP_URL + '\nSYNC_TOKEN=' + token + '\nSYNC_BUSINESS_ID=' + BUSINESS_ID;
            document.getElementById('laragon-env-snippet').textContent = snippet;
            document.getElementById('laragon-instructions').classList.remove('tw-hidden');
        }

        setTimeout(() => location.reload(), 3000);
    })
    .catch(err => alert('Registration failed: ' + err.message));
}

function copyToken() {
    navigator.clipboard.writeText(document.getElementById('new-token-value').textContent)
        .then(() => { document.querySelector('[onclick="copyToken()"]').textContent = '✓ Copied!'; })
        .catch(() => alert('Select and copy the token manually.'));
}

// ── Revoke device ────────────────────────────────────────────────────────────
function revokeDevice(id) {
    if (!confirm('Revoke this device? It will no longer be able to sync.')) return;
    fetch('/sync/token/' + id, {
        method : 'DELETE',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
    })
    .then(() => location.reload())
    .catch(err => alert('Revoke failed: ' + err.message));
}
</script>
@endpush
