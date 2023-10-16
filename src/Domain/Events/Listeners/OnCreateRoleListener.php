<?php

namespace RavineRbac\Listener;

use RavineRbac\Domain\Events\Events\OnRoleCreateEvent;
use RavineRbac\Domain\Events\ListenerInterface;
use RavineRbac\Domain\Models\RBAC\Role;
use Psr\Log\LoggerInterface;



interface RoleDataStorerInterface
{
    public function storeRole(Role $role): void;
}

/**
 * @implements ListenerInterface<OnRoleCreateEvent>
 */
class OnCreateRoleListener implements ListenerInterface
{
    public function __construct(
        public readonly LoggerInterface $logger,
        public readonly RoleDataStorerInterface $roleStorage
    ) {
    }

    public function execute(OnRoleCreateEvent $subject): void
    { 
        $this->roleStorage->storeRole($subject->role);
    }
}