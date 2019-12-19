<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Organism extends Model
{
    use SoftDeletes;

    public $fillable = [
        'name',
        'description',
        'text',
    ];

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
