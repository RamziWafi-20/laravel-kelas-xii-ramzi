<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Cast;
use Faker\Factory as Faker;

class CastSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $faker = Faker::create('id_ID');
        for ($i = Cast::count(); $i < 50; $i++) {
            Cast::create([
                'name' => $faker->name,
                'age' => $faker->numberBetween(7,80),
                'bio' => $faker->company,
            ]);
        }

    }
}
