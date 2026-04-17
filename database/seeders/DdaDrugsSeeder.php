<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DdaDrugsSeeder extends Seeder
{
    public function run()
    {
        $drugs = [
            // Opioids
            ['name' => 'Morphine',          'class' => 'Opioid',           'schedule' => 'II',  'description' => 'Strong opioid analgesic for severe pain'],
            ['name' => 'Pethidine',         'class' => 'Opioid',           'schedule' => 'II',  'description' => 'Opioid analgesic, also known as Meperidine'],
            ['name' => 'Codeine',           'class' => 'Opioid',           'schedule' => 'III', 'description' => 'Mild opioid analgesic and cough suppressant'],
            ['name' => 'Tramadol',          'class' => 'Opioid',           'schedule' => 'III', 'description' => 'Centrally acting opioid analgesic'],
            ['name' => 'Fentanyl',          'class' => 'Opioid',           'schedule' => 'II',  'description' => 'Potent synthetic opioid analgesic'],
            ['name' => 'Buprenorphine',     'class' => 'Opioid',           'schedule' => 'III', 'description' => 'Partial opioid agonist for pain and opioid dependence'],
            ['name' => 'Methadone',         'class' => 'Opioid',           'schedule' => 'II',  'description' => 'Opioid used for pain and opioid use disorder treatment'],
            ['name' => 'Oxycodone',         'class' => 'Opioid',           'schedule' => 'II',  'description' => 'Semi-synthetic opioid analgesic'],

            // Benzodiazepines
            ['name' => 'Diazepam',          'class' => 'Benzodiazepine',   'schedule' => 'IV',  'description' => 'Anxiolytic, muscle relaxant and anticonvulsant (Valium)'],
            ['name' => 'Lorazepam',         'class' => 'Benzodiazepine',   'schedule' => 'IV',  'description' => 'Short-acting benzodiazepine anxiolytic'],
            ['name' => 'Alprazolam',        'class' => 'Benzodiazepine',   'schedule' => 'IV',  'description' => 'Short-acting benzodiazepine for anxiety (Xanax)'],
            ['name' => 'Clonazepam',        'class' => 'Benzodiazepine',   'schedule' => 'IV',  'description' => 'Anticonvulsant and anxiolytic benzodiazepine'],
            ['name' => 'Midazolam',         'class' => 'Benzodiazepine',   'schedule' => 'IV',  'description' => 'Short-acting benzodiazepine used in anaesthesia'],
            ['name' => 'Nitrazepam',        'class' => 'Benzodiazepine',   'schedule' => 'IV',  'description' => 'Benzodiazepine hypnotic for insomnia'],
            ['name' => 'Temazepam',         'class' => 'Benzodiazepine',   'schedule' => 'IV',  'description' => 'Short-acting benzodiazepine for sleep disorders'],

            // Barbiturates
            ['name' => 'Phenobarbitone',    'class' => 'Barbiturate',      'schedule' => 'IV',  'description' => 'Barbiturate anticonvulsant and sedative'],

            // Dissociatives
            ['name' => 'Ketamine',          'class' => 'Dissociative',     'schedule' => 'III', 'description' => 'Dissociative anaesthetic with analgesic properties'],

            // Sedatives
            ['name' => 'Zolpidem',          'class' => 'Sedative',         'schedule' => 'IV',  'description' => 'Non-benzodiazepine hypnotic for insomnia'],

            // Anticonvulsants (newly controlled)
            ['name' => 'Pregabalin',        'class' => 'Anticonvulsant',   'schedule' => 'IV',  'description' => 'Anticonvulsant and neuropathic pain agent'],
            ['name' => 'Gabapentin',        'class' => 'Anticonvulsant',   'schedule' => 'IV',  'description' => 'Anticonvulsant for neuropathic pain and epilepsy'],

            // Precursors
            ['name' => 'Pseudoephedrine',   'class' => 'Precursor',        'schedule' => 'III', 'description' => 'Decongestant; controlled as methamphetamine precursor'],
            ['name' => 'Ephedrine',         'class' => 'Precursor',        'schedule' => 'III', 'description' => 'Sympathomimetic; controlled as drug precursor'],
        ];

        foreach ($drugs as $drug) {
            DB::table('dda_drugs')->insertOrIgnore(array_merge($drug, [
                'is_active'  => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
