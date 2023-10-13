<?php
use RavineRbac\Domain\Models\RBAC\AccessControl;
use RavineRbac\Domain\Models\RBAC\Role;
use RavineRbac\Domain\OptionalApi\Result;

interface RoleStorer
{
    function store(Role $role): Result;
}

class RoleCreator
{
    public function __construct(
        public AccessControl $accessControl,
        public RoleStorer $roleStorer
    ) {

    }
    public function create(
        string $roleName,
        string $description = ""
    ) {
        $role = $this->accessControl->forgeRole($roleName, $description)->getRole($roleName);

        return $this->roleStorer->store($role->get());
    }
}