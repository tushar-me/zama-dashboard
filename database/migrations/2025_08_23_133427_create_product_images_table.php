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
        Schema::create('product_images', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('product_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignUuid('campaign_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignUuid('product_side_id')->nullable()->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignUuid('product_color_id')->nullable()->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('side')->nullable();
            $table->string('color_code')->nullable();
            $table->string('mockup')->nullable();
            $table->string('image')->nullable();
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_images');
    }
};
