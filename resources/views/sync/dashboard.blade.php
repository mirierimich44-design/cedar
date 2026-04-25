@extends('layouts.app')
@section('title', __('Cloud Sync'))

@section('content')
<section class="content-header">
    <h1>Cloud Sync</h1>
</section>

<section class="content">
<div style="padding:16px;max-width:960px">

    {{-- ── Status bar ──────────────────────────────────────────────────────── --}}
    <div style="display:flex;flex-wrap:wrap;align-items:center;gap:10px;margin-bottom:20px">
        <span id="sync-connection-badge"
              style="display:inline-flex;align-items:center;gap:6px;font-size:12px;font-weight:600;background:#f3f4f6;color:#6b7280;border-radius:999px;padding:4px 14px">
            <span id="badge-dot" style="width:8px;height:8px;border-radius:50%;background:#9ca3af;display:inline-block"></span>
            <span id="badge-text">Checking connection…</span>
        </span>
        @if($conflicts > 0)
        <span style="font-size:12px;font-weight:600;background:#fef3c7;color:#92400e;border-radius:999px;padding:4px 14px">
            ⚠ {{ $conflicts }} conflict{{ $conflicts > 1 ? 's' : '' }}
        </span>
        @endif
        <span style="font-size:12px;color:#9ca3af">
            Server: <strong id="display-remote-url">{{ config('app.url') }}</strong>
        </span>
    </div>

    {{-- ── How it works ─────────────────────────────────────────────────────── --}}
    <div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:10px;padding:18px;margin-bottom:20px">
        <p style="font-weight:700;color:#1e40af;margin:0 0 10px;font-size:13px">📋 How Cloud Sync Works</p>
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:14px;margin-bottom:10px">
            <div style="display:flex;gap:8px">
                <span style="min-width:22px;height:22px;background:#bfdbfe;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:12px;color:#1e40af">1</span>
                <div>
                    <p style="font-weight:600;font-size:12px;color:#1e3a8a;margin:0 0 2px">Pull from Cloud</p>
                    <p style="font-size:11px;color:#1d4ed8;margin:0">Downloads products, contacts, stock &amp; transactions.</p>
                </div>
            </div>
            <div style="display:flex;gap:8px">
                <span style="min-width:22px;height:22px;background:#bfdbfe;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:12px;color:#1e40af">2</span>
                <div>
                    <p style="font-weight:600;font-size:12px;color:#1e3a8a;margin:0 0 2px">Work Offline</p>
                    <p style="font-size:11px;color:#1d4ed8;margin:0">Create sales &amp; contacts — they queue until you reconnect.</p>
                </div>
            </div>
            <div style="display:flex;gap:8px">
                <span style="min-width:22px;height:22px;background:#bfdbfe;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:12px;color:#1e40af">3</span>
                <div>
                    <p style="font-weight:600;font-size:12px;color:#1e3a8a;margin:0 0 2px">Push to Cloud</p>
                    <p style="font-size:11px;color:#1d4ed8;margin:0">Upload offline transactions back to the live server.</p>
                </div>
            </div>
        </div>
        <p style="font-size:11px;color:#1d4ed8;margin:0;border-top:1px solid #bfdbfe;padding-top:8px">
            <strong>Laragon / Desktop:</strong> Configure the remote URL &amp; token in the
            <a href="#settings-panel" style="color:#2563eb" onclick="document.getElementById('settings-panel').scrollIntoView({behavior:'smooth'});return false">⚙ Settings</a>
            panel below, then run <code>php artisan sync:pull</code>.
        </p>
    </div>

    {{-- ── Action cards ──────────────────────────────────────────────────────── --}}
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:14px;margin-bottom:20px">

        {{-- Pull --}}
        <div style="background:#fff;border:1px solid #e5e7eb;border-radius:10px;padding:18px;box-shadow:0 1px 2px rgba(0,0,0,.04)">
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px">
                <div style="background:#dbeafe;border-radius:8px;padding:7px;line-height:0">
                    <svg width="18" height="18" fill="none" stroke="#2563eb" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/>
                    </svg>
                </div>
                <div>
                    <p style="font-weight:600;font-size:13px;margin:0">Pull from Cloud</p>
                    <p style="font-size:11px;color:#6b7280;margin:0">Cloud → This device</p>
                </div>
            </div>
            <p id="sync-last-pull" style="font-size:11px;color:#9ca3af;margin:0 0 10px">Never pulled</p>
            <button id="btn-pull" onclick="doPull()"
                    style="width:100%;background:#2563eb;color:#fff;border:none;border-radius:7px;padding:7px 14px;font-size:12px;font-weight:600;cursor:pointer">
                ↓ Pull
            </button>
        </div>

        {{-- Push --}}
        <div style="background:#fff;border:1px solid #e5e7eb;border-radius:10px;padding:18px;box-shadow:0 1px 2px rgba(0,0,0,.04)">
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px">
                <div style="background:#dcfce7;border-radius:8px;padding:7px;line-height:0">
                    <svg width="18" height="18" fill="none" stroke="#16a34a" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                    </svg>
                </div>
                <div>
                    <p style="font-weight:600;font-size:13px;margin:0">Push to Cloud</p>
                    <p style="font-size:11px;color:#6b7280;margin:0">This device → Cloud</p>
                </div>
            </div>
            <p id="sync-last-push" style="font-size:11px;color:#9ca3af;margin:0 0 4px">Never pushed</p>
            <p style="font-size:11px;color:#d97706;margin:0 0 10px"><span id="sync-pending-count" style="font-weight:700">0</span> records pending</p>
            <button id="btn-push" onclick="doPush()"
                    style="width:100%;background:#16a34a;color:#fff;border:none;border-radius:7px;padding:7px 14px;font-size:12px;font-weight:600;cursor:pointer">
                ↑ Push
            </button>
        </div>

        {{-- Full Sync --}}
        <div style="background:#fff;border:1px solid #e5e7eb;border-radius:10px;padding:18px;box-shadow:0 1px 2px rgba(0,0,0,.04)">
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px">
                <div style="background:#f3e8ff;border-radius:8px;padding:7px;line-height:0">
                    <svg width="18" height="18" fill="none" stroke="#9333ea" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                </div>
                <div>
                    <p style="font-weight:600;font-size:13px;margin:0">Full Sync</p>
                    <p style="font-size:11px;color:#6b7280;margin:0">Push then Pull</p>
                </div>
            </div>
            <p style="font-size:11px;color:#9ca3af;margin:0 0 10px">Both directions at once.</p>
            <button id="btn-sync" onclick="doSync()"
                    style="width:100%;background:#9333ea;color:#fff;border:none;border-radius:7px;padding:7px 14px;font-size:12px;font-weight:600;cursor:pointer">
                ⟳ Sync Now
            </button>
        </div>
    </div>

    {{-- ── Response Panel (shown after any button click) ─────────────────────── --}}
    <div id="response-panel" style="display:none;background:#fff;border:1px solid #e5e7eb;border-radius:10px;padding:20px;margin-bottom:20px;box-shadow:0 1px 2px rgba(0,0,0,.04)">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px">
            <p id="resp-title" style="font-weight:700;font-size:14px;margin:0">Results</p>
            <span id="resp-time" style="font-size:11px;color:#9ca3af"></span>
        </div>

        {{-- Progress --}}
        <div id="resp-progress" style="display:none;margin-bottom:14px">
            <div style="display:flex;justify-content:space-between;font-size:11px;color:#6b7280;margin-bottom:4px">
                <span id="resp-progress-label">Working…</span>
                <span id="resp-progress-pct">0%</span>
            </div>
            <div style="background:#f3f4f6;border-radius:999px;height:7px;overflow:hidden">
                <div id="resp-progress-bar" style="background:#2563eb;height:100%;width:0%;transition:width .35s ease"></div>
            </div>
        </div>

        {{-- Pull breakdown table --}}
        <table style="width:100%;border-collapse:collapse;font-size:13px" id="resp-table">
            <thead>
                <tr style="border-bottom:2px solid #f3f4f6;text-align:left">
                    <th style="padding:5px 8px;font-size:11px;color:#9ca3af;font-weight:600;text-transform:uppercase">Data</th>
                    <th style="padding:5px 8px;font-size:11px;color:#9ca3af;font-weight:600;text-transform:uppercase;text-align:right">Records</th>
                    <th style="padding:5px 8px;font-size:11px;color:#9ca3af;font-weight:600;text-transform:uppercase">Status</th>
                </tr>
            </thead>
            <tbody id="resp-tbody">
                <tr><td colspan="3" style="padding:14px 8px;color:#9ca3af;text-align:center">Click Pull to see what's downloaded</td></tr>
            </tbody>
        </table>

        <div id="resp-total-row" style="display:none;border-top:2px solid #f3f4f6;margin-top:6px;padding-top:8px;display:flex;justify-content:space-between;align-items:center">
            <span style="font-size:12px;color:#6b7280">Total records</span>
            <span id="resp-total-count" style="font-weight:700;font-size:18px;color:#2563eb">0</span>
        </div>

        <div id="resp-error-box" style="display:none;margin-top:12px;background:#fef2f2;border:1px solid #fecaca;border-radius:8px;padding:12px;font-size:12px;color:#b91c1c">
            <strong>⚠ Error:</strong>
            <pre id="resp-error-text" style="margin:4px 0 0;white-space:pre-wrap;font-family:monospace;font-size:11px"></pre>
        </div>
    </div>

    {{-- ── Register Device ─────────────────────────────────────────────────── --}}
    <div style="background:#fff;border:1px solid #e5e7eb;border-radius:10px;padding:20px;margin-bottom:20px;box-shadow:0 1px 2px rgba(0,0,0,.04)">
        <p style="font-weight:700;font-size:14px;margin:0 0 4px">＋ Register a Device / Get Token</p>
        <p style="font-size:12px;color:#6b7280;margin:0 0 14px">
            <strong>Browser</strong> = this POS terminal (auto-auth via session, no token needed for Pull/Push).
            <strong>Desktop/Laragon</strong> = gets a token to use with <code>php artisan sync:pull</code>.
        </p>
        <div style="display:flex;flex-wrap:wrap;gap:10px;margin-bottom:10px">
            <input type="text" id="device-name-input" placeholder="e.g. Cashier Laptop – Branch 1"
                   style="flex:1;min-width:180px;border:1px solid #d1d5db;border-radius:7px;padding:7px 11px;font-size:12px">
            <select id="device-type-input"
                    style="border:1px solid #d1d5db;border-radius:7px;padding:7px 11px;font-size:12px">
                <option value="browser">Browser / POS terminal</option>
                <option value="desktop">Desktop / Laragon</option>
                <option value="mobile">Mobile phone</option>
            </select>
            <button type="button" onclick="registerDevice()"
                    style="background:#111827;color:#fff;border:none;border-radius:7px;padding:7px 18px;font-size:12px;font-weight:600;cursor:pointer">
                Register &amp; Get Token
            </button>
        </div>
        <div id="register-error" style="display:none;background:#fef2f2;border:1px solid #fecaca;border-radius:7px;padding:10px;font-size:12px;color:#b91c1c;margin-bottom:10px"></div>

        <div id="new-token-box" style="display:none;border:2px dashed #93c5fd;background:#eff6ff;border-radius:9px;padding:14px">
            <p style="font-size:12px;font-weight:700;color:#1e40af;margin:0 0 8px">✓ Device registered — copy your token now (shown only once)</p>
            <div style="display:flex;gap:8px;margin-bottom:10px">
                <code id="new-token-value"
                      style="flex:1;font-size:11px;background:#fff;border:1px solid #bfdbfe;border-radius:6px;padding:7px 10px;word-break:break-all;font-family:monospace"></code>
                <button onclick="copyToken()"
                        style="background:#fff;border:1px solid #bfdbfe;border-radius:6px;padding:5px 10px;font-size:12px;font-weight:600;color:#1d4ed8;cursor:pointer;white-space:nowrap">
                    Copy
                </button>
            </div>
            <div id="laragon-instructions" style="display:none;background:#111827;border-radius:8px;padding:12px">
                <p style="font-size:11px;color:#86efac;margin:0 0 4px;font-family:monospace">
                    # Add to your Laragon .env — OR save via ⚙ Settings below
                </p>
                <pre id="laragon-env-snippet" style="margin:0;font-size:11px;color:#fbbf24;font-family:monospace;white-space:pre-wrap"></pre>
                <p style="font-size:11px;color:#86efac;margin:6px 0 0;font-family:monospace"># Then run: php artisan sync:pull</p>
            </div>
        </div>
    </div>

    {{-- ── Settings Panel ──────────────────────────────────────────────────── --}}
    <div id="settings-panel" style="background:#fff;border:2px solid #e5e7eb;border-radius:10px;padding:20px;margin-bottom:20px;box-shadow:0 1px 2px rgba(0,0,0,.04)">
        <p style="font-weight:700;font-size:14px;margin:0 0 4px">⚙ Sync Settings</p>
        <p style="font-size:12px;color:#6b7280;margin:0 0 16px">
            Configure where <code>php artisan sync:pull</code> connects to. Settings are saved on this server — no need to edit <code>.env</code>.
        </p>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:14px">
            <div>
                <label style="font-size:12px;font-weight:600;color:#374151;display:block;margin-bottom:4px">
                    Remote Server URL
                    <span style="font-weight:400;color:#9ca3af">(the live Hostinger server)</span>
                </label>
                <input type="url" id="setting-remote-url"
                       placeholder="https://reenson.apextechsolutions.co.ke"
                       style="width:100%;border:1px solid #d1d5db;border-radius:7px;padding:7px 11px;font-size:12px;box-sizing:border-box">
            </div>
            <div>
                <label style="font-size:12px;font-weight:600;color:#374151;display:block;margin-bottom:4px">
                    Sync Token
                    <span style="font-weight:400;color:#9ca3af">(from Register Device above)</span>
                </label>
                <input type="text" id="setting-sync-token"
                       placeholder="Paste token from Register Device above"
                       style="width:100%;border:1px solid #d1d5db;border-radius:7px;padding:7px 11px;font-size:12px;box-sizing:border-box;font-family:monospace">
            </div>
        </div>

        <div style="display:flex;align-items:center;gap:10px">
            <button onclick="saveSettings()"
                    style="background:#2563eb;color:#fff;border:none;border-radius:7px;padding:7px 20px;font-size:12px;font-weight:600;cursor:pointer">
                💾 Save Settings
            </button>
            <span id="settings-saved-msg" style="display:none;font-size:12px;color:#16a34a;font-weight:600">✓ Saved!</span>
            <span id="settings-error-msg" style="display:none;font-size:12px;color:#dc2626"></span>
        </div>

        <p style="font-size:11px;color:#9ca3af;margin:10px 0 0">
            Settings are stored in <code>storage/app/sync_config_{{ $businessId }}.json</code> and read automatically by <code>php artisan sync:pull</code>.
        </p>
    </div>

    {{-- ── Registered Devices ──────────────────────────────────────────────── --}}
    <div style="background:#fff;border:1px solid #e5e7eb;border-radius:10px;padding:20px;margin-bottom:20px;box-shadow:0 1px 2px rgba(0,0,0,.04)">
        <p style="font-weight:700;font-size:14px;margin:0 0 14px">
            Registered Devices
            <span style="font-size:12px;font-weight:400;color:#9ca3af">({{ $tokens->count() }} total)</span>
        </p>
        @if($tokens->isEmpty())
            <p style="font-size:13px;color:#9ca3af;margin:0">No devices registered yet. Register one above.</p>
        @else
        <div style="overflow-x:auto">
            <table style="width:100%;border-collapse:collapse;font-size:12px;min-width:480px">
                <thead>
                    <tr style="border-bottom:2px solid #f3f4f6;font-size:10px;color:#9ca3af;text-transform:uppercase;text-align:left">
                        <th style="padding:5px 8px">Device</th>
                        <th style="padding:5px 8px">Type</th>
                        <th style="padding:5px 8px">Last Pull</th>
                        <th style="padding:5px 8px">Last Push</th>
                        <th style="padding:5px 8px">Last Seen</th>
                        <th style="padding:5px 8px">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tokens as $t)
                    <tr style="border-bottom:1px solid #f9fafb;{{ $t->is_active ? '' : 'opacity:.4' }}">
                        <td style="padding:9px 8px">
                            <p style="font-weight:600;margin:0">{{ $t->device_name ?? 'Unnamed' }}</p>
                            <p style="font-size:10px;color:#9ca3af;font-family:monospace;margin:0">…{{ substr($t->token,-10) }}</p>
                        </td>
                        <td style="padding:9px 8px">
                            <span style="background:#f3f4f6;color:#4b5563;border-radius:999px;padding:2px 8px;font-size:10px;text-transform:capitalize">{{ $t->device_type }}</span>
                        </td>
                        <td style="padding:9px 8px;color:#6b7280">{{ $t->last_pulled_at ? $t->last_pulled_at->diffForHumans() : 'Never' }}</td>
                        <td style="padding:9px 8px;color:#6b7280">{{ $t->last_pushed_at ? $t->last_pushed_at->diffForHumans() : 'Never' }}</td>
                        <td style="padding:9px 8px;color:#6b7280">{{ $t->last_seen_at ? $t->last_seen_at->diffForHumans() : 'Never' }}</td>
                        <td style="padding:9px 8px">
                            @if($t->is_active)
                                <span style="color:#16a34a;font-size:11px;font-weight:600">● Active</span>
                                <button onclick="revokeDevice({{ $t->id }})"
                                        style="background:none;border:none;color:#dc2626;font-size:11px;cursor:pointer;margin-left:6px">Revoke</button>
                            @else
                                <span style="color:#9ca3af;font-size:11px">Revoked</span>
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
    <div style="background:#fff;border:1px solid #e5e7eb;border-radius:10px;padding:20px;box-shadow:0 1px 2px rgba(0,0,0,.04)">
        <p style="font-weight:700;font-size:14px;margin:0 0 14px">
            Sync History
            <span style="font-size:12px;font-weight:400;color:#9ca3af">(last 50)</span>
        </p>
        @if($logs->isEmpty())
            <p style="font-size:13px;color:#9ca3af;margin:0">No sync history yet. Run your first Pull or Push above.</p>
        @else
        <div style="overflow-x:auto">
            <table style="width:100%;border-collapse:collapse;font-size:12px;min-width:480px">
                <thead>
                    <tr style="border-bottom:2px solid #f3f4f6;font-size:10px;color:#9ca3af;text-transform:uppercase;text-align:left">
                        <th style="padding:5px 8px">When</th>
                        <th style="padding:5px 8px">Direction</th>
                        <th style="padding:5px 8px">Status</th>
                        <th style="padding:5px 8px">Records</th>
                        <th style="padding:5px 8px">Device</th>
                        <th style="padding:5px 8px">Breakdown</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($logs as $log)
                    <tr style="border-bottom:1px solid #f9fafb">
                        <td style="padding:7px 8px;color:#6b7280;white-space:nowrap">{{ $log->synced_at->diffForHumans() }}</td>
                        <td style="padding:7px 8px">
                            @if($log->direction === 'pull')
                            <span style="background:#dbeafe;color:#1d4ed8;border-radius:999px;padding:2px 8px;font-size:10px;font-weight:600">↓ Pull</span>
                            @else
                            <span style="background:#dcfce7;color:#166534;border-radius:999px;padding:2px 8px;font-size:10px;font-weight:600">↑ Push</span>
                            @endif
                        </td>
                        <td style="padding:7px 8px;font-size:11px;font-weight:600;color:{{ $log->status==='success'?'#16a34a':($log->status==='partial'?'#d97706':'#dc2626') }}">
                            {{ ucfirst($log->status) }}
                        </td>
                        <td style="padding:7px 8px;font-weight:700;color:#1f2937">{{ $log->records_sent + $log->records_received }}</td>
                        <td style="padding:7px 8px;color:#9ca3af">{{ optional($log->token)->device_name ?? '—' }}</td>
                        <td style="padding:7px 8px;color:#6b7280;font-size:11px">
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

