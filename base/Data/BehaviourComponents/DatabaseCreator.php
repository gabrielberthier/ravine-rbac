<?php

declare(strict_types=1);

namespace Core\Data\BehaviourComponents;

use Core\Data\Factories\CycleDatabaseManagerFactory;
use Core\Data\Factories\CycleOrmFactory;

class DatabaseCreator
{
    public static function create(): void
    {
        DatabaseCreator::createCycleDatabase();
    }

    public static function createCycleDatabase(): void
    {
        $orm = CycleOrmFactory::createOrm(CycleDatabaseManagerFactory::createDatabase());

        $orm->prepareServices();
    }
}