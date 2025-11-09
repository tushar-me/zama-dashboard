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
        Schema::create('orders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('order_code')->nullable();
            $table->foreignUuid('customer_id')->nullable()->nullOnDelete();
            $table->string('guest_id')->nullable();
            $table->string('paypal_order_id')->nullable();
            $table->json('payment_details')->nullable();
            $table->foreignUuid('order_status_id')->nullable()->constrained('order_statuses')->nullOnDelete();
            $table->foreignUuid('country_id')->nullable()->constrained('countries')->nullOnDelete();
            $table->foreignUuid('state_id')->nullable()->constrained('states')->nullOnDelete();
            $table->foreignUuid('city_id')->nullable()->constrained('cities')->nullOnDelete();
            $table->string('payment_method')->nullable();
            $table->integer('tax_percentage')->nullable();
            $table->decimal('shipping_charge')->nullable();
            $table->decimal('shipping_warranty')->nullable();
            $table->decimal('rush_production')->nullable();
            $table->decimal('tax')->nullable();
            $table->decimal('vat')->nullable();
            $table->decimal('sub_total')->nullable();
            $table->decimal('grand_total')->nullable();
            $table->enum('payment_status', ['paid', 'pending', 'cancelled'])->default("pending");
            $table->text('status_note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
