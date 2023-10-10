<?php

declare(strict_types=1);

namespace Core\Data\Factories;

use Cycle\Database\Config\DriverConfig;
use Core\Data\Cycle\Facade\ConnectorFacade;
use function Core\functions\inTesting;
use function Core\functions\mode;

final class ConnectionFactory
{
    public static function getConnectionSettings(): array
    {
        return [
            'connection' => static function (): array {
                $exceptionMessage = 'An application mode should be specified at project level .env or _ENV' .
                    'variable containing one of the following values: PRODUCTION, TEST or DEV';
                $connectionArray = [];

                if (isset($_ENV['DATABASE_URL'])) {
                    $connectionArray['url'] = $_ENV['DATABASE_URL'];
                } else {
                    $dbParams = ['DRIVER', 'HOST', 'DBNAME', 'PORT', 'USER', 'PASSWORD', 'CHARSET'];
                    foreach ($dbParams as $param) {
                        $connectionArray[$param] = $_ENV[$param];
                    }
                }

                return match (mode()) {
                    'TEST' => [
                        'driver' => 'pdo_sqlite',
                        'memory' => 'true',
                    ],
                    'PRODUCTION', 'DEV' => $connectionArray,
                    default => throw new \Exception($exceptionMessage, 500)
                };
            }
        ];
    }

    public static function getConnection(): ?DriverConfig
    {
        if (!inTesting()) {
            $connectorFacade = new ConnectorFacade(
                connection: self::getConnectionSettings(),
                connectionOptions: []
            );

            // Configure connector as you wish
            $connectorFacade
                ->configureFactory()
                ->withQueryCache(true)
                ->withSchema("public");

            return $connectorFacade->produceDriverConnection(
                driverOptions: []
            );
        }

        return null;
    }
}