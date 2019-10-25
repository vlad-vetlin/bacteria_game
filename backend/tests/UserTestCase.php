<?php

namespace Tests;

use App\User;
use Illuminate\Support\Facades\Auth;

abstract class UserTestCase extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $user = factory(User::class)->create();

        Auth::login($user);
    }
}
