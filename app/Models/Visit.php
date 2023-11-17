<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Visit extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'subform1' => 'json',
    ];

    public function baby(): BelongsTo
    {
        return $this->belongsTo(Baby::class);
    }
}
