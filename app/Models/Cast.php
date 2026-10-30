<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cast extends Model
{
    use HasFactory;
    protected $table = 'casts';
    protected $fillable = ['name','age', 'bio'];

    public function perans(): HasMany
    {
        return $this->hasMany(Peran::class);
    }
}
