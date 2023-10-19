<?php

declare(strict_types=1);

namespace RavineRbac\Domain\Repositories;

use RavineRbac\Domain\Models\Account;
use RavineRbac\Domain\OptionalApi\Result;

interface AccountRepository
{
    public function findByAccess(string $access): ?Account;
    public function findById(int $id): ?Account;
    public function findByUUID(string $uuid): ?Account;
    public function findWithAuthType(string $access, AuthTypes $authType): ?Account;
    /** @return Result<bool, \Exception> */
    public function insert(Account $account): Result;
}