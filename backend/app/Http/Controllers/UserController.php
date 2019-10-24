<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Services\Models\UserService;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    public function __construct()
    {
        $this->service = new UserService();
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     *
     * @return AnonymousResourceCollection
     */
    public function index(Request $request) : AnonymousResourceCollection
    {
        $data = $request->validate([
            'filters' => 'array|min:1',

            'filters.rating' => 'array|min:1',
            'filters.rating.from' => 'integer|min:'  . User::MIN_RATING_VALUE . '|max:' . User::MAX_RATING_VALUE,
            'filters.rating.to' => 'integer|min:' . User::MIN_RATING_VALUE . '|max:' . User::MAX_RATING_VALUE,

            'filters.is_admin' => 'boolean',

            'filters.city' => 'string|max:191',
            'filters.country' => 'string|max:191',

            'query' => 'string|nullable',

            'pagination' => 'array|min:1',

            'pagination.page' => 'integer|min:1',
            'pagination.per_page' => 'integer|min:1',

            'sort' => 'array',

            'sort.by' => 'string|in:rating,city,country,first_name,last_name',
            'sort.dir' => 'string|in:ASC,DESC',
        ]);

        if (($data['filters']['rating']['to'] ?? User::MAX_RATING_VALUE) < ($data['filters']['rating']['from'] ?? User::MIN_RATING_VALUE)) {
            throw ValidationException::withMessages([
                'filters.rating.to' => 'to field should be greater or equal than from field.',
            ]);
        }

        return $this->service->index($data);
    }

    /**
     * Display the specified resource.
     *
     * @param  User $user
     *
     * @return UserResource
     */
    public function show(User $user)
    {
        return $this->service->show($user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  User $user
     *
     * @return UserResource
     */
    public function update(Request $request, User $user) : UserResource
    {
        $data = $request->validate([
            'first_name' => 'string|max:191',
            'last_name' => 'string|max:191',
            'country' => 'string|max:191',
            'city' => 'string|max:191',
            'description' => 'string|nullable',
        ]);

        return $this->service->update($data, $user);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
