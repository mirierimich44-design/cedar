<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hospital_queue', function (Blueprint $table) {
            if (!Schema::hasColumn('hospital_queue', 'triage_vitals')) {
                $table->text('triage_vitals')->nullable()->after('token_number');
            }
            if (!Schema::hasColumn('hospital_queue', 'triage_notes')) {
                $table->text('triage_notes')->nullable()->after('triage_vitals');
            }
        });
    }

    public function down(): void
    {
        Schema::table('hospital_queue', function (Blueprint $table) {
            $table->dropColumn(['triage_vitals', 'triage_notes']);
        });
    }
};
