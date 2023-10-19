<?php

namespace RavineRbac\Infra\Persistence\Cycle\RbacDb;

use RavineRbac\Domain\Models\Role;
use RavineRbac\Domain\OptionalApi\Result;
use RavineRbac\Domain\OptionalApi\Result\Err;
use RavineRbac\Domain\OptionalApi\Result\Ok;
use Cycle\ORM\EntityManager;
use Cycle\ORM\ORM;
use RavineRbac\Infra\Persistence\Cycle\Entities\CycleRole;

final class CycleRoleExtender
{
    public function __construct(
        private ORM $orm
    ) {
    }

    /**
     * @return Result<CycleRole>
     */
    public function extend(
        Role $target,
        Role $parent
    ): Result {
        try {
            $t = new EntityManager($this->orm);
            $repository = new CycleRoleAccessRepository($this->orm);
            $cycleRole = $repository
                ->getOneByName($target->name)
                ->ok()
                ->map(fn($el) => $el ?? CycleRole::fromModel($target))
                ->get();

            $cycleParentRole = $repository
                ->getOneByName($parent->name)
                ->ok()
                ->map(fn($el) => $el ?? CycleRole::fromModel($parent))
                ->get();

            $cycleRole->extendRole($cycleParentRole);

            $t->persist($cycleRole);

            $t->run();

            return new Ok($cycleRole);
        } catch (\Throwable $th) {
            dd($th);
            return new Err($th);
        }
    }
}