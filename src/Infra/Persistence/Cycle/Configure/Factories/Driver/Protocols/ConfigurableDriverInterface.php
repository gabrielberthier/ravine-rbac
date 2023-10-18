<?php

declare(strict_types=1);

namespace RavineRbac\Infra\Persistence\Cycle\Configure\Factories\Driver\Protocols;

interface ConfigurableDriverInterface
{
    public function withSchema(string $schema): static;
    public function withTimezone(string $tz): static;
    public function withQueryCache(bool $queryCache): static;
    public function withReadonlySchema(bool $readonlySchema): static;
    public function readonly(): static;

    public function factory(): DriverFactoryInterface;
}