<?php

declare(strict_types=1);

namespace RavineRbac\Domain\Repositories\ResourcesRepositories;

use RavineRbac\Domain\Models\RBAC\ResourceType;
use RavineRbac\Domain\OptionalApi\Result;

interface ResourceFetcherRepositoryInterface
{
    /** 
     * This function MUST return a RESULT object containing 
     * a SINGLE RESOURCE TYPE in successful case 
     * or an EXCEPTION otherwise.
     * 
     * @return Result<ResourceType,\Exception> 
     */
    function fetch(mixed $identifier): Result;

    /** 
     * This function MUST return a RESULT object containing 
     * RESOURCE TYPES instances in successful case 
     * or an EXCEPTION otherwise.
     * 
     * @return Result<ResourceType[],\Exception> 
     */
    function fetchAll(): Result;
}