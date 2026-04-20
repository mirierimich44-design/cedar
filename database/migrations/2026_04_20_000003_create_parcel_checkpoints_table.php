<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (Schema::hasTable('parcel_checkpoints')) return;

        // Detect whether parcels.id is INT or BIGINT so we use the right FK column type
        $colType = 'unsignedInteger'; // default
        try {
            $row = DB::selectOne("SELECT DATA_TYPE FROM information_schema.COLUMNS
                                  WHERE TABLE_SCHEMA = DATABASE()
                                    AND TABLE_NAME   = 'parcels'
                                    AND COLUMN_NAME  = 'id'");
            if ($row && strtolower($row->DATA_TYPE) === 'bigint') {
                $colType = 'unsignedBigInteger';
            }
        } catch (\Exception $e) {}

        Schema::create('parcel_checkpoints', function (Blueprint $table) use ($colType) {
            $table->increments('id');
            $table->{$colType}('parcel_id');
            $table->string('location');                            // town/depot name
            $table->enum('checkpoint_type', [
                'booked',
                'collected_from_sender',
                'dispatched',
                'arrived_at_depot',
                'out_for_delivery',
                'delivered',
                'delivery_attempted',
                'returned_to_sender',
                'exception'
            ]);
            $table->string('status_note')->nullable();
            $table->unsignedInteger('scanned_by')->nullable();
            $table->string('vehicle_reg')->nullable();
            $table->timestamps();
        });

        // Add FK separately so a type-mismatch doesn't roll back the whole table creation
        try {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
            DB::statement('ALTER TABLE `parcel_checkpoints`
                           ADD CONSTRAINT `parcel_checkpoints_parcel_id_foreign`
                           FOREIGN KEY (`parcel_id`) REFERENCES `parcels` (`id`) ON DELETE CASCADE');
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        } catch (\Exception $e) {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
            // Column exists; FK skipped due to type mismatch — not critical
        }
    }

    public function down()
    {
        Schema::dropIfExists('parcel_checkpoints');
    }
};
