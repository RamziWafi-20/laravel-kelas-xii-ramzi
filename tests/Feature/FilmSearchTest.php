<?php

namespace Tests\Feature;

use App\Models\Film;
use App\Models\Genre;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class FilmSearchTest extends TestCase
{
    use DatabaseTransactions;

    public function test_home_search_uses_get_and_filters_films_by_title(): void
    {
        $genre = Genre::query()->firstOrFail();
        $film = Film::create([
            'title' => 'Searchable Film Title',
            'sinopsis' => 'Search test film',
            'year' => 2024,
            'poster' => 'm1.jpg',
            'genre_id' => $genre->id,
        ]);

        $this->get(route('home', ['search' => 'Searchable Film']))
            ->assertOk()
            ->assertSee($film->title)
            ->assertDontSee('No matching film title');
    }

    public function test_search_with_no_results_shows_a_message(): void
    {
        $this->get(route('home', ['search' => 'No matching film title']))
            ->assertOk()
            ->assertSee('Film tidak ditemukan.');
    }

    public function test_search_term_is_preserved_in_pagination_links(): void
    {
        $genre = Genre::query()->firstOrFail();
        foreach (range(1, 19) as $number) {
            Film::create([
                'title' => 'Pagination Search Film ' . $number,
                'sinopsis' => 'Search pagination test film',
                'year' => 2024,
                'poster' => 'm1.jpg',
                'genre_id' => $genre->id,
            ]);
        }

        $this->get(route('home', ['search' => 'Pagination Search Film']))
            ->assertOk()
            ->assertSee('search=Pagination%20Search%20Film&amp;page=2', false);
    }

    public function test_footer_links_use_registered_routes(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('href="' . route('movies') . '">Movies</a>', false)
            ->assertSee('href="' . route('genre', 'action') . '">Action</a>', false)
            ->assertSee('href="' . route('genre', 'adventure') . '">Adventure</a>', false)
            ->assertSee('href="' . route('genre', 'comedy') . '">Comedy</a>', false)
            ->assertSee('href="' . route('faq') . '">FAQ</a>', false);

        $this->get(route('faq'))
            ->assertOk()
            ->assertSee('Frequently Asked Questions');
    }

    public function test_genre_links_filter_case_insensitively_and_unknown_genres_show_empty_state(): void
    {
        $action = Genre::whereRaw('LOWER(name) = ?', ['action'])->firstOrFail();
        $adventure = Genre::whereRaw('LOWER(name) = ?', ['adventure'])->firstOrFail();
        Film::create([
            'title' => 'Footer Action Search Film',
            'sinopsis' => 'Action genre test film',
            'year' => 2025,
            'poster' => 'm1.jpg',
            'genre_id' => $action->id,
        ]);
        Film::create([
            'title' => 'Footer Adventure Search Film',
            'sinopsis' => 'Adventure genre test film',
            'year' => 2025,
            'poster' => 'm1.jpg',
            'genre_id' => $adventure->id,
        ]);

        $this->get(route('genre', 'ACTION'))
            ->assertOk()
            ->assertSee('Footer Action Search Film')
            ->assertDontSee('Footer Adventure Search Film');

        $this->get(route('genre', 'missing-genre'))
            ->assertOk()
            ->assertSee('Film tidak ditemukan.');
    }

    public function test_genre_filter_and_search_are_preserved_in_pagination(): void
    {
        $genre = Genre::whereRaw('LOWER(name) = ?', ['comedy'])->firstOrFail();
        foreach (range(1, 19) as $number) {
            Film::create([
                'title' => 'Footer Genre Pagination ' . $number,
                'sinopsis' => 'Genre search pagination test',
                'year' => 2024,
                'poster' => 'm1.jpg',
                'genre_id' => $genre->id,
            ]);
        }

        $this->get(route('genre', ['genre' => 'comedy', 'search' => 'Footer Genre Pagination']))
            ->assertOk()
            ->assertSee('search=Footer%20Genre%20Pagination&amp;page=2', false);
    }
}
