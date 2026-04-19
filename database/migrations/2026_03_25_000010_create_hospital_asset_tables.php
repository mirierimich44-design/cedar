<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHospitalAssetTables extends Migration
{
    public function up()
    {
        // 1. Assets Table
        Schema::create('hospital_assets', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('business_id')->unsigned();
            $table->string('name');
            $table->string('asset_code')->unique(); // Tag number / Barcode
            $table->string('model')->nullable();
            $table->string('serial_number')->nullable();
            $table->string('category')->nullable(); // Medical, Furniture, IT
            $table->integer('location_id')->unsigned()->nullable(); // Linking to Wards/Depts
            $table->date('purchase_date')->nullable();
            $table->decimal('purchase_price', 22, 4)->default(0);
            $table->date('warranty_expiry')->nullable();
            $table->enum('status', ['active', 'under_maintenance', 'disposed', 'broken'])->default('active');
            $table->date('next_service_date')->nullable();
            $table->timestamps();

            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
        });

        // 2. Asset Maintenance Log
        Schema::create('hospital_asset_maintenance', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('asset_id')->unsigned();
            $table->date('service_date');
            $table->string('service_type'); // Preventive, Repair, Calibration
            $table->text('details')->nullable();
            $table->decimal('cost', 22, 4)->default(0);
            $table->string('performed_by')->nullable(); // External vendor or internal tech
            $table->timestamps();

            $table->foreign('asset_id')->references('id')->on('hospital_assets')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('hospital_asset_maintenance');
        Schema::dropIfExists('hospital_assets');
    }
}
