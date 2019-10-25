<?php

namespace Tests;

use App\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Auth;

abstract class AdminTestCase extends BaseTestCase
{
    public function setUp(): void
    {
        $user = factory(User::class)->create(['is_admin' => true]);

        Auth::login($user);
    }
}
