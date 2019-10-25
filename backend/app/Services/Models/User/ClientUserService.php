<?php


namespace App\Services\Models\User;


use App\Services\Paginator;
use App\User;
use App\Http\Resources\UserResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

class ClientUserService
{
    /**
     * @param User $user
     *
     * @return UserResource
     */
    public function show(User $user)
    {
        return UserResource::make($user);
    }

    /**
     * @param array $data
     *
     * @return AnonymousResourceCollection
     */
    public function index(array $data) : AnonymousResourceCollection
    {
        $by = $data['sort']['by'] ?? 'id';
        $dir = $data['sort']['dir'] ?? 'ASC';

        $builder = User::query()->when(Arr::has($data, 'filters'), function (EloquentBuilder $builder) use ($data) {
            $filters = $data['filters'];

            $builder->ratingFilter($filters['rating']['from'] ?? null, $filters['rating']['to'] ?? null);

            $builder->when(Arr::has($filters, 'is_admin'), function (EloquentBuilder $builder) use ($filters) {
                $builder->where('is_admin', $filters['is_admin']);
            });

            $builder->when(Arr::has($filters, 'country'), function (EloquentBuilder $builder) use ($filters) {
                $builder->where('country', $filters['country']);
            });

            $builder->when(Arr::has($filters, 'city'), function (EloquentBuilder $builder) use ($filters) {
                $builder->where('city', $filters['city']);
            });
        })->when(Arr::has($data, 'query') && !is_null($data['query']), function (EloquentBuilder $builder) use ($data) {
            $query = $data['query'];

            $builder->where(function (EloquentBuilder $builder) use ($query) {
                $builder->where('first_name', 'ILIKE', "%$query%")
                        ->orWhere('last_name', 'ILIKE', "%$query%")
                        ->orWhere('description', 'ILIKE', "%$query%");
            });
        })->when(Arr::has($data, 'sort'), function (EloquentBuilder $builder) use ($data) {
            $by = $data['sort']['by'] ?? 'id';
            $dir = $data['sort']['dir'] ?? 'ASC';

            $builder->orderBy($by, $dir);
        });

        if (Arr::has($data, 'pagination')) {
            $response = Paginator::paginateIfNeeded($builder);
        } else {
            $response = $builder->get();
        }

        return UserResource::collection($response);
    }

    public function update(array $data, User $user) : UserResource
    {
        $user->update($data);

        return UserResource::make($user);
    }
}
