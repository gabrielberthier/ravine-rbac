<?php


namespace RavineRbac\Domain\Events\Events;

use RavineRbac\Domain\Events\Event;
use RavineRbac\Domain\Models\Role;


class OnRoleCreateEvent extends Event
{
    public function __construct(public readonly Role $role)
    {
    }
}