@section('javascript')
<script>
const BUSINESS_ID = @json($businessId);
const APP_URL     = @json(config('app.url'));
const CSRF        = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

// ── Pull label map ───────────────────────────────────────────────────────────
const PULL_LABELS = {
    business:'Business settings', locations:'Locations', categories:'Categories',
    brands:'Brands', units:'Units', tax_rates:'Tax rates',
    contacts:'Contacts', products:'Products', stock:'Stock levels',
    transactions:'Transactions (last 30 days)'
};

// ── Load saved settings into form ────────────────────────────────────────────
let REMOTE_URL = '';  // set if this device has a remote configured
let HAS_TOKEN  = false;

fetch('/sync/settings', { headers:{ 'Accept':'application/json', 'X-CSRF-TOKEN':CSRF } })
    .then(r => r.json())
    .then(s => {
        if (s.remote_url) {
            document.getElementById('setting-remote-url').value = s.remote_url;
            document.getElementById('display-remote-url').textContent = s.remote_url;
            REMOTE_URL = s.remote_url;
        }
        if (s.sync_token) {
            document.getElementById('setting-sync-token').value = s.sync_token;
            HAS_TOKEN = true;
        }
        // If remote is configured, update Pull button label to show it pulls from live server
        if (REMOTE_URL && HAS_TOKEN) {
            const btn = document.getElementById('btn-pull');
            btn.title = 'Pull from ' + REMOTE_URL;
            document.querySelector('#btn-pull').closest('div').querySelector('p:last-of-type')
                && (document.querySelectorAll('.pull-source-label').forEach(el => el.textContent = 'Live Server → This device'));
        }
    }).catch(() => {});

