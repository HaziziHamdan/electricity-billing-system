<?php

namespace Database\Seeders;

use App\Enums\BuildingTypeCode;
use App\Models\BuildingType;
use Illuminate\Database\Seeder;

class BuildingTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rows = array_map(
            fn (BuildingTypeCode $type) => [
                'code' => $type->value,
                'name' => $type->label(),
            ],
            BuildingTypeCode::cases(),
        );

        // Safe to run repeatedly: inserts new codes, updates names of existing ones.
        BuildingType::upsert($rows, uniqueBy: ['code'], update: ['name']);
    }
}
