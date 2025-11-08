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
        Schema::create('sizes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('created_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->foreignUuid('last_updated_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->string('name')->unique()->index();
            $table->string('image')->nullable();
            $table->boolean('status')->default(true);
            $table->integer('order_level')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sizes');
    }
};
