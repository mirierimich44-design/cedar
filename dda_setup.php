<?php
/**
 * DDA Setup Script - Run once to create DDA tables and seed drugs
 * Usage: php dda_setup.php
 * Delete this file after running.
 */

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

echo "Starting DDA setup...\n\n";

// 1. Create dda_drugs table
if (!Schema::hasTable('dda_drugs')) {
    Schema::create('dda_drugs', function (Blueprint $table) {
        $table->increments('id');
        $table->string('name');
        $table->string('class')->nullable();
        $table->string('schedule')->nullable();
        $table->text('description')->nullable();
        $table->boolean('is_active')->default(true);
        $table->timestamps();
    });
    echo "Created table: dda_drugs\n";
} else {
    echo "Table already exists: dda_drugs\n";
}

// 2. Create dda_prescriptions table
if (!Schema::hasTable('dda_prescriptions')) {
    Schema::create('dda_prescriptions', function (Blueprint $table) {
        $table->increments('id');
        $table->unsignedInteger('business_id');
        $table->string('patient_name');
        $table->string('patient_id_number')->nullable();
        $table->string('prescriber_name');
        $table->string('prescriber_reg_number')->nullable();
        $table->string('prescription_number')->nullable();
        $table->date('prescription_date')->nullable();
        $table->string('image_path')->nullable();
        $table->text('notes')->nullable();
        $table->unsignedInteger('created_by')->nullable();
        $table->timestamps();
    });
    echo "Created table: dda_prescriptions\n";
} else {
    echo "Table already exists: dda_prescriptions\n";
}

// 3. Create dda_dispense_log table
if (!Schema::hasTable('dda_dispense_log')) {
    Schema::create('dda_dispense_log', function (Blueprint $table) {
        $table->increments('id');
        $table->unsignedInteger('business_id');
        $table->unsignedInteger('dda_drug_id');
        $table->unsignedInteger('dda_prescription_id')->nullable();
        $table->string('patient_name');
        $table->string('patient_id_number')->nullable();
        $table->string('prescriber_name')->nullable();
        $table->decimal('quantity_dispensed', 8, 2);
        $table->string('unit')->nullable();
        $table->string('batch_number')->nullable();
        $table->date('dispensed_date');
        $table->string('dispensed_by')->nullable();
        $table->string('witnessed_by')->nullable();
        $table->unsignedInteger('transaction_id')->nullable();
        $table->text('notes')->nullable();
        $table->unsignedInteger('created_by')->nullable();
        $table->timestamps();
    });
    echo "Created table: dda_dispense_log\n";
} else {
    echo "Table already exists: dda_dispense_log\n";
}

// 4. Create dda_stock_log table
if (!Schema::hasTable('dda_stock_log')) {
    Schema::create('dda_stock_log', function (Blueprint $table) {
        $table->increments('id');
        $table->unsignedInteger('business_id');
        $table->unsignedInteger('dda_drug_id');
        $table->enum('type', ['in', 'out', 'adjustment', 'destruction']);
        $table->decimal('quantity', 8, 2);
        $table->string('reference')->nullable();
        $table->date('movement_date');
        $table->text('notes')->nullable();
        $table->unsignedInteger('created_by')->nullable();
        $table->timestamps();
    });
    echo "Created table: dda_stock_log\n";
} else {
    echo "Table already exists: dda_stock_log\n";
}

// 5. Create dda_destruction_log table
if (!Schema::hasTable('dda_destruction_log')) {
    Schema::create('dda_destruction_log', function (Blueprint $table) {
        $table->increments('id');
        $table->unsignedInteger('business_id');
        $table->unsignedInteger('dda_drug_id');
        $table->decimal('quantity_destroyed', 8, 2);
        $table->string('unit')->nullable();
        $table->string('batch_number')->nullable();
        $table->date('destruction_date');
        $table->string('method')->nullable();
        $table->string('witness_1')->nullable();
        $table->string('witness_2')->nullable();
        $table->string('ppb_officer')->nullable();
        $table->string('certificate_number')->nullable();
        $table->text('notes')->nullable();
        $table->unsignedInteger('created_by')->nullable();
        $table->timestamps();
    });
    echo "Created table: dda_destruction_log\n";
} else {
    echo "Table already exists: dda_destruction_log\n";
}

// 6. Add is_dda and dda_drug_id to products table
if (!Schema::hasColumn('products', 'is_dda')) {
    Schema::table('products', function (Blueprint $table) {
        $table->boolean('is_dda')->default(false)->after('not_for_selling');
        $table->unsignedInteger('dda_drug_id')->nullable()->after('is_dda');
    });
    echo "Added columns is_dda and dda_drug_id to products\n";
} else {
    echo "Columns already exist on products\n";
}

