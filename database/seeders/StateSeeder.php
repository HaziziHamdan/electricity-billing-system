<?php

namespace Database\Seeders;

use App\Models\State;
use Illuminate\Database\Seeder;

class StateSeeder extends Seeder
{
    /** 13 states + 3 federal territories. */
    private const STATES = [
        'JHR' => 'Johor',
        'KDH' => 'Kedah',
        'KTN' => 'Kelantan',
        'MLK' => 'Melaka',
        'NSN' => 'Negeri Sembilan',
        'PHG' => 'Pahang',
        'PNG' => 'Pulau Pinang',
        'PRK' => 'Perak',
        'PLS' => 'Perlis',
        'SBH' => 'Sabah',
        'SWK' => 'Sarawak',
        'SGR' => 'Selangor',
        'TRG' => 'Terengganu',
        'KUL' => 'W.P. Kuala Lumpur',
        'LBN' => 'W.P. Labuan',
        'PJY' => 'W.P. Putrajaya',
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rows = [];

        foreach (self::STATES as $code => $name) {
            $rows[] = ['code' => $code, 'name' => $name];
        }

        State::upsert($rows, uniqueBy: ['code'], update: ['name']);
    }
}