// ── Connection badge ─────────────────────────────────────────────────────────
(function checkConnection() {
    fetch('/sync/status', { headers:{ 'Accept':'application/json' } })
        .then(r => r.json())
        .then(() => setBadge(true))
        .catch(() => setBadge(false));
})();

function setBadge(online) {
    const dot  = document.getElementById('badge-dot');
    const text = document.getElementById('badge-text');
    const wrap = document.getElementById('sync-connection-badge');
    if (online) {
        dot.style.background  = '#16a34a';
        text.textContent      = 'Online';
        wrap.style.background = '#dcfce7';
        wrap.style.color      = '#166534';
    } else {
        dot.style.background  = '#dc2626';
        text.textContent      = 'Offline / Server unreachable';
        wrap.style.background = '#fef2f2';
        wrap.style.color      = '#991b1b';
    }
}

// ── Button loading state ─────────────────────────────────────────────────────
function setBtnLoading(id, loading) {
    const btn = document.getElementById(id);
    if (!btn) return;
    btn.disabled      = loading;
    btn.style.opacity = loading ? '.55' : '1';
    btn.style.cursor  = loading ? 'wait' : 'pointer';
    if (loading) { btn.dataset.orig = btn.textContent; btn.textContent = '⟳ Working…'; }
    else if (btn.dataset.orig) btn.textContent = btn.dataset.orig;
}

