<?php

namespace RavineRbac\Domain\Events\Events;

use RavineRbac\Domain\Contracts\AccessControlInterface;
use RavineRbac\Domain\Events\Event;

final class OnRbacStart extends Event
{
    public function __construct(public readonly AccessControlInterface $accessControlInterface)
    {
    }
}