@extends('layouts.app')
@section('title', __('Cloud Sync'))

@section('content')
<section class="content-header">
    <h1 class="tw-text-2xl tw-font-bold">Cloud Sync</h1>
</section>

<section class="content">
<div class="tw-p-4 md:tw-p-6 tw-space-y-5 tw-max-w-5xl">

    {{-- ── Status bar ──────────────────────────────────────────────────────── --}}
    <div class="tw-flex tw-flex-wrap tw-items-center tw-gap-3">
        <span id="sync-connection-badge"
              style="display:inline-flex;align-items:center;gap:6px;font-size:12px;font-weight:600;background:#f3f4f6;color:#6b7280;border-radius:999px;padding:4px 12px">
            <span id="badge-dot" style="width:8px;height:8px;border-radius:50%;background:#9ca3af;display:inline-block"></span>
            <span id="badge-text">Checking connection…</span>
        </span>
        @if($conflicts > 0)
        <span style="display:inline-flex;align-items:center;gap:4px;font-size:12px;font-weight:600;background:#fef3c7;color:#92400e;border-radius:999px;padding:4px 12px">
            ⚠ {{ $conflicts }} unresolved conflict{{ $conflicts > 1 ? 's' : '' }}
        </span>
        @endif
        <span style="font-size:12px;color:#9ca3af">Server: <strong>{{ config('app.url') }}</strong></span>
    </div>

    {{-- ── How it works ─────────────────────────────────────────────────────── --}}
    <div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:12px;padding:20px">
        <p style="font-weight:600;color:#1e40af;margin:0 0 12px;font-size:14px">📋 How Cloud Sync Works</p>
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:16px">
            <div style="display:flex;gap:8px;align-items:flex-start">
                <span style="min-width:24px;min-height:24px;width:24px;height:24px;background:#bfdbfe;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:13px;color:#1e40af">1</span>
                <div>
                    <p style="font-weight:600;font-size:13px;color:#1e3a8a;margin:0 0 4px">Pull from Cloud</p>
                    <p style="font-size:12px;color:#1d4ed8;margin:0">Downloads products, contacts, stock, transactions from the live server into this browser. Do this before going offline.</p>
                </div>
            </div>
            <div style="display:flex;gap:8px;align-items:flex-start">
                <span style="min-width:24px;min-height:24px;width:24px;height:24px;background:#bfdbfe;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:13px;color:#1e40af">2</span>
                <div>
                    <p style="font-weight:600;font-size:13px;color:#1e3a8a;margin:0 0 4px">Work Offline</p>
                    <p style="font-size:12px;color:#1d4ed8;margin:0">Create sales, record payments and add contacts while offline. They queue automatically until you reconnect.</p>
                </div>
            </div>
            <div style="display:flex;gap:8px;align-items:flex-start">
                <span style="min-width:24px;min-height:24px;width:24px;height:24px;background:#bfdbfe;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:13px;color:#1e40af">3</span>
                <div>
                    <p style="font-weight:600;font-size:13px;color:#1e3a8a;margin:0 0 4px">Push to Cloud</p>
                    <p style="font-size:12px;color:#1d4ed8;margin:0">When back online, click Push to upload queued offline transactions to the server. Full Sync does both at once.</p>
                </div>
            </div>
        </div>
        <div style="border-top:1px solid #bfdbfe;margin-top:12px;padding-top:10px;font-size:12px;color:#1d4ed8">
            <strong>Laragon/Desktop:</strong> Register a device below, copy the token, add to your <code>.env</code> as <code>SYNC_TOKEN=…</code>, then run <code>php artisan sync:pull</code>
        </div>
    </div>

    {{-- ── Action cards ──────────────────────────────────────────────────────── --}}
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:16px">

        {{-- Pull --}}
        <div style="background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:20px;box-shadow:0 1px 3px rgba(0,0,0,.05)">
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px">
                <div style="background:#dbeafe;border-radius:8px;padding:8px;display:flex">
                    <svg width="20" height="20" fill="none" stroke="#2563eb" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/>
                    </svg>
                </div>
                <div>
                    <p style="font-weight:600;font-size:14px;margin:0">Pull from Cloud</p>
                    <p style="font-size:11px;color:#6b7280;margin:0">Cloud → This browser</p>
                </div>
            </div>
            <p id="sync-last-pull" style="font-size:11px;color:#9ca3af;margin:0 0 12px">Never pulled</p>
            <button id="btn-pull" onclick="doPull()"
                    style="width:100%;background:#2563eb;color:#fff;border:none;border-radius:8px;padding:8px 16px;font-size:13px;font-weight:600;cursor:pointer">
                ↓ Pull
            </button>
        </div>

        {{-- Push --}}
        <div style="background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:20px;box-shadow:0 1px 3px rgba(0,0,0,.05)">
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px">
                <div style="background:#dcfce7;border-radius:8px;padding:8px;display:flex">
                    <svg width="20" height="20" fill="none" stroke="#16a34a" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                    </svg>
                </div>
                <div>
                    <p style="font-weight:600;font-size:14px;margin:0">Push to Cloud</p>
                    <p style="font-size:11px;color:#6b7280;margin:0">Browser → Cloud</p>
                </div>
            </div>
            <p id="sync-last-push" style="font-size:11px;color:#9ca3af;margin:0 0 4px">Never pushed</p>
            <p style="font-size:11px;color:#d97706;margin:0 0 12px">
                <span id="sync-pending-count" style="font-weight:700">0</span> records pending
            </p>
            <button id="btn-push" onclick="doPush()"
                    style="width:100%;background:#16a34a;color:#fff;border:none;border-radius:8px;padding:8px 16px;font-size:13px;font-weight:600;cursor:pointer">
                ↑ Push
            </button>
        </div>

        {{-- Full Sync --}}
        <div style="background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:20px;box-shadow:0 1px 3px rgba(0,0,0,.05)">
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px">
                <div style="background:#f3e8ff;border-radius:8px;padding:8px;display:flex">
                    <svg width="20" height="20" fill="none" stroke="#9333ea" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                </div>
                <div>
                    <p style="font-weight:600;font-size:14px;margin:0">Full Sync</p>
                    <p style="font-size:11px;color:#6b7280;margin:0">Push then Pull (both ways)</p>
                </div>
            </div>
            <p style="font-size:11px;color:#9ca3af;margin:0 0 12px">Auto-runs every 5 min while online.</p>
            <button id="btn-sync" onclick="doSync()"
                    style="width:100%;background:#9333ea;color:#fff;border:none;border-radius:8px;padding:8px 16px;font-size:13px;font-weight:600;cursor:pointer">
                ⟳ Sync Now
            </button>
        </div>
    </div>

    {{-- ── Pull Results Panel ───────────────────────────────────────────────── --}}
    <div id="pull-results-panel" style="display:none;background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:20px;box-shadow:0 1px 3px rgba(0,0,0,.05)">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px">
            <p style="font-weight:600;font-size:14px;margin:0" id="pull-results-title">Pull Results</p>
            <span id="pull-results-time" style="font-size:11px;color:#9ca3af"></span>
        </div>

        {{-- Progress bar --}}
        <div id="pull-progress-wrap" style="display:none;margin-bottom:16px">
            <div style="display:flex;justify-content:space-between;font-size:12px;color:#6b7280;margin-bottom:4px">
                <span id="pull-progress-label">Pulling data…</span>
                <span id="pull-progress-pct">0%</span>
            </div>
            <div style="background:#f3f4f6;border-radius:999px;height:8px;overflow:hidden">
                <div id="pull-progress-bar" style="background:#2563eb;height:100%;width:0%;transition:width .4s"></div>
            </div>
        </div>

        {{-- Breakdown table --}}
        <table style="width:100%;border-collapse:collapse;font-size:13px" id="pull-breakdown-table">
            <thead>
                <tr style="border-bottom:2px solid #f3f4f6;text-align:left">
                    <th style="padding:6px 8px;font-weight:600;color:#6b7280;font-size:11px;text-transform:uppercase">Data Type</th>
                    <th style="padding:6px 8px;font-weight:600;color:#6b7280;font-size:11px;text-transform:uppercase;text-align:right">Records</th>
                    <th style="padding:6px 8px;font-weight:600;color:#6b7280;font-size:11px;text-transform:uppercase">Status</th>
                </tr>
            </thead>
            <tbody id="pull-breakdown-body">
                <tr><td colspan="3" style="padding:20px;text-align:center;color:#9ca3af">Click Pull to see what's downloaded</td></tr>
            </tbody>
        </table>

        <div id="pull-totals" style="display:none;border-top:2px solid #f3f4f6;margin-top:8px;padding-top:10px;display:flex;justify-content:space-between;align-items:center">
            <span style="font-size:12px;color:#6b7280">Total records downloaded</span>
            <span id="pull-total-count" style="font-weight:700;font-size:18px;color:#2563eb">0</span>
        </div>

        <div id="pull-error-box" style="display:none;margin-top:12px;background:#fef2f2;border:1px solid #fecaca;border-radius:8px;padding:12px;font-size:12px;color:#b91c1c">
            <strong>⚠ Some data failed to pull:</strong>
            <ul id="pull-error-list" style="margin:4px 0 0 16px;padding:0"></ul>
        </div>
    </div>

    {{-- ── Register New Device ──────────────────────────────────────────────── --}}
    <div style="background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:20px;box-shadow:0 1px 3px rgba(0,0,0,.05)">
        <p style="font-weight:600;font-size:14px;margin:0 0 4px">＋ Register a Device / Get Token</p>
        <p style="font-size:12px;color:#6b7280;margin:0 0 14px">
            Register once per device. <em>Browser</em> = this POS terminal (auto-auth via session).
            <em>Desktop</em> = your Laragon dev machine (needs the token in <code>.env</code>).
        </p>
        <div style="display:flex;flex-wrap:wrap;gap:10px">
            <input type="text" id="device-name-input"
                   placeholder="e.g. Cashier PC – Branch 1"
                   style="flex:1;min-width:200px;border:1px solid #d1d5db;border-radius:8px;padding:8px 12px;font-size:13px">
            <select id="device-type-input"
                    style="border:1px solid #d1d5db;border-radius:8px;padding:8px 12px;font-size:13px">
                <option value="browser">Browser / POS terminal</option>
                <option value="desktop">Laragon / Desktop app</option>
                <option value="mobile">Mobile phone</option>
            </select>
            <button type="button" onclick="registerDevice()"
                    style="background:#111827;color:#fff;border:none;border-radius:8px;padding:8px 20px;font-size:13px;font-weight:600;cursor:pointer">
                Register &amp; Get Token
            </button>
        </div>

        <div id="new-token-box" style="display:none;margin-top:14px;border:2px dashed #93c5fd;background:#eff6ff;border-radius:10px;padding:16px">
            <p style="font-size:12px;font-weight:600;color:#1e40af;margin:0 0 8px">✓ Device registered — copy your token now (shown only once)</p>
            <div style="display:flex;gap:8px;margin-bottom:10px">
                <code id="new-token-value"
                      style="flex:1;font-size:11px;background:#fff;border:1px solid #bfdbfe;border-radius:6px;padding:8px 12px;word-break:break-all;font-family:monospace"></code>
                <button onclick="copyToken()"
                        style="background:#fff;border:1px solid #bfdbfe;border-radius:6px;padding:6px 10px;font-size:12px;font-weight:600;color:#1d4ed8;cursor:pointer;white-space:nowrap">
                    Copy
                </button>
            </div>
            <div id="laragon-instructions" style="display:none">
                <p style="font-size:12px;font-weight:600;color:#1e40af;margin:0 0 6px">Add to your Laragon <code>.env</code>:</p>
                <pre id="laragon-env-snippet"
                     style="background:#111827;color:#86efac;border-radius:8px;padding:12px;font-size:11px;overflow-x:auto;font-family:monospace;margin:0 0 8px"></pre>
                <p style="font-size:12px;color:#1d4ed8;margin:0">
                    Then run: <code style="background:#fff;padding:1px 4px;border-radius:3px">php artisan sync:pull</code>
                    to download all live data to your local database.
                </p>
            </div>
        </div>
    </div>

    {{-- ── Registered Devices ───────────────────────────────────────────────── --}}
    <div style="background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:20px;box-shadow:0 1px 3px rgba(0,0,0,.05)">
        <p style="font-weight:600;font-size:14px;margin:0 0 14px">
            Registered Devices
            <span style="font-size:12px;font-weight:400;color:#9ca3af">({{ $tokens->count() }} total)</span>
        </p>
        @if($tokens->isEmpty())
            <p style="font-size:13px;color:#9ca3af;margin:0">No devices registered yet. Register one above.</p>
        @else
        <div style="overflow-x:auto">
            <table style="width:100%;border-collapse:collapse;font-size:13px;min-width:520px">
                <thead>
                    <tr style="border-bottom:2px solid #f3f4f6;font-size:11px;color:#9ca3af;text-transform:uppercase;text-align:left">
                        <th style="padding:6px 8px">Device</th>
                        <th style="padding:6px 8px">Type</th>
                        <th style="padding:6px 8px">Last Pull</th>
                        <th style="padding:6px 8px">Last Push</th>
                        <th style="padding:6px 8px">Last Seen</th>
                        <th style="padding:6px 8px">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tokens as $t)
                    <tr style="border-bottom:1px solid #f9fafb;{{ $t->is_active ? '' : 'opacity:.4' }}">
                        <td style="padding:10px 8px">
                            <p style="font-weight:600;margin:0">{{ $t->device_name ?? 'Unnamed' }}</p>
                            <p style="font-size:10px;color:#9ca3af;font-family:monospace;margin:0">…{{ substr($t->token,-10) }}</p>
                        </td>
                        <td style="padding:10px 8px">
                            <span style="background:#f3f4f6;color:#4b5563;border-radius:999px;padding:2px 8px;font-size:11px;text-transform:capitalize">{{ $t->device_type }}</span>
                        </td>
                        <td style="padding:10px 8px;font-size:12px;color:#6b7280">{{ $t->last_pulled_at ? $t->last_pulled_at->diffForHumans() : 'Never' }}</td>
                        <td style="padding:10px 8px;font-size:12px;color:#6b7280">{{ $t->last_pushed_at ? $t->last_pushed_at->diffForHumans() : 'Never' }}</td>
                        <td style="padding:10px 8px;font-size:12px;color:#6b7280">{{ $t->last_seen_at ? $t->last_seen_at->diffForHumans() : 'Never' }}</td>
                        <td style="padding:10px 8px">
                            @if($t->is_active)
                                <span style="color:#16a34a;font-size:12px;font-weight:600">● Active</span>
                                <button onclick="revokeDevice({{ $t->id }})"
                                        style="background:none;border:none;color:#dc2626;font-size:11px;cursor:pointer;margin-left:8px">Revoke</button>
                            @else
                                <span style="color:#9ca3af;font-size:12px">Revoked</span>
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
    <div style="background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:20px;box-shadow:0 1px 3px rgba(0,0,0,.05)">
        <p style="font-weight:600;font-size:14px;margin:0 0 14px">
            Sync History
            <span style="font-size:12px;font-weight:400;color:#9ca3af">(last 50 operations)</span>
        </p>
        @if($logs->isEmpty())
            <p style="font-size:13px;color:#9ca3af;margin:0">No sync history yet. Run your first Pull or Push above.</p>
        @else
        <div style="overflow-x:auto">
            <table style="width:100%;border-collapse:collapse;font-size:13px;min-width:480px">
                <thead>
                    <tr style="border-bottom:2px solid #f3f4f6;font-size:11px;color:#9ca3af;text-transform:uppercase;text-align:left">
                        <th style="padding:6px 8px">When</th>
                        <th style="padding:6px 8px">Direction</th>
                        <th style="padding:6px 8px">Status</th>
                        <th style="padding:6px 8px">Records</th>
                        <th style="padding:6px 8px">Device</th>
                        <th style="padding:6px 8px">Breakdown</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($logs as $log)
                    <tr style="border-bottom:1px solid #f9fafb">
                        <td style="padding:8px;font-size:12px;color:#6b7280;white-space:nowrap">{{ $log->synced_at->diffForHumans() }}</td>
                        <td style="padding:8px">
                            @if($log->direction === 'pull')
                            <span style="background:#dbeafe;color:#1d4ed8;border-radius:999px;padding:2px 8px;font-size:11px;font-weight:600">↓ Pull</span>
                            @else
                            <span style="background:#dcfce7;color:#166534;border-radius:999px;padding:2px 8px;font-size:11px;font-weight:600">↑ Push</span>
                            @endif
                        </td>
                        <td style="padding:8px;font-size:12px;font-weight:600;color:{{ $log->status==='success'?'#16a34a':($log->status==='partial'?'#d97706':'#dc2626') }}">
                            {{ ucfirst($log->status) }}
                        </td>
                        <td style="padding:8px;font-size:13px;font-weight:700;color:#1f2937">{{ $log->records_sent + $log->records_received }}</td>
                        <td style="padding:8px;font-size:12px;color:#9ca3af">{{ optional($log->token)->device_name ?? '—' }}</td>
                        <td style="padding:8px;font-size:11px;color:#6b7280">
                            @if($log->summary)
                                @foreach($log->summary as $k => $v)
                                    @if($v > 0)<span style="margin-right:6px;white-space:nowrap">{{ $k }}: <strong>{{ $v }}</strong></span>@endif
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
</section>
@endsection

