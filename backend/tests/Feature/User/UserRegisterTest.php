<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserRegisterTest extends TestCase
{
    public function testRegistrationWithAllData()
    {
        $data = factory(User::class)->make()->toArray();

        $data['password'] = 'secret228';
        $data['password_confirmation'] = 'secret228';

        $this->post(route('register'), $data);

        unset($data['password']);
        unset($data['password_confirmation']);

        $data['rating'] = User::START_RATING_VALUE;
        $data['is_admin'] = false;

        $this->assertDatabaseHas('users', $data);
    }

    public function testCheckAuthAfterLogin()
    {
        $data = factory(User::class)->make()->toArray();

        $data['password'] = 'secret228';
        $data['password_confirmation'] = 'secret228';

        $this->post(route('register'), $data);

        self::assertNotNull(Auth::user());

        Auth::logout();

        self::assertNull(Auth::user());

        $this->post(route('login'), [
            'email' => $data['email'],
            'password' => $data['password'],
        ]);

        self::assertNotNull(Auth::user());
    }

    public function testAuthWithoutDescription()
    {
        $data = factory(User::class)->make()->toArray();

        $data['password'] = 'secret228';
        $data['password_confirmation'] = 'secret228';

        unset($data['description']);

        $this->post(route('register'), $data);

        self::assertCount(1, User::all());
    }

    /**
     * @param string $key
     *
     * @dataProvider requiredFieldsDataProvider
     */
    public function testCheckRequiredFields(string $key)
    {
        $data = factory(User::class)->make()->toArray();

        $data['password'] = 'secret228';
        $data['password_confirmation'] = 'secret228';

        unset($data[$key]);

        $response = $this->postJson(route('register'), $data);

        $this->assertValidationFailed($response, [$key => 'The ' . str_replace('_', ' ', $key) . ' field is required.']);

    }

    public function requiredFieldsDataProvider()
    {
        return [
            ['first_name'],
            ['last_name'],
            ['city'],
            ['country'],
            ['email'],
            ['password'],
        ];
    }

    public function testCheckPasswordConfirmationNotExist()
    {
        $data = factory(User::class)->make()->toArray();

        $data['password'] = 'secret228';

        $response = $this->postJson(route('register'), $data);

        $this->assertValidationFailed($response, ['password' => 'The password confirmation does not match.']);
    }

    public function testCheckPasswordConfirmationNotEqualToPassword()
    {
        $data = factory(User::class)->make()->toArray();

        $data['password'] = 'secret228';
        $data['password_confirmation'] = 'secret2288';

        $response = $this->postJson(route('register'), $data);

        $this->assertValidationFailed($response, ['password' => 'The password confirmation does not match.']);
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

        $data = factory(User::class)->make()->toArray();

        $data['password'] = 'secret228';
        $data[$key] = $value;
        $data['password_confirmation'] = $data['password'];

        $response = $this->postJson(route('register'), $data);

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
            ['password'],
        ];
    }

    public function testCheckInvalidEmail()
    {
        $data = factory(User::class)->make()->toArray();

        $data['password'] = 'secret228';
        $data['password_confirmation'] = 'secret228';

        $data['email'] = 'kek';

        $response = $this->postJson(route('register'), $data);

        $this->assertValidationFailed($response, ['email' => 'The email must be a valid email address.']);
    }

    public function testCheckRegisterUserWithExistEmail()
    {
        $user = factory(User::class)->create();

        $data = $user->toArray();

        $data['password'] = 'secret228';
        $data['password_confirmation'] = 'secret228';

        $response = $this->postJson(route('register'), $data);

        $this->assertValidationFailed($response, ['email' => 'The email has already been taken.']);
    }
}
