<?php

declare(strict_types=1);

namespace RavineRbac\Infra\Persistence\Cycle\Configure\Factories\Driver\Protocols;


use Cycle\Database\Config\DriverConfig;
use RavineRbac\Infra\Persistence\Cycle\Configure\Factories\Connection\Input\ConnectionDefinitions;

interface DriverFactoryInterface
{
    function getDriver(
        ConnectionDefinitions $connectionDefinitions,
        array $options
    ): DriverConfig;
}