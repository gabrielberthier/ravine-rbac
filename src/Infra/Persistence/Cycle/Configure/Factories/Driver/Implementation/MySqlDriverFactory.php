<?php

declare(strict_types=1);

namespace RavineRbac\Infra\Persistence\Cycle\Configure\Factories\Driver\Implementation;


use Cycle\Database\Config\MySQLDriverConfig;
use Cycle\Database\Config\DriverConfig;
use RavineRbac\Infra\Persistence\Cycle\Configure\Factories\Connection\Input\ConnectionDefinitions;
use RavineRbac\Infra\Persistence\Cycle\Configure\Factories\Driver\Protocols\AbstractDriverFactory;


class MySqlDriverFactory extends AbstractDriverFactory
{
    function getDriver(
        ConnectionDefinitions $connectionDefinitions,
        array $options
    ): DriverConfig {
        return new MySQLDriverConfig(
            connection: $this->produceConnection($connectionDefinitions),
            timezone: $this->timezone ?? "UTC",
            queryCache: $this->queryCache ?? true,
            readonlySchema: $this->readonlySchema ?? false,
            readonly: $this->readonly ?? false,
            options: $options
        );
    }
}
