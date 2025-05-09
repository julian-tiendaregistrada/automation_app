<?php

namespace Database\Seeders;

use App\Enums\AreasEnum;
use App\Models\Area;
use Illuminate\Database\Seeder;

class AreaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (AreasEnum::cases() as $areaEnum) {
            Area::firstOrCreate([
                'id' => $areaEnum->id(),
                'name' => $areaEnum->value,
            ]);
        }
    }
}
