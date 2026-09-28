<?php

namespace Database\Seeders;

use App\Models\IFEStatus;
use Illuminate\Database\Seeder;

class IFEStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $statuses = [
            [
                'name' => 'New Customer',
                'value' => 'New Customer'
            ],
            [
                'name' => 'Existing Customer',
                'value' => 'Existing Customer'
            ],
            [
                'name' => 'Previous Yes, Now No',
                'value' => 'Previous Yes, Now No',
            ],
            [
                'name' => 'New Inquiry',
                'value' => 'New Inquiry'
            ],
        ];

        foreach ($statuses as $status) {
            IFEStatus::firstOrCreate(
                ['name' => $status['name']],
                ['value' => $status['value']]
            );
        }
    }

}
