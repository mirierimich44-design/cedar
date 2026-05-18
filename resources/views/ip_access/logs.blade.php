@extends('layouts.app')
@section('title', 'IP Access Logs')

@section('content')
<div class="content-wrapper">

    <section class="content-header">
        <div class="tw-flex tw-justify-between tw-items-center">
            <h1><i class="fa fa-shield"></i> IP Access Control — Logs</h1>
            <a href="{{ route('ip-access.settings') }}" class="btn btn-default btn-sm">
                <i class="fa fa-cog"></i> Settings
            </a>
        </div>
    </section>

    <section class="content">

        {{-- Flash --}}
        @if(session('status'))
            @php $st = session('status'); @endphp
            <div class="alert alert-{{ $st['success'] ? 'success' : 'danger' }} alert-dismissible">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                {{ $st['msg'] }}
            </div>
        @endif

        {{-- Filters --}}
        <div class="box box-default" style="margin-bottom:16px;">
            <div class="box-body" style="padding:12px 16px;">
                <form method="GET" action="{{ route('ip-access.logs') }}"
                      style="display:flex;flex-wrap:wrap;gap:8px;align-items:flex-end;">
                    <div class="form-group" style="margin:0;">
                        <label style="font-size:11px;font-weight:600;color:#6b7280;display:block;margin-bottom:3px;">IP Search</label>
                        <input type="text" name="ip_search" class="form-control input-sm"
                               placeholder="e.g. 102.0" value="{{ request('ip_search') }}" style="width:130px;">
                    </div>
                    <div class="form-group" style="margin:0;">
                        <label style="font-size:11px;font-weight:600;color:#6b7280;display:block;margin-bottom:3px;">Location</label>
                        <select name="location_id" class="form-control input-sm" style="width:160px;">
                            <option value="">All Locations</option>
                            @foreach($locations as $loc)
                                <option value="{{ $loc->id }}" {{ request('location_id') == $loc->id ? 'selected' : '' }}>
                                    {{ $loc->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group" style="margin:0;">
                        <label style="font-size:11px;font-weight:600;color:#6b7280;display:block;margin-bottom:3px;">Outcome</label>
                        <select name="outcome" class="form-control input-sm" style="width:140px;">
                            <option value="">All Outcomes</option>
                            <option value="success"          {{ request('outcome') == 'success'          ? 'selected' : '' }}>Success</option>
                            <option value="blocked_ip"       {{ request('outcome') == 'blocked_ip'       ? 'selected' : '' }}>Blocked IP</option>
                            <option value="wrong_password"   {{ request('outcome') == 'wrong_password'   ? 'selected' : '' }}>Wrong Password</option>
                            <option value="account_disabled" {{ request('outcome') == 'account_disabled' ? 'selected' : '' }}>Disabled</option>
                        </select>
                    </div>
                    <div class="form-group" style="margin:0;">
                        <label style="font-size:11px;font-weight:600;color:#6b7280;display:block;margin-bottom:3px;">Date</label>
                        <input type="date" name="date" class="form-control input-sm" value="{{ request('date') }}">
                    </div>
                    <button class="btn btn-sm btn-primary"><i class="fa fa-search"></i> Filter</button>
                    <a href="{{ route('ip-access.logs') }}" class="btn btn-sm btn-default">Reset</a>
                    <a href="{{ route('ip-access.logs', array_merge(request()->all(), ['export' => 1])) }}"
                       class="btn btn-sm btn-default"><i class="fa fa-download"></i> Export CSV</a>
                </form>
            </div>
        </div>

        {{-- Tabs --}}
        <ul class="nav nav-tabs" style="margin-bottom:0;border-bottom:2px solid #e2e8f0;">
            <li class="active"><a href="#tab-summary" data-toggle="tab" style="font-weight:600;">
                <i class="fa fa-globe"></i> IP Summary
                <span class="badge" style="background:var(--theme-main);color:#fff;margin-left:4px;">{{ $ipSummary->count() }}</span>
            </a></li>
            <li><a href="#tab-activity" data-toggle="tab" style="font-weight:600;">
                <i class="fa fa-list"></i> Activity Log
            </a></li>
        </ul>

        <div class="tab-content" style="background:#fff;border:1px solid #e2e8f0;border-top:none;border-radius:0 0 8px 8px;">

            {{-- ══════════════════════════════════════════════════
                 TAB 1 — IP SUMMARY (unique IPs with actions)
                 ══════════════════════════════════════════════════ --}}
            <div class="tab-pane active" id="tab-summary" style="padding:0;">
                @if($ipSummary->isEmpty())
                    <div style="text-align:center;padding:48px;color:#94a3b8;">
                        <i class="fa fa-inbox" style="font-size:40px;display:block;margin-bottom:12px;"></i>
                        No login attempts recorded yet.
                    </div>
                @else
                <div style="overflow-x:auto;">
                <table class="table table-condensed" style="margin:0;">
                    <thead>
                        <tr>
                            <th>IP Address</th>
                            <th>Geo Location</th>
                            <th>ISP</th>
                            <th>Users</th>
                            <th>Locations</th>
                            <th style="text-align:center;">Logins</th>
                            <th style="text-align:center;">Blocked</th>
                            <th>Last Seen</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($ipSummary as $row)
                        <tr style="{{ $row->is_banned ? 'background:#fef2f2;' : ($row->is_whitelisted ? 'background:#f0fdf4;' : '') }}">
                            <td>
                                <code style="font-size:12px;">{{ $row->ip_address }}</code>
                            </td>
                            <td style="font-size:12px;">
                                @if($row->city || $row->country)
                                    <i class="fa fa-map-marker" style="color:#94a3b8;margin-right:3px;"></i>
                                    {{ $row->city ? $row->city . ', ' . $row->country : $row->country }}
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td style="font-size:12px;color:#6b7280;">{{ $row->isp ?? '—' }}</td>
                            <td style="font-size:12px;">
                                @forelse($row->users as $uname)
                                    <span style="background:#e0f2fe;color:#0369a1;padding:1px 6px;border-radius:10px;font-size:11px;margin-right:2px;">{{ $uname }}</span>
                                @empty
                                    <span class="text-muted">—</span>
                                @endforelse
                            </td>
                            <td style="font-size:12px;">
                                @forelse($row->locations as $locName)
                                    <span style="background:#f3e8ff;color:#7c3aed;padding:1px 6px;border-radius:10px;font-size:11px;margin-right:2px;">{{ $locName }}</span>
                                @empty
                                    <span class="text-muted">—</span>
                                @endforelse
                            </td>
                            <td style="text-align:center;">
                                <strong style="color:#16a34a;">{{ $row->successful_logins }}</strong>
                            </td>
                            <td style="text-align:center;">
                                @if($row->blocked_attempts > 0)
                                    <strong style="color:#dc2626;">{{ $row->blocked_attempts }}</strong>
                                @else
                                    <span class="text-muted">0</span>
                                @endif
                            </td>
                            <td style="font-size:11px;color:#6b7280;white-space:nowrap;">
                                {{ \Carbon\Carbon::parse($row->last_seen)->diffForHumans() }}
                            </td>
                            <td>
                                @if($row->is_banned)
                                    <span style="background:#fee2e2;color:#dc2626;padding:2px 8px;border-radius:10px;font-size:11px;font-weight:700;">
                                        <i class="fa fa-ban"></i> Banned
                                    </span>
                                    @if($row->ban_reason)
                                        <div style="font-size:10px;color:#94a3b8;margin-top:2px;">{{ $row->ban_reason }}</div>
                                    @endif
                                @elseif($row->is_whitelisted)
                                    <span style="background:#dcfce7;color:#16a34a;padding:2px 8px;border-radius:10px;font-size:11px;font-weight:700;">
                                        <i class="fa fa-check"></i> Whitelisted
                                    </span>
                                @else
                                    <span style="background:#f1f5f9;color:#64748b;padding:2px 8px;border-radius:10px;font-size:11px;">
                                        Unknown
                                    </span>
                                @endif
                            </td>
                            <td style="white-space:nowrap;">
                                @if($row->is_banned)
                                    {{-- Unban --}}
                                    <form method="POST" action="{{ route('ip-access.unban-ip') }}" style="display:inline;">
                                        @csrf
                                        <input type="hidden" name="ip_address" value="{{ $row->ip_address }}">
                                        <button class="btn btn-xs btn-success" onclick="return confirm('Unban {{ $row->ip_address }}?')">
                                            <i class="fa fa-unlock"></i> Unban
                                        </button>
                                    </form>
                                @else
                                    {{-- Ban --}}
                                    <button class="btn btn-xs btn-danger" onclick="showBanModal('{{ $row->ip_address }}')">
                                        <i class="fa fa-ban"></i> Ban
                                    </button>
                                    {{-- Whitelist (if not already) --}}
                                    @if(!$row->is_whitelisted)
                                    <form method="POST" action="{{ route('ip-access.whitelist-from-log') }}" style="display:inline;">
                                        @csrf
                                        <input type="hidden" name="ip_address" value="{{ $row->ip_address }}">
                                        <button class="btn btn-xs btn-primary" onclick="return confirm('Add {{ $row->ip_address }} to whitelist?')">
                                            <i class="fa fa-plus"></i> Whitelist
                                        </button>
                                    </form>
                                    @else
                                    <form method="POST" action="{{ route('ip-access.delete-ip', $allowedMap->get($row->ip_address)?->id ?? 0) }}"
                                          style="display:inline;" onsubmit="return confirm('Remove from whitelist?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-xs btn-default">
                                            <i class="fa fa-times"></i> Remove
                                        </button>
                                    </form>
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                </div>
                @endif
            </div>

            {{-- ══════════════════════════════════════════════════
                 TAB 2 — ACTIVITY LOG (individual entries)
                 ══════════════════════════════════════════════════ --}}
            <div class="tab-pane" id="tab-activity" style="padding:0;">
                <div style="overflow-x:auto;">
                <table class="table table-condensed" style="margin:0;">
                    <thead>
                        <tr>
                            <th>Date / Time</th>
                            <th>User</th>
                            <th>IP</th>
                            <th>Location</th>
                            <th>Geo</th>
                            <th>Browser / OS</th>
                            <th>Outcome</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($logs as $log)
                        @php
                            $oc = [
                                'success'          => ['#dcfce7','#16a34a','Success'],
                                'blocked_ip'       => ['#fee2e2','#dc2626','Blocked IP'],
                                'blocked_schedule' => ['#fef3c7','#d97706','Blocked Hours'],
                                'wrong_password'   => ['#ffedd5','#ea580c','Wrong Password'],
                                'account_disabled' => ['#f3e8ff','#9333ea','Disabled'],
                            ][$log->outcome] ?? ['#f1f5f9','#475569',$log->outcome];
                        @endphp
                        <tr>
                            <td style="white-space:nowrap;font-size:12px;">{{ $log->created_at->format('d M Y H:i') }}</td>
                            <td style="font-size:12px;">{{ $log->user?->username ?? $log->username_attempted ?? '—' }}</td>
                            <td><code style="font-size:11px;">{{ $log->ip_address }}</code></td>
                            <td style="font-size:12px;">{{ $log->location?->name ?? '—' }}</td>
                            <td style="font-size:12px;">
                                {{ $log->city ? $log->city . ', ' . $log->country : ($log->country ?? '—') }}
                            </td>
                            <td style="font-size:11px;color:#6b7280;">
                                {{ $log->browser ?? '' }}{{ $log->os ? ' / ' . $log->os : '' }}
                                @if($log->device_type && $log->device_type !== 'desktop')
                                    <span style="background:#e0f2fe;color:#0369a1;padding:1px 5px;border-radius:8px;font-size:10px;">{{ $log->device_type }}</span>
                                @endif
                            </td>
                            <td>
                                <span style="background:{{ $oc[0] }};color:{{ $oc[1] }};padding:2px 8px;border-radius:10px;font-size:11px;font-weight:700;">
                                    {{ $oc[2] }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted" style="padding:30px;">No activity found.</td></tr>
                    @endforelse
                    </tbody>
                </table>
                </div>
                <div style="padding:12px 16px;">{{ $logs->links() }}</div>
            </div>

        </div>{{-- end tab-content --}}

    </section>
</div>

{{-- Ban Modal --}}
<div class="modal fade" id="ban-modal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"><i class="fa fa-ban"></i> Ban IP</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form method="POST" action="{{ route('ip-access.ban-ip') }}">
                @csrf
                <div class="modal-body">
                    <p style="margin-bottom:12px;">
                        Banning <code id="ban-ip-display"></code> will block it immediately,
                        even if IP restriction is off.
                    </p>
                    <input type="hidden" name="ip_address" id="ban-ip-input">
                    <div class="form-group">
                        <label style="font-size:12px;font-weight:600;">Reason (optional)</label>
                        <input type="text" name="reason" class="form-control input-sm" placeholder="e.g. Suspicious activity">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger btn-sm"><i class="fa fa-ban"></i> Confirm Ban</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('javascript')
<script>
function showBanModal(ip) {
    document.getElementById('ban-ip-display').textContent = ip;
    document.getElementById('ban-ip-input').value = ip;
    $('#ban-modal').modal('show');
}

// Restore active tab from URL hash
$(function() {
    var hash = window.location.hash;
    if (hash) {
        $('a[href="' + hash + '"]').tab('show');
    }
    $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
        history.replaceState(null, null, e.target.getAttribute('href'));
    });
});
</script>
@endsection
