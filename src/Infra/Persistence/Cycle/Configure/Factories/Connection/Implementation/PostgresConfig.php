<?php

declare(strict_types=1);

namespace RavineRbac\Infra\Persistence\Cycle\Configure\Factories\Connection\Implementation;

use Cycle\Database\Config;
use Cycle\Database\Config\Postgres\ConnectionConfig;
use RavineRbac\Infra\Persistence\Cycle\Configure\Factories\Connection\Protocols\ConnectionConfigInterface;

class PostgresConfig implements ConnectionConfigInterface
{
    public function tcpConnection(
        string $database,
        string $host,
        int|string $port,
        string|null $user,
        string|null $password,
        ?array $options
    ): ConnectionConfig {
        $options ??= [];

        return new Config\Postgres\TcpConnectionConfig(
            $database,
            $host ?? "localhost",
            $port ?? 5432,
            $user,
            $password,
            $options
        );
    }
    public function dsnConnection(\Stringable|string $dsn, ?string $user, ?string $password, ?array $options): ConnectionConfig
    {
        $options ??= [];

        $dsn ??= "pgsql:host=db;port=5432;dbname=backofficeapi;user=postgres;password=POSTGRES_PASSWORD";

        $conn = new Config\Postgres\DsnConnectionConfig(
            $dsn,
            $user,
            $password,
            $options
        );

        return $conn;
    }
}