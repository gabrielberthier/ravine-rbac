<?php


namespace RavineRbac\Domain\Events\Events;

use RavineRbac\Domain\Events\Event;
use RavineRbac\Domain\Models\RBAC\Permission;
use RavineRbac\Domain\Models\RBAC\ResourceType;
use RavineRbac\Domain\Models\RBAC\Role;


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