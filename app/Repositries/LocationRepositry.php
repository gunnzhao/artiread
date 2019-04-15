<?php

namespace App\Repositries;

use App\Location;

class LocationRepositry
{
    /**
     * 获取所有省市信息
     */
    public function getProvincesAndCities()
    {
        $records = Location::where([
            ['leveltype', '>', 0], ['leveltype', '<', 3]
        ])->get();

        $provices = $cities = [];

        foreach ($records as $record) {
            if ($record->leveltype == 1) {
                $provices[$record->id] = [
                    'id' => $record->id,
                    'name' => $record->name
                ];
            } else {
                $cities[$record->parentid][] = [
                    'id' => $record->id,
                    'name' => $record->name
                ];
            }
        }

        return [$provices, $cities];
    }
}
