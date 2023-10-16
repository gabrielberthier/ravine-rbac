<?php

declare(strict_types=1);

namespace RavineRbac\Domain\Repositories\RolesRepositories;

use RavineRbac\Domain\OptionalApi\Result;

interface RoleBulkInsertRepositoryInterface
{
    /**
     * This function MUST store resources as they are defined.
     * 
     * @param \RavineRbac\Domain\Models\RBAC\ResourceType[] $role
     * 
     * @return Result<bool,\Exception>
     */
    function bulkInsertResourceTypes(array $role): Result;
}