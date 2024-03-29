<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Support\Facades\Auth;
use Tests\UserTestCase;

class UserSelfDestroyTest extends UserTestCase
{
    public function testDestroy()
    {
        $this->delete(route('users.self_destroy'))->json();

        self::assertNull(Auth::user());
        self::assertCount(0, User::all());
    }

    public function testSelfDestroyNotAuth()
    {
        Auth::logout();

        $response = $this->deleteJson(route('users.self_destroy'));

        self::assertEquals(403, $response->status());
    }
}
