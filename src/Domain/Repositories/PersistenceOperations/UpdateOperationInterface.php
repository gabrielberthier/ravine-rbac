<?php

namespace RavineRbac\Domain\Repositories\PersistenceOperations;


interface UpdateOperationInterface
{
  public function update(int $id, array $values): ?object;
}
