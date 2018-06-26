<?php

namespace App\Http\Controllers;

use App\Helpers\GeoData;

class GeoDataController extends Controller
{
    public function test()
    {
        $gd = new GeoData;

        return $gd->getGeoData('1256 Warren Road, Lakewood Ohio 44107');
    }
}
