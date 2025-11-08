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
        Schema::create('customers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('password')->nullable();
            $table->string('otp')->nullable();
            $table->string('otp_expires_at')->nullable();
            $table->unsignedBigInteger('guest_id')->nullable();
            $table->foreignUuid('state_id')->nullable()->constrained('states')->nullOnDelete();
            $table->foreignUuid('country_id')->nullable()->constrained('countries')->nullOnDelete();
            $table->foreignUuid('city_id')->nullable()->constrained('cities')->nullOnDelete();
            $table->string('province')->nullable();
            $table->string('street_address')->nullable();
            $table->string('address_line_2')->nullable();
            $table->string('post_code')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
