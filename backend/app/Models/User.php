<?php

namespace App;

use Carbon\Carbon;
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
        'rating',
        'first_name',
        'second_name',
        'country',
        'city',
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


    public function scopeRatingFilter(EloquentBuilder $builder, ?int $from, ?int $to)
    {
        return $builder->whereBetween('rating', [$from ?? static::MIN_RATING_VALUE, $to ?? static::MAX_RATING_VALUE]);
    }
}
