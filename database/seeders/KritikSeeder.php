<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Kritik;
use App\Models\Film;
use App\Models\User;
use Faker\Factory as Faker;
use RuntimeException;

class KritikSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $filmIds = Film::query()->pluck('id');
        $userIds = User::query()->pluck('id');

        if ($filmIds->isEmpty() || $userIds->isEmpty()) {
            throw new RuntimeException('Seed films and users before seeding comments.');
        }

        $faker = Faker::create('id_ID');
        for ($loop = 1; $loop <= 1000; $loop++) {
            Kritik::create([
                'comment' => $faker->text(maxNbChars: 200),
                'rating' => $faker->numberBetween(1, 5),
                'film_id' => $filmIds->random(),
                'user_id' => $userIds->random(),
            ]);
        }
    }
}
