<?php

namespace App\Enums;

enum BuildingTypeCode: string
{
    case Residential = 'RESIDENTIAL';
    case Commercial = 'COMMERCIAL';

    public function label(): string
    {
        return match ($this) {
            self::Residential => 'Residential',
            self::Commercial => 'Commercial',
        };
    }
}