<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Support\Arr;
use Tests\TestCase;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

class UserIndexTest extends TestCase
{
    protected function getValidAnswers(EloquentCollection $collection, callable $callback) : array
    {
        $response = collect();
        foreach ($collection as $item) {
            if ($callback($item)) {
                $response->push($item);
            }
        }

        return $response->sortBy('id')->pluck('id')->toArray();
    }

    public function setUp(): void
    {
        parent::setUp();

        factory(User::class, 10)->create();
    }

    public function testWithoutData()
    {
        $response = $this->get(route('users.index'))->json()['data'];

        self::assertCount(10, $response);
    }

    /**
     * @param $from
     * @param $to
     * @param string[] $validation_error_messages
     *
     * @dataProvider ratingFilterDataProvider
     */
    public function testRatingFilter($from, $to, ?array $validation_error_messages = null)
    {
        factory(User::class, 20)->create();
        factory(User::class, 20)->create(['is_admin' => true]);

        $data = [
            'filters' => [
                'rating' => [
                    'from' => $from,
                    'to' => $to,
                ]
            ],
        ];

        $response = $this->getJson(route('users.index', $data));

        if (!is_null($validation_error_messages)) {
            $this->assertValidationFailed($response, $validation_error_messages);

            return;
        }

        $response = $response->json()['data'];

        $from = $from ?? User::MIN_RATING_VALUE;
        $to = $to ?? User::MAX_RATING_VALUE;

        $valid_answer = $this->getValidAnswers(User::all(), function (User $user) use ($from, $to) {
            return $user->rating >= $from && $user->rating <= $to;
        });

        $response = Arr::pluck($response, 'id');
        sort($response);

        self::assertEquals($valid_answer, $response);
    }

    public function ratingFilterDataProvider()
    {
        return [
            [null, null],
            [null, User::MAX_RATING_VALUE],
            [User::MIN_RATING_VALUE, null],
            [User::MIN_RATING_VALUE, User::MAX_RATING_VALUE],
            [1, 1],
            [1, 3],
            [null, 100],
            [100, null],
            [100, 200],
            [200, 1, ['filters.rating.to' => 'to field should be greater or equal than from field.']],
            [User::MIN_RATING_VALUE - 1, User::MIN_RATING_VALUE + 1, ['filters.rating.from' => 'The filters.rating.from must be at least 0.']],
            [User::MIN_RATING_VALUE, User::MIN_RATING_VALUE],
            [User::MAX_RATING_VALUE, User::MAX_RATING_VALUE],
            [User::MAX_RATING_VALUE - 1, User::MAX_RATING_VALUE + 1, ['filters.rating.to' => 'The filters.rating.to may not be greater than 5000.']],
            [User::MAX_RATING_VALUE + 1, User::MAX_RATING_VALUE + 1, [
                'filters.rating.from' => 'The filters.rating.from may not be greater than 5000.',
                'filters.rating.to' => 'The filters.rating.to may not be greater than 5000.'
            ]],
            [User::MIN_RATING_VALUE - 1, User::MIN_RATING_VALUE - 1, [
                'filters.rating.from' => 'The filters.rating.from must be at least 0.',
                'filters.rating.to' => 'The filters.rating.to must be at least 0.'
            ]],
            [1.5, 100, ['filters.rating.from' => 'The filters.rating.from must be an integer.']],
            [1, 100.5, ['filters.rating.to' => 'The filters.rating.to must be an integer.']],
        ];
    }

    public function testFilterIsAdminTrue()
    {
        $data = [
            'filters' => [
                'is_admin' => true,
            ]
        ];

        factory(User::class, 5)->create(['is_admin' => true]);

        $response = $this->getJson(route('users.index', $data))->json()['data'];

        self::assertCount(5, $response);
    }

    public function testFilterIsAdminFalse()
    {
        $data = [
            'filters' => [
                'is_admin' => false,
            ]
        ];

        factory(User::class, 5)->create(['is_admin' => true]);

        $response = $this->getJson(route('users.index', $data))->json()['data'];

        self::assertCount(10, $response);
    }

    public function testFilterIsAdminNotBoolean()
    {
        $data = [
            'filters' => [
                'is_admin' => 10,
            ]
        ];

        factory(User::class, 5)->create(['is_admin' => true]);

        $response = $this->getJson(route('users.index', $data));

        self::assertValidationFailed($response, ['filters.is_admin' => 'The filters.is admin field must be true or false.']);
    }

