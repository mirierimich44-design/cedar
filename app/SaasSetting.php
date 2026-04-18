<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SaasSetting extends Model
{
    protected $guarded = ['id'];
    public $timestamps = true;

    const CACHE_KEY = 'saas_settings_all';
    const CACHE_TTL = 3600; // 1 hour

    public static function all_cached(): array
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
            return static::query()->pluck('value', 'key')->toArray();
        });
    }

    public static function get(string $key, $default = null)
    {
        $all = self::all_cached();
        if (!array_key_exists($key, $all)) return $default;

        $row = static::where('key', $key)->first();
        $val = $all[$key];

        if ($row) {
            return match ($row->type) {
                'int'  => (int) $val,
                'bool' => (bool) (int) $val,
                'json' => json_decode($val, true),
                default => $val,
            };
        }
        return $val;
    }

    public static function set(string $key, $value): void
    {
        $row = static::firstOrNew(['key' => $key]);
        $type = $row->type ?: 'string';

        $stored = match ($type) {
            'bool' => $value ? '1' : '0',
            'json' => json_encode($value),
            default => (string) $value,
        };

        $row->value = $stored;
        $row->save();

        Cache::forget(self::CACHE_KEY);
    }

    public static function flushCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    // Convenience helpers used across the app
    public static function trialDays(): int        { return (int) self::get('trial_days', 3); }
    public static function trialEnabled(): bool    { return (bool) self::get('trial_enabled', true); }
    public static function trialGraceDays(): int   { return (int) self::get('trial_grace_days', 3); }
    public static function campaignActive(): bool  { return (bool) self::get('campaign_active', false); }
    public static function campaignDiscount(): int { return (int) self::get('campaign_discount_percent', 0); }
    public static function mpesaPaybill(): string  { return (string) self::get('mpesa_paybill', '4117852'); }
}
