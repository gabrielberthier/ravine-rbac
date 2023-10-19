<?php

declare(strict_types=1);

namespace RavineRbac\Application\Protocols;

use RavineRbac\Domain\Models\{Role, ResourceType, ContextIntent, Permission};

interface RbacFallbackInterface
{
    public function retry(
        Role|string $role,
        ResourceType|string $resource,
        ContextIntent|Permission $permission
    ): bool;
}