// 7. Seed DDA drugs
$count = DB::table('dda_drugs')->count();
if ($count === 0) {
    $drugs = [
        // Opioids
        ['name' => 'Morphine', 'class' => 'Opioid', 'schedule' => 'Schedule I', 'description' => 'Strong opioid analgesic for severe pain'],
        ['name' => 'Pethidine (Meperidine)', 'class' => 'Opioid', 'schedule' => 'Schedule I', 'description' => 'Opioid used for moderate to severe pain'],
        ['name' => 'Codeine', 'class' => 'Opioid', 'schedule' => 'Schedule II', 'description' => 'Mild opioid analgesic and antitussive'],
        ['name' => 'Tramadol', 'class' => 'Opioid', 'schedule' => 'Schedule II', 'description' => 'Synthetic opioid for moderate to severe pain'],
        ['name' => 'Fentanyl', 'class' => 'Opioid', 'schedule' => 'Schedule I', 'description' => 'Highly potent synthetic opioid analgesic'],
        ['name' => 'Buprenorphine', 'class' => 'Opioid', 'schedule' => 'Schedule I', 'description' => 'Partial opioid agonist for pain and opioid dependence'],
        ['name' => 'Methadone', 'class' => 'Opioid', 'schedule' => 'Schedule I', 'description' => 'Long-acting opioid for pain and opioid dependence treatment'],
        ['name' => 'Oxycodone', 'class' => 'Opioid', 'schedule' => 'Schedule I', 'description' => 'Semi-synthetic opioid for moderate to severe pain'],
        // Benzodiazepines
        ['name' => 'Diazepam', 'class' => 'Benzodiazepine', 'schedule' => 'Schedule III', 'description' => 'Anxiolytic and muscle relaxant'],
        ['name' => 'Lorazepam', 'class' => 'Benzodiazepine', 'schedule' => 'Schedule III', 'description' => 'Short-acting benzodiazepine for anxiety and seizures'],
        ['name' => 'Alprazolam', 'class' => 'Benzodiazepine', 'schedule' => 'Schedule III', 'description' => 'Benzodiazepine for anxiety and panic disorders'],
        ['name' => 'Clonazepam', 'class' => 'Benzodiazepine', 'schedule' => 'Schedule III', 'description' => 'Anticonvulsant and anxiolytic'],
        ['name' => 'Midazolam', 'class' => 'Benzodiazepine', 'schedule' => 'Schedule III', 'description' => 'Short-acting benzodiazepine for sedation and anesthesia'],
        ['name' => 'Nitrazepam', 'class' => 'Benzodiazepine', 'schedule' => 'Schedule III', 'description' => 'Hypnotic benzodiazepine for insomnia'],
        ['name' => 'Temazepam', 'class' => 'Benzodiazepine', 'schedule' => 'Schedule III', 'description' => 'Hypnotic benzodiazepine for insomnia'],
        // Barbiturates
        ['name' => 'Phenobarbitone', 'class' => 'Barbiturate', 'schedule' => 'Schedule III', 'description' => 'Anticonvulsant barbiturate for epilepsy'],
        // Dissociatives
        ['name' => 'Ketamine', 'class' => 'Dissociative', 'schedule' => 'Schedule II', 'description' => 'Dissociative anesthetic used in veterinary and human medicine'],
        // Sedatives
        ['name' => 'Zolpidem', 'class' => 'Sedative', 'schedule' => 'Schedule III', 'description' => 'Non-benzodiazepine hypnotic for insomnia'],
        // Anticonvulsants
        ['name' => 'Pregabalin', 'class' => 'Anticonvulsant', 'schedule' => 'Schedule IV', 'description' => 'Anticonvulsant for neuropathic pain and epilepsy'],
        ['name' => 'Gabapentin', 'class' => 'Anticonvulsant', 'schedule' => 'Schedule IV', 'description' => 'Anticonvulsant for neuropathic pain and epilepsy'],
        // Precursors
        ['name' => 'Pseudoephedrine', 'class' => 'Precursor', 'schedule' => 'Schedule IV', 'description' => 'Decongestant and precursor to methamphetamine'],
        ['name' => 'Ephedrine', 'class' => 'Precursor', 'schedule' => 'Schedule IV', 'description' => 'Bronchodilator and precursor to methamphetamine'],
    ];

    $now = now();
    foreach ($drugs as &$drug) {
        $drug['is_active'] = 1;
        $drug['created_at'] = $now;
        $drug['updated_at'] = $now;
    }
    DB::table('dda_drugs')->insert($drugs);
    echo "Seeded " . count($drugs) . " DDA drugs\n";
} else {
    echo "DDA drugs already seeded ($count records)\n";
}

// 8. Add DDA permissions to Admin roles
echo "\nAdding DDA permissions to Admin roles...\n";
$dda_permissions = [
    'dda.view', 'dda.manage', 'dda.dispense',
    'dda.prescriptions.view', 'dda.prescriptions.upload',
    'dda.reports.view', 'dda.destruction.manage',
];

foreach ($dda_permissions as $perm) {
    $exists = DB::table('permissions')->where('name', $perm)->where('guard_name', 'web')->first();
    if (!$exists) {
        DB::table('permissions')->insert([
            'name' => $perm,
            'guard_name' => 'web',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        echo "Created permission: $perm\n";
    }
}

// Assign all DDA permissions to all Admin roles
$adminRoles = DB::table('roles')->where('name', 'like', 'Admin%')->get();
foreach ($adminRoles as $role) {
    foreach ($dda_permissions as $perm) {
        $permission = DB::table('permissions')->where('name', $perm)->where('guard_name', 'web')->first();
        if ($permission) {
            $alreadyAssigned = DB::table('role_has_permissions')
                ->where('permission_id', $permission->id)
                ->where('role_id', $role->id)
                ->exists();
            if (!$alreadyAssigned) {
                DB::table('role_has_permissions')->insert([
                    'permission_id' => $permission->id,
                    'role_id' => $role->id,
                ]);
            }
        }
    }
    echo "Permissions assigned to role: {$role->name}\n";
}

// 9. Create uploads directory for prescriptions
$uploadDir = __DIR__ . '/public/uploads/dda';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
    echo "\nCreated directory: public/uploads/dda\n";
}

// 10. Clear cache
echo "\nClearing cache...\n";
\Artisan::call('optimize:clear');
echo \Artisan::output();

echo "\nDDA setup complete!\n";
echo "IMPORTANT: Delete this file from the server now.\n";
