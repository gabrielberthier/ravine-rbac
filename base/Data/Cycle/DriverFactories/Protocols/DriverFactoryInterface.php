<?php

declare(strict_types=1);

namespace Core\Data\Cycle\DriverFactories\Protocols;

use Core\Data\Cycle\ConnectionFactories\Input\ConnectionDefinitions;
use Cycle\Database\Config\DriverConfig;

interface DriverFactoryInterface
{
    function getDriver(
        ConnectionDefinitions $connectionDefinitions,
        array $options
    ): DriverConfig;
}