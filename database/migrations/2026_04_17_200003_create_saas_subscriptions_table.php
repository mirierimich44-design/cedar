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
            $table->unsignedInteger('business_id');
            $table->string('billing_cycle'); // monthly, quarterly, yearly, once
            $table->string('hosting_type')->default('cloud'); // cloud, self_hosted
            $table->decimal('total_amount', 10, 2);
            $table->decimal('discount_percent', 5, 2)->default(0);
            $table->string('status')->default('pending'); // pending, active, grace, suspended, cancelled
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamp('grace_ends_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
        });

        Schema::create('saas_subscription_features', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')->constrained('saas_subscriptions')->onDelete('cascade');
            $table->foreignId('feature_id')->constrained('saas_features')->onDelete('cascade');
            $table->decimal('price_locked', 10, 2); // price at time of purchase
        });
    }

    public function down()
    {
        Schema::dropIfExists('saas_subscription_features');
        Schema::dropIfExists('saas_subscriptions');
    }
}
