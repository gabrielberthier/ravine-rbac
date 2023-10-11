<?php

namespace RavineRbac\Domain\Repositories\PersistenceOperations;

use RavineRbac\Domain\Contracts\ModelInterface;

interface DeleteOperationInterface
{
  public function delete(ModelInterface|int $id): ?object;
}
