@php
    $all_notifications = auth()->user()->notifications;
    $unread_notifications = $all_notifications->where('read_at', null);
    $total_unread = count($unread_notifications);
@endphp

<div class="hdr-bell-wrap">
    <button type="button" id="smart-notif-btn"
        class="hdr-btn hdr-btn-icon"
        onclick="toggleSmartNotif()" aria-label="Notifications" style="position:relative;">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="width:17px;height:17px;">
            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
            <path d="M10 5a2 2 0 1 1 4 0a7 7 0 0 1 4 6v3a4 4 0 0 0 2 3h-16a4 4 0 0 0 2 -3v-3a7 7 0 0 1 4 -6"/>
            <path d="M9 17v1a3 3 0 0 0 6 0v-1"/>
        </svg>
    </button>
    {{-- Pulsing dot --}}
    @if(!empty($total_unread))
        <span id="notif-badge" class="hdr-bell-dot pulse"></span>
    @else
        <span id="notif-badge" class="hdr-bell-dot" style="display:none;"></span>
    @endif

    <div id="smart-notif-panel" style="display:none;position:absolute;right:0;top:calc(100% + 8px);width:380px;background:#fff;border-radius:12px;box-shadow:0 20px 40px rgba(0,0,0,0.15);border:1px solid #e2e8f0;z-index:9998;overflow:hidden;">
        {{-- Header --}}
        <div style="padding:16px 16px 12px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between;">
            <span style="font-size:15px;font-weight:700;color:#1e293b;">Notifications</span>
            <a href="#" onclick="markAllRead()" style="font-size:12px;color:#3b82f6;text-decoration:none;">Mark all read</a>
        </div>

        {{-- Smart Alerts --}}
        <div id="smart-alerts-section">
            <div style="padding:8px 16px 4px;font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:0.05em;">System Alerts</div>
            <div id="smart-alerts-list">
                <div style="padding:12px 16px;text-align:center;color:#94a3b8;font-size:13px;">Loading alerts...</div>
            </div>
        </div>

        {{-- User Notifications --}}
        <div>
            <div style="padding:8px 16px 4px;font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:0.05em;">Recent</div>
            <ul style="margin:0;padding:0;list-style:none;max-height:240px;overflow-y:auto;" id="notifications_list"></ul>
            @if (count($all_notifications) > 10)
                <div style="padding:8px 16px;border-top:1px solid #f1f5f9;text-align:center;">
                    <a href="#" class="load_more_notifications" style="font-size:13px;color:#3b82f6;text-decoration:none;">Load more</a>
                </div>
            @endif
        </div>
    </div>
</div>

<input type="hidden" id="notification_page" value="1">

<script>
var smartNotifOpen = false;
var smartAlertsLoaded = false;

function toggleSmartNotif() {
    smartNotifOpen = !smartNotifOpen;
    var panel = document.getElementById('smart-notif-panel');
    panel.style.display = smartNotifOpen ? 'block' : 'none';
    if (smartNotifOpen && !smartAlertsLoaded) {
        loadSmartAlerts();
        // Also load regular notifications
        if ($('#notifications_list').data('loaded') !== true) {
            loadNotifications();
        }
    }
}

document.addEventListener('click', function(e) {
    if (!document.getElementById('smart-notif-btn').contains(e.target) &&
        !document.getElementById('smart-notif-panel').contains(e.target)) {
        document.getElementById('smart-notif-panel').style.display = 'none';
        smartNotifOpen = false;
    }
});

function loadSmartAlerts() {
    smartAlertsLoaded = true;
    $.get('{{ route("smart.alerts") }}', function(data) {
        var alertIcons = {
            'low_stock': '<svg style="width:16px;height:16px;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10.24 3.957l-8.422 14.06a1.989 1.989 0 0 0 1.7 2.983h16.845a1.989 1.989 0 0 0 1.7 -2.983l-8.423 -14.06a1.989 1.989 0 0 0 -3.4 0z"/><path d="M12 9v4"/><path d="M12 17h.01"/></svg>',
            'pending_mpesa': '<svg style="width:16px;height:16px;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0"/><path d="M3 6m0 2a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v8a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2z"/></svg>',
            'overdue_payments': '<svg style="width:16px;height:16px;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0"/><path d="M12 7v5l3 3"/></svg>',
            'gym_expiry': '<svg style="width:16px;height:16px;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M6.5 6.5m-1.5 0a1.5 1.5 0 1 0 3 0a1.5 1.5 0 1 0 -3 0"/><path d="M17.5 6.5m-1.5 0a1.5 1.5 0 1 0 3 0a1.5 1.5 0 1 0 -3 0"/><path d="M6.5 17.5m-1.5 0a1.5 1.5 0 1 0 3 0a1.5 1.5 0 1 0 -3 0"/><path d="M17.5 17.5m-1.5 0a1.5 1.5 0 1 0 3 0a1.5 1.5 0 1 0 -3 0"/><path d="M8 6.5h7"/><path d="M8 17.5h7"/><path d="M6.5 8v7"/><path d="M17.5 8v7"/></svg>',
        };
        var alertColors = {
            'low_stock': { bg: '#fef3c7', color: '#d97706' },
            'pending_mpesa': { bg: '#dbeafe', color: '#2563eb' },
            'overdue_payments': { bg: '#fee2e2', color: '#dc2626' },
            'gym_expiry': { bg: '#ede9fe', color: '#7c3aed' },
        };
        var html = '';
        if (data.alerts && data.alerts.length > 0) {
            data.alerts.forEach(function(alert) {
                var ic = alertIcons[alert.type] || '';
                var colors = alertColors[alert.type] || { bg: '#f1f5f9', color: '#64748b' };
                html += '<a href="' + (alert.url || '#') + '" style="display:flex;align-items:center;gap:12px;padding:10px 16px;text-decoration:none;color:#1e293b;border-bottom:1px solid #f8fafc;"' +
                    ' onmouseover="this.style.background=\'#f8fafc\'" onmouseout="this.style.background=\'\'">' +
                    '<span style="width:32px;height:32px;background:' + colors.bg + ';color:' + colors.color + ';border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">' + ic + '</span>' +
                    '<div style="min-width:0;flex:1;"><div style="font-size:13px;font-weight:500;">' + alert.title + '</div><div style="font-size:12px;color:#94a3b8;">' + alert.message + '</div></div>' +
                    (alert.count ? '<span style="background:' + colors.bg + ';color:' + colors.color + ';border-radius:12px;padding:2px 8px;font-size:11px;font-weight:700;">' + alert.count + '</span>' : '') +
                    '</a>';
            });
        } else {
            html = '<div style="padding:12px 16px;text-align:center;color:#94a3b8;font-size:13px;">No alerts right now ✓</div>';
        }
        document.getElementById('smart-alerts-list').innerHTML = html;
        // Update badge count to include smart alerts
        if (data.total_alert_count > 0) {
            var badge = document.getElementById('notif-badge');
            badge.style.display = 'flex';
            badge.textContent = data.total_alert_count > 9 ? '9+' : data.total_alert_count;
        }
    });
}

function loadNotifications() {
    $('#notifications_list').data('loaded', true);
    $.get('{{ action([\App\Http\Controllers\NotificationController::class, "getNotifications"]) }}', { page: 1 }, function(data) {
        $('#notifications_list').html(data);
    });
}

function markAllRead() {
    $.post('{{ action([\App\Http\Controllers\NotificationController::class, "markAllRead"]) }}', { _token: '{{ csrf_token() }}' }, function() {
        document.getElementById('notif-badge').style.display = 'none';
        loadNotifications();
    });
}
</script>