@push('js')
<script>
const BUSINESS_ID = @json($businessId);
const APP_URL     = @json(config('app.url'));
const CSRF        = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

// ── Connection badge ─────────────────────────────────────────────────────────
(function checkConnection() {
    fetch('/sync/status', { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF } })
        .then(r => r.json())
        .then(() => {
            document.getElementById('badge-dot').style.background = '#16a34a';
            document.getElementById('badge-text').textContent = 'Online';
            document.getElementById('sync-connection-badge').style.background = '#dcfce7';
            document.getElementById('sync-connection-badge').style.color = '#166534';
        })
        .catch(() => {
            document.getElementById('badge-dot').style.background = '#dc2626';
            document.getElementById('badge-text').textContent = 'Offline';
            document.getElementById('sync-connection-badge').style.background = '#fef2f2';
            document.getElementById('sync-connection-badge').style.color = '#991b1b';
        });
})();

// ── Button helpers ───────────────────────────────────────────────────────────
function setBtnLoading(id, loading, label) {
    const btn = document.getElementById(id);
    if (!btn) return;
    btn.disabled = loading;
    btn.style.opacity = loading ? '.6' : '1';
    btn.style.cursor  = loading ? 'wait' : 'pointer';
    if (loading) { btn.dataset.orig = btn.textContent; btn.textContent = '⟳ Working…'; }
    else if (label) btn.textContent = label;
    else if (btn.dataset.orig) btn.textContent = btn.dataset.orig;
}

