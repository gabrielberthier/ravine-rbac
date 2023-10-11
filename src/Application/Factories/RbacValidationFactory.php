<?php
namespace RavineRbac\Application\Factories;

use RavineRbac\Data\Protocols\Rbac\ResourceFetcherInterface;
use RavineRbac\Data\Protocols\Rbac\RoleFetcherInterface;
use RavineRbac\Domain\Models\RBAC\AccessControl;
use RavineRbac\Domain\Models\RBAC\Permission;
use RavineRbac\Domain\Models\RBAC\Resource;
use RavineRbac\Presentation\Middleware\RoleValidationMiddleware;
use RavineRbac\Presentation\Protocols\RbacFallbackInterface;
use Psr\Container\ContainerInterface;

class RbacValidationFactory
{
    private RoleValidationMiddleware $middleware;
    public function __construct(private ContainerInterface $containerInterface)
    {
        $accessControl = $containerInterface->get(AccessControl::class);
        $roleFetcherInterface = $containerInterface->get(RoleFetcherInterface::class);
        $resourceFetcherInterface = $containerInterface->get(ResourceFetcherInterface::class);

        $this->middleware = new RoleValidationMiddleware(
            $accessControl,
            $roleFetcherInterface,
            $resourceFetcherInterface
        );
    }

    public function __invoke(Resource|string $target): RoleValidationMiddleware
    {
        return $this->middleware->setResourceTarget($target);
    }

    public function setCustomPermission(Permission $permission): self
    {
        $this->middleware->setPredefinedPermission($permission);

        return $this;
    }

    public function setCustomFallback(RbacFallbackInterface $fallback): self
    {
        $this->middleware->setByPassFallback($fallback);

        return $this;
    }
}