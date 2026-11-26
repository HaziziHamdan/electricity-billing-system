<?php

namespace App\Enums;

enum SortDirection: string
{
    case Asc = 'asc';
    case Desc = 'desc';

    public function opposite(): self
    {
        return $this === self::Asc ? self::Desc : self::Asc;
    }
}
