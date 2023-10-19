<?php

declare(strict_types=1);

namespace RavineRbac\Infra\Persistence\Cycle\Entities;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use RavineRbac\Data\Entities\Contracts\ModelCoercionInterface;
use RavineRbac\Domain\Models\Account;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Table\Index;
use Cycle\ORM\Entity\Behavior;
use Cycle\ORM\Entity\Behavior\Uuid\Uuid4;
use RavineRbac\Domain\Models\Role;
use RavineRbac\Infra\Persistence\Cycle\Entities\Traits\TimestampsTrait;
use RavineRbac\Infra\Persistence\Cycle\Entities\Traits\UuidTrait;
use Cycle\Annotated\Annotation\Relation\HasMany;

#[Entity(table: 'cycle_accounts')]
#[Uuid4]
#[Behavior\CreatedAt(
    field: 'createdAt', // Required. By default 'createdAt'
    column: 'created_at' // Optional. By default 'null'. If not set, will be used information from property declaration.
)]
#[Behavior\UpdatedAt(
    field: 'updated', // Required. By default 'updatedAt' 
    column: 'updated_at' // Optional. By default 'null'. If not set, will be used information from property declaration.
)]
#[Index(columns: ['access'], unique: true)]
class CycleAccount implements ModelCoercionInterface
{
    use UuidTrait, TimestampsTrait;

    #[Column(type: "primary")]
    public int $id;

    #[Column(type: 'string', nullable: false)]
    public string $access;

    #[HasMany(target: CycleRole::class, nullable: true, collection: 'doctrine')]
    public Collection $roles;

    #[Column(name: 'auth_type', type: 'string', nullable: true)]
    public ?string $authType = 'default';

    public function __construct()
    {
        $this->roles = new ArrayCollection();
    }

    public function toModel(): Account
    {
        return new Account(
            access: $this->access,
            roles: $this->roles->toArray(),
            authType: $this->authType,
            id: $this->id,
            uuid: $this->uuid,
            createdAt: $this->createdAt,
            updated: $this->updated,
        );
    }

    public function addRole(CycleRole $role): void
    {
        $this->roles->add($role);
    }

    public function removeRole(CycleRole $role): void
    {
        $this->extendedRoles = $this->roles->filter(static fn(CycleRole $p) => $p !== $role);
    }

    public static function fromModel(Account $account)
    {
        $cycle = new CycleAccount();
        $cycle->id = $account->id;
        $cycle->access = $account->access;
        $cycle->createdAt = $account->createdAt;
        $cycle->updatedAt = $account->updated;

        $cycle->roles = new ArrayCollection(
            array_map(
                fn(Role $role) => CycleRole::fromModel($role),
                $account->roles
            )
        );

        return $cycle;
    }
}