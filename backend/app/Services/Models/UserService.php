<?php


namespace App\Services\Models;


use App\User;
use App\Http\Resources\UserResource;

class UserService
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

}
