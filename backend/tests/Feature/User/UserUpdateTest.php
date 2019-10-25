<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;
use Tests\UserTestCase;

class UserUpdateTest extends UserTestCase
{
    /**
     * @param string $key
     * @param $value
     *
     * @dataProvider fieldDataProvider
     */
    public function testUpdateOnlyOneField(string $key, $value)
    {
        $data = [
            $key => $value,
        ];

        $this->patch(route('users.self_update'), $data);

        $user = Auth::user();

        self::assertEquals($value, $user->$key);
    }

    public function fieldDataProvider()
    {
        return [
            ['first_name', 'kek'],
            ['last_name', 'kek'],
            ['email', 'kek@kek.kek'],
            ['city', 'kek'],
            ['country', 'kek'],
            ['description', 'kek'],
            ['description', null],
        ];
    }

    /**
     * @param string $key
     *
     * @dataProvider stringFieldsDataProvider
     */
    public function testTooBigFields(string $key)
    {
        $value = '';
        for ($i = 0; $i < 200; ++$i) {
            $value .= 'a';
        }

        $data = [
            $key => $value,
        ];

        $response = $this->patchJson(route('users.self_update'), $data);

        $this->assertValidationFailed($response, [$key => 'The ' . str_replace('_', ' ', $key) .' may not be greater than 191 characters.']);
    }

    public function stringFieldsDataProvider()
    {
        return [
            ['first_name'],
            ['last_name'],
            ['city'],
            ['country'],
            ['email'],
        ];
    }

    public function testCheckInvalidEmail()
    {
        $data = [
            'email' => 'kek',
        ];

        $response = $this->patchJson(route('users.self_update'), $data);

        $this->assertValidationFailed($response, ['email' => 'The email must be a valid email address.']);
    }

    public function testUpdateEmailThatAlreadyExist()
    {
        $second_user = factory(User::class)->create();

        $data = [
            'email' => $second_user->email,
        ];

        $response = $this->patchJson(route('users.self_update'), $data);

        $this->assertValidationFailed($response, ['email' => 'The email has already been taken.']);
    }

    public function testUpdateSelfEmailWithoutAnyDistincts()
    {
        $data = [
            'email' => Auth::user()->email,
        ];

        $response = $this->patchJson(route('users.self_update'), $data);

        self::assertEquals(201, $response->status());
    }
}
