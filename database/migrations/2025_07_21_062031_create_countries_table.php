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
        Schema::create('countries', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('created_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->foreignUuid('last_updated_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->string('name')->index();
            $table->string('code')->unique();
            $table->string('phone_code')->index();
            $table->string('iso2', 2)->nullable();
            $table->string('iso3', 3)->nullable(); 
            $table->string('flag')->nullable(); 
            $table->string('currency', 3)->nullable(); 
            $table->string('currency_symbol', 10)->nullable(); 
            $table->decimal('shipping_charge', 10, 2)->nullable();
            $table->decimal('tax_percentage', 10, 2)->nullable();
            $table->decimal('vat_percentage', 10, 2)->nullable();
            $table->integer('order_level')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('countries');
    }
};
