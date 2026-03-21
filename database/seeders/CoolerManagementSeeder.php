<?php

namespace Database\Seeders;

use App\CoolerAsset;
use App\CoolerDealer;
use App\CoolerAgreement;
use App\CoolerComplianceLog;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CoolerManagementSeeder extends Seeder
{
    /**
     * Seed sample cooler management data for a given business.
     * Run: php artisan db:seed --class=CoolerManagementSeeder
     */
    public function run(): void
    {
        // Get the first business in the system
        $business = DB::table('business')->first();
        if (!$business) {
            $this->command->warn('No business found. Create a business first.');
            return;
        }

        $user = DB::table('users')->where('business_id', $business->id)->first();
        if (!$user) {
            $this->command->warn('No user found for business ' . $business->id);
            return;
        }

        $businessId = $business->id;
        $userId     = $user->id;

        // Sample dealers
        $dealerData = [
            ['name' => 'John Kamau', 'outlet_name' => 'Kamau General Store', 'id_number' => '12345678', 'kra_pin' => 'A123456789B', 'phone' => '0712345678', 'channel' => 'retail', 'area' => 'Westlands', 'road' => 'Waiyaki Way', 'compliance_score' => 95.00],
            ['name' => 'Mary Wanjiku', 'outlet_name' => 'Wanjiku Supermart', 'id_number' => '23456789', 'kra_pin' => 'B234567890C', 'phone' => '0723456789', 'channel' => 'supermarket', 'area' => 'Kasarani', 'road' => 'Thika Road', 'compliance_score' => 78.00],
            ['name' => 'Peter Otieno', 'outlet_name' => 'Otieno Wholesale', 'id_number' => '34567890', 'kra_pin' => 'C345678901D', 'phone' => '0734567890', 'channel' => 'wholesale', 'area' => 'Industrial Area', 'road' => 'Enterprise Road', 'compliance_score' => 45.00],
            ['name' => 'Grace Muthoni', 'outlet_name' => 'Muthoni Kiosk', 'id_number' => '45678901', 'kra_pin' => 'D456789012E', 'phone' => '0745678901', 'channel' => 'kiosk', 'area' => 'Kibera', 'road' => 'Kibera Drive', 'compliance_score' => 90.00],
        ];

        $dealers = [];
        foreach ($dealerData as $data) {
            $dealer = CoolerDealer::firstOrCreate(
                ['business_id' => $businessId, 'id_number' => $data['id_number']],
                array_merge($data, [
                    'business_id'       => $businessId,
                    'kra_pin'           => $data['kra_pin'],
                    'postal_address'    => 'P.O. Box ' . rand(1000, 9999) . ', Nairobi',
                    'building'          => 'Block ' . chr(rand(65, 90)),
                    'years_in_business' => rand(1, 15),
                    'brands_stocked'    => ['Pepsi', 'Mountain Dew', 'Miranda'],
                    'status'            => 'active',
                    'created_by'        => $userId,
                ])
            );
            $dealers[] = $dealer;
        }

        // Sample coolers
        $coolerTypes = ['Upright Cooler 300L', 'Chest Cooler 200L', 'Display Cooler 150L', 'Upright Cooler 500L'];
        $assets      = [];

        for ($i = 1; $i <= 8; $i++) {
            $asset = CoolerAsset::firstOrCreate(
                ['business_id' => $businessId, 'asset_number' => 'SBC-' . str_pad($i, 3, '0', STR_PAD_LEFT)],
                [
                    'business_id'       => $businessId,
                    'asset_type'        => $coolerTypes[($i - 1) % count($coolerTypes)],
                    'serial_number'     => 'SN' . rand(100000, 999999),
                    'cooler_tag'        => 'TAG-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                    'status'            => $i <= 4 ? 'deployed' : 'available',
                    'replacement_value' => rand(50, 200) * 1000,
                    'created_by'        => $userId,
                ]
            );
            $assets[] = $asset;
        }

        // Agreements — one per deployed cooler
        foreach (array_slice($dealers, 0, 4) as $idx => $dealer) {
            $cooler    = $assets[$idx];
            $agreement = CoolerAgreement::firstOrCreate(
                ['business_id' => $businessId, 'cooler_id' => $cooler->id, 'dealer_id' => $dealer->id],
                [
                    'business_id'         => $businessId,
                    'agreement_date'      => now()->subMonths(rand(1, 12))->toDateString(),
                    'sales_volume_target' => rand(30, 100) * 1000,
                    'status'              => 'active',
                    'created_by'          => $userId,
                ]
            );

            // Link cooler to dealer
            $cooler->update([
                'current_dealer_id' => $dealer->id,
                'deployment_date'   => $agreement->agreement_date,
            ]);

            // Add compliance log
            CoolerComplianceLog::create([
                'dealer_id'  => $dealer->id,
                'cooler_id'  => $cooler->id,
                'check_type' => 'sales_volume',
                'status'     => $dealer->compliance_score >= 70 ? 'compliant' : 'non_compliant',
                'details'    => ['note' => 'Auto-seeded compliance check'],
                'checked_at' => now()->subDays(rand(1, 30)),
                'checked_by' => $userId,
            ]);
        }

        // Create upload directories
        foreach (['dealers', 'agreements', 'retrievals'] as $dir) {
            $path = public_path("uploads/cooler/{$dir}");
            if (!is_dir($path)) {
                mkdir($path, 0755, true);
            }
        }

        $this->command->info('Cooler Management data seeded successfully.');
        $this->command->info('  → ' . count($dealers) . ' dealers');
        $this->command->info('  → ' . count($assets) . ' coolers (' . count(array_slice($assets, 0, 4)) . ' deployed)');
        $this->command->info('  → ' . count(array_slice($dealers, 0, 4)) . ' agreements');
    }
}
