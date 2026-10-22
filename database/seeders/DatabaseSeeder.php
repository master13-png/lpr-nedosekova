<?php

namespace Database\Seeders;

use App\Models\Manufacturer;
use App\Models\Medicine;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $manufacturers = Manufacturer::factory()
            ->count(5)
            ->sequence(
                ['name' => 'Борисовский завод медицинских препаратов', 'country' => 'Беларусь'],
                ['name' => 'KRKA', 'country' => 'Словения'],
                ['name' => 'Sandoz', 'country' => 'Швейцария'],
                ['name' => 'Berlin-Chemie', 'country' => 'Германия'],
                ['name' => 'Фармстандарт', 'country' => 'Россия'],
            )
            ->create();

        $medicines = [
            ['Парацетамол', 'Таблетки', 500, 450, false],
            ['Ибупрофен', 'Таблетки', 200, 650, false],
            ['Амоксициллин', 'Капсулы', 500, 1800, true],
            ['Цетиризин', 'Таблетки', 10, 900, false],
            ['Омепразол', 'Капсулы', 20, 1100, false],
            ['Лоратадин', 'Таблетки', 10, 750, false],
            ['Метформин', 'Таблетки', 500, 1350, true],
            ['Аспирин', 'Таблетки', 500, 520, false],
            ['Дротаверин', 'Таблетки', 40, 800, false],
            ['Аскорбиновая кислота', 'Таблетки', 100, 380, false],
            ['Ксилометазолин', 'Спрей', 1, 700, false],
            ['Амброксол', 'Сироп', 30, 950, false],
            ['Панкреатин', 'Таблетки', 250, 600, false],
            ['Амлодипин', 'Таблетки', 5, 1200, true],
            ['Эналаприл', 'Таблетки', 10, 850, true],
            ['Нимесулид', 'Порошок', 100, 1450, true],
            ['Азитромицин', 'Капсулы', 500, 2100, true],
            ['Хлоргексидин', 'Раствор', 1, 500, false],
            ['Диклофенак', 'Гель', 10, 1300, false],
            ['Левотироксин', 'Таблетки', 50, 1600, true],
            ['Лоперамид', 'Капсулы', 2, 400, false],
            ['Декспантенол', 'Мазь', 50, 1700, false],
            ['Фурацилин', 'Таблетки', 20, 350, false],
            ['Хлоропирамин', 'Таблетки', 25, 900, true],
            ['Амилметакрезол', 'Таблетки', 1, 1250, false],
            ['Дексаметазон', 'Раствор', 4, 1050, true],
        ];

        foreach ($medicines as $index => [$name, $form, $dosageMg, $price, $byPrescription]) {
            Medicine::factory()
                ->recycle($manufacturers)
                ->create([
                    'code' => sprintf('MD-%04d', $index + 1),
                    'name' => $name,
                    'form' => $form,
                    'dosage_mg' => $dosageMg,
                    'price' => $price,
                    'by_prescription' => $byPrescription,
                ]);
        }
    }
}
