<?php

namespace App\Jobs;

use App\Models\IpAccessLog;
use App\Services\IpLookupService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class LookupGeoForLog implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;
    public int $timeout = 10;

    public function __construct(
        public readonly int    $logId,
        public readonly string $ip
    ) {}

    public function handle(IpLookupService $service): void
    {
        $log = IpAccessLog::find($this->logId);
        if (! $log) return;

        $geo = $service->geo($this->ip ?: $log->ip_address);

        $log->update([
            'isp'     => $geo['isp']     ?? null,
            'country' => $geo['country'] ?? null,
            'city'    => $geo['city']    ?? null,
        ]);
    }
}
