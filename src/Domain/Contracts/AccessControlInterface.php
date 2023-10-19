<?php

declare(strict_types=1);

namespace RavineRbac\Domain\Contracts;

use PhpOption\Option;
use RavineRbac\Domain\Models\{ResourceType, Role, Permission, ContextIntent};

interface AccessControlInterface extends \JsonSerializable
{
    public function addPermissionToRole(
        Role|string $role,
        ResourceType|string $resource,
        ContextIntent $intent,
        string $permissionName = ""
    ): self;

    /**
     * @param Role|string $role
     * @param ResourceType $permission
     * @param Permission[] $permission
     */
    public function grantAccessOn(
        Role|string $role,
        ResourceType $resource,
        array $permissions
    ): self;

    public function tryAccess(
        Role|string $role,
        ResourceType|string $resource,
        ContextIntent|Permission $permission,
        ?callable $fallback = null
    ): bool;

    public function appendRole(Role $role): self;

    public function forgeRole(
        string $roleName,
        string $description = ''
    ): self;

    /**
     * @return Option<Role>
     */
    public function getRole(Role|string $role): Option;

    /** @return Role[] */
    public function getRoles(): array;

    public function revokeRole(Role|string $role): void;

    public function extendRole(Role|string $targetRole, Role|string ...$roles): void;

    /**
     * @return ResourceType[]
     */
    public function getResourceTypes(): array;

    /** @return Option<ResourceType> */
    public function getResourceType(ResourceType|string $resource): Option;

    public function createResourceType(string $name, string $description): self;

    public function appendResourceType(ResourceType $resource): self;

    public function toJson(): string;
}