<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

/**
 * php artisan sync:pull
 *
 * Pulls live data from the cloud server into the local database.
 * Useful on Laragon to keep local dev DB in sync with production.
 *
 * Setup in .env (local only):
 *   SYNC_REMOTE_URL=https://reenson.apextechsolutions.co.ke
 *   SYNC_TOKEN=your-device-token-from-the-sync-dashboard
 *   SYNC_BUSINESS_ID=1
 */
class SyncPullCommand extends Command
{
    protected $signature = 'sync:pull
        {--url=      : Remote server URL (overrides SYNC_REMOTE_URL env)}
        {--token=    : Sync device token (overrides SYNC_TOKEN env)}
        {--business= : Business ID to sync (overrides SYNC_BUSINESS_ID env)}
        {--full      : Force full sync (ignore last_pulled_at)}
        {--dry-run   : Show what would be synced without writing to DB}';

    protected $description = 'Pull live data from cloud server into local database (for Laragon dev)';

    public function handle(): int
    {
        // Priority: CLI option > saved settings file > .env
        // Note: use ?: not env($key, default) so that empty .env values don't shadow saved settings
        $saved      = $this->loadSavedSettings();
        $remoteUrl  = rtrim($this->option('url')     ?: ($saved['remote_url']  ?? '') ?: env('SYNC_REMOTE_URL', ''), '/');
        $token      = $this->option('token')          ?: ($saved['sync_token'] ?? '') ?: env('SYNC_TOKEN', '');
        $businessId = (int)($this->option('business') ?: ($saved['business_id'] ?? 0) ?: env('SYNC_BUSINESS_ID', 0));

        if (! $remoteUrl) {
            $this->error('No remote URL. Set it via:');
            $this->line('  1. The /sync settings panel on the live server');
            $this->line('  2. SYNC_REMOTE_URL in .env');
            $this->line('  3. --url=https://reenson.apextechsolutions.co.ke');
            return 1;
        }
        if (! $token) {
            $this->error('No sync token. Get one from: ' . $remoteUrl . '/sync → Register Device');
            $this->line('  Then save it via the /sync settings panel OR add SYNC_TOKEN to .env');
            return 1;
        }

        $endpoint = $remoteUrl . '/sync/pull';
        $this->info("Connecting to: {$endpoint}");
        $this->info("Business ID : {$businessId}");

        // ── Call the remote pull endpoint ────────────────────────────────────
        try {
            $response = Http::withHeaders([
                'X-Sync-Token' => $token,
                'Accept'       => 'application/json',
            ])->timeout(60)->post($endpoint, [
                'sync_token'  => $token,
                'business_id' => $businessId,
                'since'       => $this->option('full') ? null : $this->getLastPulledAt(),
            ]);
        } catch (\Exception $e) {
            $this->error('Connection failed: ' . $e->getMessage());
            return 1;
        }

        if ($response->failed()) {
            $this->error('Server returned ' . $response->status() . ': ' . $response->body());
            return 1;
        }

        $body     = $response->json();
        $data     = $body['data']    ?? [];
        $summary  = $body['summary'] ?? [];
        $pulledAt = $body['pulled_at'] ?? now()->toIso8601String();

        // ── Display summary ──────────────────────────────────────────────────
        $this->newLine();
        $this->line('  <info>Pull summary</info> (' . ($body['is_full'] ? 'full sync' : 'delta') . ')');
        $rows = [];
        foreach ($summary as $key => $count) {
            $rows[] = [$key, $count];
        }
        $this->table(['Dataset', 'Records'], $rows);

        if ($this->option('dry-run')) {
            $this->warn('DRY RUN — nothing written to local DB.');
            return 0;
        }

        // ── Write to local DB ────────────────────────────────────────────────
        $this->info('Writing to local database…');
        $bar = $this->output->createProgressBar(array_sum($summary));
        $bar->start();

        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        try {
            $this->upsertTable('categories',     $data['categories']   ?? [], $bar);
            $this->upsertTable('brands',          $data['brands']       ?? [], $bar, 'brands');
            $this->upsertTable('units',           $data['units']        ?? [], $bar);
            $this->upsertTable('tax_rates',       $data['tax_rates']    ?? [], $bar);
            $this->upsertContacts($businessId, $data['contacts']     ?? [], $bar);
            $this->upsertProducts($businessId, $data['products']     ?? [], $bar);
            $this->upsertStock(   $data['stock']        ?? [], $bar);
        } finally {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }

        $bar->finish();
        $this->newLine(2);

        // Save pulled_at so next run is delta only
        $this->setLastPulledAt($pulledAt);

        $total = array_sum($summary);
        $this->info("✓ Synced {$total} records from {$remoteUrl}");
        $this->line("  Last pulled: {$pulledAt}");
        $this->newLine();

        return 0;
    }

    // ── Upsert helpers ────────────────────────────────────────────────────────

    private function upsertTable(string $table, array $rows, $bar, ?string $override = null): void
    {
        $tbl = $override ?? $table;
        foreach ($rows as $row) {
            if (empty($row['id'])) continue;
            DB::table($tbl)->updateOrInsert(['id' => $row['id']], $row);
            $bar->advance();
        }
    }

    private function upsertContacts(int $businessId, array $rows, $bar): void
    {
        foreach ($rows as $c) {
            if (empty($c['id'])) continue;
            $c['business_id'] = $businessId;
            DB::table('contacts')->updateOrInsert(['id' => $c['id']], $c);
            $bar->advance();
        }
    }

    private function upsertProducts(int $businessId, array $rows, $bar): void
    {
        foreach ($rows as $p) {
            if (empty($p['id'])) continue;
            $variations = $p['variations'] ?? [];
            unset($p['variations']);
            $p['business_id'] = $businessId;

            DB::table('products')->updateOrInsert(['id' => $p['id']], $p);

            foreach ($variations as $v) {
                if (empty($v['id'])) continue;
                DB::table('variations')->updateOrInsert(['id' => $v['id']], $v);
                DB::table('product_variations')->updateOrInsert(
                    ['product_id' => $p['id'], 'variation_id' => $v['id']],
                    ['product_id' => $p['id'], 'variation_id' => $v['id']]
                );
            }
            $bar->advance();
        }
    }

    private function upsertStock(array $rows, $bar): void
    {
        foreach ($rows as $s) {
            unset($s['id']); // composite key, not a real PK
            if (empty($s['variation_id']) || empty($s['location_id'])) continue;
            DB::table('variation_location_details')->updateOrInsert(
                ['variation_id' => $s['variation_id'], 'location_id' => $s['location_id']],
                $s
            );
            $bar->advance();
        }
    }

    // ── Load settings saved from the /sync dashboard UI ─────────────────────

    private function loadSavedSettings(): array
    {
        // Try business 1 first, then scan for any saved config
        foreach ([1, 2, 3] as $bid) {
            $path = storage_path("app/sync_config_{$bid}.json");
            if (file_exists($path)) {
                $data = json_decode(file_get_contents($path), true);
                if (is_array($data) && ! empty($data['remote_url'])) {
                    return $data;
                }
            }
        }
        return [];
    }

    // ── Persisting last_pulled_at ─────────────────────────────────────────────

    private function getLastPulledAt(): ?string
    {
        $path = storage_path('sync_last_pulled.txt');
        return file_exists($path) ? trim(file_get_contents($path)) : null;
    }

    private function setLastPulledAt(string $iso): void
    {
        file_put_contents(storage_path('sync_last_pulled.txt'), $iso);
    }
}
