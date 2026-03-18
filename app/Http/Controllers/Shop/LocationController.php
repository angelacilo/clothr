<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Region;
use App\Models\City;

class LocationController extends Controller
{
    public function getCountries()
    {
        return response()->json(Country::all());
    }

    public function getRegions($country_id)
    {
        return response()->json(Region::where('country_id', $country_id)->get());
    }

    public function getCities($region_id)
    {
        return response()->json(City::where('region_id', $region_id)->get());
    }
}
