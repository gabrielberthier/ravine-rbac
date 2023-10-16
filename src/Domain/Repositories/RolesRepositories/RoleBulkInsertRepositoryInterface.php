<?php

declare(strict_types=1);

namespace RavineRbac\Domain\Repositories\RolesRepositories;

use RavineRbac\Domain\OptionalApi\Result;

interface RoleBulkInsertRepositoryInterface 
{
    /**
     * This function MUST store roles as they are defined containing resources and permissions associated to them.
     * 
     * @param \RavineRbac\Domain\Models\RBAC\Role[] $role
     * 
     * @return Result<bool,\Exception>
     */
    function bulkInsertRoles(array $role): Result;
}
