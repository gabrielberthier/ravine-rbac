<?php
namespace RavineRbac\Domain\Models\RBAC;

use RavineRbac\Domain\Models\Account;


class Executor
{
    static function execute()
    {
        $account = new Account(null, 'mail', 'username', 'pass', 'COMMON');
        $accountRole = new RoleProfile($account);
        $role = new Role('image_role', '');
        $resource = new ResourceType('image', 'images resources');
        $canCreate = new Permission('can:create', ContextIntent::READ);
        $role->addPermissionToResourceType($canCreate, $resource);
        $accountRole->addRole($role);

        foreach ($accountRole->roles as $role) {
            if ($role->canAcess($resource, ContextIntent::CREATE)) {
                return true;
            }
        }

        return false;
    }
}