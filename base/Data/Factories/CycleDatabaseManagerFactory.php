<?php

declare(strict_types=1);

namespace Core\Data\Factories;


use Core\Data\Factories\ConnectionFactory;
use Cycle\Database\Config;
use Cycle\Database\DatabaseManager;
use Cycle\Database\Config\DatabaseConfig;
use function RavineRbac\functions\inTesting;

final class CycleDatabaseManagerFactory
{
    public static function createDatabase(): DatabaseManager
    {
        return new DatabaseManager(
            new DatabaseConfig([
                "default" => "default",
                "databases" => [
                    "default" => [
                        "connection" => inTesting() ? "sqlite" : "production",
                    ],
                ],
                "connections" => [
                    "sqlite" => new Config\SQLiteDriverConfig(
                        connection: new Config\SQLite\MemoryConnectionConfig(),
                        queryCache: true
                    ),
                    "production" => CycleDriverConfigFactory::create(),
                ],
            ])
        );
    }
}