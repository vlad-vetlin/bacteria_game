<?php

namespace Tests\Feature;

use App\User;
use Tests\TestCase;

class UserSetIsAdminTest extends TestCase
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
}
