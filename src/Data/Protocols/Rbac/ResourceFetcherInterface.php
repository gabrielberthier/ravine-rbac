<?php

namespace RavineRbac\Data\Protocols\Rbac;

use PhpOption\Option;
use RavineRbac\Domain\Models\RBAC\ResourceType;

interface ResourceFetcherInterface
{
    /** @return Option<ResourceType> */
    public function getResource(string $resource): Option;
}