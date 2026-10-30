<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    AdminController,
    AuthController,
    FilmController,
    KritikController,
};
use Illuminate\Http\Request;

Route::get('/login', function (Request $request) {
    $redirectTo = $request->query('redirect_to');

    if (is_string($redirectTo) && preg_match('#^/movies/\d+$#D', $redirectTo)) {
        $request->session()->put('url.intended', url($redirectTo));
    } elseif (preg_match('#^/movies/(\d+)/comments$#D', parse_url((string) $request->session()->get('url.intended'), PHP_URL_PATH) ?: '', $matches)) {
        $request->session()->put('url.intended', route('movies.show', $matches[1]));
    }

    return redirect()->route('home')->with('auth_form', 'login');
})->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.store');
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');
Route::post('/movies/{film}/comments', [KritikController::class, 'store'])
    ->middleware('auth')
    ->name('comments.store');

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('dashboard');
    Route::get('/films', [AdminController::class, 'films'])->name('films.index');
    Route::get('/films/create', [AdminController::class, 'createFilm'])->name('films.create');
    Route::post('/films', [AdminController::class, 'storeFilm'])->name('films.store');
    Route::get('/films/{film}/edit', [AdminController::class, 'editFilm'])->name('films.edit');
    Route::put('/films/{film}', [AdminController::class, 'updateFilm'])->name('films.update');
    Route::delete('/films/{film}', [AdminController::class, 'destroyFilm'])->name('films.destroy');
    Route::get('/comments', [AdminController::class, 'comments'])->name('comments.index');
    Route::delete('/comments/{kritik}', [AdminController::class, 'destroyComment'])->name('comments.destroy');
    Route::get('/users', [AdminController::class, 'users'])->name('users.index');
    Route::delete('/users/{user}', [AdminController::class, 'destroyUser'])->name('users.destroy');
    Route::get('/casts', [AdminController::class, 'casts'])->name('casts.index');
    Route::get('/casts/create', [AdminController::class, 'createCast'])->name('casts.create');
    Route::post('/casts', [AdminController::class, 'storeCast'])->name('casts.store');
    Route::get('/casts/{cast}/edit', [AdminController::class, 'editCast'])->name('casts.edit');
    Route::put('/casts/{cast}', [AdminController::class, 'updateCast'])->name('casts.update');
    Route::delete('/casts/{cast}', [AdminController::class, 'destroyCast'])->name('casts.destroy');
    Route::get('/actors', [AdminController::class, 'actors'])->name('actors.index');
    Route::get('/actors/create', [AdminController::class, 'createActor'])->name('actors.create');
    Route::post('/actors', [AdminController::class, 'storeActor'])->name('actors.store');
    Route::get('/actors/{peran}/edit', [AdminController::class, 'editActor'])->name('actors.edit');
    Route::put('/actors/{peran}', [AdminController::class, 'updateActor'])->name('actors.update');
    Route::delete('/actors/{peran}', [AdminController::class, 'destroyActor'])->name('actors.destroy');
});

Route::get('/', [FilmController::class, 'movieHome'])->name('home');
Route::get('/movies', [FilmController::class, 'movies'])->name('movies');
Route::get('/movies/{film}', [FilmController::class, 'show'])->name('movies.show');
Route::get('/movies/genre/{genre}', [FilmController::class, 'moviesByGenre'])->name('genre');
Route::view('/faq', 'components.faq')->name('faq');
