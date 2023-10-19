<?php


namespace RavineRbac\Domain\Events\Events;

use RavineRbac\Domain\Events\Event;
use RavineRbac\Domain\Models\ResourceType;


class OnResourceCreateEvent extends Event
{
    public function __construct(public readonly ResourceType $role)
    {
    }
}