// ── Progress helpers ─────────────────────────────────────────────────────────
function setProgress(pct, label) {
    document.getElementById('resp-progress-bar').style.width = pct + '%';
    document.getElementById('resp-progress-pct').textContent = pct + '%';
    if (label) document.getElementById('resp-progress-label').textContent = label;
}

// ── Show response panel ──────────────────────────────────────────────────────
function showPanel(title, state) {
    // state = 'loading' | 'error' | data object
    const panel = document.getElementById('response-panel');
    panel.style.display = 'block';
    document.getElementById('resp-title').textContent = title;
    document.getElementById('resp-error-box').style.display  = 'none';
    document.getElementById('resp-total-row').style.display  = 'none';
    document.getElementById('resp-time').textContent = '';

    if (state === 'loading') {
        document.getElementById('resp-progress').style.display = 'block';
        setProgress(10, 'Connecting…');
        // Skeleton rows
        document.getElementById('resp-tbody').innerHTML = Object.keys(PULL_LABELS).map(k => `
            <tr style="border-bottom:1px solid #f9fafb">
                <td style="padding:7px 8px;color:#6b7280">${PULL_LABELS[k]}</td>
                <td style="padding:7px 8px;text-align:right"><span style="display:inline-block;width:28px;height:12px;background:#f3f4f6;border-radius:3px;animation:pulse 1.4s infinite"></span></td>
                <td style="padding:7px 8px"><span style="color:#9ca3af;font-size:11px">waiting…</span></td>
            </tr>`).join('');
        return;
    }

    document.getElementById('resp-progress').style.display = 'none';

    if (state === 'error' || (state && state.error)) {
        const msg = typeof state === 'string' ? state : state.error;
        document.getElementById('resp-error-box').style.display  = 'block';
        document.getElementById('resp-error-text').textContent   = msg;
        document.getElementById('resp-tbody').innerHTML = '<tr><td colspan="3" style="padding:10px;color:#9ca3af;text-align:center">—</td></tr>';
        return;
    }

    // Pull/Push data object
    const { summary, labels, errors, pulled_at, pushed_at } = state;
    const ts = pulled_at || pushed_at;
    if (ts) document.getElementById('resp-time').textContent = 'at ' + new Date(ts).toLocaleTimeString();

    const allLabels = labels || PULL_LABELS;
    let total = 0;
    document.getElementById('resp-tbody').innerHTML = Object.keys(allLabels).map(k => {
        const count    = (summary && summary[k]) ?? 0;
        total += count;
        const hasErr   = errors && errors[k];
        const statusHtml = hasErr
            ? `<span style="color:#dc2626;font-size:11px" title="${errors[k]}">✗ Error</span>`
            : count > 0
                ? `<span style="color:#16a34a;font-size:11px;font-weight:600">✓ Synced</span>`
                : `<span style="color:#9ca3af;font-size:11px">No changes</span>`;
        return `<tr style="border-bottom:1px solid #f9fafb">
            <td style="padding:7px 8px;color:#374151">${allLabels[k] || k}</td>
            <td style="padding:7px 8px;text-align:right;font-weight:700;color:${count>0?'#2563eb':'#d1d5db'}">${count > 0 ? count.toLocaleString() : '—'}</td>
            <td style="padding:7px 8px">${statusHtml}</td>
        </tr>`;
    }).join('');

    document.getElementById('resp-total-count').textContent = total.toLocaleString();
    document.getElementById('resp-total-row').style.display = 'flex';

    if (errors && Object.keys(errors).length > 0) {
        document.getElementById('resp-error-box').style.display = 'block';
        document.getElementById('resp-error-text').textContent  =
            Object.entries(errors).map(([k,v]) => k + ': ' + v).join('\n');
    }
}

