<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Vendor>
 */
class VendorFactory extends Factory
{
    protected static ?string $password;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => (string) Str::uuid(),
            'region_id' => $this->faker->numberBetween(1, 100),
            'state' => $this->faker->state,
            'city' => $this->faker->city,
            'address_line_1' => $this->faker->streetAddress,
            'address_line_2' => $this->faker->optional()->secondaryAddress,
            'name' => $this->faker->company,
            'email' => $this->faker->unique()->safeEmail,
            'phone' => $this->faker->unique()->phoneNumber,
            'email_verified_at' => $this->faker->optional()->dateTime,
            'phone_verified_at' => $this->faker->optional()->dateTime,
            'password' => Hash::make('password'), 
            'is_active' => $this->faker->boolean(90),
            'is_suspended' => $this->faker->boolean(10),
            'remember_token' => Str::random(10),
        ];
    }
}
