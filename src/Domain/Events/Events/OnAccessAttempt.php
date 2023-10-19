<?php


namespace RavineRbac\Domain\Events\Events;

use RavineRbac\Domain\Events\Event;
use RavineRbac\Domain\Models\ContextIntent;
use RavineRbac\Domain\Models\Permission;
use RavineRbac\Domain\Models\ResourceType;
use RavineRbac\Domain\Models\Role;


class OnAccessAttempt extends Event
{
    public function __construct(
        Role|string $role,
        ResourceType|string $resource,
        ContextIntent|Permission $permission,
    ) {
        $role = $this->extractName($role);
        $permission = $this->extractName($permission);
        $resource = $this->extractName($resource);
        
        echo "User with {$role} needs {$permission} permission to access {$resource}" . PHP_EOL;
    }

    private function extractName(ResourceType|Role|Permission|string $subject): string
    {
        return is_string($subject) ? $subject : $subject->name;
    }
}