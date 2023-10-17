<?php
namespace RavineRbac\Application\Factories;

use RavineRbac\Domain\Models\RBAC\AccessControl;
use RavineRbac\Domain\Models\RBAC\Permission;
use RavineRbac\Domain\Models\RBAC\ResourceType;
use RavineRbac\Application\Middleware\RoleValidationMiddleware;
use RavineRbac\Application\Protocols\RbacFallbackInterface;

class RbacValidationFactory
{
    private RoleValidationMiddleware $middleware;
    public function __construct(private AccessControl $accessControl)
    {
        $this->middleware = new RoleValidationMiddleware(
            $accessControl,
            ""
        );
    }

    public function __invoke(ResourceType|string $target): RoleValidationMiddleware
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