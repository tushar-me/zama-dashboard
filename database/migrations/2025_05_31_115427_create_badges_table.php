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
        Schema::create('badges', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('created_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->foreignUuid('last_updated_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->string('name');
            $table->string('logo')->nullable();
            $table->enum('type', ['percentage', 'fixed'])->nullable();
            $table->enum('adjustment_type', ['add', 'substract'])->nullable();
            $table->decimal('adjustment_value')->default(0);
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
        Schema::dropIfExists('badges');
    }
};
