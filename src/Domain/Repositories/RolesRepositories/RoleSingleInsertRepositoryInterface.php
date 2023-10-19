<?php

declare(strict_types=1);

namespace RavineRbac\Domain\Repositories\RolesRepositories;


use RavineRbac\Domain\Models\Role;
use RavineRbac\Domain\OptionalApi\Result;

interface RoleSingleInsertRepositoryInterface 
{
    /**
     * This function MUST store a single role as it is defined containing resources and permissions associated to it.
     * 
     * @param Role $role
     * 
     * @return Result<bool,\Exception>
     */
    function insertRole(Role $role): Result;
}