// ── Pull ─────────────────────────────────────────────────────────────────────
const PULL_LABELS = {
    business: 'Business settings', locations: 'Locations', categories: 'Categories',
    brands: 'Brands', units: 'Units', tax_rates: 'Tax rates',
    contacts: 'Contacts', products: 'Products', stock: 'Stock levels',
    transactions: 'Transactions (last 30 days)'
};

function showPullPanel(state) {
    // state: 'loading' | data object
    const panel = document.getElementById('pull-results-panel');
    const tbody  = document.getElementById('pull-breakdown-body');
    panel.style.display = 'block';

    if (state === 'loading') {
        document.getElementById('pull-results-title').textContent = 'Pull Results';
        document.getElementById('pull-results-time').textContent = '';
        document.getElementById('pull-progress-wrap').style.display = 'block';
        document.getElementById('pull-progress-bar').style.width = '0%';
        document.getElementById('pull-progress-pct').textContent = '0%';
        document.getElementById('pull-progress-label').textContent = 'Connecting to server…';
        document.getElementById('pull-error-box').style.display = 'none';
        document.getElementById('pull-totals').style.display = 'none';

        // Skeleton rows while loading
        const keys = Object.keys(PULL_LABELS);
        tbody.innerHTML = keys.map(k => `
            <tr style="border-bottom:1px solid #f9fafb">
                <td style="padding:8px 8px;color:#6b7280">${PULL_LABELS[k]}</td>
                <td style="padding:8px 8px;text-align:right">
                    <span style="display:inline-block;width:32px;height:14px;background:#f3f4f6;border-radius:4px;animation:pulse 1.5s infinite"></span>
                </td>
                <td style="padding:8px 8px"><span style="color:#9ca3af;font-size:11px">waiting…</span></td>
            </tr>`).join('');
        return;
    }

    // Received data
    const { summary, labels, errors, pulled_at, is_full } = state;
    document.getElementById('pull-progress-wrap').style.display = 'none';
    document.getElementById('pull-results-title').textContent = is_full ? '✓ Full Pull Complete' : '✓ Delta Pull Complete';
    document.getElementById('pull-results-time').textContent = 'at ' + new Date(pulled_at).toLocaleTimeString();

    const keys = Object.keys(labels || PULL_LABELS);
    let total = 0;
    tbody.innerHTML = keys.map(k => {
        const count = (summary && summary[k]) ?? 0;
        total += count;
        const hasError = errors && errors[k];
        const statusHtml = hasError
            ? `<span style="color:#dc2626;font-size:11px">✗ Error</span>`
            : count > 0
                ? `<span style="color:#16a34a;font-size:11px;font-weight:600">✓ Synced</span>`
                : `<span style="color:#9ca3af;font-size:11px">No changes</span>`;
        return `
            <tr style="border-bottom:1px solid #f9fafb">
                <td style="padding:8px;color:#374151;font-size:13px">${(labels && labels[k]) || k}</td>
                <td style="padding:8px;text-align:right;font-weight:700;font-size:15px;color:${count>0?'#2563eb':'#d1d5db'}">${count > 0 ? count.toLocaleString() : '—'}</td>
                <td style="padding:8px">${statusHtml}</td>
            </tr>`;
    }).join('');

    // Total row
    document.getElementById('pull-total-count').textContent = total.toLocaleString();
    const totals = document.getElementById('pull-totals');
    totals.style.display = 'flex';

    // Errors
    const errBox = document.getElementById('pull-error-box');
    if (errors && Object.keys(errors).length > 0) {
        errBox.style.display = 'block';
        document.getElementById('pull-error-list').innerHTML =
            Object.entries(errors).map(([k,v]) => `<li><strong>${k}:</strong> ${v}</li>`).join('');
    }
}

