<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Support\Facades\Auth;
use Tests\AdminTestCase;
use Tests\TestCase;
use Tests\UserTestCase;

class UserDestroyTest extends AdminTestCase
{
    public function testSimpleDestroy()
    {
        $user = factory(User::class)->create();

        $this->delete(route('users.destroy', ['user' => $user->id]))->json();

        $this->assertModelIsDeleted($user);
    }
}
