<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSaasSubscriptionsTable extends Migration
{
    public function up()
    {
        Schema::create('saas_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('business_id')->nullable();
            $table->string('billing_cycle')->default('monthly'); // monthly|quarterly|yearly|once
            $table->string('hosting_type')->default('cloud');    // cloud|self
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->string('currency')->default('KES');
            $table->string('status')->default('pending');       // pending|active|grace|suspended|cancelled
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamp('grace_ends_at')->nullable();
            $table->decimal('discount_percent', 5, 2)->default(0);
            $table->timestamps();
        });

        // Pivot — named to match belongsToMany in SaasSubscription::features()
        Schema::create('saas_subscription_features', function (Blueprint $table) {
            $table->unsignedBigInteger('subscription_id');
            $table->unsignedBigInteger('feature_id');
            $table->decimal('price_locked', 10, 2)->default(0);
            $table->primary(['subscription_id', 'feature_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('saas_subscription_features');
        Schema::dropIfExists('saas_subscriptions');
    }
}
