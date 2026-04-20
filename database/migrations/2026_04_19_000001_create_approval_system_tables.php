<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Approval Flows — define a named workflow (e.g. "Purchase Order Approval")
        Schema::create('approval_flows', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('business_id');
            $table->string('name');                           // e.g. "Purchase Order > 50k"
            $table->string('approvable_type');                // e.g. "App\PurchaseOrder"
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index('business_id');
        });

        // Approval Flow Steps — ordered steps within a flow
        Schema::create('approval_flow_steps', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('approval_flow_id');
            $table->unsignedBigInteger('role_id')->nullable();   // Spatie role
            $table->unsignedBigInteger('user_id')->nullable();   // specific user override
            $table->string('label')->nullable();                  // e.g. "Finance Manager"
            $table->integer('order')->default(1);
            $table->timestamps();
            $table->foreign('approval_flow_id')->references('id')->on('approval_flows')->onDelete('cascade');
        });

        // Approvals — one record per document waiting for/having gone through approval
        Schema::create('approvals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('business_id');
            $table->string('approvable_type');                // polymorphic type
            $table->unsignedBigInteger('approvable_id');      // polymorphic id
            $table->string('title');                          // human label e.g. "PO #1023 - KES 120,000"
            $table->unsignedBigInteger('requested_by');       // user who submitted
            $table->unsignedBigInteger('approval_flow_id')->nullable();
            $table->integer('current_step')->default(1);
            $table->enum('status', ['pending', 'approved', 'rejected', 'returned'])->default('pending');
            $table->text('notes')->nullable();                // submitter notes
            $table->timestamps();
            $table->index(['approvable_type', 'approvable_id']);
            $table->index(['business_id', 'status']);
        });

        // Approval Decisions — one row per step action taken
        Schema::create('approval_decisions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('approval_id');
            $table->unsignedBigInteger('step_number');
            $table->unsignedBigInteger('decided_by');         // user who acted
            $table->enum('decision', ['approved', 'rejected', 'returned']);
            $table->text('comment')->nullable();
            $table->timestamp('decided_at')->useCurrent();
            $table->foreign('approval_id')->references('id')->on('approvals')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('approval_decisions');
        Schema::dropIfExists('approvals');
        Schema::dropIfExists('approval_flow_steps');
        Schema::dropIfExists('approval_flows');
    }
};
