<?php

namespace RavineRbac\Domain\Repositories;

use RavineRbac\Domain\Exceptions\Security\DuplicatedTokenException;
use RavineRbac\Domain\Models\Security\SignatureToken;

interface SignatureTokenRepositoryInterface
{
    /**
     * Inserts a new token in the database.
     *
     * @throws DuplicatedTokenException
     */
    public function save(SignatureToken $token): bool;
}
