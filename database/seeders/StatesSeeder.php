<?php

namespace Database\Seeders;

use App\Models\States;
use Illuminate\Database\Seeder;

class StatesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $states = collect([
            [
                'code' => "JHR",
                'name' => "Johor",
            ],
            [
                'code' => "KDH",
                'name' => "Kedah",
            ],
            [
                'code' => "KTN",
                'name' => "Kelantan",
            ],
            [
                'code' => "MLK",
                'name' => "Melaka",
            ],
            [
                'code' => "NSN",
                'name' => "Negeri Sembilan",
            ],
            [
                'code' => "PHG",
                'name' => "Pahang",
            ],
            [
                'code' => "PNG",
                'name' => "Penang",
            ],
            [
                'code' => "PRK",
                'name' => "Perak",
            ],
            [
                'code' => "PLS",
                'name' => "Perlis",
            ],
            [
                'code' => "SBH",
                'name' => "Sabah",
            ],
            [
                'code' => "SRW",
                'name' => "Sarawak",
            ],
            [
                'code' => "SGR",
                'name' => "Selangor",
            ],
            [
                'code' => "TRG",
                'name' => "Terengganu",
            ],
            [
                'code' => "KUL",
                'name' => "Kuala Lumpur",
            ],
            [
                'code' => "LBN",
                'name' => "WP Labuan",
            ],
            [
                'code' => "PJY",
                'name' => "WP Putrajaya",
            ],
        ]);

        $states->each(function ($state) {
            States::firstOrCreate([
                'code' => $state['code'],
                'name' => $state['name'],
            ]);
        });
    }
}
