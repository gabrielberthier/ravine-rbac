<?php

declare(strict_types=1);

namespace RavineRbac\Infra\Persistence\Cycle\Configure\Factories\Static;

use Cycle\Database\Config\DriverConfig;

use RavineRbac\Infra\Persistence\Cycle\Configure\Facade\ConnectorFacade;
use function RavineRbac\functions\inTesting;

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