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
        Schema::create('products', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('category_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->unsignedBigInteger('brand_id')->nullable();
            $table->foreignUuid('created_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->foreignUuid('last_updated_by')->nullable()->constrained('admins')->nullOnDelete();
            //Information
            $table->string('code');
            $table->string('name');
            $table->string('slug');
            $table->string('artwork')->nullable();
            $table->string('cover_image')->nullable();
            $table->string('hover_image')->nullable();
            $table->string('measurement_guide')->nullable();
            $table->text('short_description')->nullable();
            $table->longText('description')->nullable();
            $table->json('specification')->nullable();
            $table->text('video_url')->nullable();
            // Pricing & Inventory
            $table->decimal('price', 10, 2)->default(0.00);
            $table->decimal('compare_price', 10, 2)->nullable();
            $table->decimal('unit_price', 10, 2)->nullable();
            $table->decimal('profit_amount')->nullable();
            $table->decimal('cost', 10, 2)->nullable();
            $table->decimal('tax', 10, 2)->nullable();
            $table->boolean('is_tax_included')->default(true);
            $table->integer('inventory_quantity')->default(0);
            $table->enum('inventory_policy', ['deny', 'continue'])->default('deny');
            $table->decimal('weight', 8, 2)->nullable();
            $table->json('dimensions')->nullable();
            // SEO
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->text('meta_keywords')->nullable();
            $table->string('og_image')->nullable();
            $table->string('og_title')->nullable();
            $table->text('og_description')->nullable();
            $table->string('canonical_url')->nullable();
            $table->string('alt_text')->nullable();
            $table->json('extra_meta')->nullable();
            
            //campaign stats
            $table->integer('sale')->default(0);
            $table->integer('view')->default(0);
            $table->integer('likes')->default(0);
    
            // Status & publishing
            $table->enum('status', ['published','unpublished','resctricted'])->default('unpublished'); 
            $table->timestamp('published_at')->nullable();
            $table->integer('order_number')->nullable();
            // Categorization
            $table->json('tags')->nullable();
            $table->boolean('is_deleted')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
