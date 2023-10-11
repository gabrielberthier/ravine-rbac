<?php

namespace RavineRbac\Domain\Repositories\PersistenceOperations;



interface CrudOperationsInterface extends
    ReadOperationInterface,
    PersistOperationInterface,
    DeleteOperationInterface,
    UpdateOperationInterface
{
}
