<?php

declare(strict_types=1);

namespace Core\Data\BehaviourComponents;

use Core\Data\Factories\CycleDatabaseManagerFactory;
use Core\Data\Factories\CycleOrmFactory;
use Spiral\Core\Container;

class DatabaseCreator
{
    public static function create(): void
    {
        DatabaseCreator::createCycleDatabase();
    }

    public static function createCycleDatabase(): void
    {
        $orm = CycleOrmFactory::createOrm(CycleDatabaseManagerFactory::createDatabase(), new Container());

        $orm->prepareServices();
    }
}