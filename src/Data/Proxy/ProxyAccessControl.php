<?php
namespace RavineRbac\Data\Proxy;

use PhpOption\LazyOption;
use PhpOption\None;
use Psr\Log\LoggerInterface;
use RavineRbac\Domain\Contracts\AccessControlInterface;
use RavineRbac\Domain\Events\EventDispatcher;
use RavineRbac\Domain\Events\Events\{OnRoleRevokedEvent, OnRoleCreateEvent, OnRoleAppendedEvent};
use RavineRbac\Domain\Events\Events\OnPermissionAdded;
use RavineRbac\Domain\Events\Events\OnRbacStart;
use RavineRbac\Domain\Events\Events\OnResourceAppendedEvent;
use RavineRbac\Domain\Events\Events\OnResourceCreateEvent;
use RavineRbac\Domain\Events\Events\OnRoleExtendedEvent;
use RavineRbac\Domain\Models\RBAC\AccessControl;
use PhpOption\Option;
use RavineRbac\Domain\Models\RBAC\{ResourceType, Role, Permission, ContextIntent};
use RavineRbac\Domain\OptionalApi\Result;
use RavineRbac\Domain\Repositories\RolesRepositories\RoleFetcherRepositoryInterface;
use RavineRbac\Domain\Repositories\ResourcesRepositories\ResourceFetcherRepositoryInterface;

final class ProxyAccessControl implements AccessControlInterface
{
    public function __construct(
        private AccessControl $accessControl,
        private EventDispatcher $eventDispatcher,
        private LoggerInterface $loggerInterface,
        private ?RoleFetcherRepositoryInterface $roleFetcherRepositoryInterface = null,
        private ?ResourceFetcherRepositoryInterface $resourceFetcherRepositoryInterface = null
    ) {
        $this->eventDispatcher->dispatch(new OnRbacStart($accessControl));
    }

    public function setRoleFetcherRepository(RoleFetcherRepositoryInterface $roleFetcherRepositoryInterface)
    {
        $this->roleFetcherRepositoryInterface = $roleFetcherRepositoryInterface;
    }

    public function setResourceFetcherRepository(ResourceFetcherRepositoryInterface $resourceFetcherRepositoryInterface)
    {
        $this->resourceFetcherRepositoryInterface = $resourceFetcherRepositoryInterface;
    }

    public function addPermissionToRole(
        Role|string $role,
        ResourceType|string $resource,
        ContextIntent $intent,
        string $permissionName = ""
    ): self {
        $this->accessControl->addPermissionToRole(
            $role,
            $resource,
            $intent,
            $permissionName
        );

        $permission = $permissionName === "" ?
            Permission::makeWithPreferableName($intent, $resource) :
            new Permission($permissionName, $intent);

        $this->eventDispatcher->dispatch(
            new OnPermissionAdded(
                $role,
                $resource,
                $permission
            )
        );

        return $this;
    }

    /**
     * @param Role|string $role
     * @param ResourceType $permission
     * @param Permission[] $permission
     */
    public function grantAccessOn(
        Role|string $role,
        ResourceType $resource,
        array $permissions
    ): self {
        $this->accessControl->grantAccessOn($role, $resource, $permissions);

        foreach ($permissions as $permission) {
            $this->eventDispatcher->dispatch(
                new OnPermissionAdded(
                    $role,
                    $resource,
                    $permission
                )
            );
        }

        return $this;
    }

    public function tryAccess(
        Role|string $role,
        ResourceType|string $resource,
        ContextIntent|Permission $permission,
        ?callable $fallback = null
    ): bool {
        return $this->accessControl->tryAccess($role, $resource, $permission, $fallback);
    }

    public function appendRole(Role $role): self
    {
        $this->accessControl->appendRole($role);

        $this->eventDispatcher->dispatch(new OnRoleAppendedEvent($role));

        return $this;
    }

    public function forgeRole(
        string $roleName,
        string $description = ''
    ): self {
        $this->accessControl->forgeRole($roleName, $description);
        $role = $this->getRole($roleName)->get();

        $this->eventDispatcher->dispatch(
            new OnRoleCreateEvent(
                $role
            )
        );

        return $this;
    }

