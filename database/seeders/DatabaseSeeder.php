<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AdministratorSeeder::class,
            TruckTypeSeeder::class,
            CargoTypeSeeder::class,
            RequiredDocumentSeeder::class,
        ]);
    }
}
