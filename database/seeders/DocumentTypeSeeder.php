<?php

namespace Database\Seeders;

use App\Enums\DocumentTypesEnum;
use App\Models\DocumentType;
use Illuminate\Database\Seeder;

class DocumentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (DocumentTypesEnum::cases() as $docType) {
            DocumentType::firstOrCreate([
                'id' => $docType->id(),
                'name' => $docType->value,
            ]);
        }

    }
}
