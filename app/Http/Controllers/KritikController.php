<?php

namespace App\Http\Controllers;

use App\Models\Kritik;
use App\Models\Film;
use App\Http\Requests\StoreKritikRequest;
use App\Http\Requests\UpdateKritikRequest;
use Illuminate\Http\RedirectResponse;

class KritikController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    public function store(StoreKritikRequest $request, Film $film): RedirectResponse
    {
        Kritik::create([
            'comment' => $request->validated('comment'),
            'rating' => 0,
            'film_id' => $film->id,
            'user_id' => $request->user()->id,
        ]);

        return redirect()
            ->route('movies.show', $film)
            ->with('status', 'Komentar berhasil dikirim.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Kritik $kritik)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Kritik $kritik)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateKritikRequest $request, Kritik $kritik)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Kritik $kritik)
    {
        //
    }
}
