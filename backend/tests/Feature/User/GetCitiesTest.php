<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;
use Tests\UserTestCase;

class GetCitiesTest extends TestCase
{
    public function testSimpleGetCities()
    {
        factory(User::class, 10)->create();

        $response = $this->get(route('users.cities'))->json()['data'];

        self::assertCount(10, $response);

        $answer = [];
        foreach (User::all() as $user) {
            $answer[] = $user->city;
        }

        sort($answer);

        self::assertEquals($answer, $response);
    }
}
