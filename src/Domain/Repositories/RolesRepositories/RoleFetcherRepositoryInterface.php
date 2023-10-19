<?php

declare(strict_types=1);

namespace RavineRbac\Domain\Repositories\RolesRepositories;

use RavineRbac\Domain\Models\Role;
use RavineRbac\Domain\OptionalApi\Result;

interface RoleFetcherRepositoryInterface
{
    /**
     * This function MUST return a RESULT object containing a SINGLE
     * ROLE with its RESOURCES and PERMISSIONS in successful case 
     * or an EXCEPTION otherwise. 
     * 
     * @return \RavineRbac\Domain\OptionalApi\Result<Role,\Exception> 
     */
    function fetch(mixed $identifier): Result;

    /** 
     * This function MUST return a RESULT object containing 
     * ROLES with its RESOURCES and PERMISSIONS in successful case 
     * or an EXCEPTION otherwise.
     * 
     * @return \RavineRbac\Domain\OptionalApi\Result<Role[],\Exception> 
     */
    function fetchAll(): Result;
}