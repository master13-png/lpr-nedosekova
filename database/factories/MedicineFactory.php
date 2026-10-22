<?php

namespace Database\Factories;

use App\Models\Manufacturer;
use App\Models\Medicine;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Medicine>
 */
class MedicineFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'manufacturer_id' => Manufacturer::factory(),
            'code' => fake()->unique()->regexify('[A-Z]{2}-[0-9]{4}'),
            'name' => fake()->words(2, true),
            'form' => fake()->randomElement([
                'Таблетки',
                'Капсулы',
                'Сироп',
                'Раствор',
                'Спрей',
                'Мазь',
                'Крем',
                'Капли',
            ]),
            'dosage_mg' => fake()->randomElement([5, 10, 20, 50, 100, 250, 500, 1000]),
            'price' => fake()->randomFloat(2, 50, 25000),
            'by_prescription' => fake()->boolean(30),
        ];
    }
}
