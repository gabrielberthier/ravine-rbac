<?php

namespace RavineRbac\Application\Middleware;


use Psr\Http\Message\ServerRequestInterface;
use RavineRbac\Application\Exceptions\HttpForbiddenAccessException;
use RavineRbac\Domain\Contracts\AccessControlInterface;
use RavineRbac\Domain\Models\AccessControl;
use RavineRbac\Domain\Models\ContextIntent;
use RavineRbac\Domain\Models\Permission;
use RavineRbac\Domain\Models\ResourceType;
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
        $token = $this->getTokenInstance($request);
        if ($token) {
            $permission = $this->getAccessGrantRequest($request);

            $canAccess = $this->accessControl->tryAccess(
                $token->role,
                $this->resource,
                $permission,
                $this->getFallback()
            );

            if ($canAccess) {
                return $handler->handle($request);
            }
            
            throw new HttpForbiddenAccessException();
        }

        throw new \RuntimeException("A valid token could no be found in attributes");
    }

    private function getTokenInstance(Request $request): ?Token
    {
        /** @var array|string|object */
        $rawToken = $request->getAttribute("token");

        if (is_string($rawToken)) {
            $rawToken = json_decode($rawToken, associative: true);
        }
        if (is_object($rawToken)) {
            $rawToken = (array) $rawToken;
        }
        if (is_array($rawToken) && !empty($rawToken) && isset($rawToken["data"])) {
            return new Token(...$rawToken["data"]);
        }

        return null;
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