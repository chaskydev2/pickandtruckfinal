<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CargoType;

class CargoTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cargoTypes = [
            ['name' => 'Carga General', 'description' => 'Carga general'],
            ['name' => 'Productos Perecederos', 'description' => 'Productos perecederos'],
            ['name' => 'Materiales Peligrosos', 'description' => 'Materiales peligrosos'],
        ];

        foreach ($cargoTypes as $type) {
            CargoType::create($type);
        }
    }
}
