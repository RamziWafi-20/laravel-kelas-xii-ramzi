<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFilmRequest;
use App\Http\Requests\UpdateFilmRequest;
use App\Models\Film;
use App\Models\Cast;
use App\Models\Genre;
use App\Models\Kritik;
use App\Models\Peran;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function index(): View
    {
        $comments = Kritik::with([
            'user:id,name',
            'film:id,title',
        ])
            ->select('id', 'comment', 'user_id', 'film_id', 'created_at')
            ->latest()
            ->limit(20)
            ->get();

        return view('admin.dashboard', [
            'comments' => $comments,
            'recentFilms' => Film::with('genre')
                ->latest()
                ->limit(5)
                ->get(),
            'filmCount' => Film::count(),
            'castCount' => Cast::count(),
            'genreCount' => Genre::count(),
            'userCount' => User::count(),
            'commentCount' => Kritik::count(),
        ]);
    }

    public function films(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $films = Film::with('genre')
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('title', 'like', '%' . $search . '%')
                        ->orWhere('year', 'like', '%' . $search . '%');
                });
            })
            ->orderBy('title')
            ->paginate(15)
            ->withQueryString();

        return view('admin.films.index', compact('films', 'search'));
    }

    public function createFilm(): View
    {
        return view('admin.films.form', [
            'film' => new Film(),
            'genres' => Genre::orderBy('name')->get(),
            'formAction' => route('admin.films.store'),
            'formTitle' => 'Tambah Film',
        ]);
    }

    public function storeFilm(StoreFilmRequest $request): RedirectResponse
    {
        Film::create($request->validated());

        return redirect()->route('admin.films.index')->with('status', 'Film berhasil ditambahkan.');
    }

    public function editFilm(Film $film): View
    {
        return view('admin.films.form', [
            'film' => $film,
            'genres' => Genre::orderBy('name')->get(),
            'formAction' => route('admin.films.update', $film),
            'formTitle' => 'Edit Film',
        ]);
    }

    public function updateFilm(UpdateFilmRequest $request, Film $film): RedirectResponse
    {
        $film->update($request->validated());

        return redirect()->route('admin.films.index')->with('status', 'Film berhasil diperbarui.');
    }

    public function destroyFilm(Film $film): RedirectResponse
    {
        DB::transaction(function () use ($film): void {
            $film->perans()->delete();
            $film->kritiks()->delete();
            $film->delete();
        });

        return redirect()->route('admin.films.index')->with('status', 'Film berhasil dihapus.');
    }

    public function destroyComment(Kritik $kritik): RedirectResponse
    {
        $kritik->delete();

        return redirect()->route('admin.comments.index')->with('status', 'Komentar berhasil dihapus.');
    }

    public function comments(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $comments = Kritik::with(['user:id,name', 'film:id,title'])
            ->when($search !== '', function ($query) use ($search): void {
                $query->where('comment', 'like', '%' . $search . '%')
                    ->orWhereHas('user', fn ($userQuery) => $userQuery->where('name', 'like', '%' . $search . '%'))
                    ->orWhereHas('film', fn ($filmQuery) => $filmQuery->where('title', 'like', '%' . $search . '%'));
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.comments.index', compact('comments', 'search'));
    }

    public function users(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $users = User::query()
            ->when($search !== '', function ($query) use ($search): void {
                $query->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            })
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.users.index', compact('users', 'search'));
    }

    public function destroyUser(User $user): RedirectResponse
    {
        if ($user->is(auth()->user())) {
            return back()->withErrors(['user' => 'Anda tidak dapat menghapus akun yang sedang digunakan.']);
        }

        if ($user->role === 'admin' && User::where('role', 'admin')->count() <= 1) {
            return back()->withErrors(['user' => 'Akun admin terakhir tidak dapat dihapus.']);
        }

        DB::transaction(function () use ($user): void {
            $profileId = $user->profile_id;
            $user->kritiks()->delete();
            $user->delete();

            if ($profileId !== null && ! User::where('profile_id', $profileId)->exists()) {
                Profile::whereKey($profileId)->delete();
            }
        });

        return redirect()->route('admin.users.index')->with('status', 'Pengguna dan komentarnya berhasil dihapus.');
    }

    public function casts(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $casts = Cast::with(['perans.film'])
            ->withCount('perans')
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('name', 'like', '%' . $search . '%')
                        ->orWhereHas('perans', function ($roleQuery) use ($search): void {
                            $roleQuery->where('actor', 'like', '%' . $search . '%')
                                ->orWhereHas('film', fn ($filmQuery) => $filmQuery->where('title', 'like', '%' . $search . '%'));
                        });
                });
            })
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.casts.index', compact('casts', 'search'));
    }

    public function createCast(): View
    {
        return view('admin.casts.form', [
            'cast' => new Cast(),
            'formAction' => route('admin.casts.store'),
            'formTitle' => 'Tambah Cast',
        ]);
    }

    public function storeCast(Request $request): RedirectResponse
    {
        Cast::create($request->validate([
            'name' => ['required', 'string', 'max:45'],
            'age' => ['required', 'integer', 'min:1', 'max:150'],
            'bio' => ['required', 'string', 'max:10000'],
        ]));

        return redirect()->route('admin.casts.index')->with('status', 'Cast berhasil ditambahkan.');
    }

    public function editCast(Cast $cast): View
    {
        return view('admin.casts.form', [
            'cast' => $cast,
            'formAction' => route('admin.casts.update', $cast),
            'formTitle' => 'Edit Cast',
        ]);
    }

    public function updateCast(Request $request, Cast $cast): RedirectResponse
    {
        $cast->update($request->validate([
            'name' => ['required', 'string', 'max:45'],
            'age' => ['required', 'integer', 'min:1', 'max:150'],
            'bio' => ['required', 'string', 'max:10000'],
        ]));

        return redirect()->route('admin.casts.index')->with('status', 'Cast berhasil diperbarui.');
    }

    public function destroyCast(Cast $cast): RedirectResponse
    {
        DB::transaction(function () use ($cast): void {
            $cast->perans()->delete();
            $cast->delete();
        });

        return redirect()->route('admin.casts.index')->with('status', 'Cast beserta data actor terkait berhasil dihapus.');
    }

    public function actors(Request $request): RedirectResponse
    {
        return redirect()->route('admin.casts.index', $request->query());
    }

    public function createActor(Request $request): View
    {
        return view('admin.actors.form', [
            'peran' => Peran::make([
                'cast_id' => Cast::whereKey($request->query('cast_id'))->value('id'),
            ]),
            'casts' => Cast::orderBy('name')->get(),
            'films' => Film::orderBy('title')->get(),
            'formAction' => route('admin.actors.store'),
            'formTitle' => 'Tambah Actor / Peran',
        ]);
    }

    public function storeActor(Request $request): RedirectResponse
    {
        Peran::create($request->validate([
            'actor' => ['required', 'string', 'max:255'],
            'cast_id' => ['required', 'integer', 'exists:casts,id'],
            'film_id' => ['required', 'integer', 'exists:films,id'],
        ]));

        return redirect()->route('admin.casts.index')->with('status', 'Actor / peran berhasil ditambahkan.');
    }

    public function editActor(Peran $peran): View
    {
        return view('admin.actors.form', [
            'peran' => $peran,
            'casts' => Cast::orderBy('name')->get(),
            'films' => Film::orderBy('title')->get(),
            'formAction' => route('admin.actors.update', $peran),
            'formTitle' => 'Edit Actor / Peran',
        ]);
    }

    public function updateActor(Request $request, Peran $peran): RedirectResponse
    {
        $peran->update($request->validate([
            'actor' => ['required', 'string', 'max:255'],
            'cast_id' => ['required', 'integer', 'exists:casts,id'],
            'film_id' => ['required', 'integer', 'exists:films,id'],
        ]));

        return redirect()->route('admin.casts.index')->with('status', 'Actor / peran berhasil diperbarui.');
    }

    public function destroyActor(Peran $peran): RedirectResponse
    {
        $peran->delete();

        return redirect()->route('admin.casts.index')->with('status', 'Actor / peran berhasil dihapus.');
    }
}
