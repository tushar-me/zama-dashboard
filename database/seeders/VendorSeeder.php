<?php

namespace Database\Seeders;

use App\Models\Vendor;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class VendorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /**
         * Addning 10k vendor records in the database.
         * This is just for testing purpose, you can add records as per your requirement.
         * @use DB::disableQueryLog() to disable query log to improve performance.
         */
        DB::disableQueryLog();
        $faker = Faker::create();
        $chunkSize = 10;
        $passwordHash = bcrypt('password');
        foreach(range(1,10) as $i){
            $data = [];
            for($j = 0; $j < $chunkSize; $j++){
                $data[] = [
                    'id' => (string) Str::uuid(),
                    'region_id' => rand(1, 100),
                    'state' => $faker->state,
                    'city' => $faker->city,
                    'address_line_1' => $faker->streetAddress,
                    'address_line_2' => $faker->optional()->secondaryAddress,
                    'name' => $faker->company,
                    'email' => 'vendor'. uniqid(). '@gmail.com',
                    'phone' => '01' . rand(10000000, 99999999),
                    'email_verified_at' => now(),
                    'phone_verified_at' => now(),
                    'password' => $passwordHash,
                    'is_active' => true,
                    'is_suspended' => false,
                    'remember_token' => Str::random(10),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
           DB::table('vendors')->insert($data);
        }
    }
}