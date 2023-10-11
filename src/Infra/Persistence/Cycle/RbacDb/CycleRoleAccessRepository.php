<?php
namespace RavineRbac\Infra\Persistence\Cycle\RbacDb;

use RavineRbac\Data\Entities\Cycle\Rbac\CycleRole;
use RavineRbac\Domain\Models\RBAC\Resource;
use RavineRbac\Domain\Models\RBAC\Role;
use RavineRbac\Domain\OptionalApi\Result;
use RavineRbac\Domain\OptionalApi\Result\Err;
use RavineRbac\Domain\OptionalApi\Result\Ok;
use Cycle\ORM\ORM;

class CycleRoleAccessRepository
{
    public function __construct(private ORM $orm)
    {
    }

    /**
     * @return Result<CycleRole,\Exception>
     */
    public function getRoleWithPermissions(Role $role, Resource $resource): Result
    {
        try {
            $roleDb = $this->orm
                ->getRepository(CycleRole::class)
                ->select()
                ->load(["permissions", "extendedRoles"])
                ->where('name', $role->name)
                ->fetchOne();

            return new Ok($roleDb);
        } catch (\Throwable $th) {
            return new Err($th);
        }
    }

    /**
     * @return Result<CycleRole,\Exception>
     */
    public function getOneByName(string $subject): Result
    {
        try {
            return new Ok(
                $this->orm->getRepository(CycleRole::class)
                    ->select()
                    ->where('name', $subject)
                    ->fetchOne()
            );
        } catch (\Throwable $th) {
            return new Err($th);

        }
    }
}