<?php

namespace Database\Seeders;

use App\Models\Address;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AddressFakerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('en_IN');

        foreach (range(1, 50) as $index) {
            // Generate unique pincode
            $pincode = $faker->unique()->postcode;

            // Create address record
            $data = new Address;
            $data->pincode  = $pincode;
            $data->city     = $faker->city;
            $data->state    = $faker->state;
            $data->country  = 'India';
            $data->save();
        }
    }
}
