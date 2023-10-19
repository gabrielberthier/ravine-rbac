<?php


namespace RavineRbac\Domain\Events\Events;

use RavineRbac\Domain\Events\Event;
use RavineRbac\Domain\Models\Role;


class OnRoleExtendedEvent extends Event
{
    public function __construct(
        public readonly Role $target,
        public readonly array $roles
    ) {
    }
}