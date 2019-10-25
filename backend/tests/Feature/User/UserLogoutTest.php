<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;
use Tests\UserTestCase;

class UserLogoutTest extends UserTestCase
{
    public function testLogout()
    {
        self::assertNotNull(Auth::user());

        $this->post(route('logout'));

        self::assertNull(Auth::user());
    }

    public function testLogoutWithoutLoginUser()
    {
        Auth::logout();
        self::assertNull(Auth::user());

        $this->post(route('logout'));

        self::assertNull(Auth::user());
    }
}