    /**
     * @return Option<Role>
     */
    public function getRole(Role|string $role): Option
    {
        return $this->accessControl->getRole($role)->orElse(
            new LazyOption(
                fn() => $this->roleFallback($this->extractName($role)),
            )
        );
    }

    /** @return Role[] */
    public function getRoles(): array
    {
        $roles = $this->accessControl->getRoles();

        if (count($roles)) {
            return $roles;
        }

        if (!is_null($this->roleFetcherRepositoryInterface)) {
            return $this->coerceResultArray(
                $this->roleFetcherRepositoryInterface->fetchAll()
            );
        }

        return [];
    }

    public function revokeRole(Role|string $role): void
    {
        $this->accessControl->revokeRole($role);

        $this->eventDispatcher->dispatch(new OnRoleRevokedEvent($role));
    }

    public function extendRole(Role|string $targetRole, Role|string ...$roles): void
    {
        $this->accessControl->extendRole($targetRole, $roles);

        $this->eventDispatcher->dispatch(new OnRoleExtendedEvent($targetRole, $roles));
    }

    /**
     * @return ResourceType[]
     */
    public function getResourceTypes(): array
    {
        $resourceTypes = $this->accessControl->getResourceTypes();

        if (count($resourceTypes)) {
            return $resourceTypes;
        }

        if (!is_null($this->resourceFetcherRepositoryInterface)) {
            return $this->coerceResultArray(
                $this->resourceFetcherRepositoryInterface->fetchAll()
            );
        }

        return [];
    }

    /** @return Option<ResourceType> */
    public function getResourceType(ResourceType|string $resource): Option
    {
        return $this->accessControl->getResourceType($resource)->orElse(
            new LazyOption(
                fn() => $this->resourceFallback($this->extractName($resource)),
            )
        );
    }

    public function createResourceType(string $name, string $description): ResourceType
    {
        $resourceType = $this->accessControl->createResourceType($name, $description);

        $this->eventDispatcher->dispatch(new OnResourceCreateEvent($resourceType));

        return $resourceType;
    }

    public function appendResourceType(ResourceType $resource): self
    {
        $this->accessControl->appendResourceType($resource);

        $this->eventDispatcher->dispatch(new OnResourceAppendedEvent($resource));

        return $this;
    }

    public function toJson(): string
    {
        return $this->accessControl->toJson();
    }

    private function coerceResultArray(Result $result): array
    {
        $logger = $this->loggerInterface;
        
        return $result->unwrapOrElse(
            function (\Exception $exception) use ($logger) {
                $logger->error(sprintf("An error occured: %s", $exception->getMessage()));

                return [];
            }
        );
    }

    /** @return Option<Role> */
    private function roleFallback(string $role): Option
    {
        $roleFetcherRepositoryInterface = $this->roleFetcherRepositoryInterface;

        if (is_null($roleFetcherRepositoryInterface)) {
            return None::create();
        }

        $result = $roleFetcherRepositoryInterface->fetch($role);

        if ($result->isErr()) {
            $error = $result->unwrapErr();
            $this->loggerInterface->error(
                sprintf("An error occured: %s", $error->getMessage())
            );

            return None::create();
        }

        return $result->ok()->map(
            function (Role $role): Role {
                $this->accessControl->appendRole($role);

                return $role;
            }
        );
    }

    /** @return Option<ResourceType> */
    private function resourceFallback(string $resourceName): Option
    {
        $resourceFetcherRepositoryInterface = $this->resourceFetcherRepositoryInterface;

        if (is_null($resourceFetcherRepositoryInterface)) {
            return None::create();
        }

        $result = $resourceFetcherRepositoryInterface->fetch($resourceName);

        if ($result->isErr()) {
            $error = $result->unwrapErr();
            $this->loggerInterface->error(
                sprintf("An error occured: %s", $error->getMessage())
            );

            return None::create();
        }

        return $result->ok()->map(
            function (ResourceType $resource): ResourceType {
                $this->accessControl->appendResourceType($resource);

                return $resource;
            }
        );
    }

    public function jsonSerialize(): mixed
    {
        return $this->accessControl->jsonSerialize();
    }

    private function extractName(ResourceType|Role|Permission|string $subject): string
    {
        return is_string($subject) ? $subject : $subject->name;
    }
}