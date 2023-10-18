<?php

declare(strict_types=1);

namespace RavineRbac\Infra\Persistence\Cycle\Configure\Factories\Connection\Input;

final readonly class ConnectionDefinitions
{
    public function __construct(
        public ?string $db = null,
        public int|string|null $port = null,
        public ?string $user = null,
        public ?string $password = null,
        public ?string $host = null,
        public ?string $url = null,
        public array $options = []
    ) {
    }
}