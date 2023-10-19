<?php

declare(strict_types=1);

namespace RavineRbac\Domain\Models;

enum ContextIntent: string
{
    case CREATE = 'create';
    case READ = 'read';
    case UPDATE = 'update';
    case DELETE = 'delete';
    case CUSTOM = 'custom';
    case FREEPASS = '*';
}