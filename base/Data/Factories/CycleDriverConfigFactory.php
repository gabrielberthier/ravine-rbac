<?php

declare(strict_types=1);

namespace Core\Data\Factories;

use Cycle\Database\Config\DriverConfig;
use Core\Data\Cycle\Facade\ConnectorFacade;
use function Core\functions\inTesting;

class CycleDriverConfigFactory
{
    public static function create(
        array $connectionOptions = [],
        array $driverOptions = []
    ): ?DriverConfig {
        if (!inTesting()) {
            $connectorFacade = new ConnectorFacade(
                connection: ConnectionFactory::getConnectionSettings(),
                connectionOptions: $connectionOptions
            );

            // Configure connector as you wish
            $connectorFacade
                ->configureFactory()
                ->withQueryCache(true)
                ->withSchema("public");

            return $connectorFacade->produceDriverConnection(
                driverOptions: $driverOptions
            );
        }

        return null;
    }
}