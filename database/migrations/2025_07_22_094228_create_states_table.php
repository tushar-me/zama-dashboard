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
        Schema::create('states', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('last_updated_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->foreignUuid('created_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->foreignUuid('country_id')->constrained('countries')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('name');
            $table->string('code')->unique();
            $table->decimal('shipping_charge', 10, 2)->nullable();
            $table->decimal('tax_percentage', 10, 2)->nullable();
            $table->decimal('vat_percentage', 10, 2)->nullable();
            $table->string('capital')->nullable(); 
            $table->string('timezone')->nullable(); 
            $table->string('iso_code')->nullable(); 
            $table->string('currency')->nullable(); 
            $table->boolean('is_default')->default(false);
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
        Schema::dropIfExists('states');
    }
};
