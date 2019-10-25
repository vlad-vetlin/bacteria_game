<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Support\Facades\Auth;
use Tests\AdminTestCase;

class UserSetIsAdminTest extends AdminTestCase
{

    /**
     * @param $value
     * @param $start_value
     *
     * @dataProvider isAdminValuesProvider
     */
    public function testSetIsAdmin($value, $start_value)
    {
        $user = factory(User::class)->create(['is_admin' => $start_value]);

        $data = [
            'is_admin' => $value,
        ];

        $this->patch(route('users.set_is_admin', ['user' => $user->id]), $data)->json();

        $user->refresh();

        self::assertEquals($value, $user->is_admin);
    }

    public function isAdminValuesProvider()
    {
        return [
            [true, true],
            [true, false],
            [false, true],
            [false, false],
        ];
    }

    public function testSetIsAdminNotAuth()
    {
        $user = factory(User::class)->create(['is_admin' => false]);

        Auth::logout();

        $data = [
            'is_admin' => true,
        ];

        $response = $this->patchJson(route('users.set_is_admin', ['user' => $user->id]), $data);

        self::assertEquals(403, $response->status());
    }

    public function testSetIsAdminNotAdmin()
    {
        $user = factory(User::class)->create(['is_admin' => false]);

        /** @var User $auth_user */
        $auth_user = Auth::user();
        $auth_user->is_admin = false;
        $auth_user->save();

        $data = [
            'is_admin' => true,
        ];

        $response = $this->patchJson(route('users.set_is_admin', ['user' => $user->id]), $data);

        self::assertEquals(403, $response->status());
    }
}