function animateProgress(pct, label) {
    document.getElementById('pull-progress-bar').style.width = pct + '%';
    document.getElementById('pull-progress-pct').textContent = pct + '%';
    if (label) document.getElementById('pull-progress-label').textContent = label;
}

async function doPull() {
    setBtnLoading('btn-pull', true);
    showPullPanel('loading');
    animateProgress(10, 'Connecting to server…');

    try {
        animateProgress(30, 'Downloading data…');
        const res = await fetch('/sync/pull', {
            method : 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body   : JSON.stringify({ business_id: BUSINESS_ID }),
        });
        animateProgress(80, 'Processing response…');
        const data = await res.json();

        if (data.error) {
            document.getElementById('pull-results-title').textContent = '✗ Pull Failed';
            document.getElementById('pull-progress-wrap').style.display = 'none';
            document.getElementById('pull-error-box').style.display = 'block';
            document.getElementById('pull-error-list').innerHTML = `<li>${data.error}</li>`;
            document.getElementById('pull-breakdown-body').innerHTML =
                `<td colspan="3" style="padding:12px;color:#dc2626;font-size:13px">${data.error}</td>`;
            return;
        }

        animateProgress(100, 'Done!');
        showPullPanel(data);

        const total = data.summary ? Object.values(data.summary).reduce((a,b) => a+b, 0) : 0;
        document.getElementById('sync-last-pull').textContent = 'Just now (' + total.toLocaleString() + ' records)';

        if (typeof toastr !== 'undefined')
            toastr.success('Pull complete — ' + total.toLocaleString() + ' records downloaded');

    } catch (e) {
        document.getElementById('pull-results-title').textContent = '✗ Pull Failed';
        document.getElementById('pull-error-box').style.display = 'block';
        document.getElementById('pull-error-list').innerHTML = `<li>${e.message}</li>`;
        document.getElementById('pull-progress-wrap').style.display = 'none';
        if (typeof toastr !== 'undefined') toastr.error('Pull failed: ' + e.message);
    } finally {
        setBtnLoading('btn-pull', false, '↓ Pull');
    }
}