// ── Pull ─────────────────────────────────────────────────────────────────────
async function doPull() {
    setBtnLoading('btn-pull', true);
    showPanel('Pull Results', 'loading');

    // If remote settings are saved, use pull-remote (server→server fetch from live)
    // Otherwise pull from this server directly (live server dashboard use)
    const pullEndpoint = (REMOTE_URL && HAS_TOKEN) ? '/sync/pull-remote' : '/sync/pull';

    try {
        setProgress(25, REMOTE_URL ? ('Connecting to ' + REMOTE_URL + '…') : 'Sending request…');
        const res = await fetch(pullEndpoint, {
            method : 'POST',
            headers: { 'Content-Type':'application/json', 'Accept':'application/json', 'X-CSRF-TOKEN':CSRF },
            body   : JSON.stringify({ business_id: BUSINESS_ID }),
        });

        setProgress(75, 'Processing…');
        const text = await res.text();
        let data;
        try { data = JSON.parse(text); }
        catch(e) { throw new Error('Server returned non-JSON (HTTP ' + res.status + '):\n' + text.slice(0, 400)); }

        if (!res.ok || data.error) {
            showPanel('✗ Pull Failed', data.error ? data : { error: 'HTTP ' + res.status + ': ' + text.slice(0,300) });
            return;
        }

        setProgress(100, 'Done!');
        showPanel('✓ Pull Complete', data);
        const total = data.summary ? Object.values(data.summary).reduce((a,b)=>a+b,0) : 0;
        document.getElementById('sync-last-pull').textContent = 'Just now (' + total.toLocaleString() + ' records)';
        if (typeof toastr !== 'undefined') toastr.success('Pull complete — ' + total.toLocaleString() + ' records');
    } catch(e) {
        showPanel('✗ Pull Failed', { error: e.message });
        if (typeof toastr !== 'undefined') toastr.error('Pull failed');
    } finally {
        setBtnLoading('btn-pull', false);
    }
}

