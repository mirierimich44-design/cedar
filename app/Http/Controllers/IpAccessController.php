<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\IpAllowed;
use App\Models\IpAccessLog;
use App\Models\AccessSchedule;
use App\Models\UserIpSetting;
use App\Models\BusinessFeatureSetting;
use App\BusinessLocation;
use Spatie\Permission\Models\Role;

class IpAccessController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $user = auth()->user();
            $isSuperadmin  = $user->can('superadmin') || $user->hasRole('Superadmin');
            $isAdmin       = $user->hasRole('Admin#' . session('business.id'));
            $hasPermission = $user->can('ip_access.access');

            if (! $isSuperadmin && ! $isAdmin && ! $hasPermission) {
                abort(403);
            }
            return $next($request);
        });
    }

    // ── Settings page ────────────────────────────────────────────────────

    public function settings()
    {
        $business  = auth()->user()->business;
        $businessId = $business->id;

        $allowed   = IpAllowed::where(function ($q) use ($businessId) {
                        $q->where('business_id', $businessId)->orWhereNull('business_id');
                     })->orderByDesc('created_at')->get();
        $roles     = Role::whereNotIn('name', ['Superadmin'])->get();
        $schedules = AccessSchedule::all()->keyBy(fn($s) => $s->role_id . '_' . $s->day_of_week);
        $currentIp = request()->ip();
        $days      = AccessSchedule::$days;

        $restrictionEnabled   = (bool) $business->enable_ip_restriction;
        $currentIpWhitelisted = $allowed->where('ip_address', $currentIp)->where('is_active', true)->isNotEmpty();
        $moduleEnabled        = BusinessFeatureSetting::isEnabled('ip_restriction', $businessId);

        return view('ip_access.settings', compact(
            'allowed', 'roles', 'schedules', 'currentIp', 'days',
            'restrictionEnabled', 'currentIpWhitelisted', 'business', 'moduleEnabled'
        ));
    }

    // ── Toggle module visibility (enable/disable the whole IP module) ────────

    public function toggleModule(Request $request)
    {
        $businessId = auth()->user()->business_id;
        // Support both JSON body (fetch) and regular form POST
        $enable = $request->boolean('enable');

        BusinessFeatureSetting::updateOrCreate(
            ['business_id' => $businessId, 'feature_key' => 'ip_restriction'],
            ['is_enabled' => $enable]
        );
        BusinessFeatureSetting::clearCache($businessId, 'ip_restriction');

        $msg = $enable
            ? 'IP Access Control module enabled. The menu item is now visible to admins.'
            : 'IP Access Control module disabled. The sidebar item is hidden.';

        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json(['success' => true, 'msg' => $msg]);
        }

        return back()->with('status', ['success' => 1, 'msg' => $msg]);
    }

    // ── Toggle IP restriction on/off ─────────────────────────────────────

    public function toggleRestriction(\Illuminate\Http\Request $request)
    {
        $business = auth()->user()->business;

        // Safety: refuse to enable if the current admin IP is not whitelisted
        if ($request->boolean('enable')) {
            $currentIp = $request->ip();
            $businessId = $business->id;
            $isSafe = IpAllowed::where('ip_address', $currentIp)
                ->where('is_active', true)
                ->where(function ($q) use ($businessId) {
                    $q->where('business_id', $businessId)->orWhereNull('business_id');
                })->exists();

            if (! $isSafe) {
                return back()->with('status', [
                    'success' => 0,
                    'msg'     => 'Cannot enable: your current IP (' . $currentIp . ') is not in the whitelist. Add it first, then enable restriction.',
                ]);
            }
        }

        $business->update(['enable_ip_restriction' => $request->boolean('enable')]);

        $msg = $request->boolean('enable')
            ? 'IP restriction ENABLED. Only whitelisted IPs can log in (owners are always exempt).'
            : 'IP restriction DISABLED. All IPs can log in.';

        return back()->with('status', ['success' => 1, 'msg' => $msg]);
    }

    // ── Allowed IPs ──────────────────────────────────────────────────────

    public function addIp(Request $request)
    {
        $request->validate([
            'ip_address' => 'required|ip',
            'label'      => 'nullable|string|max:100',
        ]);

        $businessId = auth()->user()->business_id;

        IpAllowed::firstOrCreate(
            ['ip_address' => $request->ip_address, 'business_id' => $businessId],
            ['label' => $request->label, 'added_by' => auth()->id(), 'is_active' => true]
        );

        return back()->with('success', 'IP address added successfully.');
    }

    public function toggleIp(IpAllowed $ip)
    {
        $ip->update(['is_active' => ! $ip->is_active]);
        return back();
    }

    public function deleteIp(IpAllowed $ip)
    {
        $ip->delete();
        return back()->with('success', 'IP removed.');
    }

    // ── Schedules ────────────────────────────────────────────────────────

    public function saveSchedules(Request $request)
    {
        $schedules = $request->input('schedules', []);

        foreach ($schedules as $roleId => $days) {
            foreach ($days as $day => $data) {
                $active = isset($data['active']) && $data['active'];

                AccessSchedule::updateOrCreate(
                    ['role_id' => $roleId, 'day_of_week' => $day],
                    [
                        'start_time' => $data['start'] ?? '08:00',
                        'end_time'   => $data['end'] ?? '18:00',
                        'is_active'  => $active,
                    ]
                );
            }
        }

        return back()->with('success', 'Schedules saved.');
    }

    // ── Logs ─────────────────────────────────────────────────────────────

    public function logs(Request $request)
    {
        $businessId = auth()->user()->business_id;
        $locations  = BusinessLocation::where('business_id', $businessId)->get();

        // ── IP Summary: unique IPs with stats ─────────────────────────
        $summaryQuery = IpAccessLog::selectRaw('
                ip_address,
                MAX(isp) as isp,
                MAX(country) as country,
                MAX(city) as city,
                COUNT(*) as total_hits,
                SUM(outcome = "success") as successful_logins,
                SUM(outcome = "blocked_ip") as blocked_attempts,
                MAX(created_at) as last_seen,
                COUNT(DISTINCT user_id) as unique_users
            ')
            ->where('business_id', $businessId)
            ->groupBy('ip_address')
            ->orderByDesc('last_seen');

        if ($request->filled('location_id')) {
            $summaryQuery->where('business_location_id', $request->location_id);
        }
        if ($request->filled('ip_search')) {
            $summaryQuery->where('ip_address', 'like', '%' . $request->ip_search . '%');
        }

        $ipSummary = $summaryQuery->get();

        // Attach whitelist/ban status and associated users to each IP row
        $allowedMap = IpAllowed::where(fn($q) => $q->where('business_id', $businessId)->orWhereNull('business_id'))
            ->get()->keyBy('ip_address');

        foreach ($ipSummary as $row) {
            $record = $allowedMap->get($row->ip_address);
            $row->is_banned      = $record ? $record->is_banned : false;
            $row->is_whitelisted = $record && $record->is_active && ! $record->is_banned;
            $row->ban_reason     = $record?->ban_reason;

            // Users who logged in from this IP
            $row->users = IpAccessLog::with('user')
                ->where('ip_address', $row->ip_address)
                ->where('business_id', $businessId)
                ->where('outcome', 'success')
                ->select('user_id')->distinct()
                ->get()
                ->pluck('user.username')
                ->filter()
                ->unique()
                ->values();

            // Business locations associated with this IP
            $row->locations = IpAccessLog::with('location')
                ->where('ip_address', $row->ip_address)
                ->where('business_id', $businessId)
                ->whereNotNull('business_location_id')
                ->select('business_location_id')->distinct()
                ->get()
                ->pluck('location.name')
                ->filter()
                ->unique()
                ->values();
        }

        // ── Activity Log: recent individual entries ────────────────────
        $logQuery = IpAccessLog::with(['user', 'location'])
            ->where('business_id', $businessId)
            ->orderByDesc('created_at');

        if ($request->filled('outcome'))     $logQuery->where('outcome', $request->outcome);
        if ($request->filled('location_id')) $logQuery->where('business_location_id', $request->location_id);
        if ($request->filled('date'))        $logQuery->whereDate('created_at', $request->date);
        if ($request->filled('ip_search'))   $logQuery->where('ip_address', 'like', '%' . $request->ip_search . '%');

        if ($request->has('export')) {
            return $this->exportCsv($logQuery->get());
        }

        $logs = $logQuery->paginate(50)->withQueryString();

        return view('ip_access.logs', compact('ipSummary', 'logs', 'locations', 'allowedMap'));
    }

    // ── Ban an IP ─────────────────────────────────────────────────────────

    public function banIp(Request $request)
    {
        $request->validate(['ip_address' => 'required|ip', 'reason' => 'nullable|string|max:200']);

        $businessId = auth()->user()->business_id;

        IpAllowed::updateOrCreate(
            ['ip_address' => $request->ip_address, 'business_id' => $businessId],
            [
                'is_active'  => false,
                'is_banned'  => true,
                'ban_reason' => $request->reason ?: 'Banned by admin',
                'added_by'   => auth()->id(),
                'label'      => 'BANNED',
            ]
        );

        return back()->with('status', ['success' => 1, 'msg' => "IP {$request->ip_address} has been banned."]);
    }

    // ── Unban an IP ───────────────────────────────────────────────────────

    public function unbanIp(Request $request)
    {
        $request->validate(['ip_address' => 'required|ip']);

        $businessId = auth()->user()->business_id;

        IpAllowed::where('ip_address', $request->ip_address)
            ->where(fn($q) => $q->where('business_id', $businessId)->orWhereNull('business_id'))
            ->update(['is_banned' => false, 'is_active' => true, 'ban_reason' => null]);

        return back()->with('status', ['success' => 1, 'msg' => "IP {$request->ip_address} has been unbanned."]);
    }

    // ── Whitelist an IP directly from logs ────────────────────────────────

    public function whitelistFromLog(Request $request)
    {
        $request->validate(['ip_address' => 'required|ip', 'label' => 'nullable|string|max:100']);

        $businessId = auth()->user()->business_id;

        IpAllowed::updateOrCreate(
            ['ip_address' => $request->ip_address, 'business_id' => $businessId],
            [
                'is_active'  => true,
                'is_banned'  => false,
                'ban_reason' => null,
                'label'      => $request->label ?: 'Added from logs',
                'added_by'   => auth()->id(),
            ]
        );

        return back()->with('status', ['success' => 1, 'msg' => "IP {$request->ip_address} added to whitelist."]);
    }

    public function blockIpFromLog(Request $request)
    {
        // Legacy — redirect to banIp
        return $this->banIp($request);
    }

    // ── User bypass toggles ───────────────────────────────────────────────

    public function saveUserSettings(Request $request, $userId)
    {
        UserIpSetting::updateOrCreate(
            ['user_id' => $userId],
            [
                'bypass_ip_check' => $request->boolean('bypass_ip_check'),
                'bypass_schedule' => $request->boolean('bypass_schedule'),
            ]
        );

        return back()->with('success', 'User access settings saved.');
    }

    private function exportCsv($logs)
    {
        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="access_logs_' . now()->format('Ymd_His') . '.csv"',
        ];

        $callback = function () use ($logs) {
            $fh = fopen('php://output', 'w');
            fputcsv($fh, ['Date', 'User', 'IP', 'ISP', 'Country', 'City', 'Browser', 'OS', 'Device', 'Outcome']);
            foreach ($logs as $log) {
                fputcsv($fh, [
                    $log->created_at,
                    $log->user?->username ?? $log->username_attempted,
                    $log->ip_address,
                    $log->isp,
                    $log->country,
                    $log->city,
                    $log->browser,
                    $log->os,
                    $log->device_type,
                    $log->outcome,
                ]);
            }
            fclose($fh);
        };

        return response()->stream($callback, 200, $headers);
    }
}
