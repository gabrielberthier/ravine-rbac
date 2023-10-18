<?php

use Cycle\ORM\ORM;
use RavineRbac\Domain\Events\Events\OnPermissionAdded;
use RavineRbac\Domain\Events\Events\OnRbacStart;
use RavineRbac\Domain\Events\ListenerProvider;
use RavineRbac\Domain\Models\RBAC\ContextIntent;
use RavineRbac\Domain\Models\RBAC\Permission;
use RavineRbac\Domain\Models\RBAC\ResourceType;
use RavineRbac\Domain\Models\RBAC\Role;
use RavineRbac\Infra\Persistence\Cycle\Entities\CycleRole;

return static function (ORM $orm) {
    $provider = new ListenerProvider();

    $provider->addListener(OnRbacStart::class, function (OnRbacStart $event) use ($orm) {
        echo 'Event RBAC started, loading roles' . PHP_EOL;

        /** @var CycleRole[] */
        $rolesDb = $orm
            ->getRepository(CycleRole::class)
            ->select()
            ->load(["permissions", "extendedRoles"]);

        foreach ($rolesDb as $roleDb) {
            $role = new Role($roleDb->name, $roleDb->description);
            foreach ($roleDb->getPermissions() as $cyclePermission) {
                $permission = new Permission($cyclePermission->name, ContextIntent::from($cyclePermission->context));
                /** @var \RavineRbac\Infra\Persistence\Cycle\Entities\CycleResource */
                $cycleResource = $cyclePermission->getResource();
                $resource = new ResourceType($cycleResource->name, $cycleResource->description);
                $role->addPermissionToResourceType($permission, $resource);
            }

            $event->accessControlInterface->appendRole($role);
        }
    });

    $provider->addListener(OnPermissionAdded::class, function (OnPermissionAdded $event){
        echo "Permission added". PHP_EOL;
    });

    return $provider;
};