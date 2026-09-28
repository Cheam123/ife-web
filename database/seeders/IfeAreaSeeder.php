<?php

namespace Database\Seeders;

use App\Models\IfeArea;
use Illuminate\Database\Seeder;

/**
 * Starter IFE areas so the area dropdowns are not empty on a fresh install.
 * Admins rename, add and remove them under Admin > IFE Areas.
 */
class IfeAreaSeeder extends Seeder
{
    public function run()
    {
        $areas = [
            'Kuala Lumpur'   => 'KL city centre and surrounding districts',
            'Petaling Jaya'  => 'PJ, Damansara and Mutiara Damansara',
            'Subang Jaya'    => 'Subang Jaya, USJ and Puchong',
            'Shah Alam'      => 'Shah Alam and Klang',
            'Cheras'         => 'Cheras, Kajang and Bangi',
            'Ampang'         => 'Ampang, Setapak and Wangsa Maju',
            'Penang'         => 'Penang island and Seberang Perai',
            'Johor Bahru'    => 'Johor Bahru and Iskandar Puteri',
        ];

        foreach ($areas as $area => $description) {
            IfeArea::firstOrCreate(['area' => $area], ['description' => $description]);
        }
    }
}
