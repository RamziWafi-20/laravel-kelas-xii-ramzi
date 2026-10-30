<?php

namespace Tests\Feature;

use App\Models\Cast;
use App\Models\Film;
use App\Models\Genre;
use App\Models\Peran;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class FilmCastTest extends TestCase
{
    use DatabaseTransactions;

    public function test_film_detail_displays_cast_names_and_character_roles_without_n_plus_one_queries(): void
    {
        $film = Film::query()->firstOrFail();
        $cast = Cast::create([
            'name' => 'Film Detail Test Cast',
            'age' => 30,
            'bio' => 'Test cast biography',
        ]);
        Peran::create([
            'film_id' => $film->id,
            'cast_id' => $cast->id,
            'actor' => 'Test Character',
        ]);
        $queries = [];
        DB::listen(function (QueryExecuted $query) use (&$queries): void {
            $queries[] = strtolower($query->sql);
        });

        $response = $this->get(route('movies.show', $film));

        $response->assertOk()
            ->assertSee('Film Detail Test Cast')
            ->assertSee('Test Character');
        $this->assertCount(1, array_filter($queries, fn (string $sql): bool => str_contains($sql, 'from `perans`')));
        $this->assertCount(1, array_filter($queries, fn (string $sql): bool => str_contains($sql, 'from `casts`')));
    }

    public function test_film_detail_shows_a_message_when_no_cast_is_available(): void
    {
        $film = Film::create([
            'title' => 'Film Without Cast',
            'sinopsis' => 'A film without cast data',
            'year' => 2025,
            'poster' => 'storage/images/m1.jpg',
            'genre_id' => Genre::query()->value('id'),
        ]);

        $this->get(route('movies.show', $film))
            ->assertOk()
            ->assertSee('Data cast belum tersedia');
    }
}
