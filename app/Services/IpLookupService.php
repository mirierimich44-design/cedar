<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Jenssegers\Agent\Agent;

class IpLookupService
{
    public function lookup(string $ip): array
    {
        return array_merge($this->geo($ip), $this->device());
    }

    private function geo(string $ip): array
    {
        // Local / private IPs — no lookup needed
        if ($this->isPrivate($ip)) {
            return ['isp' => 'Local Network', 'country' => 'Local', 'city' => 'Local'];
        }

        // Cache per IP for 24 hours so we don't hammer the API
        return Cache::remember("ip_geo_{$ip}", 86400, function () use ($ip) {
            try {
                // ipinfo.io — HTTPS, free tier (50k/month), no key required for basic fields
                $res = Http::timeout(4)->get("https://ipinfo.io/{$ip}/json");

                if ($res->successful() && ! $res->json('bogon')) {
                    $city    = $res->json('city');
                    $region  = $res->json('region');
                    $country = $res->json('country');
                    $org     = $res->json('org'); // e.g. "AS12345 Safaricom PLC"
                    // Strip the AS number prefix from org if present
                    $isp = $org ? preg_replace('/^AS\d+\s+/', '', $org) : null;

                    return [
                        'isp'     => $isp,
                        'country' => $country,
                        'city'    => implode(', ', array_filter([$city, $region])),
                    ];
                }
            } catch (\Throwable $e) {
                // fail silently — never break login
            }

            return ['isp' => null, 'country' => null, 'city' => null];
        });
    }

    private function device(): array
    {
        $agent = new Agent();

        $deviceType = 'desktop';
        if ($agent->isMobile()) $deviceType = 'mobile';
        elseif ($agent->isTablet()) $deviceType = 'tablet';

        return [
            'browser'     => $agent->browser() ?: null,
            'os'          => $agent->platform() ?: null,
            'device_type' => $deviceType,
        ];
    }

    private function isPrivate(string $ip): bool
    {
        return in_array($ip, ['127.0.0.1', '::1'])
            || str_starts_with($ip, '192.168.')
            || str_starts_with($ip, '10.')
            || str_starts_with($ip, '172.');
    }
}
