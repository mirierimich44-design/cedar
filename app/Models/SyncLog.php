<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SyncLog extends Model
{
    public $timestamps = false;

    protected $table = 'sync_logs';

    protected $fillable = [
        'business_id', 'sync_token_id', 'direction', 'status',
        'summary', 'errors', 'records_sent', 'records_received',
        'conflicts', 'synced_at',
    ];

    protected $casts = [
        'summary'    => 'array',
        'errors'     => 'array',
        'synced_at'  => 'datetime',
    ];

    public function token()
    {
        return $this->belongsTo(SyncToken::class, 'sync_token_id');
    }
}
