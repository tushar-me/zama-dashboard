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
        Schema::create('shipping_charges', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuidMorphs('chargeable');
            $table->foreignUuid('created_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->foreignUuid('last_updated_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->decimal('us_charge', 10, 2)->default(0.00);
            $table->decimal('us_add_charge_per_item', 10, 2)->default(0.00);
            $table->decimal('worldwide_charge',10, 2)->default(0.00);
            $table->decimal('worldwide_add_charge_per_item', 10, 2)->default(0.00);
            $table->boolean('status')->default(true);
            $table->boolean('is_free')->default(false);
            $table->integer('order_level')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mockup_shipping_charges');
    }
};
