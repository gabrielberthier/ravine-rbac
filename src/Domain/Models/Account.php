<?php

declare(strict_types=1);

namespace RavineRbac\Domain\Models;

use DateTimeImmutable;
use DateTimeInterface;
use JsonSerializable;
use Ramsey\Uuid\UuidInterface;

/**
 * The account class holds information regarding an account.
 * This account instance requires an access to be identified, such access might be
 * an email, username, etc.
 * 
 * 
 */
class Account implements JsonSerializable
{
    public function __construct(
        public string $access,
        /** @var Role[] $roles */
        public array $roles = [],
        public string $authType = 'default',
        public ?int $id = null,
        public ?UuidInterface $uuid = null,
        public DateTimeInterface $createdAt = new DateTimeImmutable(),
        public DateTimeInterface $updated = new DateTimeImmutable()
    ) {
    }

    public function canAccess(
        ResourceType $resource,
        ContextIntent|Permission $permission
    ): bool {
        foreach ($this->roles as $role) {
            if ($role->canAcess($resource, $permission)) {
                return true;
            }
        }

        return false;
    }

    public function addRole(Role $role): void
    {
        array_push($this->roles, $role);
    }

    /**
     * @return array
     */
    public function jsonSerialize(): mixed
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'access' => $this->access,
            'roles' => $this->roles,
            'auth_type' => $this->authType,
            'created_at' => $this->createdAt,
            'updated' => $this->updated,
        ];
    }
}