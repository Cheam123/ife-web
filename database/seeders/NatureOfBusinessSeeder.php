<?php

namespace Database\Seeders;

use App\Models\IFENatureOfBusiness;
use Illuminate\Database\Seeder;

class NatureOfBusinessSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $natureBusiness = [
            ['name' => 'AGENT', 'value' => 'Agent'],
            ['name' => 'Bar/Pub/Bistro', 'value' => 'Bar/Pub/Bistro'],
            ['name' => 'BAKERY', 'value' => 'Bakery'],
            ['name' => 'CAFE', 'value' => 'Cafe'],
            ['name' => 'CAFE/REST', 'value' => 'Cafe & Restaurant'],
            ['name' => 'CHAIN-INTERNATIONAL', 'value' => 'International Chain'],
            ['name' => 'CHAIN-LOCAL', 'value' => 'Local Chain'],
            ['name' => 'DEALER', 'value' => 'Dealer'],
            ['name' => 'EVENT/EXHIBITION', 'value' => 'Event/Exhibition'],
            ['name' => 'HOME', 'value' => 'Home/End User'],
            ['name' => 'HOTEL', 'value' => 'Hotel'],
            ['name' => 'IMPORTER', 'value' => 'Importer'],
            ['name' => 'INSTITUTION', 'value' => 'Institution'],
            ['name' => 'KIOSK', 'value' => 'Kiosk'],
            ['name' => 'OFFICE', 'value' => 'Office'],
            ['name' => 'OTHER', 'value' => 'Other'],
            ['name' => 'PARTNER', 'value' => 'Partner'],
            ['name' => 'RESTAURANT', 'value' => 'RESTAURANT'],
            ['name' => 'RETAIL', 'value' => 'Retail'],
            ['name' => 'STOCKIST', 'value' => 'Stockist'],
            ['name' => 'TRUCK', 'value' => 'Truck'],
            ['name' => 'DISTRIBUTOR', 'value' => 'DISTRIBUTOR'],
            ['name' => 'WHOLESALER', 'value' => 'WHOLESALER'],
            ['name' => 'BAR', 'value' => 'BAR'],
        ];

        // foreach ($natureBusiness as $business) {
        //     DB::table('nature_of_business')->updateOrInsert(
        //         ['name' => $business['name']],
        //         ['value' => $business['value']]
        //     );
        // }

        
        foreach($natureBusiness as $business) {
            IFENatureOfBusiness::firstOrCreate(
                ['name' => $business['name']],
                ['value' => $business['value']]
            );
        }
    }
}
