<?php

declare(strict_types=1);

namespace RavineRbac\Domain\Models;

use JsonSerializable;
use Ramsey\Uuid\UuidInterface;

readonly class Token implements JsonSerializable
{
    public function __construct(
        public string $role,
        public ?string $email = null,
        public ?string $username = null,
        public ?string $authType = null,
        public ?UuidInterface $uuid = null,
    ) {
    }

    /**
     * @return array
     */
    public function jsonSerialize(): mixed
    {
        return [
            'uuid' => $this->uuid,
            'email' => $this->email,
            'username' => $this->username,
            'role' => $this->role,
            'auth_type' => $this->authType,
        ];
    }
}