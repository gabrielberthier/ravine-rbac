<?php

declare(strict_types=1);

namespace RavineRbac\Infra\Persistence\Cycle\Configure\Factories\Driver\Implementation;

use Cycle\Database\Config\DriverConfig;
use Cycle\Database\Config\PostgresDriverConfig;
use RavineRbac\Infra\Persistence\Cycle\Configure\Factories\Connection\Input\ConnectionDefinitions;
use RavineRbac\Infra\Persistence\Cycle\Configure\Factories\Driver\Protocols\AbstractDriverFactory;

class PostresDriverFactory extends AbstractDriverFactory
{
    function getDriver(
        ConnectionDefinitions $connectionDefinitions,
        array $options
    ): DriverConfig {
        return new PostgresDriverConfig(
            connection: $this->produceConnection($connectionDefinitions),
            timezone: $this->timezone ?? "UTC",
            schema: $this->schema ?? "public",
            queryCache: $this->queryCache ?? true,
            readonlySchema: $this->readonlySchema ?? false,
            readonly: $this->readonly ?? false,
            options: $options
        );
    }
}