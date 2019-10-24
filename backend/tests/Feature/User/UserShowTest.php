<?php

namespace Tests\Feature;

use App\User;
use Tests\TestCase;

class UserShowTest extends TestCase
{
    /**
     * @param string $key
     *
     * @dataProvider visibleValuesDataProvider
     */
    public function testCheckVisibleValues(string $key)
    {
        $user = factory(User::class)->create();

        $response = $this->get(route('users.show', ['user' => $user->id]))->json();

        self::assertEquals($user->$key, $response['data'][$key]);
    }

    /**
     * @param string $key
     *
     * @dataProvider notVisibleValuesDataProvider
     */
    public function testCheckNotVisibleValues(string $key)
    {
        $user = factory(User::class)->create();

        $response = $this->get(route('users.show', ['user' => $user->id]))->json();

        self::assertArrayNotHasKey($key, $response['data']);
    }

    public function visibleValuesDataProvider()
    {
        return [
            ['id'],
            ['rating'],
            ['first_name'],
            ['last_name'],
            ['city'],
            ['country'],
            ['description'],
        ];
    }

    public function notVisibleValuesDataProvider()
    {
        return [
            ['password'],
            ['is_admin'],
        ];
    }
}
