<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Symfony\Component\HttpFoundation\IpUtils;

class IpAllowed extends Model
{
    protected $table = 'ip_allowed';

    protected $fillable = ['ip_address', 'label', 'added_by', 'is_active', 'is_banned', 'ban_reason', 'business_id'];

    protected $casts = ['is_active' => 'boolean', 'is_banned' => 'boolean'];

    public function addedBy()
    {
        return $this->belongsTo(\App\User::class, 'added_by');
    }

    /** True if IP is explicitly banned */
    public static function isBanned(string $ip, int $businessId): bool
    {
        return self::where('ip_address', $ip)
            ->where(fn($q) => $q->where('business_id', $businessId)->orWhereNull('business_id'))
            ->where('is_banned', true)
            ->exists();
    }

    /** True if IP is in the active whitelist (supports exact IPs and CIDR ranges e.g. 192.168.1.0/24) */
    public static function isWhitelisted(string $ip, int $businessId): bool
    {
        $ranges = self::where('is_active', true)
            ->where('is_banned', false)
            ->where(fn($q) => $q->where('business_id', $businessId)->orWhereNull('business_id'))
            ->pluck('ip_address')
            ->toArray();

        return IpUtils::checkIp($ip, $ranges);
    }
}
