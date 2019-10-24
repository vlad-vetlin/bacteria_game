<?php

namespace Tests;

use Cache;
use DB;
use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\TestResponse;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use ReflectionObject;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use MockeryPHPUnitIntegration;

    protected function truncateDB(Connection $connection)
    {
        $tables = array_filter($connection->getDoctrineSchemaManager()->listTableNames(), function($name) {
            return $name !== 'migrations';
        });

        $connection->statement("TRUNCATE " . implode(',', $tables) . " RESTART IDENTITY");
    }

    protected function setUp() : void
    {
        parent::setUp();

        $this->truncateDB(DB::connection());

        Cache::flush();
    }

    protected function tearDown() : void
    {
        $this->getConnection()->disconnect();

        $refl = new ReflectionObject($this);
        foreach ($refl->getProperties() as $prop) {
            if (!$prop->isStatic() && 0 !== strpos($prop->getDeclaringClass()->getName(), 'PHPUnit_')) {
                $prop->setAccessible(true);
                $prop->setValue($this, null);
            }
        }

        parent::tearDown();
    }

    public function assertModelIsDeleted(Model $model)
    {
        $model = $model->fresh();

        if (method_exists($model, "getDeletedAtColumn")) {
            $column = $model->getDeletedAtColumn();
            $this->assertNotNull($model->$column, "Failed asserting that model is soft deleted");
        } else {
            $this->assertTrue(is_null($model), "Failed asserting that model is deleted");
        }
    }

    public function assertValidationFailed(TestResponse $response, array $messages)
    {
        self::assertEquals(422, $response->status());

        foreach ($messages as $field => $message) {
            self::assertTrue(in_array($message, $response->json()['errors'][$field]),
                "Failed asserting that field " . $field . ' has validation error');
        }
    }
}
