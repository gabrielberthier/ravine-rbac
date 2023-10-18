<?php

declare(strict_types=1);

namespace RavineRbac\Infra\Persistence\Cycle\RbacDb;

use RavineRbac\Domain\Models\RBAC\Permission;
use RavineRbac\Domain\Models\RBAC\ResourceType;
use RavineRbac\Domain\Models\RBAC\Role;
use RavineRbac\Domain\OptionalApi\Result;
use RavineRbac\Domain\OptionalApi\Result\Err;
use RavineRbac\Domain\OptionalApi\Result\Ok;
use Cycle\ORM\EntityManager;
use Cycle\ORM\ORM;
use RavineRbac\Infra\Persistence\Cycle\Entities\{
    CycleRole,
    CycleResource,
    CyclePermission
};


class CycleRoleAccessCreator
{
    public function __construct(
        private ORM $orm
    ) {
    }

    /**
     * @return Result<CycleRole>
     */
    public function create(
        Role $role,
        ResourceType $resource,
        Permission $permission
    ): Result {
        try {
            $t = new EntityManager($this->orm);
            $cycleRole = CycleRole::fromModel($role);
            $cycleResource = CycleResource::fromModel($resource);
            $cyclePermission = CyclePermission::fromModel($permission)
                ->setResource($cycleResource)
                ->setRole($cycleRole);

            $cycleRole->addPermission($cyclePermission);

            $t->persist($cycleRole);

            $t->run();

            return new Ok($cycleRole);
        } catch (\Throwable $th) {
            return new Err($th);
        }
    }
}