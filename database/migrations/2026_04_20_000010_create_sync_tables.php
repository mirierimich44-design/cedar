<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSyncTables extends Migration
{
    public function up()
    {
        // ── offline_ref on transactions ──────────────────────────────────────────
        // Idempotency key for transactions submitted from offline devices.
        if (Schema::hasTable('transactions') && ! Schema::hasColumn('transactions', 'offline_ref')) {
            Schema::table('transactions', function (Blueprint $table) {
                $table->string('offline_ref', 80)->nullable()->unique()->after('is_synced')
                      ->comment('Client-generated idempotency ID from offline device');
            });
        }

        // ── sync_tokens ─────────────────────────────────────────────────────────
        // One row per offline device. Tracks when it last pulled from and pushed to server.
        Schema::create('sync_tokens', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('business_id')->unsigned()->index();
            $table->integer('user_id')->unsigned()->nullable()->index();
            $table->string('token', 64)->unique();       // UUID v4 device token
            $table->string('device_name')->nullable();   // e.g. "Cashier PC - Branch 2"
            $table->string('device_type')->default('browser'); // browser | desktop | mobile
            $table->timestamp('last_pulled_at')->nullable();
            $table->timestamp('last_pushed_at')->nullable();
            $table->timestamp('last_seen_at')->nullable();
            $table->boolean('is_active')->default(1);
            $table->timestamps();

            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });

        // ── sync_logs ────────────────────────────────────────────────────────────
        // Audit trail of every pull/push operation.
        Schema::create('sync_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('business_id')->unsigned()->index();
            $table->unsignedInteger('sync_token_id')->nullable()->index();
            $table->enum('direction', ['pull', 'push']);
            $table->enum('status', ['success', 'partial', 'failed'])->default('success');
            $table->json('summary')->nullable();  // {"products":12, "contacts":3, "transactions":7}
            $table->json('errors')->nullable();   // array of error messages
            $table->unsignedSmallInteger('records_sent')->default(0);
            $table->unsignedSmallInteger('records_received')->default(0);
            $table->unsignedSmallInteger('conflicts')->default(0);
            $table->timestamp('synced_at')->useCurrent();

            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
        });

        // ── sync_conflicts ───────────────────────────────────────────────────────
        // Rows where offline and server versions diverged. Admin resolves them.
        Schema::create('sync_conflicts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('business_id')->unsigned()->index();
            $table->string('model');                    // e.g. "Contact", "Transaction"
            $table->unsignedBigInteger('record_id')->nullable(); // server record ID
            $table->json('server_data');
            $table->json('client_data');
            $table->enum('resolution', ['pending', 'server_wins', 'client_wins', 'merged'])
                  ->default('pending');
            $table->unsignedInteger('resolved_by')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();

            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('sync_conflicts');
        Schema::dropIfExists('sync_logs');
        Schema::dropIfExists('sync_tokens');

        if (Schema::hasColumn('transactions', 'offline_ref')) {
            Schema::table('transactions', function (Blueprint $table) {
                $table->dropColumn('offline_ref');
            });
        }
    }
}
