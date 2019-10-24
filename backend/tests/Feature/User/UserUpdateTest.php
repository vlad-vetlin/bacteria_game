<?php

namespace Tests\Feature;

use App\User;
use Tests\TestCase;

class UserUpdateTest extends TestCase
{
    /**
     * @param string $key
     * @param $value
     *
     * @dataProvider fieldDataProvider
     */
    public function testUpdateOnlyOneField(string $key, $value)
    {
        $user = factory(User::class)->create([$key => $value]);

        $data = [
            $key => $value,
        ];


        $response = $this->patch(route('users.update', ['user' => $user->id]), $data)->json()['data'];

        self::assertEquals($value, $response[$key]);
    }

    public function fieldDataProvider()
    {
        return [
            ['first_name', 'kek'],
            ['last_name', 'kek'],
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

        $user = factory(User::class)->create();

        $response = $this->patchJson(route('users.update', ['user' => $user->id]), $data);

        $this->assertValidationFailed($response, [$key => 'The ' . str_replace('_', ' ', $key) .' may not be greater than 191 characters.']);
    }

    public function stringFieldsDataProvider()
    {
        return [
            ['first_name'],
            ['last_name'],
            ['city'],
            ['country'],
        ];
    }
}
