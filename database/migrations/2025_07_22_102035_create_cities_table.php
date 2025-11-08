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
        Schema::create('cities', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->foreignUuid('state_id')->constrained('states')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignUuid('country_id')->constrained('countries')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignUuid('created_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->foreignUuid('last_updated_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->string('code')->unique();
            $table->string('postal_code')->nullable();
            $table->string('timezone')->nullable();
            $table->string('area_code')->nullable();
            $table->decimal('shipping_charge', 10, 2)->nullable();
            $table->decimal('tax_percentage', 10, 2)->nullable();
            $table->decimal('vat_percentage', 10, 2)->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->boolean('is_default')->default(false);
            $table->integer('order_level')->nullable(0); 
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cities');
    }
};
