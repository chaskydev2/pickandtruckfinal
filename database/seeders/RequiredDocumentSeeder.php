<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RequiredDocument;

class RequiredDocumentSeeder extends Seeder
{
    public function run(): void
    {
        $documents = [
            [
                'name' => 'DNI',
                'description' => 'Documento Nacional de Identidad',
                'notes' => 'Ambos lados, formato PDF o imagen',
                'active' => true
            ],
            [
                'name' => 'Licencia de Conducir',
                'description' => 'Licencia de conducir vigente',
                'notes' => 'Debe estar vigente al momento de la carga',
                'active' => true
            ],
            [
                'name' => 'SOAT',
                'description' => 'Seguro Obligatorio de Accidentes de Tránsito',
                'notes' => 'Vigente y legible',
                'active' => true
            ],
            [
                'name' => 'Tarjeta de Propiedad',
                'description' => 'Tarjeta de propiedad del vehículo',
                'notes' => 'Documento original',
                'active' => true
            ]
        ];

        foreach ($documents as $document) {
            RequiredDocument::create($document);
        }
    }
}