// ── Push ─────────────────────────────────────────────────────────────────────
async function doPush() {
    setBtnLoading('btn-push', true);
    showPanel('Push Results', 'loading');

    const pushEndpoint = (REMOTE_URL && HAS_TOKEN) ? '/sync/push-remote' : '/sync/push';

    try {
        setProgress(30, REMOTE_URL ? ('Sending to ' + REMOTE_URL + '…') : 'Uploading…');
        const res  = await fetch(pushEndpoint, {
            method : 'POST',
            headers: { 'Content-Type':'application/json', 'Accept':'application/json', 'X-CSRF-TOKEN':CSRF },
            body   : JSON.stringify({ business_id: BUSINESS_ID }),
        });

        setProgress(80, 'Processing…');
        const text = await res.text();
        let data;
        try { data = JSON.parse(text); }
        catch(e) { throw new Error('Server returned non-JSON (HTTP ' + res.status + '):\n' + text.slice(0,400)); }

        if (!res.ok || data.error) {
            showPanel('✗ Push Failed', data.error ? data : { error: 'HTTP ' + res.status });
            return;
        }

        setProgress(100, 'Done!');
        // Normalise push response into the same shape showPanel expects
        if (data.sent && !data.summary) {
            data.summary = data.sent;
            data.labels  = { contacts: 'Contacts', transactions: 'Transactions',
                             stock_adjustments: 'Stock adjustments' };
            data.pushed_at = data.pushed_at || new Date().toISOString();
        }
        if (data.message && !data.summary) {
            data.summary = {}; data.labels = {}; data.pushed_at = data.pushed_at || new Date().toISOString();
        }
        showPanel('✓ Push Complete', data);
        const count = data.summary ? Object.values(data.summary).reduce((a,b)=>a+b,0) : 0;
        const msg   = data.message || ('Push complete — ' + count + ' records uploaded');
        document.getElementById('sync-last-push').textContent = 'Just now (' + count + ' records)';
        if (typeof toastr !== 'undefined') toastr.success(msg);
    } catch(e) {
        showPanel('✗ Push Failed', { error: e.message });
        if (typeof toastr !== 'undefined') toastr.error('Push failed');
    } finally {
        setBtnLoading('btn-push', false);
    }
}

