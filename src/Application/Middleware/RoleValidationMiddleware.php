<?php

namespace RavineRbac\Application\Middleware;


use RavineRbac\Application\Exceptions\HttpForbiddenAccessException;
use RavineRbac\Domain\Contracts\AccessControlInterface;
use RavineRbac\Domain\Models\RBAC\AccessControl;
use RavineRbac\Domain\Models\RBAC\ContextIntent;
use RavineRbac\Domain\Models\RBAC\Permission;
use RavineRbac\Domain\Models\RBAC\ResourceType;
use RavineRbac\Domain\Models\Token;
use RavineRbac\Application\Protocols\RbacFallbackInterface;
use Closure;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class RoleValidationMiddleware implements Middleware
{
    private ?RbacFallbackInterface $bypassFallback = null;
    private ?Permission $predefinedPermission = null;
    public function __construct(
        public readonly AccessControlInterface $accessControl,
        private ResourceType|string $resource = ""
    ) {
    }
    public function process(Request $request, RequestHandler $handler): Response
    {
        /** @var array */
        $rawToken = $request->getAttribute("token");
        if (is_array($rawToken) && !empty($rawToken)) {
            $token = new Token(...$rawToken["data"]);

            $permission = $this->getAccessGrantRequest($request);
            $maybeRole = $this->accessControl->getRole($token->role);
            $maybeResource = $this->accessControl->getResourceType($this->resource);

            if ($maybeRole->isDefined() && $maybeResource->isDefined()) {
                $role = $maybeRole->get();
                $resource = $maybeResource->get();

                $canAccess = $this->accessControl->tryAccess(
                    $role,
                    $resource,
                    $permission,
                    $this->getFallback()
                );

                if ($canAccess) {
                    return $handler->handle($request);
                }
            }
        }

        throw new HttpForbiddenAccessException();
    }

    public function setResourceTarget(ResourceType|string $resource): self
    {
        $this->resource = $resource;

        return $this;
    }

    public function setByPassFallback(RbacFallbackInterface $fallback): self
    {
        $this->bypassFallback = $fallback;

        return $this;
    }

    public function setPredefinedPermission(Permission $permission): self
    {
        $this->predefinedPermission = $permission;

        return $this;
    }

    public function getAccessGrantRequest(Request $request): Permission
    {
        return
            $this->predefinedPermission ??
            $this->makeGrantBasedOnRequestMethod($request->getMethod());
    }

    /**
     * Creates a Permission object based on request method.
     */
    private function makeGrantBasedOnRequestMethod(
        string $method
    ): Permission {
        $contextIntent = match (strtoupper($method)) {
            "GET" => ContextIntent::READ,
            "POST" => ContextIntent::CREATE,
            "PATCH", "PUT" => ContextIntent::UPDATE,
            "DELETE" => ContextIntent::DELETE,
        };

        return Permission::makeWithPreferableName(
            $contextIntent,
            $this->resource
        );
    }

    private function getFallback(): ?Closure
    {
        if (is_null($this->bypassFallback)) {
            return null;
        }

        return $this->bypassFallback->retry(...);
    }
}