<?php

declare(strict_types=1);

namespace RavineRbac\Domain\Repositories\RolesRepositories;


use RavineRbac\Domain\Models\RBAC\Role;
use RavineRbac\Domain\OptionalApi\Result;

interface RoleSingleInsertRepositoryInterface 
{
    /**
     * This function MUST store a single role as it is defined containing resources and permissions associated to it.
     * 
     * @param \RavineRbac\Domain\Models\RBAC\Role $role
     * 
     * @return Result<bool>
     */
    function insertRole(Role $role): Result;
}
