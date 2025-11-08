<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('vendor_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('plan_id')->constrained()->cascadeOnDelete();
            $table->decimal('payment_amount')->default(0);
            $table->string('payment_method')->nullable();
            $table->enum('payment_status', ['paid', 'unpaid'])->default('paid');
            $table->date('payment_date')->nullable();
            $table->date('next_payment_date')->nullable();
            $table->date('starts_at');
            $table->date('ends_at');
            $table->date('expiry_date');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_trial')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
