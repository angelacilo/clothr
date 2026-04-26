<?php

/**
 * FILE: Shop/LocationController.php
 * 
 * What this file does:
 * This controller provides geographic data (Countries, Regions, Cities) 
 * for the address forms. It allows the website to show dependent dropdowns.
 * For example, when you pick a Country, it fetches the Regions for that country.
 * 
 * How it connects to the project:
 * - It is called via JavaScript (AJAX) from the Address and Checkout pages.
 * - It uses the Country, Region, and City models.
 * - It returns JSON data (simple lists) for the browser to read.
 */

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Region;
use App\Models\City;

class LocationController extends Controller
{
    /**
     * Gets the list of all Countries.
     * 
     * @return json — list of countries
     */
    public function getCountries()
    {
        // Fetch all rows from the countries table and return as JSON.
        return response()->json(Country::all());
    }

    /**
     * Gets the list of Regions for a specific Country.
     * 
     * @param int $country_id — the ID of the selected country
     * @return json — list of regions
     */
    public function getRegions($country_id)
    {
        // Search the regions table for all rows matching the country_id.
        return response()->json(Region::where('country_id', $country_id)->get());
    }

    /**
     * Gets the list of Cities for a specific Region.
     * 
     * @param int $region_id — the ID of the selected region
     * @return json — list of cities
     */
    public function getCities($region_id)
    {
        // Search the cities table for all rows matching the region_id.
        return response()->json(City::where('region_id', $region_id)->get());
    }
}
