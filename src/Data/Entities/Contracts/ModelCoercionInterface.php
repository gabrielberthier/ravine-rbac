<?php

declare(strict_types=1);

namespace RavineRbac\Data\Entities\Contracts;

/**
 * @template T
 */
interface ModelCoercionInterface
{
    /**
     * @return T
     */
    public function toModel(): object;
}
