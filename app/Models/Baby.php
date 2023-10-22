<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Baby extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'umur' => 'string',
    ];

    public function visits(): HasMany
    {
        return $this->hasMany(Visit::class);
    }
}
