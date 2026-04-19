<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HospitalDefaultsSeeder extends Seeder
{
    public function run()
    {
        $business_id = 1; // Assuming default business ID is 1

        // 1. Lab Tests
        $lab_tests = [
            // Hematology
            ['name' => 'Full Hemogram (CBC)', 'short_name' => 'FHG/CBC', 'price' => 1200],
            ['name' => 'Hemoglobin (Hb)', 'short_name' => 'Hb', 'price' => 500],
            ['name' => 'ESR', 'short_name' => 'ESR', 'price' => 600],
            ['name' => 'Blood Grouping & Rh', 'short_name' => 'BG', 'price' => 500],
            
            // Biochemistry
            ['name' => 'Random Blood Sugar', 'short_name' => 'RBS', 'price' => 400],
            ['name' => 'Fasting Blood Sugar', 'short_name' => 'FBS', 'price' => 400],
            ['name' => 'Kidney Function Tests (KFT)', 'short_name' => 'KFT', 'price' => 3500],
            ['name' => 'Liver Function Tests (LFT)', 'short_name' => 'LFT', 'price' => 3500],
            ['name' => 'Lipid Profile', 'short_name' => 'Lipid', 'price' => 3000],
            ['name' => 'HbA1c', 'short_name' => 'HbA1c', 'price' => 2500],

            // Parasitology/Microbiology
            ['name' => 'Malaria Smear (BS for MPS)', 'short_name' => 'BS', 'price' => 400],
            ['name' => 'Urinalysis', 'short_name' => 'U/A', 'price' => 600],
            ['name' => 'Stool Microscopy', 'short_name' => 'Stool', 'price' => 600],
            ['name' => 'Gram Stain', 'short_name' => 'Gram', 'price' => 800],

            // Serology
            ['name' => 'HIV Rapid Test', 'short_name' => 'HIV', 'price' => 0],
            ['name' => 'VDRL (Syphilis)', 'short_name' => 'VDRL', 'price' => 500],
            ['name' => 'Brucella Test', 'short_name' => 'Bruc', 'price' => 800],
            ['name' => 'H. Pylori Antigen', 'short_name' => 'H.Pylori', 'price' => 1500],
            ['name' => 'Pregnancy Test (Urine)', 'short_name' => 'PT', 'price' => 500],
        ];

        foreach ($lab_tests as $test) {
            DB::table('hospital_lab_tests')->updateOrInsert(
                ['name' => $test['name'], 'business_id' => $business_id],
                ['price' => $test['price']]
            );
        }

        // 2. Radiography Tests
        $radio_tests = [
            ['name' => 'Chest X-Ray (PA View)', 'short_name' => 'CXR', 'type' => 'x-ray', 'price' => 2500],
            ['name' => 'Abdominal X-Ray', 'short_name' => 'AXR', 'type' => 'x-ray', 'price' => 2500],
            ['name' => 'Pelvic X-Ray', 'short_name' => 'PXR', 'type' => 'x-ray', 'price' => 2500],
            ['name' => 'Obstetric Ultrasound', 'short_name' => 'OB US', 'type' => 'ultrasound', 'price' => 3000],
            ['name' => 'Abdominal Ultrasound', 'short_name' => 'Abd US', 'type' => 'ultrasound', 'price' => 3500],
            ['name' => 'CT Scan Brain (Plain)', 'short_name' => 'CT Brain', 'type' => 'ct-scan', 'price' => 9000],
            ['name' => 'MRI Brain', 'short_name' => 'MRI Brain', 'type' => 'mri', 'price' => 18000],
        ];

        foreach ($radio_tests as $test) {
            DB::table('hospital_radiography_tests')->updateOrInsert(
                ['name' => $test['name'], 'business_id' => $business_id],
                ['short_name' => $test['short_name'], 'type' => $test['type'], 'price' => $test['price']]
            );
        }

        // 3. Surgeries
        $surgeries = [
            ['name' => 'Caesarean Section', 'base_price' => 35000],
            ['name' => 'Normal Delivery Package', 'base_price' => 12000],
            ['name' => 'Appendectomy', 'base_price' => 50000],
            ['name' => 'Hernia Repair', 'base_price' => 45000],
            ['name' => 'Exploratory Laparotomy', 'base_price' => 70000],
            ['name' => 'Debridement & Dressing', 'base_price' => 15000],
            ['name' => 'Circumcision', 'base_price' => 8000],
        ];

        foreach ($surgeries as $s) {
            DB::table('hospital_surgeries')->updateOrInsert(
                ['name' => $s['name'], 'business_id' => $business_id],
                ['base_price' => $s['base_price']]
            );
        }

        // 4. Default Wards & Beds
        $wards = [
            ['name' => 'General Male Ward'],
            ['name' => 'General Female Ward'],
            ['name' => 'Maternity Ward'],
            ['name' => 'Paediatric Ward'],
        ];

        foreach ($wards as $w) {
            $ward_id = DB::table('hospital_wards')->updateOrInsert(
                ['name' => $w['name'], 'business_id' => $business_id],
                []
            );
            
            // Add some beds to each ward if none exist
            // (Using manual ID retrieval for simpler seeder logic)
            $db_ward = DB::table('hospital_wards')->where('name', $w['name'])->where('business_id', $business_id)->first();
            
            for ($i = 1; $i <= 5; $i++) {
                DB::table('hospital_beds')->updateOrInsert(
                    ['name' => 'Bed ' . $i, 'ward_id' => $db_ward->id],
                    ['is_available' => 1]
                );
            }
        }
    }
}
