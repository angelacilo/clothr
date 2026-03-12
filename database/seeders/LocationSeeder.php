<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LocationSeeder extends Seeder
{
    public function run()
    {
        $phId = DB::table('countries')->insertGetId(['name' => 'Philippines', 'created_at' => now(), 'updated_at' => now()]);
        $usId = DB::table('countries')->insertGetId(['name' => 'United States', 'created_at' => now(), 'updated_at' => now()]);

        // Philippines Regions
        $ncrId = DB::table('regions')->insertGetId(['country_id' => $phId, 'name' => 'Metro Manila', 'created_at' => now(), 'updated_at' => now()]);
        $calId = DB::table('regions')->insertGetId(['country_id' => $phId, 'name' => 'CALABARZON', 'created_at' => now(), 'updated_at' => now()]);

        // NCR Cities
        DB::table('cities')->insert([
            ['region_id' => $ncrId, 'name' => 'Manila City', 'created_at' => now(), 'updated_at' => now()],
            ['region_id' => $ncrId, 'name' => 'Quezon City', 'created_at' => now(), 'updated_at' => now()],
            ['region_id' => $ncrId, 'name' => 'Makati City', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // CALABARZON Cities
        DB::table('cities')->insert([
            ['region_id' => $calId, 'name' => 'Cabuyao City', 'created_at' => now(), 'updated_at' => now()],
            ['region_id' => $calId, 'name' => 'Calamba City', 'created_at' => now(), 'updated_at' => now()],
            ['region_id' => $calId, 'name' => 'Santa Rosa City', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // US States (Regions)
        $caId = DB::table('regions')->insertGetId(['country_id' => $usId, 'name' => 'California', 'created_at' => now(), 'updated_at' => now()]);
        $nyId = DB::table('regions')->insertGetId(['country_id' => $usId, 'name' => 'New York', 'created_at' => now(), 'updated_at' => now()]);

        // US Cities
        DB::table('cities')->insert([
            ['region_id' => $caId, 'name' => 'Los Angeles', 'created_at' => now(), 'updated_at' => now()],
            ['region_id' => $caId, 'name' => 'San Francisco', 'created_at' => now(), 'updated_at' => now()],
            ['region_id' => $nyId, 'name' => 'New York City', 'created_at' => now(), 'updated_at' => now()],
            ['region_id' => $nyId, 'name' => 'Buffalo', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
