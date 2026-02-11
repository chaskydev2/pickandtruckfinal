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
                'name' => 'Copia del carnet de identidad del representante legal',
                'description' => 'Carnet de identidad del representante legal de la empresa',
                'notes' => 'Debe estar firmado en el centro. Formato PDF o imagen. Ambos lados.',
                'active' => true
            ],
            [
                'name' => 'NIT (certificado de inscripción)',
                'description' => 'Número de Identificación Tributaria - Certificado de inscripción',
                'notes' => 'Documento emitido por impuestos. Formato PDF o imagen legible.',
                'active' => true
            ],
            [
                'name' => 'NIT (documento de exhibición)',
                'description' => 'Número de Identificación Tributaria - Documento de exhibición',
                'notes' => 'Formato PDF o imagen legible.',
                'active' => true
            ],
            [
                'name' => 'Matrícula de Comercio (SEPREC)',
                'description' => 'Matrícula de comercio emitida por SEPREC',
                'notes' => 'Debe estar vigente. Formato PDF preferentemente.',
                'active' => true
            ],
            [
                'name' => 'Certificado de operador de comercio exterior / Licencia de operador de carga',
                'description' => 'Certificado de operador de comercio exterior o Licencia de operador de carga',
                'notes' => 'Según corresponda al tipo de empresa. Formato PDF o imagen.',
                'active' => true
            ],
            [
                'name' => 'Carta de aceptación',
                'description' => 'Carta de aceptación de términos y condiciones',
                'notes' => 'Descargue la plantilla desde el botón "Descargar Plantilla", imprima en hoja membretada de su empresa, complete con sus datos, firme, escanee y suba el archivo en formato PDF.',
                'active' => true
            ]
        ];

        foreach ($documents as $document) {
            RequiredDocument::create($document);
        }
    }
}
