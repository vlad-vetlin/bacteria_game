<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Support\Facades\Auth;
use Tests\AdminTestCase;

class UserDestroyTest extends AdminTestCase
{
    public function testSimpleDestroy()
    {
        $user = factory(User::class)->create();

        $this->delete(route('users.destroy', ['user' => $user->id]))->json();

        $this->assertModelIsDeleted($user);
    }

    public function testDestroyNotAuth()
    {
        Auth::logout();

        $user = factory(User::class)->create();

        $response = $this->deleteJson(route('users.destroy', ['user' => $user->id]));

        self::assertEquals(403, $response->status());
    }

    public function testDestroyNotAdmin()
    {
        /** @var User $auth_user */
        $auth_user = Auth::user();
        $auth_user->is_admin = false;
        $auth_user->save();

        $user = factory(User::class)->create();

        $response = $this->deleteJson(route('users.destroy', ['user' => $user->id]));

        self::assertEquals(403, $response->status());
    }
}
