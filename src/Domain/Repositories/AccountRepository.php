<?php

declare(strict_types=1);

namespace RavineRbac\Domain\Repositories;

use RavineRbac\Domain\Dto\AccountDto;
use RavineRbac\Domain\Models\Account;
use RavineRbac\Domain\Models\Enums\AuthTypes;

interface AccountRepository
{
    public function findByMail(string $mail): ?Account;

    public function findByAccess(string $access): ?Account;

    public function findByUUID(string $uuid): ?Account;

    public function findWithAuthType(string $email, AuthTypes $authType): ?Account;

    public function insert(AccountDto $account): Account;
}
