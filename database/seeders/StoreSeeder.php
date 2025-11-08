<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class StoreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /**
         * Addning 10k stores records in the database.
         * This is just for testing purpose, you can add records as per your requirement.
         * @use DB::disableQueryLog() to disable query log to improve performance.
         */
        DB::disableQueryLog();
        $faker = Faker::create();
        $vendorIds = \App\Models\Vendor::pluck('id')->toArray();
        $chunkSize = 10;
        foreach(range(1,10) as $i){
            $data = [];
            for($j = 0; $j < $chunkSize; $j++){
                $data[] = [
                    'id' => (string) Str::uuid(),
                    'vendor_id' => $faker->randomElement($vendorIds),
                    'name' => $faker->company,
                    'url' => $faker->slug,
                    'address' => $faker->address,
                    'email' => 'store'. uniqid(). '@gmail.com',
                    'phone' => '01' . rand(10000000, 99999999),
                    'default_domain' => 'store.Tentomart.com',
                    'is_verified' => $faker->randomElement([true, false]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
           DB::table('stores')->insert($data);
        }
    }
}
