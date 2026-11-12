<?php

namespace App\Http\Controllers;

use App\Models\{
    Film,
    Genre,
    Kritik,
};
use App\Http\Requests\StoreFilmRequest;
use App\Http\Requests\UpdateFilmRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;

class FilmController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function movies()
    {
        //
        $genreFilm = null;
        $films = Film::select('id','title', 'poster','year')
                ->orderByDesc('year')
                ->OrderBy('created_at', 'asc')
                ->paginate(18);
        return view('components/movies', compact('films', 'genreFilm'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreFilmRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Film $film)
    {
        //
        $filmByGenre    = Film::select('id','title', 'poster','year', 'sinopsis')
                        ->where('genre_id', '=', $film->genre_id)
                        ->OrderBy('created_at', 'asc')
                        ->limit(7)
                        ->get();
        $filmByRelease  = Film::select('id','title', 'poster','year')
                        ->where('year', '=', Carbon::now()->format('Y'))
                        ->OrderBy('created_at', 'asc')
                        ->limit(9)
                        ->get();
        $comments        = Kritik::with('user:id,name')
                        ->select('id', 'comment', 'user_id', 'created_at')
                        ->where('film_id', '=', $film->id)
                        ->latest()
                        ->limit(10)
                        ->get();
        $perans         = $film->perans()
                        ->with('cast:id,name')
                        ->get(['id', 'film_id', 'cast_id', 'actor']);
        return view('components.movie-show', compact('film','filmByGenre','filmByRelease','comments', 'perans'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Film $film)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFilmRequest $request, Film $film)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Film $film)
    {
        //
    }

    public function movieHome(Request $request)
    {
        $search = trim((string) $request->query('search', ''));

        if ($search !== '') {
            $genreFilm = null;
            $films = Film::select('id', 'title', 'poster', 'year')
                ->where('title', 'like', '%' . $search . '%')
                ->orderByDesc('year')
                ->orderBy('created_at', 'asc')
                ->paginate(18)
                ->withQueryString();

            return view('components/movies', compact('films', 'genreFilm'));
        }

        $films = Film::select('id','title', 'poster','year')
                ->OrderBy('created_at', 'asc')
                ->limit(9)->get();
        $filmFutured = Film::select('id','title', 'poster','year')
                        ->orderByDesc('year')
                        ->OrderBy('created_at', 'asc')
                        ->limit(6)->get();
        $filmRecentAdded = Film::select('id','title', 'poster','year')
                            ->orderByDesc('created_at', 'asc')
                            ->limit(6)->get();
        $filmSlidey = Film::select('title', 'poster','sinopsis')
                        ->orderByDesc('year')
                        ->OrderBy('created_at', 'asc')
                        ->limit(6)->get();
        return view('components/home', compact('filmSlidey','films','filmFutured','filmRecentAdded'));
    }

    public function moviesByGenre(Request $request, string $genre)
    {
        $genreName = str_replace('-', ' ', trim($genre));
        $genreFilm = ctype_digit($genre)
            ? Genre::find($genre)
            : Genre::whereRaw('LOWER(name) = ?', [strtolower($genreName)])->first();

        $search = trim((string) $request->query('search', ''));
        $films = Film::select('id', 'title', 'poster', 'year')
            ->when(
                $genreFilm,
                fn ($query) => $query->where('genre_id', $genreFilm->id),
                fn ($query) => $query->whereRaw('1 = 0')
            )
            ->when($search !== '', fn ($query) => $query->where('title', 'like', '%' . $search . '%'))
            ->orderByDesc('year')
            ->orderBy('created_at', 'asc')
            ->paginate(18)
            ->withQueryString();

        return view('components/movies', compact('films','genreFilm'));
    }
}
