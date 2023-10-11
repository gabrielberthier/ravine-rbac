<?php

namespace RavineRbac\Domain\Repositories\PersistenceOperations;

use RavineRbac\Domain\Contracts\ModelInterface;

interface PersistOperationInterface
{
  public function insert(ModelInterface $model): void;
}
