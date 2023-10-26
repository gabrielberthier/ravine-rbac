<?php

declare(strict_types=1);

namespace RavineRbac\Infra\Persistence\Cycle\Configure\Facade;


use Cycle\Database\Config\DriverConfig;
use Exception;
use RavineRbac\Infra\Persistence\Cycle\Configure\Factories\Connection\Implementation as ConnectionImplementation;
use RavineRbac\Infra\Persistence\Cycle\Configure\Factories\Driver\Implementation as DriverImplementation;
use RavineRbac\Infra\Persistence\Cycle\Configure\Factories\Connection\Input\ConnectionDefinitions;
use RavineRbac\Infra\Persistence\Cycle\Configure\Factories\Driver\Protocols;
use RavineRbac\Infra\Persistence\Cycle\Configure\Factories\Driver\Protocols\AbstractDriverFactory;

class ConnectorFacade
{
    private ?AbstractDriverFactory $driverFactory = null;
    private string $driver;

    private ConnectionDefinitions $connectionDefinitions;

    public function __construct(private array $connection, array $connectionOptions = [])
    {
        /** @var string */
        $this->driver = $this->prepareDriverSelection($connection);
        $this->connectionDefinitions = $this->createInput($connection, $connectionOptions);
    }

    public function produceDriverConnection(
        array $driverOptions = [],
    ): DriverConfig {
        return $this
            ->getFactory()
            ->getDriver(
                $this->connectionDefinitions,
                $driverOptions
            );
    }

    public function configureFactory(): Protocols\ConfigurableDriverInterface
    {
        return $this->getFactory();
    }

    private function getFactory(): Protocols\AbstractDriverFactory
    {
        if ($this->driverFactory === null) {
            $this->startFactory();
        }
        return $this->driverFactory;
    }

    private function prepareDriverSelection(array $connection): string
    {
        return $connection["DRIVER"] ?? explode("://", $connection["url"])[0];
    }

    private function startFactory()
    {
        $this->driverFactory = match ($this->driver) {
            "postgres",
            "postgresql",
            "pg",
            "pdo_pgsql",
            "pgsql"
            => new DriverImplementation\PostresDriverFactory(new ConnectionImplementation\PostgresConfig()),

            "mysql",
            "pdo_mysql",
            "mysqli"
            => new DriverImplementation\MySqlDriverFactory(new ConnectionImplementation\MySqlConfig()),

            default => throw new Exception("Driver selection is not correct"),
        };
    }

    private function createInput(
        array $connection,
        array $options
    ): ConnectionDefinitions {
        $db = $connection["DBNAME"] ?? null;
        $port = $connection["PORT"] ?? null;
        $user = $connection["USER"] ?? null;
        $password = $connection["PASSWORD"] ?? null;
        $host = $connection["HOST"] ?? null;
        $url = $connection["url"] ?? null;

        return new ConnectionDefinitions(
            db: $db,
            port: $port,
            user: $user,
            password: $password,
            host: $host,
            url: $url,
            options: $options
        );
    }
}