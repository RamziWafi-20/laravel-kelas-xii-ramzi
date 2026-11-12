<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Peran extends Model
{
    use HasFactory;
    protected $table = 'perans';
    protected $fillable = ['actor', 'film_id', 'cast_id'];

    public function film(): BelongsTo
    {
        return $this->belongsTo(Film::class);
    }

    public function cast(): BelongsTo
    {
        return $this->belongsTo(Cast::class);
    }
}
