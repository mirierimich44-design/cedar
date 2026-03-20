<?php

namespace Database\Seeders;

use App\Business;
use App\TaxRate;
use App\User;
use Illuminate\Database\Seeder;

class KenyaVatRatesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * This seeder adds Kenya VAT rates (as of 2024):
     * - Standard Rate: 16%
     * - Zero Rate: 0%
     * - Exempt: 0% (marked as exempt)
     *
     * @return void
     */
    public function run()
    {
        $businesses = Business::all();

        foreach ($businesses as $business) {
            $this->addKenyaVatRates($business);
        }
    }

    /**
     * Add Kenya VAT rates to a specific business
     */
    public function addKenyaVatRates(Business $business)
    {
        // Get the first user of the business to set as creator
        $user = User::where('business_id', $business->id)->first();
        $created_by = $user ? $user->id : 1;

        $kenya_vat_rates = [
            [
                'name' => 'VAT 16%',
                'amount' => 16.00,
                'description' => 'Standard VAT rate for Kenya (KRA)',
            ],
            [
                'name' => 'VAT 0%',
                'amount' => 0.00,
                'description' => 'Zero-rated supplies (exports, etc)',
            ],
            [
                'name' => 'VAT Exempt',
                'amount' => 0.00,
                'description' => 'VAT exempt supplies (financial services, education, etc)',
            ],
        ];

        foreach ($kenya_vat_rates as $vat) {
            // Check if this tax rate already exists
            $exists = TaxRate::where('business_id', $business->id)
                ->where('name', $vat['name'])
                ->exists();

            if (!$exists) {
                TaxRate::create([
                    'business_id' => $business->id,
                    'name' => $vat['name'],
                    'amount' => $vat['amount'],
                    'is_tax_group' => 0,
                    'for_tax_group' => 0,
                    'created_by' => $created_by,
                ]);
            }
        }
    }
}
