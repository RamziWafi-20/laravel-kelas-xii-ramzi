<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Cast;
use App\Models\Film;
use App\Models\Peran;
use Faker\Factory as Faker;
use RuntimeException;

class PeranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $films = Film::query()->pluck('id');
        $castIds = Cast::query()->pluck('id');

        if ($films->isEmpty() || $castIds->isEmpty()) {
            throw new RuntimeException('Seed films and casts before seeding film roles.');
        }

        $faker = Faker::create('id_ID');
        $existingPerans = Peran::query()->get(['film_id', 'cast_id'])->groupBy('film_id');

        foreach ($films as $filmId) {
            $assignedCastIds = $existingPerans->get($filmId, collect())->pluck('cast_id');
            $availableCastIds = $castIds->diff($assignedCastIds);
            $rolesToCreate = max(0, 3 - $assignedCastIds->count());

            foreach ($availableCastIds->shuffle()->take($rolesToCreate) as $castId) {
                Peran::create([
                    'film_id' => $filmId,
                    'cast_id' => $castId,
                    'actor' => $faker->jobTitle,
                ]);
            }
        }
    }
}
