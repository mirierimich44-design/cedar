@extends('layouts.app')
@section('title', 'IP Access Control')

@section('content')
<div class="content-wrapper">

    <section class="content-header">
        <h1><i class="fa fa-shield"></i> IP Access Control
            <small>Manage allowed networks &amp; login schedules</small>
        </h1>
    </section>

    <section class="content">

        {{-- Flash status --}}
        @if(session('status'))
            @php $st = session('status'); @endphp
            <div class="alert alert-{{ $st['success'] ? 'success' : 'danger' }} alert-dismissible">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                {{ $st['msg'] }}
            </div>
        @endif

        {{-- ══════════════════════════════════════════════════════════
             MODULE TOGGLE — enable/disable the sidebar item
             ══════════════════════════════════════════════════════════ --}}
        <div class="box box-default" style="margin-bottom:16px;border-left:4px solid {{ $moduleEnabled ? '#16a34a' : '#94a3b8' }};">
            <div class="box-body" style="padding:14px 20px;">
                <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
                    <div>
                        <strong style="font-size:14px;">
                            <i class="fa fa-toggle-{{ $moduleEnabled ? 'on' : 'off' }}" style="color:{{ $moduleEnabled ? '#16a34a' : '#94a3b8' }};margin-right:6px;"></i>
                            IP Access Control Module
                        </strong>
                        <div style="font-size:12px;color:#6b7280;margin-top:3px;">
                            @if($moduleEnabled)
                                Module is <strong style="color:#16a34a;">enabled</strong> — the IP Access Control item is visible in the sidebar.
                            @else
                                Module is <strong style="color:#94a3b8;">disabled</strong> — the sidebar item is hidden. You can still access this page directly.
                            @endif
                        </div>
                    </div>
                    <form method="POST" action="{{ route('ip-access.toggle-module') }}">
                        @csrf
                        <input type="hidden" name="enable" value="{{ $moduleEnabled ? '0' : '1' }}">
                        <button type="submit" class="btn btn-sm {{ $moduleEnabled ? 'btn-default' : 'btn-success' }}">
                            <i class="fa fa-{{ $moduleEnabled ? 'toggle-off' : 'toggle-on' }}"></i>
                            {{ $moduleEnabled ? 'Disable Module' : 'Enable Module' }}
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════════
             MASTER TOGGLE — full-width banner at the top
             ══════════════════════════════════════════════════════════ --}}
        <div class="box {{ $restrictionEnabled ? 'box-danger' : 'box-default' }}" style="margin-bottom:20px;">
            <div class="box-body" style="padding:18px 22px;">
                <div style="display:flex;align-items:center;gap:16px;flex-wrap:wrap;">

                    {{-- Status indicator --}}
                    <div style="flex:1;min-width:220px;">
                        <h4 style="margin:0 0 4px;">
                            @if($restrictionEnabled)
                                <span class="label label-danger" style="font-size:13px;padding:5px 10px;">
                                    <i class="fa fa-lock"></i> Restriction ON
                                </span>
                            @else
                                <span class="label label-default" style="font-size:13px;padding:5px 10px;">
                                    <i class="fa fa-unlock"></i> Restriction OFF
                                </span>
                            @endif
                        </h4>
                        <p style="margin:6px 0 0;font-size:12px;color:#6b7280;">
                            @if($restrictionEnabled)
                                Only whitelisted IPs can log in. Business owners &amp; superadmin are always exempt.
                            @else
                                Anyone with valid credentials can log in from any location.
                            @endif
                        </p>
                    </div>

                    {{-- Safety check for the current admin --}}
                    <div style="flex:1;min-width:200px;background:{{ $currentIpWhitelisted ? '#f0fdf4' : '#fef9c3' }};border:1px solid {{ $currentIpWhitelisted ? '#86efac' : '#fde047' }};border-radius:8px;padding:10px 14px;">
                        <div style="font-size:12px;font-weight:600;color:{{ $currentIpWhitelisted ? '#166534' : '#92400e' }};margin-bottom:3px;">
                            <i class="fa fa-{{ $currentIpWhitelisted ? 'check-circle' : 'warning' }}"></i>
                            Your current IP: <code>{{ $currentIp }}</code>
                        </div>
                        @if($currentIpWhitelisted)
                            <div style="font-size:11px;color:#166534;">✓ Is in the whitelist — safe to enable restriction</div>
                        @else
                            <div style="font-size:11px;color:#92400e;">⚠ Not in the whitelist — add it below before enabling, or you may be blocked on next login</div>
                            <form method="POST" action="{{ route('ip-access.add-ip') }}" style="margin-top:6px;">
                                @csrf
                                <input type="hidden" name="ip_address" value="{{ $currentIp }}">
                                <input type="hidden" name="label" value="My IP (auto-added)">
                                <button class="btn btn-xs btn-warning"><i class="fa fa-plus"></i> Add my IP now</button>
                            </form>
                        @endif
                    </div>

                    {{-- Toggle buttons --}}
                    <div style="display:flex;gap:8px;flex-shrink:0;">
                        @if($restrictionEnabled)
                            <form method="POST" action="{{ route('ip-access.toggle-restriction') }}">
                                @csrf
                                <input type="hidden" name="enable" value="0">
                                <button type="submit" class="btn btn-default"
                                    onclick="return confirm('Disable IP restriction? All IPs will be able to log in.')">
                                    <i class="fa fa-unlock"></i> Disable Restriction
                                </button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('ip-access.toggle-restriction') }}">
                                @csrf
                                <input type="hidden" name="enable" value="1">
                                <button type="submit" class="btn btn-danger"
                                    {{ ! $currentIpWhitelisted ? 'disabled title="Add your IP to the whitelist first"' : '' }}
                                    onclick="return confirm('Enable IP restriction?\n\nOnly whitelisted IPs will be able to log in.\n\nBusiness owners (Admin role) are ALWAYS exempt.\n\nContinue?')">
                                    <i class="fa fa-lock"></i> Enable Restriction
                                </button>
                            </form>
                        @endif
                    </div>

                </div>
            </div>
        </div>

        <div class="row">

            {{-- ── Allowed IPs ───────────────────────────────────────────── --}}
            <div class="col-md-5">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-wifi"></i> Whitelisted Networks</h3>
                    </div>
                    <div class="box-body">

                        {{-- Add IP form --}}
                        <form method="POST" action="{{ route('ip-access.add-ip') }}" class="form-inline" style="margin-bottom:16px;gap:6px;display:flex;flex-wrap:wrap;">
                            @csrf
                            <input type="text" name="ip_address" class="form-control input-sm" placeholder="IP address (e.g. 102.0.5.12)" style="flex:1;min-width:140px;" required>
                            <input type="text" name="label" class="form-control input-sm" placeholder="Label e.g. Office WiFi" style="flex:1;min-width:120px;">
                            <button class="btn btn-sm btn-primary"><i class="fa fa-plus"></i> Add IP</button>
                        </form>

                        {{-- IP Table --}}
                        <table class="table table-condensed">
                            <thead><tr>
                                <th>IP Address</th><th>Label</th><th>Status</th><th></th>
                            </tr></thead>
                            <tbody>
                            @forelse($allowed as $ip)
                                <tr style="{{ $ip->ip_address === $currentIp ? 'background:#f0fdf4;' : '' }}">
                                    <td>
                                        <code>{{ $ip->ip_address }}</code>
                                        @if($ip->ip_address === $currentIp)
                                            <span class="label label-success" style="font-size:10px;margin-left:4px;">You</span>
                                        @endif
                                    </td>
                                    <td style="font-size:12px;color:#6b7280;">{{ $ip->label ?? '—' }}</td>
                                    <td>
                                        <form method="POST" action="{{ route('ip-access.toggle-ip', $ip->id) }}">
                                            @csrf @method('PATCH')
                                            <button class="btn btn-xs {{ $ip->is_active ? 'btn-success' : 'btn-default' }}">
                                                {{ $ip->is_active ? 'Active' : 'Inactive' }}
                                            </button>
                                        </form>
                                    </td>
                                    <td>
                                        <form method="POST" action="{{ route('ip-access.delete-ip', $ip->id) }}"
                                              onsubmit="return confirm('Remove IP {{ $ip->ip_address }}?')">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-xs btn-danger"><i class="fa fa-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center text-muted" style="padding:20px;">
                                    No IPs added yet. Add your office WiFi IP above.
                                </td></tr>
                            @endforelse
                            </tbody>
                        </table>

                        <div class="alert alert-info" style="font-size:12px;margin-top:10px;margin-bottom:0;">
                            <strong><i class="fa fa-info-circle"></i> How it works:</strong>
                            Business owners (Admin role) can <strong>always</strong> log in regardless of this list.
                            Only non-owner staff are checked against it when restriction is ON.
                        </div>
                    </div>
                </div>

                <a href="{{ route('ip-access.logs') }}" class="btn btn-default btn-block" style="margin-bottom:20px;">
                    <i class="fa fa-list"></i> View Access Logs
                </a>
            </div>

            {{-- ── Access Schedules ──────────────────────────────────────── --}}
            <div class="col-md-7">
                <div class="box box-warning">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-clock-o"></i> Login Schedules (by Role)</h3>
                    </div>
                    <div class="box-body">
                        <form method="POST" action="{{ route('ip-access.save-schedules') }}">
                            @csrf

                            @foreach($roles as $role)
                            <div style="margin-bottom:20px;">
                                <h4 style="margin-bottom:8px;">{{ $role->name }}</h4>
                                <div style="overflow-x:auto;">
                                <table class="table table-condensed table-bordered" style="font-size:12px;min-width:600px;">
                                    <thead><tr>
                                        <th>Day</th><th>Restrict?</th><th>Allow From</th><th>Allow Until</th>
                                    </tr></thead>
                                    <tbody>
                                    @foreach($days as $dayNum => $dayName)
                                        @php $key = $role->id . '_' . $dayNum; $sch = $schedules[$key] ?? null; @endphp
                                        <tr>
                                            <td><strong>{{ $dayName }}</strong></td>
                                            <td>
                                                <input type="checkbox"
                                                    name="schedules[{{ $role->id }}][{{ $dayNum }}][active]"
                                                    value="1"
                                                    {{ $sch && $sch->is_active ? 'checked' : '' }}>
                                            </td>
                                            <td>
                                                <input type="time"
                                                    name="schedules[{{ $role->id }}][{{ $dayNum }}][start]"
                                                    value="{{ $sch ? $sch->start_time : '08:00' }}"
                                                    class="form-control input-sm">
                                            </td>
                                            <td>
                                                <input type="time"
                                                    name="schedules[{{ $role->id }}][{{ $dayNum }}][end]"
                                                    value="{{ $sch ? $sch->end_time : '18:00' }}"
                                                    class="form-control input-sm">
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                                </div>
                            </div>
                            @endforeach

                            <button type="submit" class="btn btn-warning btn-block">
                                <i class="fa fa-save"></i> Save Schedules
                            </button>
                        </form>

                        <p class="text-muted" style="font-size:12px;margin-top:10px;">
                            <i class="fa fa-info-circle"></i>
                            Only ticked days are time-restricted. Unticked days allow login at any time.
                            Superadmin is always exempt.
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </section>
</div>
@endsection
