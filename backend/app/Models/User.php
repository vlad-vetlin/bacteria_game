<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

/**
 * Class User
 * @package App
 * @property int $id
 * @property int $rating
 * @property string $first_name
 * @property string $second_name
 * @property string $description
 * @property string $password
 * @property string $country
 * @property string $city
 * @property bool $is_admin
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon $deleted_at
 * @mixin \Eloquent
 */
class User extends Authenticatable
{
    use Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'country',
        'city',
        'email',
        'description',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'is_admin',
    ];

    public const MIN_RATING_VALUE = 0;
    public const MAX_RATING_VALUE = 5000;
    public const START_RATING_VALUE = 1000;

    public function scopeRatingFilter(EloquentBuilder $builder, ?int $from, ?int $to)
    {
        return $builder->whereBetween('rating', [$from ?? static::MIN_RATING_VALUE, $to ?? static::MAX_RATING_VALUE]);
    }

    public function organisms() : HasMany
    {
        return $this->hasMany(Organism::class);
    }
}
