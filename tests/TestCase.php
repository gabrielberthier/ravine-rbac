<?php

declare(strict_types=1);

namespace Tests;

use Core\Data\BehaviourComponents\DatabaseCleaner;
use Core\Data\BehaviourComponents\DatabaseCreator;
use PHPUnit\Framework\TestCase as PHPUnit_TestCase;
use Tests\Traits\App\AppTestTrait;
use Tests\Traits\App\DoublesTrait;
use Tests\Traits\App\ErrorHandlerTrait;
use Tests\Traits\App\InstanceManagerTrait;
use Tests\Traits\App\RequestManagerTrait;

/**
 * @internal
 * @coversNothing
 */
class TestCase extends PHPUnit_TestCase
{
    use AppTestTrait;
    use DoublesTrait;
    use ErrorHandlerTrait;
    use RequestManagerTrait;

    public static function createDatabase()
    {
        DatabaseCreator::create();
    }

    final public static function truncateDatabase()
    {
        DatabaseCleaner::truncate();
    }

    public static function createDatabaseDoctrine()
    {
        DatabaseCreator::createDoctrineDatabase();
    }

    final public static function truncateDatabaseDoctrine()
    {
        DatabaseCleaner::truncateDoctrineDatabase();
    }


}