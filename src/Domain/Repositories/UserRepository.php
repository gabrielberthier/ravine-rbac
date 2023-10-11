<?php
declare(strict_types=1);

namespace RavineRbac\Domain\Repositories;

use RavineRbac\Domain\Models\User;

interface UserRepository
{
    /**
     * @return User[]
     */
    public function findAll(): array;

    /**
     * @throws \RavineRbac\Domain\Exceptions\UserNotFoundException
     */
    public function findUserOfId(int $id): User;
}