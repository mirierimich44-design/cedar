<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SyncToken extends Model
{
    protected $table = 'sync_tokens';

    protected $fillable = [
        'business_id', 'user_id', 'token', 'device_name',
        'device_type', 'last_pulled_at', 'last_pushed_at',
        'last_seen_at', 'is_active',
    ];

    protected $casts = [
        'last_pulled_at' => 'datetime',
        'last_pushed_at' => 'datetime',
        'last_seen_at'   => 'datetime',
        'is_active'      => 'boolean',
    ];

    // ── Relations ────────────────────────────────────────────────────────────

    public function business()
    {
        return $this->belongsTo(\App\Business::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\User::class);
    }

    public function logs()
    {
        return $this->hasMany(SyncLog::class, 'sync_token_id');
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    /** Generate a new unique device token. */
    public static function generate(): string
    {
        do {
            $token = Str::random(32) . '-' . time();
        } while (static::where('token', $token)->exists());

        return $token;
    }

    public function touch_seen(): void
    {
        $this->last_seen_at = now();
        $this->saveQuietly();
    }
}
