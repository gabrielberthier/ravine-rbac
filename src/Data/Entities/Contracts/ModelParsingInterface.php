<?php

declare(strict_types=1);

namespace RavineRbac\Data\Entities\Contracts;

/**
 * @template T
 */
interface ModelParsingInterface
{
    /**
     * @param T
     */
    public function fromModel(object $model): static;
}