async function doSync() {
    setBtnLoading('btn-sync', true);
    await doPush();
    await doPull();
    setBtnLoading('btn-sync', false);
}

// ── Register device ──────────────────────────────────────────────────────────
function registerDevice() {
    const name  = document.getElementById('device-name-input').value.trim() || 'Unnamed Device';
    const type  = document.getElementById('device-type-input').value;
    const errEl = document.getElementById('register-error');
    errEl.style.display = 'none';

    fetch('/sync/register', {
        method : 'POST',
        headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN':CSRF, 'Accept':'application/json' },
        body   : JSON.stringify({ business_id: BUSINESS_ID, device_name: name, device_type: type }),
    })
    .then(async r => {
        const text = await r.text();
        let data;
        try { data = JSON.parse(text); } catch(e) { throw new Error('Server error: ' + text.slice(0,300)); }
        if (!r.ok || data.error) throw new Error(data.error || data.message || ('HTTP ' + r.status + ': ' + text.slice(0,200)));
        return data;
    })
    .then(data => {
        const token = data.sync_token;
        document.getElementById('new-token-value').textContent = token;
        document.getElementById('new-token-box').style.display = 'block';

        if (type === 'desktop') {
            const snippet = 'SYNC_REMOTE_URL=' + APP_URL + '\nSYNC_TOKEN=' + token + '\nSYNC_BUSINESS_ID=' + BUSINESS_ID;
            document.getElementById('laragon-env-snippet').textContent = snippet;
            document.getElementById('laragon-instructions').style.display = 'block';
            // Also auto-fill the settings panel
            document.getElementById('setting-sync-token').value = token;
        }

        // Show a manual reload button — don't auto-reload so user can copy the token
        const reloadBtn = document.createElement('button');
        reloadBtn.textContent = '↺ Done — reload page';
        reloadBtn.style = 'margin-top:10px;background:#1e40af;color:#fff;border:none;border-radius:6px;padding:6px 14px;font-size:12px;font-weight:600;cursor:pointer;display:block';
        reloadBtn.onclick = () => location.reload();
        document.getElementById('new-token-box').appendChild(reloadBtn);
    })
    .catch(err => {
        errEl.style.display = 'block';
        errEl.textContent   = '✗ ' + err.message;
    });
}

