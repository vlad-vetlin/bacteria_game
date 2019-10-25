<?php

namespace Tests;

use App\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Auth;

abstract class AdminTestCase extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $user = factory(User::class)->create(['is_admin' => true]);

        Auth::login($user);
    }
}
