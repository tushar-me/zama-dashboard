<?php

namespace Database\Seeders;

use App\Models\Admin;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Admin::factory()->create([
            'name' => 'Tushar Imran',
            'email' => 'tusharimran@gmail.com',
            'phone' => '01648550599',
            'password' => Hash::make('tusharimran'),
        ]);
    }
}
