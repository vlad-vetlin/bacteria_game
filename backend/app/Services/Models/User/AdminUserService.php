<?php


namespace App\Services\Models\User;


use App\Services\Paginator;
use App\User;
use App\Http\Resources\UserResource;
use Exception;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

class AdminUserService
{
    /**
     * @param User $user
     * @param bool $value
     *
     * @return UserResource
     */
    public function setIsAdmin(User $user, bool $value) : UserResource
    {
        $user->is_admin = $value;
        $user->save();

        return UserResource::make($user);
    }

    /**
     * @param User $user
     *
     * @return array
     *
     * @throws Exception
     */
    public function destroy(User $user) : array
    {
        return ['data' => $user->delete()];
    }
}
