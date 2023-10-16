<?php

declare(strict_types=1);

namespace RavineRbac\Domain\Repositories;

use RavineRbac\Domain\Models\RBAC\ResourceType;
use RavineRbac\Domain\Models\RBAC\Role;
use RavineRbac\Domain\OptionalApi\Result;

interface PermissionAssignerRepository 
{
    /** @return Result<bool, \Exception> */
    function assignPermissionToRoleOnResourceType(
        Role|string $role,
        ResourceType $resource,
        array $permissions
    ): Result;
}