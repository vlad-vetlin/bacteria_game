<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;
use Tests\UserTestCase;

class GetCountriesTest extends TestCase
{
    public function testSimpleGetCountries()
    {
        factory(User::class, 10)->create();

        $response = $this->get(route('users.countries'))->json()['data'];

        self::assertCount(10, $response);

        $answer = [];
        foreach (User::all() as $user) {
            $answer[] = $user->country;
        }

        sort($answer);

        self::assertEquals($answer, $response);
    }
}
