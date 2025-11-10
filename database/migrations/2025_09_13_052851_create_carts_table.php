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
        Schema::create('carts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('customer_id')->nullable()->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignUuid('category_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignUuid('product_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignUuid('product_color_id')->nullable()->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignUuid('product_variation_id')->nullable()->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('guest_id')->nullable();
            $table->string('product_image')->nullable();
            $table->decimal('quantity')->default(1);
            $table->decimal('total')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};