    /**
     * @param string $key
     * @param string $value
     *
     * @dataProvider identicalFiltersDataProvider
     */
    public function testIdenticalFilters(string $key)
    {
        factory(User::class)->create([$key => 'Kek']);

        $data = [
            'filters' => [
                $key => 'Kek',
            ]
        ];

        $response = $this->getJson(route('users.index', $data))->json()['data'];

        $valid_answers = $this->getValidAnswers(User::all(), function (User $user) use ($key) {
            return $user->$key === 'Kek';
        });

        $response = Arr::pluck($response, 'id');
        sort($response);

        self::assertEquals($valid_answers, $response);
    }

    public function identicalFiltersDataProvider()
    {
        return [
            ['city'],
            ['country'],
        ];
    }

    /**
     * @param string $key
     *
     * @dataProvider identicalFiltersDataProvider
     */
    public function testIdenticalFilterIsTooBig(string $key)
    {
        $value = "";
        for ($i = 0; $i < 200; ++$i) {
            $value .= 'a';
        }

        $data = [
            'filters' => [
                $key => $value,
            ]
        ];

        $response = $this->getJson(route('users.index', $data));

        $this->assertValidationFailed($response, ['filters.' . $key => 'The filters.' . $key . ' may not be greater than 191 characters.']);
    }

    /**
     * @param string $key
     *
     * @dataProvider querySearchedValuesDataProvider
     */
    public function testSearchByQuery(string $key)
    {
        $user = factory(User::class)->create([$key => 'ТеСт']);

        $data = [
            'query' => 'теСТ',
        ];

        $response = $this->getJson(route('users.index', $data))->json()['data'];

        self::assertCount(1, $response);
        self::assertEquals($user->id, $response[0]['id']);
    }

    public function querySearchedValuesDataProvider()
    {
        return [
            ['first_name'],
            ['last_name'],
            ['description'],
        ];
    }

    public function testQueryIsTooBig()
    {
        $value = "";
        for ($i = 0; $i < 200; ++$i) {
            $value .= 'a';
        }

        $user = factory(User::class)->create(['description' => $value]);

        $data = [
            'query' => $value,
        ];

        $response = $this->getJson(route('users.index', $data))->json()['data'];

        self::assertCount(1, $response);
        self::assertEquals($user->id, $response[0]['id']);
    }

    public function testPagination()
    {
        $data = [
            'pagination' => [
                'page' => 2,
                'per_page' => 2,
            ]
        ];

        $response = $this->getJson(route('users.index', $data))->json();

        self::assertCount(2, $response['data']);
        self::assertEquals(10, $response['meta']['total']);
        self::assertEquals(2, $response['meta']['per_page']);
        self::assertEquals(2, $response['meta']['current_page']);
        self::assertEquals(5, $response['meta']['last_page']);
    }

    /**
     * @param string $field
     * @param string $dir
     * @param array|null $validation_error_messages
     *
     * @dataProvider sortFieldsDataProvider
     */
    public function testSort(?string $field, ?string $dir, ?array $validation_error_messages = null)
    {
        $data = [
            'sort' => [
                'by' => $field,
                'dir' => $dir,
            ]
        ];

        $response = $this->getJson(route('users.index', $data));

        if (!is_null($validation_error_messages)) {
            $this->assertValidationFailed($response, $validation_error_messages);

            return;
        }

        $response = $response->json()['data'];

        $valid_answer = User::orderBy($field ?? 'id', $dir ?? 'ASC')->pluck('id')->toArray();

        $response = Arr::pluck($response, 'id');

        self::assertEquals($valid_answer, $response);
    }

    public function sortFieldsDataProvider()
    {
        return [
            ['rating', null],

            [null, 'ASC'],
            [null, 'DESC'],

            ['rating', 'ASC'],
            ['rating', 'DESC'],

            ['city', 'ASC'],
            ['city', 'DESC'],

            ['country', 'ASC'],
            ['country', 'DESC'],

            ['first_name', 'ASC'],
            ['first_name', 'DESC'],

            ['last_name', 'ASC'],
            ['last_name', 'DESC'],

            ['kek', 'ASC', ['sort.by' => 'The selected sort.by is invalid.']],
            ['rating', 'kek', ['sort.dir' => 'The selected sort.dir is invalid.']],
        ];
    }
}