function copyToken() {
    const txt = document.getElementById('new-token-value').textContent;
    navigator.clipboard.writeText(txt)
        .then(() => { document.querySelector('[onclick="copyToken()"]').textContent = '✓ Copied!'; })
        .catch(() => alert('Copy manually:\n' + txt));
}

// ── Revoke device ────────────────────────────────────────────────────────────
function revokeDevice(id) {
    if (!confirm('Revoke this device? It will no longer be able to sync.')) return;
    fetch('/sync/token/' + id, {
        method : 'DELETE',
        headers: { 'X-CSRF-TOKEN':CSRF, 'Accept':'application/json' },
    })
    .then(r => r.json())
    .then(() => location.reload())
    .catch(err => alert('Revoke failed: ' + err.message));
}

// ── Save settings ────────────────────────────────────────────────────────────
function saveSettings() {
    const remoteUrl = document.getElementById('setting-remote-url').value.trim();
    const syncToken = document.getElementById('setting-sync-token').value.trim();
    const savedMsg  = document.getElementById('settings-saved-msg');
    const errMsg    = document.getElementById('settings-error-msg');
    savedMsg.style.display = 'none';
    errMsg.style.display   = 'none';

    fetch('/sync/settings', {
        method : 'POST',
        headers: { 'Content-Type':'application/json', 'Accept':'application/json', 'X-CSRF-TOKEN':CSRF },
        body   : JSON.stringify({ remote_url: remoteUrl, sync_token: syncToken, business_id: BUSINESS_ID }),
    })
    .then(async r => {
        const text = await r.text();
        let data;
        try { data = JSON.parse(text); } catch(e) { throw new Error(text.slice(0,200)); }
        if (!r.ok || !data.ok) throw new Error(data.error || ('HTTP ' + r.status));
        return data;
    })
    .then(() => {
        savedMsg.style.display = 'inline';
        document.getElementById('display-remote-url').textContent = remoteUrl || APP_URL;
        setTimeout(() => { savedMsg.style.display = 'none'; }, 3000);
    })
    .catch(err => {
        errMsg.style.display  = 'inline';
        errMsg.textContent    = '✗ ' + err.message;
    });
}

// Pulse animation for skeleton loading
const _s = document.createElement('style');
_s.textContent = '@keyframes pulse{0%,100%{opacity:1}50%{opacity:.35}}';
document.head.appendChild(_s);
</script>
@endsection
