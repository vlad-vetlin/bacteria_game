<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class UserLoginTest extends TestCase
{
    public function testLogin()
    {
        $user = factory(User::class)->create();

        self::assertNull(Auth::user());

        $data = [
            'email' => $user->email,
            'password' => 'secret'
        ];

        $this->post(route('login'), $data);

        self::assertNotNull(Auth::user());
    }

    public function testInvalidEmail()
    {
        factory(User::class)->create();

        self::assertNull(Auth::user());

        $data = [
            'email' => 'kek',
            'password' => 'secret'
        ];

        $response = $this->postJson(route('login'), $data);

        $this->assertValidationFailed($response, ['email' => 'These credentials do not match our records.']);
    }

    public function testInvalidPassword()
    {
        $user = factory(User::class)->create();

        self::assertNull(Auth::user());

        $data = [
            'email' => $user->email,
            'password' => 'kek'
        ];

        $response = $this->postJson(route('login'), $data);

        $this->assertValidationFailed($response, ['email' => 'These credentials do not match our records.']);
    }
}
