<?php
namespace RavineRbac\Data\Protocols\Rbac;

use RavineRbac\Domain\Models\RBAC\Role;
use PhpOption\Option;

interface RoleFetcherInterface
{
    /** @return Option<Role> */
    public function getRole(string $role): Option;
}