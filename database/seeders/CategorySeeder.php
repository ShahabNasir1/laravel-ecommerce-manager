<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\category; // Tumhara lowercase model

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Factory ko call kiya aur bola 2,000 records banao
        category::factory()->count(2000)->create();
    }
}