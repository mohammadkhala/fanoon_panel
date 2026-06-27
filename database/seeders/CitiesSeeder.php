<?php

namespace Database\Seeders;

use App\Helpers\CitiesHelper;
use App\Models\City;
use Illuminate\Database\Seeder;

class CitiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $cities = CitiesHelper::getCities();
        $sortOrder = 0;
        foreach ($cities as $name) {
            City::firstOrCreate(
                ['name' => $name],
                ['sort_order' => $sortOrder++]
            );
        }
    }
}