// ── Push ─────────────────────────────────────────────────────────────────────
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
        const count = data.upserted ?? 0;
        document.getElementById('sync-last-push').textContent = 'Just now (' + count + ' records)';
        if (typeof toastr !== 'undefined')
            toastr.success('Push complete — ' + count + ' records uploaded');
    } catch (e) {
        if (typeof toastr !== 'undefined') toastr.error('Push failed: ' + e.message);
    } finally {
        setBtnLoading('btn-push', false, '↑ Push');
    }
}

async function doSync() {
    setBtnLoading('btn-sync', true);
    await doPush();
    await doPull();
    setBtnLoading('btn-sync', false, '⟳ Sync Now');
}

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
        document.getElementById('new-token-box').style.display = 'block';
        if (type === 'desktop') {
            const snippet = 'SYNC_REMOTE_URL=' + APP_URL + '\nSYNC_TOKEN=' + token + '\nSYNC_BUSINESS_ID=' + BUSINESS_ID;
            document.getElementById('laragon-env-snippet').textContent = snippet;
            document.getElementById('laragon-instructions').style.display = 'block';
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

function revokeDevice(id) {
    if (!confirm('Revoke this device? It will no longer be able to sync.')) return;
    fetch('/sync/token/' + id, {
        method : 'DELETE',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
    })
    .then(() => location.reload())
    .catch(err => alert('Revoke failed: ' + err.message));
}

// Pulse animation for skeleton
const style = document.createElement('style');
style.textContent = '@keyframes pulse { 0%,100%{opacity:1} 50%{opacity:.4} }';
document.head.appendChild(style);
</script>
@endpush
