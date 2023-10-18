<?php

declare(strict_types=1);

namespace RavineRbac\Infra\Persistence\Cycle\BehaviourComponents;


use RavineRbac\Infra\Persistence\Cycle\Configure\Factories\Static\CycleDatabaseManagerFactory;
use RavineRbac\Infra\Persistence\Cycle\Configure\Factories\Static\CycleOrmFactory;
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