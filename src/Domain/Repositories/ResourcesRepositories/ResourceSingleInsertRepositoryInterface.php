<?php

declare(strict_types=1);

namespace RavineRbac\Domain\Repositories\RolesRepositories;


use RavineRbac\Domain\Models\ResourceType;
use RavineRbac\Domain\OptionalApi\Result;

interface ResourceSingleInsertRepositoryInterface 
{
    /**
     * This function MUST store a single role as it is defined containing resources and permissions associated to it.
     * 
     * @param ResourceType $resourceType
     * 
     * @return Result<bool,\Exception>
     */
    function insertResource(ResourceType $resourceType): Result;
}
