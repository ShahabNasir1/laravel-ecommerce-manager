<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\category>
 */
class CategoryFactory extends Factory
{
    // Zaroori Note: Agar model ka naam lowercase 'category' hai, toh yahan manually map karo
    protected $model = \App\Models\category::class;

    public function definition(): array
    {
        return [
            // words(2, true) se 2 lafzon ka string banega, aur unique() ke sath safety rahegi
            'category_name' => $this->faker->unique()->words(2, true), 
            
            'category_status' => $this->faker->randomElement(['active', 'inactive']),
        ];
    }
}