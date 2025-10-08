<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TruckType;

class TruckTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $truckTypes = [
            ['name' => 'Camión Plataforma', 'description' => 'Camión con plataforma', 'active' => true],
            ['name' => 'Camión Caja', 'description' => 'Camión con caja cerrada', 'active' => true],
            ['name' => 'Camión Refrigerado', 'description' => 'Camión con refrigeración', 'active' => true],
        ];

        foreach ($truckTypes as $type) {
            TruckType::create($type);
        }
    }
}
