<?php


namespace RavineRbac\Domain\Events\Events;

use RavineRbac\Domain\Events\Event;
use RavineRbac\Domain\Models\Role;


class OnRoleAppendedEvent extends Event
{
    public function __construct(public readonly Role $role)
    {
    }
}