<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Services\Models\User\ClientUserService;
use App\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ClientUserController extends Controller
{
    public function __construct()
    {
        $this->service = new ClientUserService();
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
     *
     * @return UserResource
     */
    public function selfUpdate(Request $request) : UserResource
    {
        /** @var User $user */
        $user = Auth::user();

        $data = $request->validate([
            'first_name' => 'string|max:191',
            'last_name' => 'string|max:191',
            'country' => 'string|max:191',
            'email' => [
               'string',
               'max:191',
               'email',
               Rule::unique('users', 'email')
                   ->whereNull('deleted_at')
                   ->whereNot('id', $user->id),
            ],
            'city' => 'string|max:191',
            'description' => 'string|nullable',
        ]);


        return $this->service->update($data, $user);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return array
     *
     * @throws Exception
     */
    public function selfDestroy() : array
    {
        /** @var User $user */
        $user = Auth::user();

        return $this->service->destroy($user);
    }

    /**
     * @return array
     */
    public function getCities() : array
    {
        return $this->service->getCities();
    }

    /**
     * @return array
     */
    public function getCountries() : array
    {
        return $this->service->getCountries();
    }
}
