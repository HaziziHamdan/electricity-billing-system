<?php

namespace App\Enums;

/** Allow-list of sortable fields on the records listing. */
enum UsageSortField: string
{
    case BuildingType = 'building_type';
    case Usage = 'usage';
}
