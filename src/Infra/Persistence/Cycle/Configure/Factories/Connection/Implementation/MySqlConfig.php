<?php

declare(strict_types=1);

namespace RavineRbac\Infra\Persistence\Cycle\Configure\Factories\Connection\Implementation;


use Cycle\Database\Config;
use Cycle\Database\Config\MySQL\ConnectionConfig;
use RavineRbac\Infra\Persistence\Cycle\Configure\Factories\Connection\Protocols\ConnectionConfigInterface;

class MySqlConfig implements ConnectionConfigInterface
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
        $charset = $options['charset'] ?? 'utf8mb4';

        return new Config\MySQL\TcpConnectionConfig(
            $database,
            $host ?? "localhost",
            $port ?? 5432,
            $charset,
            $user,
            $password,
            $options
        );
    }
    public function dsnConnection(\Stringable|string $dsn, ?string $user, ?string $password, ?array $options): ConnectionConfig
    {
        $options ??= [];

        return new Config\MySQL\DsnConnectionConfig(
            $dsn,
            $user,
            $password,
            $options
        );
    }
}