<?php

declare(strict_types=1);

namespace RavineRbac\Domain\Repositories\RolesRepositories;

use RavineRbac\Domain\OptionalApi\Result;

interface RoleExtenderRepositoryInterface
{
    /** 
     * This function attaches a parent role to a target role.
     * 
     * @return Result<bool,\Exception> */
    function extendRole(Role $role, Role $parent): Result;
}