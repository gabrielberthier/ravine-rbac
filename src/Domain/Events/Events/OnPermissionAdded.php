<?php


namespace RavineRbac\Domain\Events\Events;

use RavineRbac\Domain\Events\Event;
use RavineRbac\Domain\Models\Permission;
use RavineRbac\Domain\Models\ResourceType;
use RavineRbac\Domain\Models\Role;


class OnPermissionAdded extends Event
{
    public function __construct(
        private Role|string $role,
        private ResourceType|string $resource,
        private Permission $permission
    )
    {
    }
}