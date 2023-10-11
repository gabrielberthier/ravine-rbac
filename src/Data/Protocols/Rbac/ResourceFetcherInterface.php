<?php

namespace RavineRbac\Data\Protocols\Rbac;

use PhpOption\Option;
use RavineRbac\Domain\Models\RBAC\Resource;

interface ResourceFetcherInterface
{
    /** @return Option<Resource> */
    public function getResource(string $resource): Option;
}